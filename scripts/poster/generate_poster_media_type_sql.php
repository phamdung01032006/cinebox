<?php

ignore_user_abort(true);
set_time_limit(0);

define("PROJECT_ROOT", dirname(__DIR__, 2));
define("TRAILER_DIR", PROJECT_ROOT . "/Trailer");
define("MEDIA_TYPE_OUTPUT_SQL", TRAILER_DIR . "/poster_media_type_updates.sql");
define("MEDIA_TYPE_OUTPUT_REPORT", TRAILER_DIR . "/poster_media_type_report.json");
define("CATEGORY_OUTPUT_REPORT", TRAILER_DIR . "/poster_category_report.json");
const TMDB_MULTI_SEARCH_URL = "https://www.themoviedb.org/search?language=en-US&query=";
const TMDB_REQUEST_DELAY_US = 900000;
const TMDB_MAX_RETRIES = 8;

$pdo = new PDO("mysql:dbname=cinebox;host=localhost;charset=utf8mb4", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->exec("SET NAMES utf8mb4 COLLATE utf8mb4_general_ci");

$rows = readPosterCsv(TRAILER_DIR . "/poster.csv");
$entityMap = fetchImportedEntitiesByKey($pdo);
$cache = loadMediaTypeCache(MEDIA_TYPE_OUTPUT_REPORT);
$categoryReferenceMap = loadCategoryReferenceCache(CATEGORY_OUTPUT_REPORT);
$processed = [];
$report = [];
$sqlStatements = ["START TRANSACTION;"];
$summary = [
    "csvRows" => count($rows),
    "processedTitles" => 0,
    "movieCount" => 0,
    "tvCount" => 0,
    "manualOverrideCount" => 0,
    "fallbackMovieCount" => 0,
    "missingImportedEntities" => 0
];

$updateStatement = $pdo->prepare("
    UPDATE videos
    SET isMovie = :isMovie,
        season = :season,
        episode = :episode
    WHERE entityId = :entityId
    AND filePath LIKE 'Trailer/imported/%'
");

foreach($rows as $row) {
    $key = buildPosterKey($row["title"], $row["poster"]);
    if(isset($processed[$key])) {
        continue;
    }

    $processed[$key] = true;
    $entityIds = $entityMap[$key] ?? [];

    if(empty($entityIds)) {
        $summary["missingImportedEntities"]++;
        $report[] = [
            "title" => $row["title"],
            "poster" => $row["poster"],
            "status" => "missing-imported-entity"
        ];
        continue;
    }

    $mediaData = resolveMediaType($row["title"], $row["poster"], $cache[$key] ?? null);
    $mediaData = applyMediaTypeSanityRules($mediaData, $categoryReferenceMap[$key] ?? null);
    $isMovie = $mediaData["mediaType"] !== "tv";
    $season = $isMovie ? 0 : 1;
    $episode = $isMovie ? 0 : 1;

    foreach($entityIds as $entityId) {
        $updateStatement->bindValue(":isMovie", $isMovie ? 1 : 0, PDO::PARAM_INT);
        $updateStatement->bindValue(":season", $season, PDO::PARAM_INT);
        $updateStatement->bindValue(":episode", $episode, PDO::PARAM_INT);
        $updateStatement->bindValue(":entityId", (int)$entityId, PDO::PARAM_INT);
        $updateStatement->execute();
    }

    if($isMovie) {
        $summary["movieCount"]++;
    }
    else {
        $summary["tvCount"]++;
    }

    if(($mediaData["status"] ?? "") === "manual-override") {
        $summary["manualOverrideCount"]++;
    }

    if(($mediaData["status"] ?? "") === "fallback-movie") {
        $summary["fallbackMovieCount"]++;
    }

    $sqlStatements[] = "";
    $sqlStatements[] = "-- " . sqlCommentSafe($row["title"]);
    $sqlStatements[] = "-- TMDB type: " . ($mediaData["mediaType"] ?? "movie");
    $sqlStatements[] = "-- Match status: " . ($mediaData["status"] ?? "unknown");
    if(!empty($mediaData["url"])) {
        $sqlStatements[] = "-- Source: " . sqlCommentSafe($mediaData["url"]);
    }
    $sqlStatements[] = "UPDATE videos";
    $sqlStatements[] = "SET isMovie = " . ($isMovie ? 1 : 0) . ", season = " . $season . ", episode = " . $episode;
    $sqlStatements[] = "WHERE entityId IN (" . implode(", ", array_map("intval", $entityIds)) . ")";
    $sqlStatements[] = "AND filePath LIKE 'Trailer/imported/%';";

    $report[] = [
        "title" => $row["title"],
        "poster" => $row["poster"],
        "entityIds" => $entityIds,
        "mediaType" => $mediaData["mediaType"],
        "status" => $mediaData["status"],
        "tmdbUrl" => $mediaData["url"] ?? null,
        "matchedBy" => $mediaData["matchedBy"] ?? null,
        "season" => $season,
        "episode" => $episode
    ];

    $summary["processedTitles"]++;
    persistMediaTypeReport($summary, $report);
}

$sqlStatements[] = "";
$sqlStatements[] = "COMMIT;";
$sqlStatements[] = "";

file_put_contents(MEDIA_TYPE_OUTPUT_SQL, implode(PHP_EOL, $sqlStatements));
persistMediaTypeReport($summary, $report);

echo json_encode(
    [
        "summary" => $summary,
        "sqlFile" => MEDIA_TYPE_OUTPUT_SQL,
        "reportFile" => MEDIA_TYPE_OUTPUT_REPORT
    ],
    JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
);

function readPosterCsv($csvPath) {
    $handle = fopen($csvPath, "r");
    if($handle === false) {
        throw new RuntimeException("Unable to open poster.csv");
    }

    $rows = [];
    $headerSkipped = false;
    while(($data = fgetcsv($handle)) !== false) {
        if(!$headerSkipped) {
            $headerSkipped = true;
            continue;
        }

        $title = isset($data[0]) ? trim(stripBom((string)$data[0])) : "";
        $poster = isset($data[1]) ? trim((string)$data[1]) : "";

        if($title === "" || $poster === "") {
            continue;
        }

        $rows[] = [
            "title" => $title,
            "poster" => $poster
        ];
    }

    fclose($handle);
    return $rows;
}

function fetchImportedEntitiesByKey($pdo) {
    $query = $pdo->query("
        SELECT id, name, thumbnail
        FROM entities
        WHERE preview LIKE 'Trailer/imported/%'
        ORDER BY id ASC
    ");

    $map = [];
    while($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $key = buildPosterKey($row["name"], $row["thumbnail"]);
        if(!isset($map[$key])) {
            $map[$key] = [];
        }

        $map[$key][] = (int)$row["id"];
    }

    return $map;
}

function loadMediaTypeCache($reportPath) {
    if(!is_file($reportPath)) {
        return [];
    }

    $decoded = json_decode(file_get_contents($reportPath), true);
    if(!is_array($decoded) || !isset($decoded["items"]) || !is_array($decoded["items"])) {
        return [];
    }

    $cache = [];
    foreach($decoded["items"] as $item) {
        $title = $item["title"] ?? "";
        $poster = $item["poster"] ?? "";
        if($title === "" || $poster === "") {
            continue;
        }

        $cache[buildPosterKey($title, $poster)] = $item;
    }

    return $cache;
}

function loadCategoryReferenceCache($reportPath) {
    if(!is_file($reportPath)) {
        return [];
    }

    $decoded = json_decode(file_get_contents($reportPath), true);
    if(!is_array($decoded) || !isset($decoded["items"]) || !is_array($decoded["items"])) {
        return [];
    }

    $cache = [];
    foreach($decoded["items"] as $item) {
        $title = $item["title"] ?? "";
        $poster = $item["poster"] ?? "";
        if($title === "" || $poster === "") {
            continue;
        }

        $cache[buildPosterKey($title, $poster)] = [
            "tmdbUrl" => $item["tmdbUrl"] ?? null,
            "tmdbStatus" => $item["tmdbStatus"] ?? null
        ];
    }

    return $cache;
}

function resolveMediaType($title, $poster, $cached = null) {
    $manualOverride = getManualMediaTypeOverride($title);
    if($manualOverride !== null) {
        return $manualOverride;
    }

    if(is_array($cached) && !empty($cached["mediaType"])) {
        return [
            "mediaType" => $cached["mediaType"],
            "status" => $cached["status"] ?? "report-cache",
            "url" => $cached["tmdbUrl"] ?? null,
            "matchedBy" => $cached["matchedBy"] ?? "report-cache"
        ];
    }

    $html = fetchUrl(TMDB_MULTI_SEARCH_URL . rawurlencode($title));
    $candidate = chooseMultiSearchCandidate($html, $title, $poster);

    if($candidate === null) {
        return [
            "mediaType" => "movie",
            "status" => "fallback-movie",
            "url" => null,
            "matchedBy" => null
        ];
    }

    return [
        "mediaType" => $candidate["mediaType"],
        "status" => "matched",
        "url" => "https://www.themoviedb.org" . $candidate["href"],
        "matchedBy" => $candidate["matchedBy"]
    ];
}

function applyMediaTypeSanityRules($mediaData, $categoryReference = null) {
    if(($mediaData["mediaType"] ?? null) !== "tv") {
        return $mediaData;
    }

    $matchedBy = $mediaData["matchedBy"] ?? null;
    $categoryUrl = $categoryReference["tmdbUrl"] ?? null;
    $categoryLooksLikeMovie = is_string($categoryUrl) && strpos($categoryUrl, "/movie/") !== false;

    if($matchedBy !== "poster" && $matchedBy !== "manual" && $categoryLooksLikeMovie) {
        return [
            "mediaType" => "movie",
            "status" => "category-report-movie-override",
            "url" => $categoryUrl,
            "matchedBy" => "category-report"
        ];
    }

    return $mediaData;
}

function chooseMultiSearchCandidate($html, $title, $posterUrl) {
    $dom = new DOMDocument();
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    $anchors = $xpath->query("//a[(@data-media-type='movie' or @data-media-type='tv') and (@href[contains(., '/movie/')] or @href[contains(., '/tv/')])]");

    $normalizedTitle = normalizeTitle($title);
    $posterBasename = getPosterBasename($posterUrl);
    $candidates = [];
    $seen = [];

    foreach($anchors as $anchor) {
        $href = trim((string)$anchor->getAttribute("href"));
        $mediaType = trim((string)$anchor->getAttribute("data-media-type"));
        if($href === "" || $mediaType === "" || isset($seen[$mediaType . "|" . $href])) {
            continue;
        }

        $seen[$mediaType . "|" . $href] = true;
        $card = findAncestorWithClass($anchor, "comp:media-card");
        if($card === null) {
            $card = $anchor->parentNode;
        }

        $posterNode = $xpath->query(".//img[contains(@class, 'poster')]", $card)->item(0);
        $titleNode = $xpath->query(".//h2//span", $card)->item(0);
        $releaseNode = $xpath->query(".//span[contains(@class, 'release_date')]", $card)->item(0);

        $candidateTitle = $titleNode ? trim($titleNode->textContent) : trim($anchor->textContent);
        $candidatePoster = $posterNode ? (string)$posterNode->getAttribute("src") : "";
        $releaseText = $releaseNode ? trim($releaseNode->textContent) : "";
        $matchedBy = null;

        if($posterBasename !== "" && getPosterBasename($candidatePoster) !== "" && strcasecmp(getPosterBasename($candidatePoster), $posterBasename) === 0) {
            $matchedBy = "poster";
        }
        elseif(normalizeTitle($candidateTitle) === $normalizedTitle) {
            $matchedBy = "title";
        }

        $candidates[] = [
            "href" => $href,
            "mediaType" => $mediaType,
            "title" => $candidateTitle,
            "matchedBy" => $matchedBy,
            "distance" => levenshtein($normalizedTitle, normalizeTitle($candidateTitle)),
            "releaseYear" => extractYear($releaseText)
        ];
    }

    usort($candidates, function($left, $right) {
        $leftRank = mediaCandidateRank($left["matchedBy"]);
        $rightRank = mediaCandidateRank($right["matchedBy"]);

        if($leftRank !== $rightRank) {
            return $leftRank <=> $rightRank;
        }

        if($left["distance"] !== $right["distance"]) {
            return $left["distance"] <=> $right["distance"];
        }

        return ($right["releaseYear"] ?? 0) <=> ($left["releaseYear"] ?? 0);
    });

    if(empty($candidates)) {
        return null;
    }

    $best = $candidates[0];
    if($best["matchedBy"] === null && $best["distance"] > max(4, (int)floor(strlen($normalizedTitle) * 0.25))) {
        return null;
    }

    return $best;
}

function getManualMediaTypeOverride($title) {
    $overrides = [
        normalizeTitle("Laura y el misterio del asesino inesperado") => ["mediaType" => "movie", "status" => "manual-override", "url" => "https://www.imdb.com/title/tt15378928/", "matchedBy" => "manual"],
        normalizeTitle("Matando Cabos 2: La MÃ¡scara del MÃ¡scara") => ["mediaType" => "movie", "status" => "manual-override", "url" => "https://www.rottentomatoes.com/m/matando_cabos_2", "matchedBy" => "manual"],
        normalizeTitle("El Paseo 6") => ["mediaType" => "movie", "status" => "manual-override", "url" => "https://www.themoviedb.org/movie/920143-el-paseo-6", "matchedBy" => "manual"],
        normalizeTitle("Seobok: Project Clone") => ["mediaType" => "movie", "status" => "manual-override", "url" => "https://en.wikipedia.org/wiki/Seo_Bok", "matchedBy" => "manual"],
        normalizeTitle("PokÃ©mon: Mewtwo Strikes Back - Evolution") => ["mediaType" => "movie", "status" => "manual-override", "url" => "https://www.themoviedb.org/movie/571891-evolution", "matchedBy" => "manual"]
    ];

    return $overrides[normalizeTitle($title)] ?? null;
}

function fetchUrl($url) {
    $attempt = 0;

    while($attempt < TMDB_MAX_RETRIES) {
        $attempt++;
        $headers = [];
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_USERAGENT => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0 Safari/537.36",
            CURLOPT_HTTPHEADER => [
                "Accept-Language: en-US,en;q=0.9"
            ],
            CURLOPT_HEADERFUNCTION => function($curl, $headerLine) use (&$headers) {
                $length = strlen($headerLine);
                $parts = explode(":", $headerLine, 2);
                if(count($parts) === 2) {
                    $headers[strtolower(trim($parts[0]))] = trim($parts[1]);
                }

                return $length;
            }
        ]);

        $body = curl_exec($ch);
        $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if($body !== false && $httpCode > 0 && $httpCode < 400) {
            usleep(TMDB_REQUEST_DELAY_US);
            return $body;
        }

        if($httpCode === 429 && $attempt < TMDB_MAX_RETRIES) {
            $retryAfter = parseRetryAfterSeconds($headers["retry-after"] ?? null);
            $sleepSeconds = $retryAfter !== null
                ? max(3, $retryAfter)
                : min(60, 5 * $attempt);

            sleep($sleepSeconds);
            continue;
        }

        throw new RuntimeException("Failed to fetch URL: " . $url . " (" . $httpCode . ") " . $error);
    }

    throw new RuntimeException("Failed to fetch URL after retries: " . $url);
}

function buildPosterKey($title, $poster) {
    return normalizeTitle($title) . "\t" . trim((string)$poster);
}

function normalizeTitle($value) {
    $value = strtolower(stripBom(trim((string)$value)));
    $value = preg_replace('/[^a-z0-9]+/i', ' ', $value);
    $value = preg_replace('/\s+/', ' ', $value);
    return trim($value);
}

function getPosterBasename($url) {
    $path = parse_url((string)$url, PHP_URL_PATH);
    return $path ? basename($path) : "";
}

function extractYear($text) {
    if(preg_match('/(19|20)\d{2}/', (string)$text, $matches)) {
        return (int)$matches[0];
    }

    return null;
}

function mediaCandidateRank($matchedBy) {
    if($matchedBy === "poster") {
        return 0;
    }

    if($matchedBy === "title") {
        return 1;
    }

    return 2;
}

function findAncestorWithClass($node, $classFragment) {
    while($node !== null) {
        if($node instanceof DOMElement) {
            $className = (string)$node->getAttribute("class");
            if($className !== "" && strpos($className, $classFragment) !== false) {
                return $node;
            }
        }

        $node = $node->parentNode;
    }

    return null;
}

function sqlCommentSafe($text) {
    return trim(str_replace(["\r", "\n"], " ", (string)$text));
}

function stripBom($value) {
    return preg_replace('/^\xEF\xBB\xBF/', '', (string)$value);
}

function persistMediaTypeReport($summary, $report) {
    file_put_contents(
        MEDIA_TYPE_OUTPUT_REPORT,
        json_encode(
            [
                "summary" => $summary,
                "items" => $report
            ],
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        )
    );
}

function parseRetryAfterSeconds($value) {
    if($value === null || $value === "") {
        return null;
    }

    if(is_numeric($value)) {
        return (int)$value;
    }

    $timestamp = strtotime((string)$value);
    if($timestamp === false) {
        return null;
    }

    return max(0, $timestamp - time());
}

?>

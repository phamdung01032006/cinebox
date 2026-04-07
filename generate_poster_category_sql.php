<?php

ignore_user_abort(true);
set_time_limit(0);

const CATEGORY_OUTPUT_SQL = __DIR__ . "/Trailer/poster_category_updates.sql";
const CATEGORY_OUTPUT_REPORT = __DIR__ . "/Trailer/poster_category_report.json";
const TMDB_BASE_URL = "https://www.themoviedb.org";

$pdo = new PDO("mysql:dbname=cinebox;host=localhost;charset=utf8mb4", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->exec("SET NAMES utf8mb4 COLLATE utf8mb4_general_ci");

$rows = readPosterCsv(__DIR__ . "/Trailer/poster.csv");
$importedEntitiesByKey = fetchImportedEntities($pdo);
$tmdbCache = loadTmdbCache(CATEGORY_OUTPUT_REPORT);
$processed = [];
$sqlStatements = ["START TRANSACTION;"];
$report = [];
$summary = [
    "csvRows" => count($rows),
    "processedTitles" => 0,
    "updatedEntities" => 0,
    "fallbackCount" => 0,
    "unmatchedTmdbCount" => 0,
    "missingImportedEntities" => 0
];

$updateStatement = $pdo->prepare("UPDATE entities SET categoryId = :categoryId WHERE id = :id");

foreach($rows as $row) {
    $key = buildPosterKey($row["title"], $row["poster"]);
    if(isset($processed[$key])) {
        continue;
    }

    $processed[$key] = true;
    $entityIds = $importedEntitiesByKey[$key] ?? [];

    if(empty($entityIds)) {
        $summary["missingImportedEntities"]++;
        $report[] = [
            "title" => $row["title"],
            "poster" => $row["poster"],
            "status" => "missing-imported-entity"
        ];
        continue;
    }

    $tmdbData = resolveTmdbMovie($row["title"], $row["poster"], $tmdbCache[$key] ?? null);
    $categoryInfo = mapTmdbGenresToLocalCategory($row["title"], $tmdbData);

    if(($categoryInfo["fallback"] ?? false) === true) {
        $summary["fallbackCount"]++;
    }

    if(in_array(($tmdbData["status"] ?? ""), ["search-no-match", "missing-imported-entity"], true)) {
        $summary["unmatchedTmdbCount"]++;
    }

    $categoryId = (int)$categoryInfo["id"];
    foreach($entityIds as $entityId) {
        $updateStatement->bindValue(":categoryId", $categoryId, PDO::PARAM_INT);
        $updateStatement->bindValue(":id", (int)$entityId, PDO::PARAM_INT);
        $updateStatement->execute();
        $summary["updatedEntities"]++;
    }

    $commentTitle = sqlCommentSafe($row["title"]);
    $commentGenres = sqlCommentSafe(implode(", ", $tmdbData["genres"] ?? []));
    $commentReason = sqlCommentSafe($categoryInfo["reason"]);
    $entityIdList = implode(", ", array_map("intval", $entityIds));

    $sqlStatements[] = "";
    $sqlStatements[] = "-- " . $commentTitle;
    $sqlStatements[] = "-- TMDB genres: " . ($commentGenres !== "" ? $commentGenres : "unavailable");
    $sqlStatements[] = "-- Local category: " . $categoryInfo["name"] . " (" . $categoryId . ")";
    $sqlStatements[] = "-- Mapping reason: " . $commentReason;
    $sqlStatements[] = "UPDATE entities";
    $sqlStatements[] = "SET categoryId = " . $categoryId;
    $sqlStatements[] = "WHERE id IN (" . $entityIdList . ");";

    $report[] = [
        "title" => $row["title"],
        "poster" => $row["poster"],
        "entityIds" => $entityIds,
        "tmdbStatus" => $tmdbData["status"],
        "tmdbUrl" => $tmdbData["url"] ?? null,
        "tmdbGenres" => $tmdbData["genres"] ?? [],
        "keywords" => $tmdbData["keywords"] ?? [],
        "releaseYear" => $tmdbData["releaseYear"] ?? null,
        "countries" => $tmdbData["countries"] ?? [],
        "categoryId" => $categoryId,
        "categoryName" => $categoryInfo["name"],
        "mappingReason" => $categoryInfo["reason"],
        "fallback" => $categoryInfo["fallback"] ?? false
    ];

    $summary["processedTitles"]++;
}

$sqlStatements[] = "";
$sqlStatements[] = "COMMIT;";

file_put_contents(CATEGORY_OUTPUT_SQL, implode(PHP_EOL, $sqlStatements) . PHP_EOL);
file_put_contents(
    CATEGORY_OUTPUT_REPORT,
    json_encode(
        [
            "summary" => $summary,
            "items" => $report
        ],
        JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    )
);

echo json_encode(
    [
        "summary" => $summary,
        "sqlFile" => CATEGORY_OUTPUT_SQL,
        "reportFile" => CATEGORY_OUTPUT_REPORT
    ],
    JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
);

function readPosterCsv($csvPath) {
    if(!is_file($csvPath)) {
        throw new RuntimeException("poster.csv not found at " . $csvPath);
    }

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

function fetchImportedEntities($pdo) {
    $query = $pdo->prepare("
        SELECT id, name, thumbnail
        FROM entities
        WHERE preview LIKE 'Trailer/imported/%'
        ORDER BY id ASC
    ");
    $query->execute();

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

function loadTmdbCache($reportPath) {
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
            "status" => $item["tmdbStatus"] ?? "",
            "url" => $item["tmdbUrl"] ?? null,
            "genres" => $item["tmdbGenres"] ?? [],
            "keywords" => $item["keywords"] ?? [],
            "countries" => $item["countries"] ?? [],
            "description" => $item["description"] ?? "",
            "releaseYear" => $item["releaseYear"] ?? null,
            "categoryId" => $item["categoryId"] ?? null,
            "categoryName" => $item["categoryName"] ?? null,
            "mappingReason" => $item["mappingReason"] ?? null,
            "fallback" => $item["fallback"] ?? false
        ];
    }

    return $cache;
}

function resolveTmdbMovie($title, $posterUrl, $cachedTmdbData = null) {
    $manualOverride = getManualTmdbOverride($title);
    if($manualOverride !== null) {
        return $manualOverride;
    }

    if(is_array($cachedTmdbData) && !empty($cachedTmdbData["genres"])) {
        return [
            "status" => "matched",
            "url" => $cachedTmdbData["url"] ?? null,
            "genres" => $cachedTmdbData["genres"] ?? [],
            "keywords" => $cachedTmdbData["keywords"] ?? [],
            "countries" => $cachedTmdbData["countries"] ?? [],
            "description" => $cachedTmdbData["description"] ?? "",
            "releaseYear" => $cachedTmdbData["releaseYear"] ?? null,
            "matchedBy" => "report-cache"
        ];
    }

    if(is_array($cachedTmdbData) && !empty($cachedTmdbData["url"])) {
        $detailHtml = fetchUrl($cachedTmdbData["url"]);
        $detailData = parseTmdbDetailPage($detailHtml);
        $detailData["status"] = "matched";
        $detailData["url"] = $cachedTmdbData["url"];
        $detailData["matchedBy"] = "cached-url";
        return $detailData;
    }

    $searchUrl = TMDB_BASE_URL . "/search/movie?query=" . rawurlencode($title) . "&language=en-US";
    $searchHtml = fetchUrl($searchUrl);
    $candidate = chooseTmdbMovieCandidate($searchHtml, $title, $posterUrl);

    if($candidate === null) {
        return [
            "status" => "search-no-match",
            "genres" => [],
            "keywords" => [],
            "countries" => [],
            "description" => "",
            "releaseYear" => null
        ];
    }

    usleep(175000);
    $detailUrl = TMDB_BASE_URL . $candidate["href"];
    $detailHtml = fetchUrl($detailUrl);
    $detailData = parseTmdbDetailPage($detailHtml);
    $detailData["status"] = "matched";
    $detailData["url"] = $detailUrl;
    $detailData["matchedBy"] = $candidate["matchedBy"];
    $detailData["searchResultTitle"] = $candidate["title"];
    $detailData["searchResultPoster"] = $candidate["poster"];

    return $detailData;
}

function chooseTmdbMovieCandidate($html, $title, $posterUrl) {
    $dom = new DOMDocument();
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    $anchors = $xpath->query("//a[@data-media-type='movie' and starts-with(@href, '/movie/')]");

    $targetPosterBasename = getPosterBasename($posterUrl);
    $normalizedTitle = normalizeTitle($title);
    $candidates = [];
    $seenHrefs = [];

    foreach($anchors as $anchor) {
        $href = trim((string)$anchor->getAttribute("href"));
        if($href === "" || isset($seenHrefs[$href])) {
            continue;
        }

        $seenHrefs[$href] = true;
        $card = findAncestorWithClass($anchor, "comp:media-card");
        if($card === null) {
            $card = $anchor->parentNode;
        }

        $posterNode = $xpath->query(".//img[contains(@class, 'poster')]", $card)->item(0);
        $titleNode = $xpath->query(".//h2//span", $card)->item(0);
        $releaseNode = $xpath->query(".//span[contains(@class, 'release_date')]", $card)->item(0);

        $candidateTitle = $titleNode ? trim($titleNode->textContent) : trim($anchor->textContent);
        $posterSrc = $posterNode ? (string)$posterNode->getAttribute("src") : "";
        $posterBasename = getPosterBasename($posterSrc);
        $releaseText = $releaseNode ? trim($releaseNode->textContent) : "";
        $releaseYear = extractYear($releaseText);
        $matchedBy = null;

        if($targetPosterBasename !== "" && $posterBasename !== "" && strcasecmp($posterBasename, $targetPosterBasename) === 0) {
            $matchedBy = "poster";
        }
        elseif(normalizeTitle($candidateTitle) === $normalizedTitle) {
            $matchedBy = "title";
        }

        $distance = levenshtein($normalizedTitle, normalizeTitle($candidateTitle));
        $candidates[] = [
            "href" => $href,
            "title" => $candidateTitle,
            "poster" => $posterSrc,
            "releaseYear" => $releaseYear,
            "matchedBy" => $matchedBy,
            "distance" => $distance
        ];
    }

    usort($candidates, function($left, $right) {
        $leftRank = candidateRank($left["matchedBy"]);
        $rightRank = candidateRank($right["matchedBy"]);

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

    $bestCandidate = $candidates[0];
    if($bestCandidate["matchedBy"] === null && $bestCandidate["distance"] > max(4, (int)floor(strlen($normalizedTitle) * 0.25))) {
        return null;
    }

    return $bestCandidate;
}

function parseTmdbDetailPage($html) {
    $genres = [];
    $countries = [];
    $description = "";
    $releaseYear = null;

    $dom = new DOMDocument();
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    $scripts = $xpath->query("//script[@type='application/ld+json']");

    foreach($scripts as $script) {
        $jsonText = trim($script->textContent);
        $jsonText = str_replace(["/* <![CDATA[ */", "/* ]]> */"], "", $jsonText);
        $jsonText = trim($jsonText);
        $decoded = json_decode($jsonText, true);
        if(!is_array($decoded)) {
            continue;
        }

        if(($decoded["@type"] ?? "") !== "Movie") {
            continue;
        }

        $genres = normalizeStringList($decoded["genre"] ?? []);
        $description = trim((string)($decoded["description"] ?? ""));
        $countries = [];

        foreach(($decoded["countryOfOrigin"] ?? []) as $country) {
            if(is_array($country) && !empty($country["name"])) {
                $countries[] = trim((string)$country["name"]);
            }
        }

        $releaseDate = "";
        $releasedEvents = $decoded["releasedEvent"] ?? [];
        if(is_array($releasedEvents) && isset($releasedEvents[0]["startDate"])) {
            $releaseDate = (string)$releasedEvents[0]["startDate"];
        }

        $releaseYear = extractYear($releaseDate);
        break;
    }

    preg_match_all('/href="\/keyword\/[^"]+\/movie\?language=en-US">([^<]+)<\/a>/i', $html, $keywordMatches);
    $keywords = normalizeStringList($keywordMatches[1] ?? []);

    return [
        "genres" => $genres,
        "keywords" => $keywords,
        "countries" => $countries,
        "description" => $description,
        "releaseYear" => $releaseYear
    ];
}

function mapTmdbGenresToLocalCategory($title, $tmdbData) {
    if(($tmdbData["status"] ?? "") === "manual-override" && isset($tmdbData["manualCategory"])) {
        return $tmdbData["manualCategory"];
    }

    $genres = array_map("strtolower", $tmdbData["genres"] ?? []);
    $keywords = array_map("strtolower", $tmdbData["keywords"] ?? []);
    $countries = array_map("strtolower", $tmdbData["countries"] ?? []);
    $titleLower = strtolower($title);
    $descriptionLower = strtolower((string)($tmdbData["description"] ?? ""));
    $keywordBlob = implode(" ", $keywords);
    $textBlob = trim($titleLower . " " . $descriptionLower . " " . $keywordBlob);
    $releaseYear = (int)($tmdbData["releaseYear"] ?? 0);

    if(in_array("documentary", $genres, true)) {
        return localCategory(10, "Documentaries", "TMDB genre Documentary");
    }

    if(in_array("music", $genres, true)) {
        return localCategory(17, "Music", "TMDB genre Music");
    }

    if(containsAny($textBlob, ["christmas", "xmas", "santa claus", "santa", "holiday", "noel"])) {
        return localCategory(18, "Christmas", "Title, overview, or keywords indicate Christmas/holiday content");
    }

    if(containsAny($textBlob, ["boxing", "football", "soccer", "basketball", "baseball", "wrestling", "mma", "ufc", "race car", "racing", "motorsport", "athlete", "sport"])) {
        return localCategory(8, "Sports", "Title, overview, or keywords indicate sports content");
    }

    if(in_array("animation", $genres, true)) {
        if(in_array("japan", $countries, true) || containsAny($textBlob, ["anime", "manga", "shonen", "shoujo"])) {
            return localCategory(14, "Anime", "Animated title with Japanese indicators");
        }

        return localCategory(20, "Cartoon", "TMDB genre Animation");
    }

    if(in_array("family", $genres, true)) {
        return localCategory(13, "Children & family", "TMDB genre Family");
    }

    if(in_array("horror", $genres, true)) {
        return localCategory(5, "Horror", "TMDB genre Horror");
    }

    if(in_array("romance", $genres, true)) {
        return localCategory(6, "Romantic", "TMDB genre Romance");
    }

    if(in_array("science fiction", $genres, true) || in_array("fantasy", $genres, true)) {
        return localCategory(7, "Sci - Fi & Fantasy", "TMDB genre Science Fiction/Fantasy");
    }

    if(in_array("thriller", $genres, true) || in_array("mystery", $genres, true) || in_array("crime", $genres, true)) {
        return localCategory(9, "Thrillers", "TMDB genre Thriller/Mystery/Crime");
    }

    if(containsAny($textBlob, ["teen", "high school", "coming of age", "prom", "cheerleader", "college student", "student life"])) {
        return localCategory(12, "Teen", "Title, overview, or keywords indicate teen/high-school content");
    }

    if(in_array("comedy", $genres, true)) {
        return localCategory(3, "Comedies", "TMDB genre Comedy");
    }

    if(in_array("action", $genres, true) || in_array("adventure", $genres, true) || in_array("war", $genres, true) || in_array("western", $genres, true)) {
        return localCategory(1, "Action & adventure", "TMDB genre Action/Adventure/War/Western");
    }

    if(isForeignCountryList($countries)) {
        return localCategory(16, "Foreign", "Country of origin is outside the default English-speaking set");
    }

    if($releaseYear > 0 && $releaseYear <= 1980) {
        return localCategory(2, "Classic", "Release year is 1980 or earlier");
    }

    if(in_array("drama", $genres, true)) {
        return localCategory(4, "Dramas", "TMDB genre Drama");
    }

    return localCategory(19, "Others", "No strong TMDB genre/category mapping found", true);
}

function localCategory($id, $name, $reason, $fallback = false) {
    return [
        "id" => (int)$id,
        "name" => $name,
        "reason" => $reason,
        "fallback" => $fallback
    ];
}

function fetchUrl($url) {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_USERAGENT => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0 Safari/537.36",
        CURLOPT_HTTPHEADER => [
            "Accept-Language: en-US,en;q=0.9"
        ]
    ]);

    $body = curl_exec($ch);
    $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if($body === false || $httpCode >= 400) {
        throw new RuntimeException("Failed to fetch URL: " . $url . " (" . $httpCode . ") " . $error);
    }

    usleep(175000);
    return $body;
}

function getManualTmdbOverride($title) {
    $overrides = [
        normalizeTitle("Laura y el misterio del asesino inesperado") => [
            "status" => "manual-override",
            "url" => "https://www.imdb.com/title/tt15378928/",
            "genres" => ["Comedy", "Crime", "Mystery"],
            "keywords" => [],
            "countries" => ["Spain"],
            "description" => "Laura Lebrel investigates a murder case at a university.",
            "releaseYear" => 2022,
            "manualCategory" => localCategory(9, "Thrillers", "Manual web override from IMDb genres Comedy/Crime/Mystery")
        ],
        normalizeTitle("Matando Cabos 2: La MÃ¡scara del MÃ¡scara") => [
            "status" => "manual-override",
            "url" => "https://www.rottentomatoes.com/m/matando_cabos_2",
            "genres" => ["Action", "Adventure", "Comedy"],
            "keywords" => [],
            "countries" => ["Mexico"],
            "description" => "Rubén races to recover his father's stolen mask.",
            "releaseYear" => 2021,
            "manualCategory" => localCategory(1, "Action & adventure", "Manual web override from Rotten Tomatoes/other web sources")
        ],
        normalizeTitle("El Paseo 6") => [
            "status" => "manual-override",
            "url" => "https://www.themoviedb.org/movie/920143-el-paseo-6?language=es-MX",
            "genres" => ["Comedy"],
            "keywords" => ["mother-in-law", "excursion"],
            "countries" => ["Colombia"],
            "description" => "A father and mother-in-law crash a high-school trip.",
            "releaseYear" => 2021,
            "manualCategory" => localCategory(3, "Comedies", "Manual web override from TMDB/Rotten Tomatoes")
        ],
        normalizeTitle("Seobok: Project Clone") => [
            "status" => "manual-override",
            "url" => "https://en.wikipedia.org/wiki/Seo_Bok",
            "genres" => ["Science Fiction", "Action", "Thriller"],
            "keywords" => ["clone", "agent"],
            "countries" => ["South Korea"],
            "description" => "A former agent protects the first human clone.",
            "releaseYear" => 2021,
            "manualCategory" => localCategory(7, "Sci - Fi & Fantasy", "Manual web override from Wikipedia summary")
        ],
        normalizeTitle("PokÃ©mon: Mewtwo Strikes Back - Evolution") => [
            "status" => "manual-override",
            "url" => "https://www.themoviedb.org/movie/571891-evolution?language=en-US",
            "genres" => ["Animation", "Adventure", "Fantasy", "Action", "Family"],
            "keywords" => ["anime", "pokemon"],
            "countries" => ["Japan"],
            "description" => "Ash and friends meet Mewtwo in a Pokémon remake film.",
            "releaseYear" => 2019,
            "manualCategory" => localCategory(14, "Anime", "Manual web override from TMDB movie page")
        ]
    ];

    $normalizedTitle = normalizeTitle($title);
    return $overrides[$normalizedTitle] ?? null;
}

function normalizeStringList($values) {
    if(!is_array($values)) {
        $values = [$values];
    }

    $result = [];
    foreach($values as $value) {
        $value = trim(strip_tags((string)$value));
        if($value === "") {
            continue;
        }

        $result[] = $value;
    }

    return array_values(array_unique($result));
}

function normalizeTitle($value) {
    $value = strtolower(stripBom(trim((string)$value)));
    $value = preg_replace('/[^a-z0-9]+/i', ' ', $value);
    $value = preg_replace('/\s+/', ' ', $value);
    return trim($value);
}

function buildPosterKey($title, $poster) {
    return normalizeTitle($title) . "\t" . trim((string)$poster);
}

function getPosterBasename($url) {
    $path = parse_url((string)$url, PHP_URL_PATH);
    if(!$path) {
        return "";
    }

    return basename($path);
}

function extractYear($text) {
    if(preg_match('/(19|20)\d{2}/', (string)$text, $matches)) {
        return (int)$matches[0];
    }

    return null;
}

function containsAny($text, $needles) {
    foreach($needles as $needle) {
        if($needle !== "" && strpos($text, strtolower($needle)) !== false) {
            return true;
        }
    }

    return false;
}

function isForeignCountryList($countries) {
    if(empty($countries)) {
        return false;
    }

    $domestic = [
        "united states",
        "united kingdom",
        "canada",
        "australia",
        "new zealand",
        "ireland"
    ];

    foreach($countries as $country) {
        if(in_array($country, $domestic, true)) {
            return false;
        }
    }

    return true;
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

function candidateRank($matchedBy) {
    if($matchedBy === "poster") {
        return 0;
    }

    if($matchedBy === "title") {
        return 1;
    }

    return 2;
}

function sqlCommentSafe($text) {
    return trim(str_replace(["\r", "\n"], " ", (string)$text));
}

function stripBom($value) {
    return preg_replace('/^\xEF\xBB\xBF/', '', (string)$value);
}

?>

<?php

class PosterMovieLibrary {

    private const IMPORT_CATEGORY_ID = 19;
    private const IMPORT_VIDEO_PREFIX = "Trailer/imported/";
    private const DEFAULT_DURATION = "02:30";

    public static function syncFromCsv($con, $csvPath, $trailerDir) {
        $rows = self::readPosterRows($csvPath);
        $videoPool = self::prepareTrailerVideoPool($trailerDir);

        if(empty($videoPool)) {
            throw new RuntimeException("No trailer videos were found in the Trailer directory.");
        }

        $desiredCounts = [];
        foreach($rows as $row) {
            $key = self::buildPosterKey($row["title"], $row["poster"]);

            if(!isset($desiredCounts[$key])) {
                $desiredCounts[$key] = [
                    "title" => $row["title"],
                    "poster" => $row["poster"],
                    "count" => 0
                ];
            }

            $desiredCounts[$key]["count"]++;
        }

        $currentCounts = self::getCurrentImportedCounts($con);
        $existingMatched = 0;
        $inserted = 0;
        $nextEntityId = self::getNextId($con, "entities");
        $nextVideoId = self::getNextId($con, "videos");
        $videoPoolIndex = self::countImportedMovies($con);
        $entityStatement = $con->prepare("
            INSERT INTO entities (id, name, thumbnail, preview, categoryId)
            VALUES (:id, :name, :thumbnail, :preview, :categoryId)
        ");
        $videoStatement = $con->prepare("
            INSERT INTO videos (
                id,
                title,
                description,
                filePath,
                isMovie,
                uploadDate,
                releaseDate,
                views,
                duration,
                season,
                episode,
                entityId
            )
            VALUES (
                :id,
                :title,
                :description,
                :filePath,
                1,
                :uploadDate,
                :releaseDate,
                0,
                :duration,
                0,
                0,
                :entityId
            )
        ");

        $con->beginTransaction();

        try {
            foreach($desiredCounts as $key => $item) {
                $existingCount = (int)($currentCounts[$key] ?? 0);
                $requiredCount = (int)$item["count"];
                $existingMatched += min($existingCount, $requiredCount);
                $missingCount = max(0, $requiredCount - $existingCount);

                for($i = 0; $i < $missingCount; $i++) {
                    $relativeVideoPath = $videoPool[$videoPoolIndex % count($videoPool)];
                    $releaseDate = date("Y-m-d");
                    $uploadDate = date("Y-m-d H:i:s");
                    $description = self::buildMovieDescription($item["title"]);

                    $entityStatement->bindValue(":id", $nextEntityId, PDO::PARAM_INT);
                    $entityStatement->bindValue(":name", $item["title"]);
                    $entityStatement->bindValue(":thumbnail", $item["poster"]);
                    $entityStatement->bindValue(":preview", $relativeVideoPath);
                    $entityStatement->bindValue(":categoryId", self::IMPORT_CATEGORY_ID, PDO::PARAM_INT);
                    $entityStatement->execute();

                    $videoStatement->bindValue(":id", $nextVideoId, PDO::PARAM_INT);
                    $videoStatement->bindValue(":title", $item["title"]);
                    $videoStatement->bindValue(":description", $description);
                    $videoStatement->bindValue(":filePath", $relativeVideoPath);
                    $videoStatement->bindValue(":uploadDate", $uploadDate);
                    $videoStatement->bindValue(":releaseDate", $releaseDate);
                    $videoStatement->bindValue(":duration", self::DEFAULT_DURATION);
                    $videoStatement->bindValue(":entityId", $nextEntityId, PDO::PARAM_INT);
                    $videoStatement->execute();

                    $nextEntityId++;
                    $nextVideoId++;
                    $videoPoolIndex++;
                    $inserted++;
                }
            }

            $rebalancedCount = 0;

            $con->commit();
        }
        catch(Throwable $e) {
            if($con->inTransaction()) {
                $con->rollBack();
            }

            throw $e;
        }

        return [
            "csvRows" => count($rows),
            "existingMatched" => $existingMatched,
            "inserted" => $inserted,
            "rebalancedCategories" => $rebalancedCount,
            "importedMovieCount" => self::countImportedMovies($con),
            "trailerVideoCount" => count($videoPool)
        ];
    }

    public static function countImportedMovies($con) {
        $query = $con->prepare("
            SELECT COUNT(*)
            FROM entities
            WHERE preview LIKE :previewPrefix
        ");
        $query->bindValue(":previewPrefix", self::IMPORT_VIDEO_PREFIX . "%");
        $query->execute();

        return (int)$query->fetchColumn();
    }

    public static function getImportedMovies($con, $limit, $offset = 0) {
        $query = $con->prepare("
            SELECT e.*
            FROM entities e
            WHERE e.preview LIKE :previewPrefix
            ORDER BY e.id DESC
            LIMIT :offset, :limit
        ");
        $query->bindValue(":previewPrefix", self::IMPORT_VIDEO_PREFIX . "%");
        $query->bindValue(":offset", (int)$offset, PDO::PARAM_INT);
        $query->bindValue(":limit", (int)$limit, PDO::PARAM_INT);
        $query->execute();

        $entities = [];
        while($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $entities[] = new Entity($con, $row);
        }

        return $entities;
    }

    private static function readPosterRows($csvPath) {
        if(!is_file($csvPath)) {
            throw new RuntimeException("poster.csv was not found.");
        }

        $handle = fopen($csvPath, "r");
        if($handle === false) {
            throw new RuntimeException("Unable to open poster.csv.");
        }

        $rows = [];
        $headerSkipped = false;

        while(($data = fgetcsv($handle)) !== false) {
            if(!$headerSkipped) {
                $headerSkipped = true;
                continue;
            }

            $title = isset($data[0]) ? trim((string)$data[0]) : "";
            $poster = isset($data[1]) ? trim((string)$data[1]) : "";

            if($title === "" || $poster === "") {
                continue;
            }

            $rows[] = [
                "title" => self::stripBom($title),
                "poster" => $poster
            ];
        }

        fclose($handle);

        return $rows;
    }

    private static function prepareTrailerVideoPool($trailerDir) {
        $sourceFiles = glob(rtrim($trailerDir, "\\/") . DIRECTORY_SEPARATOR . "*.mp4") ?: [];
        sort($sourceFiles, SORT_NATURAL | SORT_FLAG_CASE);

        if(empty($sourceFiles)) {
            return [];
        }

        $importDir = rtrim($trailerDir, "\\/") . DIRECTORY_SEPARATOR . "imported";
        if(!is_dir($importDir) && !mkdir($importDir, 0777, true) && !is_dir($importDir)) {
            throw new RuntimeException("Unable to create Trailer/imported directory.");
        }

        $videoPool = [];
        foreach($sourceFiles as $index => $sourceFile) {
            $targetFileName = sprintf("trailer-%03d.mp4", $index + 1);
            $targetFilePath = $importDir . DIRECTORY_SEPARATOR . $targetFileName;

            if(!is_file($targetFilePath) && !copy($sourceFile, $targetFilePath)) {
                throw new RuntimeException("Unable to prepare sanitized trailer files.");
            }

            $videoPool[] = self::IMPORT_VIDEO_PREFIX . $targetFileName;
        }

        return $videoPool;
    }

    private static function getCurrentImportedCounts($con) {
        $query = $con->prepare("
            SELECT name, thumbnail, COUNT(*) AS total
            FROM entities
            WHERE preview LIKE :previewPrefix
            GROUP BY name, thumbnail
        ");
        $query->bindValue(":previewPrefix", self::IMPORT_VIDEO_PREFIX . "%");
        $query->execute();

        $counts = [];
        while($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $counts[self::buildPosterKey($row["name"], $row["thumbnail"])] = (int)$row["total"];
        }

        return $counts;
    }

    private static function getNextId($con, $tableName) {
        $query = $con->query("SELECT COALESCE(MAX(id), 0) + 1 FROM " . $tableName);
        return (int)$query->fetchColumn();
    }

    private static function buildMovieDescription($title) {
        return "Trailer-based movie entry imported from Trailer/poster.csv for " . $title . ".";
    }

    private static function buildPosterKey($title, $poster) {
        $normalizedTitle = trim((string)$title);
        if(function_exists("mb_strtolower")) {
            $normalizedTitle = mb_strtolower($normalizedTitle, "UTF-8");
        }
        else {
            $normalizedTitle = strtolower($normalizedTitle);
        }

        return $normalizedTitle . "\t" . trim((string)$poster);
    }

    private static function stripBom($value) {
        return preg_replace('/^\xEF\xBB\xBF/', '', (string)$value);
    }
}

?>

<?php

ignore_user_abort(true);
set_time_limit(0);

const IMPORTED_MOVIES_SQL = __DIR__ . "/Trailer/imported_poster_movies.sql";

$pdo = new PDO("mysql:dbname=cinebox;host=localhost;charset=utf8mb4", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->exec("SET NAMES utf8mb4 COLLATE utf8mb4_general_ci");

$entities = $pdo->query("
    SELECT id, name, thumbnail, preview, categoryId
    FROM entities
    WHERE preview LIKE 'Trailer/imported/%'
    ORDER BY id ASC
")->fetchAll(PDO::FETCH_ASSOC);

$videos = $pdo->query("
    SELECT id, title, description, filePath, isMovie, uploadDate, releaseDate, views, duration, season, episode, entityId
    FROM videos
    WHERE filePath LIKE 'Trailer/imported/%'
    ORDER BY id ASC
")->fetchAll(PDO::FETCH_ASSOC);

$sql = [];
$sql[] = "-- Imported poster.csv movies";
$sql[] = "-- Generated on " . date("Y-m-d H:i:s");
$sql[] = "START TRANSACTION;";
$sql[] = "";

if(!empty($videos)) {
    $videoIds = implode(", ", array_map("intval", array_column($videos, "id")));
    $sql[] = "DELETE FROM videos WHERE id IN (" . $videoIds . ");";
}

if(!empty($entities)) {
    $entityIds = implode(", ", array_map("intval", array_column($entities, "id")));
    $sql[] = "DELETE FROM entities WHERE id IN (" . $entityIds . ");";
}

if(!empty($entities)) {
    $sql[] = "";
    $sql[] = "INSERT INTO entities (id, name, thumbnail, preview, categoryId) VALUES";
    $entityRows = [];
    foreach($entities as $entity) {
        $entityRows[] = "("
            . (int)$entity["id"] . ", "
            . quoteSql($pdo, $entity["name"]) . ", "
            . quoteSql($pdo, $entity["thumbnail"]) . ", "
            . quoteSql($pdo, $entity["preview"]) . ", "
            . (int)$entity["categoryId"]
            . ")";
    }
    $sql[] = implode("," . PHP_EOL, $entityRows) . ";";
}

if(!empty($videos)) {
    $sql[] = "";
    $sql[] = "INSERT INTO videos (id, title, description, filePath, isMovie, uploadDate, releaseDate, views, duration, season, episode, entityId) VALUES";
    $videoRows = [];
    foreach($videos as $video) {
        $videoRows[] = "("
            . (int)$video["id"] . ", "
            . quoteSql($pdo, $video["title"]) . ", "
            . quoteSql($pdo, $video["description"]) . ", "
            . quoteSql($pdo, $video["filePath"]) . ", "
            . (int)$video["isMovie"] . ", "
            . quoteSql($pdo, $video["uploadDate"]) . ", "
            . quoteSql($pdo, $video["releaseDate"]) . ", "
            . (int)$video["views"] . ", "
            . quoteSql($pdo, $video["duration"]) . ", "
            . (int)$video["season"] . ", "
            . (int)$video["episode"] . ", "
            . (int)$video["entityId"]
            . ")";
    }
    $sql[] = implode("," . PHP_EOL, $videoRows) . ";";
}

$sql[] = "";
$sql[] = "COMMIT;";
$sql[] = "";

file_put_contents(IMPORTED_MOVIES_SQL, implode(PHP_EOL, $sql));

echo json_encode([
    "entityCount" => count($entities),
    "videoCount" => count($videos),
    "sqlFile" => IMPORTED_MOVIES_SQL
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

function quoteSql($pdo, $value) {
    return $pdo->quote((string)$value);
}

?>

<?php

ignore_user_abort(true);
set_time_limit(0);

define("PROJECT_ROOT", dirname(__DIR__, 2));
define("TRAILER_DIR", PROJECT_ROOT . "/Trailer");

require_once(PROJECT_ROOT . "/includes/classes/PosterMovieLibrary.php");

try {
    $con = new PDO("mysql:dbname=cinebox;host=localhost;charset=utf8mb4", "root", "");
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $con->exec("SET NAMES utf8mb4 COLLATE utf8mb4_general_ci");

    $result = PosterMovieLibrary::syncFromCsv(
        $con,
        TRAILER_DIR . "/poster.csv",
        TRAILER_DIR,
        PROJECT_ROOT . "/entities/videos"
    );

    if(PHP_SAPI !== "cli") {
        header("Content-Type: application/json; charset=utf-8");
    }

    echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
}
catch(Throwable $e) {
    http_response_code(500);

    if(PHP_SAPI !== "cli") {
        header("Content-Type: application/json; charset=utf-8");
    }

    echo json_encode([
        "error" => $e->getMessage()
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit(1);
}

?>

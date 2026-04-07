<?php

ignore_user_abort(true);
set_time_limit(0);

require_once(__DIR__ . "/includes/classes/PosterMovieLibrary.php");

try {
    $con = new PDO("mysql:dbname=cinebox;host=localhost;charset=utf8mb4", "root", "");
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $con->exec("SET NAMES utf8mb4 COLLATE utf8mb4_general_ci");

    $result = PosterMovieLibrary::syncFromCsv(
        $con,
        __DIR__ . "/Trailer/poster.csv",
        __DIR__ . "/Trailer"
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

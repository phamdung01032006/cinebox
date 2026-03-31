<?php
require_once("../includes/config.php");
require_once("../includes/classes/User.php");

header("Content-Type: application/json");

if(empty($_SESSION["userLoggedIn"])) {
    echo json_encode([
        "status" => "error",
        "message" => "login_required"
    ]);
    exit();
}

$entityId = isset($_POST["entityId"]) ? (int)$_POST["entityId"] : 0;
$rating = isset($_POST["rating"]) ? (int)$_POST["rating"] : 0;

if($entityId <= 0 || $rating < 1 || $rating > 5) {
    echo json_encode([
        "status" => "error",
        "message" => "invalid_payload"
    ]);
    exit();
}

$user = new User($con, $_SESSION["userLoggedIn"]);
$success = $user->rateEntity($entityId, $rating);
$averageRating = 0;

if($success) {
    $query = $con->prepare("SELECT AVG(rating) AS averageRating FROM entityRatings WHERE entityId = :entityId");
    $query->bindValue(":entityId", $entityId, PDO::PARAM_INT);
    $query->execute();
    $averageRating = (float)($query->fetch(PDO::FETCH_ASSOC)["averageRating"] ?? 0);
}

echo json_encode([
    "status" => $success ? "success" : "error",
    "rating" => $rating,
    "averageRating" => $averageRating
]);

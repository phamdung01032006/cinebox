<?php
require_once("../includes/config.php");
require_once("../includes/classes/Entity.php");
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
if($entityId <= 0) {
    echo json_encode([
        "status" => "error",
        "message" => "invalid_entity"
    ]);
    exit();
}

$entityQuery = $con->prepare("SELECT id FROM entities WHERE id = :id LIMIT 1");
$entityQuery->bindValue(":id", $entityId, PDO::PARAM_INT);
$entityQuery->execute();

if(!$entityQuery->fetchColumn()) {
    echo json_encode([
        "status" => "error",
        "message" => "entity_not_found"
    ]);
    exit();
}

$user = new User($con, $_SESSION["userLoggedIn"]);
$result = $user->addToWishlist($entityId);

if($result === false) {
    echo json_encode([
        "status" => "error",
        "message" => "wishlist_save_failed"
    ]);
    exit();
}

echo json_encode([
    "status" => "success",
    "action" => $result
]);

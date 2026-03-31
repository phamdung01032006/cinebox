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

$user = new User($con, $_SESSION["userLoggedIn"]);
$removed = $user->removeFromWishlist($entityId);

echo json_encode([
    "status" => "success",
    "removed" => $removed
]);

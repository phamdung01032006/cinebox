<?php
require_once("../includes/config.php");
require_once("../includes/classes/Entity.php");
require_once("../includes/classes/PreviewProvider.php");
require_once("../includes/classes/User.php");
require_once("../includes/wishlist_panel.php");

header("Content-Type: application/json");

if(empty($_SESSION["userLoggedIn"])) {
    echo json_encode([
        "status" => "error",
        "message" => "login_required"
    ]);
    exit();
}

$panelData = buildWishlistPanelData($con, $_SESSION["userLoggedIn"]);

echo json_encode([
    "status" => "success",
    "count" => $panelData["count"],
    "itemsHtml" => $panelData["itemsHtml"]
]);

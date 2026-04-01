<?php

require_once(__DIR__ . "/classes/PreviewProvider.php");
require_once(__DIR__ . "/classes/User.php");

function buildWishlistPanelData($con, $username) {
    if(!$username) {
        return [
            "count" => 0,
            "itemsHtml" => "<div class='wishlistDropdownEmpty'><p>" . htmlspecialchars(t("wishlist.login_required"), ENT_QUOTES, "UTF-8") . "</p></div>"
        ];
    }

    $user = new User($con, $username);
    $entities = $user->getWishlistEntities();
    $previewProvider = new PreviewProvider($con, $username);

    $itemsHtml = "";
    foreach(array_slice($entities, 0, 12) as $entity) {
        $itemsHtml .= $previewProvider->createWishlistDropdownItem($entity);
    }

    if($itemsHtml === "") {
        $itemsHtml = "<div class='wishlistDropdownEmpty'><p>" . htmlspecialchars(t("wishlist.empty"), ENT_QUOTES, "UTF-8") . "</p><span>" . htmlspecialchars(t("wishlist.empty_hint"), ENT_QUOTES, "UTF-8") . "</span></div>";
    }

    return [
        "count" => count($entities),
        "itemsHtml" => $itemsHtml
    ];
}

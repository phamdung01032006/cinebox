<?php

require_once(__DIR__ . "/classes/PreviewProvider.php");
require_once(__DIR__ . "/classes/User.php");

function buildWishlistPanelData($con, $username) {
    if(!$username) {
        return [
            "count" => 0,
            "itemsHtml" => "<div class='wishlistDropdownEmpty'><p>Log in to use your wishlist.</p></div>"
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
        $itemsHtml = "<div class='wishlistDropdownEmpty'><p>Your wishlist is empty.</p><span>Add titles from the preview button to see them here.</span></div>";
    }

    return [
        "count" => count($entities),
        "itemsHtml" => $itemsHtml
    ];
}

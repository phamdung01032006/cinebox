<?php

require_once("includes/config.php");
require_once("includes/classes/PreviewProvider.php");
require_once("includes/classes/CategoryContainers.php");
require_once("includes/classes/Entity.php");
require_once("includes/classes/EntityProvider.php");
require_once("includes/classes/ErrorMessage.php");
require_once("includes/classes/SeasonProvider.php");
require_once("includes/classes/Season.php");
require_once("includes/classes/Video.php");
require_once("includes/classes/VideoProvider.php");
require_once("includes/classes/User.php");
require_once("includes/wishlist_panel.php");

$userLoggedIn = $_SESSION["userLoggedIn"] ?? null;
$wishlistUrl = $userLoggedIn ? "wishlist.php" : "login.php?returnUrl=" . urlencode("wishlist.php");
$wishlistPanel = buildWishlistPanelData($con, $userLoggedIn);
$currentLanguage = getCurrentLanguage();
$clientTranslations = [
    "wishlistRemoveError" => t("js.wishlist_remove_error"),
    "wishlistAddError" => t("js.wishlist_add_error"),
    "ratingSaveError" => t("js.rating_save_error"),
    "wishlistEmpty" => t("js.wishlist_empty")
];

?>

<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($currentLanguage); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars(t("site.title")); ?></title>

    <link rel="stylesheet" href="assets/style/plyr.css" />
    <link rel="stylesheet" type="text/css" href="assets/style/style.css"/>
    <link rel="stylesheet" href="assets/style/header.css">
    <link rel="stylesheet" href="assets/style/menu.css">
    <link rel="stylesheet" href="assets/style/background.css">

    <script src="https://code.jquery.com/jquery-4.0.0.min.js" integrity="sha256-OaVG6prZf4v69dPg6PhVattBXkcOWQB62pdZ3ORyrao=" crossorigin="anonymous"></script>
    <script src="https://kit.fontawesome.com/eeaac7bcf0.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/plyr@3.7.8/dist/plyr.polyfilled.min.js"></script>
    <script>
        window.cineboxI18n = <?php echo json_encode($clientTranslations, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
    </script>
    <script src="assets/js/script.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
    <div class='wrapper'>

    <div class="topBar">
        <div class="logoContainer">
            <a href="index.php">
                <img src="assets/images/logo.png" alt="CineBox logo">
            </a>
        </div>

        <div class="mobileNavMenu">
            <button
                type="button"
                class="mobileNavToggle"
                title="<?php echo htmlspecialchars(t("nav.menu")); ?>"
                aria-label="<?php echo htmlspecialchars(t("nav.menu")); ?>"
                aria-expanded="false"
                aria-controls="mobileNavDropdown"
            >
                <span><?php echo htmlspecialchars(t("nav.menu")); ?></span>
                <i class="fa-solid fa-chevron-down"></i>
            </button>

            <div id="mobileNavDropdown" class="mobileNavDropdown" aria-hidden="true">
                <a href="index.php" class="mobileNavLink"><?php echo htmlspecialchars(t("nav.home")); ?></a>
                <a href="shows.php" class="mobileNavLink"><?php echo htmlspecialchars(t("nav.tv_shows")); ?></a>
                <a href="movies.php" class="mobileNavLink"><?php echo htmlspecialchars(t("nav.movies")); ?></a>
            </div>
        </div>

        <a class="navButton" href="index.php">
            <span class="top-key"></span>
            <span class="text"><?php echo htmlspecialchars(t("nav.home")); ?></span>
            <span class="bottom-key-1"></span>
            <span class="bottom-key-2"></span>
        </a>
        <a class="navButton" href="shows.php">
            <span class="top-key"></span>
            <span class="text"><?php echo htmlspecialchars(t("nav.tv_shows")); ?></span>
            <span class="bottom-key-1"></span>
            <span class="bottom-key-2"></span>
        </a>
        <a class="navButton" href="movies.php">
            <span class="top-key"></span>
            <span class="text"><?php echo htmlspecialchars(t("nav.movies")); ?></span>
            <span class="bottom-key-1"></span>
            <span class="bottom-key-2"></span>
        </a>

        <div class="rightItems">
            <button class="iconButtons" onclick="window.location.href='search.php'" title="<?php echo htmlspecialchars(t("nav.search")); ?>" aria-label="<?php echo htmlspecialchars(t("nav.search")); ?>"><i class="fa-solid fa-magnifying-glass"></i></button>
            <?php if($userLoggedIn): ?>
                <div class="wishlistMenu">
                    <button
                        type="button"
                        class="iconButtons wishlistToggle"
                        title="<?php echo htmlspecialchars(t("nav.wishlist")); ?>"
                        aria-label="<?php echo htmlspecialchars(t("nav.wishlist")); ?>"
                        aria-expanded="false"
                        aria-controls="wishlistDropdown"
                    >
                        <i class="fa-regular fa-bookmark"></i>
                        <span id="wishlistCountBadge" class="wishlistCountBadge<?php echo $wishlistPanel["count"] > 0 ? " show" : ""; ?>">
                            <?php echo (int)$wishlistPanel["count"]; ?>
                        </span>
                    </button>

                    <div id="wishlistDropdown" class="wishlistDropdown" aria-hidden="true">
                        <div class="wishlistDropdownHeader">
                            <h4><?php echo htmlspecialchars(t("wishlist.my")); ?></h4>
                            <a href="wishlist.php"><?php echo htmlspecialchars(t("wishlist.open_page")); ?></a>
                        </div>
                        <div id="wishlistDropdownBody" class="wishlistDropdownBody">
                            <?php echo $wishlistPanel["itemsHtml"]; ?>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <button class="iconButtons" onclick="window.location.href='<?php echo $wishlistUrl; ?>'" title="<?php echo htmlspecialchars(t("nav.wishlist")); ?>" aria-label="<?php echo htmlspecialchars(t("nav.wishlist")); ?>"><i class="fa-regular fa-bookmark"></i></button>
            <?php endif; ?>
            <button class="iconButtons" onclick="window.location.href='profile.php'" title="<?php echo htmlspecialchars(t("nav.profile")); ?>" aria-label="<?php echo htmlspecialchars(t("nav.profile")); ?>"><i class="fa-regular fa-user"></i></button>
            <button type="button" class="logOutButton" onclick="window.location.href='logout.php'">
                <svg xmlns="http://www.w3.org/2000/svg" class="arr-2" viewBox="0 0 24 24">
                    <path d="M16.1716 10.9999L10.8076 5.63589L12.2218 4.22168L20 11.9999L12.2218 19.778L10.8076 18.3638L16.1716 12.9999H4V10.9999H16.1716Z"></path>
                </svg>
                <span class="text"><?php echo htmlspecialchars(t("nav.logout")); ?></span>
                <span class="circle"></span>
                <svg xmlns="http://www.w3.org/2000/svg" class="arr-1" viewBox="0 0 24 24">
                    <path d="M16.1716 10.9999L10.8076 5.63589L12.2218 4.22168L20 11.9999L12.2218 19.778L10.8076 18.3638L16.1716 12.9999H4V10.9999H16.1716Z"></path>
                </svg>
            </button>
        </div>
    </div>

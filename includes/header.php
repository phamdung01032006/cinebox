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

$userLoggedIn = $_SESSION["userLoggedIn"] ?? null;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CineBox</title>

    <link rel="stylesheet" href="assets/style/plyr.css" />
    <link rel="stylesheet" type="text/css" href="assets/style/style.css"/>

    <script src="https://code.jquery.com/jquery-4.0.0.min.js" integrity="sha256-OaVG6prZf4v69dPg6PhVattBXkcOWQB62pdZ3ORyrao=" crossorigin="anonymous"></script>
    <script src="https://kit.fontawesome.com/eeaac7bcf0.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/plyr@3.7.8/dist/plyr.polyfilled.min.js"></script>
    <script src="assets/js/script.js"></script>
</head>
<body>
    <div class='wrapper'>

    <div class="topBar">

        <div class="logoContainer">
            <a href="index.php">
                <img src="assets/images/cinebox.png" alt="CineBox logo">
            </a>

        </div>

        <ul class="navLinks">
            <li><a href="index.php">Home</a></li>
            <li><a href="shows.php">TV Shows</a></li>
            <li><a href="movies.php">Movie</a></li>
        </ul>

        <div class="rightItems">
            <a href="search.php"><i class="fa-solid fa-magnifying-glass"></i></a>
            <a href="profile.php"><i class="fa-regular fa-user"></i></a>
        </div>

    </div>
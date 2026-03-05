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
    <link rel="stylesheet" href="assets/style/header.css">

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
                <img src="assets/images/logo.png" alt="CineBox logo">
            </a>
        </div>

        <a class="navButton" href="index.php">
            <span class="top-key"></span>
            <span class="text">Home</span>
            <span class="bottom-key-1"></span>
            <span class="bottom-key-2"></span>
        </a>
        <a class="navButton" href="shows.php">
            <span class="top-key"></span>
            <span class="text">TV Shows</span>
            <span class="bottom-key-1"></span>
            <span class="bottom-key-2"></span>
        </a>
        <a class="navButton" href="movies.php">
            <span class="top-key"></span>
            <span class="text">Movies</span>
            <span class="bottom-key-1"></span>
            <span class="bottom-key-2"></span>
        </a>

        <div class="rightItems">
            <button class="iconButtons" onclick="window.location.href='search.php'"><i class="fa-solid fa-magnifying-glass"></i></button>
            <button class="iconButtons" onclick="window.location.href='profile.php'"><i class="fa-regular fa-user"></i></button>
            <button type="button" class="logOutButton" onclick="window.location.href='logout.php'">
                <svg xmlns="http://www.w3.org/2000/svg" class="arr-2" viewBox="0 0 24 24">
                    <path
                    d="M16.1716 10.9999L10.8076 5.63589L12.2218 4.22168L20 11.9999L12.2218 19.778L10.8076 18.3638L16.1716 12.9999H4V10.9999H16.1716Z"
                    ></path>
                </svg>
                <span class="text">Log out</span>
                <span class="circle"></span>
                <svg xmlns="http://www.w3.org/2000/svg" class="arr-1" viewBox="0 0 24 24">
                    <path
                    d="M16.1716 10.9999L10.8076 5.63589L12.2218 4.22168L20 11.9999L12.2218 19.778L10.8076 18.3638L16.1716 12.9999H4V10.9999H16.1716Z"
                    ></path>
                </svg>
            </button>
        </div>
    </div>
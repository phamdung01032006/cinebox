<?php

require_once("includes/config.php");

$language = $_GET["lang"] ?? "en";
setCurrentLanguage($language);

$redirect = $_GET["redirect"] ?? "index.php";

if(!is_string($redirect) || $redirect === "" || preg_match("/^https?:\/\//i", $redirect) || strncmp($redirect, "//", 2) === 0) {
    $redirect = "index.php";
}

header("Location: " . $redirect);
exit();

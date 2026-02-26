<?php

require_once("includes/header.php");

if(!isset($_GET["id"])) {
    // stop everything after receiving this line of code exit
    ErrorMessage::show("No ID passed into page");
}

$video = new Video($con, $_GET["id"]);
$video->incrementView();

?>

<div class="watchContainer">
    <video id="watchPlayer" playsinline preload="metadata">
        <source src="<?php echo htmlspecialchars($video->getFilePath()); ?>" type="video/mp4">
    </video>
</div>

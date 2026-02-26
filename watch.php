<?php
require_once("includes/header.php");

if(!isset($_GET["id"])) {
    ErrorMessage::show("No ID passed into page");
}

$video = new Video($con, $_GET["id"]);
$video->incrementView();

$entity = $video->getEntity();
$seasonProvider = new SeasonProvider($con, $userLoggedIn);
?>

<div class="watchPage">
    <div class="watchContainer">
        <video id="watchPlayer" playsinline preload="metadata" controls>
            <source src="<?php echo htmlspecialchars($video->getFilePath()); ?>" type="video/mp4">
        </video>
    </div>

    <div class="watchEpisodes">
        <?php echo $seasonProvider->create($entity); ?>
    </div>
</div>

<?php


require_once("includes/header.php");

if(!isset($_GET["id"])) {
    // stop everything after receiving this line of code exit
    ErrorMessage::show("No ID passed into page");
}

$entityId = $_GET["id"];
$entity = new Entity($con, $entityId);

$preview = new PreviewProvider($con, $userLoggedIn);
echo $preview->createPreviewVideo($entity);

$seasonProvider = new SeasonProvider($con, $userLoggedIn);
echo $seasonProvider->create($entity);

$categoryContainers = new CategoryContainers($con, $userLoggedIn);
echo $categoryContainers->showCategory($entity->getCategoryId(), "You might also like");

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CineBox</title>
    <link rel="stylesheet" type="text/css" href="assets/style/style.css"/>
</head>
<body>
    <div id="videoPopup" class="videoPopup">

        <div class="videoPopupBackdrop" onclick="closeVideoPopup()"></div>
        <div class="videoPopupContent">
            <button class="videoPopupClose" onclick="closeVideoPopup()">
                <i class="fa-solid fa-xmark"></i>
            </button>
            <h3 id="videoPopupTitle"></h3>
            <!-- <video id="videoPopupPlayer" controls autoplay>
                <source src="" type="video/mp4">
            </video> -->
            <video id="videoPopupPlayer" playsinline></video>
        </div>
    </div>

    <?php require_once("footer.php"); ?>
</body>
</html>

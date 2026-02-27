<?php

require_once("includes/header.php");

$preview = new PreviewProvider($con, $userLoggedIn);
echo $preview->createPreviewVideo(null);

$containers = new CategoryContainers($con, $userLoggedIn);
echo $containers->showAllCategories();

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
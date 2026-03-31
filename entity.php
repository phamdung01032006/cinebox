<?php


require_once("includes/header.php");

if(!isset($_GET["id"])) {
    // stop everything after receiving this line of code exit
    ErrorMessage::show("No ID passed into page");
}

$entityId = $_GET["id"];
$entity = new Entity($con, $entityId);
$userRating = 0;
$averageRating = 0;

RecommendationProvider::ensureSchema($con);
if($userLoggedIn) {
    $ratingUser = new User($con, $userLoggedIn);
    $userRating = $ratingUser->getEntityRating($entityId);
}

$averageRatingQuery = $con->prepare("SELECT AVG(rating) AS averageRating FROM entityRatings WHERE entityId = :entityId");
$averageRatingQuery->bindValue(":entityId", (int)$entityId, PDO::PARAM_INT);
$averageRatingQuery->execute();
$averageRating = (float)($averageRatingQuery->fetch(PDO::FETCH_ASSOC)["averageRating"] ?? 0);

$preview = new PreviewProvider($con, $userLoggedIn);
echo $preview->createPreviewVideo($entity);
?>

<div class="entityRatingSection">
    <div class="entityRatingCard">
        <h3>Rate this title</h3>
        <div class="entityRatingStars" data-entity-id="<?php echo (int)$entityId; ?>">
            <?php for($i = 1; $i <= 5; $i++): ?>
                <button
                    type="button"
                    class="ratingStar<?php echo $i <= $userRating ? " active" : ""; ?>"
                    data-rating="<?php echo $i; ?>"
                    <?php echo !$userLoggedIn ? "disabled" : ""; ?>
                    title="<?php echo $userLoggedIn ? "Rate $i star" . ($i > 1 ? "s" : "") : "Log in to rate"; ?>"
                >
                    <i class="fa-solid fa-star"></i>
                </button>
            <?php endfor; ?>
        </div>
        <p class="entityRatingMeta">
            Your rating: <span class="entityUserRatingValue"><?php echo $userRating > 0 ? $userRating . "/5" : "Not rated yet"; ?></span>
            <br>
            Community rating: <span class="entityAverageRatingValue"><?php echo $averageRating > 0 ? number_format($averageRating, 1) . "/5" : "No ratings yet"; ?></span>
        </p>
    </div>
</div>
<?php

$seasonProvider = new SeasonProvider($con, $userLoggedIn);
echo $seasonProvider->create($entity);
$similarEntities = EntityProvider::getSimilarEntities($con, $entity->getId(), 10);
$similarEntitiesHtml = "";

foreach($similarEntities as $similarEntity) {
    $similarEntitiesHtml .= $preview->createEntityPreviewSquare($similarEntity);
}

if($similarEntitiesHtml !== "") {
    echo "<div class='previewCategories noScroll'>
            <div class='category'>
                <div class='category-header'>
                    <h3>Similar titles</h3>
                    <div class='category-arrows'>
                        <button class='scroll-arrow left'><i class='fa-solid fa-chevron-left'></i></button>
                        <button class='scroll-arrow right'><i class='fa-solid fa-chevron-right'></i></button>
                    </div>
                </div>
                <div class='entities'>
                    $similarEntitiesHtml
                </div>
            </div>
        </div>";
}

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

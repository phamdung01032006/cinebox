<?php
require_once("includes/header.php");

if(!isset($_SESSION["userLoggedIn"])) {
    header("Location: login.php?returnUrl=" . urlencode("watch.php?id=" . $_GET["id"]));
    exit();
}

if(!isset($_GET["id"])) {
    ErrorMessage::show("No ID passed into page");
}

$video = new Video($con, $_GET["id"]);
$video->incrementView();

$entity = $video->getEntity();
$seasonProvider = new SeasonProvider($con, $userLoggedIn);

// Tạo dữ liệu related TRƯỚC khi render HTML
$previewProvider = new PreviewProvider($con, $userLoggedIn);
$relatedEntities = EntityProvider::getEntities($con, $entity->getCategoryId(), 30);

$relatedHtml = "";
$relatedCount = 0;

foreach($relatedEntities as $relatedEntity) {
    if($relatedEntity->getId() == $entity->getId()) continue;

    $relatedHtml .= $previewProvider->createEntityPreviewSquare($relatedEntity);
    $relatedCount++;
    if($relatedCount >= 12) break;
}

$upNextVideo = VideoProvider::getUpNext($con, $video);
?>
<link rel="stylesheet" href="assets/style/watch.css">
<div class="watchPage">
    <div class="watchContainer">
        <div class="videoControls watchNav">
            <!-- <button onclick="goBack()"><i class="fa-solid fa-arrow-left"></i></button> -->
            <button class="goBackButton" onclick="goBack()">
                <svg xmlns="http://www.w3.org/2000/svg" class="arr-2" viewBox="0 0 24 24">
                    <path
                    d="M16.1716 10.9999L10.8076 5.63589L12.2218 4.22168L20 11.9999L12.2218 19.778L10.8076 18.3638L16.1716 12.9999H4V10.9999H16.1716Z"
                    ></path>
                </svg>
                <span class="text">Back</span>
                <span class="circle"></span>
                <svg xmlns="http://www.w3.org/2000/svg" class="arr-1" viewBox="0 0 24 24">
                    <path
                    d="M16.1716 10.9999L10.8076 5.63589L12.2218 4.22168L20 11.9999L12.2218 19.778L10.8076 18.3638L16.1716 12.9999H4V10.9999H16.1716Z"
                    ></path>
                </svg>
            </button>
            <h2><?php echo $video->getTitle(); ?></h2>
        </div>

        <!-- các nút chuyển tập sau khi xem xong-->
        <div class="videoControls upNext">

            <div class="upNextContainer">
                <div class="upNextText">
                    <h2>Up next: </h2>
                    <h3><?php echo $upNextVideo->getTitle(); ?></h3>
                    <h3><?php echo $upNextVideo->getSeasonAndEpisode(); ?></h3>
                </div>
                <div class="upNextAction">
                    <button class="cssbuttons-io-button" onclick="watchVideo(<?php echo $upNextVideo->getId(); ?>)">
                        Next
                        <div class="icon">
                            <svg
                            height="24"
                            width="24"
                            viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg"
                            >
                            <path d="M0 0h24v24H0z" fill="none"></path>
                            <path
                                d="M16.172 11l-5.364-5.364 1.414-1.414L20 12l-7.778 7.778-1.414-1.414L16.172 13H4v-2z"
                                fill="currentColor"
                            ></path>
                            </svg>
                        </div>
                    </button>
                </div>
            </div>
        </div>

        <video id="watchPlayer" playsinline preload="metadata" onended="showUpNext()" onplaying="hideUpNext()">
            <source src="<?php echo htmlspecialchars($video->getFilePath()); ?>" type="video/mp4">
        </video>
    </div>

    <div class="watchEpisodes">
        <?php echo $seasonProvider->create($entity); ?>
    </div>

    <?php if($relatedCount > 0): ?>
    <div class="watchRelated">
        <div class="category">
            <div class="category-header">
                <h3>You might also like</h3>
                <div class="category-arrows">
                    <button class="scroll-arrow left"><i class="fa-solid fa-chevron-left"></i></button>
                    <button class="scroll-arrow right"><i class="fa-solid fa-chevron-right"></i></button>
                </div>
            </div>
            <div class="entities">
                <?php echo $relatedHtml; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
    initVideo("<?php echo $video->getId(); ?>", "<?php echo $userLoggedIn; ?>");
</script>

<?php require_once("footer.php"); ?>

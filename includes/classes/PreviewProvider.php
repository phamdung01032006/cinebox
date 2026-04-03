<?php 

class PreviewProvider {

    private $con;
    private $username;

    public function __construct($con, $username) {

        $this->con = $con;
        $this->username = $username;

    }

    public function createCategoryPreviewVideo($categoryId) {
        $entitiesArray = EntityProvider::getEntities($this->con, $categoryId, 1);

        if(sizeof($entitiesArray) == 0) {
            ErrorMessage::show(t("preview.no_tv_shows"));
        }

        return $this->createPreviewVideo($entitiesArray[0]);
    }

    // chia trang TV shows
    public function createTVShowPreviewVideo() {
        $entitiesArray = EntityProvider::getTVShowEntities($this->con, null, 1);

        if(sizeof($entitiesArray) == 0) {
            ErrorMessage::show(t("preview.no_tv_shows"));
        }

        return $this->createPreviewVideo($entitiesArray[0]);
    }

    public function createMoviesPreviewVideo() {
    $entitiesArray = EntityProvider::getMoviesEntities($this->con, null, 1);

    if(sizeof($entitiesArray) == 0) {
        ErrorMessage::show(t("preview.no_movies"));
    }

    return $this->createPreviewVideo($entitiesArray[0]);
}

	    public function createPreviewVideo($entity) {

        if($entity == null) {
            $entity = $this->getRandomEntity();
        }

        $id = $entity->getId();
        $name = $entity->getName();
        $preview = $entity->getPreview();
        $thumbnail = $entity->getThumbnail();

	        $safePreview = htmlspecialchars($preview, ENT_QUOTES, 'UTF-8');
	        $safeName = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
	        $wishlistButton = $this->createWishlistButton($id, "featuredWishlistBtn");
            $previewTags = $entity->getPreviewTags();
            $previewTagsHtml = $previewTags !== "" ? "<div class='previewTags' title='" . strip_tags($previewTags) . "'>$previewTags</div>" : "";

        $videoId = VideoProvider::getPlayableEntityVideoForUser($this->con, $id, $this->username);
        if(!$videoId) {
            $videoId = VideoProvider::getEntityVideoForUser($this->con, $id, $this->username);
        }

        $video = new Video($this->con, $videoId);
        $user = $this->username ? new User($this->con, $this->username) : null;
        $canWatchVideo = !$user || $user->canWatchVideo($video);

        $inProgress = $video->isInProgress($this->username);
        $playButtonText = $inProgress ? t("preview.continue_watching") : t("preview.play");
        $playButtonIcon = "fa-solid fa-play";
        $playButtonAction = "watchVideo($videoId)";
        $playButtonClass = "playBtn";
        $accessNotice = "";

        if($user && !$canWatchVideo) {
            $playButtonText = t("access.subscribe_to_watch");
            $playButtonIcon = "fa-solid fa-lock";
            $playButtonAction = "window.location.href='paypal.php'";
            $playButtonClass .= " locked";
            $accessNotice = "<p class='previewAccessNotice'>" . htmlspecialchars($user->getVideoAccessMessage($video), ENT_QUOTES, "UTF-8") . "</p>";
        }

        $seasonEpisode = $video->getSeasonAndEpisode();
        $title = htmlspecialchars($video->getTitle(), ENT_QUOTES, 'UTF-8');
        $subHeading = $video->isMovie() ? "" : "<h4>" . htmlspecialchars($seasonEpisode, ENT_QUOTES, "UTF-8") . "</h4>";

	        return "<div class='previewContainer'>
	            <img src='$thumbnail' class='previewImage' hidden>

            <video autoplay muted class='previewVideo' onended='previewEnded()'>
                <source src='$preview' type='video/mp4'>
            </video>

            <div class='previewOverlay'>

                <div class='mainDetails'>

	                    <h3>$name</h3>
	                    <h4>$title</h4>
                        $previewTagsHtml
	                    $subHeading
	                    $accessNotice
	                    <div class='button'>
	                    
		                        <button class='$playButtonClass' onclick=\"$playButtonAction\"><i class='$playButtonIcon'></i> $playButtonText</button>
		                        <button onclick='volumeToggle(this)'><i class='fa-solid fa-volume-xmark'></i></button>
		                        <button class='openPopupBtn' onclick='openVideoPopup(this)' data-src = '$safePreview' data-title='$safeName'><i class='fa-solid fa-expand'></i></button>
                                $wishlistButton
		                    </div>

                </div>

            </div>

        </div>";
    }

	    public function createEntityPreviewSquare($entity) {
	        $id = $entity->getId();
	        $thumbnail = $entity->getThumbnail();
	        $name = $entity->getName();
	        $safeName = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');

        $wishlistButton = $this->createWishlistButton($id, "entityWishlistBtn");

		        return "<div class='entityCard' data-entity-id='$id'>
                            $wishlistButton
	                        <a href='entity.php?id=$id' class='entityCardLink'>
	    	                    <div class='previewContainer small'>
	    	                        <img src='$thumbnail' title='$safeName' alt='$safeName'>
    	                    </div>
    	                    <div class='entityTitle'>$safeName</div>
                        </a>
	        </div>";
	    }

    public function createContinueWatchingSquare($video, $progressSeconds) {
        $entity = $video->getEntity();
        $entityName = htmlspecialchars($entity->getName(), ENT_QUOTES, 'UTF-8');
        $thumbnail = htmlspecialchars($entity->getThumbnail(), ENT_QUOTES, 'UTF-8');
        $videoId = (int)$video->getId();
        $detailText = $video->isMovie()
            ? htmlspecialchars($video->getTitle(), ENT_QUOTES, 'UTF-8')
            : htmlspecialchars($video->getSeasonAndEpisode(), ENT_QUOTES, 'UTF-8');

        $durationSeconds = $video->getDurationInSeconds();
        $progressPercent = $durationSeconds > 0
            ? max(0, min(100, (int)round(($progressSeconds / $durationSeconds) * 100)))
            : 0;
        $progressStyle = "style='width: " . $progressPercent . "%;'";

        return "<div class='continueWatchCard'>
                    <a href='watch.php?id=$videoId' class='continueWatchLink'>
                        <div class='continueWatchThumbWrap'>
                            <img src='$thumbnail' alt='$entityName' class='continueWatchThumb'>
                            <span class='continueWatchBadge'>" . htmlspecialchars(t("preview.resume_now"), ENT_QUOTES, 'UTF-8') . "</span>
                            <div class='continueWatchProgress'>
                                <span $progressStyle></span>
                            </div>
                        </div>
                        <div class='continueWatchTitle'>$entityName</div>
                        <div class='continueWatchSubtitle'>$detailText</div>
                    </a>
                </div>";
    }

    public function createWishlistDropdownItem($entity) {
        $id = $entity->getId();
        $thumbnail = htmlspecialchars($entity->getThumbnail(), ENT_QUOTES, 'UTF-8');
        $safeName = htmlspecialchars($entity->getName(), ENT_QUOTES, 'UTF-8');
        $wishlistButton = $this->createWishlistButton($id, "wishlistDropdownAction");

        return "<div class='wishlistDropdownItem' data-entity-id='$id'>
                    <a href='entity.php?id=$id' class='wishlistDropdownItemLink'>
                        <img src='$thumbnail' alt='$safeName' class='wishlistDropdownThumb'>
                        <span class='wishlistDropdownTitle'>$safeName</span>
                    </a>
                    $wishlistButton
                </div>";
    }

    private function createWishlistButton($entityId, $extraClass = "", $showLabel = false) {
        if(!$this->username) {
            return "";
        }

        $user = new User($this->con, $this->username);
        $isInWishlist = $user->hasEntityInWishlist($entityId);
        $wishlistStateClass = $isInWishlist ? " active" : "";
        $wishlistIconClass = $isInWishlist ? "fa-solid fa-check" : "fa-solid fa-plus";
        $addWishlistTitle = t("preview.add_to_wishlist");
        $removeWishlistTitle = t("preview.remove_from_wishlist");
        $wishlistTitle = $isInWishlist ? $removeWishlistTitle : $addWishlistTitle;
        $ariaPressed = $isInWishlist ? "true" : "false";
        $buttonClass = trim("wishlistBtn$wishlistStateClass $extraClass");
        $labelHtml = $showLabel
            ? "<span class='wishlistBtnLabel'>" . htmlspecialchars(t("preview.my_list"), ENT_QUOTES, 'UTF-8') . "</span>"
            : "";

        return "<button class='$buttonClass' type='button' data-entity-id='$entityId' data-icon-default='fa-solid fa-plus' data-icon-active='fa-solid fa-check' data-title-add='$addWishlistTitle' data-title-remove='$removeWishlistTitle' onclick='addToWishlist($entityId, this)' title='$wishlistTitle' aria-label='$wishlistTitle' aria-pressed='$ariaPressed'>
                    <i class='$wishlistIconClass'></i>
                    $labelHtml
                </button>";
    }


    // chọn film ngẫu nhiên để chiếu preview
    private function getRandomEntity() {

        $entity = EntityProvider::getEntities($this->con, null, 1);
        return $entity[0];

    }

}

?>

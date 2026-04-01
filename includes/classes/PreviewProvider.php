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
        $wishlistButton = $this->createWishlistButton($id);
        

	        $videoId = VideoProvider::getEntityVideoForUser($this->con, $id, $this->username);
	        $video = new Video($this->con, $videoId);

        $inProgress = $video->isInProgress($this->username);
	        $playButtonText = $inProgress ? t("preview.continue_watching") : t("preview.play");
	        $seasonEpisode = $video->getSeasonAndEpisode();
	        $title = $video->getTitle();
	        $subHeading = $video->isMovie() ? "": "<h4>$seasonEpisode</h4>";

	        return "<div class='previewContainer'>
	            <img src='$thumbnail' class='previewImage' hidden>

            <video autoplay muted class='previewVideo' onended='previewEnded()'>
                <source src='$preview' type='video/mp4'>
            </video>

            <div class='previewOverlay'>

                <div class='mainDetails'>

                    <h3>$name</h3>
                    <h4>$title</h4>
                    $subHeading
	                    <div class='button'>
	                    
		                        <button class='playBtn' onclick='watchVideo($videoId)'><i class='fa-solid fa-play'></i> $playButtonText</button>
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

	        return "<div class='entityCard' data-entity-id='$id'>
                        <a href='entity.php?id=$id' class='entityCardLink'>
    	                    <div class='previewContainer small'>
    	                        <img src='$thumbnail' title='$safeName' alt='$safeName'>
    	                    </div>
    	                    <div class='entityTitle'>$safeName</div>
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

    private function createWishlistButton($entityId, $extraClass = "") {
        if(!$this->username) {
            return "";
        }

        $user = new User($this->con, $this->username);
        $isInWishlist = $user->hasEntityInWishlist($entityId);
        $wishlistStateClass = $isInWishlist ? " active" : "";
        $wishlistIconClass = $isInWishlist ? "fa-solid fa-check" : "fa-regular fa-bookmark";
        $addWishlistTitle = t("preview.add_to_wishlist");
        $removeWishlistTitle = t("preview.remove_from_wishlist");
        $wishlistTitle = $isInWishlist ? $removeWishlistTitle : $addWishlistTitle;
        $ariaPressed = $isInWishlist ? "true" : "false";
        $buttonClass = trim("wishlistBtn$wishlistStateClass $extraClass");

        return "<button class='$buttonClass' type='button' data-entity-id='$entityId' data-icon-default='fa-regular fa-bookmark' data-icon-active='fa-solid fa-check' data-title-add='$addWishlistTitle' data-title-remove='$removeWishlistTitle' onclick='addToWishlist($entityId, this)' title='$wishlistTitle' aria-label='$wishlistTitle' aria-pressed='$ariaPressed'>
                    <i class='$wishlistIconClass'></i>
                </button>";
    }


    // chọn film ngẫu nhiên để chiếu preview
    private function getRandomEntity() {

        $entity = EntityProvider::getEntities($this->con, null, 1);
        return $entity[0];

    }

}

?>

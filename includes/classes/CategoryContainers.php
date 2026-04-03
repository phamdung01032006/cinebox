<?php 

class CategoryContainers {

    private $con;
    private $username;

    public function __construct($con, $username) {

        $this->con = $con;
        $this->username = $username;

    }

    public function showAllCategories() {
        $query = $this->con->prepare("SELECT * FROM categories");
        $query->execute();

        $html = "<div class='previewCategories'>";

        if($this->username) {
            $html .= $this->getContinueWatchingCategoryHtml(true, true);
            $html .= $this->getBecauseYouWatchedCategoryHtml(true, true);
        }

        $html .= $this->getMostViewedCategoryHtml(true, true);
        $html .= $this->getNewestCategoryHtml(true, true);

        while($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $html .= $this->getCategoryHtml($row, null, true, true);
        }

        return $html . "</div>";
    }

    public function showTVShowCategories() {
        $query = $this->con->prepare("SELECT * FROM categories");
        $query->execute();

        $html = "<div class='previewCategories'>
                    <h1>" . htmlspecialchars(t("category.tv_shows_heading"), ENT_QUOTES, "UTF-8") . "</h1>";

        if($this->username) {
            $html .= $this->getContinueWatchingCategoryHtml(true, false);
            $html .= $this->getBecauseYouWatchedCategoryHtml(true, false);
        }

        $html .= $this->getMostViewedCategoryHtml(true, false);
        $html .= $this->getNewestCategoryHtml(true, false);

        while($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $html .= $this->getCategoryHtml($row, null, true, false);
        }

        return $html . "</div>";
    }

    public function showMovieCategories() {
        $query = $this->con->prepare("SELECT * FROM categories");
        $query->execute();

        $html = "<div class='previewCategories'>
                    <h1>" . htmlspecialchars(t("category.movies_heading"), ENT_QUOTES, "UTF-8") . "</h1>";

        if($this->username) {
            $html .= $this->getContinueWatchingCategoryHtml(false, true);
            $html .= $this->getBecauseYouWatchedCategoryHtml(false, true);
        }

        $html .= $this->getMostViewedCategoryHtml(false, true);
        $html .= $this->getNewestCategoryHtml(false, true);

        while($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $html .= $this->getCategoryHtml($row, null, false, true);
        }

        return $html . "</div>";
    }

    public function showCategory($categoryId, $title = null) {
        $query = $this->con->prepare("SELECT * FROM categories WHERE id=:id");
        $query->bindValue(":id",$categoryId);
        $query->execute();

        $html = "<div class='previewCategories noScroll'>";

        while($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $html .= $this->getCategoryHtml($row, $title, true, true);
        }

        return $html . "</div>";
    }

    private function getCategoryHtml($sqlData, $title, $tvShows, $movies) {
        $categoryId = $sqlData["id"];
        $title = $title == null ? $sqlData["name"] : $title;

        if($tvShows && $movies) {
            $entities = EntityProvider::getEntities($this->con, $categoryId, 30);
        }
        else if($tvShows) {
            $entities = EntityProvider::getTVShowEntities($this->con, $categoryId, 30);
        }
        else {
            $entities = EntityProvider::getMoviesEntities($this->con, $categoryId, 30);
        }

        if(sizeof($entities) == 0) {
            return;
        }

        $entitiesHtml = "";
        $previewProvider = new PreviewProvider($this->con, $this->username);
        foreach($entities as $entity) {
            $entitiesHtml .= $previewProvider->createEntityPreviewSquare($entity);
        }

	        return "<div class='category'>
                <div class='category-header'>
                    <a href='category.php?id=$categoryId'>
                        <h3>$title</h3>
                    </a>
                    <div class='category-arrows'>
                        <button class='scroll-arrow left'><i class='fa-solid fa-chevron-left'></i></button>
                        <button class='scroll-arrow right'><i class='fa-solid fa-chevron-right'></i></button>
                    </div>
                </div>
                <div class='entities'>
                    $entitiesHtml
                </div>
	        </div>";
    }

	    private function getBecauseYouWatchedCategoryHtml($tvShows, $movies) {
	        $recommendation = EntityProvider::getBecauseYouWatchedRecommendation($this->con, $this->username, 30, $movies, $tvShows);

        if(!$recommendation || empty($recommendation["entities"])) {
            return "";
        }

        $seedTitle = $recommendation["seedTitle"] ?? "";
        $title = htmlspecialchars(t("category.because_you_watched", ["title" => $seedTitle]), ENT_QUOTES, "UTF-8");
        $entitiesHtml = "";
        $previewProvider = new PreviewProvider($this->con, $this->username);
        foreach($recommendation["entities"] as $entity) {
            $entitiesHtml .= $previewProvider->createEntityPreviewSquare($entity);
        }

	        return $this->renderCategoryRow($title, $entitiesHtml, "recommendedCategory");
	    }

    private function getContinueWatchingCategoryHtml($tvShows, $movies) {
        $items = VideoProvider::getContinueWatchingVideos($this->con, $this->username, 30, $movies, $tvShows);

        if(empty($items)) {
            return "";
        }

        $itemsHtml = "";
        $previewProvider = new PreviewProvider($this->con, $this->username);
        foreach($items as $item) {
            $itemsHtml .= $previewProvider->createContinueWatchingSquare($item["video"], (int)$item["progress"]);
        }

        $title = htmlspecialchars(t("category.continue_watching"), ENT_QUOTES, "UTF-8");

	        return $this->renderCategoryRow($title, $itemsHtml, "continueWatchingCategory", "continueWatchingRow");
	    }

    private function getMostViewedCategoryHtml($tvShows, $movies) {
        $entities = EntityProvider::getMostViewedEntities($this->con, 10, $movies, $tvShows);

        if(empty($entities)) {
            return "";
        }

        $title = htmlspecialchars(t("category.top_viewed"), ENT_QUOTES, "UTF-8");
        $previewProvider = new PreviewProvider($this->con, $this->username);
        $entitiesHtml = "";
        foreach($entities as $entity) {
            $entitiesHtml .= $previewProvider->createEntityPreviewSquare($entity);
        }

        return $this->renderCategoryRow($title, $entitiesHtml, "topViewedCategory");
    }

    private function getNewestCategoryHtml($tvShows, $movies) {
        $entities = EntityProvider::getNewestEntities($this->con, 10, $movies, $tvShows);

        if(empty($entities)) {
            return "";
        }

        $title = htmlspecialchars(t("category.newest_releases"), ENT_QUOTES, "UTF-8");
        $previewProvider = new PreviewProvider($this->con, $this->username);
        $entitiesHtml = "";
        foreach($entities as $entity) {
            $entitiesHtml .= $previewProvider->createEntityPreviewSquare($entity);
        }

        return $this->renderCategoryRow($title, $entitiesHtml, "newestCategory");
    }

    private function renderCategoryRow($title, $itemsHtml, $categoryClass = "", $entitiesClass = "") {
        $categoryClass = trim("category " . $categoryClass);
        $entitiesClass = trim("entities " . $entitiesClass);

        return "<div class='$categoryClass'>
                <div class='category-header'>
                    <h3>$title</h3>
                    <div class='category-arrows'>
                        <button class='scroll-arrow left'><i class='fa-solid fa-chevron-left'></i></button>
                        <button class='scroll-arrow right'><i class='fa-solid fa-chevron-right'></i></button>
                    </div>
                </div>
                <div class='$entitiesClass'>
                    $itemsHtml
                </div>
        </div>";
    }

}
?>

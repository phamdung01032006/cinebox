<?php 

class CategoryContainers {

    private $con;
    private $username;

    public function __construct($con, $username) {

        $this->con = $con;
        $this->username = $username;

    }

    public function showAllCategories() {
        $query = $this->con->prepare("SELECT * FROM categories ORDER BY id ASC LIMIT :limit");
        $query->bindValue(":limit", (int)HOMEPAGE_MAX_CATEGORIES, PDO::PARAM_INT);
        $query->execute();

        $html = "<div class='previewCategories'>";

        if($this->username) {
            $html .= $this->getRecommendedForYouCategoryHtml(true, true);
            $html .= $this->getContinueWatchingCategoryHtml(true, true);
            $html .= $this->getBecauseYouWatchedCategoryHtml(true, true);
        }

        $html .= $this->getMostViewedCategoryHtml(true, true);
        $html .= $this->getNewestCategoryHtml(true, true);

        while($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $html .= $this->getCategoryHtml($row, null, true, true, HOME_CATEGORY_ROW_LIMIT);
        }

        return $html . "</div>";
    }

    public function showTVShowCategories() {
        $query = $this->con->prepare("
            SELECT c.*
            FROM categories c
            WHERE EXISTS (
                SELECT 1
                FROM entities e
                INNER JOIN videos v ON v.entityId = e.id
                WHERE e.categoryId = c.id
                AND v.isMovie = 0
            )
            ORDER BY c.id ASC
            LIMIT :limit
        ");
        $query->bindValue(":limit", (int)BROWSE_MAX_CATEGORIES, PDO::PARAM_INT);
        $query->execute();

        $html = "<div class='previewCategories'>
                    <h1>" . htmlspecialchars(t("category.tv_shows_heading"), ENT_QUOTES, "UTF-8") . "</h1>";

        if($this->username) {
            $html .= $this->getRecommendedForYouCategoryHtml(true, false);
            $html .= $this->getContinueWatchingCategoryHtml(true, false);
            $html .= $this->getBecauseYouWatchedCategoryHtml(true, false);
        }

        $html .= $this->getMostViewedCategoryHtml(true, false, FEATURED_ROW_LIMIT);
        $html .= $this->getNewestCategoryHtml(true, false, FEATURED_ROW_LIMIT);

        while($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $html .= $this->getCategoryHtml($row, null, true, false, BROWSE_CATEGORY_ROW_LIMIT);
        }

        return $html . "</div>";
    }

    public function showMovieCategories() {
        $query = $this->con->prepare("
            SELECT c.*
            FROM categories c
            WHERE EXISTS (
                SELECT 1
                FROM entities e
                INNER JOIN videos v ON v.entityId = e.id
                WHERE e.categoryId = c.id
                AND v.isMovie = 1
            )
            ORDER BY c.id ASC
            LIMIT :limit
        ");
        $query->bindValue(":limit", (int)BROWSE_MAX_CATEGORIES, PDO::PARAM_INT);
        $query->execute();

        $html = "<div class='previewCategories'>
                    <h1>" . htmlspecialchars(t("category.movies_heading"), ENT_QUOTES, "UTF-8") . "</h1>";

        if($this->username) {
            $html .= $this->getRecommendedForYouCategoryHtml(false, true);
            $html .= $this->getContinueWatchingCategoryHtml(false, true);
            $html .= $this->getBecauseYouWatchedCategoryHtml(false, true);
        }

        $html .= $this->getMostViewedCategoryHtml(false, true, FEATURED_ROW_LIMIT);
        $html .= $this->getNewestCategoryHtml(false, true, FEATURED_ROW_LIMIT);

        while($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $html .= $this->getCategoryHtml($row, null, false, true, BROWSE_CATEGORY_ROW_LIMIT);
        }

        return $html . "</div>";
    }

    public function showCategory($categoryId, $title = null) {
        $query = $this->con->prepare("SELECT * FROM categories WHERE id=:id");
        $query->bindValue(":id",$categoryId);
        $query->execute();

        $html = "<div class='previewCategories noScroll'>";

        while($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $html .= $this->getCategoryHtml($row, $title, true, true, CATEGORY_PAGE_ROW_LIMIT);
        }

        return $html . "</div>";
    }

    private function getCategoryHtml($sqlData, $title, $tvShows, $movies, $limit = BROWSE_CATEGORY_ROW_LIMIT) {
        $categoryId = $sqlData["id"];
        $title = $title == null ? $sqlData["name"] : $title;
        $limit = max(1, (int)$limit);

        if($tvShows && $movies) {
            $entities = EntityProvider::getEntities($this->con, $categoryId, $limit);
        }
        else if($tvShows) {
            $entities = EntityProvider::getTVShowEntities($this->con, $categoryId, $limit);
        }
        else {
            $entities = EntityProvider::getMoviesEntities($this->con, $categoryId, $limit);
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
	        $recommendation = EntityProvider::getBecauseYouWatchedRecommendation($this->con, $this->username, RECOMMENDATION_ROW_LIMIT, $movies, $tvShows);

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

    private function getRecommendedForYouCategoryHtml($tvShows, $movies) {
        $entities = EntityProvider::getRecommendedEntitiesForUser(
            $this->con,
            $this->username,
            RECOMMENDATION_ROW_LIMIT,
            $movies,
            $tvShows
        );

        if(empty($entities)) {
            return "";
        }

        $title = htmlspecialchars(t("category.recommended_for_you"), ENT_QUOTES, "UTF-8");
        $entitiesHtml = "";
        $previewProvider = new PreviewProvider($this->con, $this->username);
        foreach($entities as $entity) {
            $entitiesHtml .= $previewProvider->createEntityPreviewSquare($entity);
        }

        return $this->renderCategoryRow($title, $entitiesHtml, "recommendedCategory");
    }

    private function getContinueWatchingCategoryHtml($tvShows, $movies) {
        $items = VideoProvider::getContinueWatchingVideos($this->con, $this->username, CONTINUE_WATCHING_LIMIT, $movies, $tvShows);

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

    private function getMostViewedCategoryHtml($tvShows, $movies, $limit = FEATURED_ROW_LIMIT) {
        $entities = EntityProvider::getMostViewedEntities($this->con, (int)$limit, $movies, $tvShows);

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

    private function getNewestCategoryHtml($tvShows, $movies, $limit = FEATURED_ROW_LIMIT) {
        $entities = EntityProvider::getNewestEntities($this->con, (int)$limit, $movies, $tvShows);

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

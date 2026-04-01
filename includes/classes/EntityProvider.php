<?php

class EntityProvider {

    public static function getEntities($con, $categoryId, $limit) {

        $sql = "SELECT * FROM entities ";
        
        if($categoryId != null) {
            $sql .= "WHERE categoryId=:categoryId ";
        }

        $sql .= "ORDER BY RAND() LIMIT :limit";

        $query = $con->prepare($sql);

        if($categoryId != null) {
            $query->bindValue(":categoryId", $categoryId);
        }

        $query->bindValue(":limit", $limit, PDO::PARAM_INT);
        $query->execute();

        $result = array();
        while($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $result[] = new Entity($con, $row);
        }

        return $result;
    }

    public static function getTVShowEntities($con, $categoryId, $limit) {

        $sql = "SELECT DISTINCT(entities.id) FROM `entities`
                INNER JOIN videos
                ON entities.id = videos.entityId
                WHERE videos.isMovie=0 ";
        
        if($categoryId != null) {
            $sql .= "AND categoryId=:categoryId ";
        }

        $sql .= "ORDER BY RAND() LIMIT :limit";

        $query = $con->prepare($sql);

        if($categoryId != null) {
            $query->bindValue(":categoryId", $categoryId);
        }

        $query->bindValue(":limit", $limit, PDO::PARAM_INT);
        $query->execute();

        $result = array();
        while($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $result[] = new Entity($con, $row["id"]);
        }

        return $result;
    }

    public static function getMoviesEntities($con, $categoryId, $limit) {

        $sql = "SELECT DISTINCT(entities.id) FROM `entities`
                INNER JOIN videos
                ON entities.id = videos.entityId
                WHERE videos.isMovie=1 ";
        
        if($categoryId != null) {
            $sql .= "AND categoryId=:categoryId ";
        }

        $sql .= "ORDER BY RAND() LIMIT :limit";

        $query = $con->prepare($sql);

        if($categoryId != null) {
            $query->bindValue(":categoryId", $categoryId);
        }

        $query->bindValue(":limit", $limit, PDO::PARAM_INT);
        $query->execute();

        $result = array();
        while($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $result[] = new Entity($con, $row["id"]);
        }

        return $result;
    }

    public static function getSearchEntities($con, $term) {

        $sql = "SELECT * FROM entities WHERE name LIKE CONCAT('%', :term, '%') LIMIT 30";

        $query = $con->prepare($sql);

        $query->bindValue(":term", $term);
        $query->execute();

        $result = array();
        while($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $result[] = new Entity($con, $row);
        }

        return $result;
    }

    public static function getRecommendedEntitiesForUser($con, $username, $limit = 30, $movies = true, $tvShows = true) {

        if(!$username) {
            return [];
        }

        $entityIds = self::fetchRecommendedEntityIdsFromApi($con, $username);

        if(!empty($entityIds)) {
            $entities = self::getEntitiesByIds($con, $entityIds, $movies, $tvShows);

            if(!empty($entities)) {
                return array_slice($entities, 0, $limit);
            }
        }

        return self::getFallbackRecommendedEntities($con, $username, $limit, $movies, $tvShows);
    }

    public static function getBecauseYouWatchedRecommendation($con, $username, $limit = 30, $movies = true, $tvShows = true) {

        if(!$username) {
            return null;
        }

        RecommendationProvider::ensureSchema($con);

        $seedEntities = self::getRecentFiveStarSeedData($con, $username, 3, $movies, $tvShows);
        if(empty($seedEntities)) {
            return null;
        }

        shuffle($seedEntities);

        foreach($seedEntities as $seedEntity) {
            $seedEntityId = (int)$seedEntity["id"];
            $seedName = $seedEntity["name"] ?? "";
            $seedIsMovie = isset($seedEntity["isMovie"]) ? (int)$seedEntity["isMovie"] : null;

            $entityIds = self::getRandomSimilarEntityIdsForSeed(
                $con,
                $username,
                $seedEntityId,
                $limit,
                $movies,
                $tvShows,
                $seedIsMovie
            );

            if(empty($entityIds)) {
                continue;
            }

            return [
                "title" => t("category.because_you_watched", ["title" => $seedName]),
                "seedTitle" => $seedName,
                "seedEntityId" => $seedEntityId,
                "entities" => self::getEntitiesByIds($con, $entityIds, true, true)
            ];
        }

        return null;
    }

    public static function getSimilarEntities($con, $entityId, $limit = 10, $isMovie = null) {

        RecommendationProvider::ensureSchema($con);

        $entityId = (int)$entityId;
        $limit = (int)$limit;

        if($entityId <= 0 || $limit <= 0) {
            return [];
        }

        $typeSql = "";
        if($isMovie !== null) {
            $typeSql = "AND EXISTS (
                            SELECT 1
                            FROM videos typeVideos
                            WHERE typeVideos.entityId = e.id
                            AND typeVideos.isMovie = :isMovie
                        )";
        }

        $query = $con->prepare("
            SELECT e.id
            FROM entities e
            LEFT JOIN entityRatings r ON r.entityId = e.id
            WHERE e.categoryId = (
                SELECT categoryId
                FROM entities
                WHERE id = :sourceEntityId
            )
            AND e.id <> :currentEntityId
            $typeSql
            GROUP BY e.id
            ORDER BY COALESCE(AVG(r.rating), 0) DESC, e.id DESC
            LIMIT :limit
        ");

        $query->bindValue(":sourceEntityId", $entityId, PDO::PARAM_INT);
        $query->bindValue(":currentEntityId", $entityId, PDO::PARAM_INT);
        if($isMovie !== null) {
            $query->bindValue(":isMovie", $isMovie ? 1 : 0, PDO::PARAM_INT);
        }
        $query->bindValue(":limit", $limit, PDO::PARAM_INT);
        $query->execute();

        $entityIds = array_map("intval", $query->fetchAll(PDO::FETCH_COLUMN));
        return self::getEntitiesByIds($con, $entityIds, true, true);
    }

    public static function getEntitiesByIds($con, $entityIds, $movies = true, $tvShows = true) {

        $entityIds = array_values(array_unique(array_filter(array_map("intval", $entityIds), function($id) {
            return $id > 0;
        })));

        if(empty($entityIds)) {
            return [];
        }

        $idList = implode(",", $entityIds);
        $typeSql = "";

        if($movies xor $tvShows) {
            $typeSql = "AND EXISTS (
                            SELECT 1
                            FROM videos filterVideos
                            WHERE filterVideos.entityId = e.id
                            AND filterVideos.isMovie = " . ($movies ? "1" : "0") . "
                        )";
        }

        $query = $con->prepare("
            SELECT e.*
            FROM entities e
            WHERE e.id IN ($idList)
            $typeSql
            ORDER BY FIELD(e.id, $idList)
        ");
        $query->execute();

        $result = [];
        while($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $result[] = new Entity($con, $row);
        }

        return $result;
    }

    private static function fetchRecommendedEntityIdsFromApi($con, $username) {

        $userId = self::getUserIdByUsername($con, $username);
        if($userId <= 0 || !function_exists("curl_init")) {
            return [];
        }

        $apiBaseUrl = defined("RECOMMENDATION_API_BASE_URL")
            ? RECOMMENDATION_API_BASE_URL
            : "http://127.0.0.1:8000";

        $ch = curl_init($apiBaseUrl . "/recommend/" . rawurlencode((string)$userId));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);
        curl_setopt($ch, CURLOPT_HTTPGET, true);

        $response = curl_exec($ch);
        $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $hasError = curl_errno($ch);
        curl_close($ch);

        if($hasError || $httpCode >= 400 || !$response) {
            return [];
        }

        $decoded = json_decode($response, true);

        if(isset($decoded["entityIds"]) && is_array($decoded["entityIds"])) {
            return array_map("intval", $decoded["entityIds"]);
        }

        if(is_array($decoded)) {
            return array_map("intval", $decoded);
        }

        return [];
    }

    private static function getFallbackRecommendedEntities($con, $username, $limit, $movies, $tvShows) {

        RecommendationProvider::ensureSchema($con);

        $lastWatchedCategory = self::getLastWatchedCategoryData($con, $username);
        $entityIds = [];

        if($lastWatchedCategory) {
            $entityIds = self::getUnseenTopRatedEntityIds(
                $con,
                $username,
                (int)$lastWatchedCategory["categoryId"],
                $limit,
                $movies,
                $tvShows
            );
        }

        if(empty($entityIds)) {
            $entityIds = self::getUnseenTopRatedEntityIds(
                $con,
                $username,
                null,
                $limit,
                $movies,
                $tvShows
            );
        }

        return self::getEntitiesByIds($con, $entityIds, $movies, $tvShows);
    }

    private static function getRecentFiveStarSeedData($con, $username, $limit, $movies, $tvShows) {

        $typeSql = "";
        if($movies xor $tvShows) {
            $typeSql = "AND EXISTS (
                            SELECT 1
                            FROM videos typeVideos
                            WHERE typeVideos.entityId = e.id
                            AND typeVideos.isMovie = " . ($movies ? "1" : "0") . "
                        )";
        }

        $query = $con->prepare("
            SELECT
                e.id,
                e.name,
                (
                    SELECT MAX(seedVideos.isMovie)
                    FROM videos seedVideos
                    WHERE seedVideos.entityId = e.id
                ) AS isMovie
            FROM entityRatings r
            INNER JOIN entities e ON e.id = r.entityId
            WHERE r.username = :username
            AND r.rating = 5
            $typeSql
            ORDER BY r.updatedAt DESC, r.id DESC
            LIMIT :limit
        ");
        $query->bindValue(":username", $username);
        $query->bindValue(":limit", (int)$limit, PDO::PARAM_INT);
        $query->execute();

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    private static function getLastWatchedCategoryData($con, $username) {

        $query = $con->prepare("
            SELECT e.categoryId, v.isMovie
            FROM videoprogress vp
            INNER JOIN videos v ON v.id = vp.videoId
            INNER JOIN entities e ON e.id = v.entityId
            WHERE vp.username = :username
            AND vp.finished = 1
            ORDER BY vp.dateModified DESC
            LIMIT 1
        ");
        $query->bindValue(":username", $username);
        $query->execute();

        return $query->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    private static function getUserIdByUsername($con, $username) {

        $query = $con->prepare("SELECT id FROM users WHERE username = :username LIMIT 1");
        $query->bindValue(":username", $username);
        $query->execute();

        $userId = $query->fetchColumn();
        return $userId === false ? 0 : (int)$userId;
    }

    private static function getRandomSimilarEntityIdsForSeed($con, $username, $seedEntityId, $limit, $movies, $tvShows, $seedIsMovie = null) {

        $entityType = null;
        if($movies xor $tvShows) {
            $entityType = $movies ? 1 : 0;
        }
        elseif($seedIsMovie !== null) {
            $entityType = (int)$seedIsMovie;
        }

        $typeSql = "";
        if($entityType !== null) {
            $typeSql = "AND EXISTS (
                            SELECT 1
                            FROM videos typeVideos
                            WHERE typeVideos.entityId = e.id
                            AND typeVideos.isMovie = :entityType
                        )";
        }

        $query = $con->prepare("
            SELECT DISTINCT e.id
            FROM entities e
            INNER JOIN entityCategories ec ON ec.entityId = e.id
            WHERE e.id <> :seedEntityId
            AND ec.categoryId IN (
                SELECT seedCategories.categoryId
                FROM entityCategories seedCategories
                WHERE seedCategories.entityId = :seedCategoryEntityId
            )
            AND e.id NOT IN (
                SELECT rated.entityId
                FROM entityRatings rated
                WHERE rated.username = :ratedUsername
            )
            AND e.id NOT IN (
                SELECT wishlist.entityId
                FROM wishlist wishlist
                WHERE wishlist.username = :wishlistUsername
            )
            AND e.id NOT IN (
                SELECT DISTINCT watchedVideos.entityId
                FROM videoprogress watchedProgress
                INNER JOIN videos watchedVideos ON watchedVideos.id = watchedProgress.videoId
                WHERE watchedProgress.username = :watchedUsername
                AND watchedProgress.finished = 1
            )
            $typeSql
            ORDER BY RAND()
            LIMIT :limit
        ");

        $query->bindValue(":seedEntityId", (int)$seedEntityId, PDO::PARAM_INT);
        $query->bindValue(":seedCategoryEntityId", (int)$seedEntityId, PDO::PARAM_INT);
        $query->bindValue(":ratedUsername", $username);
        $query->bindValue(":wishlistUsername", $username);
        $query->bindValue(":watchedUsername", $username);
        if($entityType !== null) {
            $query->bindValue(":entityType", (int)$entityType, PDO::PARAM_INT);
        }
        $query->bindValue(":limit", (int)$limit, PDO::PARAM_INT);
        $query->execute();

        return array_map("intval", $query->fetchAll(PDO::FETCH_COLUMN));
    }

    private static function getUnseenTopRatedEntityIds($con, $username, $categoryId, $limit, $movies, $tvShows) {

        $typeSql = "";
        if($movies xor $tvShows) {
            $typeSql = "AND EXISTS (
                            SELECT 1
                            FROM videos typeVideos
                            WHERE typeVideos.entityId = e.id
                            AND typeVideos.isMovie = " . ($movies ? "1" : "0") . "
                        )";
        }

        $categorySql = "";
        if($categoryId !== null) {
            $categorySql = "AND e.categoryId = :categoryId";
        }

        $query = $con->prepare("
            SELECT e.id
            FROM entities e
            LEFT JOIN entityRatings r ON r.entityId = e.id
            WHERE e.id NOT IN (
                SELECT DISTINCT watchedVideos.entityId
                FROM videos watchedVideos
                INNER JOIN videoprogress vp ON vp.videoId = watchedVideos.id
                WHERE vp.username = :watchedUsername
                AND vp.finished = 1
            )
            AND e.id NOT IN (
                SELECT w.entityId
                FROM wishlist w
                WHERE w.username = :wishlistUsername
            )
            $categorySql
            $typeSql
            GROUP BY e.id
            ORDER BY COALESCE(AVG(r.rating), 0) DESC, e.id DESC
            LIMIT :limit
        ");

        $query->bindValue(":watchedUsername", $username);
        $query->bindValue(":wishlistUsername", $username);
        if($categoryId !== null) {
            $query->bindValue(":categoryId", (int)$categoryId, PDO::PARAM_INT);
        }
        $query->bindValue(":limit", (int)$limit, PDO::PARAM_INT);
        $query->execute();

        return array_map("intval", $query->fetchAll(PDO::FETCH_COLUMN));
    }

}

?>

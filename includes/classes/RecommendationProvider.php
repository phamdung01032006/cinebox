<?php

class RecommendationProvider {
    public static function ensureSchema($con) {
        static $isReady = false;

        if($isReady) {
            return;
        }

        $con->exec("
            CREATE TABLE IF NOT EXISTS entityRatings (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(255) NOT NULL,
                entityId INT NOT NULL,
                rating TINYINT NOT NULL,
                createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY unique_user_rating (username, entityId),
                KEY idx_rating_entity (entityId),
                KEY idx_rating_username (username)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        $con->exec("
            CREATE TABLE IF NOT EXISTS entityCategories (
                id INT AUTO_INCREMENT PRIMARY KEY,
                entityId INT NOT NULL,
                categoryId INT NOT NULL,
                createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY unique_entity_category (entityId, categoryId),
                KEY idx_entity_category_entity (entityId),
                KEY idx_entity_category_category (categoryId)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        $con->exec("
            INSERT IGNORE INTO entityCategories(entityId, categoryId)
            SELECT id, categoryId
            FROM entities
            WHERE categoryId IS NOT NULL
        ");

        $isReady = true;
    }

    public static function getRecommendedEntities($con, $username, $limit = 30, $movies = true, $tvShows = true) {
        self::ensureSchema($con);

        if(!$username) {
            return [];
        }

        $user = new User($con, $username);
        $wishlistIds = $user->getWishlistEntityIds();
        $ratedItems = $user->getRatedEntitiesWithScores();
        $gender = $user->getGender();

        $seedScores = [];
        foreach($wishlistIds as $entityId) {
            $seedScores[$entityId] = max($seedScores[$entityId] ?? 0, 3.5);
        }

        foreach($ratedItems as $item) {
            $seedScores[(int)$item["entityId"]] = max($seedScores[(int)$item["entityId"]] ?? 0, (float)$item["rating"]);
        }

        if(empty($seedScores)) {
            return [];
        }

        $tagWeights = [];
        $seedIds = array_keys($seedScores);
        $placeholders = implode(",", array_fill(0, count($seedIds), "?"));

        $tagQuery = $con->prepare("
            SELECT entityId, categoryId
            FROM entityCategories
            WHERE entityId IN ($placeholders)
        ");
        foreach($seedIds as $index => $entityId) {
            $tagQuery->bindValue($index + 1, (int)$entityId, PDO::PARAM_INT);
        }
        $tagQuery->execute();

        while($row = $tagQuery->fetch(PDO::FETCH_ASSOC)) {
            $entityId = (int)$row["entityId"];
            $categoryId = (int)$row["categoryId"];
            $tagWeights[$categoryId] = ($tagWeights[$categoryId] ?? 0) + ($seedScores[$entityId] ?? 0);
        }

        if(empty($tagWeights)) {
            return [];
        }

        $conditions = [];
        if($movies xor $tvShows) {
            $conditions[] = "EXISTS (
                SELECT 1 FROM videos v
                WHERE v.entityId = e.id AND v.isMovie = " . ($movies ? "1" : "0") . "
            )";
        }

        if(!empty($seedIds)) {
            $conditions[] = "e.id NOT IN (" . implode(",", array_fill(0, count($seedIds), "?")) . ")";
        }

        $whereSql = empty($conditions) ? "" : "WHERE " . implode(" AND ", $conditions);
        $candidateQuery = $con->prepare("
            SELECT DISTINCT e.*
            FROM entities e
            $whereSql
        ");

        foreach($seedIds as $index => $entityId) {
            $candidateQuery->bindValue($index + 1, (int)$entityId, PDO::PARAM_INT);
        }
        $candidateQuery->execute();

        $scored = [];
        while($row = $candidateQuery->fetch(PDO::FETCH_ASSOC)) {
            $entityId = (int)$row["id"];
            $score = self::scoreEntity($con, $entityId, $tagWeights, $gender);
            if($score <= 0) {
                continue;
            }
            $scored[] = [
                "score" => $score,
                "entity" => new Entity($con, $row)
            ];
        }

        usort($scored, function($a, $b) {
            return $b["score"] <=> $a["score"];
        });

        return array_map(function($item) {
            return $item["entity"];
        }, array_slice($scored, 0, $limit));
    }

    private static function scoreEntity($con, $entityId, $tagWeights, $gender) {
        $query = $con->prepare("
            SELECT categoryId
            FROM entityCategories
            WHERE entityId = :entityId
        ");
        $query->bindValue(":entityId", $entityId, PDO::PARAM_INT);
        $query->execute();

        $score = 0.0;
        while($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $categoryId = (int)$row["categoryId"];
            $score += $tagWeights[$categoryId] ?? 0;
        }

        $avgQuery = $con->prepare("
            SELECT AVG(rating) AS avgRating
            FROM entityRatings
            WHERE entityId = :entityId
        ");
        $avgQuery->bindValue(":entityId", $entityId, PDO::PARAM_INT);
        $avgQuery->execute();
        $avgRating = (float)($avgQuery->fetch(PDO::FETCH_ASSOC)["avgRating"] ?? 0);
        $score += $avgRating * 1.4;

        if($gender) {
            $genderQuery = $con->prepare("
                SELECT AVG(r.rating) AS genderAvg
                FROM entityRatings r
                INNER JOIN users u ON u.username = r.username
                WHERE r.entityId = :entityId
                AND u.gender = :gender
            ");
            $genderQuery->bindValue(":entityId", $entityId, PDO::PARAM_INT);
            $genderQuery->bindValue(":gender", $gender);
            $genderQuery->execute();
            $genderAvg = (float)($genderQuery->fetch(PDO::FETCH_ASSOC)["genderAvg"] ?? 0);
            $score += $genderAvg * 0.8;
        }

        return $score;
    }
}

?>

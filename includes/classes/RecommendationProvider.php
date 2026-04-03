<?php

class RecommendationProvider {
    public static function ensureSchema($con) {
        static $isReady = false;

        if($isReady) {
            return;
        }

        self::ensureUtf8ContentTables($con);

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
            CREATE TABLE IF NOT EXISTS tags (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(120) NOT NULL,
                slug VARCHAR(140) NOT NULL,
                tagType VARCHAR(50) NOT NULL,
                searchTerms TEXT DEFAULT NULL,
                createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY unique_tag_slug (slug),
                KEY idx_tags_type (tagType)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        $con->exec("
            CREATE TABLE IF NOT EXISTS entityTags (
                id INT AUTO_INCREMENT PRIMARY KEY,
                entityId INT NOT NULL,
                tagId INT NOT NULL,
                createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY unique_entity_tag (entityId, tagId),
                KEY idx_entity_tags_entity (entityId),
                KEY idx_entity_tags_tag (tagId)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        $con->exec("
            INSERT IGNORE INTO entityCategories(entityId, categoryId)
            SELECT id, categoryId
            FROM entities
            WHERE categoryId IS NOT NULL
        ");

        self::seedTags($con);
        self::syncEntityTags($con);

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
            SELECT entityId, tagId
            FROM entityTags
            WHERE entityId IN ($placeholders)
        ");
        foreach($seedIds as $index => $entityId) {
            $tagQuery->bindValue($index + 1, (int)$entityId, PDO::PARAM_INT);
        }
        $tagQuery->execute();

        while($row = $tagQuery->fetch(PDO::FETCH_ASSOC)) {
            $entityId = (int)$row["entityId"];
            $tagId = (int)$row["tagId"];
            $tagWeights[$tagId] = ($tagWeights[$tagId] ?? 0) + ($seedScores[$entityId] ?? 0);
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
            SELECT tagId
            FROM entityTags
            WHERE entityId = :entityId
        ");
        $query->bindValue(":entityId", $entityId, PDO::PARAM_INT);
        $query->execute();

        $score = 0.0;
        while($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $tagId = (int)$row["tagId"];
            $score += $tagWeights[$tagId] ?? 0;
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

    private static function ensureUtf8ContentTables($con) {
        $tables = ["categories", "entities", "videos"];

        foreach($tables as $table) {
            $query = $con->prepare("
                SELECT TABLE_COLLATION
                FROM information_schema.TABLES
                WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME = :tableName
                LIMIT 1
            ");
            $query->bindValue(":tableName", $table);
            $query->execute();

            $collation = (string)($query->fetchColumn() ?: "");
            if(stripos($collation, "utf8mb4_") === 0) {
                continue;
            }

            $con->exec("ALTER TABLE `$table` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
        }
    }

    private static function seedTags($con) {
        $tags = [
            ["Action & Adventure", "action-adventure", "genre", "action, adventure, hanh dong, phieu luu, truy duoi"],
            ["Comedy", "comedy", "genre", "comedy, funny, hai, hai huoc"],
            ["Drama", "drama", "genre", "drama, chinh kich, tinh cam"],
            ["Horror", "horror", "genre", "horror, kinh di, supernatural, sieu nhien"],
            ["Romance", "romance", "genre", "romance, lang man, ngon tinh, tinh cam"],
            ["Sci-Fi & Fantasy", "sci-fi-fantasy", "genre", "science fiction, fantasy, vien tuong, ky ao, phep thuat"],
            ["Thriller", "thriller", "genre", "thriller, giat gan, cang thang, tam ly toi pham"],
            ["Documentary", "documentary", "genre", "documentary, tai lieu, based on true story, su kien co that"],
            ["Sports", "sports", "genre", "sports, the thao, thi dau"],
            ["Anime", "anime", "genre", "anime, hoat hinh nhat ban"],
            ["Cartoon", "cartoon", "genre", "cartoon, hoat hinh"],
            ["Family", "family", "audience", "family, gia dinh, kids, tre em"],
            ["Teens", "teens", "audience", "teens, teen, thanh thieu nien, tuoi moi lon"],
            ["Adults", "adults", "audience", "adults, nguoi lon, 18+"],
            ["Funny", "funny", "mood", "funny, vui nhon, hai huoc, giai tri"],
            ["Lighthearted", "lighthearted", "mood", "lighthearted, nhe nhang, de thuong"],
            ["Heartwarming", "heartwarming", "mood", "heartwarming, healing, chua lanh, am ap"],
            ["Emotional", "emotional", "mood", "emotional, dau long, cam dong, tearjerker"],
            ["Suspenseful", "suspenseful", "mood", "suspenseful, cang thang, gay can"],
            ["Dark", "dark", "mood", "dark, tam toi, am anh"],
            ["Imaginative", "imaginative", "mood", "imaginative, hack nao, mind-bending, the gioi song song"],
            ["Nostalgic", "nostalgic", "mood", "nostalgic, hoai co, co dien"],
            ["Inspiring", "inspiring", "mood", "inspiring, truyen cam hung, vuon len"],
            ["Friendship", "friendship", "content", "friendship, tinh ban, dong doi"],
            ["Coming of Age", "coming-of-age", "content", "coming of age, thanh xuan, truong hoc, school life"],
            ["Adventure", "adventure", "content", "adventure, phieu luu, hanh trinh"],
            ["Crime", "crime", "content", "crime, toi pham, dieu tra"],
            ["True Story", "true-story", "content", "true story, chuyen co that, su kien co that"],
            ["Holiday", "holiday", "content", "holiday, christmas, giang sinh, le hoi"],
            ["Musical", "musical", "content", "musical, am nhac, ca nhac"],
            ["Movie", "movie-format", "metadata", "movie, phim le, feature film"],
            ["Series", "series-format", "metadata", "series, phim bo, tv show"],
            ["Trending", "trending", "metadata", "trending, xu huong, hot"],
            ["Most Viewed", "most-viewed", "metadata", "most viewed, xem nhieu, pho bien"],
            ["New Release", "new-release", "metadata", "new, moi phat hanh, moi nhat"],
            ["Classic", "classic-tag", "metadata", "classic, co dien, bat hu"],
            ["Independent", "independent", "metadata", "independent, indie, doc lap"],
            ["Foreign", "foreign", "metadata", "foreign, quoc te, nuoc ngoai"]
        ];

        $insert = $con->prepare("
            INSERT INTO tags(name, slug, tagType, searchTerms)
            VALUES(:name, :slug, :tagType, :searchTerms)
            ON DUPLICATE KEY UPDATE
                name = VALUES(name),
                tagType = VALUES(tagType),
                searchTerms = VALUES(searchTerms)
        ");

        foreach($tags as $tag) {
            $insert->bindValue(":name", $tag[0]);
            $insert->bindValue(":slug", $tag[1]);
            $insert->bindValue(":tagType", $tag[2]);
            $insert->bindValue(":searchTerms", $tag[3]);
            $insert->execute();
        }
    }

    private static function syncEntityTags($con) {
        $tagIdMap = [];
        foreach($con->query("SELECT id, slug FROM tags") as $row) {
            $tagIdMap[$row["slug"]] = (int)$row["id"];
        }

        if(empty($tagIdMap)) {
            return;
        }

        $topViewedIds = self::getTopEntityIdsByMetric($con, "views", 10);
        $newestIds = self::getTopEntityIdsByMetric($con, "newest", 10);
        $topViewedLookup = array_fill_keys($topViewedIds, true);
        $newestLookup = array_fill_keys($newestIds, true);

        $sql = "
            SELECT
                e.id,
                c.name AS categoryName,
                COALESCE(MAX(v.isMovie), 0) AS isMovie,
                MAX(v.releaseDate) AS latestReleaseDate,
                COALESCE(SUM(v.views), 0) AS totalViews
            FROM entities e
            LEFT JOIN categories c ON c.id = e.categoryId
            LEFT JOIN videos v ON v.entityId = e.id
            GROUP BY e.id, c.name
        ";

        $insert = $con->prepare("
            INSERT IGNORE INTO entityTags(entityId, tagId)
            VALUES(:entityId, :tagId)
        ");

        foreach($con->query($sql) as $row) {
            $entityId = (int)$row["id"];
            $categoryName = (string)($row["categoryName"] ?? "");
            $latestReleaseDate = (string)($row["latestReleaseDate"] ?? "");
            $totalViews = (int)($row["totalViews"] ?? 0);

            $slugs = self::getDefaultTagSlugsForEntity(
                $categoryName,
                (int)($row["isMovie"] ?? 0) === 1,
                $latestReleaseDate,
                $totalViews,
                isset($topViewedLookup[$entityId]),
                isset($newestLookup[$entityId])
            );

            foreach($slugs as $slug) {
                if(!isset($tagIdMap[$slug])) {
                    continue;
                }

                $insert->bindValue(":entityId", $entityId, PDO::PARAM_INT);
                $insert->bindValue(":tagId", $tagIdMap[$slug], PDO::PARAM_INT);
                $insert->execute();
            }
        }
    }

    private static function getDefaultTagSlugsForEntity($categoryName, $isMovie, $latestReleaseDate, $totalViews, $isTopViewed, $isNewest) {
        $slugs = [$isMovie ? "movie-format" : "series-format"];

        $categoryTagMap = [
            "Action & adventure" => ["action-adventure", "adventure", "suspenseful", "teens"],
            "Classic" => ["classic-tag", "nostalgic", "drama", "adults"],
            "Comedies" => ["comedy", "funny", "lighthearted", "friendship"],
            "Dramas" => ["drama", "emotional", "adults", "heartwarming"],
            "Horror" => ["horror", "dark", "suspenseful", "adults"],
            "Romantic" => ["romance", "emotional", "heartwarming", "adults"],
            "Sci - Fi & Fantasy" => ["sci-fi-fantasy", "imaginative", "adventure", "teens"],
            "Sports" => ["sports", "inspiring", "teens", "friendship"],
            "Thrillers" => ["thriller", "crime", "suspenseful", "dark"],
            "Documentaries" => ["documentary", "true-story", "adults", "inspiring"],
            "Teen" => ["teens", "coming-of-age", "friendship", "lighthearted"],
            "Children & family" => ["family", "heartwarming", "lighthearted", "kids"],
            "Anime" => ["anime", "imaginative", "friendship", "teens"],
            "Independent" => ["independent", "drama", "emotional", "adults"],
            "Foreign" => ["foreign", "drama", "emotional", "adults"],
            "Music" => ["musical", "heartwarming", "nostalgic", "lighthearted"],
            "Christmas" => ["holiday", "family", "heartwarming", "lighthearted"],
            "Others" => ["drama", "adults", "friendship"],
            "Cartoon" => ["cartoon", "kids", "lighthearted", "friendship"]
        ];

        if(isset($categoryTagMap[$categoryName])) {
            $slugs = array_merge($slugs, $categoryTagMap[$categoryName]);
        }

        if($isTopViewed || $totalViews > 0) {
            $slugs[] = "most-viewed";
        }

        if($isTopViewed) {
            $slugs[] = "trending";
        }

        if($isNewest) {
            $slugs[] = "new-release";
        }

        $releaseYear = (int)substr((string)$latestReleaseDate, 0, 4);
        if($releaseYear > 0 && $releaseYear <= 2005) {
            $slugs[] = "nostalgic";
        }
        elseif($releaseYear >= 2015) {
            $slugs[] = "new-release";
        }

        if(!$isMovie) {
            $slugs[] = "friendship";
        }

        return array_values(array_unique($slugs));
    }

    private static function getTopEntityIdsByMetric($con, $metric, $limit) {
        $orderSql = $metric === "newest"
            ? "MAX(v.releaseDate) DESC, MAX(v.uploadDate) DESC, e.id DESC"
            : "SUM(v.views) DESC, MAX(v.releaseDate) DESC, e.id DESC";

        $query = $con->prepare("
            SELECT e.id
            FROM entities e
            INNER JOIN videos v ON v.entityId = e.id
            GROUP BY e.id
            ORDER BY $orderSql
            LIMIT :limit
        ");
        $query->bindValue(":limit", (int)$limit, PDO::PARAM_INT);
        $query->execute();

        return array_map("intval", $query->fetchAll(PDO::FETCH_COLUMN));
    }
}

?>

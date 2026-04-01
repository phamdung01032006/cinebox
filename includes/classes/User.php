<?php
require_once(__DIR__ . "/RecommendationProvider.php");

class User {
    private $con, $sqlData;

    public function __construct($con, $username) {
        $this->con = $con;
        $this->ensureUserSchema();
        RecommendationProvider::ensureSchema($this->con);

        $query = $con->prepare("SELECT * FROM users WHERE username=:username");
        $query->bindValue(":username",$username);
        $query->execute();

        $this->sqlData = $query->fetch(PDO::FETCH_ASSOC);
        $this->syncLegacyWishlist();
    }

    public function getFirstName() {
        return $this->sqlData["firstName"] ?? "";
    }

    public function getLastName() {
        return $this->sqlData["lastName"] ?? "";
    }

    public function getEmail() {
        return $this->sqlData["email"] ?? "";
    }

    public function getIsSubscribed() {
        return $this->sqlData["isSubscribed"] ?? "";
    }

    public function getGender() {
        return $this->sqlData["gender"] ?? "prefer_not_to_say";
    }

    public function hasEntityInWishlist($entityId) {
        $this->ensureWishlistTable();

        $query = $this->con->prepare("
            SELECT 1
            FROM wishlist
            WHERE username = :username AND entityId = :entityId
            LIMIT 1
        ");
        $query->bindValue(":username", $this->sqlData["username"]);
        $query->bindValue(":entityId", (int)$entityId, PDO::PARAM_INT);
        $query->execute();

        return $query->fetchColumn() !== false;
    }

    public function addToWishlist($entityId) {
        $this->ensureWishlistTable();

        $entityId = (int)$entityId;
        if($entityId <= 0) {
            return false;
        }

        if($this->hasEntityInWishlist($entityId)) {
            $this->updateLatestWishlistId($entityId);
            return "exists";
        }

        $query = $this->con->prepare("
            INSERT INTO wishlist(username, entityId)
            VALUES(:username, :entityId)
        ");
        $query->bindValue(":username", $this->sqlData["username"]);
        $query->bindValue(":entityId", $entityId, PDO::PARAM_INT);
        if(!$query->execute()) {
            return false;
        }

        $this->updateLatestWishlistId($entityId);
        return "added";
    }

    public function getWishlistEntities() {
        $this->ensureWishlistTable();

        $query = $this->con->prepare("
            SELECT e.*
            FROM wishlist w
            INNER JOIN entities e ON e.id = w.entityId
            WHERE w.username = :username
            ORDER BY w.createdAt DESC, w.id DESC
        ");
        $query->bindValue(":username", $this->sqlData["username"]);
        $query->execute();

        $entities = [];
        while($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $entities[] = new Entity($this->con, $row);
        }

        return $entities;
    }

    public function getWishlistEntityIds() {
        $this->ensureWishlistTable();

        $query = $this->con->prepare("
            SELECT entityId
            FROM wishlist
            WHERE username = :username
        ");
        $query->bindValue(":username", $this->sqlData["username"]);
        $query->execute();

        return array_map("intval", $query->fetchAll(PDO::FETCH_COLUMN));
    }

    public function removeFromWishlist($entityId) {
        $this->ensureWishlistTable();

        $query = $this->con->prepare("
            DELETE FROM wishlist
            WHERE username = :username AND entityId = :entityId
        ");
        $query->bindValue(":username", $this->sqlData["username"]);
        $query->bindValue(":entityId", (int)$entityId, PDO::PARAM_INT);
        $query->execute();

        $removed = $query->rowCount() > 0;
        if($removed) {
            $this->refreshLatestWishlistId();
        }

        return $removed;
    }

    public function rateEntity($entityId, $rating) {
        RecommendationProvider::ensureSchema($this->con);

        $entityId = (int)$entityId;
        $rating = (int)$rating;

        if($entityId <= 0 || $rating < 1 || $rating > 5) {
            return false;
        }

        $query = $this->con->prepare("
            INSERT INTO entityRatings(username, entityId, rating)
            VALUES(:username, :entityId, :rating)
            ON DUPLICATE KEY UPDATE rating = VALUES(rating)
        ");
        $query->bindValue(":username", $this->sqlData["username"]);
        $query->bindValue(":entityId", $entityId, PDO::PARAM_INT);
        $query->bindValue(":rating", $rating, PDO::PARAM_INT);

        return $query->execute();
    }

    public function getEntityRating($entityId) {
        RecommendationProvider::ensureSchema($this->con);

        $query = $this->con->prepare("
            SELECT rating
            FROM entityRatings
            WHERE username = :username AND entityId = :entityId
            LIMIT 1
        ");
        $query->bindValue(":username", $this->sqlData["username"]);
        $query->bindValue(":entityId", (int)$entityId, PDO::PARAM_INT);
        $query->execute();

        $rating = $query->fetchColumn();
        return $rating === false ? 0 : (int)$rating;
    }

    public function getRatedEntitiesWithScores() {
        RecommendationProvider::ensureSchema($this->con);

        $query = $this->con->prepare("
            SELECT entityId, rating
            FROM entityRatings
            WHERE username = :username
        ");
        $query->bindValue(":username", $this->sqlData["username"]);
        $query->execute();

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    private function updateLatestWishlistId($entityId) {
        $query = $this->con->prepare("
            UPDATE users
            SET wishList = :entityId
            WHERE username = :username
        ");
        $query->bindValue(":entityId", (int)$entityId, PDO::PARAM_INT);
        $query->bindValue(":username", $this->sqlData["username"]);
        $query->execute();

        $this->sqlData["wishList"] = (int)$entityId;
    }

    private function syncLegacyWishlist() {
        $this->ensureWishlistTable();

        if(empty($this->sqlData["username"])) {
            return;
        }

        $legacyEntityId = isset($this->sqlData["wishList"]) ? (int)$this->sqlData["wishList"] : 0;
        if($legacyEntityId <= 0) {
            return;
        }

        $query = $this->con->prepare("
            INSERT IGNORE INTO wishlist(username, entityId)
            VALUES(:username, :entityId)
        ");
        $query->bindValue(":username", $this->sqlData["username"]);
        $query->bindValue(":entityId", $legacyEntityId, PDO::PARAM_INT);
        $query->execute();
    }

    private function refreshLatestWishlistId() {
        $query = $this->con->prepare("
            SELECT entityId
            FROM wishlist
            WHERE username = :username
            ORDER BY createdAt DESC, id DESC
            LIMIT 1
        ");
        $query->bindValue(":username", $this->sqlData["username"]);
        $query->execute();

        $latestEntityId = $query->fetchColumn();

        $updateQuery = $this->con->prepare("
            UPDATE users
            SET wishList = :entityId
            WHERE username = :username
        ");
        if($latestEntityId === false) {
            $updateQuery->bindValue(":entityId", null, PDO::PARAM_NULL);
            $this->sqlData["wishList"] = null;
        }
        else {
            $updateQuery->bindValue(":entityId", (int)$latestEntityId, PDO::PARAM_INT);
            $this->sqlData["wishList"] = (int)$latestEntityId;
        }
        $updateQuery->bindValue(":username", $this->sqlData["username"]);
        $updateQuery->execute();
    }

    private function ensureWishlistTable() {
        static $isReady = false;

        if($isReady) {
            return;
        }

        $this->con->exec("
            CREATE TABLE IF NOT EXISTS wishlist (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(255) NOT NULL,
                entityId INT NOT NULL,
                createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY unique_user_entity (username, entityId),
                KEY idx_wishlist_username (username),
                KEY idx_wishlist_entity (entityId)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        $isReady = true;
    }

    private function ensureUserSchema() {
        static $schemaReady = false;

        if($schemaReady) {
            return;
        }

        $query = $this->con->query("SHOW COLUMNS FROM users LIKE 'gender'");
        if(!$query->fetch(PDO::FETCH_ASSOC)) {
            $this->con->exec("ALTER TABLE users ADD COLUMN gender VARCHAR(32) NOT NULL DEFAULT 'prefer_not_to_say'");
        }

        $schemaReady = true;
    }
}
?>

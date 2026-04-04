<?php
require_once(__DIR__ . "/RecommendationProvider.php");

class User {
    private const FREE_EPISODE_LIMIT = 3;
    private const PROFILE_IMAGE_DIRECTORY = "assets/images/profiles";
    private const MAX_PROFILE_IMAGE_SIZE = 5242880;

    private $con, $sqlData;
    private $freeEpisodeIdsByEntity = [];

    public function __construct($con, $username) {
        $this->con = $con;
        $this->ensureUserSchema();

        $query = $con->prepare("SELECT * FROM users WHERE username=:username");
        $query->bindValue(":username",$username);
        $query->execute();

        $this->sqlData = $query->fetch(PDO::FETCH_ASSOC);
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

    public function getAvatarPath() {
        return $this->getStoredProfileImagePath("avatarPath");
    }

    public function getCoverPath() {
        return $this->getStoredProfileImagePath("coverPath");
    }

    public function getIsSubscribed() {
        return $this->sqlData["isSubscribed"] ?? "";
    }

    public function isSubscribed() {
        return !empty($this->sqlData["isSubscribed"]);
    }

    public function activateTrialMembership() {
        if(empty($this->sqlData["username"])) {
            return false;
        }

        $query = $this->con->prepare("
            UPDATE users
            SET isSubscribed = 1
            WHERE username = :username
        ");
        $query->bindValue(":username", $this->sqlData["username"]);
        $executed = $query->execute();

        if($executed) {
            $this->sqlData["isSubscribed"] = 1;
        }

        return $executed;
    }

    public function updateProfileImages($avatarFile, $coverFile) {
        $hasAvatarUpload = $this->hasUploadedFile($avatarFile);
        $hasCoverUpload = $this->hasUploadedFile($coverFile);

        if(!$hasAvatarUpload && !$hasCoverUpload) {
            return [
                "success" => false,
                "message" => t("profile.media_select_prompt")
            ];
        }

        try {
            if($hasAvatarUpload) {
                $this->storeUploadedProfileImage($avatarFile, "avatar");
            }

            if($hasCoverUpload) {
                $this->storeUploadedProfileImage($coverFile, "cover");
            }
        }
        catch(Exception $exception) {
            return [
                "success" => false,
                "message" => $exception->getMessage()
            ];
        }

        return [
            "success" => true,
            "message" => t("profile.media_saved")
        ];
    }

    public function cancelMembership() {
        if(empty($this->sqlData["username"])) {
            return false;
        }

        $query = $this->con->prepare("
            UPDATE users
            SET isSubscribed = 0
            WHERE username = :username
        ");
        $query->bindValue(":username", $this->sqlData["username"]);
        $executed = $query->execute();

        if($executed) {
            $this->sqlData["isSubscribed"] = 0;
        }

        return $executed;
    }

    public function getFreeEpisodeLimit() {
        return self::FREE_EPISODE_LIMIT;
    }

    public function canWatchVideo($video) {
        if(!$video instanceof Video) {
            $video = new Video($this->con, $video);
        }

        if($this->isSubscribed()) {
            return true;
        }

        if($video->isMovie()) {
            return false;
        }

        return $this->isVideoInFreeEpisodeWindow($video);
    }

    public function getVideoAccessMessage($video) {
        if(!$video instanceof Video) {
            $video = new Video($this->con, $video);
        }

        if($this->canWatchVideo($video)) {
            return "";
        }

        if($video->isMovie()) {
            return t("access.movie_subscription_required");
        }

        return t("access.tv_episode_limit", [
            "count" => $this->getFreeEpisodeLimit()
        ]);
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

        $query = $this->con->prepare("
            INSERT IGNORE INTO wishlist(username, entityId)
            VALUES(:username, :entityId)
        ");
        $query->bindValue(":username", $this->sqlData["username"]);
        $query->bindValue(":entityId", $entityId, PDO::PARAM_INT);
        if(!$query->execute()) {
            return false;
        }

        return $query->rowCount() > 0 ? "added" : "exists";
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

        return $query->rowCount() > 0;
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

    private function isVideoInFreeEpisodeWindow($video) {
        $entityId = (int)$video->getEntityId();

        if(!isset($this->freeEpisodeIdsByEntity[$entityId])) {
            $query = $this->con->prepare("
                SELECT id
                FROM videos
                WHERE entityId = :entityId
                AND isMovie = 0
                ORDER BY season ASC, episode ASC
                LIMIT " . self::FREE_EPISODE_LIMIT
            );
            $query->bindValue(":entityId", $entityId, PDO::PARAM_INT);
            $query->execute();

            $this->freeEpisodeIdsByEntity[$entityId] = array_map("intval", $query->fetchAll(PDO::FETCH_COLUMN));
        }

        return in_array((int)$video->getId(), $this->freeEpisodeIdsByEntity[$entityId], true);
    }

    private function hasUploadedFile($file) {
        return is_array($file)
            && isset($file["error"])
            && (int)$file["error"] !== UPLOAD_ERR_NO_FILE;
    }

    private function storeUploadedProfileImage($file, $type) {
        if(!is_array($file) || !isset($file["error"])) {
            throw new Exception(t("profile.image_upload_error"));
        }

        if((int)$file["error"] !== UPLOAD_ERR_OK) {
            throw new Exception(t("profile.image_upload_error"));
        }

        if(empty($file["tmp_name"]) || !is_uploaded_file($file["tmp_name"])) {
            throw new Exception(t("profile.image_upload_error"));
        }

        if((int)$file["size"] <= 0 || (int)$file["size"] > self::MAX_PROFILE_IMAGE_SIZE) {
            throw new Exception(t("profile.image_size_error"));
        }

        $imageInfo = @getimagesize($file["tmp_name"]);
        $mimeType = $imageInfo["mime"] ?? "";
        $allowedTypes = [
            "image/jpeg" => "jpg",
            "image/png" => "png",
            "image/webp" => "webp",
            "image/gif" => "gif"
        ];

        if(!isset($allowedTypes[$mimeType])) {
            throw new Exception(t("profile.image_type_error"));
        }

        $column = $type === "cover" ? "coverPath" : "avatarPath";
        $extension = $allowedTypes[$mimeType];
        $relativeDirectory = $this->getProfileImageRelativeDirectory();
        $absoluteDirectory = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . str_replace("/", DIRECTORY_SEPARATOR, $relativeDirectory);

        if(!is_dir($absoluteDirectory) && !mkdir($absoluteDirectory, 0777, true) && !is_dir($absoluteDirectory)) {
            throw new Exception(t("profile.image_upload_error"));
        }

        $fileName = $type . "_" . time() . "_" . bin2hex(random_bytes(4)) . "." . $extension;
        $relativePath = $relativeDirectory . "/" . $fileName;
        $absolutePath = $absoluteDirectory . DIRECTORY_SEPARATOR . $fileName;

        if(!move_uploaded_file($file["tmp_name"], $absolutePath)) {
            throw new Exception(t("profile.image_upload_error"));
        }

        $previousPath = $this->sqlData[$column] ?? "";
        $query = $this->con->prepare("
            UPDATE users
            SET $column = :path
            WHERE username = :username
        ");
        $query->bindValue(":path", $relativePath);
        $query->bindValue(":username", $this->sqlData["username"]);

        if(!$query->execute()) {
            @unlink($absolutePath);
            throw new Exception(t("profile.image_upload_error"));
        }

        $this->sqlData[$column] = $relativePath;
        $this->deleteProfileImageFile($previousPath);
    }

    private function getStoredProfileImagePath($column) {
        $storedPath = $this->sqlData[$column] ?? "";
        if(!$storedPath) {
            return "";
        }

        $absolutePath = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . str_replace("/", DIRECTORY_SEPARATOR, $storedPath);
        return file_exists($absolutePath) ? $storedPath : "";
    }

    private function getProfileImageRelativeDirectory() {
        $safeUsername = preg_replace("/[^A-Za-z0-9_-]/", "_", $this->sqlData["username"] ?? "user");
        return self::PROFILE_IMAGE_DIRECTORY . "/" . $safeUsername;
    }

    private function deleteProfileImageFile($relativePath) {
        if(!$relativePath) {
            return;
        }

        $normalizedPath = str_replace("\\", "/", $relativePath);
        $allowedPrefix = $this->getProfileImageRelativeDirectory() . "/";
        if(strpos($normalizedPath, $allowedPrefix) !== 0) {
            return;
        }

        $absolutePath = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . str_replace("/", DIRECTORY_SEPARATOR, $normalizedPath);
        if(file_exists($absolutePath)) {
            @unlink($absolutePath);
        }
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

        $this->ensureUserColumn("gender", "ALTER TABLE users ADD COLUMN gender VARCHAR(32) NOT NULL DEFAULT 'prefer_not_to_say'");
        $this->ensureUserColumn("avatarPath", "ALTER TABLE users ADD COLUMN avatarPath VARCHAR(255) DEFAULT NULL");
        $this->ensureUserColumn("coverPath", "ALTER TABLE users ADD COLUMN coverPath VARCHAR(255) DEFAULT NULL");

        $schemaReady = true;
    }

    private function ensureUserColumn($columnName, $alterSql) {
        $query = $this->con->query("SHOW COLUMNS FROM users LIKE " . $this->con->quote($columnName));
        if(!$query->fetch(PDO::FETCH_ASSOC)) {
            $this->con->exec($alterSql);
        }
    }
}
?>

<?php

require_once(__DIR__ . "/RecommendationProvider.php");

class Entity {

    private $con, $sqlData;
    public function __construct($con, $input) {
        
        $this->con = $con;
        
        if(is_array($input)) {
            $this->sqlData = $input;
        }

        else {
            $query = $this->con->prepare("SELECT * FROM entities WHERE id=:id");
            $query->bindValue(":id", $input);
            $query->execute();

            $this->sqlData = $query->fetch(PDO::FETCH_ASSOC);
        }

    }

    public function getId() {
        return $this->sqlData["id"];
    }
    public function getName() {
        return $this->sqlData["name"];
    }
    public function getThumbnail() {
        return $this->sqlData["thumbnail"];
    }
    public function getPreview() {
        return $this->sqlData["preview"];
    }
	    public function getCategoryId() {
	        return $this->sqlData["categoryId"];
	    }

    public function getPreviewTags($limit = 4) {
        RecommendationProvider::ensureSchema($this->con);

        $limit = max(1, (int)$limit);
        $query = $this->con->prepare("
            SELECT t.name
            FROM entityTags et
            INNER JOIN tags t ON t.id = et.tagId
            WHERE et.entityId = :entityId
            ORDER BY
                FIELD(t.tagType, 'genre', 'mood', 'content', 'audience', 'metadata'),
                t.name ASC
            LIMIT :limit
        ");
        $query->bindValue(":entityId", (int)$this->getId(), PDO::PARAM_INT);
        $query->bindValue(":limit", $limit, PDO::PARAM_INT);
        $query->execute();

        $tags = $query->fetchAll(PDO::FETCH_COLUMN);
        $previewItems = [];
        $releaseYear = $this->getReleaseYear();

        if($releaseYear !== "") {
            $previewItems[] = htmlspecialchars($releaseYear, ENT_QUOTES, "UTF-8");
        }

        foreach($tags as $tag) {
            $previewItems[] = htmlspecialchars((string)$tag, ENT_QUOTES, "UTF-8");
        }

        return implode(" • ", $previewItems);
    }

    public function getReleaseYear() {
        $query = $this->con->prepare("
            SELECT MAX(releaseDate) AS latestReleaseDate
            FROM videos
            WHERE entityId = :entityId
        ");
        $query->bindValue(":entityId", (int)$this->getId(), PDO::PARAM_INT);
        $query->execute();

        $releaseDate = (string)($query->fetchColumn() ?: "");
        $year = substr($releaseDate, 0, 4);

        return preg_match("/^\d{4}$/", $year) ? $year : "";
    }

	    public function getSeasons() {
        $query = $this->con->prepare("SELECT * FROM videos WHERE entityId=:id
                                        AND isMovie=0 ORDER BY season, episode ASC");

        $query->bindValue(":id", $this->getId());
        $query->execute();

        $seasons = array();
        $videos = array();
        $currentSeason = null;
        while($row = $query->fetch(PDO::FETCH_ASSOC)) {
            
            //  if these two aren't same we know that the season has changed
            if($currentSeason != null && $currentSeason != $row["season"]) {
                $seasons[] = new Season($currentSeason, $videos);
                $videos = array();
            }

            $currentSeason = $row["season"];
            $videos[] = new Video($this->con, $row);

    }
    
    // handle the last season after the loop
    if(sizeof($videos) != 0) {
        $seasons[] = new Season($currentSeason, $videos);
    }

    return $seasons;
}
}
?>

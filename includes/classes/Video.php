<?php
class Video {

    private $con, $sqlData, $entity;
    public function __construct($con, $input) {
        
        $this->con = $con;
        
        if(is_array($input)) {
            $this->sqlData = $input;
        }

        else {
            $query = $this->con->prepare("SELECT * FROM videos WHERE id=:id");
            $query->bindValue(":id", $input);
            $query->execute();

            $this->sqlData = $query->fetch(PDO::FETCH_ASSOC);
            if(!$this->sqlData) {
                ErrorMessage::show(t("error.video_not_found"));
                exit();
            }
            
        }

        $this->entity = new Entity($con, $this->sqlData["entityId"]);

    }

    public function getId() {
        return $this->sqlData["id"];
    }
    public function getTitle() {
        return $this->sqlData["title"];
    }
    public function getDescription() {
        return $this->sqlData["description"];
    }
    public function getFilePath() {
        return $this->sqlData["filePath"];
    }
    public function getDuration() {
        return $this->sqlData["duration"];
    }
    public function getThumbnail() {
        return $this->entity->getThumbnail();
    }
    public function getEpisodeNumber() {
        return $this->sqlData["episode"];
    }

        public function getSeasonNumber() {
        return $this->sqlData["season"];
    }
    public function getEntityId() {
        return $this->sqlData["entityId"];
    }

    public function incrementView() {
        $query = $this->con->prepare("UPDATE videos SET views=views+1 WHERE id=:id");
        $query->bindValue(":id", $this->getId());
        $query->execute();
    }

    public function getEntity() {
    return $this->entity;
    }

    public function getSeasonAndEpisode() {
        if($this->isMovie()) {
            return;
        }

        $season = $this->getSeasonNumber();
        $episode = $this->getEpisodeNumber();

        return t("video.season_episode", [
            "season" => $season,
            "episode" => $episode
        ]);
    }

    public function isMovie() {
        return $this->sqlData["isMovie"] == 1;
    }

    public function getDurationInSeconds() {
        $duration = trim((string)$this->getDuration());

        if($duration === "") {
            return 0;
        }

        $parts = array_map("intval", explode(":", $duration));
        if(count($parts) === 3) {
            return ($parts[0] * 3600) + ($parts[1] * 60) + $parts[2];
        }

        if(count($parts) === 2) {
            return ($parts[0] * 60) + $parts[1];
        }

        if(count($parts) === 1) {
            return $parts[0];
        }

        return 0;
    }

    public function isInProgress($username) {
        $query = $this->con->prepare("SELECT * FROM videoProgress
                                    WHERE videoId=:videoId AND username=:username");
        
        $query->bindValue(":videoId", $this->getId());
        $query->bindValue(":username", $username);
        $query->execute();

        return $query->rowCount() != 0;
    }

    public function hasSeen($username) {
        $query = $this->con->prepare("SELECT * FROM videoProgress
                                    WHERE videoId=:videoId AND username=:username
                                    AND finished=1");
        
        $query->bindValue(":videoId", $this->getId());
        $query->bindValue(":username", $username);
        $query->execute();

        return $query->rowCount() != 0;
    }

}
?>

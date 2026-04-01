<?php
class VideoProvider {
    public static function getUpNext($con, $currentVideo) {
        $query = $con->prepare("SELECT * FROM videos
                                WHERE entityId=:entityId AND id != :videoId
                                AND (
                                    (season = :season AND episode > :episode) OR season > :season
                                )
                                ORDER BY season, episode ASC LIMIT 1");

        $query->bindValue(":entityId", $currentVideo->getEntityId());
        $query->bindValue(":season", $currentVideo->getSeasonNumber());
        $query->bindValue(":episode", $currentVideo->getEpisodeNumber());
        $query->bindValue(":videoId", $currentVideo->getId());

        $query->execute();

        if($query->rowCount() == 0) {
            $query = $con->prepare("SELECT * FROM videos
                                    WHERE entityId=:entityId
                                    AND id != :videoId
                                    ORDER BY season, episode ASC LIMIT 1");
            $query->bindValue(":entityId", $currentVideo->getEntityId());
            $query->bindValue(":videoId", $currentVideo->getId());
            $query->execute();
        }

        $row = $query->fetch(PDO::FETCH_ASSOC);
        if(!$row) {
            // Entity chi co 1 tap/1 phim, khong co "up next"
            return $currentVideo;
        }

        return new Video($con, $row);
    }

    public static function getEntityVideoForUser($con, $entityId, $username) {
        $query = $con->prepare("SELECT videoId FROM `videoprogress` 
                                INNER JOIN videos
                                ON videoprogress.videoId = videos.id
                                WHERE videos.entityId = :entityId
                                AND videoprogress.username = :username
                                ORDER BY videoprogress.dateModified DESC
                                LIMIT 1;");

        $query->bindValue(":entityId", $entityId);
        $query->bindValue(":username", $username);
        $query->execute();

        if($query->rowCount() == 0) {
            $query = $con->prepare("SELECT id FROM videos 
                                    WHERE entityId=:entityId 
                                    ORDER BY season, episode ASC LIMIT 1");
            
            $query->bindValue(":entityId", $entityId);
            $query->execute();
        }

        return $query->fetchColumn();
    }

    public static function getPlayableEntityVideoForUser($con, $entityId, $username) {
        $defaultVideoId = self::getEntityVideoForUser($con, $entityId, $username);

        if(!$username || !$defaultVideoId) {
            return $defaultVideoId;
        }

        $user = new User($con, $username);
        if($user->isSubscribed()) {
            return $defaultVideoId;
        }

        $query = $con->prepare("
            SELECT v.*
            FROM videoprogress vp
            INNER JOIN videos v ON vp.videoId = v.id
            WHERE v.entityId = :entityId
            AND vp.username = :username
            ORDER BY vp.dateModified DESC, vp.id DESC
        ");
        $query->bindValue(":entityId", $entityId, PDO::PARAM_INT);
        $query->bindValue(":username", $username);
        $query->execute();

        while($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $video = new Video($con, $row);
            if($user->canWatchVideo($video)) {
                return $video->getId();
            }
        }

        $query = $con->prepare("
            SELECT *
            FROM videos
            WHERE entityId = :entityId
            ORDER BY season ASC, episode ASC
        ");
        $query->bindValue(":entityId", $entityId, PDO::PARAM_INT);
        $query->execute();

        while($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $video = new Video($con, $row);
            if($user->canWatchVideo($video)) {
                return $video->getId();
            }
        }

        return $defaultVideoId;
    }

    public static function getContinueWatchingVideos($con, $username, $limit = 30, $movies = true, $tvShows = true) {
        if(!$username) {
            return [];
        }

        $user = new User($con, $username);

        $typeSql = "";
        if($movies xor $tvShows) {
            $typeSql = "AND v.isMovie = " . ($movies ? "1" : "0");
        }

        $query = $con->prepare("
            SELECT v.*, vp.progress, vp.dateModified
            FROM videoprogress vp
            INNER JOIN videos v ON v.id = vp.videoId
            WHERE vp.username = :username
            AND vp.finished = 0
            $typeSql
            ORDER BY vp.dateModified DESC, vp.id DESC
        ");
        $query->bindValue(":username", $username);
        $query->execute();

        $result = [];
        $seenEntityIds = [];

        while($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $entityId = (int)$row["entityId"];
            if(isset($seenEntityIds[$entityId])) {
                continue;
            }

            $video = new Video($con, $row);
            if(!$user->canWatchVideo($video)) {
                continue;
            }

            $seenEntityIds[$entityId] = true;
            $result[] = [
                "video" => $video,
                "progress" => (int)$row["progress"],
                "dateModified" => $row["dateModified"]
            ];

            if(count($result) >= (int)$limit) {
                break;
            }
        }

        return $result;
    }
}
?>

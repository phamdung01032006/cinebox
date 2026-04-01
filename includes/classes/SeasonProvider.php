<?php

class SeasonProvider {

    private $con, $username;

    public function __construct($con, $username) {
        $this->con = $con;
        $this->username = $username;
    }

    public function create($entity) {
        $seasons = $entity->getSeasons();

        if(sizeof($seasons) == 0) {
            return;
        }

        $seasonHtml = "";
        foreach($seasons as $season) {
            $seasonNumber = $season->getSeasonNumber();

            $videosHtml = "";
            foreach($season->getVideos() as $video) {
                $videosHtml .= $this->createVideoSquare($video);
            }

            $seasonHtml .= "<div class='season'>
                <div class='category-header'>
                    <h3>" . htmlspecialchars(t("season.title", ["season" => $seasonNumber]), ENT_QUOTES, "UTF-8") . "</h3>
                    <div class='category-arrows'>
                        <button class='scroll-arrow left'><i class='fa-solid fa-chevron-left'></i></button>
                        <button class='scroll-arrow right'><i class='fa-solid fa-chevron-right'></i></button>
                    </div>
                </div>
                <div class='videos'>
                    $videosHtml
                </div>
            </div>";

        }
        return $seasonHtml;
    }

    private function createVideoSquare($video) {
        $id = $video->getId();
        $thumbnail = $video->getThumbnail();
        $name = $video->getTitle();
        $description = $video->getDescription();
        $episodeNumber = $video->getEpisodeNumber();
        $episodeLabel = htmlspecialchars(t("season.episode", ["episode" => $episodeNumber]), ENT_QUOTES, "UTF-8");
        $hasSeen = $video->hasSeen($this->username) ? "<button class='watchedButton'><i class='fa-solid fa-circle-check seen'></i> " . htmlspecialchars(t("season.watched"), ENT_QUOTES, "UTF-8") . "</button>": "";

        return "<a href='watch.php?id=$id'>
                <div class='episodeContainer'>
                    <div class='contents'>
                        <img src='$thumbnail'>
                        <div class='videoInfo'>
                            <h4>$episodeLabel. $name</h4>
                            <span>$description</span>
                        </div>

                        $hasSeen

                    </div>
                </div>
        </a>";
    }

}

?>

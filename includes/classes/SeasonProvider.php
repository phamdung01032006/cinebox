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



            $seasonHtml .= "<div class='season'>
                                <h3>Season $seasonNumber</h3>
                                </div>";
        }
        return $seasonHtml;
    }

    private function createVideoSquare($video) {
        $id = $video->getId();
        $thumbnail = $video->getThumbnail();
        $name = $video->getName();
        $description = $video->getDescription();
        $episodeNumber = $video->getEpisodeNumber();
    }

}

?>
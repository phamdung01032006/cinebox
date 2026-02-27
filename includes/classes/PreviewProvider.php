<?php 

class PreviewProvider {

    private $con;
    private $username;

    public function __construct($con, $username) {

        $this->con = $con;
        $this->username = $username;

    }

    public function createPreviewVideo($entity) {

        if($entity == null) {
            $entity = $this->getRandomEntity();
        }

        $id = $entity->getId();
        $name = $entity->getName();
        $preview = $entity->getPreview();
        $thumbnail = $entity->getThumbnail();

        $safePreview = htmlspecialchars($preview, ENT_QUOTES, 'UTF-8');
        $safeName = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
        return "<div class='previewContainer'>
            <img src='$thumbnail' class='previewImage' hidden>

            <video autoplay muted class='previewVideo' onended='previewEnded()'>
                <source src='$preview' type='video/mp4'>
            </video>

            <div class='previewOverlay'>

                <div class='mainDetails'>

                    <h3>$name</h3>

                    <div class='button'>
                    
                        <button><i class='fa-solid fa-play'></i></button>
                        <button onclick='volumeToggle(this)'><i class='fa-solid fa-volume-xmark'></i></button>
                        <button class='openPopupBtn' onclick='openVideoPopup(this)' data-src = '$safePreview' data-title='$safeName'><i class='fa-solid fa-expand'></i></button>
                    </div>

                </div>

            </div>

        </div>";
    }

    public function createEntityPreviewSquare($entity) {
        $id = $entity->getId();
        $thumbnail = $entity->getThumbnail();
        $name = $entity->getName();
        $safeName = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');

        return "<a href='entity.php?id=$id' class='entityCard'>
                    <div class='previewContainer small'>
                        <img src='$thumbnail' title='$safeName' alt='$safeName'>
                    </div>
                    <div class='entityTitle'>$safeName</div>
        </a>";
    }


    // chọn film ngẫu nhiên để chiếu preview
    private function getRandomEntity() {

        $entity = EntityProvider::getEntities($this->con, null, 1);
        return $entity[0];

    }

}

?>
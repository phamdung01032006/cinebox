<?php
require_once("includes/header.php");

if(!$userLoggedIn) {
    header("Location: login.php");
    exit();
}

$user = new User($con, $userLoggedIn);
$wishlistEntities = $user->getWishlistEntities();
$previewProvider = new PreviewProvider($con, $userLoggedIn);
?>

<div class="previewCategories wishlistPage">
    <div class="category">
        <div class="category-header">
            <h3><?php echo htmlspecialchars(t("wishlist.my")); ?></h3>
        </div>

        <?php if(empty($wishlistEntities)): ?>
            <div class="wishlistEmptyState">
                <p><?php echo htmlspecialchars(t("wishlist.empty_page")); ?></p>
            </div>
        <?php else: ?>
            <div class="entities wishlistEntities">
                <?php
                    foreach($wishlistEntities as $entity) {
                        echo $previewProvider->createEntityPreviewSquare($entity);
                    }
                ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once("includes/footer.php"); ?>

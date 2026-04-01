<?php
require_once("includes/header.php");

if(!$userLoggedIn) {
    header("Location: login.php?returnUrl=" . urlencode("momo.php"));
    exit();
}
?>
<link rel="stylesheet" href="assets/style/profile.css">
<div class="profilePage">
    <div class="profileLayout">
        <section class="profileRight">
            <div class="profileCol">
                <h3 class="updateProfile">MoMo</h3>
                <div class="successMessage"><?php echo htmlspecialchars(t("momo.pending"), ENT_QUOTES, "UTF-8"); ?></div>
                <div class="profileActions full">
                    <button type="button" class="button type1" onclick="window.location.href='paypal.php'"><span class="btn-txt">PayPal</span></button>
                </div>
                <div class="profileActions full">
                    <button type="button" class="button type1" onclick="window.location.href='profile.php'"><span class="btn-txt"><?php echo htmlspecialchars(t("momo.back_to_profile"), ENT_QUOTES, "UTF-8"); ?></span></button>
                </div>
            </div>
        </section>
    </div>
</div>

<?php require_once("footer.php"); ?>

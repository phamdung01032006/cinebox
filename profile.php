<?php
	require_once("includes/config.php");
	require_once("includes/classes/Account.php");
	require_once("includes/classes/FormSanitizer.php");
	require_once("includes/classes/Constants.php");
	require_once("includes/classes/User.php");

	$userLoggedIn = $_SESSION["userLoggedIn"] ?? null;
	$detailsMessage="";
	$passwordMessage="";
	$membershipMessage = "";
	$mediaMessage = "";

	if(!$userLoggedIn){
	    header("Location: login.php");
	    exit();
    }

    if(isset($_POST["saveDetailsButton"])) {
        $account = new Account($con);

		        $firstName = FormSanitizer::sanitizeFormString($_POST["firstName"]);
		        $lastName = FormSanitizer::sanitizeFormString($_POST["lastName"]);
		        $email = FormSanitizer::sanitizeFormEmail($_POST["email"]);
	            $gender = FormSanitizer::sanitizeGender($_POST["gender"] ?? "");
		        
		        if($account->updateDetails($firstName, $lastName, $email, $gender, $userLoggedIn)) {
            header("Location: profile.php?details=success");
            exit();
        }
        else {
            $errorMessage = $account->getFirstError();
            header("Location: profile.php?details=error&details_message=" . urlencode($errorMessage));
            exit();
    }
}
    
    if(isset($_POST["savePasswordButton"])) {
        $account = new Account($con);

        $oldPassword = FormSanitizer::sanitizeFormPassword($_POST["oldPassword"]);
        $newPassword = FormSanitizer::sanitizeFormPassword($_POST["newPassword"]);
	        $newPassword2 = FormSanitizer::sanitizeFormPassword($_POST["newPassword2"]);
	        
	        if($account->updatePassword($oldPassword, $newPassword, $newPassword2, $userLoggedIn)) {
	            header("Location: profile.php?password=success");
	            exit();
	        }
	        else {
	            $errorMessage = $account->getFirstError();
	            header("Location: profile.php?password=error&password_message=" . urlencode($errorMessage));
	            exit();
	    }
}

    $user = new User($con, $userLoggedIn);

    if(isset($_POST["saveMediaButton"])) {
        $mediaResult = $user->updateProfileImages($_FILES["avatarImage"] ?? null, $_FILES["coverImage"] ?? null);
        $mediaQuery = !empty($mediaResult["success"]) ? "success" : "error";
        $mediaMessageText = $mediaResult["message"] ?? t("profile.image_upload_error");
        header("Location: profile.php?media=$mediaQuery&media_message=" . urlencode($mediaMessageText));
        exit();
    }

    if(isset($_POST["activateTrialMembership"])) {
        if($user->isSubscribed()) {
            header("Location: profile.php?membership=already_active");
            exit();
        }

        if($user->activateTrialMembership()) {
            header("Location: profile.php?membership=trial_success");
            exit();
        }

        header("Location: profile.php?membership=trial_error");
        exit();
    }

    if(isset($_POST["cancelMembership"])) {
        if(!$user->isSubscribed()) {
            header("Location: profile.php?membership=already_inactive");
            exit();
        }

        if($user->cancelMembership()) {
            header("Location: profile.php?membership=cancelled");
            exit();
        }

        header("Location: profile.php?membership=cancel_error");
        exit();
    }

    $membershipState = $_GET["membership"] ?? "";
    if($membershipState === "trial_success") {
        $membershipMessage = "<div class='successMessage'>" . htmlspecialchars(t("profile.trial_success"), ENT_QUOTES, "UTF-8") . "</div>";
    }
    else if($membershipState === "already_active") {
        $membershipMessage = "<div class='successMessage'>" . htmlspecialchars(t("profile.membership_active"), ENT_QUOTES, "UTF-8") . "</div>";
    }
    else if($membershipState === "trial_error") {
        $membershipMessage = "<div class='errorMessage'>" . htmlspecialchars(t("profile.trial_error"), ENT_QUOTES, "UTF-8") . "</div>";
    }
    else if($membershipState === "cancelled") {
        $membershipMessage = "<div class='successMessage'>" . htmlspecialchars(t("profile.cancel_success"), ENT_QUOTES, "UTF-8") . "</div>";
    }
    else if($membershipState === "already_inactive") {
        $membershipMessage = "<div class='successMessage'>" . htmlspecialchars(t("profile.membership_inactive"), ENT_QUOTES, "UTF-8") . "</div>";
    }
    else if($membershipState === "cancel_error") {
        $membershipMessage = "<div class='errorMessage'>" . htmlspecialchars(t("profile.cancel_error"), ENT_QUOTES, "UTF-8") . "</div>";
    }

    $detailsState = $_GET["details"] ?? "";
    if($detailsState === "success") {
        $detailsMessage = "<div class='successMessage'>" . htmlspecialchars(t("profile.details_saved"), ENT_QUOTES, "UTF-8") . "</div>";
    }
    else if($detailsState === "error") {
        $detailsMessageText = $_GET["details_message"] ?? "";
        if($detailsMessageText !== "") {
            $detailsMessage = "<div class='errorMessage'>" . htmlspecialchars($detailsMessageText, ENT_QUOTES, "UTF-8") . "</div>";
        }
    }

    $passwordState = $_GET["password"] ?? "";
    if($passwordState === "success") {
        $passwordMessage = "<div class='successMessage'>" . htmlspecialchars(t("profile.password_changed"), ENT_QUOTES, "UTF-8") . "</div>";
    }
    else if($passwordState === "error") {
        $passwordMessageText = $_GET["password_message"] ?? "";
        if($passwordMessageText !== "") {
            $passwordMessage = "<div class='errorMessage'>" . htmlspecialchars($passwordMessageText, ENT_QUOTES, "UTF-8") . "</div>";
        }
    }

    $mediaState = $_GET["media"] ?? "";
    if($mediaState === "success" || $mediaState === "error") {
        $mediaMessageText = $_GET["media_message"] ?? "";
        if($mediaMessageText !== "") {
            $mediaMessageClass = $mediaState === "success" ? "successMessage" : "errorMessage";
            $mediaMessage = "<div class='$mediaMessageClass'>" . htmlspecialchars($mediaMessageText, ENT_QUOTES, "UTF-8") . "</div>";
        }
    }

		    $firstName = $user->getFirstName();
		    $lastName = $user->getLastName();
		    $email= $user->getEmail();
	        $gender = $user->getGender();
            

    $displayName = trim($firstName . " " . $lastName);
    if($displayName === "") $displayName = $userLoggedIn;
    $initial = strtoupper(substr($displayName, 0, 1));
    $membershipStatusClass = $user->isSubscribed() ? " active" : " inactive";
    $membershipStatusText = $user->isSubscribed() ? t("profile.membership_active") : t("profile.membership_inactive");
    $avatarPath = $user->getAvatarPath();
    $coverPath = $user->getCoverPath();
    $safeAvatarPath = htmlspecialchars($avatarPath, ENT_QUOTES, "UTF-8");
    $safeCoverPath = htmlspecialchars($coverPath, ENT_QUOTES, "UTF-8");
	    $coverStyle = $coverPath
	        ? " style=\"background-image: linear-gradient(135deg, rgba(15, 15, 15, 0.18), rgba(15, 15, 15, 0.5)), url('" . $safeCoverPath . "');\""
	        : "";

    require_once("includes/header.php");
?>
<link rel="stylesheet" href="assets/style/profile.css">
<div class="profilePage">
    <div class="profileLayout">
        <aside class="profileLeft">
        <p class="profileWelcome"><?php echo htmlspecialchars(t("profile.welcome", ["name" => $displayName])); ?></p>
        <div class="profileHero"<?php echo $coverStyle; ?>></div>

        <div class="profileIdentity">
            <div class="profileAvatar">
                <?php if($avatarPath): ?>
                    <img src="<?php echo $safeAvatarPath; ?>" alt="<?php echo htmlspecialchars($displayName, ENT_QUOTES, "UTF-8"); ?>">
                <?php else: ?>
                    <?php echo htmlspecialchars($initial); ?>
                <?php endif; ?>
            </div>
            <div class="profileIdentityText">
                <h2><?php echo htmlspecialchars($displayName); ?></h2>
                <p><?php echo htmlspecialchars($email); ?></p>
            </div>
        </div>

        <form class="profileMediaForm" method="POST" enctype="multipart/form-data">
            <h3 class="updateProfile"><?php echo htmlspecialchars(t("profile.media_heading"), ENT_QUOTES, "UTF-8"); ?></h3>

            <div class="profileMediaGrid">
                <div class="profileMediaCard">
                    <div class="profileMediaPreview avatarPreview">
                        <?php if($avatarPath): ?>
                            <img src="<?php echo $safeAvatarPath; ?>" alt="<?php echo htmlspecialchars($displayName, ENT_QUOTES, "UTF-8"); ?>">
                        <?php else: ?>
                            <span><?php echo htmlspecialchars($initial); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="profileMediaMeta">
                        <strong><?php echo htmlspecialchars(t("profile.avatar_label"), ENT_QUOTES, "UTF-8"); ?></strong>
                        <label class="profileUploadButton" for="avatarImage"><?php echo htmlspecialchars(t("profile.choose_avatar"), ENT_QUOTES, "UTF-8"); ?></label>
                        <input type="file" id="avatarImage" name="avatarImage" class="profileFileInput" accept="image/*">
                    </div>
                </div>

                <div class="profileMediaCard">
                    <div class="profileMediaPreview coverPreview"<?php echo $coverStyle; ?>>
                        <?php if(!$coverPath): ?>
                            <span><?php echo htmlspecialchars(t("profile.cover_placeholder"), ENT_QUOTES, "UTF-8"); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="profileMediaMeta">
                        <strong><?php echo htmlspecialchars(t("profile.cover_label"), ENT_QUOTES, "UTF-8"); ?></strong>
                        <label class="profileUploadButton" for="coverImage"><?php echo htmlspecialchars(t("profile.choose_cover"), ENT_QUOTES, "UTF-8"); ?></label>
                        <input type="file" id="coverImage" name="coverImage" class="profileFileInput" accept="image/*">
                    </div>
                </div>
            </div>

            <p class="profileMediaHint"><?php echo htmlspecialchars(t("profile.media_hint"), ENT_QUOTES, "UTF-8"); ?></p>
            <div class="message"><?php echo $mediaMessage; ?></div>
            <button type="submit" name="saveMediaButton" class="button type1 profileMediaSave"><span class="btn-txt"><?php echo htmlspecialchars(t("profile.save_media"), ENT_QUOTES, "UTF-8"); ?></span></button>
        </form>
        </aside>

	        <div class="subscriptionButtons">
	            <h3><?php echo htmlspecialchars(t("profile.subscription")); ?></h3>
                <p class="membershipStatus<?php echo $membershipStatusClass; ?>"><?php echo htmlspecialchars($membershipStatusText, ENT_QUOTES, "UTF-8"); ?></p>
                <?php echo $membershipMessage; ?>

	                <div class="membershipMenu">
	                    <button type="button" class="navButton membershipTriggerButton membershipTrigger" aria-expanded="false" aria-controls="membershipOptions">
	                        <span class="top-key"></span>
	                        <span class="text"><?php echo htmlspecialchars(t("profile.membership_cta"), ENT_QUOTES, "UTF-8"); ?></span>
	                        <span class="bottom-key-1"></span>
	                        <span class="bottom-key-2"></span>
	                    </button>

	                    <div id="membershipOptions" class="membershipOptions" aria-hidden="true">
                        <a href="paypal.php" class="membershipOption"><?php echo htmlspecialchars(t("profile.paypal_option"), ENT_QUOTES, "UTF-8"); ?></a>
                        <a href="momo.php" class="membershipOption"><?php echo htmlspecialchars(t("profile.momo_option"), ENT_QUOTES, "UTF-8"); ?></a>
                        <form method="POST" class="membershipTrialForm">
                            <button type="submit" name="activateTrialMembership" class="membershipOption membershipOptionButton"><?php echo htmlspecialchars(t("profile.trial_option"), ENT_QUOTES, "UTF-8"); ?></button>
                        </form>
                    </div>
                </div>

                <?php if($user->isSubscribed()): ?>
                    <form method="POST" class="membershipCancelForm">
                        <button type="submit" name="cancelMembership" class="membershipCancelButton"><?php echo htmlspecialchars(t("profile.cancel_membership"), ENT_QUOTES, "UTF-8"); ?></button>
                    </form>
                <?php endif; ?>
        </div>

        <section class="profileRight">
            <form class="profileForm" method="POST">
                <div class="profileCol left">
                    <h3 class="updateProfile"><?php echo htmlspecialchars(t("profile.user_details")); ?></h3>

                    <div class="profileField">
                    <label for="firstName"><?php echo htmlspecialchars(t("profile.first_name")); ?></label>
                    <input type="text" id="firstName" name="firstName" placeholder="<?php echo htmlspecialchars(t("profile.your_first_name")); ?>" value="<?php echo $firstName; ?>">
                    </div>

                    <div class="profileField">
                    <label for="lastName"><?php echo htmlspecialchars(t("profile.last_name")); ?></label>
                    <input type="text" id="lastName" name="lastName" placeholder="<?php echo htmlspecialchars(t("profile.your_last_name")); ?>" value="<?php echo $lastName; ?>">
                    </div>

	                    <div class="profileField">
	                    <label for="email"><?php echo htmlspecialchars(t("profile.email")); ?></label>
	                    <input type="email" id="email" name="email" placeholder="<?php echo htmlspecialchars(t("profile.your_email")); ?>" value="<?php echo $email; ?>">
	                    </div>

                        <div class="profileField">
                        <label for="gender"><?php echo htmlspecialchars(t("profile.gender")); ?></label>
                        <select id="gender" name="gender">
                            <option value="male" <?php echo $gender === "male" ? "selected" : ""; ?>><?php echo htmlspecialchars(t("auth.male")); ?></option>
                            <option value="female" <?php echo $gender === "female" ? "selected" : ""; ?>><?php echo htmlspecialchars(t("auth.female")); ?></option>
                            <option value="other" <?php echo $gender === "other" ? "selected" : ""; ?>><?php echo htmlspecialchars(t("auth.other")); ?></option>
                            <option value="prefer_not_to_say" <?php echo $gender === "prefer_not_to_say" ? "selected" : ""; ?>><?php echo htmlspecialchars(t("auth.prefer_not_to_say")); ?></option>
                        </select>
                        </div>

                    <div class="profileField">
                    <label for="username"><?php echo htmlspecialchars(t("profile.username")); ?></label>
                    <input type="text" id="username" name="username" placeholder=<?php echo htmlspecialchars($userLoggedIn); ?> disabled>
                    </div>
                    
                    <div class="profileActions full">
                    <div class="message">
                        <?php echo $detailsMessage; ?>
                    </div>
                    <button type="submit" name="saveDetailsButton" class="button type1"><span class="btn-txt"><?php echo htmlspecialchars(t("profile.save_details")); ?></span></button>
                    </div>
                </div>
                </form>
                <form class="profileForm" method="POST">
                <div class="profileCol right">
                    <h3 class="updateProfile"><?php echo htmlspecialchars(t("profile.update_password")); ?></h3>

                    <div class="profileField">
                    <label for="oldPassword"><?php echo htmlspecialchars(t("profile.old_password")); ?></label>
                    <input type="password" id="oldPassword" name="oldPassword" placeholder="<?php echo htmlspecialchars(t("profile.old_password")); ?>">
                    </div>

                    <div class="profileField">
                    <label for="newPassword"><?php echo htmlspecialchars(t("profile.new_password")); ?></label>
                    <input type="password" id="newPassword" name="newPassword" placeholder="<?php echo htmlspecialchars(t("profile.new_password")); ?>">
                    </div>

                    <div class="profileField full">
                    <label for="newPassword2"><?php echo htmlspecialchars(t("profile.confirm_new_password")); ?></label>
                    <input type="password" id="newPassword2" name="newPassword2" placeholder="<?php echo htmlspecialchars(t("profile.confirm_new_password")); ?>">
                    </div>
                    <div class="profileActions full">
                    <div class="message">
                        <?php echo $passwordMessage; ?>
                    </div>
                    <button type="submit" name="savePasswordButton" class="button type1"><span class="btn-txt"><?php echo htmlspecialchars(t("profile.change_password")); ?></span></button>
                    </div>
                </div>
            </form>
        </section>
    </div>
</div>

<?php require_once("footer.php"); ?>

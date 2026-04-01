<?php
    require_once("includes/header.php");
    require_once("includes/classes/Account.php");
    require_once("includes/classes/FormSanitizer.php");
    require_once("includes/classes/Constants.php");

    $detailsMessage="";
    $passwordMessage="";
    $loggedInUserID = $userLoggedIn;

    // Get logged-in user ID from sesion 
    // Session name need to be changed as per your system 
    $loggedInUserID = !empty($_SESSION['userID'])?$_SESSION['userID']:0; 

    if(isset($_POST["saveDetailsButton"])) {
        $account = new Account($con);

	        $firstName = FormSanitizer::sanitizeFormString($_POST["firstName"]);
	        $lastName = FormSanitizer::sanitizeFormString($_POST["lastName"]);
	        $email = FormSanitizer::sanitizeFormEmail($_POST["email"]);
            $gender = FormSanitizer::sanitizeGender($_POST["gender"] ?? "");
	        
	        if($account->updateDetails($firstName, $lastName, $email, $gender, $userLoggedIn)) {
            $detailsMessage = "<div class='successMessage'> 
                                    " . htmlspecialchars(t("profile.details_saved"), ENT_QUOTES, "UTF-8") . "
                                </div>";
        }
        else {
            $errorMessage = $account->getFirstError();
            
            $detailsMessage = "<div class='errorMessage'> 
                                    $errorMessage
                                </div>";
    }
}
    
    if(isset($_POST["savePasswordButton"])) {
        $account = new Account($con);

        $oldPassword = FormSanitizer::sanitizeFormPassword($_POST["oldPassword"]);
        $newPassword = FormSanitizer::sanitizeFormPassword($_POST["newPassword"]);
        $newPassword2 = FormSanitizer::sanitizeFormPassword($_POST["newPassword2"]);
        
        if($account->updatePassword($oldPassword, $newPassword, $newPassword2, $userLoggedIn)) {
            $passwordMessage = "<div class='successMessage'> 
                                    " . htmlspecialchars(t("profile.password_changed"), ENT_QUOTES, "UTF-8") . "
                                </div>";
        }
        else {
            $errorMessage = $account->getFirstError();
            
            $passwordMessage = "<div class='errorMessage'> 
                                    $errorMessage
                                </div>";
    }
}

    if(!$userLoggedIn){
        header("Location: login.php");
        exit();
    }

    $user = new User($con, $userLoggedIn);

	    $firstName = isset($_POST["firstName"]) ? $_POST["firstName"] : $user->getFirstName();
	    $lastName = isset($_POST["lastName"]) ? $_POST["lastName"] : $user->getLastName();
	    $email= isset($_POST["email"]) ? $_POST["email"] : $user->getEmail();
        $gender = isset($_POST["gender"]) ? $_POST["gender"] : $user->getGender();
            

    $displayName = trim($firstName . " " . $lastName);
    if($displayName === "") $displayName = $userLoggedIn;
    $initial = strtoupper(substr($displayName, 0, 1));
?>
<link rel="stylesheet" href="assets/style/profile.css">
<div class="profilePage">
    <div class="profileLayout">
        <aside class="profileLeft">
        <p class="profileWelcome"><?php echo htmlspecialchars(t("profile.welcome", ["name" => $displayName])); ?></p>
        <div class="profileHero"></div>

        <div class="profileIdentity">
            <div class="profileAvatar"><?php echo htmlspecialchars($initial); ?></div>
            <div class="profileIdentityText">
                <h2><?php echo htmlspecialchars($displayName); ?></h2>
                <p><?php echo htmlspecialchars($email); ?></p>
            </div>
        </div>
        </aside>

	        <div class="subscriptionButtons">
	            <h3><?php echo htmlspecialchars(t("profile.subscription")); ?></h3>
                <button type="button" class="animatedSubscriptionButton" onclick="window.location.href='wishlist.php'">
                    <svg xmlns="http://www.w3.org/2000/svg" class="arr-2" viewBox="0 0 24 24">
                        <path
                        d="M16.1716 10.9999L10.8076 5.63589L12.2218 4.22168L20 11.9999L12.2218 19.778L10.8076 18.3638L16.1716 12.9999H4V10.9999H16.1716Z"
                        ></path>
                    </svg>
                    <span class="text"><?php echo htmlspecialchars(t("nav.wishlist")); ?></span>
                    <span class="circle"></span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="arr-1" viewBox="0 0 24 24">
                        <path
                        d="M16.1716 10.9999L10.8076 5.63589L12.2218 4.22168L20 11.9999L12.2218 19.778L10.8076 18.3638L16.1716 12.9999H4V10.9999H16.1716Z"
                        ></path>
                    </svg>
                </button>
	            <button type="button" class="animatedSubscriptionButton" onclick="window.location.href='paypal.php'">
	                <svg xmlns="http://www.w3.org/2000/svg" class="arr-2" viewBox="0 0 24 24">
	                    <path
                    d="M16.1716 10.9999L10.8076 5.63589L12.2218 4.22168L20 11.9999L12.2218 19.778L10.8076 18.3638L16.1716 12.9999H4V10.9999H16.1716Z"
                    ></path>
                </svg>
                <span class="text">PayPal</span>
                <span class="circle"></span>
                <svg xmlns="http://www.w3.org/2000/svg" class="arr-1" viewBox="0 0 24 24">
                    <path
                    d="M16.1716 10.9999L10.8076 5.63589L12.2218 4.22168L20 11.9999L12.2218 19.778L10.8076 18.3638L16.1716 12.9999H4V10.9999H16.1716Z"
                    ></path>
                </svg>
            </button>

            <button type="button" class="animatedSubscriptionButton" onclick="window.location.href='momo.php'">
                <svg xmlns="http://www.w3.org/2000/svg" class="arr-2" viewBox="0 0 24 24">
                    <path
                    d="M16.1716 10.9999L10.8076 5.63589L12.2218 4.22168L20 11.9999L12.2218 19.778L10.8076 18.3638L16.1716 12.9999H4V10.9999H16.1716Z"
                    ></path>
                </svg>
                <span class="text">MoMo</span>
                <span class="circle"></span>
                <svg xmlns="http://www.w3.org/2000/svg" class="arr-1" viewBox="0 0 24 24">
                    <path
                    d="M16.1716 10.9999L10.8076 5.63589L12.2218 4.22168L20 11.9999L12.2218 19.778L10.8076 18.3638L16.1716 12.9999H4V10.9999H16.1716Z"
                    ></path>
                </svg>
            </button>
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

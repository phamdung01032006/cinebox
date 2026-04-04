<?php

require_once("includes/config.php");
require_once("includes/classes/FormSanitizer.php");
require_once("includes/classes/Constants.php");
require_once("includes/classes/Account.php");

$account = new Account($con);

if(isset($_POST["submitButton"])) {

// Gọi đến hàm sanitizeFormString trong class FormSanitizer
    $firstName = FormSanitizer::sanitizeFormString($_POST["firstName"]);
    $lastName = FormSanitizer::sanitizeFormString($_POST["lastName"]);
	    $username = FormSanitizer::sanitizeFormUsername($_POST["username"]);
	    $email = FormSanitizer::sanitizeFormEmail($_POST["email"]);
	    $email2 = FormSanitizer::sanitizeFormEmail($_POST["email2"]);
        $gender = FormSanitizer::sanitizeGender($_POST["gender"] ?? "");
	    $password = FormSanitizer::sanitizeFormPassword($_POST["password"]);
	    $password2 = FormSanitizer::sanitizeFormPassword($_POST["password2"]);

	    $success = $account->register($firstName, $lastName, $username, $email, $email2, $gender, $password, $password2);

    if($success) {

        $_SESSION["userLoggedIn"] = $username;
        header("Location:index.php");

    }

}

// Hàm lưu lại những gì đã nhập kể cả sau khi bấm nút SIGN UP hay LOG IN
function getInputValue($name) {
    if(isset($_POST[$name])) {
        echo $_POST[$name];
    }
}

?>

<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars(getCurrentLanguage()); ?>">
	<head>
	    <meta charset="UTF-8">
	    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	    <title><?php echo htmlspecialchars(t("site.title")); ?></title>
	    <link rel="stylesheet" type="text/css" href="assets/style/style.css"/>
        <script src="assets/js/password-toggle.js" defer></script>
	</head>
<body>
    
    <div class="signInContainer">
        
        <div class="authSide">
            <div class="authOverlay">
                <!-- <img src="assets\images\cinebox.png" title="CineBox Logo" alt="CineBox Logo"> -->
                <h2><?php echo htmlspecialchars(t("auth.step_world")); ?> <div>
                <button class="btn"><i class="animation"></i>CINEBOX<i class="animation"></i>
                </button>
                </div>
                </h2>
                <p><?php echo htmlspecialchars(t("auth.watch_all_in_one")); ?></p>
            </div>
        </div>

        <div class="column">

            <div class="header">
                <h3><?php echo htmlspecialchars(t("auth.signup")); ?></h3>
                <span><?php echo htmlspecialchars(t("auth.continue_to_cinebox")); ?></span>
            </div>

            <form method="POST">

                <?php echo $account->getError(Constants::$firstNameCharacters)?>
                <label for="firstName"><?php echo htmlspecialchars(t("auth.first_name")); ?></label>
                <input type="text" id="firstName" name="firstName" placeholder="<?php echo htmlspecialchars(t("auth.first_name")); ?>" value="<?php getInputValue("firstName") ?>" required>

                <?php echo $account->getError(Constants::$lastNameCharacters)?>
                <label for="lastName"><?php echo htmlspecialchars(t("auth.last_name")); ?></label>
                <input type="text" id="lastName" name="lastName" placeholder="<?php echo htmlspecialchars(t("auth.last_name")); ?>" value="<?php getInputValue("lastName") ?>" required>
                
                <?php echo $account->getError(Constants::$usernameCharacters)?>
                <?php echo $account->getError(Constants::$usernameTaken)?>
                <label for="username"><?php echo htmlspecialchars(t("auth.username")); ?></label>
                <input type="text" id="username" name="username" placeholder="<?php echo htmlspecialchars(t("auth.username")); ?>" value="<?php getInputValue("username") ?>" required>
                
                <?php echo $account->getError(Constants::$emailsDontMatch)?>
                <?php echo $account->getError(Constants::$emailInvalid)?>
                <?php echo $account->getError(Constants::$emailTaken)?>
                <label for="email"><?php echo htmlspecialchars(t("profile.email")); ?></label>
                <input type="email" id="email" name="email" placeholder="<?php echo htmlspecialchars(t("profile.email")); ?>" value="<?php getInputValue("email") ?>" required>

	                <label for="email2"><?php echo htmlspecialchars(t("auth.confirm_email")); ?></label>
	                <input type="email" id="email2" name="email2" placeholder="<?php echo htmlspecialchars(t("auth.confirm_email")); ?>" value="<?php getInputValue("email2") ?>" required>

                    <?php echo $account->getError(Constants::$genderInvalid)?>
                    <label for="gender"><?php echo htmlspecialchars(t("profile.gender")); ?></label>
                    <select id="gender" name="gender" required>
                        <option value=""><?php echo htmlspecialchars(t("auth.select_gender")); ?></option>
                        <option value="male" <?php echo (isset($_POST["gender"]) && $_POST["gender"] === "male") ? "selected" : ""; ?>><?php echo htmlspecialchars(t("auth.male")); ?></option>
                        <option value="female" <?php echo (isset($_POST["gender"]) && $_POST["gender"] === "female") ? "selected" : ""; ?>><?php echo htmlspecialchars(t("auth.female")); ?></option>
                        <option value="other" <?php echo (isset($_POST["gender"]) && $_POST["gender"] === "other") ? "selected" : ""; ?>><?php echo htmlspecialchars(t("auth.other")); ?></option>
                        <option value="prefer_not_to_say" <?php echo (isset($_POST["gender"]) && $_POST["gender"] === "prefer_not_to_say") ? "selected" : ""; ?>><?php echo htmlspecialchars(t("auth.prefer_not_to_say")); ?></option>
                    </select>

	                <?php echo $account->getError(Constants::$passwordsDontMatch)?>
	                <label for="password"><?php echo htmlspecialchars(t("auth.password")); ?></label>
                    <div class="passwordFieldWrap authPasswordField">
	                    <input type="password" id="password" name="password" placeholder="<?php echo htmlspecialchars(t("auth.password")); ?>" required>
                        <button type="button" class="passwordToggle" data-show-label="<?php echo htmlspecialchars(t("form.show_password"), ENT_QUOTES, "UTF-8"); ?>" data-hide-label="<?php echo htmlspecialchars(t("form.hide_password"), ENT_QUOTES, "UTF-8"); ?>"></button>
                    </div>

	                <label for="password2"><?php echo htmlspecialchars(t("auth.confirm_password")); ?></label>
                    <div class="passwordFieldWrap authPasswordField">
	                    <input type="password" id="password2" name="password2" placeholder="<?php echo htmlspecialchars(t("auth.confirm_password")); ?>" required>
                        <button type="button" class="passwordToggle" data-show-label="<?php echo htmlspecialchars(t("form.show_password"), ENT_QUOTES, "UTF-8"); ?>" data-hide-label="<?php echo htmlspecialchars(t("form.hide_password"), ENT_QUOTES, "UTF-8"); ?>"></button>
                    </div>
	                
	                <a href="login.php" class="signInMessage"><?php echo htmlspecialchars(t("auth.already_have_account")); ?> <span><?php echo htmlspecialchars(t("auth.log_in_here")); ?></span></a>

                <button type="submit" name="submitButton"><?php echo htmlspecialchars(t("auth.sign_up_button")); ?></button>
                

            </form>


        </div>
    </div>


</body>
</html>

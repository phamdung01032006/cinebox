<?php

require_once("includes/config.php");
require_once("includes/classes/FormSanitizer.php");
require_once("includes/classes/Constants.php");
require_once("includes/classes/Account.php");

$account = new Account($con);

if(isset($_POST["submitButton"])) {


    $username = FormSanitizer::sanitizeFormUsername($_POST["username"]);
    $password = FormSanitizer::sanitizeFormPassword($_POST["password"]);

    $success = $account->login($username, $password);

    if($success) {
        // storing something in the session variable, check this right here and give the username of
        // the person that logged in
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

</head>
<body>
    
    <div class="signInContainer">

        <div class="authSide">
            <div class="authOverlay">
                <!-- <img src="assets\images\cinebox.png" title="CineBox Logo" alt="CineBox Logo"> -->
                <h2><?php echo htmlspecialchars(t("auth.return_world")); ?> <div>
                <button class="btn"><i class="animation"></i>CINEBOX<i class="animation"></i>
                </button>
                </div>
                </h2>
                <p><?php echo htmlspecialchars(t("auth.watch_all_in_one")); ?></p>
            </div>
        </div>

        <div class="column">

            <div class="header">
                <h3><?php echo htmlspecialchars(t("auth.login")); ?></h3>
                <span><?php echo htmlspecialchars(t("auth.continue_to_cinebox")); ?></span>
            </div>

            <form method="POST">
                
                <?php echo $account->getError(Constants::$loginFailed)?>
                <label for="username"><?php echo htmlspecialchars(t("auth.username")); ?></label>
                <input type="text" name="username" placeholder="<?php echo htmlspecialchars(t("auth.username")); ?>" value="<?php getInputValue("username") ?>" required>
                
                <label for="password"><?php echo htmlspecialchars(t("auth.password")); ?></label>
                <input type="password" name="password" placeholder="<?php echo htmlspecialchars(t("auth.password")); ?>" required>
                
                <a href="register.php" class="signInMessage"><?php echo htmlspecialchars(t("auth.need_account")); ?> <span><?php echo htmlspecialchars(t("auth.sign_up_here")); ?></span></a>

                <button type="submit" name="submitButton"><?php echo htmlspecialchars(t("auth.login_button")); ?></button>
                

            </form>



        </div>
    </div>


</body>
</html>

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
    $password = FormSanitizer::sanitizeFormPassword($_POST["password"]);
    $password2 = FormSanitizer::sanitizeFormPassword($_POST["password2"]);

    $success = $account->register($firstName, $lastName, $username, $email, $email2,$password, $password2);

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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CineBox</title>
    <link rel="stylesheet" type="text/css" href="assets/style/style.css"/>
</head>
<body>
    
    <div class="signInContainer">
        <div class="column">

            <div class="header">
                <img src="assets\images\cinebox.png" title="CineBox Logo" alt="CineBox Logo">
                <h3>Sign Up</h3>
                <span>to continue to CineBox</span>
            </div>

            <form method="POST">

                <?php echo $account->getError(Constants::$firstNameCharacters)?>
                <label for="firstName">First name</label>
                <input type="text" id="firstName" name="firstName" placeholder="First name" value="<?php getInputValue("firstName") ?>" required>

                <?php echo $account->getError(Constants::$lastNameCharacters)?>
                <label for="lastName">Last name</label>
                <input type="text" id="lastName" name="lastName" placeholder="Last name" value="<?php getInputValue("lastName") ?>" required>
                
                <?php echo $account->getError(Constants::$usernameCharacters)?>
                <?php echo $account->getError(Constants::$usernameTaken)?>
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Username" value="<?php getInputValue("username") ?>" required>
                
                <?php echo $account->getError(Constants::$emailsDontMatch)?>
                <?php echo $account->getError(Constants::$emailInvalid)?>
                <?php echo $account->getError(Constants::$emailTaken)?>
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Email" value="<?php getInputValue("email") ?>" required>

                <label for="email2">Confirm email</label>
                <input type="email" id="email2" name="email2" placeholder="Confirm email" value="<?php getInputValue("email2") ?>" required>

                <?php echo $account->getError(Constants::$passwordsDontMatch)?>
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Password" required>

                <label for="password2">Confirm password</label>
                <input type="password" id="password2" name="password2" placeholder="Confirm password" required>
                
                <a href="login.php" class="signInMessage">Already have an account? <span>Log in here!</span></a>

                <button type="submit" name="submitButton">SIGN UP</button>
                

            </form>


        </div>
    </div>


</body>
</html>
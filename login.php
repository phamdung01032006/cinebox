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
                <h3>Log in</h3>
                <span>to continue to CineBox</span>
            </div>

            <form method="POST">
                
                <?php echo $account->getError(Constants::$loginFailed)?>
                <label for="username">Username</label>
                <input type="text" name="username" placeholder="Username" value="<?php getInputValue("username") ?>" required>
                
                <label for="password">Password</label>
                <input type="password" name="password" placeholder="Password" required>
                
                <a href="register.php" class="signInMessage">Need an account? <span>Sign up here!</span></a>

                <button type="submit" name="submitButton">LOG IN</button>
                

            </form>



        </div>
    </div>


</body>
</html>
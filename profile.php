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
        
        if($account->updateDetails($firstName, $lastName, $email, $userLoggedIn)) {
            $detailsMessage = "<div class='successMessage'> 
                                    Details saved
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
                                    Password changed
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
            

    $displayName = trim($firstName . " " . $lastName);
    if($displayName === "") $displayName = $userLoggedIn;
    $initial = strtoupper(substr($displayName, 0, 1));
?>
<link rel="stylesheet" href="assets/style/profile.css">
<div class="profilePage">
    <div class="profileLayout">
        <aside class="profileLeft">
        <p class="profileWelcome">Welcome, <?php echo htmlspecialchars($displayName); ?></p>
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
            <h3>Subscription</h3>
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
                <div class="profileCol">
                    <h3 class="updateProfile">User details</h3>

                    <div class="profileField">
                    <label for="firstName">First name</label>
                    <input type="text" id="firstName" name="firstName" placeholder="Your first name" value="<?php echo $firstName; ?>">
                    </div>

                    <div class="profileField">
                    <label for="lastName">Last name</label>
                    <input type="text" id="lastName" name="lastName" placeholder="Your last name" value="<?php echo $lastName; ?>">
                    </div>

                    <div class="profileField">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Your email" value="<?php echo $email; ?>">
                    </div>

                    <div class="profileField">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" placeholder=<?php echo htmlspecialchars($userLoggedIn); ?> disabled>
                    </div>
                    
                    <div class="profileActions full">
                    <div class="message">
                        <?php echo $detailsMessage; ?>
                    </div>
                    <button type="submit" name="saveDetailsButton" class="button type1"><span class="btn-txt">Save details</span></button>
                    </div>
                </div>

                <div class="profileCol">
                    <h3 class="updateProfile">Update password</h3>

                    <div class="profileField">
                    <label for="oldPassword">Old password</label>
                    <input type="password" id="oldPassword" name="oldPassword" placeholder="Old password">
                    </div>

                    <div class="profileField">
                    <label for="newPassword">New password</label>
                    <input type="password" id="newPassword" name="newPassword" placeholder="New password">
                    </div>

                    <div class="profileField full">
                    <label for="newPassword2">Confirm new password</label>
                    <input type="password" id="newPassword2" name="newPassword2" placeholder="Confirm new password">
                    </div>

                    <div class="profileActions full">
                    <div class="message">
                        <?php echo $passwordMessage; ?>
                    </div>
                    <button type="submit" name="savePasswordButton" class="button type1"><span class="btn-txt">Change password</span></button>
                    </div>
                </div>
            </form>
        </section>
    </div>
</div>

<?php require_once("footer.php"); ?>

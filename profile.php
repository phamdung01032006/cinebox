<?php
    require_once("includes/header.php");
    require_once("includes/classes/Account.php");
    require_once("includes/classes/FormSanitizer.php");
    require_once("includes/classes/Constants.php");

    $detailsMessage="";
    $passwordMessage="";

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
                    <button type="submit" name="saveDetailsButton">Save details</button>
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
                    <button type="submit" name="savePasswordButton">Change password</button>
                    </div>
                </div>
            </form>
        </section>
    </div>

    <div class="formSection">
        <div id="paypal-button-container"></div>
        <p id="result-message"></p>
    </div>
</div>

<?php require_once("footer.php"); ?>

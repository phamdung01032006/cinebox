<?php
require_once("includes/header.php");
?>

<div class="settingsContainer column">
    <div class="formSection">
        <form method="POST">
            <h2>User details</h2>

            <?php
            $user = new User($con, $userLoggedIn);

            $firstName = isset($_POST["firstName"]) ? $_POST["firstName"] : $user->getFirstName();
            $lastName = isset($_POST["lastName"]) ? $_POST["lastName"] : $user->getLastName();
            $email= isset($_POST["email"]) ? $_POST["email"] : $user->getEmail();
            
            ?>

            <label for="firstName">First name</label>
            <input type="text" id="firstName" name="firstName" placeholder="First name" value="<?php echo $firstName; ?>">

            <label for="lastName">Last name</label>
            <input type="text" id="lastName" name="lastName" placeholder="Last name" value="<?php echo $lastName; ?>">
            
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Email" value="<?php echo $email; ?>">

            <button type="submit" name="submitDetailButton">Update</button>
        </form>
    </div>
        <div class="formSection">
        <form method="POST">
            <h2>Update password</h2>

            <label for="oldPassword">Old password</label>
            <input type="password" id="oldPassword" name="oldPassword" placeholder="Old password">

            <label for="newPassword">New password</label>
            <input type="password" id="newPassword" name="newPassword" placeholder="New password">

            <label for="newPassword2">Confirm new password</label>
            <input type="password" id="newPassword2" name="newPassword2" placeholder="Confirm new password">


            <button type="submit" name="submitDetailButton">Update</button>
        </form>
    </div>
</div>
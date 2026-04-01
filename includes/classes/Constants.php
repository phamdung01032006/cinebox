<?php
class Constants {
    public static $firstNameCharacters;
    public static $lastNameCharacters;
    public static $usernameCharacters;
    public static $usernameTaken;
    public static $emailsDontMatch;
    public static $emailInvalid;
    public static $emailTaken;
    public static $genderInvalid;
    public static $passwordsDontMatch;
    public static $passwordLength;
    public static $loginFailed;
    public static $passwordIncorrect;

    public static function init() {
        self::$firstNameCharacters = t("error.first_name_characters");
        self::$lastNameCharacters = t("error.last_name_characters");
        self::$usernameCharacters = t("error.username_characters");
        self::$usernameTaken = t("error.username_taken");
        self::$emailsDontMatch = t("error.emails_dont_match");
        self::$emailInvalid = t("error.email_invalid");
        self::$emailTaken = t("error.email_taken");
        self::$genderInvalid = t("error.gender_invalid");
        self::$passwordsDontMatch = t("error.passwords_dont_match");
        self::$passwordLength = t("error.password_length");
        self::$loginFailed = t("error.login_failed");
        self::$passwordIncorrect = t("error.password_incorrect");
    }
}

Constants::init();
?>

<?php
class Account {

    private $con;
    private $errorArray = array();

    public function __construct($con) {
        $this->con = $con;
    }

    public function register($fn, $ln, $un, $em, $em2, $pw, $pw2) {
        $this -> validateFirstName($fn);
        $this -> validateLastName($ln);
        $this -> validateUserName($un);
        $this -> validateEmails($em, $em2);
        $this -> validatePasswords($pw, $pw2);

        if(empty($this->errorArray)) {
            return $this->insertUserDetails($fn, $ln, $un, $em, $pw);
        }

        return false;
    }

    public function login($un, $pw) {

    // hash the password and save it to $pw, có thể dùng hàm hash khác ngoài sha512
    // ở đây tiếp tục hash là do mật khẩu đã lưu trong MySQL đã hash nên mk khi log in người dùng nhập vào cũng
    // hash để so sánh với mk ở trong database
    $pw = hash("sha512", $pw);

    $query = $this->con->prepare("SELECT * FROM users WHERE username=:un AND password=:pw");
    
    $query->bindvalue(":un", $un);
    $query->bindvalue(":pw", $pw);

    // Code để đưa ra chi tiết lỗi trên màn hình nếu bị lỗi
    // $query->execute;
    // var_dump($query->errorInfo());
    // return false;

    $query->execute();

    if($query->rowCount() == 1) {
        return true;
    }

    array_push($this->errorArray, Constants::$loginFailed);
    return false;

    }

    private function insertUserDetails($fn, $ln, $un, $em, $pw) {

    // hash the password and save it to $pw, có thể dùng hàm hash khác ngoài sha512
    $pw = hash("sha512", $pw);

    $query = $this->con->prepare("INSERT INTO users (firstName, lastName, username, email, password)
                                    VALUES (:fn, :ln, :un, :em, :pw)");
    
    $query->bindvalue(":fn", $fn);
    $query->bindvalue(":ln", $ln);
    $query->bindvalue(":un", $un);
    $query->bindvalue(":em", $em);
    $query->bindvalue(":pw", $pw);

    // Code để đưa ra chi tiết lỗi trên màn hình nếu bị lỗi
    // $query->execute;
    // var_dump($query->errorInfo());
    // return false;

    return $query->execute();

    }

    private function validateFirstName($fn) {
        if(strlen($fn) < 2 || strlen($fn) > 25) {
            array_push($this->errorArray, Constants::$firstNameCharacters);
    }
}
    private function validateLastName($ln) {
        if(strlen($ln) < 2 || strlen($ln) > 25) {
            array_push($this->errorArray, Constants::$lastNameCharacters);
    }
}
    private function validateUserName($un) {
        if(strlen($un) < 5 || strlen($un) > 25) {
            array_push($this->errorArray, Constants::$usernameCharacters);
            return;
    }
    // Kiểm tra xem thử có username trùng trong database hay không bằng SQL
        $query = $this->con->prepare("SELECT * FROM users WHERE username=:un");
        // bind :un to $un variable
        $query->bindValue(":un", $un);

        // execute query
        $query->execute();

        if($query->rowCount() != 0) {
            array_push($this->errorArray, Constants::$usernameTaken);
        }

}

    private function validateEmails($em, $em2) {
        if($em != $em2) {
            array_push($this->errorArray, Constants::$emailsDontMatch);
            return;
        }

        if(!filter_var($em, FILTER_VALIDATE_EMAIL)) {
            array_push($this->errorArray, Constants::$emailInvalid);
            return;
        }

        // Kiểm tra xem thử có email trùng trong database hay không bằng SQL
        $query = $this->con->prepare("SELECT * FROM users WHERE email=:em");
        // bind :un to $un variable
        $query->bindValue(":em", $em);

        // execute query
        $query->execute();

        if($query->rowCount() != 0) {
            array_push($this->errorArray, Constants::$emailTaken);
        }

    }

    private function validatePasswords($pw,$pw2) {
        if($pw != $pw2) {
            array_push($this->errorArray, Constants::$passwordsDontMatch);
            return;
        }

        if(strlen($pw) < 5 || strlen($pw) > 25) {
            array_push($this->errorArray, Constants::$passwordLength);
            return;
}

    }

    public function getError($error) {
        if(in_array($error, $this->errorArray)) {
            return "<span class='errorMessage'>$error</span>";
        }
    }

}

?>
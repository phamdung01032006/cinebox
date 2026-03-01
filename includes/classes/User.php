<?php
class User {
    private $con, $sqlData;

    public function __construct($con, $username) {
        $this->con = $con;

        $query = $con->prepare("SELECT * FROM users WHERE username=:username");
        $query->bindValue(":username",$username);
        $query->execute();

        $this->sqlData = $query->fetch(PDO::FETCH_ASSOC);
    }

    public function getFirstName() {
        $this->sqlData["firstName"];
    }
    public function getLastName() {
        $this->sqlData["lastName"];
    }
    public function getEmail() {
        $this->sqlData["email"];
    }
}
?>
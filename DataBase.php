<?php
/**
 * Created by PhpStorm.
 * Users: Kon
 * Date: 3/18/2019
 * Time: 6:47 PM
 */
include_once "Users.php";
class DataBase {
    private $Servername;
    private $Username;
    private $Password;
    private $DataBaseName;

    function __construct()
    {
        $this -> Servername = "localhost";
        $this -> Username = "root";
        $this -> Password = "";
        $this -> DataBaseName = "MySQL";
    }
    function Connect()
    {
        $conn = new mysqli($this->Servername, $this -> Username, $this -> Password, $this -> DataBaseName);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
    }
    function FindUsername($Username)
    {
        if($this->Connect() == null)
            return false;
        else {
            $User = $this->Connect()->query("SELECT  username FROM users");
        }
    }
    function FindPassword($Password)
    {
        $User = $this->Connect()->query("SELECT  password FROM users");
    }
    function FindEmail($Email)
    {
        if($this->Connect() == null)
            return false;
        else {
            $User = $this->Connect()->query("SELECT  email FROM users");
        }
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: Matas
 * Date: 2019-03-16
 * Time: 17:39
 */
include_once "User.php";
class dbconnect
{
    private $server;
    private $username;
    private $password;
    private $dbname;
    private $charset;

    function __construct(){
        $this->server = "localhost";
        $this->username = "root";
        $this->password = "";
        $this->dbname = "myusers";
        $this->charset = "utf8mb4";
    }
    function connect()
    {
        try
        {
            $dataSourceName = "mysql:host=" . $this->server . ";dbname=" . $this->dbname . ";charset=" . $this->charset;
            $pdo = new PDO($dataSourceName, $this->username, $this->password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        }
        catch (PDOException $e)
        {
            echo "Connection failed: " . $e->getMessage();
        }
    }

    function getUser($username){
        $stmt =  $this->connect()->query("SELECT * FROM users WHERE username = '$username'");
            foreach ($stmt as $row) {
                return new User($row['username'], $row['hash'], $row['real_name'], $row['surname'], $row['email']);
            }
    }

    function duplicateUsername($username){
        $stmt = $this->connect()->query("SELECT username FROM users WHERE username = '$username'");
        if($stmt->rowCount() > 0)
            return true;
        else
            return false;
    }

    function duplicateEmail($email){
        $stmt = $this->connect()->query("SELECT username FROM users WHERE email = '$email'");
        if($stmt->rowCount() > 0)
            return true;
        else
            return false;
    }

    function addEvent($event){

    }
}
<?php

class DbMngr
{
    private $servername = 'localhost';
    private $username   = 'root';
    private $password   = 'root';
    private $dbname     = 'nd_database';

    public $conn;

    public function __construct()
    {
        $this->conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname);
    }

    public function disconnect()
    {
        mysqli_close($this->conn);
    }

    public function GetUserByEmail($email)
    {
        $sql = "SELECT * FROM USERS WHERE email='$email'";
        $result = $this->conn->query($sql);

        return $result->fetch_assoc();
    }

    public function GetUserByUsername($username)
    {
        $sql = "SELECT * FROM USERS WHERE username='$username'";
        $result = $this->conn->query($sql);

        return $result->fetch_assoc();
    }

    public function AddUser($username, $email, $passwordHash)
    {
        $sql = "INSERT INTO users (username, email, password_hash)
        VALUES ('$username', '$email', '$passwordHash')";

        return $this->conn->query($sql);
    }

    public function GetEventsByUserId($userId)
    {
        $sql = "SELECT * FROM EVENTS WHERE creator='$userId'";
        $result = $this->conn->query($sql);

        return $result->fetch_all();
    }
}

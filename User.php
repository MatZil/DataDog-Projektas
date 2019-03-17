<?php
/**
 * Created by PhpStorm.
 * User: Matas
 * Date: 2019-03-10
 * Time: 14:06
 */

class User
{
    public $username;
    public $hashedPw;
    public $name;
    public $surname;
    public $email;

    function __construct($username, $hash, $name, $surname, $email)
    {
        $this->username = $username;
        $this->hashedPw = $hash;
        $this->name = $name;
        $this->surname = $surname;
        $this->email = $email;
    }

    function pushToDb($currentDate)
    {
        $db = new dbconnect();
        $db->connect()->query("INSERT INTO users 
                                         VALUES('$this->name', '$this->surname', '$this->username', '$this->email', '$currentDate', '$this->hashedPw')");
    }

    function getEvents(){
        $db = new dbconnect();
        return $db->connect()->query("SELECT * FROM events WHERE fk_users_username = '$this->username'");
    }
}
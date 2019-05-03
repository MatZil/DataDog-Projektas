<?php
/**
 * Created by PhpStorm.
 * Users: Kon
 * Date: 3/18/2019
 * Time: 6:24 PM
 */
class Users {
    public $Username;
    public $Email;
    public $Password;

    public function __construct($Username, $Email, $Password)
    {
        $this->username= $Username;
        $this->email = $Email;
        $this->password = $Password;
    }
    public function TransferToDataBase()
    {
        $DataBase = new DataBase();
        $DataBase ->Connect()->query("INSERT INTO users VALUES($this->username, $this->email, $this->password)");
    }

}
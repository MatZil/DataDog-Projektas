<?php
/**
 * Created by PhpStorm.
 * User: Lukas
 * Date: 3/9/2019
 * Time: 6:40 PM
 */

class User
{
    public $email;
    public $password;

    public function __construct($Email, $Password)
    {
        $this->email = $Email;
        $this->password = $Password;
    }
}
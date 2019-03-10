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

    function __construct($uname, $hpw)
    {
        $this->username = $uname;
        $this->hashedPw = $hpw;
    }
}
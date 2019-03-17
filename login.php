<?php

include_once "User.php";
include_once "dbconnect.php";
$db = new dbconnect();
if(!isset($_SESSION))
    session_start();
if(isset($_POST['loginButton']))
{
    $correctName = false;
    $correctPw = false;
    $name = $_POST['usernameBox'];
    $pw = $_POST['passwordBox'];

    $user = $db->getUser($name);
    if(!is_null($user))
    {
        $correctName = true;
        if (password_verify($pw, $user->hashedPw)) {
            $correctPw = true;
        }
    }
    if($correctPw) {
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $name;
        header("Location:main.php");
        exit;
    }
    else if($correctName) {
        $message = "Incorrect Password! <br>";
        $_SESSION['username'] = $name;
    }
    else
        $message = "You are not a registered user!<br>";
    require_once("index.php");
    echo $message;
}


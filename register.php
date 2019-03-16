<?php
if(!isset($_SESSION))
    session_start();
include "User.php";

if(isset($_POST['registerButton']))
{
    $match = true;          //boolean variables for specifying the error in user's form - their value is changed if they don't pass the conditions.
    $validName = true;
    $validPw = true;

    $_SESSION['name'] = $_POST['nameBox'];      //saving everything in session so user wouldn't need to retype the correct data.
    $_SESSION['surname'] = $_POST['surnameBox'];
    $_SESSION['email'] = $_POST['emailBox'];

    if(strlen($_POST['usernameBox']) < 5)
        $validName = false;
    else
        $_SESSION['username'] = $_POST['usernameBox'];      //saving username in session only if it is valid.

    if ($_POST['passwordBox'] != $_POST['confirmBox'])
        $match = false;
    if(strlen($_POST['passwordBox']) < 8)
        $validPw = false;

    if($match && $validName && $validPw) //if everything is correct, send all the data into database.
    {
        $user = new User($_POST['usernameBox'], password_hash($_POST['passwordBox'], PASSWORD_DEFAULT), $_POST['nameBox'], $_POST['surnameBox'], $_POST['emailBox']);
        $user->pushToDb(date('y-m-d'));
    }
    else
    {
        require_once "registerForm.php";                                    //refreshing form to fill in data in session
        if(!$match)                                                     //printing out the errors
            echo "Your passwords do not match!<br>";
        if(!$validName)
            echo "Your username has to contain at least 5 characters!<br>";
        if(!$validPw)
            echo "Your password has to contain at least 8 characters!<br>";
    }

}

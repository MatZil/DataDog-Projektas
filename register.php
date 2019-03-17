<?php
if(!isset($_SESSION))
    session_start();
include_once "User.php";
include_once "dbconnect.php";
if(isset($_POST['registerButton']))
{
    $match = true;          //boolean variables for specifying the error in user's form - their value is changed if they don't pass the conditions.
    $validName = true;
    $uniqueName = true;
    $validPw = true;
    $uniqueEmail = true;

    $db = new dbconnect();

    $_SESSION['name'] = $_POST['nameBox'];      //saving everything in session so user wouldn't need to retype the correct data.
    $_SESSION['surname'] = $_POST['surnameBox'];
    $email = $_POST['emailBox'];
    $username = $_POST['usernameBox'];

    $_SESSION['username'] = "";
    if(strlen($username) < 5)
        $validName = false;
    else if($db->duplicateUsername($username))
        $uniqueName = false;
    else
        $_SESSION['username'] = $username;      //saving username in session only if it is valid.

    $_SESSION['email'] = "";
    if($db->duplicateEmail($email))
        $uniqueEmail = false;
    else
        $_SESSION['email'] = $email;      //saving email in session only if it is valid.

    if ($_POST['passwordBox'] != $_POST['confirmBox'])
        $match = false;
    if(strlen($_POST['passwordBox']) < 8)
        $validPw = false;


    if($match && $validName && $validPw && $uniqueName) //if everything is correct, send all the data into database.
    {
        $user = new User($username, password_hash($_POST['passwordBox'], PASSWORD_DEFAULT), $_POST['nameBox'], $_POST['surnameBox'], $_POST['emailBox']);
        $user->pushToDb(date('y-m-d'));
        $_SESSION['loggedin'] = true;
        header("Location:main.php");
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
        if(!$uniqueName)                                                     //printing out the errors
            echo "This username is already registered!<br>";
        if(!$uniqueEmail)                                                     //printing out the errors
            echo "This e-mail is already registered!<br>";
    }

}

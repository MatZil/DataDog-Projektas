<?php

include_once "Users.php";
include_once "DataBase.php";
$Database = new DataBase();
if(isset($_SESSION) == false)
    session_start();
if(isset($_POST['button']))
{

    if($Database->FindUsername($_POST['name']) == true && $Database->FindEmail($_POST['email']) ==true
        && $Database->FindPassword($_POST['password']))
    {
        header("Location:Welcome.php");
    }
    else
        header("Location:RegisterForm.php");
}




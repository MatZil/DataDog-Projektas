<?php

function LoginUser($userData)
{
    $_SESSION["LoggedIn"] = true;
    $_SESSION["UserId"] = $userData["id"];
    $_SESSION["UserName"] = $userData["username"];
}

function LogoutUser()
{
    session_unset();
    $_SESSION["LoggedIn"] = false;
}
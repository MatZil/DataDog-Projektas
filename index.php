
<?php
require_once("form.html");
require_once("User.php");
$u1 = new User("name1", password_hash("saugu", PASSWORD_DEFAULT));
$u2 = new User("name2", password_hash("saugu1", PASSWORD_DEFAULT));
$u3 = new User("name3", password_hash("saugu22", PASSWORD_DEFAULT));
$u4 = new User("name4", password_hash("sauguty1", PASSWORD_DEFAULT));
$u5 = new User("name5", password_hash("saugu", PASSWORD_DEFAULT));
$users = array($u1, $u2, $u3, $u4, $u5);

if(isset($_POST['loginButton']))
{
    $correctName = false;
    $correctPw = false;
    $name = $_POST['usernameBox'];
    $pw = $_POST['passwordBox'];
    foreach($users as $user)
    {
        if($name === $user->username)
        {
            $correctName = true;
            if(password_verify($pw, $user->hashedPw))
            {
                $correctPw = true;
                break;
            }
        }
    }
    if($correctPw)
        echo "You have logged in successfully!";
    elseif($correctName)
        echo "Incorrect Password!";
    else
        echo "You are not a registered user!";
}





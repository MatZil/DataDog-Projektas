<?php
/**
 * Created by PhpStorm.
 * User: Lukas
 * Date: 3/9/2019
 * Time: 6:59 PM
 */

include "User.php";

$user1 = new User('oof@ktu.lt', 'pass');
$user2 = new User('biggeroof@ktu.lt', 'pass2');
$user3 = new User('whatisthis@yikes.lt', 'pass3');
$users = array($user1, $user2, $user3);

if (isset($_POST['login-submit'])){

    $login = $_POST['email'];
    $pass = $_POST['pwd'];
    $status = false;

    if (empty($login) || empty($pass)){
        header("Location: index.php?error=EmptyFields");
        exit();
    }
    else {
        foreach ($users as $user){
            if ($login === $user->email and $pass === $user->password){
                $status = true;
                break;
            }
            else {
                $status = false;
            }
        }
    }

    if ($status === false){
        header('Location: index.php?error=LoginFailed');
    }
}
else {
    header("Location: index.php");
    exit();
}
?>

<html>
<body>
<h1>
    You have logged in!
</h1>
</body>
</html>
<?php
/**
 * Created by PhpStorm.
 * User: Lukas
 * Date: 3/9/2019
 * Time: 6:59 PM
 */

include "User.php";

$user1 = new User('oof@ktu.lt', password_hash('pass', PASSWORD_DEFAULT));
$user2 = new User('biggeroof@ktu.lt', password_hash('pass2', PASSWORD_DEFAULT));
$user3 = new User('whatisthis@yikes.lt', password_hash('pass3', PASSWORD_DEFAULT));
$users = array($user1, $user2, $user3);
session_start();

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
            if ($login === $user->email and password_verify($pass, $user->password)){
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
    else {
        $_SESSION['isLoggedIn'] = true;
    }
}
else {
    header("Location: index.php");
    exit();
}
?>

<html>
<body>
<h1>You have logged in!</h1>
<form action="index.php" method="post">
    <button type="submit" name="logout-submit">Logout</button>
</form>
</body>
</html>

<?php
if (isset($_POST['logout-submit'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}
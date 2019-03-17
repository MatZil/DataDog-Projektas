<?php
/**
 * Created by PhpStorm.
 * User: Lukas
 * Date: 3/9/2019
 * Time: 6:59 PM
 */

include "User.php";
include_once "includes/dbh.inc.php";

//$user1 = new User('oof@ktu.lt', password_hash('pass', PASSWORD_DEFAULT));
//$user2 = new User('biggeroof@ktu.lt', password_hash('pass2', PASSWORD_DEFAULT));
//$user3 = new User('whatisthis@yikes.lt', password_hash('pass3', PASSWORD_DEFAULT));
//$users = array($user1, $user2, $user3);
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
        $qry = mysqli_query($conn, "SELECT * FROM users WHERE email='$login'");
        $row = mysqli_fetch_array($qry);

        if ($login === $row['email'] and password_verify($pass, $row['password_hash'])){
            $status = true;
            $uid=$row['id'];
        }
    }
    if ($status === false){
        header('Location: index.php?error=LoginFailed');
    }
    elseif ($status === true) {
        $_SESSION['isLoggedIn'] = true;
        $qry = mysqli_query($conn, "SELECT * FROM events WHERE users_id='$uid'");
        //Imagine a proper table here:
        echo '<font size = "5">Your events: </font><br>';
        echo '<strong>'.'Event name' .'</strong>'. str_repeat('&nbsp;', 10) .'<strong>'.'Date' .'</strong>'. '<br>';
        while($row = mysqli_fetch_array($qry)) {
            echo $row['name'] . str_repeat('&nbsp;', 2);
            echo $row['date'];
            echo '<br>';
        }
        echo '<br>';
    }
}
else {
    header("Location: index.php");
    exit();
}
?>

<html>
<body>
<form action="index.php" method="post">
    <button type="submit" name="logout-submit">Logout</button>
</form>
<footer style="color:green"><font size="2">You have logged in!</font></footer>
<!--<a href="index.php">Main menu</a>-->
</body>
</html>

<?php
if (isset($_POST['logout-submit'])) {
    session_destroy();
    $conn->close();
    header("Location: index.php");
    exit();
}
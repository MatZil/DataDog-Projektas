<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Login Website</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>

<body>

    <?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (isset($_SESSION["LoggedIn"]) && $_SESSION["LoggedIn"]) {
        echo "<h1>Hi user Id: " . $_SESSION["UserId"] . "</h1>";
        echo '<form action="login.php" method="get" style="display:inline-block;margin-left:50px">
            <input type="submit" name="action" value="Logout"></form>';
    } else {
        echo "<h1>Login Form</h1>";
        require_once "resources/LoginForm.html";
    }
    ?>

</body>

</html>
<!DOCTYPE html>

<html>

<head>
    <meta charset="UTF-8">
    <title>Cool Login Form</title>
</head>

<body>
<form action="login.php" method="POST">

    Username:
    &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
    Password: <br>
    <input name="usernameBox" size="15"  style="height: 19px;" type="text" required value="<?php if(isset($_SESSION["username"])) echo $_SESSION["username"]; ?>">
    &nbsp; &nbsp; &nbsp;
    <input name="passwordBox" size="15" style="height: 19px;"  type="password" required>
    &nbsp; &nbsp; &nbsp;
    <input type="submit" name="registerButton" value="Register">

    <br>
    <br>

    <input type="submit" name="loginButton" value="Login" >
</form>
</body>
</html>






<!DOCTYPE html>

<html>

<head>
    <meta charset="UTF-8">
    <title>Cool Register Form</title>
    <style>
        .asterix{
            color: red;
            display: inline;
        }
    </style>
</head>

<body>
<form action="register.php" method="POST">

    Name<br>
    <input name="nameBox" class="required" size="15"  style="height: 19px;" type="text" required value="<?php if(isset($_SESSION['name'])) echo $_SESSION['name']; ?>">
    <p class="asterix">*</p><br><br>
    Surname<br>
    <input name="surnameBox" size="15"  style="height: 19px;" type="text" value="<?php if(isset($_SESSION['surname'])) echo $_SESSION['surname']; ?>"><br><br>
    Username<br>
    <input name="usernameBox" size="15"  style="height: 19px;" type="text" required value="<?php if(isset($_SESSION['username'])) echo $_SESSION['username']; ?>">
    <p class="asterix">*</p><br><br>
    Password<br>
    <input name="passwordBox" size="15" style="height: 19px;"  type="password" required>
    <p class="asterix">*</p><br><br>
    Confirm Password<br>
    <input name="confirmBox" size="15" style="height: 19px;"  type="password" required>
    <p class="asterix">*</p><br><br>
    E-mail<br>
    <input name="emailBox" size="15" style="height: 19px;"  type="email" required value="<?php if(isset($_SESSION['email'])) echo $_SESSION['email']; ?>">
    <p class="asterix">*</p><br><br>

    <input type="submit" name="registerButton" value="Register"><br><br>
</form>
</body>
</html>



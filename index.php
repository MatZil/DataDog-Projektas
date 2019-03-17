<?php
/**
 * Created by PhpStorm.
 * User: Lukas
 * Date: 3/9/2019
 * Time: 6:47 PM
 */
session_start();
?>

<html>
    <body>
        <form action="login.php" method="post">
            <input type="text" name="email" placeholder="E-mail">
            <input type="password" name="pwd" placeholder="Password">
            <button type="submit" name="login-submit">Login</button>
        </form>
        <?php

        if (isset($_GET['error'])){
            if ($_GET['error'] === 'LoginFailed')
                echo '<p style="color:red;">Email or password is incorrect</p>';
            elseif ($_GET['error'] === 'EmptyFields')
                echo '<p style="color:red;">One or more fields are empty</p>';
        }
        ?>
    </body>
</html>

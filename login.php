<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Login Website</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>

<body>

    <?php
    require_once "resources/User.php";

    $user1 = new User("1", "email1@domain.com", password_hash("pas1", PASSWORD_DEFAULT));
    $user2 = new User("2", "email2@domain.com", password_hash("pas2", PASSWORD_DEFAULT));
    $user3 = new User("3", "email3@domain.com", password_hash("pas3", PASSWORD_DEFAULT));
    $user4 = new User("4", "email4@domain.com", password_hash("pas4", PASSWORD_DEFAULT));
    $users = [$user1, $user2, $user3, $user4];


    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (isset($_GET["action"]) && $_GET["action"] === "Logout") {
        echo "<h1>Logout succeed</h1>";
        LogoutUser();
        goto SCRIPT_END;
    }

    if (!isset($_POST["email"]) || !isset($_POST["password"])) {
        echo "<h1>Error: invalid request</h1>";
        goto SCRIPT_END;
    }

    foreach ($users as $user) {
        if ($_POST["email"] === $user->Email && password_verify($_POST["password"], $user->PasswordHash)) {
            LoginUser($user);
            break;
        }
    }
    
    if ($_SESSION["LoggedIn"]) {
        echo "<h1>login succeed</h1>";
    } else {
        echo "<h1>login information was incorrect</h1>";        
    }

    //////////////////////////////////////////////////////////////////////
    function LoginUser($user)
    {
        $_SESSION["LoggedIn"] = true;
        $_SESSION["UserId"] = $user->Id;
    }

    function LogoutUser()
    {
        $_SESSION["LoggedIn"] = false;
        unset($_SESSION["UserId"]);
    }
    //////////////////////////////////////////////////////////////////////

    SCRIPT_END:
    ?>

    <h4>Redirecting to main page...</h4>
    <script>
        setTimeout(function() {
            window.location.href = "/";
        }, 3000);
    </script>

</body>

</html> 
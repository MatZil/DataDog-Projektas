<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Login Website</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" media="screen" href="css/main.css">
</head>

<body>

    <?php
    require_once "resources/Utils.php";
    require_once "resources/DbMngr.php";


    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (isset($_GET["action"]) && $_GET["action"] === "Logout") {
        echo "<h1>Logout succeed</h1>";
        LogoutUser();
        goto SCRIPT_END;
    }

    if (!isset($_POST["email"]) || !isset($_POST["password"])) {
        echo '<h1 class="err">Error: invalid request</h1>';
        goto SCRIPT_END;
    }


    $dbm = new DbMngr();
    if ($dbm->conn->connect_error) {
        echo '<h1 class="err">Error: ' . $dbm->conn->connect_error . '</h1>';
        goto SCRIPT_END;
    }

    $userData = $dbm->GetUserByEmail($_POST["email"]);
    if ($userData !== null && password_verify($_POST["password"], $userData["password_hash"])) {
        LoginUser($userData);
    }

    $dbm->disconnect();


    if (isset($_SESSION["LoggedIn"]) && $_SESSION["LoggedIn"]) {
        echo "<h1>login succeed</h1>";
    } else {
        echo "<h1>login information was incorrect</h1>";
    }

    SCRIPT_END:
    ?>

    <h4>Redirecting to main page in <span id="secCount"></span> Seconds...</h4>

    <script>
        var secs = 3;
        var secCount = document.getElementById("secCount");
        secCount.innerHTML = secs;

        setInterval(() => {
            secCount.innerHTML = --secs;
        }, 1000);

        setTimeout(() => {
            window.location.href = "/";
        }, secs * 1000);
    </script>

</body>

</html>
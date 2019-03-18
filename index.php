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
    require_once "resources/DbMngr.php";

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (isset($_SESSION["LoggedIn"]) && $_SESSION["LoggedIn"]) {
        echo "<h1>Welcome " . $_SESSION["UserName"] . "</h1>";
        echo '<form action="login.php" method="get">
            <input type="submit" name="action" value="Logout"></form>';
        echo "<br>";

        $dbm = new DbMngr();
        if ($dbm->conn->connect_error) {
            echo '<h1 class="err">Error: ' . $dbm->conn->connect_error . '</h1>';
            goto SCRIPT_END;
        }

        $eventData = $dbm->GetEventsByUserId($_SESSION["UserId"]);
        foreach ($eventData as $event) {
            echo "<h3>Title: " . $event[1] . " Description: " . $event[2] . " Date: " . $event[3] . " Address: " . $event[4] . "</h3>";
        }

        $dbm->disconnect();
        SCRIPT_END:
    } else {
        echo "<h1>Login Form</h1>";
        require_once "resources/LoginForm.html";
    }
    ?>

</body>

</html>
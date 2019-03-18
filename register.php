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

    $errMessage = "";

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (!isset($_POST["username"]) || !isset($_POST["email"]) || !isset($_POST["password"])) {
            $errMessage = $errMessage . '<h2 class="err">Error: invalid request</h2>';
            goto SCRIPT_END;
        }
    } else {
        goto SCRIPT_END;
    }

    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    if (empty($username)) {
        $errMessage = $errMessage . '<p class="err">Username is required</p>';
    } else {
        if (!preg_match("/^[a-zA-Z0-9]*$/", $username)) {
            $errMessage = $errMessage . '<p class="err">Username must contain only letters and numbers</p>';
        }
    }

    if (empty($email)) {
        $errMessage = $errMessage . '<p class="err">Email is required</p>';
    } else {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errMessage = $errMessage . '<p class="err">Invalid email format</p>';
        }
    }

    if (empty($password)) {
        $errMessage = $errMessage . '<p class="err">Password is required</p>';
    } else {
        if (!preg_match("/^[a-zA-Z0-9]*$/", $password)) {
            $errMessage = $errMessage . '<p class="err">Password must contain only letters and numbers</p>';
        }
    }

    if ($errMessage !== "") {
        goto SCRIPT_END;
    }


    $dbm = new DbMngr();
    if ($dbm->conn->connect_error) {
        $errMessage = $errMessage . '<h2 class="err">Error: ' . $dbm->conn->connect_error . '</h2>';
        goto SCRIPT_END;
    }

    if ($dbm->GetUserByUsername($username) !== null) {
        $errMessage = $errMessage . '<p class="err">Username is taken</p>';
    }

    if ($dbm->GetUserByEmail($email) !== null) {
        $errMessage = $errMessage . '<p class="err">Email is registered</p>';
    }

    if ($errMessage !== "") {
        $dbm->disconnect();
        goto SCRIPT_END;
    }

    $result = $dbm->AddUser($username, $email, password_hash($password, PASSWORD_DEFAULT));
    echo var_dump($result);
    if ($result === true) {
        $userData = $dbm->GetUserByEmail($email);
        if (password_verify($password, $userData["password_hash"])) {
            LoginUser($userData);
        }
    }
    $dbm->disconnect();

    SCRIPT_END:
    ?>

    <?php

    if (isset($_SESSION["LoggedIn"]) && $_SESSION["LoggedIn"]) {
        header("Location:/");
    }
    echo "<h1>Registration Form</h1>";
    echo $errMessage;
    include_once "resources/RegisterForm.html";
    ?>

</body>

</ht ml>
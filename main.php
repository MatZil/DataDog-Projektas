<!DOCTYPE html>

<html>

<head>
    <meta charset="UTF-8">
    <title>Cool Home</title>
</head>

<body>
<form>
    <?php
    include_once "dbconnect.php";
    session_start();
    $db = new dbconnect();
    if($_SESSION['loggedin']) {
        $user = $db->getUser($_SESSION['username']);
        $events = $user->getEvents();
        if($events->rowCount() > 0) {
            echo "Info about your events:<br><br>";
            $counter = 1;
            foreach ($events as $event) {
                echo "Event number " . $counter++ . ":<br><br>";
                echo "Name: " . $event['name'] . "<br>";
                echo "Description: " . $event['description'] . "<br>";
                echo "Created: " . $event['created'] . "<br>";
                echo "Price: " . $event['price'] . " eur<br>";
                echo "Address: " . $event['address'] . "<br><br><br>";
            }
        }
        else{
            echo "You have not created any events just yet!";
        }
    }
    ?>



</form>

</body>
</html>





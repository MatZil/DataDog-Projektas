
<!DOCTYPE html>

<html>

<head>
    <meta charset="UTF-8">
    <title>Cool New Event Form</title>
    <style>
        .asterix{
            color: red;
            display: inline;
        }
    </style>
</head>

<body>
<form action="newEventForm.php" method="POST">

    Name<br>
    <input name="nameBox" class="required" size="15"  style="height: 19px;" type="text" required value="<?php if(isset($_SESSION['name'])) echo $_SESSION['name']; ?>">
    <p class="asterix">*</p><br><br>
    Description<br>
    <input name="descriptionBox" size="15"  style="height: 19px;" type="text" required value="<?php if(isset($_SESSION['surname'])) echo $_SESSION['surname']; ?>">
    <p class="asterix">*</p><br><br>
    Price<br>
    <input name="priceBox" size="15"  style="height: 19px;" type="number" required value="<?php if(isset($_SESSION['username'])) echo $_SESSION['username']; ?>">
    <p class="asterix">*</p><br><br>
    Address<br>
    <input name="addressBox" size="15" style="height: 19px;"  type="text" required>
    <p class="asterix">*</p><br><br>
    Date<br>
    <input name="dateBox" size="15" style="height: 19px;"  type="date" required>
    <p class="asterix">*</p><br><br>

    <input type="submit" name="newEventButton" value="Create Event"><br><br>
</form>
</body>
</html>
<?php
include_once "Event.php";
if(isset($_POST['newEventButton'])){
    session_start();
    $event = new Event($_POST['nameBox'], $_POST['descriptionBox'], $_POST['priceBox'], $_POST['addressBox'], $_POST['dateBox']);
    $event->pushToDb($_SESSION['username']);
    header("Location:main.php");
}
?>



<?php
/**
 * Created by PhpStorm.
 * Users: Kon
 * Date: 3/18/2019
 * Time: 9:27 PM
 */
include_once "Users.php";
include_once "DataBase.php";
if(isset($_POST['Rbutton']))
{
    $Database = new DataBase();
    if($Database->FindUsername($_POST['Rname']) != true && $Database->FindEmail($_POST['Remail']) != true) {
        $User = new Users($_POST['Rname'], $_POST['Remail'], $_POST['Rpassword']);
        $User->TransferToDataBase();
        header("Location:Login.php");
    }
}

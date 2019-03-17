<?php
/**
 * Created by PhpStorm.
 * User: Matas
 * Date: 2019-03-17
 * Time: 19:02
 */
include_once "dbconnect.php";
class Event
{
    public $name;
    public $description;
    public $created;
    public $price;
    public $address;
    public $date;

    function __construct($name, $description, $price, $address, $date)
    {
        $this->name = $name;
        $this->description = $description;
        $this->created = date('y-m-d');
        $this->price = $price;
        $this->address = $address;
        $this->date = $date;
    }

    function pushToDb($username)
    {
        $db = new dbconnect();
        $db->connect()->query("INSERT INTO events 
                                         VALUES(DEFAULT, '$this->name', '$this->description', '$this->created', '$this->price', '$this->address', '$this->date', '$username')");
    }
}
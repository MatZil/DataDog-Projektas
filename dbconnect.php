<?php
/**
 * Created by PhpStorm.
 * User: Matas
 * Date: 2019-03-16
 * Time: 17:39
 */

class dbconnect
{
    private $server;
    private $username;
    private $password;
    private $dbname;
    private $charset;

    function __construct($host, $name, $pw, $db, $charset)
    {
        $this->server = $host;
        $this->username = $name;
        $this->password = $pw;
        $this->dbname = $db;
        $this->charset = $charset;
    }

    function connect()
    {
        try
        {
            $dataSourceName = "mysql:host=" . $this->server . ";dbname=" . $this->dbname . ";charset=" . $this->charset;
            $pdo = new PDO($dataSourceName, $this->username, $this->password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        }
        catch (PDOException $e)
        {
            echo "Connection failed: " . $e->getMessage();
        }
    }
}
<?php
error_reporting(0); // turns off all error reporting
$hn = 'localhost';
$un = 'scmuser';  // Change this to the username you use for your DB
$pw = 'p123456d'; // Change this to the password you use for your DB
$db = 'santa_clara_menus';
$port = 3306;     // Change this to the port you use for your DB

// Connect to DB
$conn = new mysqli($hn, $un, $pw, $db, $port);
if ($conn->connect_error)
    die($conn->connect_error);
?>
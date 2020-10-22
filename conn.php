<?php
$hn = 'localhost';
$un = 'scmuser';
$pw = 'p123456d';
$db = 'santa_clara_menus';
$port = 3306;

// Connect to DB
$conn = new mysqli($hn, $un, $pw, $db, $port);
if ($conn->connect_error) 
    die($conn->connect_error);
?>
<?php
require 'conn.php';
require 'session.php';
require 'auth.php';
// invoke cookie to an hour ago
setcookie('scd-secret-cookie', '', time() - 3600,'/');
$_SESSION['authenticated'] = false;
// redirect to home page
header("location: index.php");
?>
<?php
require 'includes/conn.php';
require 'includes/session.php';
require 'includes/auth.php';
// invoke cookie to an hour ago
setcookie('scd-secret-cookie', '', time() - 3600,'/');
$_SESSION['authenticated'] = false;
// redirect to home page
header("location: index.php");
?>
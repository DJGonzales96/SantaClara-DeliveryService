<?php

require 'includes/conn.php';
require 'includes/session.php';
require 'includes/auth.php';
include 'db.php';

$isRestaurant = dbUserGetByUsername($_SESSION["username"])[4];
if($isRestaurant)
    header("location: restaurant.php");
elseif (!$isRestaurant)
    header("location: driver.php");
?>
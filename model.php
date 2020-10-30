<?php
//TODO: Functions need to be tested
require 'includes/conn.php';
require 'includes/session.php';

$user_id = $_SESSION["user_id"];
$isRestaurant = $_SESSION["isRestaurant"];

// We determine if the user is a driver or restaurant here
// The database will need to know this for future queries
$id_type;
if($isRestaurant == true)
  $id_type = "restaurant_id";
elseif($isRestaurant == false)
  $id_type = "driver_id";
else
{
  echo("Error: unauthorized user type: $isRestaurant, are you sure you're signed in?");
}

function getUserInformation($conn, String $user_id)
{
  $query = "SELECT * FROM user WHERE user_id = '$user_id'";
  $result = $conn->query($query);
  if(!$result) die($conn->error);

  $info = $result->fetch_array(MYSQLI_NUM);
  $result->close();

  return $info;
}

function updateLocation($conn, String $loc_id, String $newLat, String $newLong)
{
  $query = "UPDATE location SET latitude = '$newLat', longitude = '$newLong' WHERE loc_id = '$loc_id'";
  $result = $conn->query($query);
  if(!$result) die($conn->error);
}

function getLocation($conn, String $loc_id)
{
  $query = "SELECT * FROM location WHERE loc_id = '$loc_id'";
  $result = $conn->query($query);
  if(!$result) die($conn->error);

  $row = $result->fetch_array(MYSQLI_NUM);
  $result->close();

  $currLat = $row[1];
  $currLong = $row[2];
  $currAddr = $row[3];

  return array($currLat, $currLong, $currAddr);
}


function getCurrentDeliveries($conn, String $user_id)
{
  // We only want deliveries that are active
  $query = "SELECT * FROM transaction WHERE $id_type = '$user_id' AND active = true";
  $result = $conn->query($query);
  if(!$result) die($conn->error);

  $deliveryInfo = $result->fetch_array(MYSQLI_NUM);
  $result->close();

  return $deliveryInfo;
}

function updateDeliveries()
{

}

//TODO: need google mapping functionality
function calculateCost(String $start, String $end)
{

}

//TODO: Need this functionality eventually
function requestDelivery() {}
function acceptDelivery() {}
function driverDelivered() {}

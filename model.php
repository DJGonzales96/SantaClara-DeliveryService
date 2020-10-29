<?php
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

function getUserInformation()
{

}

function updateLocation(String $newLoc)
{
  $query = "UPDATE location SET address = '$newLoc' WHERE user_id = '$user_id'";
  $result = $conn->query($query);
  if(!$result) die($conn->error);
}

function getLocation()
{
  $query = "SELECT address FROM location WHERE user_id = '$user_id'";
  $result = $conn->query($query);
  if(!$result) die($conn->error);

  $row = $result->fetch_array(MYSQLI_NUM);
  $result->close();

  $currentLoc = $row[0];
  return $currentLoc;
}

//TODO: we need a way to determine what deliveries are active or expired
function getCurrentDeliveries(String $user_id)
{

}

function updateDeliveries()
{

}

//TODO: need google mapping functionality
function calculateCost(String $start, String $end)
{

}

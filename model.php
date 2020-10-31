<?php
require_once 'includes/conn.php';
require_once 'includes/session.php';
require_once 'includes/auth.php';
include 'maps.php';

// prevent unauthorized access
if ($_SESSION['authenticated'] != true || $_SESSION["username"] == NULL)
    die("Not logged in");


function getUserInformation(String $user_id)
{
  global $conn;
  $query = "SELECT * FROM user WHERE username = '$user_id'"; // changed to username TEMPORARILY
  $result = $conn->query($query);
  if(!$result) die($conn->error);
  $info = $result->fetch_array(MYSQLI_NUM);
  $result->close();
  echo getGeocode("34655 Skylark Dr."); //->{"lat"}
  return $info;
}

function updateLocation(String $loc_id, String $newLat, String $newLong)
{
  global $conn;
  $query = "UPDATE location SET latitude = '$newLat', longitude = '$newLong' WHERE loc_id = '$loc_id'";
  $result = $conn->query($query);
  if(!$result) die($conn->error);
}

function getLocation(String $loc_id)
{
  global $conn;
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


function getCurrentDeliveries(String $user_id)
{
  global $conn;
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
?>
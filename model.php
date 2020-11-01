<?php
require_once 'includes/conn.php';
require_once 'includes/session.php';
require_once 'includes/auth.php';
include 'maps.php';

// prevent unauthorized access
if ($_SESSION['authenticated'] != true || $_SESSION["username"] == NULL)
    die("Not logged in");


function getUserInformation(String $username)
{
  global $conn;
  $query = "SELECT * FROM user WHERE username = '$username'"; //
  $result = $conn->query($query);
  if(!$result) die($conn->error);
  $info = $result->fetch_array(MYSQLI_NUM);
  $result->close();
  return $info;
}

function setLocationTransaction(String $user_id, String $newLat, String $newLong, String $newAddr)
{
  global $conn;
  // notice user has a t_id to show CURRENT location
  // t_id and loc_id is AUTO-INCREMENT
  // - this needs to be changed accordingly
  // INSERT to transactions - a location update is a transaction with TIME_STAMP
  // INSERT to location ....
  // previous code:
//  $query = "UPDATE location SET latitude = '$newLat', longitude = '$newLong' WHERE loc_id = '$loc_id'"; // CHANGE
//  // get the new loc_id and set it as the user's location
//  $result = $conn->query($query);
//  if(!$result) die($conn->error);
}


// JUST AN EXAMPLE OF GETTING SOMETHING FROM MAPS API - REMOVE LATER
function getFromMapsApiDemo($friendlyName){
    return getGeocode($friendlyName);
}


function getLocationByTid($t_id){

}

// helper function
function getLocationById(String $loc_id)
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
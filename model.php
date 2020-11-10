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

function setLocationTransaction(String $username, String $newLat, String $newLong, String $newAddr)
{
  global $conn;
  // notice user has a t_id to show CURRENT location
  // t_id and loc_id is AUTO-INCREMENT
  // - this needs to be changed accordingly
  // INSERT to transactions - a location update is a transaction with TIME_STAMP
  // INSERT to location ....
  $user_id = getUserInformation($username)[0];

  // Create a new location
  $query = "INSERT INTO location(lat, lon, address) VALUES('$newLat', '$newLong', '$newAddr')";
  $result = $conn->query($query);
  if(!$result) die($conn->error);

  $new_loc_id = $conn->insert_id; // the location id is the most recently inserted
  $t_type = "loc update";
  $start_location = $new_loc_id;
  // Create a new transaction
  $query = "INSERT INTO transaction(t_type, restaurant_id, start_loc) VALUES('$t_type', '$user_id', '$start_location')";
  $result = $conn->query($query);
  if(!$result) die($conn->error);
}

function updateDriverLocation(String $username, String $newLat, String $newLong, String $newAddr)
{
  global $conn;
  $t_id = getUserInformation($username)[5];
  $loc_to_update = getLocationByTid($t_id);

  // Update location
  $query = "UPDATE location SET lat = '$newLat', lon = '$newLong', address = '$newAddr' WHERE loc_id = '$loc_to_update'";
  $result = $conn->query($query);
  if(!$result) die($conn->error);
}

function RestaurantUpdateAddress(String $user_id, String $newLat, String $newLong, String $newAddr)
{
  global $conn;
  // Create new location and transaction
  setLocationTransaction($user_id, $newLat, $newLong, $newAddr);
  $new_t_id = $conn->insert_id;

  // Restaurant's t_id is updated to the new values
  $query = "UPDATE user SET t_id = '$new_t_id' WHERE user_id = '$user_id'";
  $result = $conn->query($query);
  if(!$result) die($conn->error);
}

// JUST AN EXAMPLE OF GETTING SOMETHING FROM MAPS API - REMOVE LATER
function getFromMapsApiDemo($friendlyName){
    return getGeocode($friendlyName);
}


function getLocationByTid($t_id){
  global $conn;
  $query = "SELECT start_loc FROM transaction WHERE t_id = '$t_id'";
  $result = $conn->query($query);
  if(!$result) die($conn->error);

  $row = $result->fetch_array(MYSQLI_NUM);
  $loc = $row[4];

  return $loc;
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
function requestDelivery()
{

}
function acceptDelivery() {

}
function driverDelivered(String $user_id) {

}
?>

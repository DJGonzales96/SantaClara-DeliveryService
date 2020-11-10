<?php

/*
//TODO: need google mapping functionality
function calculateCost(String $start, String $end) {}

//TODO: Need this functionality eventually
function updateDeliveries() {}
function requestDelivery(){}
function acceptDelivery() {}
function driverDelivered(String $user_id) {}
*/


require_once 'includes/conn.php';
require_once 'includes/session.php';
require_once 'includes/auth.php';
include 'db.php';
include 'maps.php';

// prevent unauthorized access
if ($_SESSION['authenticated'] != true || $_SESSION["username"] == NULL)
  die("{'status':'STATUS_ERROR','error':'Not logged in'}");


// JUST AN EXAMPLE OF GETTING SOMETHING FROM MAPS API - REMOVE LATER
function getFromMapsApiDemo($friendlyName){
  return getGeocode($friendlyName);
}

function getUserInformation(String $username) {
  global $conn;
  $query = "SELECT * FROM user WHERE username = '$username'"; //
  $result = $conn->query($query);
  if(!$result) die($conn->error);
  $info = $result->fetch_array(MYSQLI_NUM);
  $result->close();
  return $info;
}

function updateLocation(String $user_id, String $newLat, String $newLong, String $newAddr) {
  $new_loc_id = insertNewLocationToDb($newLat, $newLong, $newAddr);
  $t_type = "loc update";
  $new_t_id = insertNewTransactionToDb($user_id, $new_loc_id, $t_type);
  updateUserTransactionInDb($user_id, $new_t_id);
}

function getCurrentUserLocationByTid($t_id){
  return getLocationByLocId(getLocationIdByTid($t_id));
}

//function updateDriverLocation(String $username, String $newLat, String $newLong, String $newAddr)
//{
//  global $conn;
//  $t_id = getUserInformation($username)[5];
//  $loc_to_update = getLocationByTid($t_id);
//
//  // Update location
//  $query = "UPDATE location SET lat = '$newLat', lon = '$newLong', address = '$newAddr' WHERE loc_id = '$loc_to_update'";
//  $result = $conn->query($query);
//  if(!$result) die($conn->error);
//}

//function RestaurantUpdateAddress(String $user_id, String $newLat, String $newLong, String $newAddr)
//{
//  global $conn;
//  // Create new location and transaction
//  setLocationTransaction($user_id, $newLat, $newLong, $newAddr);
//  $new_t_id = $conn->insert_id;
//
//  // Restaurant's t_id is updated to the new values
//  $query = "UPDATE user SET t_id = '$new_t_id' WHERE user_id = '$user_id'";
//  $result = $conn->query($query);
//  if(!$result) die($conn->error);
//}





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

?>

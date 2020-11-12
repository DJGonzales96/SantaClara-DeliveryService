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

function getUserInformation($username) {
  global $conn;
  $query = "SELECT * FROM user WHERE username = '$username'"; //
  $result = $conn->query($query);
  if(!$result) die($conn->error);
  $info = $result->fetch_array(MYSQLI_NUM);
  $result->close();
  return $info;
}

function updateLocation($user_id, $newLat, $newLong, $newAddr) {
  $new_loc_id = insertNewLocationToDb($newLat, $newLong, $newAddr);
  $t_type = "loc_update";
  $new_t_id = insertNewTransactionToDb($user_id, $new_loc_id, $t_type);
  updateUserTransactionInDb($user_id, $new_t_id);
}

function restaurantCreateNewDelivery($user_id, $address, $food) {
  //TODO: Need maps API for functionality
  // getLatLong($addr)
  // $in_range = isDestinationWithin40($Lat, $Long)
  // $nearby_driver_ids = getDriversNearby($lat,$long)
  // if($in_range && $nearby_driver_ids > 0)
    // for now we hard code values and assume every delivery request is valid
      $newLat = 1.1;
      $newLong = 2.2;
      $newAddr = "1 Washington Square SJ";
      $t_type = "delivery_req";
      $t_status = "pending";
      $new_loc_id = insertNewLocationToDb($newLat, $newLong, $newAddr);
      $new_t_id = insertNewRestTransactionToDb($user_id, $t_type, $new_loc_id, $t_status);

      //TODO: notify drivers of pending transactions

  // else (not succeed case)
      $t_status = "failed";
}
//TODO: Using array of drivers, for each driver set his t_status to incoming_call
function notifyNearbyDrivers() {
  retur
}

//TODO: Doesnt include drivers with >1 deliveries
function getValidDrivers() {
  switch($num_deliveries) {
    case 0:
      assignDriverToDelivery($user_id, $t_id);
      return ClientStatus::SERVICING_1;
    case 1:
      assignDriverToDelivery($user_id, $t_id);
      return ClientStatus::SERVICING_2;
    case 2:
      //TODO: Driver is full on deliveries! Cannot queue more
      return ClientStatus::SERVICING_2;
  }
}

function checkDriverLocations() {

  //TODO: Need Maps API for functionality   @Ilan
  // getSurroundingDriversArray($)
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
//

//TODO: Own the request,
function driverAcceptDelivery($user_id, $t_id) {
  $active_deliveries = getCurrentDriverDeliveries($user_id);
  assignDriverToDelivery($user_id, $t_id);
  //TODO: Cancel notification for all other drivers, they are IDLE now
  // Check how many active deliveries driver is servicing
  $num_deliveries = count($active_deliveries);

}

function assignDriverToDelivery($user_id, $t_id) {
  updateUserTransactionInDb($user_id, $t_id);   // Assign driver to this transaction TODO: be sure not to lose location
  updateTransactionSecondaryId($t_id, $user_id);// Assign transaction it's correct driver
  $t_status = "in_progress";
  updateTransactionStatus($t_status);         // Mark the transaction as an in_progress delivery
}

// Gets current in progress deliveries of a driver
function getCurrentDriverDeliveries($user_id)
{
  global $conn;
  // We only want deliveries that are active
  $query = "SELECT * FROM transaction WHERE 'secondary_user_id' = '$user_id' AND t_status = 'in_progress'";
  $result = $conn->query($query);
  if(!$result) die($conn->error);

  $deliveryInfo = $result->fetch_array(MYSQLI_NUM);
  $result->close();

  return $deliveryInfo;
}

// Gets current pending deliveries of a restaurant
//TODO: probably need to grab in-progress as well? AND status = open
function getCurrentRestaurantDeliveries($user_id)
{
  global $conn;
  // We only want deliveries that are active
  $query = "SELECT * FROM transaction WHERE 'primary_user_id' = '$user_id' AND t_status = 'pending'";
  $result = $conn->query($query);
  if(!$result) die($conn->error);

  $deliveryInfo = $result->fetch_array(MYSQLI_NUM);
  $result->close();

  return $deliveryInfo;
}

?>

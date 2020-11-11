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
  // getLatLong($addr)
  // $in_range = isDestinationWithin40($Lat, $Long)
  // $nearby_driver_ids = getDriversNearby($lat,$long)
  // if($in_range && $nearby_driver_ids > 0)
      $new_loc_id = insertNewLocationToDb($newLat, $newLong, $newAddr);
      $new_t_id = insertNewTransactionToDb($user_id, $new_loc);
      $t_type = "delivery_req";
      $t_status = "pending";
      //TODO: notify drivers of pending transactions

  // else (not succeed case)
      $t_status = "failed";
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

//TODO: rework this logic for syncronization from Restaurant
function driverAcceptDelivery() {
  $active_deliveries = getCurrentDeliveries($username);

  // Check how many active deliveries driver is servicing
  $num_deliveries = count($active_deliveries);
  switch($num_deliveries) {
    case 0:
      //TODO: create new delivery()
        // insert new location to DB
        // insert new transaction to DB
      $comm->setClientStatus(ClientStatus::SERVICING_1);
      break;
    case 1:
      //TODO: create new delivery()
        // insert new location to DB
        // insert new transaction to DB
      $comm->setClientStatus(ClientStatus::SERVICING_2);
      break;
    case 2:
      //TODO: Driver is full on deliveries! Cannot queue more
      break;
  }

  $comm->setStatus(CommStatus::UPDATE_OK); // when everything is finished mark it UPDATE_OK

}



//TODO: fix this later
function getCurrentDeliveries($user_id)
{
  global $conn;
  $user_info = getUserInformation($username);
  $user_id = $_user_info[0];
  $isRestaurant = $user_info[4];
  // Check if we have a restaurant or driver
  $id_type;
  if($isRestaurant == false)
    $id_type = "driver_id";
  else
    $id_type = "restaurant_id";

  // We only want deliveries that are active
  $query = "SELECT * FROM transaction WHERE '$id_type' = '$user_id' AND active = true";
  $result = $conn->query($query);
  if(!$result) die($conn->error);

  $deliveryInfo = $result->fetch_array(MYSQLI_NUM);
  $result->close();

  return $deliveryInfo;
}

?>

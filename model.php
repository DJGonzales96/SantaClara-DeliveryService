<?php
// DEVINS STORAGE
/*
//TODO: need google mapping functionality
function calculateCost(String $start, String $end) {}

//TODO: Need this functionality eventually
function updateDeliveries() {}
function requestDelivery(){}
function acceptDelivery() {}
function driverDelivered(String $user_id) {}
*/


//TODO: Using array of drivers, for each driver set his t_status to incoming_call
//function notifyNearbyDrivers() {
//
//}

//TODO: Doesnt include drivers with >1 deliveries
// function getValidDrivers() {
//  switch($num_deliveries) {
//    case 0:
//      assignDriverToDelivery($user_id, $t_id);
//      return ClientStatus::SERVICING_1;
//    case 1:
//      assignDriverToDelivery($user_id, $t_id);
//      return ClientStatus::SERVICING_2;
//    case 2:
//      //TODO: Driver is full on deliveries! Cannot queue more
//      return ClientStatus::SERVICING_2;
//  }
//}

//function checkDriverLocations() {
//  //TODO: Need Maps API for functionality   @Ilan
//  // getSurroundingDriversArray($)
//}


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

function getCurrentLocation($user_current_tid){
  $currentLoc = dbQuery("SELECT start_loc FROM transaction WHERE t_id='$user_current_tid'")[0];
    return dbQuery("SELECT * FROM Location WHERE loc_id=".$currentLoc);
}

function getLocations($user_id, $isRestaurant){
  if ($isRestaurant)
    $locations = dbQuery("SELECT start_loc FROM transaction WHERE primary_user_id=".$user_id.
      " AND t_type='request'");
  else
    $locations = dbQuery("SELECT start_loc FROM transaction WHERE secondary_user_id=".$user_id.
        " AND t_type='request'");
  return $locations;
}


function driverUpdateLocation($user_id, $newLat, $newLong, $newAddr) {
  $new_loc_id = dbInsert("INSERT INTO location(lat,lon,address) VALUES ('$newLat',' $newLong',' $newAddr')");
  $new_t_id = dbInsert("INSERT INTO transaction(t_type, primary_user_id, start_loc) 
                VALUES ('loc_update', '$user_id',$new_loc_id )");
  dbUserUpdate($user_id, $new_t_id);
}


// Gets current in progress deliveries of a driver
function driverGetCurrentDeliveries($user_id)
{
  global $conn;
  $allTransactions = 0; // deleted
  $filteredTransactions = 0; // deleted
  for($i=0; $i < count($allTransactions); $i++) {
    $aDriverTransaction = $allTransactions[i];
  //TODO: filter allTransactions for the ones we want
}
  return $filteredTransactions;
}


function driverAcceptDelivery($user_id, $t_id) {
  $active_deliveries = driverGetCurrentDeliveries($user_id);
  driverAssignToDelivery($user_id, $t_id);
  // TODO: Own the request,
  //TODO: Cancel notification for all other drivers, they are IDLE now
  // Check how many active deliveries driver is servicing
  $num_deliveries = count($active_deliveries);

}

function driverAssignToDelivery($user_id, $t_id) {
  updateUserTransactionInDb($user_id, $t_id);   // Assign driver to this transaction TODO: be sure not to lose location
  updateTransactionSecondaryId($t_id, $user_id);// Assign transaction it's correct driver
  $t_status = "in_progress";
  updateTransactionStatus($t_status);         // Mark the transaction as an in_progress delivery
}



/// RESTAURANTS

// Gets current pending deliveries of a restaurant
//TODO: probably need to grab in-progress as well? AND status = open
function restaurantGetCurrentDeliveries($user_id) {
  global $conn;
  // We only want deliveries that are active
  $query = "SELECT * FROM transaction WHERE 'primary_user_id' = '$user_id' AND t_status = 'pending'";
  $result = $conn->query($query);
  if(!$result) die($conn->error);

  $deliveryInfo = $result->fetch_array(MYSQLI_NUM);
  $result->close();
  return $deliveryInfo;
}

function restaurantCreateNewDelivery($user_id, $address, $food) {
  //TODO: Need maps API for functionality
  // getLatLong($addr)
  // $in_range = isDestinationWithin40($Lat, $Long)
  // $nearby_driver_ids = getDriversNearby($lat,$long)
  // if($in_range && $nearby_driver_ids > 0)
  // for now we hard code values and assume every delivery request is valid
  $newLat = 100.1;
  $newLong = 200.2;
  $t_type = "delivery_req";
  $t_status = "pending";
  $new_loc_id = insertNewLocationToDb($newLat, $newLong, $address);
  $new_t_id = insertNewRestTransactionToDb($user_id, $t_type, $new_loc_id, $t_status);

  //TODO: notify drivers of pending transactions

  // else (not succeed case)
  $t_status = "failed";
}

function restaurantUpdateAddress($user_id, $newLat, $newLong, $newAddr) {
    driverUpdateLocation($user_id, $newLat, $newLong, $newAddr);
}

?>

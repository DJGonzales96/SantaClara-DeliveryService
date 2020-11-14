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


const MAXIMUM_DISTANCE_FROM_RESTAURANT = 15; // max. distance allowed between driver & restaurant
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
  return getLatLongFromAddress($friendlyName);
}

// OK for v0.1
function getCurrentLocation($user_current_tid){
  $currentLoc = dbQuery("SELECT start_loc FROM transaction WHERE t_id='$user_current_tid'")[0];
  return dbQuery("SELECT * FROM Location WHERE loc_id='$currentLoc'");
}

// CHECK for v0.1 (meaning it's working but may get name changed etc.)
function getCurrentTransactions($user_id, $isRestaurant){
  if ($isRestaurant)
    $transactions = dbQuery("SELECT * FROM transaction WHERE primary_user_id='$user_id'
       AND t_type='delivery_req'");
  else
    $transactions = dbQuery("SELECT * FROM transaction WHERE secondary_user_id='$user_id'
         AND t_type='delivery_req' AND t_status='active'");
  if (is_null($transactions))
    $transactions = array();
  return $transactions;
}


// OK for v0.1
function driverGetPendingRequests($user_id) {
  $result = null;
  $query = "SELECT t_id, lat, lon, address, food, price FROM 
        (Transaction JOIN Location ON Transaction.end_loc=Location.loc_id) 
        WHERE 
         t_type='delivery_req' AND t_status='pending'"; // ... WHERE... secondary_user_id='$user_id'
  $pendingRequestInfo = dbQuery($query); // array with lat[1], lon[2]
  if (!is_null($pendingRequestInfo)){
    $currentUserTid = dbQuery("SELECT t_id From User WHERE user_id='$user_id'");
    $currentUserLocation = getCurrentLocation($currentUserTid[0]); // array with lat[1],lon[2]
    $mapsMatrixData = getMapsDistanceDurationTwoPts($currentUserLocation[1],$currentUserLocation[2],
        $pendingRequestInfo[1],$pendingRequestInfo[2]);
    if ($mapsMatrixData[0] <= MAXIMUM_DISTANCE_FROM_RESTAURANT) // $mapsMatrixData -> [0] distance Mi, [1] time min.
      $result = $pendingRequestInfo;
  }
  return $result;
}


// OK for v0.1
function driverUpdateLocation($user_id, $newLat, $newLong, $newAddr) {
  $new_loc_id = dbInsert("INSERT INTO location(lat,lon,address) VALUES ('$newLat',' $newLong',' $newAddr')");
  $new_t_id = dbInsert("INSERT INTO transaction(t_type, primary_user_id, start_loc) 
                VALUES ('loc_update', '$user_id',$new_loc_id )");
  dbUserUpdate($user_id, $new_t_id);
}


// Gets current in progress deliveries of a driver
//function driverGetCurrentDeliveries($user_id)
//{
//  global $conn;
//  $allTransactions = 0; // deleted
//  $filteredTransactions = 0; // deleted
//  for($i=0; $i < count($allTransactions); $i++) {
//    $aDriverTransaction = $allTransactions[i];
//    //TODO: filter allTransactions for the ones we want
//  }
//  return $filteredTransactions;
//}


//function driverAcceptDelivery($user_id, $t_id) {
//  $active_deliveries = driverGetCurrentDeliveries($user_id);
//  driverAssignToDelivery($user_id, $t_id);
//  // TODO: Own the request,
//  //TODO: Cancel notification for all other drivers, they are IDLE now
//  // Check how many active deliveries driver is servicing
//  $num_deliveries = count($active_deliveries);
//}


//function driverAssignToDelivery($user_id, $t_id) {
//  updateUserTransactionInDb($user_id, $t_id);   // Assign driver to this transaction TODO: be sure not to lose location
//  updateTransactionSecondaryId($t_id, $user_id);// Assign transaction it's correct driver
//  $t_status = "in_progress";
//  updateTransactionStatus($t_status);         // Mark the transaction as an in_progress delivery
//}



/// RESTAURANTS

// Gets current pending deliveries of a restaurant
////TODO: probably need to grab in-progress as well? AND status = open
//function restaurantGetCurrentDeliveries($user_id) {
//  global $conn;
//  // We only want deliveries that are active
//  $query = "SELECT * FROM transaction WHERE 'primary_user_id' = '$user_id' AND t_status = 'pending'";
//  $result = $conn->query($query);
//  if(!$result) die($conn->error);
//
//  $deliveryInfo = $result->fetch_array(MYSQLI_NUM);
//  $result->close();
//  return $deliveryInfo;
//}



//TODO: Need maps API for functionality
// getLatLong($addr)
// $in_range = isDestinationWithin40($Lat, $Long)
// $nearby_driver_ids = getDriversNearby($lat,$long)
// if($in_range && $nearby_driver_ids > 0)
// for now we hard code values and assume every delivery request is valid
//t_id
//t_type
//primary_user_id
//secondary_user_id
//start_loc
//end_loc
//timestamp
//food
//price
//duration
//t_status
// IN-PROGRESS for v0.1
function restaurantCreateNewDelivery($user_id, $friendlyName, $food) {
  // 1. GET LAT, LON from google
  $destinationAddressArray = getMapsLocationFromFriendlyAddress($friendlyName);
  // 2. CHECK if within 40 min. drive
  // 3. Get list of drivers within range

  $query = "SELECT start_loc FROM Transaction WHERE t_id IN 
            (SELECT t_id FROM User WHERE user_id = '$user_id') "; // get the restaurant's address
  $start_loc = dbQuery($query)[0];

  $query = "INSERT INTO Location(lat,lon, address) 
    VALUES ($destinationAddressArray[0],$destinationAddressArray[1],'$destinationAddressArray[2]')";
  $end_loc_id = dbInsert($query);

  $query = "INSERT INTO Transaction(t_type, primary_user_id, start_loc, end_loc, food, price, duration, t_status)
            VALUES('delivery_req', '$user_id', '$start_loc', '$end_loc_id', '$food', 100 , 41 ,'pending')";
  $new_t_id = dbInsert($query);

  //TODO: notify drivers of pending transactions

  // else (not succeed case)
  $t_status = "failed";
}


// CHECK for v0.1 - suppose to work fine
function restaurantUpdateAddress($user_id, $newLat, $newLong, $newAddr) {
  driverUpdateLocation($user_id, $newLat, $newLong, $newAddr);
}

?>

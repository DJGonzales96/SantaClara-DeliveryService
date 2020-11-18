<?php

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
         AND t_type='delivery_req' AND t_status='in-progress'");
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
  // Case: we are passed only a friendlyAddress and have missing lat/long
  if(strlen($newLat) < 2 || strlen($newLong) < 2) {
    $mapsArray = getMapsLocationFromFriendlyAddress($newAddr);
    $newLat = $mapsArray[0];
    $newLong = $mapsArray[1];
  }
  $new_loc_id = dbInsert("INSERT INTO location(lat,lon,address) VALUES ('$newLat',' $newLong',' $newAddr')");
  $new_t_id = dbInsert("INSERT INTO transaction(t_type, primary_user_id, start_loc) 
                VALUES ('loc_update', '$user_id',$new_loc_id )");
  dbUserUpdate($user_id, $new_t_id);
}

// IN-PROGRESS for v0.1
function restaurantCreateNewDelivery($user_id, $friendlyName, $food) {
  // get the restaurant's address
  $query = "SELECT start_loc FROM Transaction WHERE t_id IN 
            (SELECT t_id FROM User WHERE user_id = '$user_id') ";
  $start_loc = dbQuery($query)[0];

  $query = "SELECT * FROM location WHERE loc_id = '$start_loc'";
  $start_friendly_addr = dbQuery($query)[3];

  // 1. GET LAT, LON from google
  $destinationAddressArray = getMapsLocationFromFriendlyAddress($friendlyName);
  $originAddressArray = getMapsLocationFromFriendlyAddress($start_friendly_addr);
  // 2. GET DISTANCE, TIME from google
  $distanceTimeArray = getMapsDistanceDurationTwoPts($originAddressArray[0],$originAddressArray[1],$destinationAddressArray[0],$destinationAddressArray[1]);
  $duration = $distanceTimeArray[1];
  $price = calculateCost($distanceTimeArray[0], $duration);
  // 3. CHECK if within 40 min. drive
  if(deliveryInRange($duration)) {
    $query = "INSERT INTO Location(lat,lon, address) 
        VALUES ($destinationAddressArray[0],$destinationAddressArray[1],'$destinationAddressArray[2]')";
    $end_loc_id = dbInsert($query);

    $query = "INSERT INTO Transaction(t_type, primary_user_id, start_loc, end_loc, food, price, duration, t_status)
          VALUES('delivery_req', '$user_id', '$start_loc', '$end_loc_id', '$food', '$price' , '$duration' ,'pending')";
    $new_t_id = dbInsert($query);

    // 4. Get list of drivers within range
    //TODO: notify drivers of pending transactions
  }
  else {//TODO: (not succeed case)
  $t_status = "failed";
  }
}

function driverAcceptDelivery($user_id, $delivery_t_id) {
  $query = "UPDATE transaction SET secondary_user_id = '$user_id', t_status = 'in-progress' WHERE t_id = '$delivery_t_id'";
  dbInsert($query);
}


// CHECK for v0.1 - suppose to work fine
function restaurantUpdateAddress($user_id, $newLat, $newLong, $newAddr) {
  driverUpdateLocation($user_id, $newLat, $newLong, $newAddr);
}

function deliveryInRange($time_in_mins) {
  if($time_in_mins < 40)
    return true;
  elseif($time_in_mins >= 40)
    return false;
}

// Calculate cost for delivery
function calculateCost($distance_in_miles, $time_in_mins) {
  $min_cost = 5;
  // First mile is free, 25c per min
  $cost = $min_cost + 2*($distance_in_miles - 1) + 0.25*$time_in_mins;
  return $cost;
}

?>

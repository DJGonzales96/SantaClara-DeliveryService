<?php

const MAXIMUM_DISTANCE_FROM_RESTAURANT = 5; // max. distance allowed between driver & restaurant
require_once 'includes/conn.php';
require_once 'includes/session.php';
require_once 'includes/auth.php';
include 'db.php';
include 'maps.php';

// prevent unauthorized access
//if ($_SESSION['authenticated'] != true || $_SESSION["username"] == NULL)
// die("{'status':'STATUS_ERROR','error':'Not logged in'}");

// OK for v0.1
function getCurrentLocation($user_current_tid){
  $currentLoc = dbQuery("SELECT start_loc FROM transaction WHERE t_id='$user_current_tid'")[0];
  return dbQuery("SELECT * FROM location WHERE loc_id='$currentLoc'");
}

// CHECK for v0.1 (meaning it's working but may get name changed etc.)
function getCurrentTransactions($user_id, $isRestaurant){
  if ($isRestaurant) // RESTAURANT current deliveries
    $transactions = dbQueryMultiRow("SELECT t_id, t_type, primary_user_id, secondary_user_id, loc1.lat, loc1.lon, loc1.address,
loc2.lat, loc2.lon, loc2.address, timestamp, food, price, duration, t_status
FROM transaction
JOIN location AS loc1 ON (start_loc=loc1.loc_id)
JOIN location AS loc2 ON (end_loc=loc2.loc_id)
WHERE primary_user_id = '$user_id' AND t_type = 'delivery_req' AND (t_status = 'in-progress' OR t_status = 'pending')");
  else // DRIVER current deliveries
    $transactions = dbQueryMultiRow("SELECT t_id, t_type, primary_user_id, secondary_user_id, loc1.lat, loc1.lon, loc1.address,
loc2.lat, loc2.lon, loc2.address, timestamp, food, price, duration, t_status
FROM transaction
JOIN location AS loc1 ON (start_loc=loc1.loc_id)
JOIN location AS loc2 ON (end_loc=loc2.loc_id)
WHERE secondary_user_id = '$user_id' AND t_type = 'delivery_req' AND t_status = 'in-progress'");
  if (is_null($transactions))
    $transactions = array();
  return $transactions;
}


// OK for v0.1
function driverGetPendingRequests($user_id) {
  $result = null;
  $query = "SELECT t_id, lat, lon, address, food, price, primary_user_id FROM
        (transaction JOIN location ON transaction.end_loc=location.loc_id)
        WHERE
         t_type='delivery_req' AND t_status='pending'"; // ... WHERE... secondary_user_id='$user_id'
  $pendingRequestInfo = dbQuery($query); // array with lat[1], lon[2]
  $restaurnatLocationTid = dbQuery("SELECT t_id FROM user WHERE user_id='$pendingRequestInfo[6]'")[0];
  $restaurantAddress = getCurrentLocation($restaurnatLocationTid);

  if (!is_null($pendingRequestInfo)){
    $currentUserTid = dbQuery("SELECT t_id From user WHERE user_id='$user_id'");
    $currentUserLocation = getCurrentLocation($currentUserTid[0]); // array with lat[1],lon[2]
    $mapsMatrixData = getMapsDistanceDurationTwoPts($currentUserLocation[1],$currentUserLocation[2],
        $restaurantAddress[1],$restaurantAddress[2]);//$pendingRequestInfo[1],$pendingRequestInfo[2]
    if ($mapsMatrixData[0] <= MAXIMUM_DISTANCE_FROM_RESTAURANT) // $mapsMatrixData -> [0] distance Mi, [1] time min.
      $result = $pendingRequestInfo;
  }
  return $result;
}


// OK for v0.1
function restaurnatGetPendingRequests($user_id) {
  $query = "SELECT t_id, lat, lon, address, food, price FROM
        (transaction JOIN location ON transaction.end_loc=location.loc_id)
        WHERE
        primary_user_id ='$user_id' AND
         t_type='delivery_req' AND
         t_status='pending'"; // ... WHERE... secondary_user_id='$user_id'
  return dbQuery($query); // array with lat[1], lon[2]
}


// OK for v0.1
function driverUpdateLocation($user_id, $newLat, $newLong, $newAddr) {
  // Case: we are passed only a friendlyAddress and have missing lat/long
  if(strlen($newLat) < 2 || strlen($newLong) < 2) {
    $mapsArray = getMapsLocationFromFriendlyAddress($newAddr);
    $newLat = $mapsArray[0];
    $newLong = $mapsArray[1];
  }
  $newAddr = getMapsFriendlyAddressFromLatLng($newLat, $newLong);
  // Case: invalid location is given
  if($newLat == 0.000000 && $newLong == 0.000000)
    $newAddr = "Invalid Location, consider setting a valid address before continuing.";

  $new_loc_id = dbInsert("INSERT INTO location(lat,lon,address) VALUES ('$newLat',' $newLong',' $newAddr')");
  $new_t_id = dbInsert("INSERT INTO transaction(t_type, primary_user_id, start_loc)
                VALUES ('loc_update', '$user_id',$new_loc_id )");
  dbUserUpdate($user_id, $new_t_id);
}


function restaurantCancelDelivery($user_id, $t_id) {
  $query = "UPDATE transaction SET t_status = 'canceled' WHERE primary_user_id = '$user_id' AND t_id = '$t_id'";
  dbInsert($query);
}


// TODO: THIS IS NOT FINISHED - CASE THE DISTANCE TOO FAR!!!!
function restaurantCreateNewDelivery($user_id, $friendlyName, $food) {
  // get the restaurant's address
  $query = "SELECT start_loc FROM transaction WHERE t_id IN
            (SELECT t_id FROM user WHERE user_id = '$user_id') ";
  $start_loc = dbQuery($query)[0];//NULL

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
    $query = "INSERT INTO location(lat,lon, address)
        VALUES ($destinationAddressArray[0],$destinationAddressArray[1],'$destinationAddressArray[2]')";
    $end_loc_id = dbInsert($query);

    $query = "INSERT INTO transaction(t_type, primary_user_id, start_loc, end_loc, food, price, duration, t_status)
          VALUES('delivery_req', '$user_id', '$start_loc', '$end_loc_id', '$food', '$price' , '$duration' ,'pending')";
    $new_t_id = dbInsert($query);
    return 0;
  }
  else {
    return -1;
  }
}

function driverAcceptDelivery($user_id, $delivery_t_id) {
  $query = "UPDATE transaction SET secondary_user_id = '$user_id', t_status = 'in-progress' WHERE t_id = '$delivery_t_id'";
  dbInsert($query);
}

function driverDelieveredDelivery($user_id, $delivery_t_id) {
  $query = "UPDATE transaction SET t_status = 'completed' WHERE t_id = '$delivery_t_id' AND secondary_user_id = '$user_id'";
  dbInsert($query);
  $query = "SELECT primary_user_id FROM transaction WHERE t_id = '$delivery_t_id'";
  $restaurantId = dbQuery($query)[0];
  $query = "SELECT price FROM transaction WHERE t_id = '$delivery_t_id'";
  $cost = dbQuery($query)[0];
  $query = "UPDATE user SET wallet = wallet - '$cost' WHERE user_id = '$restaurantId'";
  dbInsert($query);
  $query = "UPDATE user SET wallet = wallet + '$cost' WHERE user_id = '$user_id'";
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
  // Case: 0 mile or <1 mile delivery, first mile is free
  if($distance_in_miles < 1)
    $cost = $min_cost + 0.25+$time_in_mins;
  else
    // First mile is free, 25c per min
    $cost = $min_cost + 2*($distance_in_miles - 1) + 0.25*$time_in_mins;

  return $cost;
}

?>

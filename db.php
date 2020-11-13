<?php
require_once 'includes/conn.php';
require_once 'includes/session.php';
require_once 'includes/auth.php';

// prevent unauthorized access
if ($_SESSION['authenticated'] != true || $_SESSION["username"] == NULL)
    die("{'status':'STATUS_ERROR','error':'Not logged in'}");

function dbQueryError($conn, $where){
    $dumpStr = "in db query, dump: " .  $conn->error . ", where: " . $where;
    die("{'status':'STATUS_ERROR','error':'". $dumpStr . "'}");
}

// General DB query
function dbQuery($query) {
    global $conn;
    $result = $conn->query($query);
    if(!$result)
        dbQueryError($conn, "dbQuery, query:".$query);
    $info = $result->fetch_array(MYSQLI_NUM);
    $result->close();
    return $info;
}


// Inserts new transaction
function dbInsert($query) {
    global $conn;
    $result = $conn->query($query);
    if(!$result)
        dbQueryError($conn, "dbInsert, query:".$query);
    return $conn->insert_id;
}



// User info
function dbUserGetByUsername($username) {
    global $conn;
    $query = "SELECT * FROM user WHERE username = '$username'";
    $result = $conn->query($query);
    if(!$result)
        dbQueryError($conn, "getUser");
    $info = $result->fetch_array(MYSQLI_NUM);
    $result->close();
    return $info;
}

// Update user
function dbUserUpdate($user_id_to_update, $new_t_id) {
    global $conn;
    $query = "UPDATE user SET t_id=' $new_t_id' WHERE user_id='$user_id_to_update'";
    $result = $conn->query($query);
    if(!$result)
        dbQueryError($conn, "updateUser");
}



// list of fields:
// $t_id, $t_type, $primary_user_id, $secondary_user_id, $start_loc, $end_loc,
//                             $timestamp,  $food, $price, $duration, $t_status
// get transactions
// TODO: FIX PROBLEM WHEN USER HAS NO LOCATION AT ALL
function dbTransactionsGetBy($t_id, $primary_id, $t_type, $t_status) {
    global $conn;
    $query = "SELECT * FROM transaction WHERE"
        .(is_null($t_id) ? "" : " t_id =".$t_id)
        .(is_null($primary_id) ? "" : " primary_user_id =".$primary_id)
        .(is_null($t_type) ? "" : " AND t_type ='$t_type'")
        .(is_null($t_status) ? "" : " t_status ='$t_status'").";";
    $result = $conn->query($query);
    if(!$result)
        dbQueryError($conn, "getTransaction");
    $transactions = array();
    $index = 0;
    while($row = mysqli_fetch_row($result)) {
        $transactions[$index++] = $row;
    }
    $result->close();
    return $transactions;
}

// $t_id, $t_type, $primary_user_id, $secondary_user_id, $start_loc, $end_loc,
//                             $timestamp,  $food, $price, $duration, $t_status
// Inserts new transaction
function dbTransactionInsert($t_type, $primary_id, $secondary_id, $start_loc, $destination, $food,  $t_status) {
    global $conn;
    $query = "INSERT INTO transaction(t_type, primary_user_id, secondary_user_id, start_loc, end_loc, food, t_status) 
                VALUES('$t_type', '$primary_id', '$secondary_id', '$start_loc', '$destination', '$food', '$t_status')";
    $result = $conn->query($query);
    if(!$result)
        dbQueryError($conn, "insertNewTransaction");
    return $conn->insert_id;
}

// Update transaction
function dbTransactionUpdate($t_id_to_update, $t_type, $primary_id, $secondary_id, $start_loc, $destination, $food, $t_status) {
    global $conn;
    $query = "UPDATE transaction SET t_type = '$t_type', primary_user_id = '$primary_id', secondary_user_id = '$secondary_id', 
                start_loc = '$start_loc', end_loc = '$destination', food = '$food', t_status = '$t_status'
                WHERE t_id - '$t_id_to_update')";
    $result = $conn->query($query);
    if(!$result)
        dbQueryError($conn, "updateTransaction");
}


// Returns a 2D array of locations
function dbLocationsGetBy($loc_id, $lat, $lon, $address) {
    global $conn;
    $query = "SELECT * FROM location WHERE "
        .(is_null($loc_id)?"": " loc_id =".$loc_id)
        .(is_null($lat)?"": " lat =".$lat)
        .(is_null($lon)?"": " lon =".$lon)
        .(is_null($address)?"": " address =".$address);
    $result = $conn->query($query);
    if(!$result)
        dbQueryError($conn, "getLocation");
    $locations = array();
    $index = 0;
    while($row = mysqli_fetch_row($result)) {
        $locations[$index++] = $row;
    }
    $result->close();
    return $locations;
}

// Inserts a new location
function dbLocationInsert($newLat, $newLong, $newAddr) {
    global $conn;
    $query = "INSERT INTO location(lat, lon, address) VALUES('$newLat', '$newLong', '$newAddr')";
    $result = $conn->query($query);
    if(!$result)
        dbQueryError($conn, "insertLocation");
    return $conn->insert_id; // the location id is the most recently inserted
}


/* UPDATE OPERATIONS */
// Update location
function dbLocationUpdate($loc_id_to_update, $newLat, $newLong, $newAddr) {
    global $conn;
    $query = "UPDATE location SET lat = '$newLat', lon = '$newLong', address = '$newAddr' WHERE loc_id = '$loc_id_to_update')";
    $result = $conn->query($query);
    if(!$result)
        dbQueryError($conn, "updateLocation");
}
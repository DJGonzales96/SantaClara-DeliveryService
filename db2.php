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
/* INSERTION OPERATIONS */
// Inserts a new location
function insertLocation($newLat, $newLong, $newAddr) {
    global $conn;
    $query = "INSERT INTO location(lat, lon, address) VALUES('$newLat', '$newLong', '$newAddr')";
    $result = $conn->query($query);
    if(!$result)
        dbQueryError($conn, "insertLocation");
    return $conn->insert_id; // the location id is the most recently inserted
}

// Inserts new transaction
function insertTransaction($t_type, $primary_id, $secondary_id, $start_loc, $destination, $food, $t_status) {
    global $conn;
    $query = "INSERT INTO transaction(t_type, primary_user_id, secondary_user_id, start_loc, end_loc, food, t_status) 
                VALUES('$t_type', '$primary_id', '$secondary_id', '$start_loc', '$destination', '$food', '$t_status')";
    $result = $conn->query($query);
    if(!$result)
        dbQueryError($conn, "insertNewTransaction");
    return $conn->insert_id;
}

/* UPDATE OPERATIONS */
// Update location
function updateLocation($loc_id_to_update, $newLat, $newLong, $newAddr) {
    global $conn;
    $query = "UPDATE location SET lat = '$newLat', lon = '$newLong', address = '$newAddr' WHERE loc_id = '$loc_id_to_update')";
    $result = $conn->query($query);
    if(!$result)
        dbQueryError($conn, "updateLocation");
}

// Update transaction
function updateTransaction($t_id_to_update, $t_type, $primary_id, $secondary_id, $start_loc, $destination, $food, $t_status) {
    global $conn;
    $query = "UPDATE transaction SET t_type = '$t_type', primary_user_id = '$primary_id', secondary_user_id = '$secondary_id', 
                start_loc = '$start_loc', end_loc = '$destination', food = '$food', t_status = '$t_status'
                WHERE t_id - '$t_id_to_update')";
    $result = $conn->query($query);
    if(!$result)
        dbQueryError($conn, "updateTransaction");
}

// Update user
function updateUser($user_id_to_update, $new_t_id) {
    global $conn;
    $query = "UPDATE user SET t_id=' $new_t_id' WHERE user_id='$user_id_to_update'";
    $result = $conn->query($query);
    if(!$result)
        dbQueryError($conn, "updateUser");
}

/* READ OPERATIONS */
// Returns a 2D array of locations
function getLocations() {
    global $conn;
    $query = "SELECT * FROM location";
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

// Returns a 2D array of transactions
function getTransactions() {
    global $conn;
    $query = "SELECT * FROM transaction";
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



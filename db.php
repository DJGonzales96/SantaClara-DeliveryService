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

// Inserts a new location
function insertNewLocationToDb(String $newLat, String $newLong, String $newAddr) {
    global $conn;
    $query = "INSERT INTO location(lat, lon, address) VALUES('$newLat', '$newLong', '$newAddr')";
    $result = $conn->query($query);
    if(!$result)
        dbQueryError($conn, "insertNewLocationToDb");
    return $conn->insert_id; // the location id is the most recently inserted
}

// Inserts a new transaction
function insertNewTransactionToDb($user_id, $start_location, $t_type){
    global $conn;
    // echo "t_type: " . $t_type . ", user_id: ". $user_id . ", start_location: ". $start_location; // REMOVE DEBUG  REMOVE DEBUG  REMOVE DEBUG  REMOVE DEBUG  REMOVE DEBUG  REMOVE DEBUG  REMOVE DEBUG
    $query = "INSERT INTO transaction(t_type, restaurant_id, start_loc) VALUES('$t_type', '$user_id', '$start_location')";
    $result = $conn->query($query);
    if(!$result)
        dbQueryError($conn, "insertNewTransactionToDb");
    return $conn->insert_id;
}

function updateUserTransactionInDb($user_id,$new_t_id){
    global $conn;
    $query = "UPDATE User SET t_id=' $new_t_id' WHERE user_id='$user_id'";
    $result = $conn->query($query);
    if(!$result)
        dbQueryError($conn, "updateUserTransactionInDb");
}

function getLocationIdByTid($t_id){
    global $conn;
    $query = "SELECT start_loc FROM transaction WHERE t_id = '$t_id'";
    $result = $conn->query($query);
    if(!$result)
        dbQueryError($conn, "getLocationByTid");
    $row = $result->fetch_array(MYSQLI_NUM);
    $loc = $row[0];
    return $loc;
}

function getLocationByLocId($loc_id)
{
    global $conn;
    $query = "SELECT * FROM location WHERE loc_id = '$loc_id'";
    $result = $conn->query($query);
    if(!$result)
        dbQueryError($conn, "getLocationByLocId");

    $row = $result->fetch_array(MYSQLI_NUM);
    $result->close();

    $currLat = $row[1];
    $currLong = $row[2];
    $currAddr = $row[3];

    return array($currLat, $currLong, $currAddr);
}

?>

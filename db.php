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

<<<<<<< Updated upstream
// Update user
function dbUserUpdate($user_id_to_update, $new_t_id) {
=======
function getLocationByLocId( $loc_id)
{
>>>>>>> Stashed changes
    global $conn;
    $query = "UPDATE user SET t_id=' $new_t_id' WHERE user_id='$user_id_to_update'";
    $result = $conn->query($query);
    if(!$result)
        dbQueryError($conn, "updateUser");
}
<?php
require 'includes/conn.php';
require 'includes/session.php';
require 'includes/auth.php';
require 'includes/comm.php';
require 'model.php';


if ($_SESSION['authenticated'] != true || $_SESSION["username"] == NULL){
    die("{'status':'STATUS_ERROR','error':'Not logged in'}");
} else {
    $method = $_SERVER['REQUEST_METHOD'];
    $request = explode("/", substr(@$_SERVER['PATH_INFO'], 1));

    switch ($method) {
        // POST
        case 'POST':
            // print_r($request) . "<br/>";  // uncomment to see request structure
            $comm = new Comm(); // Create JSON empty communication object
            // DRIVER
            if ($request[0] == 'driver'){
                if ($request[1] == 'location'){
                    setCommDriverLocation($comm);
                } else
                    if ($request[1] == 'accept'){
                        setCommDriverAccepted($comm);
                    } else {
                        $comm->setError("not set POST operation");
                    }
            }
            // RESTAURANT
            else if ($request[0] == 'restaurant'){
                if ($request[1] == 'request'){
                    setCommRestaurantRequest($comm);
                } else {
                    $comm->setError("not set POST operation"); // error no operation
                }
            } else {
                $comm->setError("not set if to POST to driver or to restaurant");
            }
            // JSON will display results whether POST operation is successful
            echo json_encode($comm);
            break;

        // GET
        case 'GET':
            // print_r($request) . "<br/>";  // uncomment to see request structure
            $comm = new Comm(); // Create JSON empty communication object
            // DRIVER / RESTAURANT
            if ($request[0] == 'driver' || $request[0] == 'restaurant')
                getComm($comm, $request[0]);
            else
                $comm->setError("not set if to GET from driver or restaurant");
            // JSON will show content with status READY
            echo json_encode($comm);
            break;

        // Request is not supported
        default:
            $comm = new Comm(); // Create JSON empty communication object
            echo json_encode($comm); // Shows JSON with INVALID status
            break;
    }
}

function setCommRestaurantRequest($comm){
    $user_info = dbUserGetByUsername($_SESSION["username"]);
    restaurantCreateNewDelivery($user_info[0], $_POST["address"], $_POST["food"]);
    getComm($comm,'driver'); // shortcut to get back all info
    $comm->setStatus(CommStatus::UPDATE_OK); // when everything is finished mark it UPDATE_OK
}

function setCommDriverAccepted($comm){
    $user_info = dbUserGetByUsername($_SESSION["username"]);
    driverAcceptDelivery($user_info[0],$_POST['request_ID']); // request ID could be similar to t_id
    getComm($comm,'driver'); // shortcut to get back all info
    $comm->setStatus(CommStatus::UPDATE_OK); // when everything is finished mark it UPDATE_OK
}

function setCommDriverLocation($comm){
    $user_info = dbUserGetByUsername($_SESSION["username"]);
    driverUpdateLocation($user_info[0],$_POST['lat'],$_POST['long'],$_POST['address']);
    getComm($comm,'driver'); // shortcut to get back all info
    $comm->setStatus(CommStatus::UPDATE_OK); // when everything is finished mark it UPDATE_OK
}


// builds a Communication object
function getComm($comm, $identityString){ // gets an empty comm and sets it to valid one
    $user_info = dbUserGetByUsername($_SESSION["username"]);
    $isRestaurant = (strcmp($user_info[4],"1") == 0);
// NEEDS TO BE CHECKED CHECK CHECK CHECK if isRestaurant - and exit if wrong
//        if ( ($user_info[4] && ...identityString == ...) )
//        return;

    $comm->setUserId($user_info[0]);
    $comm->setFriendlyName($user_info[2]);
    $comm->setIsRestaurant($isRestaurant);
    $comm->setLocation(getCurrentLocation($user_info[5])); // $user_info[5] currentTid
    $comm->setClientStatus(ClientStatus::IDLE); //TODO: This will be a function call = getStatusByUserId() which a function that checks for pending transactions

    $comm->setCurrentTransactions(getLocations($user_info[0],$isRestaurant));

    $comm->setStatus(CommStatus::STATUS_OK); // when everything is finished mark it STATUS_OK
}
?>

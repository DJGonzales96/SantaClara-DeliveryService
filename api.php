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
            if ($request[0] == 'driver')
                getCommDriver($comm);
            else if ($request[0] == 'restaurant'){
                getCommRestaurant($comm);
            } else {
                $comm->setError("not set if to GET from driver or restaurant");
            }
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
  $user_info = getUserInformation($_SESSION["username"]);
  $user_id = $user_info[0];
  restaurantCreateNewDelivery($user_id, $_POST["address"], $_POST["food"]);
  $current_deliveries = getCurrentRestaurantDeliveries($user_id); //this is an array of "pending" transactions
  $comm->setCurrentTransactions($current_deliveries);
  $comm->setStatus(CommStatus::UPDATE_OK); // when everything is finished mark it UPDATE_OK
}

function setCommDriverAccepted($comm){
    // get user
    $user_info = getUserInformation($_SESSION["username"]);
    $new_driver_status = driverAcceptDelivery($user_info[0],$_POST['request_ID']); // request ID could be similar to t_id
    $comm->setClientStatus($new_driver_status);

    $comm->setStatus(CommStatus::UPDATE_OK); // when everything is finished mark it UPDATE_OK
}

function setCommDriverLocation($comm){
    // get user
    $user_info = getUserInformation($_SESSION["username"]);
    $user_id = $user_info[0];
    updateLocation($user_id,$_POST['lat'],$_POST['long'],$_POST['address']);
    $comm->setLocation(getCurrentUserLocationByTid($userCurrentLocationByTransactionId));
    $comm->setStatus(CommStatus::UPDATE_OK); // when everything is finished mark it UPDATE_OK
}


// builds a Communication object
function getCommDriver($comm){ // gets an empty comm and sets it to valid one
    // get the state of the user
    $user_info = getUserInformation($_SESSION["username"]);
//    if ($user_info[4]) // NEEDS TO BE CHECKED CHECK CHECK CHECK if isRestaurant - exit
//        return;
    $user_id = $user_info[0];
    $friendly_name = $user_info[2];
    $userCurrentLocationByTransactionId = $user_info[5];
    // update the comm to reflect user's state
    $comm->setUserId($user_id);
    $comm->setFriendlyName($friendly_name);
    $comm->setIsRestaurant($user_info[4]);

    $comm->setLocation(getCurrentUserLocationByTid($userCurrentLocationByTransactionId));
    //TODO: Call a function that checks for pending transactions
      // Client status will be updated based on stuff
    $comm->setClientStatus(ClientStatus::IDLE);
    //TODO: This will be a function call = getStatusByUserId()
    $comm->setCurrentTransactions(array("1 Empty","2 Empty"));
    //$comm->setLocation(getLocationByTid($currentTransactionId)); // set in COMM the user's current location

    $comm->setStatus(CommStatus::STATUS_OK); // when everything is finished mark it STATUS_OK
}

function getCommRestaurant($comm){ // gets an empty comm and sets it to valid one
  $user_info = getUserInformation($_SESSION["username"]);

  $user_id = $user_info[0];
  $friendly_name = $user_info[2];
  $userCurrentLocationByTransactionId = $user_info[5];

  $comm->setUserId($user_id);
  $comm->setFriendlyName($friendly_name);
  $comm->setIsRestaurant($user_info[4]);

  $comm->setLocation(getCurrentUserLocationByTid($userCurrentLocationByTransactionId));
  $comm->setClientStatus(ClientStatus::IDLE);
  $comm->setCurrentTransactions(array("1 Empty","2 Empty"));

  $comm->setStatus(CommStatus::STATUS_OK); // when everything is finished mark it STATUS_OK
}
?>

<?php
require 'includes/conn.php';
require 'includes/session.php';
require 'includes/auth.php';
require 'includes/comm.php';
require 'model.php';


if ($_SESSION['authenticated'] != true || $_SESSION["username"] == NULL){
    die("Not logged in");
} else {
    $method = $_SERVER['REQUEST_METHOD'];
    $request = explode("/", substr(@$_SERVER['PATH_INFO'], 1));

    switch ($method) {
      case 'POST': // Request is POST
            // print_r($request) . "<br/>";  // uncomment to see request structure
            $comm = new Comm(); // Create JSON empty communication object
            // check api.php/driver or api.php/restaurant
            if ($request[0] == 'driver'){
                if ($request[1] == 'location'){
                    updateDriverLocation($comm);
                } else if ($request[1] == 'accept'){
                    //driverAccepted($comm);
                } else {
                      $comm->setError("not set POST operation");
                }
            }
            else if ($request[0] == 'restaurant'){
                echo "do something with restaurant"; //prepareDriverComm();
            } else {
                $comm->setError("not set if to POST to driver or to restaurant");
            }
            // JSON will show if operation is successful
            echo json_encode($comm);
            break;
      case 'GET': // Request is GET
            // print_r($request) . "<br/>";  // uncomment to see request structure
            $comm = new Comm(); // Create JSON empty communication object
            // check api.php/driver or api.php/restaurant
            if ($request[0] == 'driver')
                prepareDriverComm($comm);
            else if ($request[0] == 'restaurant'){
                echo "do something with restaurant"; //CHANGES...  prepareDriverComm();
            } else {
                $comm->setError("not set if to GET from driver or restaurant");
            }
            // JSON will show content with status READY
            echo json_encode($comm);
            break;
      default:
            $comm = new Comm(); // Create JSON empty communication object
            echo json_encode($comm); // Shows JSON with INVALID status
            break;
    }
}

function driverAccepted($comm){
    // get user
    $user_info = getUserInformation($_SESSION["username"]);
//    if ($user_info[4]) // NEEDS TO BE CHECKED CHECK CHECK CHECK if isRestaurant - exit
//        return;  ... $comm->setError( .. );
    // triggerSomething($user_info[0],$_POST['request_ID']); // request ID could be similar to t_id

    $comm->setStatus(CommStatus::UPDATE_OK); // when everything is finished mark it UPDATE_OK
}

function updateDriverLocation($comm){
    // get user
    $user_info = getUserInformation($_SESSION["username"]);
//    if ($user_info[4]) // NEEDS TO BE CHECKED CHECK CHECK CHECK if isRestaurant - exit
//        return;
    $user_id = $user_info[0];
    $friendly_name = $user_info[2];
    $currentTransactionId = $user_info[5];
    // update the comm to reflect user's state
    $comm->setUserId($user_id);
    $comm->setFriendlyName($friendly_name);

    setLocationTransaction($user_id,$_POST['lat'],$_POST['long'],$_POST['address']);
    //$comm->setLocation(getLocationByTid($currentTransactionId)); // set in COMM the user's current location to reflect change

    // NEXT LINE IS JUST AN EXAMPLE OF GETTING SOMETHING FROM MAPS API - JUST FOR DEMO DELETE LATER
    $comm->setLocation(getFromMapsApiDemo($_POST['address'])); // set in COMM the user's current location

    $comm->setStatus(CommStatus::UPDATE_OK); // when everything is finished mark it UPDATE_OK
}

function prepareDriverComm($comm){ // gets an empty comm and sets it to valid one
    // get the state of the user
    $user_info = getUserInformation($_SESSION["username"]);
//    if ($user_info[4]) // NEEDS TO BE CHECKED CHECK CHECK CHECK if isRestaurant - exit
//        return;
    $user_id = $user_info[0];
    $friendly_name = $user_info[2];
    $currentTransactionId = $user_info[5];
    // update the comm to reflect user's state
    $comm->setUserId($user_id);
    $comm->setFriendlyName($friendly_name);

    // USE AFTER SQL AND MODEL ARE IMPLEMENTED
    //$comm->setLocation(getLocationByTid($currentTransactionId)); // set in COMM the user's current location

    $comm->setStatus(CommStatus::STATUS_OK); // when everything is finished mark it STATUS_OK
=======
require 'model.php';

$str = 'BLUH';

$commState = (object) [
    'isRestaurant' => 'BLUH',
    'anArray' => array('status' => '1',
    'string_key' => $str,
    'string_value' => $str,
    'string_extra' => $str, // this can represent location
    'is_public' => $str,    // triggerOperation
    'is_public_for_contacts' => $str)
];

// Read from commState, pass string into -> updateLocation()

echo json_encode($commState);

// Contains useful information about driver/restaurant state needed
class UserState
{
  public $isRestaurant;
  public $status; //TODO: This will be an enum for {0, 1, 2}
  public $currLoc;
  public $activeDeliveries; // array of Delivery objects
}

// Contains useful information about deliveries
class Delivery
{
  public $deliveryID;
  public $destination;
  public $driverID;
  public $restaurantID;
  public $timestamp;
>>>>>>> master
}
?>

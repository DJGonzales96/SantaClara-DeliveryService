<?php
require 'includes/conn.php';
require 'includes/session.php';
require 'includes/auth.php';
require 'includes/comm.php';
require 'model.php';

// $str = 'BLUH_ILAN';
// $commState = (object) [
//     'isRestaurant' => 'BLUH_NATHAN',
//     'anArray' => array('status' => '1',
//     'string_key' => $str,
//     'string_value' => $str,
//     'string_extra' => $str,
//     'is_public' => $str,
//     'is_public_for_contacts' => $str)
// ];

if ($_SESSION['authenticated'] != true || $_SESSION["username"] == NULL){
    die("Not logged in");
} else {
    $method = $_SERVER['REQUEST_METHOD'];
    $request = explode("/", substr(@$_SERVER['PATH_INFO'], 1));

    switch ($method) {
      case 'POST':
            echo "POST";
            $_POST["name"];
            echo $request;
            break;
      case 'GET':
            // print_r($request) . "<br/>";
            // Create and prepare JSON empty communication object
            // Request is GET for api.php/driver or api.php/restaurant
            $comm = new Comm();
            if ($request[0] == 'driver')
                prepareDriverComm($comm);
            else if ($request[0] == 'restaurant')
                echo "deal with restaurant"; //prepareDriverComm();

            //send JSON
            echo json_encode($comm);
            break;
        case 'PUT':
            echo "PUT";
            echo $request;
            break;
      default:
            echo "ERROR";
            echo $request;
            break;
    }
}

function prepareDriverComm($comm){
    $user_info = getUserInformation($_SESSION["username"]);
//    if ($user_info[4]) // if isRestaurant - exit
//        return;
    $user_id = $user_info[0];
    $friendly_name = $user_info[2];

    
    // when everything is finished mark with status READY
    $comm->setStatus(CommStatus::READY);
}
?>

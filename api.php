<?php
require 'includes/conn.php';
require 'includes/session.php';
require 'includes/auth.php';
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
?>

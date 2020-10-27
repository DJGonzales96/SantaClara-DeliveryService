<?php
require 'includes/conn.php';
require 'includes/session.php';
require 'includes/auth.php';

$str = 'BLUH_ILAN';

$commState = (object) [
    'isRestaurant' => 'BLUH_NATHAN',
    'anArray' => array('status' => '1',
    'string_key' => $str,
    'string_value' => $str,
    'string_extra' => $str,
    'is_public' => $str,
    'is_public_for_contacts' => $str)
];

echo json_encode($commState);
?>

<?php
require 'conn.php';
require 'session.php';
require 'auth.php';

$str = 'BLUH';

$commState = (object) [
    'isRestaurant' => 'BLUH',
    'anArray' => array('status' => '1',
    'string_key' => $str,
    'string_value' => $str,
    'string_extra' => $str,
    'is_public' => $str,
    'is_public_for_contacts' => $str)
];

echo json_encode($commState);
?>

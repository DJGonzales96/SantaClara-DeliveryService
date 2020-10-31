<?php
// make sure no unauthorized access
if ($_SESSION['authenticated'] != true || $_SESSION["username"] == NULL)
    die("Not logged in");

function getGeocode(String $address)
{
    $base_url = "https://maps.googleapis.com/maps/api/geocode/json";
    $api_key = "AIzaSyByH9xCzlvuJOBZkKgJgLnCn2xe9mFd-Tg";
    $curl = curl_init();
    $data = ["address" => $address,"key" => $api_key];
    $url = sprintf("%s?%s", $base_url, http_build_query($data));
    curl_setopt($curl, CURLOPT_URL, $url);
    $result = curl_exec($curl);
    curl_close($curl);
    return json_decode($result);
}


?>

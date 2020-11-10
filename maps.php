<?php
// make sure no unauthorized access
if ($_SESSION['authenticated'] != true || $_SESSION["username"] == NULL)
    die("{'status':'STATUS_ERROR','error':'Not logged in'}");

// This Maps API GeoCoding service will probably not be used
function getGeocode(String $address)
{
    $base_url = "https://maps.googleapis.com/maps/api/geocode/json";
    $api_key = "AIzaSyByH9xCzlvuJOBZkKgJgLnCn2xe9mFd-Tg";
    $curl = curl_init();
    $data = ["address" => $address,"key" => $api_key];
    $url = sprintf("%s?%s", $base_url, http_build_query($data));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_URL, $url);
    $response = curl_exec($curl);
    curl_close($curl);
    $result = json_decode($response);
    //var_dump($result); //->{"results"}[0]
    return $result;
}
?>

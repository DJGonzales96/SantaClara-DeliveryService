<?php
// make sure no unauthorized access
if ($_SESSION['authenticated'] != true || $_SESSION["username"] == NULL)
    die("{'status':'STATUS_ERROR','error':'Not logged in'}");

// This is Maps API GeoCoding service
function getMapsLocationFromFriendlyAddress(String $address)
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
    $json = json_decode($response);
    $result = array(
        $json->{'results'}[0]->{'geometry'}->{'location'}->{'lat'},
        $json->{'results'}[0]->{'geometry'}->{'location'}->{'lng'},
        $json->{'results'}[0]->{'formatted_address'}
    );
    //var_dump($json); //->{"results"}[0]
    //print_r($result);
    return $result;
}

// This is Maps API GeoCoding service
function getMapsDistanceDurationTwoPts($o_lat,$o_lon,$d_lat,$d_lon)
{
    $base_url = "https://maps.googleapis.com/maps/api/distancematrix/json";
    $api_key = "AIzaSyByH9xCzlvuJOBZkKgJgLnCn2xe9mFd-Tg";
    $curl = curl_init();
    $data = ["units" => "imperial","origins" => $o_lat.','.$o_lon,
        "destinations" => $d_lat.','.$d_lon ,"key" => $api_key];
    $url = sprintf("%s?%s", $base_url, http_build_query($data));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_URL, $url);
    $response = curl_exec($curl);
    curl_close($curl);
    $json = json_decode($response,true);
    $result = array(
        $json['rows'][0]['elements'][0]['distance']['value']/1600,
        $json['rows'][0]['elements'][0]['duration']['value']/60
    );
//    var_dump($json);
//    print_r($result);
    return $result;
}

// This is Maps API GeoCoding service
function getMapsFriendlyAddressFromLatLng($lat, $lon)
{
    $base_url = "https://maps.googleapis.com/maps/api/geocode/json";
    $api_key = "AIzaSyByH9xCzlvuJOBZkKgJgLnCn2xe9mFd-Tg";
    $curl = curl_init();
    $data = ["latlng" => $lat.','.$lon,"key" => $api_key];
    $url = sprintf("%s?%s", $base_url, http_build_query($data));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_URL, $url);
    $response = curl_exec($curl);
    curl_close($curl);
    $json = json_decode($response);
    $result = array(
        // $json->{'results'}[0]->{'geometry'}->{'location'}->{'lat'},
        // $json->{'results'}[0]->{'geometry'}->{'location'}->{'lng'},
        $json->{'results'}[0]->{'formatted_address'}
    );
//    var_dump($json); //->{"results"}[0]
//    print_r($result);
    return $result;
}
?>

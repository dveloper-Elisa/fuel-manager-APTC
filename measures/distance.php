<?php

// include "districts.php";


// $fromm = $_POST['district'];
// $too = $_POST['dest'];

function getCoordinates($location)
{
    $apiKey = "5b3ce3597851110001cf6248bc0a19586378485b99d8d7ce196af348"; // Replace with your actual API key
    $url = "https://api.openrouteservice.org/geocode/search?api_key=$apiKey&text=" . urlencode($location);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Accept: application/json"));

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        echo "cURL error: " . curl_error($ch);
    }
    curl_close($ch);

    // Log the raw response for debugging
    // echo "Geocode API Response: " . $response . "\n";

    $data = json_decode($response, true);


    if (!empty($data['features'][0]['geometry']['coordinates'])) {
        return [
            'lon' => $data['features'][0]['geometry']['coordinates'][0],
            'lat' => $data['features'][0]['geometry']['coordinates'][1]
        ];
    } else {
        $response = "Error: No coordinates found for location: $location\n";
        return $response;
    }
}

function getOSMDistance($startLocation, $endLocation)
{
    $apiKey = "5b3ce3597851110001cf6248bc0a19586378485b99d8d7ce196af348"; // Replace with your actual API key

    // Convert locations to coordinates
    $startCoords = getCoordinates($startLocation);
    $endCoords = getCoordinates($endLocation);


    $startLon = $startCoords['lon'];
    $startLat = $startCoords['lat'];
    $endLon = $endCoords['lon'];
    $endLat = $endCoords['lat'];


    // Fetch distance from ORS Directions API
    $url = "https://api.openrouteservice.org/v2/directions/driving-car?api_key=$apiKey&start=$startLon,$startLat&end=$endLon,$endLat";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Accept: application/geo+json;charset=UTF-8"));

    $response = curl_exec($ch);
    if (!$response) {
        return "You entered invalid place";
    }
    if (curl_errno($ch)) {
        echo "cURL error: " . curl_error($ch);
    }
    curl_close($ch);

    $data = json_decode($response, true);

    // Check if 'routes' and 'segments' are in the response
    if (isset($data['features'][0]['properties']['segments'][0]['distance'])) {

        $distanceInMeters = $data['features'][0]['properties']['segments'][0]['distance'];
        $distanceInKm = $distanceInMeters / 1000;
        return round($distanceInKm, 2);
    } else {
        return "Error fetching distance. Response: " . json_encode($data);
    }
}

// Places
$startLocation = $from . ", Rwanda";
$endLocation = $Destination . ", Rwanda";

$distance = getOSMDistance($startLocation, $endLocation);
// echo "Driving Distance: " . $distance;

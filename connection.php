<?php

$server = "localhost";
$base = "aptc_fab";
$user = "root";
$pass = "";
$port = 33060;

$db = mysqli_connect($server, $user, $pass, $base, $port);
if (!$db) {
    die("DB Connection Failed: " . mysqli_connect_error());
}

?>
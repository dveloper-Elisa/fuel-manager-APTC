<?php

$server = "localhost";
$base = "logistics";
$user = "root";
$pass = "";
$port = 3306;

$db = mysqli_connect($server, $user, $pass, $base, $port);
if (!$db) {
    die("DB Connection Failed: " . mysqli_connect_error());
}

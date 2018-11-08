<?php
$host     = "<DB_HOST>"; // Database Host
$user     = "<DB_USER>"; // Database Username
$password = "<DB_PASSWORD>"; // Database's user Password
$database = "<DB_NAME>"; // Database Name
$prefix   = "<DB_PREFIX>"; // Database Prefix for the script tables

$connect = mysqli_connect($host, $user, $password, $database);

// Checking Connection
if (mysqli_connect_errno()) {
    echo "Failed to connect with MySQL: " . mysqli_connect_error();
}

mysqli_set_charset($connect, "utf8");

$client = "<CLIENT>";

$site_url             = "<SITE_URL>";
$projectsecurity_path = "<PROJECTSECURITY_PATH>";
?>
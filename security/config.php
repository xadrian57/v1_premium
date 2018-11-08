<?php
$host     = "localhost"; // Database Host
$user     = "roihero_security"; // Database Username
$password = "9m+$53y-D4rb"; // Database's user Password
$database = "roihero_security"; // Database Name
$prefix   = "roihero_"; // Database Prefix for the script tables

$connect = mysqli_connect($host, $user, $password, $database);

// Checking Connection
if (mysqli_connect_errno()) {
    echo "Failed to connect with MySQL: " . mysqli_connect_error();
}

mysqli_set_charset($connect, "utf8");

$client = "No";

$site_url             = "http://roihero.com.br";
$projectsecurity_path = "http://roihero.com.br/security";
?>
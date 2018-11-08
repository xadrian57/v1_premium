<?php
include "config.php";
include "modules/core.php";

//Checking if the visitor is in the Whitelist
$wtable = $prefix . 'ip-whitelist';
$wquery = mysqli_query($connect, "SELECT ip FROM `$wtable` WHERE ip='$ip'");
$wrow   = mysqli_num_rows($wquery);
if ($wrow == "0") {
    
    //Ban System
    include "modules/ban-system.php";
    
    //Checking if Project SECURITY is enabled
    $table  = $prefix . 'settings';
    $squery = mysqli_query($connect, "SELECT * FROM `$table`");
    $srow   = mysqli_fetch_assoc($squery);
    if ($srow['realtime_protection'] == "Yes") {
        include "modules/sqli-protection.php";
        include "modules/massrequests-protection.php";
        include "modules/proxy-protection.php";
        include "modules/spam-protection.php";
        include "modules/content-protection.php";
        include "modules/badbots-protection.php";
        include "modules/fakebots-protection.php";
        include "modules/headers-check.php";
        include "modules/tor-detection.php";
    }
    
}

include "modules/optimizations.php";
?>
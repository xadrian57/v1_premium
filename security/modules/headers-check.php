<?php
@$accept_header = $_SERVER['HTTP_ACCEPT'];

//Detect Missing User-Agent Header
if (empty($useragent)) {
    
    include "lib/ip_details.php";
    
    $type = "Missing User-Agent header";
    
    //Logging
    $ltable     = $prefix . 'logs';
    $queryvalid = mysqli_query($connect, "SELECT ip, type FROM `$ltable` WHERE ip='$ip' and type='$type' LIMIT 1");
    $validator  = mysqli_num_rows($queryvalid);
    if ($validator <= "0") {
        $log = mysqli_query($connect, "INSERT INTO `$ltable` (ip, date, time, page, type, browser, browser_code, os, os_code, country, country_code, region, city, latitude, longitude, isp, useragent, referer_url) VALUES ('$ip', '$date', '$time', '$page', '$type', '$browser', '$browser_code', '$os', '$os_code', '$country', '$country_code', '$region', '$city', '$latitude', '$longitude', '$isp', '$useragent', '$referer')");
    }
    
    echo '<meta http-equiv="refresh" content="0;url=' . $projectsecurity_path . '/pages/missing-useragent" />';
    exit;
}

//Detect Missing Header Accept
if (empty($accept_header)) {
    
    include "lib/ip_details.php";
    
    $type = "Missing header Accept";
    
    //Logging
    $ltable     = $prefix . 'logs';
    $queryvalid = mysqli_query($connect, "SELECT ip, type FROM `$ltable` WHERE ip='$ip' and type='$type' LIMIT 1");
    $validator  = mysqli_num_rows($queryvalid);
    if ($validator <= "0") {
        $log = mysqli_query($connect, "INSERT INTO `$ltable` (ip, date, time, page, type, browser, browser_code, os, os_code, country, country_code, region, city, latitude, longitude, isp, useragent, referer_url) VALUES ('$ip', '$date', '$time', '$page', '$type', '$browser', '$browser_code', '$os', '$os_code', '$country', '$country_code', '$region', '$city', '$latitude', '$longitude', '$isp', '$useragent', '$referer')");
    }
    
    echo '<meta http-equiv="refresh" content="0;url=' . $projectsecurity_path . '/pages/missing-header-accept" />';
    exit;
}
?>
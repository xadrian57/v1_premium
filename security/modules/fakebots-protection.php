<?php
@$hostname = gethostbyaddr($ip);

//Fake Googlebot Detection
if (strpos(strtolower($useragent), "googlebot") !== false) {
    if (strpos($hostname, "googlebot.com") !== false OR strpos($hostname, "google.com") !== false) {
    } else {
        
        include "lib/ip_details.php";
        
        $type = "Fake Bot";
        
        //Logging
        $ltable     = $prefix . 'logs';
        $queryvalid = mysqli_query($connect, "SELECT ip, type FROM `$ltable` WHERE ip='$ip' and type='$type' LIMIT 1");
        $validator  = mysqli_num_rows($queryvalid);
        if ($validator <= "0") {
            $log = mysqli_query($connect, "INSERT INTO `$ltable` (ip, date, time, page, type, browser, browser_code, os, os_code, country, country_code, region, city, latitude, longitude, isp, useragent, referer_url) VALUES ('$ip', '$date', '$time', '$page', '$type', '$browser', '$browser_code', '$os', '$os_code', '$country', '$country_code', '$region', '$city', '$latitude', '$longitude', '$isp', '$useragent', '$referer')");
        }
        
        echo '<meta http-equiv="refresh" content="0;url=' . $projectsecurity_path . '/pages/fakebot-detected" />';
        exit;
    }
}

//Fake Bingbot Detection
if (strpos(strtolower($useragent), "bingbot") !== false) {
    if (strpos($hostname, "search.msn.com") !== false) {
    } else {
        
        include "lib/ip_details.php";
        
        $type = "Fake Bot";
        
        //Logging
        $ltable     = $prefix . 'logs';
        $queryvalid = mysqli_query($connect, "SELECT ip, type FROM `$ltable` WHERE ip='$ip' and type='$type' LIMIT 1");
        $validator  = mysqli_num_rows($queryvalid);
        if ($validator <= "0") {
            $log = mysqli_query($connect, "INSERT INTO `$ltable` (ip, date, time, page, type, browser, browser_code, os, os_code, country, country_code, region, city, latitude, longitude, isp, useragent, referer_url) VALUES ('$ip', '$date', '$time', '$page', '$type', '$browser', '$browser_code', '$os', '$os_code', '$country', '$country_code', '$region', '$city', '$latitude', '$longitude', '$isp', '$useragent', '$referer')");
        }
        
        echo '<meta http-equiv="refresh" content="0;url=' . $projectsecurity_path . '/pages/fakebot-detected" />';
        exit;
    }
}

//Fake Yahoo Bot Detection
if (strpos(strtolower($useragent), "yahoo! slurp") !== false) {
    if (strpos($hostname, "yahoo.com") !== false OR strpos($hostname, "crawl.yahoo.net") OR strpos($hostname, "yandex.com") !== false) {
    } else {
        
        include "lib/ip_details.php";
        
        $type = "Fake Bot";
        
        //Logging
        $ltable     = $prefix . 'logs';
        $queryvalid = mysqli_query($connect, "SELECT ip, type FROM `$ltable` WHERE ip='$ip' and type='$type' LIMIT 1");
        $validator  = mysqli_num_rows($queryvalid);
        if ($validator <= "0") {
            $log = mysqli_query($connect, "INSERT INTO `$ltable` (ip, date, time, page, type, browser, browser_code, os, os_code, country, country_code, region, city, latitude, longitude, isp, useragent, referer_url) VALUES ('$ip', '$date', '$time', '$page', '$type', '$browser', '$browser_code', '$os', '$os_code', '$country', '$country_code', '$region', '$city', '$latitude', '$longitude', '$isp', '$useragent', '$referer')");
        }
        
        echo '<meta http-equiv="refresh" content="0;url=' . $projectsecurity_path . '/pages/fakebot-detected" />';
        exit;
    }
}
?>
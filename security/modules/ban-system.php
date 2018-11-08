<?php
//Ban System
$table       = $prefix . 'bans';
$querybanned = mysqli_query($connect, "SELECT ip FROM `$table` WHERE ip='$ip'");
$banned      = mysqli_num_rows($querybanned);
if ($banned > "0") {
    $bannedpage_url = $projectsecurity_path . "/pages/banned";
    echo '<meta http-equiv="refresh" content="0;url=' . $bannedpage_url . '" />';
    exit;
}

//Blocking Country
$table = $prefix . 'settings';
$query = mysqli_query($connect, "SELECT `countryban_blacklist` FROM `$table` WHERE id='1'");
$row   = mysqli_fetch_array($query);

$url = 'http://www.geoplugin.net/xml.gp?ip=' . $ip;
$ch  = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
curl_setopt($ch, CURLOPT_REFERER, "https://google.com");
$ccontent = curl_exec($ch);
curl_close($ch);

$country_v     = simplexml_load_string($ccontent);
$country_check = $country_v->geoplugin_countryName;

$table = $prefix . 'bans-country';
@$querybanned = mysqli_query($connect, "SELECT country FROM `$table` WHERE country='$country_check'");
$banned = mysqli_num_rows($querybanned);

if ($row['countryban_blacklist'] == "Yes") {
    if ($banned > "0") {
        $bannedcpage_url = $projectsecurity_path . "/pages/banned-country";
        echo '<meta http-equiv="refresh" content="0;url=' . $bannedcpage_url . '" />';
        exit;
    }
} else {
    if (strpos(strtolower($useragent), "googlebot") !== false OR strpos(strtolower($useragent), "bingbot") !== false OR strpos(strtolower($useragent), "yahoo! slurp") !== false) {
    } else {
        if ($banned <= "0") {
            $bannedcpage_url = $projectsecurity_path . "/pages/banned-country";
            echo '<meta http-equiv="refresh" content="0;url=' . $bannedcpage_url . '" />';
            exit;
        }
    }
}

//Blocking Browser
$table       = $prefix . 'bans-other';
$querybanned = mysqli_query($connect, "SELECT * FROM `$table` WHERE type='browser'");
while ($rowb = mysqli_fetch_array($querybanned)) {
    if (strpos($browser, $rowb['value']) !== false) {
        $blockedbpage_url = $projectsecurity_path . "/pages/blocked-browser.php";
        echo '<meta http-equiv="refresh" content="0;url=' . $blockedbpage_url . '" />';
        exit;
    }
}

//Blocking Operating System
$table       = $prefix . 'bans-other';
$querybanned = mysqli_query($connect, "SELECT * FROM `$table` WHERE type='os'");
while ($rowo = mysqli_fetch_array($querybanned)) {
    if (strpos($os, $rowo['value']) !== false) {
        $blockedopage_url = $projectsecurity_path . "/pages/blocked-os.php";
        echo '<meta http-equiv="refresh" content="0;url=' . $blockedopage_url . '" />';
        exit;
    }
}

//Blocking Internet Service Provider
$table       = $prefix . 'bans-other';
$querybanned = mysqli_query($connect, "SELECT * FROM `$table` WHERE type='isp'");
while ($rowi = mysqli_fetch_array($querybanned)) {
    if (strpos($isp, $rowi['value']) !== false) {
        $blockedipage_url = $projectsecurity_path . "/pages/blocked-isp.php";
        echo '<meta http-equiv="refresh" content="0;url=' . $blockedipage_url . '" />';
        exit;
    }
}
?>
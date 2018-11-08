<?php
//Tor Protection
$table = $prefix . 'tor-settings';
$query = mysqli_query($connect, "SELECT * FROM `$table`");
$row   = mysqli_fetch_assoc($query);
if ($row['protection'] == "Yes") {
    
    $dnsbl_lookup = array(
        "tor.dan.me.uk",
        "tor.dnsbl.sectoor.de"
    );
    $reverse_ip   = implode(".", array_reverse(explode(".", $ip)));
    
    foreach ($dnsbl_lookup as $host) {
        if (checkdnsrr($reverse_ip . "." . $host . ".", "A")) {
            
            include "lib/ip_details.php";
            
            $type = "TOR Detected";
            
            //Logging
            if ($row['logging'] == "Yes") {
                $ltable     = $prefix . 'logs';
                $queryvalid = mysqli_query($connect, "SELECT ip, type FROM `$ltable` WHERE ip='$ip' and type='$type' LIMIT 1");
                $validator  = mysqli_num_rows($queryvalid);
                if ($validator <= "0") {
                    $log = mysqli_query($connect, "INSERT INTO `$ltable` (`ip`, `date`, `time`, `page`, `type`, `browser`, `browser_code`, `os`, `os_code`, `country`, `country_code`, `region`, `city`, `latitude`, `longitude`, `isp`, `useragent`, `referer_url`) VALUES ('$ip', '$date', '$time', '$page', '$type', '$browser', '$browser_code', '$os', '$os_code', '$country', '$country_code', '$region', '$city', '$latitude', '$longitude', '$isp', '$useragent', '$referer')");
                }
            }
            
            //AutoBan
            if ($row['autoban'] == "Yes") {
                $btable        = $prefix . 'bans';
                $bansvalid     = mysqli_query($connect, "SELECT ip FROM `$btable` WHERE ip='$ip' LIMIT 1");
                $bansvalidator = mysqli_num_rows($bansvalid);
                if ($bansvalidator <= "0") {
                    $log = mysqli_query($connect, "INSERT INTO `$btable` (ip, date, time, reason, autoban) VALUES ('$ip', '$date', '$time', '$type', 'Yes')");
                }
            }
            
            //E-Mail Notification
            if ($srow['mail_notifications'] == "Yes" && $row['mail'] == "Yes") {
                $email   = "notifications@project-security.net";
                $to      = $srow['email'];
                $subject = 'Project SECURITY - ' . $type . '';
                $message = '
				<p style="padding:0; margin:0 0 11pt 0;line-height:160%; font-size:18px;">Details of Log - ' . $type . '</p>
				<p>IP Address: <strong>' . $ip . '</strong></p>
				<p>Date: <strong>' . $date . '</strong> at <strong>' . $time . '</strong></p>
				<p>Browser:  <strong>' . $browser . '</strong></p>
				<p>Operating System:  <strong>' . $os . '</strong></p>
				<p>Threat Type:  <strong>' . $type . '</strong> </p>
				<p>Page:  <strong>' . $page . '</strong> </p>
                <p>Referer URL:  <strong>' . $referer . '</strong> </p>
                <p>Site URL:  <strong>' . $site_url . '</strong> </p>
                <p>Project SECURITY URL:  <strong>' . $projectsecurity_path . '</strong> </p>
            ';
                $headers = 'MIME-Version: 1.0' . "\r\n";
                $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
                $headers .= 'To: ' . $to . ' <' . $to . '>' . "\r\n";
                $headers .= 'From: ' . $email . ' <' . $email . '>' . "\r\n";
                @mail($to, $subject, $message, $headers);
            }
            
            echo '<meta http-equiv="refresh" content="0;url=' . $projectsecurity_path . '/pages/tor-detected" />';
            exit;
        }
    }
    
}
?>
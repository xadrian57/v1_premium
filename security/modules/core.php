<?php
//Getting Real IP Address
function get_realip()
{
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if (getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if (getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if (getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if (getenv('HTTP_FORWARDED'))
        $ipaddress = getenv('HTTP_FORWARDED');
    else if (getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    return $ipaddress;
}

//Getting Browser and Operating System
include dirname(__FILE__) . '/lib/useragent.class.php';
$useragent_data = UserAgentFactory::analyze($_SERVER['HTTP_USER_AGENT']);

//Getting Visitor Information
$ip           = get_realip();
$page         = $_SERVER['PHP_SELF'];
$browser      = $useragent_data->browser['title'];
$browser_code = $useragent_data->browser['code'];
$os           = $useragent_data->os['title'];
$os_code      = $useragent_data->os['code'];
$useragent    = $_SERVER['HTTP_USER_AGENT'];
@$referer = $_SERVER["HTTP_REFERER"];
$date = date("d F Y");
$time = date("H:i");
?>
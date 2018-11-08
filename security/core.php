<?php
$configfile = 'config.php';
if (!file_exists($configfile)) {
    echo '<meta http-equiv="refresh" content="0; url=install" />';
    exit();
}

require 'config.php';

session_start();

if ($client == 'Yes') {
    echo '
<script>
if (window.frameElement) {
  //Access Granted
}
else {
  alert("Direct access is not allowed.");
  window.stop();
}
</script>
';
}

$version = "9.0";

if ($client == 'No') {
if (isset($_SESSION['sec-username'])) {
    $uname = $_SESSION['sec-username'];
    $table = $prefix . 'users';
    $suser = mysqli_query($connect, "SELECT * FROM `$table` WHERE username='$uname'");
    $count = mysqli_num_rows($suser);
    if ($count < 0) {
        echo '<meta http-equiv="refresh" content="0; url=index" />';
        exit;
    }
} else {
    echo '<meta http-equiv="refresh" content="0; url=index" />';
    exit;
}
}

$_GET  = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);
$_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

function get_banned($ip)
{
    require 'config.php';
    $table = $prefix . 'bans';
    $query = mysqli_query($connect, "SELECT * FROM `$table` WHERE ip='$ip' LIMIT 1");
    $count = mysqli_num_rows($query);
    if ($count > 0) {
        return 'Yes';
    } else {
        return 'No';
    }
}

function get_bannedid($ip)
{
    require 'config.php';
    $table = $prefix . 'bans';
    $query = mysqli_query($connect, "SELECT * FROM `$table` WHERE ip='$ip' LIMIT 1");
    $row   = mysqli_fetch_array($query);
    return $row['id'];
}

function head()
{
    require 'config.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
    <link rel="shortcut icon" href="assets/img/favicon.png">
    <title>Project SECURITY &rsaquo; Admin Panel</title>


    <!--STYLESHEET-->
    <!--=================================================-->

    <!--Open Sans Font-->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">

    <!--Bootstrap Stylesheet-->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">

    <!--Stylesheet-->
    <link href="assets/css/admin.min.css" rel="stylesheet">
<?php
    if ($client == 'No') {
        echo '<link rel="stylesheet" href="assets/css/blue-skin.min.css">';
    } else {
        echo '<link rel="stylesheet" href="assets/css/purple-skin.min.css">';
    }
?>
    <!--Font Awesome-->
    <link href="assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet">

    <!--Morris.js-->
    <link href="assets/plugins/morris-js/morris.min.css" rel="stylesheet">

    <!--Switchery-->
    <link href="assets/plugins/switchery/switchery.min.css" rel="stylesheet">
        
    <!--Bootstrap Select-->
    <link href="assets/plugins/bootstrap-select/bootstrap-select.min.css" rel="stylesheet">

    <!--noUiSlider-->
    <link href="assets/plugins/noUiSlider/jquery.nouislider.min.css" rel="stylesheet">
	<link href="assets/plugins/noUiSlider/jquery.nouislider.pips.min.css" rel="stylesheet">
    
    <!--DataTables-->
    <link href="assets/plugins/datatables/media/css/dataTables.bootstrap.min.css" rel="stylesheet">
	<link href="assets/plugins/datatables/extensions/Responsive/css/responsive.bootstrap.min.css" rel="stylesheet">
    
    <!--Flags-->
    <link href="assets/plugins/flags/flags.css" rel="stylesheet">

    <!--SCRIPT-->
    <!--=================================================-->

    <!--jQuery-->
    <script src="assets/js/jquery-2.2.4.min.js"></script>
        
    <!--Google Charts-->
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    
</head>

<body class="hold-transition <?php
    if ($client == 'No') {
        echo 'skin-blue';
    } else {
        echo 'skin-purple';
    }
?> sidebar-mini">
<div class="wrapper">

  <header class="main-header">

    <a href="dashboard" class="logo">
      <span class="logo-mini">P<strong>SEC</strong></span>
      <span class="logo-lg">Project <strong>SECURITY</strong></span>
    </a>

    <nav class="navbar navbar-static-top">

      <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>

      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <li>
             <a href="<?php
    echo $site_url;
?>" target="_blank">
			 <span><i class="fa fa-desktop"></i>&nbsp;&nbsp;View Site</span>
			 </a>
          </li>
          <li>
             <a href="settings"><span><i class="fa fa-cogs"></i>&nbsp;&nbsp;Settings</span></a>
          </li>
<?php
    if ($client == 'No') {
        $uname = $_SESSION['sec-username'];
        $table = $prefix . 'users';
        $suser = mysqli_query($connect, "SELECT * FROM `$table` WHERE username='$uname'");
        $urow  = mysqli_fetch_array($suser);
?>
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <img src="assets/img/avatar.png" class="user-image" alt="Admin Image">
              <span class="hidden-xs"><?php
        echo $_SESSION['sec-username'];
?></span>
            </a>
            <ul class="dropdown-menu">

              <li class="user-header">
                <img src="assets/img/avatar.png" class="img-circle" alt="Admin Image">

                <p>
                  <?php
        echo $_SESSION['sec-username'];
?>
                  <small><?php
        echo $urow['email'];
?></small>
                </p>
              </li>

              <li class="user-footer">
                <div class="pull-left">
                  <a href="users?edit-id=<?php
        echo $urow['id'];
?>" class="btn btn-default btn-flat"><i class="fa fa-user fa-fw fa-lg"></i> Edit Profile</a>
                </div>
                <div class="pull-right">
                  <a href="logout" class="btn btn-default btn-flat"><i class="fa fa-sign-out fa-fw"></i> Logout</a>
                </div>
              </li>
            </ul>
          </li>
<?php
    }
?>
        </ul>
      </div>
    </nav>
  </header>

  <aside class="main-sidebar">

    <section class="sidebar">
<?php
    if ($client == 'No') {
?>
      <div class="user-panel">
        <div class="pull-left image">
          <img src="assets/img/avatar.png" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
          <p><?php
        echo $_SESSION['sec-username'];
?></p>
          <a href="#"><i class="fa fa-envelope-o"></i> <?php
        echo $urow['email'];
?></a>
        </div>
      </div>
<?php
    }
?>
      <form action="#" method="get" class="sidebar-form">
        <div class="input-group">
          <input type="text" name="q" class="form-control" placeholder="Search...">
              <span class="input-group-btn">
                <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
        </div>
      </form>

      <ul class="sidebar-menu">
        <li class="header">NAVIGATION</li>
        
        <li <?php
    if (basename($_SERVER['SCRIPT_NAME']) == 'dashboard.php') {
        echo 'class="active"';
    }
?>>
           <a href="dashboard">
              <i class="fa fa-home"></i> <span>Dashboard</span>
           </a>
        </li>
          
        <li <?php
    if (basename($_SERVER['SCRIPT_NAME']) == 'site-info.php') {
        echo 'class="active"';
    }
?>>
           <a href="site-info">
              <i class="fa fa-info-circle"></i> <span>Site Info</span>
           </a>
        </li>
          
        <li <?php
    if (basename($_SERVER['SCRIPT_NAME']) == 'ip-whitelist.php') {
        echo 'class="active"';
    }
?>>
           <a href="ip-whitelist">
              <i class="fa fa-flag-o"></i> <span>IP Whitelist</span>
           </a>
        </li>
          
        <li <?php
    if (basename($_SERVER['SCRIPT_NAME']) == 'warning-pages.php') {
        echo 'class="active"';
    }
?>>
           <a href="warning-pages">
              <i class="fa fa-file-text-o"></i> <span>Warning Pages</span>
           </a>
        </li>
   
<?php
if ($client == 'No') {
?>
        <li <?php
    if (basename($_SERVER['SCRIPT_NAME']) == 'users.php') {
        echo 'class="active"';
    }
?>>
           <a href="users">
              <i class="fa fa-users"></i> <span>Users</span>
           </a>
        </li>
<?php
}
?>
          
        <li <?php
    if (basename($_SERVER['SCRIPT_NAME']) == 'update-check.php') {
        echo 'class="active"';
    }
?>>
           <a href="update-check">
              <i class="fa fa-refresh"></i> <span>Check for Updates</span>
           </a>
        </li>

        <li class="header">SECURITY</li>
          
        <li <?php
    if (basename($_SERVER['SCRIPT_NAME']) == 'sql-injection.php') {
        echo 'class="active"';
    }
?>>
           <a href="sql-injection">
              <i class="fa fa-code"></i> <span>SQL Injection</span>
<?php
    $table = $prefix . 'sqli-settings';
    $query = mysqli_query($connect, "SELECT * FROM `$table`");
    $row   = mysqli_fetch_array($query);
    if ($row['protection'] == 'Yes') {
        echo '
              <small class="label pull-right bg-green">ON</small>
';
    } else {
        echo '
              <small class="label pull-right bg-red">OFF</small>
';
    }
?>     
           </a>
        </li>
          
        <li <?php
    if (basename($_SERVER['SCRIPT_NAME']) == 'mass-requests.php') {
        echo 'class="active"';
    }
?>>
           <a href="mass-requests">
              <i class="fa fa-retweet"></i> <span>Mass Requests</span>
<?php
    $table = $prefix . 'massrequests-settings';
    $query = mysqli_query($connect, "SELECT * FROM `$table`");
    $row   = mysqli_fetch_array($query);
    if ($row['protection'] == 'Yes') {
        echo '
              <small class="label pull-right bg-green">ON</small>
';
    } else {
        echo '
              <small class="label pull-right bg-red">OFF</small>
';
    }
?>     
           </a>
        </li>
          
        <li <?php
    if (basename($_SERVER['SCRIPT_NAME']) == 'spam.php') {
        echo 'class="active"';
    }
?>>
           <a href="spam">
              <i class="fa fa-keyboard-o"></i> <span>Spam</span>
<?php
    $table = $prefix . 'spam-settings';
    $query = mysqli_query($connect, "SELECT * FROM `$table`");
    $row   = mysqli_fetch_array($query);
    if ($row['protection'] == 'Yes') {
        echo '
              <small class="label pull-right bg-green">ON</small>
';
    } else {
        echo '
              <small class="label pull-right bg-red">OFF</small>
';
    }
?>     
           </a>
        </li>
          
        <li <?php
    if (basename($_SERVER['SCRIPT_NAME']) == 'proxy.php') {
        echo 'class="active"';
    }
?>>
           <a href="proxy">
              <i class="fa fa-globe"></i> <span>Proxy</span>
<?php
    $table = $prefix . 'proxy-settings';
    $query = mysqli_query($connect, "SELECT * FROM `$table`");
    $row   = mysqli_fetch_array($query);
    if ($row['protection'] == 'Yes') {
        echo '
              <small class="label pull-right bg-green">ON</small>
';
    } else {
        echo '
              <small class="label pull-right bg-red">OFF</small>
';
    }
?>     
           </a>
        </li>
          
        <li <?php
    if (basename($_SERVER['SCRIPT_NAME']) == 'tor-detection.php') {
        echo 'class="active"';
    }
?>>
           <a href="tor-detection">
              <i class="fa fa-globe"></i> <span>Tor Detection</span>
<?php
    $table = $prefix . 'tor-settings';
    $query = mysqli_query($connect, "SELECT * FROM `$table`");
    $row   = mysqli_fetch_array($query);
    if ($row['protection'] == 'Yes') {
        echo '
              <small class="label pull-right bg-green">ON</small>
';
    } else {
        echo '
              <small class="label pull-right bg-red">OFF</small>
';
    }
?>     
           </a>
        </li>
        
<?php
    $table   = $prefix . 'logs';
    $lquery1 = mysqli_query($connect, "SELECT * FROM $table");
    $lcount1 = mysqli_num_rows($lquery1);
    $lquery2 = mysqli_query($connect, "SELECT * FROM $table WHERE type='SQLi'");
    $lcount2 = mysqli_num_rows($lquery2);
    $lquery3 = mysqli_query($connect, "SELECT * FROM $table WHERE type='Mass Requests'");
    $lcount3 = mysqli_num_rows($lquery3);
    $lquery4 = mysqli_query($connect, "SELECT * FROM $table WHERE type='Spammer'");
    $lcount4 = mysqli_num_rows($lquery4);
    $lquery5 = mysqli_query($connect, "SELECT * FROM $table WHERE type='Proxy'");
    $lcount5 = mysqli_num_rows($lquery5);
?>
        <li class="treeview  <?php
    if (basename($_SERVER['SCRIPT_NAME']) == 'all-logs.php' OR basename($_SERVER['SCRIPT_NAME']) == 'sqli-logs.php' OR basename($_SERVER['SCRIPT_NAME']) == 'massrequest-logs.php' OR basename($_SERVER['SCRIPT_NAME']) == 'proxy-logs.php' OR basename($_SERVER['SCRIPT_NAME']) == 'spammer-logs.php' OR basename($_SERVER['SCRIPT_NAME']) == 'other-logs.php' OR basename($_SERVER['SCRIPT_NAME']) == 'log-details.php') {
        echo 'active';
    }
?>">
           <a href="#">
              <i class="fa fa-align-justify"></i> <span>Logs</span> <i class="fa fa-angle-right pull-right"></i>
           </a>
           <ul class="treeview-menu">
               <li <?php
    if (basename($_SERVER['SCRIPT_NAME']) == 'all-logs.php') {
        echo 'class="active"';
    }
?>><a href="all-logs"><i class="fa fa-align-justify"></i> All Logs <small class="label pull-right bg-purple"><?php
    echo $lcount1;
?></small></a></li>
               <li <?php
    if (basename($_SERVER['SCRIPT_NAME']) == 'sqli-logs.php') {
        echo 'class="active"';
    }
?>><a href="sqli-logs"><i class="fa fa-code"></i> SQLi Logs <small class="label pull-right bg-blue"><?php
    echo $lcount2;
?></small></a></li>
               <li <?php
    if (basename($_SERVER['SCRIPT_NAME']) == 'massrequest-logs.php') {
        echo 'class="active"';
    }
?>><a href="massrequest-logs"><i class="fa fa-retweet"></i> Mass Requests <small class="label pull-right bg-red"><?php
    echo $lcount3;
?></small></a></li>
               <li <?php
    if (basename($_SERVER['SCRIPT_NAME']) == 'spammer-logs.php') {
        echo 'class="active"';
    }
?>><a href="spammer-logs"><i class="fa fa-keyboard-o"></i> Spam Logs <small class="label pull-right bg-orange"><?php
    echo $lcount4;
?></small></a></li>
               <li <?php
    if (basename($_SERVER['SCRIPT_NAME']) == 'proxy-logs.php') {
        echo 'class="active"';
    }
?>><a href="proxy-logs"><i class="fa fa-globe"></i> Proxy Logs <small class="label pull-right bg-green"><?php
    echo $lcount5;
?></small></a></li>
               <li <?php
    if (basename($_SERVER['SCRIPT_NAME']) == 'other-logs.php') {
        echo 'class="active"';
    }
?>><a href="other-logs"><i class="fa fa-user-secret"></i> Other Logs</a></li>
           </ul>
        </li>
          
<?php
    $table   = $prefix . 'bans';
    $bquery1 = mysqli_query($connect, "SELECT * FROM `$table`");
    $bcount1 = mysqli_num_rows($bquery1);
    $table2  = $prefix . 'bans-country';
    $bquery2 = mysqli_query($connect, "SELECT * FROM `$table2`");
    $bcount2 = mysqli_num_rows($bquery2);
?>
        <li class="treeview  <?php
    if (basename($_SERVER['SCRIPT_NAME']) == 'bans-ip.php' OR basename($_SERVER['SCRIPT_NAME']) == 'bans-country.php' OR basename($_SERVER['SCRIPT_NAME']) == 'bans-other.php') {
        echo 'active';
    }
?>">
           <a href="#">
              <i class="fa fa-ban"></i> <span>Bans</span> <i class="fa fa-angle-right pull-right"></i>
           </a>
           <ul class="treeview-menu">
               <li <?php
    if (basename($_SERVER['SCRIPT_NAME']) == 'bans-ip.php') {
        echo 'class="active"';
    }
?>><a href="bans-ip"><i class="fa fa-user"></i> IP Bans <small class="label pull-right bg-red"><?php
    echo $bcount1;
?></small></a></li>
               <li <?php
    if (basename($_SERVER['SCRIPT_NAME']) == 'bans-country.php') {
        echo 'class="active"';
    }
?>><a href="bans-country"><i class="fa fa-globe"></i> Country Bans <small class="label pull-right bg-red"><?php
    echo $bcount2;
?></small></a></li>
               <li <?php
    if (basename($_SERVER['SCRIPT_NAME']) == 'bans-other.php') {
        echo 'class="active"';
    }
?>><a href="bans-other"><i class="fa fa-desktop"></i> Other Bans</a></li>
           </ul>
        </li>
          
        <li <?php
    if (basename($_SERVER['SCRIPT_NAME']) == 'malware-scanner.php') {
        echo 'class="active"';
    }
?>>
           <a href="malware-scanner">
              <i class="fa fa-search"></i> <span>Malware Scanner</span>
           </a>
        </li>
          
        <li <?php
    if (basename($_SERVER['SCRIPT_NAME']) == 'security-check.php') {
        echo 'class="active"';
    }
?>>
           <a href="security-check">
              <i class="fa fa-check"></i> <span>Security Check</span>
           </a>
        </li>
        
        <li <?php
    if (basename($_SERVER['SCRIPT_NAME']) == 'content-protection.php') {
        echo 'class="active"';
    }
?>>
           <a href="content-protection">
              <i class="fa fa-file-text"></i> <span>Content Protection</span>
           </a>
        </li>
         
<?php
if ($client == 'No') {
?>
        <li class="header">MONITORING</li>
          
        <li <?php
    if (basename($_SERVER['SCRIPT_NAME']) == 'website-monitoring.php') {
        echo 'class="active"';
    }
?>>
           <a href="website-monitoring">
              <i class="fa fa-desktop"></i> <span>Website Monitoring</span>
           </a>
        </li>
<?php
}
?>
          
        <li class="header">OPTIMIZATIONS</li>
          
        <li <?php
    if (basename($_SERVER['SCRIPT_NAME']) == 'optimizations.php') {
        echo 'class="active"';
    }
?>>
           <a href="optimizations">
              <i class="fa fa-rocket"></i> <span>Website Optimizations</span>
           </a>
        </li>
          
        <li class="header">TOOLS</li>
          
        <li <?php
    if (basename($_SERVER['SCRIPT_NAME']) == 'htaccess-editor.php') {
        echo 'class="active"';
    }
?>>
           <a href="htaccess-editor">
              <i class="fa fa-columns"></i> <span>.htacces Editor</span>
           </a>
        </li>
          
        <li <?php
    if (basename($_SERVER['SCRIPT_NAME']) == 'error-monitoring.php') {
        echo 'class="active"';
    }
?>>
           <a href="error-monitoring">
              <i class="fa fa-exclamation-circle"></i> <span>Error Monitoring</span>
           </a>
        </li>
          
<?php
if ($client == 'No') {
?>
        <li <?php
    if (basename($_SERVER['SCRIPT_NAME']) == 'html-encrypter.php') {
        echo 'class="active"';
    }
?>>
           <a href="html-encrypter">
              <i class="fa fa-code"></i> <span>HTML Encrypter</span>
           </a>
        </li>
          
        <li <?php
    if (basename($_SERVER['SCRIPT_NAME']) == 'password-generator.php') {
        echo 'class="active"';
    }
?>>
           <a href="password-generator">
              <i class="fa fa-key"></i> <span>Password Generator</span>
           </a>
        </li>
          
        <li <?php
    if (basename($_SERVER['SCRIPT_NAME']) == 'hashing.php') {
        echo 'class="active"';
    }
?>>
           <a href="hashing">
              <i class="fa fa-lock"></i> <span>Hashing</span>
           </a>
        </li>
<?php
}
?>
          
      </ul>
    </section>

  </aside>
<?php
}

function footer()
{
    require('config.php');
?>
<footer class="main-footer">
    <strong>&copy; <?php
    echo date("Y");
?> <a href="http://codecanyon.net/item/project-security-website-security-malware-vulnerability-scanner/15487703?ref=Antonov_WEB" target="_blank">Project SECURITY</a></strong>
    
    <a href="#" class="go-top"><i class="fa fa-arrow-up"></i></a>
</footer>

</div>

    <!--JAVASCRIPT-->
    <!--=================================================-->

    <!--BootstrapJS-->
    <script src="assets/js/bootstrap.min.js"></script>

    <!--Fast Click-->
    <script src="assets/plugins/fast-click/fastclick.min.js"></script>

    <!--Admin-->
    <script src="assets/js/admin.min.js"></script>

    <!--Morris.js-->
    <script src="assets/plugins/morris-js/morris.min.js"></script>
	<script src="assets/plugins/morris-js/raphael-js/raphael.min.js"></script>

    <!--Switchery-->
    <script src="assets/plugins/switchery/switchery.min.js"></script>
    
    <!--Bootstrap Select-->
    <script src="assets/plugins/bootstrap-select/bootstrap-select.min.js"></script>
    
    <!--noUiSlider-->
    <script src="assets/plugins/noUiSlider/jquery.nouislider.all.min.js"></script>
    
    <!--DataTables-->
    <script src="assets/plugins/datatables/media/js/jquery.dataTables.min.js"></script>
	<script src="assets/plugins/datatables/media/js/dataTables.bootstrap.min.js"></script>
    <script src="assets/plugins/datatables/extensions/Responsive/js/dataTables.responsive.min.js"></script>
    <script src="assets/plugins/datatables/extensions/Responsive/js/responsive.bootstrap.min.js"></script>
    
    <!--Google Maps-->
    <script src="https://maps.google.com/maps/api/js?sensor=true"></script>
	<script src="assets/plugins/gmaps/gmaps.js"></script>

</body>
    
</html>
<?php
}
?>
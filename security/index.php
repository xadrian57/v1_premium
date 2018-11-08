<?php
$configfile = 'config.php';
if (!file_exists($configfile)) {
    echo '<meta http-equiv="refresh" content="0; url=install" />';
    exit();
}

include "config.php";

session_start();

if ($client == 'Yes') {
    echo '
<script>
if (window.frameElement) {
  window.location.href = "dashboard";
}
else {
  alert("Direct access is not allowed.");
  window.stop();
}
</script>
';
} else {
    
    if (isset($_SESSION['sec-username'])) {
        $uname = $_SESSION['sec-username'];
        $table = $prefix . 'users';
        $suser = mysqli_query($connect, "SELECT * FROM `$table` WHERE username='$uname'");
        $count = mysqli_num_rows($suser);
        if ($count > 0) {
            echo '<meta http-equiv="refresh" content="0; url=dashboard" />';
            exit;
        }
    }
    
    $_GET  = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);
    $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
    
    $error = "No";
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
        <title>Project SECURITY &rsaquo; Admin Panel</title>

        <!-- CSS -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:400,100,300,500">
        <link rel="stylesheet" href="assets/css/bootstrap.min.css">
        <link rel="stylesheet" href="assets/plugins/font-awesome/css/font-awesome.min.css">
        <link rel="stylesheet" href="assets/css/admin.min.css">

        <!-- Favicon -->
        <link rel="shortcut icon" href="assets/img/favicon.png">
    </head>

    <body class="hold-transition login-page">
        
<div class="login-box">
    <div class="login-logo">
        <a href="index"><i class="fa fa-get-pocket"></i> Project <strong>SECURITY</strong></a>
    </div>
    <div class="login-box-body">
        <p class="login-box-msg">Reliable protection for every site</p>
<?php
    if (isset($_POST['signin'])) {
        $username = mysqli_real_escape_string($connect, $_POST['username']);
        $password = base64_encode($_POST['password']);
        $table    = $prefix . "users";
        $check    = mysqli_query($connect, "SELECT username, password FROM `$table` WHERE `username`='$username' AND password='$password'");
        if (mysqli_num_rows($check) > 0) {
            $_SESSION['sec-username'] = $username;
            echo '<meta http-equiv="refresh" content="0;url=dashboard">';
        } else {
            echo '<br />
		<div class="callout callout-danger">
              <i class="fa fa-exclamation-circle"></i> The entered <strong>Username</strong> or <strong>Password</strong> is incorrect.
        </div>';
            $error = "Yes";
        }
    }
?> 
        <form action="" method="post">
            <div class="input-group form-group has-feedback <?php
    if ($error == "Yes") {
        echo 'has-error';
    }
?>">
                <span class="input-group-addon"><i class="fa fa-user"></i></span>
                <input type="username" name="username" class="form-control" placeholder="Username" <?php
    if ($error == "Yes") {
        echo 'autofocus';
    }
?> required>
            </div>
            <div class="input-group form-group has-feedback">
                <span class="input-group-addon"><i class="fa fa-key"></i></span>
                <input type="password" name="password" class="form-control" placeholder="Password" required>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <button type="submit" name="signin" class="btn btn-primary btn-block btn-flat btn-lg"><i class="fa fa-sign-in"></i>
&nbsp;Sign In</button>
                </div>
            </div>
        </form> 
    </div>
</div>

        <!-- Javascript -->
        <script src="assets/js/jquery-2.2.4.min.js"></script>
        <script src="assets/js/bootstrap.min.js"></script>

    </body>
</html>
<?php
}
?>
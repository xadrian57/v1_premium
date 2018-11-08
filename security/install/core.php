<?php
session_start();

include "settings.inc.php";
include "functions.inc.php";
include "languages.inc.php";

if (file_exists(CONFIG_FILE_PATH)) {
    echo '<meta http-equiv="refresh" content="0; url=../" />';
    exit;
}

function head()
{
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Project SECURITY - <?php
    echo lang_key("installation_wizard");
?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../assets/img/favicon.png">
    <meta charset="utf-8">
    <META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css" media="screen">
    <link rel="stylesheet" href="../assets/css/admin.min.css" media="screen">
    <link type="text/css" href="../assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet">  
    <script src="../assets/js/jquery-2.2.4.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
</head>

<body>

    <div class="container">
        <div class="page-header">
            <div class="row">
                <div class="col-lg-12">
                    <br /><center><h2><i class="fa fa-get-pocket"></i> Project SECURITY - <?php
    echo lang_key("installation_wizard");
?></h2></center><br />
                    <div class="bs-example">
                        <div class="jumbotron">
<?php
}

function footer()
{
?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
<?php
}
?>
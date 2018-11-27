<!DOCTYPE html>
<html lang="en" data-textdirection="ltr" class="loading">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="keywords" content="login ROI HERO, dashboard roi hero, dash roi hero, admin roi hero, admin da roi hero">
    <!--  -->    
    <link rel="stylesheet" type="text/css" href="dashboard/assets/css/all.min.css">
    <!--  -->
    <link rel="apple-touch-icon" sizes="57x57" href="dashboard/assets/images/ico/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="dashboard/assets/images/ico/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="dashboard/assets/images/ico/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="dashboard/assets/images/ico/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="dashboard/assets/images/ico/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="dashboard/assets/images/ico/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="dashboard/assets/images/ico/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="dashboard/assets/images/ico/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="dashboard/assets/images/ico/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192"  href="dashboard/assets/images/ico/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="dashboard/assets/images/ico/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="dashboard/assets/images/ico/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="dashboard/assets/images/ico/favicon-16x16.png">
    <link rel="manifest" href="dashboard/assets/images/ico/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="dashboard/assets/images/ico/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:300,300i,400,400i,500,500i%7COpen+Sans:300,300i,400,400i,600,600i,700,700i" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="dashboard/assets/fonts/feather/style.min.css">
    <link rel="stylesheet" type="text/css" href="dashboard/assets/fonts/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="dashboard/assets/fonts/ioicons/css/ionicons.min.css">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:300,300i,400,400i,500,500i%7COpen+Sans:300,300i,400,400i,600,600i,700,700i" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="dashboard/assets/css/app.min.css">

    <script type="text/javascript" src="dashboard/assets/js/jquery-3.2.1.min.js" assync></script> 
    <script type="text/javascript" src="dashboard/assets/js/form_validator/jqBootstrapValidation.js"></script>
    <title>Login ROI HERO</title>
    <link rel="apple-touch-icon" href="dashboard/assets/images/ico/apple-icon-120.png">
    <link rel="shortcut icon" type="image/x-icon" href="dashboard/assets/images/ico/favicon.ico">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:300,300i,400,400i,500,500i%7COpen+Sans:300,300i,400,400i,600,600i,700,700i" rel="stylesheet">
    <!-- BEGIN VENDOR CSS-->
    <link rel="stylesheet" type="text/css" href="dashboard/assets/fonts/feather/style.min.css">
    <link rel="stylesheet" type="text/css" href="dashboard/assets/fonts/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="dashboard/assets/fonts/flag-icon-css/css/flag-icon.min.css">
    <!-- END VENDOR CSS-->
    <!-- BEGIN STACK CSS-->
    <link rel="stylesheet" type="text/css" href="dashboard/assets/css/app.min.css">
    <!-- END STACK CSS-->
    <!-- BEGIN Page Level CSS-->
    <link rel="stylesheet" type="text/css" href="dashboard/assets/css/login-register.css">
    <!-- END Page Level CSS-->
    <!-- BEGIN Custom CSS-->
    <link rel="stylesheet" type="text/css" href="dashboard/assets/css/style.css">
    <link rel="stylesheet" type="text/css" href="dashboard/assets/css/form_validation/form.css">
    <!-- END Custom CSS-->
</head>
<body data-open="click" data-menu="vertical-menu" data-col="1-column" class="vertical-layout vertical-menu 1-column  blank-page blank-page" style="background: #2790F2;">
    <!-- ////////////////////////////////////////////////////////////////////////////-->
    <div class="app-content content container-fluid">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
            <section class="flexbox-container">
                <div class="col-md-6 offset-md-3 col-xl-4 offset-xl-4 col-xs-10 offset-xs-1 box-shadow-2 p-0">
                    <div class="logo-container text-center"><a href="https://www.roihero.com.br"><img alt="ROI HERO" src="dashboard/assets/images/logo/stack-logo.png" class="brand-logo"></a></div>
                    <div id="card-login" class="card border-grey border-lighten-3 m-0">
                        <div class="card-body collapse in">
                            <div class="card-block">                                
                                <div class="card-header no-border">
                                </div>
                                <form id="form-login">
                                    <fieldset class="form-group position-relative has-icon-left mb-0">
                                        <input type="text" class="form-control form-control-lg input-lg mb-1" id="user-name" name="email" placeholder="Seu email" required>
                                        <div class="form-control-position">
                                            <i class="ft-user"></i>
                                        </div>
                                    </fieldset>
                                    <fieldset class="form-group position-relative has-icon-left">
                                        <input type="password" class="form-control form-control-lg input-lg" id="user-password" name="password" placeholder="Sua senha" required>
                                        <div class="form-control-position">
                                            <i class="fa fa-key"></i>
                                        </div>
                                    </fieldset>
                                    <fieldset class="form-group row">
                                        <div class="col-md-6 col-xs-12 text-xs-center text-md-left"></div>                                    
                                        <div class="col-md-6 col-xs-12 text-xs-center text-md-right"><a href="recuperar" class="card-link">Esqueci Minha Senha</a></div>
                                    </fieldset>
                                    <button id="btn-login" type="submit" class="btn btn-primary btn-lg btn-block"><i class="ft-unlock"></i> Entrar</button>
                                </form>
                                <div class="card-footer">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<!-- ////////////////////////////////////////////////////////////////////////////-->
<link rel="stylesheet" type="text/css" href="assets/css/toastr.min.css">
<script type="text/javascript" src="assets/js/toastr.min.js"></script>
<script type="text/javascript" src="assets/js/lg.js"></script>
    <style type="text/css">
        .toast-message{
            opacity: 1!important;
        }
    </style>
</body>
</html>
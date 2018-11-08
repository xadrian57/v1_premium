<!DOCTYPE html>
<html lang="en" data-textdirection="ltr" class="loading">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="keywords" content="login ROI HERO, dashboard roi hero, dash roi hero, admin roi hero, admin da roi hero">
    <link rel="stylesheet" type="text/css" href="dashboard/assets/css/all.min.css">
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
    <title>Cadastrar Nova Senha</title>
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
    <!-- END Custom CSS-->
</head>
<body data-open="click" data-menu="vertical-menu" data-col="1-column" class="vertical-layout vertical-menu 1-column  blank-page blank-page" style="background: #2790F2;">
<!-- ////////////////////////////////////////////////////////////////////////////-->
<div class="app-content content container-fluid">
    <div class="content-wrapper">
        <div class="content-header row">
        </div>
        <div class="content-body"><section class="flexbox-container">
            <?php 
            if (!empty($_GET['token'])) { ?>
            <div class="col-md-4 offset-md-4 col-xs-10 offset-xs-1 box-shadow-2 p-0">
                <div class="card border-grey border-lighten-3 px-2 py-2 m-0">
                    <div class="card-header no-border pb-0">
                        <div class="card-title text-xs-center">
                            <img src="dashboard/assets/images/logo/stack-logo.png" alt="branding logo">
                        </div>
                    </div>
                    <div class="card-body collapse in">
                        <div class="card-block">
                            <form class="form-horizontal" action="resource/redefinir-senha.php?token=<?php echo $_GET['token'] ?>" method="post" novalidate>
                                <fieldset class="form-group position-relative has-icon-left">
                                    <input type="password" name="senha" class="form-control form-control-lg input-lg" id="senha" placeholder="Insira a nova senha" required>
                                    <div class="form-control-position">
                                        <i class="fa fa-key"></i>
                                    </div>
                                </fieldset>
                                <fieldset class="form-group position-relative has-icon-left">
                                    <input type="password" name="senhaConfirmacao" class="form-control form-control-lg input-lg" id="senha" placeholder="Confirme a senha<?php $_GET['token'] ?>" required>
                                    <div class="form-control-position">
                                        <i class="fa fa-key"></i>
                                    </div>
                                </fieldset>
                                <button type="submit" name="ForgotPassword" class="btn btn-outline-primary btn-lg btn-block"><i class="ft-check"></i> Confirmar</button>
                            </form>
                        </div>
                    </div>                    
                </div>
            </div>
            <?php }
            else { ?>
                <div class="offset-md-2 col-md-8">
                    <h1 class="text-bold white text-xs-center">Não foi possível redefinir a sua senha.<br> Por favor, verifique se o link que você acessou está correto.</h1>
                </div>
            <?php } ?>
        </section>
    </div>
</div>
</div>
<!-- ////////////////////////////////////////////////////////////////////////////-->
</body>
</html>
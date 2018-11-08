<?php
require_once('../../bd/conexao_bd_cadastro.php');


if (isset($_POST["ForgotPassword"])) {
    // Harvest submitted e-mail address
    if (filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        $email = $_POST["email"];
    }else{
        echo "endereço de email inválido";
        exit;
    }
    // Check to see if a user exists with this e-mail
    $select = "SELECT CLI_email FROM cliente WHERE CLI_email = '$email'";
    $query = mysqli_query($conCad,$select);
    $email = mysqli_fetch_array($query);

    if ($email["CLI_email"])
    {
        
    }
    else
        echo "0";
}


?>
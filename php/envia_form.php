<?php

$nome       = $_POST['nome'];
$email      = $_POST['email'];
$url        = $_POST['url'];
$plataforma = $_POST['plataforma'];
$conteudo   = $_POST['mensagem'];



$emailCopia = "";
$emailPrincipal = "";

if ($plataforma == "loja_integrada") {
    $emailCopia = 'paulo.castello.branco@roihero.com.br';
    $emailPrincipal = "marcos@roihero.com.br";
} elseif ($plataforma == "vtex" || $plataforma == "xtech" || $plataforma == "tray" || $plataforma == "propria") {
    $emailCopia = 'paulo.castello.branco@roihero.com.br';
    $emailPrincipal = "daniela.guimaraes@roihero.com.br";
    enviaEmailContato($emailCopia, $emailPrincipal);
} else{    
    
    $msg = '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
    <html>
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
            <title>Agradecemos o seu contato!</title>
                <style media="all" type="text/css">
                    td, p, h1, h3, a {
                        font-family: Helvetica, Arial, sans-serif;
                    }
                </style>
            
        </head>
        <body bgcolor="" TEXT="#3d3d3d" style="font-family: Helvetica, Arial, sans-serif; font-size: 14px; color: #3d3d3d;">
            <table style="width: 538px; background-color: #21A2E2;" align="center" cellspacing="0" cellpadding="0" border="0">
                <tr>
                    <td style="width:360px;">
                        <img alt="" title="" src="http://roihero.com.br/images/logo_roihero.png" style="border:none;border-radius:0px;display:block;outline:none;text-decoration:none;margin-left: auto;margin-right: auto;margin-top: 20px;margin-bottom: 20px;">
                    </td>
            
                </tr>
                <tr>
                <td bgcolor="#ffffff">
                    <table width="470" border="0" align="center" cellpadding="0" cellspacing="0" border="0" style="padding-left: 5px; padding-right: 5px; padding-bottom: 10px;">
                        <tr bgcolor="#ffffff">
                            <td style="padding-top: 32px;">
                                <span style="font-family: Helvetica, Arial, sans-serif;font-size: 24px;color: #1b4a76;font-weight: bold;">
                                    Olá, cliente					</span><br>
                            </td>
                        </tr>
                        <tr>
                        <td style="font-family: Helvetica, Arial, sans-serif;font-size: 14px; color: #3d3d3d; padding-top: 16px;">
                                <span><br>Tudo certo?<br><br>Infelizmente ainda não possuímos integração com a sua plataforma, portanto não será possível implementarmos a ROI Hero em sua loja. Assim que tivermos a integração, entraremos em contato com você.<br><br>Para mais informações, entre em contato por marcos@roihero.com.br<br><br><br>A Equipe ROI Hero agradece!</span>
                        </td>
                        </tr>
                        
                    </table>
                </td>
                </tr>			
                
            </table>
        </body>
    </html>';


    $headers .= "Reply-To: Roi Hero <atendimento@roihero.com.br>\r\n";
    $headers .= "Return-Path: Roi Hero <atendimento@roihero.com.br>\r\n";
    $headers .= "From: <atendimento@roihero.com.br>";
    $headers .= "Organization: Roi Hero - Recomendações Inteligentes\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "X-Priority: 3\r\n";
    $headers .= "X-Mailer: PHP". phpversion() ."\r\n";

    mail($email, "Obrigado por entrar em contato", $msg, $headers);
}


function enviaEmailContato($emailCopia, $emailPrincipal){
    global  $nome, 
            $email,
            $url,
            $plataforma;


    $headers .= "Reply-To: Roi Hero <atendimento@roihero.com.br>\r\n";
    $headers .= "Return-Path: Roi Hero <atendimento@roihero.com.br>\r\n";
    $headers .= "From: <".$email.">";

    if ($emailCopia != "") {
        $headers .= 'Cc: ' . $emailCopia . "\r\n";
    }

    $headers .= "Organization: Roi Hero - Recomendações Inteligentes\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/plain; charset=iso-8859-1\r\n";
    $headers .= "X-Priority: 3\r\n";
    $headers .= "X-Mailer: PHP". phpversion() ."\r\n";


    $message = "Um cliente entrou em contato:\n\n\nNome: " . $nome."\n\nEmail: ".$email."\n\nURL: ".$url."\n\nPlataforma: ".$plataforma."\n\nMensagem:".$conteudo;
    mail($emailPrincipal , "Cliente entrou em contato pela página provisória", $message,$headers);
}

?>
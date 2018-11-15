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
} elseif ($plataforma == "vtex" || $plataforma == "xtech" || $plataforma == "tray" || $plataforma == "traycommerce" || $plataforma == "propria") {
    $emailCopia = 'paulo.castello.branco@roihero.com.br';
    $emailPrincipal = "daniela.guimaraes@roihero.com.br";
    enviaEmailContato($emailCopia, $emailPrincipal);
} else{    
    
    $msg = '<!DOCTYPE html>
    <html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
    
        <head>
            <title></title>
            <!--[if !mso]><!-- -->
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <!--<![endif]-->
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <style type="text/css">
                #outlook a {
                    padding: 0;
                }
                
                .ReadMsgBody {
                    width: 100%;
                }
                
                .ExternalClass {
                    width: 100%;
                }
                
                .ExternalClass * {
                    line-height: 100%;
                }
                
                body {
                    margin: 0;
                    padding: 0;
                    -webkit-text-size-adjust: 100%;
                    -ms-text-size-adjust: 100%;
                }
                
                table,
                td {
                    border-collapse: collapse;
                    mso-table-lspace: 0pt;
                    mso-table-rspace: 0pt;
                }
                
                img {
                    border: 0;
                    height: auto;
                    line-height: 100%;
                    outline: none;
                    text-decoration: none;
                    -ms-interpolation-mode: bicubic;
                }
                
                p {
                    display: block;
                    margin: 13px 0;
                }
            </style>
            <!--[if !mso]><!-->
            <style type="text/css">
                @media only screen and (max-width:480px) {
                    @-ms-viewport {
                        width: 320px;
                    }
                    @viewport {
                        width: 320px;
                    }
                }
            </style>
            <!--<![endif]-->
            <!--[if mso]><xml>  <o:OfficeDocumentSettings>    <o:AllowPNG/>    <o:PixelsPerInch>96</o:PixelsPerInch>  </o:OfficeDocumentSettings></xml><![endif]-->
            <!--[if lte mso 11]><style type="text/css">  .outlook-group-fix {    width:100% !important;  }</style><![endif]-->
            <!--[if !mso]><!-->
            <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet" type="text/css">
            <style type="text/css">
                @import url(https://fonts.googleapis.com/css?family=Open+Sans);
            </style>
            <!--<![endif]-->
            <style type="text/css">
                @media only screen and (min-width:480px) {
                    .mj-column-per-100 {
                        width: 100%!important;
                    }
                }
            </style>
        </head>
        
        <body style="background: #FFFFFF;">
            <div class="mj-container" style="background-color:#FFFFFF;">
                <!--[if mso | IE]>      <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="600" align="center" style="width:600px;">        <tr>          <td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;">      <![endif]-->
                <table role="presentation" cellpadding="0" cellspacing="0" style="background:#21A2E2;font-size:0px;width:100%;" border="0">
                    <tbody>
                        <tr>
                            <td>
                                <div style="margin:0px auto;max-width:600px;">
                                    <table role="presentation" cellpadding="0" cellspacing="0" style="font-size:0px;width:100%;" align="center" border="0">
                                        <tbody>
                                            <tr>
                                                <td style="text-align:center;vertical-align:top;direction:ltr;font-size:0px;padding:26px 0px 26px 0px;">
                                                    <!--[if mso | IE]>      <table role="presentation" border="0" cellpadding="0" cellspacing="0">        <tr>          <td style="vertical-align:top;width:600px;">      <![endif]-->
                                                    <div class="mj-column-per-100 outlook-group-fix" style="vertical-align:top;display:inline-block;direction:ltr;font-size:13px;text-align:left;width:100%;">
                                                        <table role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
                                                            <tbody>
                                                                <tr>
                                                                    <td style="word-wrap:break-word;font-size:0px;padding:0px 0px 0px 0px;" align="center">
                                                                        <table role="presentation" cellpadding="0" cellspacing="0" style="border-collapse:collapse;border-spacing:0px;" align="center" border="0">
                                                                            <tbody>
                                                                                <tr>
                                                                                    <td style="width:360px;"><img alt="" title="" height="auto" src="https://topolio.s3-eu-west-1.amazonaws.com/uploads/5a85acd87d1a9/1519050141.jpg" style="border:none;border-radius:0px;display:block;font-size:13px;outline:none;text-decoration:none;width:100%;height:auto;" width="360"></td>
                                                                                </tr>
                                                                            </tbody>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <!--[if mso | IE]>      </td></tr></table>      <![endif]-->
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <!--[if mso | IE]>      </td></tr></table>      <![endif]-->
                <!--[if mso | IE]>      <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="600" align="center" style="width:600px;">        <tr>          <td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;">      <![endif]-->
                <table role="presentation" cellpadding="0" cellspacing="0" style="background:#FFFFFF;font-size:0px;width:100%;" border="0">
                    <tbody>
                        <tr>
                            <td>
                                <div style="margin:0px auto;max-width:600px;">
                                    <table role="presentation" cellpadding="0" cellspacing="0" style="font-size:0px;width:100%;" align="center" border="0">
                                        <tbody>
                                            <tr>
                                                <td style="text-align:center;vertical-align:top;direction:ltr;font-size:0px;padding:26px 0px 26px 0px;">
                                                    <!--[if mso | IE]>      <table role="presentation" border="0" cellpadding="0" cellspacing="0">        <tr>          <td style="vertical-align:top;width:600px;">      <![endif]-->
                                                    <div class="mj-column-per-100 outlook-group-fix" style="vertical-align:top;display:inline-block;direction:ltr;font-size:13px;text-align:left;width:100%;">
                                                        <table role="presentation" cellpadding="0" cellspacing="0" style="background:#FFFFFF;font-size:0px;width:100%;" border="0">
                                                            <tbody>
                                                                <tr>
                                                                    <td>
                                                                        <div style="margin:0px auto;max-width:600px;">
                                                                            <table role="presentation" cellpadding="0" cellspacing="0" style="font-size:0px;width:100%;" align="center" border="0">
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <td style="text-align:center;vertical-align:top;direction:ltr;font-size:0px;padding:26px 0px 26px 0px;">
                                                                                            <!--[if mso | IE]>      <table role="presentation" border="0" cellpadding="0" cellspacing="0">        <tr>          <td style="vertical-align:top;width:600px;">      <![endif]-->
                                                                                            <div class="mj-column-per-100 outlook-group-fix" style="vertical-align:top;display:inline-block;direction:ltr;font-size:13px;text-align:left;width:100%;">
                                                                                                <table role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
                                                                                                    <tbody>
                                                                                                        <tr>
                                                                                                            <td style="word-wrap:break-word;font-size:0px;padding:0px 0px 0px 0px;" align="justify">
                                                                                                                <div style="cursor:auto;color:#505050;font-family:Open Sans, sans-serif;;font-size:11px;line-height:22px;text-align:justify;">
                                                                                                                    <p class="MsoNormal"><span style="font-size:14px;"><font color="#3b3838" face="Open Sans, sans-serif"></font><br>Olá, '.$nome.', tudo certo?<br><br>Infelizmente ainda não possuímos integração com a sua plataforma, portanto não será possível implementarmos a ROI Hero em sua loja. Assim que tivermos a integração, entraremos em contato com você.<br> <br>
                                                        Para mais informações, entre em contato em marcos@roihero.com.br
                                                        <br><br>A Equipe ROI Hero agradece!</span></p>
                                                                                                                </div>
                                                                                                            </td>
                                                                                                        </tr>
                                                                                                    </tbody>
                                                                                                </table>
                                                                                            </div>
                                                                                            <!--[if mso | IE]>      </td></tr></table>      <![endif]-->
                                                                                        </td>
                                                                                    </tr>
                                                                                </tbody>
                                                                            </table>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <!--[if mso | IE]>      </td></tr></table>      <![endif]-->
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <!--[if mso | IE]>      </td></tr></table>      <![endif]-->
                <!--[if mso | IE]>      <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="600" align="center" style="width:600px;">        <tr>          <td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;">      <![endif]-->
                <table role="presentation" cellpadding="0" cellspacing="0" style="background:#FFFFFF;font-size:0px;width:100%;" border="0">
                    <tbody>
                        <tr>
                            <td>
                                <div style="margin:0px auto;max-width:600px;">
                                    <table role="presentation" cellpadding="0" cellspacing="0" style="font-size:0px;width:100%;" align="center" border="0">
                                        <tbody>
                                            <tr>
                                                <td style="text-align:center;vertical-align:top;direction:ltr;font-size:0px;padding:26px 0px 26px 0px;">
                                                    <!--[if mso | IE]>      <table role="presentation" border="0" cellpadding="0" cellspacing="0">        <tr>          <td style="vertical-align:top;width:600px;">      <![endif]-->
                                                    <div class="mj-column-per-100 outlook-group-fix" style="vertical-align:top;display:inline-block;direction:ltr;font-size:13px;text-align:left;width:100%;">
                                                        <table role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
                                                            <tbody></tbody>
                                                        </table>
                                                    </div>
                                                    <!--[if mso | IE]>      </td></tr></table>      <![endif]-->
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <!--[if mso | IE]>      </td></tr></table>      <![endif]-->
            </div>
        </body>
    
    </html>';


    $headers .= "Reply-To: Roi Hero <atendimento@roihero.com.br>\r\n";
    $headers .= "Return-Path: Roi Hero <atendimento@roihero.com.br>\r\n";
    $headers .= "From: <atendimento@roihero.com.br>";
    $headers .= "Organization: Roi Hero - Recomendações Inteligentes\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
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
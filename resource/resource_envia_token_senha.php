<?php
    include "../../dev/apps/HeroEmail/sendEmail.php";
    include '../bd/conexao_bd_cadastro.php';

    $email = mysqli_real_escape_string($conCad,$_POST['email']);

    function geraToken($email){

        $random = rand();
        $hash = md5(uniqid($email.$random));
        $hash .= rand();

        return $hash;

    }

    function enviaEmail($email, $token){
        $headers = "Reply-To: ROI HERO <atendimento@roihero.com.br>\r\n";
    		$headers .= "Return-Path: Roi Hero <atendimento@roihero.com.br>\r\n";
    		$headers .= "From: Roi Hero <atendimento@roihero.com.br>\r\n";
    		$headers .= "Organization: Roi Hero - Recomendações Inteligentes\r\n";
    		$headers .= "MIME-Version: 1.0\r\n";
    		$headers .= "Content-type: text/html; charset=utf-8\r\n";
    		$headers .= "X-Priority: 3\r\n";
        $headers .= "X-Mailer: PHP". phpversion() ."\r\n";
        
        $linkRecuperarSenha = "https://www.roihero.com.br/redefinir-senha.php?token=".$token;

        $msg = '<!DOCTYPE html>
        <html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
           <head>
              <title></title>
              <!--[if !mso]><!-- -->  
              <meta http-equiv="X-UA-Compatible" content="IE=edge">
              <!--<![endif]-->
              <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
              <meta name="viewport" content="width=device-width, initial-scale=1.0">
              <style type="text/css">  #outlook a { padding: 0; }  .ReadMsgBody { width: 100%; }  .ExternalClass { width: 100%; }  .ExternalClass * { line-height:100%; }  body { margin: 0; padding: 0; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }  table, td { border-collapse:collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; }  img { border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; }  p { display: block; margin: 13px 0; }</style>
              <!--[if !mso]><!-->
              <style type="text/css">  @media only screen and (max-width:480px) {    @-ms-viewport { width:320px; }    @viewport { width:320px; }  }</style>
              <!--<![endif]--><!--[if mso]>
              <xml>
                 <o:OfficeDocumentSettings>
                    <o:AllowPNG/>
                    <o:PixelsPerInch>96</o:PixelsPerInch>
                 </o:OfficeDocumentSettings>
              </xml>
              <![endif]--><!--[if lte mso 11]>
              <style type="text/css">  .outlook-group-fix {    width:100% !important;  }</style>
              <![endif]--><!--[if !mso]><!-->    
              <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet" type="text/css">
              <style type="text/css">        @import url(https://fonts.googleapis.com/css?family=Open+Sans);    </style>
              <!--<![endif]-->
              <style type="text/css">  @media only screen and (min-width:480px) {    .mj-column-per-100 { width:100%!important; }  }</style>
           </head>
           <body style="background: #FFFFFF;">
              <div class="mj-container" style="background-color:#FFFFFF;">
                 <!--[if mso | IE]>      
                 <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="600" align="center" style="width:600px;">
                    <tr>
                       <td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;">
                          <![endif]-->
                          <table role="presentation" cellpadding="0" cellspacing="0" style="background:#21A2E2;font-size:0px;width:100%;" border="0">
                             <tbody>
                                <tr>
                                   <td>
                                      <div style="margin:0px auto;max-width:600px;">
                                         <table role="presentation" cellpadding="0" cellspacing="0" style="font-size:0px;width:100%;" align="center" border="0">
                                            <tbody>
                                               <tr>
                                                  <td style="text-align:center;vertical-align:top;direction:ltr;font-size:0px;padding:26px 0px 26px 0px;">
                                                     <!--[if mso | IE]>      
                                                     <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                                        <tr>
                                                           <td style="vertical-align:top;width:600px;">
                                                              <![endif]-->
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
                                                              <!--[if mso | IE]>      
                                                           </td>
                                                        </tr>
                                                     </table>
                                                     <![endif]-->
                                                  </td>
                                               </tr>
                                            </tbody>
                                         </table>
                                      </div>
                                   </td>
                                </tr>
                             </tbody>
                          </table>
                          <!--[if mso | IE]>      
                       </td>
                    </tr>
                 </table>
                 <![endif]-->      <!--[if mso | IE]>      
                 <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="600" align="center" style="width:600px;">
                    <tr>
                       <td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;">
                          <![endif]-->
                          <table role="presentation" cellpadding="0" cellspacing="0" style="background:#FFFFFF;font-size:0px;width:100%;" border="0">
                             <tbody>
                                <tr>
                                   <td>
                                      <div style="margin:0px auto;max-width:600px;">
                                         <table role="presentation" cellpadding="0" cellspacing="0" style="font-size:0px;width:100%;" align="center" border="0">
                                            <tbody>
                                               <tr>
                                                  <td style="text-align:center;vertical-align:top;direction:ltr;font-size:0px;padding:26px 0px 26px 0px;">
                                                     <!--[if mso | IE]>      
                                                     <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                                        <tr>
                                                           <td style="vertical-align:top;width:600px;">
                                                              <![endif]-->
                                                              <div class="mj-column-per-100 outlook-group-fix" style="vertical-align:top;display:inline-block;direction:ltr;font-size:13px;text-align:left;width:100%;">
                                                                 <table role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
                                                                    <tbody>
                                                                       <tr>
                                                                          <td style="word-wrap:break-word;font-size:0px;padding:0px 0px 0px 0px;" align="justify">
                                                                             <div style="cursor:auto;color:#505050;font-family:Open Sans, sans-serif;;font-size:11px;line-height:22px;text-align:justify;">
                                                                                <p class="MsoNormal" style="margin-bottom:0cm;margin-bottom:.0001pt"><span style="font-size: 24px;"><b>Redefini&#xE7;&#xE3;o de senha</b></span></p>
                                                                                <p class="MsoNormal" style="margin-bottom:0cm;margin-bottom:.0001pt"><span style="font-size:14px;"><span style="color: rgb(59, 56, 56); font-family: " open sans",="" sans-serif;"="">Ol&#xE1;,</span></span></p>
                                                                                <p class="MsoNormal" style="margin-bottom:0cm;margin-bottom:.0001pt"><span style="font-size:14px;"><span style="color: rgb(59, 56, 56); font-family: " open sans",="" sans-serif;"="">Voc&#xEA; solicitou a redefini&#xE7;&#xE3;o de&#xA0;sua senha. Para redefinir sua senha&#xA0;<a href="'.$linkRecuperarSenha.'" target="_blank">clique&#xA0;aqui</a>. Ou acesse&#xA0;o link:</span></span></p>
                                                                                <p class="MsoNormal" style="margin-bottom:0cm;margin-bottom:.0001pt"><span style="font-size:14px;"><a href="'.$linkRecuperarSenha.'" target="_blank"><span style="color: rgb(59, 56, 56); font-family: " open sans",="" sans-serif;"="">'.$linkRecuperarSenha.'</span></a></span></p>
                                                                                <p class="MsoNormal" style="margin-bottom:0cm;margin-bottom:.0001pt"></p>
                                                                                <p class="MsoNormal" style="margin-bottom:0cm;margin-bottom:.0001pt"><span style="font-size:14px;">Com d&#xFA;vidas? Contate nosso suporte por <strong><em>atendimento@roihero.com.br.</em></strong></span></p>
                                                                                <p class="MsoNormal" style="margin-bottom:0cm;margin-bottom:.0001pt"></p>
                                                                                <p class="MsoNormal" style="margin-bottom:0cm;margin-bottom:.0001pt"></p>
                                                                             </div>
                                                                          </td>
                                                                       </tr>
                                                                       <tr>
                                                                          <td style="word-wrap:break-word;font-size:0px;padding:0px 0px 0px 0px;" align="center">
                                                                             <table role="presentation" cellpadding="0" cellspacing="0" style="border-collapse:collapse;border-spacing:0px;" align="center" border="0">
                                                                                <tbody>
                                                                                   <tr>
                                                                                      <td style="width:600px;"><img alt="" title="" height="auto" src="https://topolio.s3-eu-west-1.amazonaws.com/uploads/5a85acd87d1a9/1519419877.jpg" style="border:none;border-radius:0px;display:block;font-size:13px;outline:none;text-decoration:none;width:100%;height:auto;" width="600"></td>
                                                                                   </tr>
                                                                                </tbody>
                                                                             </table>
                                                                          </td>
                                                                       </tr>
                                                                    </tbody>
                                                                 </table>
                                                              </div>
                                                              <!--[if mso | IE]>      
                                                           </td>
                                                        </tr>
                                                     </table>
                                                     <![endif]-->
                                                  </td>
                                               </tr>
                                            </tbody>
                                         </table>
                                      </div>
                                   </td>
                                </tr>
                             </tbody>
                          </table>
                          <!--[if mso | IE]>      
                       </td>
                    </tr>
                 </table>
                 <![endif]-->      <!--[if mso | IE]>      
                 <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="600" align="center" style="width:600px;">
                    <tr>
                       <td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;">
                          <![endif]-->
                          <table role="presentation" cellpadding="0" cellspacing="0" style="background:#FFFFFF;font-size:0px;width:100%;" border="0">
                             <tbody>
                                <tr>
                                   <td>
                                      <div style="margin:0px auto;max-width:600px;">
                                         <table role="presentation" cellpadding="0" cellspacing="0" style="font-size:0px;width:100%;" align="center" border="0">
                                            <tbody>
                                               <tr>
                                                  <td style="text-align:center;vertical-align:top;direction:ltr;font-size:0px;padding:26px 0px 26px 0px;">
                                                     <!--[if mso | IE]>      
                                                     <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                                        <tr>
                                                           <td style="vertical-align:top;width:600px;">
                                                              <![endif]-->
                                                              <div class="mj-column-per-100 outlook-group-fix" style="vertical-align:top;display:inline-block;direction:ltr;font-size:13px;text-align:left;width:100%;">
                                                                 <table role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
                                                                    <tbody></tbody>
                                                                 </table>
                                                              </div>
                                                              <!--[if mso | IE]>      
                                                           </td>
                                                        </tr>
                                                     </table>
                                                     <![endif]-->
                                                  </td>
                                               </tr>
                                            </tbody>
                                         </table>
                                      </div>
                                   </td>
                                </tr>
                             </tbody>
                          </table>
                          <!--[if mso | IE]>      
                       </td>
                    </tr>
                 </table>
                 <![endif]-->
              </div>
           </body>
        </html>';

        //$ret = mail($email , "Recuperação de Senha", $msg, $headers);
        $ret = sendEmail('no-reply@roihero.com.br', $email, 'Recuperação de Senha', $msg, 'ROI HERO');
        return $ret;
    }

    function redireciona($caminho){
      header("Location: ".$caminho);
    }

    $qSelectCliente = "SELECT CLI_id, CLI_ativo FROM cliente WHERE CLI_email = '$email'";
    $resultSelectCliente = mysqli_query($conCad, $qSelectCliente);
    print(mysqli_error($conCad));


    // se a query deu certo
    if($resultSelectCliente){
      // e se retornou uma linha
      if(mysqli_num_rows($resultSelectCliente) > 0){            
        $registro = mysqli_fetch_array($resultSelectCliente);
        $id = $registro['CLI_id'];
        $ativo = $registro['CLI_ativo'];

        if(!$ativo){
          header("Location: ../email-invalido.html");
        }
        
        //gera o token
        $token = geraToken($email);

        //e insere na tabela
        $qInsertTokenSenha = "UPDATE cliente SET CLI_token_senha = '$token' WHERE CLI_email = '$email'";
        $resultInsertTokenSenha = mysqli_query($conCad, $qInsertTokenSenha);

        if($resultInsertTokenSenha){
            $retorno = enviaEmail($email, $token);
            if(!$retorno){
                redireciona("../email-invalido.html");
            } else{
                redireciona('../confirmacao-envio-email.html');
            }
        } 
      } else {          
          redireciona(" ../email-invalido.html");
      }
    }
?>
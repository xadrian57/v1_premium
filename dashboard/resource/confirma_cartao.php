<?php

    function redirecionaDash(){
        header('Location: ../overview');
    }

    session_name('premium');
 	session_start();


    include "../../bd/conexao_bd_cadastro.php";

    enviaEmail("INICIEI CONFIRMA_CARTAO", "oK?", "INICIADO AQUI");
    


    $idCli = (isset($_SESSION['id']) && $_SESSION['id'] != "") ? $_SESSION['id'] : 0;
    session_write_close();

    if($idCli == 0){ //se a sessão não estiver setada, usa o select padrão mesmo
        $qClientesComCartaoNaoContratado = 
        "SELECT CLI_id, CLI_id_sl, PLAN_id_plano, PLAN_tempo, PLAN_cupom, PLAN_parcelas 
        FROM 
        cliente
        left join controle on controle.CONT_id_cli = CLI_id
        left join plano on PLAN_id_cli = CLI_id
        WHERE 
        CLI_ativo = 1 and
        CONT_contratou_cartao = 2 and
        PLAN_metodo_pag = 1 and 
        PLAN_id_plano != 0"; 
    } else{ //se estiver setada, usa o select daquele cliente
        $qClientesComCartaoNaoContratado = 
        "SELECT CLI_id, CLI_id_sl, PLAN_id_plano, PLAN_tempo, PLAN_cupom, PLAN_parcelas 
        FROM 
        cliente
        left join controle on controle.CONT_id_cli = CLI_id
        left join plano on PLAN_id_cli = CLI_id
        WHERE
        CLI_id = $idCli and 
        CLI_ativo = 1 and
        CONT_contratou_cartao = 2 and
        PLAN_metodo_pag = 1"; 
    }

    
    $result = mysqli_query($conCad,$qClientesComCartaoNaoContratado);
    
    if($result){

        $row = mysqli_fetch_array($result);

        $idCli = $row['CLI_id'];
        $idSuperLogica = $row['CLI_id_sl'];
        $idPlano = $row['PLAN_id_plano'];
        $tempo = $row['PLAN_tempo'];
        $cupom = $row['PLAN_cupom'];
        $quantidadeParcelas = $row['PLAN_parcelas'];

        $planos = array(
            30 => array(0, 0, 57, 58),
            360 => array(0, 0, 55, 56)
        );
        
        $plano = $planos[$tempo][$idPlano];
        //$plano = 54; //plano de teste

        try{

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, "https://api.superlogica.net/v2/financeiro/assinaturas");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HEADER, FALSE);

            curl_setopt($ch, CURLOPT_POST, TRUE);
            $hoje = date("m/d/Y");

            $cupom = strtoupper(str_replace(" ", "", $cupom));


            $dados = '{
                "PLANOS": [{
                        "ID_SACADO_SAC": '.$idSuperLogica.',
                        "QUANT_PARCELAS_ADESAO" : '.$quantidadeParcelas.',
                        "ID_PLANO_PLA": '.$plano.',
                        "DT_CONTRATO_PLC": "'.$hoje.'",
                        "FL_NOTIFICARCLIENTE" : 1';
            

            if($cupom != ""){
                $dados .= ',
                "cupom" : "'.$cupom.'"';
            }

            $dados .= '}]
            }';

            

            curl_setopt($ch, CURLOPT_POSTFIELDS, $dados);

            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                        "Content-Type: application/json",
                        "app_token: BzL462rPGlXD",
                        "access_token: H3mUEJQd37E1"
                    ));

            $response = curl_exec($ch);
            $err = curl_error($ch);
            $errno = curl_errno($ch);

            if(!$response){
                alertaFalha($idCli, $idSuperLogica, $response." <br> \n <br> ".curl_error($ch)." <br> \n <br> ".curl_errno($ch));
            }


            $json = json_decode($response, true);
            
            if(curl_getinfo($ch, CURLINFO_HTTP_CODE) >= 300){
                alertaFalha($idCli, $idSuperLogica, $response);
            }
            
            curl_close($ch);


            $resp = explode(" ", $json[0]['msg']);

            
            if(in_array("anteriormente.", $resp)){
                $meses = $tempo / 30;
                $qUpdatePlano = "UPDATE plano SET PLAN_status = 1, PLAN_data_venc = DATE_ADD(PLAN_data_venc, INTERVAL ".$meses." MONTH) WHERE PLAN_id_cli = $idCli";
                mysqli_query($conCad,$qUpdatePlano);

                $qUpdateControle = "UPDATE controle SET CONT_contratou_cartao = 1 WHERE CONT_id_cli = $idCli";
                mysqli_query($conCad,$qUpdateControle);

                enviaEmail($idCli, $idSuperLogica, $response);
            
            } elseif(in_array("desativado,", $resp)){
                $qUpdateCli = "UPDATE cliente SET CLI_ativo = 0 WHERE CLI_id = $idCli";
                mysqli_query($conCad,$qUpdateCli);
            
            } elseif(!in_array("sucesso.", $resp)){
                enviaEmail($idCli, $idSuperLogica, $response);
            }

            //se deu certo a contratação, atualiza as coisas no banco
            
            // ativa o plano, adiciona a data
            $meses = $tempo / 30;
            $qUpdatePlano = "UPDATE plano SET PLAN_status = 1, PLAN_data_venc = DATE_ADD(PLAN_data_venc, INTERVAL ".$meses." MONTH) WHERE PLAN_id_cli = $idCli";
            mysqli_query($conCad,$qUpdatePlano);

            $qUpdateControle = "UPDATE controle SET CONT_contratou_cartao = 1 WHERE CONT_id_cli = $idCli";
            mysqli_query($conCad,$qUpdateControle);

            $qNotContrataPlano = "INSERT INTO notificacoes (NOT_id_cli, NOT_titulo, NOT_texto, NOT_data, NOT_status, NOT_icone) VALUES ($idCli, 'Plano Contratado', 'Parabéns, você acaba de adquirir um plano Roi Hero. Aproveite as vantagens de seu novo plano', CURRENT_DATE(), 1, 'success')";
            $resultNotContrataPlano = mysqli_query($conCad, $qNotContrataPlano);

            redirecionaDash();        

        } catch (Exception $e){
            $mensagem = $e->getMessage();
            enviaEmail($idCli, $idSuperLogica, $mensagem);
        } 
    } else {
        enviaEmail($idCli, "-", "NÃO PEGOU PELA SESSÃO OU ERA OUTRO CLIENTE, OU ALGO DEU ERRADO. SEGUE O SQL:  ".$qClientesComCartaoNaoContratado);
    }


    function alertaFalha($idCli, $idSuperLogica, $mensagem){
        global $conCad;

        enviaEmail($idCli, $idSuperLogica, $mensagem);


        $selectDuplicadaCartaoHoje = "SELECT NOT_titulo FROM notificacoes WHERE NOT_id_cli = $idCli and NOT_texto = 'Foi constatado um problema no cartão de crédito informado para pagamento. Nosso suporte entrará em contato em breve.' and NOT_data = CURRENT_DATE()";
        $resultDuplicadaCartaoHoje = mysqli_query($conCad, $selectDuplicadaCartaoHoje);

        if(!$resultDuplicadaCartaoHoje || mysqli_num_rows($resultDuplicadaCartaoHoje) == 0){
            $qNotErroCartao = "INSERT INTO notificacoes (NOT_id_cli, NOT_titulo, NOT_texto, NOT_data, NOT_status, NOT_icone) VALUES ($idCli, 'Problema com o cartão', 'Foi constatado um problema no cartão de crédito informado para pagamento. Nosso suporte entrará em contato em breve.', CURRENT_DATE(), 1, 'danger')";
            $resultNotErroCartao = mysqli_query($conCad, $qNotErroCartao);
        }
        exit();
        redirecionaDash();  

    }

    function enviaEmail($idCli, $idCliSL, $mensagem){
        $headers = "Reply-To: ROI HERO <atendimento@roihero.com.br>\r\n";
		$headers .= "Return-Path: Roi Hero <atedimento@roihero.com.br>\r\n";
		$headers .= "From: Roi Hero <atendimento@roihero.com.br>\r\n"; 
		//$headers .= "Cc: julio.vieira@roihero.com.br\r\n";
		//$headers .= 'Cc: lucas_hoch_sv@hotmail.com' . "\r\n";
		$headers .= "Organization: Roi Hero - Recomendações Inteligentes\r\n";
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-type: text/html; charset=utf-8\r\n";
		$headers .= "X-Priority: 3\r\n";
		$headers .= "X-Mailer: PHP". phpversion() ."\r\n";

		//mensagem do email de novo cliente VIP
		$msg = 
        '<!DOCTYPE html>
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
                                                                                        <td style="width:186px;">
                                                                                            <a href="https://pre00.deviantart.net/0391/th/pre/i/2017/183/7/4/biblethump_by_vivienegg-dbeso18.png" target="_blank"><img alt="" title="" height="auto" src="https://pre00.deviantart.net/0391/th/pre/i/2017/183/7/4/biblethump_by_vivienegg-dbeso18.png" style="border:none;border-radius:0px;display:block;font-size:13px;outline:none;text-decoration:none;width:100%;height:auto;" width="186"></a>
                                                                                        </td>
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
                                                            <table role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
                                                                <tbody>
                                                                    <tr>
                                                                        <td style="word-wrap:break-word;font-size:0px;padding:0px 0px 0px 0px;" align="justify">
                                                                            <div style="cursor:auto;color:#505050;font-family:Open Sans, sans-serif;;font-size:11px;line-height:22px;text-align:justify;">
                                                                                <p class="MsoNormal" style="margin-bottom:0cm;margin-bottom:.0001pt"><span style="font-size: 24px;"><b>O Cliente quis contratar com cart&#xE3;o e n&#xE3;o rolou</b></span></p>
                                                                                <p class="MsoNormal" style="margin-bottom:0cm;margin-bottom:.0001pt"><span style="font-size:14px;"><span style="color: rgb(59, 56, 56); font-family: " open sans",="" sans-serif;"="">Ol&#xE1;, vim avisar-te,</span></span>
                                                                                </p>
                                                                                <p class="MsoNormal" style="margin-bottom:0cm;margin-bottom:.0001pt"><font color="#3b3838" face="Open Sans, sans-serif"><span style="font-size: 14px;">Um cliente foi pego pelo cron de contrata&#xE7;&#xE3;o de cart&#xE3;o. Ele n&#xE3;o conseguiu completar a contrata&#xE7;&#xE3;o, houve problema. Segue o cliente e o problema (provavelmente t&#xE1; em json o problema).</span></font></p>
                                                                                <p class="MsoNormal" style="margin-bottom:0cm;margin-bottom:.0001pt"></p>
                                                                                <p class="MsoNormal" style="margin-bottom:0cm;margin-bottom:.0001pt"><font color="#3b3838" face="Open Sans, sans-serif"><span style="font-size: 14px;">Cli: '.$idCli.'<br>SuperLogica: '.$idCliSL.'</span></font></p>
                                                                                <p class="MsoNormal" style="margin-bottom:0cm;margin-bottom:.0001pt"><font color="#3b3838" face="Open Sans, sans-serif"><span style="font-size: 14px;">Mensagem: <br>'.$mensagem.'</span></font></p>
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
                                                                                        <td style="width:276px;"><img alt="" title="" height="auto" src="https://vignette.wikia.nocookie.net/universosteven/images/0/04/Llorando_por_siempre.jpg/revision/latest?cb=20151228002059&amp;path-prefix=es" style="border:none;border-radius:0px;display:block;font-size:13px;outline:none;text-decoration:none;width:100%;height:auto;" width="276"></td>
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


        mail("hochlucassilva@gmail.com" , "Cliente não conseguiu contratar", $msg, $headers);
    }

?>
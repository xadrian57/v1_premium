<?php
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: POST');

	function gera_token()
	{
		global $conCad;
		
		while(true){
			$random_hash = bin2hex(openssl_random_pseudo_bytes(4));
			$qTesteTokenUnico = "SELECT CLI_id FROM cliente WHERE CLI_token = '$random_hash'";
			$resultTesteTokenUnico = mysqli_query($conCad, $qTesteTokenUnico);
			if(mysqli_num_rows($resultTesteTokenUnico) == 0){ // se não retornou resultado, é único
				return $random_hash;
			}
		}
	}
	// verifica se o email já está cadastrado no banco
	require_once '../bd/conexao_bd_cadastro.php';
	$emailCLI = mysqli_real_escape_string($conCad,$_POST['email']);
	
	$select = "SELECT CLI_nome, CLI_email, CLI_ativo FROM cliente WHERE CLI_email = '$emailCLI'";
	$query = mysqli_query($conCad,$select);
	$array = mysqli_fetch_array($query);
	$email = $array['CLI_email'];
	$ativo = ($array['CLI_ativo'] == 1) ? true:false;
	$nomeCLI = $array['CLI_nome'];
	if (!$ativo) {
		echo '0';
	} else {
		echo '1';
	}

	// gera token e seleciona email
	$tokenCLI = gera_token();
	$select = "SELECT CLI_email, CLI_ativo from cliente WHERE CLI_email = '$emailCLI'";	
	$query = mysqli_query($conCad, $select);
	$array = mysqli_fetch_array($query);
	$ativo = ($array['CLI_ativo'] == 1) ? true:false; // verifica se o cliente existe no banco

	if (!$ativo) {		
		$insert = "INSERT INTO cliente (CLI_email, CLI_token) VALUES ('$emailCLI', '$tokenCLI')";
		if (mysqli_num_rows($query) < 1) {		
			$insert = "INSERT INTO cliente (CLI_email, CLI_token, CLI_ativo) VALUES ('$emailCLI', '$tokenCLI', 0)";
			$query = mysqli_query($conCad, $insert);

			// manda o email aqui 
		    $headers = "Reply-To: ROI HERO <atendimento@roihero.com.br>\r\n";
			$headers .= "Return-Path: Roi Hero <atedimento@roihero.com.br>\r\n";
			$headers .= "From: Roi Hero <atendimento@roihero.com.br>\r\n"; 
			$headers .= "Organization: Roi Hero - Recomendações Inteligentes\r\n";
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-type: text/html; charset=utf-8\r\n";
			$headers .= "X-Priority: 3\r\n";
			$headers .= "X-Mailer: PHP". phpversion() ."\r\n";

            $message = '<!DOCTYPE html><html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office"><head>  <title></title>  <!--[if !mso]><!-- -->  <meta http-equiv="X-UA-Compatible" content="IE=edge">  <!--<![endif]--><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><style type="text/css">  #outlook a { padding: 0; }  .ReadMsgBody { width: 100%; }  .ExternalClass { width: 100%; }  .ExternalClass * { line-height:100%; }  body { margin: 0; padding: 0; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }  table, td { border-collapse:collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; }  img { border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; }  p { display: block; margin: 13px 0; }</style><!--[if !mso]><!--><style type="text/css">  @media only screen and (max-width:480px) {    @-ms-viewport { width:320px; }    @viewport { width:320px; }  }</style><!--<![endif]--><!--[if mso]><xml>  <o:OfficeDocumentSettings>    <o:AllowPNG/>    <o:PixelsPerInch>96</o:PixelsPerInch>  </o:OfficeDocumentSettings></xml><![endif]--><!--[if lte mso 11]><style type="text/css">  .outlook-group-fix {    width:100% !important;  }</style><![endif]--><!--[if !mso]><!-->    <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet" type="text/css">    <style type="text/css">        @import url(https://fonts.googleapis.com/css?family=Open+Sans);    </style>  <!--<![endif]--><style type="text/css">  @media only screen and (min-width:480px) {    .mj-column-per-100 { width:100%!important; }  }</style></head><body style="background: #FFFFFF;">    <div class="mj-container" style="background-color:#FFFFFF;"><!--[if mso | IE]>      <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="600" align="center" style="width:600px;">        <tr>          <td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;">      <![endif]--><table role="presentation" cellpadding="0" cellspacing="0" style="background:#21A2E2;font-size:0px;width:100%;" border="0"><tbody><tr><td><div style="margin:0px auto;max-width:600px;"><table role="presentation" cellpadding="0" cellspacing="0" style="font-size:0px;width:100%;" align="center" border="0"><tbody><tr><td style="text-align:center;vertical-align:top;direction:ltr;font-size:0px;padding:26px 0px 26px 0px;"><!--[if mso | IE]>      <table role="presentation" border="0" cellpadding="0" cellspacing="0">        <tr>          <td style="vertical-align:top;width:600px;">      <![endif]--><div class="mj-column-per-100 outlook-group-fix" style="vertical-align:top;display:inline-block;direction:ltr;font-size:13px;text-align:left;width:100%;"><table role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0"><tbody><tr><td style="word-wrap:break-word;font-size:0px;padding:0px 0px 0px 0px;" align="center"><table role="presentation" cellpadding="0" cellspacing="0" style="border-collapse:collapse;border-spacing:0px;" align="center" border="0"><tbody><tr><td style="width:360px;"><img alt="" title="" height="auto" src="https://topolio.s3-eu-west-1.amazonaws.com/uploads/5a85acd87d1a9/1519050141.jpg" style="border:none;border-radius:0px;display:block;font-size:13px;outline:none;text-decoration:none;width:100%;height:auto;" width="360"></td></tr></tbody></table></td></tr></tbody></table></div><!--[if mso | IE]>      </td></tr></table>      <![endif]--></td></tr></tbody></table></div></td></tr></tbody></table><!--[if mso | IE]>      </td></tr></table>      <![endif]-->      <!--[if mso | IE]>      <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="600" align="center" style="width:600px;">        <tr>          <td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;">      <![endif]--><table role="presentation" cellpadding="0" cellspacing="0" style="background:#FFFFFF;font-size:0px;width:100%;" border="0"><tbody><tr><td><div style="margin:0px auto;max-width:600px;"><table role="presentation" cellpadding="0" cellspacing="0" style="font-size:0px;width:100%;" align="center" border="0"><tbody><tr><td style="text-align:center;vertical-align:top;direction:ltr;font-size:0px;padding:26px 0px 26px 0px;"><!--[if mso | IE]>      <table role="presentation" border="0" cellpadding="0" cellspacing="0">        <tr>          <td style="vertical-align:top;width:600px;">      <![endif]--><div class="mj-column-per-100 outlook-group-fix" style="vertical-align:top;display:inline-block;direction:ltr;font-size:13px;text-align:left;width:100%;"><table role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0"><tbody><tr><td style="word-wrap:break-word;font-size:0px;padding:0px 0px 0px 0px;" align="justify"><div style="cursor:auto;color:#505050;font-family:Open Sans, sans-serif;;font-size:11px;line-height:22px;text-align:justify;"><p class="MsoNormal" style="margin-bottom:0cm;margin-bottom:.0001pt"><span style="font-size: 24px;"><b>Falta pouco!</b></span></p><p class="MsoNormal"><span style="font-size:14px;"><font color="#3b3838" face="Open Sans, sans-serif">Agora &#xE9; s&#xF3; inserir o seu token na confirma&#xE7;&#xE3;o de e-mail. Copie e cole esse token na p&#xE1;gina de confirma&#xE7;&#xE3;o e finalize seu cadastro.</font></span></p><p class="MsoNormal"><strong><span style="font-size:14px;"><font color="#3b3838" face="Open Sans, sans-serif"><u>TOKEN: '.$tokenCLI.'&#xA0;</u></font></span></strong></p><p class="MsoNormal"></p><p class="MsoNormal"><span style="font-size:14px;"><font color="#3b3838" face="Open Sans, sans-serif">Qualquer d&#xFA;vida, entre em contato com a nossa equipe pelo email&#xA0;<strong><em>julio.vieira@roihero.com.br</em></strong>, ou voc&#xEA; tamb&#xE9;m pode consultar nosso helpdesk <a href="https://roihero.octadesk.com/kb" target="_blank">clicando aqui</a>.</font></span></p><p></p><p><span style="font-size:14px;">A equipe Roi Hero te d&#xE1; as boas vindas,<br>Abra&#xE7;os</span></p></div></td></tr><tr><td style="word-wrap:break-word;font-size:0px;padding:0px 0px 0px 0px;" align="center"><table role="presentation" cellpadding="0" cellspacing="0" style="border-collapse:collapse;border-spacing:0px;" align="center" border="0"><tbody><tr><td style="width:600px;"><img alt="" title="" height="auto" src="https://topolio.s3-eu-west-1.amazonaws.com/uploads/5a85acd87d1a9/1519012790.jpg" style="border:none;border-radius:0px;display:block;font-size:13px;outline:none;text-decoration:none;width:100%;height:auto;" width="600"></td></tr></tbody></table></td></tr></tbody></table></div><!--[if mso | IE]>      </td></tr></table>      <![endif]--></td></tr></tbody></table></div></td></tr></tbody></table><!--[if mso | IE]>      </td></tr></table>      <![endif]-->      <!--[if mso | IE]>      <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="600" align="center" style="width:600px;">        <tr>          <td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;">      <![endif]--><table role="presentation" cellpadding="0" cellspacing="0" style="background:#FFFFFF;font-size:0px;width:100%;" border="0"><tbody><tr><td><div style="margin:0px auto;max-width:600px;"><table role="presentation" cellpadding="0" cellspacing="0" style="font-size:0px;width:100%;" align="center" border="0"><tbody><tr><td style="text-align:center;vertical-align:top;direction:ltr;font-size:0px;padding:26px 0px 26px 0px;"><!--[if mso | IE]>      <table role="presentation" border="0" cellpadding="0" cellspacing="0">        <tr>          <td style="vertical-align:top;width:600px;">      <![endif]--><div class="mj-column-per-100 outlook-group-fix" style="vertical-align:top;display:inline-block;direction:ltr;font-size:13px;text-align:left;width:100%;"><table role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0"><tbody></tbody></table></div><!--[if mso | IE]>      </td></tr></table>      <![endif]--></td></tr></tbody></table></div></td></tr></tbody></table><!--[if mso | IE]>      </td></tr></table>      <![endif]--></div></body></html>';
           
            
           	mail($emailCLI, "Código de Confirmação - Roi Hero", $message,$headers);
		}
	}
?>
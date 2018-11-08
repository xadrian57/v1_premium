<?php

	session_name('premium');
 	session_start();


	include "../../bd/conexao_bd_cadastro.php";


	$idCli = $_SESSION['id'];
	

	$qPlano = "SELECT PLAN_id_plano FROM plano WHERE PLAN_id_cli = $idCli";
	$result = mysqli_query($conCad,$qPlano);

	$array = mysqli_fetch_array($result);

	//id do plano atual
	$idPlano = $array['PLAN_id_plano'];

	//pega o tempo = 0:free; 30:mensal; 90:trimestral; 180:semestral; 360: anual 
	$tempo = mysqli_real_escape_string($conCad, $_POST['tempo']); 
	//pega o id do novo plano: 1:free; 2:startup; 3:pro; 4:rocket; 42:vip
	$novoPlano = mysqli_real_escape_string($conCad, $_POST['plano']);

	$metodoPagamento = mysqli_real_escape_string($conCad, $_POST['formaPagamento']);

	$cupom = mysqli_real_escape_string($conCad, $_POST['cupom']);
	$cupom = strtoupper($cupom);
	

	$quantidadeParcelas = (isset($_POST['quantidadeParcelas'])) ? mysqli_real_escape_string($conCad, $_POST['quantidadeParcelas']) : "1";

	$nomeNovoPlano = "";

	// se o plano não for vip, atualiza as informações do plano com os valores normalmente
	if (!empty($novoPlano)){
		

		$nomesPlanos = array(
			2 => "Startup",
			3 => "Pro"
		);

		$nomeNovoPlano = $nomesPlanos[$novoPlano];

		//mysqli_query($conCad,"START TRANSACTION");
		mysqli_autocommit ($conCad, false);

		$valores = array(
			30 => array(0, 0, 39.0, 99.0, 199.99),
			360 => array(0, 0, 374.4, 950.4, 1910.4)
		);
		$impressoes = 200000;

		$valor = $valores[$tempo][$novoPlano];
		$meses = $tempo / 30;

		$mPag = ($metodoPagamento == "Cartão de crédito" or $metodoPagamento == "cartao") ? 1 : 0;

		$qUpdatePlano = "UPDATE plano SET PLAN_id_plano = $novoPlano, PLAN_valor = $valor, PLAN_metodo_pag = $mPag, PLAN_views = $impressoes, PLAN_data_venc = DATE_ADD(CURRENT_DATE(), INTERVAL 7 DAY), PLAN_status = 2, PLAN_tempo = $tempo, PLAN_cupom = '$cupom', PLAN_parcelas = $quantidadeParcelas WHERE PLAN_id_cli = $idCli";

		$resultUpdatePlano = mysqli_query($conCad,$qUpdatePlano);
		

		if (!$resultUpdatePlano) {  
			mysqli_rollback($conCad);
			echo json_encode(array('status' => '0', 'msg' => 'Erro inesperado. Por favor, verifique sua conexão ou contate nosso suporte'));
			exit();
		}


		//$inseriuWidgets = gerenciaWidgets($idCli, $novoPlano);
		if (!gerenciaWidgets($idCli, $novoPlano)) {  
			mysqli_rollback($conCad);
			exit();
		}

		//insereDados($idCli, $novoPlano, $tempo);

		if (!insereDados($idCli, $novoPlano, $tempo, $metodoPagamento)) {  
			mysqli_rollback($conCad);
			exit();
		}

		$_SESSION['idPlan'] = $novoPlano;
		session_write_close();
		
		mysqli_commit($conCad);


	} else { //se for vip, tem valores especiais
		insereDadosVIP($idCli, $tempo);
    	session_write_close();
	}
	

	

	function gerenciaWidgets ($idCli, $novoPlano){
		global $conCad;

		// agora cuida da inserção e desativação de widget
		$formatos = "1-2-3-11-8"; 
		$arrayWidgets = array(
			//--trial v
			1	=> array(1, $formatos, "Mais Desejados", "1-7", "Os produtos mais desejados", false),
			2	=> array(3, $formatos, "Mais Vendidos da Categoria", "4", "Os mais vendidos da categoria", false),
			3	=> array(4, $formatos, "Remarketing On-site", "1-2-7", "Os produtos que você queria!", false),
			4	=> array(5, $formatos, "Similar Por Produto", "2", "Aproveite também essas ofertas!", false),
			5	=> array(10, 6, "Oferta Limitada", "1", "Oferta Limitada", true),
			6	=> array(13, 5, "Não vá Embora", "0", "Olhe o que separamos para você!", false),
			7	=> array(15, $formatos, "Lançamentos", "1-7", "Os Lançamentos", false),
			//--trial ^
			8	=> array(34, $formatos, "Produtos Relacionados", "2", "Talvez você se interesse por esses", true),
			9	=> array(35, 13, "Remarketing Navegação", "1-7", "Quem viu esse, viu também", false),
			
			
			//até aqui é startup

			//--trial v
			10	=> array(2, $formatos, "Mais Vendidos", "1-7", "Os mais desejados!", false),
			11	=> array(6, "1-2-3-8", "Liquidação", "1-7", "Aproveite essas promoções!", false),
			// A PEDIDO DE PAULO - 25/05/2018  // 12	=> array(14, $formatos, "Baixou de Preço", "1-7", "Olhe essas Ofertas", false),
			//--trial ^
			12	=> array(7, $formatos, "Collection", "2", "Produtos da mesma coleção", true),
			13	=> array(9, $formatos, "Manual", "1-7", "As melhores ofertas", true),
			14	=> array(12, $formatos, "Itens Complementares", "2", "Quem comprou, comprou também", false),
			15	=> array(20, $formatos, "Melhor Avaliados", "1-7", "Os Melhor Avaliados", false),
			16	=> array(24, $formatos, "Mais Vendidos da Categoria Manual", "1-2-4-7", "Os mais vendidos da categoria", true),
			17	=> array(25, $formatos, "Palavra-Chave", "1-7", "Aproveite essas ofertas", true)
		);
		$token = sha1($idCli);

		//insere os comuns: que são de 8 a 9
		//depois insere
		$i = 8;
		while ($i <= 9){
			$inseriuWidget = insereWidget($arrayWidgets[$i]);
			if (!$inseriuWidget)

				return false;

			
			$i++;
		}

		switch ($novoPlano){
			case 2: // contratou startup. Remove de 10 a 11 
				
				$i = 10;
				while($i <= 11){
					$formato = explode("-", $arrayWidgets[$i][1]);

					$condicao = "WID_inteligencia = ".$arrayWidgets[$i][0]." and (";
					foreach ($formato as $value) {
						$condicao .= " WID_formato = ".$value." or ";
					}
					$condicao .= "WID_formato = -1)  and WID_id_cli = $idCli";					
					$qUpdateWidget = "UPDATE widget SET WID_status = 2 WHERE ".$condicao;
					$queryUpdateWidgets = mysqli_query($conCad, $qUpdateWidget);

					if(!$queryUpdateWidgets){
						echo json_encode(array('status' => '0', 'msg' => 'Erro inesperado. Por favor, verifique sua conexão ou contate nosso suporte'));
						return false;
					}
					$i++;
				}
				break;



			case 3: //contratou Pro. Insere de 12 a 18
				$i = 12;
				while ($i <= 17){
					$inseriuWidget = insereWidget($arrayWidgets[$i]);
					if (!$inseriuWidget)
						return false;
					$i++;
				}
				break;
		}

		return true;
	
	}
	function insereDadosVIP($idCli, $tempo){

		global $conCad;

		$emailFinanceiro = mysqli_real_escape_string($conCad, $_POST['emailResponsavel']);
		$nomeResponsavel = mysqli_real_escape_string($conCad, $_POST['nomeResponsavel']);
		$mensagem = mysqli_real_escape_string($conCad, $_POST['mensagem']);

		//se detectar injection html em algum dos campos, retorna falso sem fazer alteração
		if ( preg_match( "/[\r\n]/", $mensagem ) || preg_match( "/[\r\n]/", $emailFinanceiro) || preg_match( "/[\r\n]/", $nomeResponsavel) ) {
			return false;
		}

		$mensagem = str_replace("\n", "<br>", $mensagem);

		

		$qSelectCliente = 
		"SELECT 
			CLI_email,
			CLI_nome, 
			CLI_site,
			CLI_plataforma,
			CAD_telefone
		FROM    
				cliente
		LEFT JOIN
				cadastro ON CLI_id = CAD_id_cli
		WHERE
				CLI_id = $idCli";
		$resultSelectCliente = mysqli_query($conCad,$qSelectCliente);

		$array = mysqli_fetch_array($resultSelectCliente);

		$email = $array['CLI_email'];
		$nome = $array['CLI_nome'];
		$site = $array['CLI_site'];
		$plataforma = $array['CLI_plataforma'];
		$telefone = $array['CAD_telefone'];

		$plataforma = str_replace("_", " ", $plataforma);
		$plataforma = ucwords($plataforma);


		$qUpdateCadastro = 
		"UPDATE 
			cadastro 
		SET 
			CAD_nome_cli = '$nomeResponsavel',
			CAD_email_sec = '$emailFinanceiro' 
		WHERE
			CAD_id_cli = $idCli";

		$resultUpdateCadastro = mysqli_query($conCad,$qUpdateCadastro);

		//se algum dos result foi falso, retorna falso
		if(!$resultUpdateCadastro)
			return false;


		$headers .= "Reply-To: ROI HERO <atendimento@roihero.com.br>\r\n";
		$headers .= "Return-Path: Roi Hero <atedimento@roihero.com.br>\r\n";
		$headers .= "From: Roi Hero <atendimento@roihero.com.br>\r\n"; 
		$headers .= "Cc: julio.vieira@roihero.com.br\r\n";
		//$headers .= 'Cc: lucas_hoch_sv@hotmail.com' . "\r\n";
		$headers .= "Organization: Roi Hero - Recomendações Inteligentes\r\n";
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-type: text/html; charset=utf-8\r\n";
		$headers .= "X-Priority: 3\r\n";
		$headers .= "X-Mailer: PHP". phpversion() ."\r\n";

		//mensagem do email de novo cliente VIP
		$msg = 
			'<!DOCTYPE html>
			<html xmlns="http://www.w3.org/1999/xhtml" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:v="urn:schemas-microsoft-com:vml">
				<head>
					<title></title>
					<!--[if !mso]><!-- -->
					<meta content="IE=edge" http-equiv="X-UA-Compatible" />
					<!--<![endif]-->
					<meta content="text/html; charset=UTF-8" http-equiv="Content-Type" />
					<meta content="width=device-width, initial-scale=1.0" name="viewport" />
					<style type="text/css">
					#outlook a { padding: 0; }  .ReadMsgBody { width: 100%; }  .ExternalClass { width: 100%; }  .ExternalClass * { line-height:100%; }  body { margin: 0; padding: 0; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }  table, td { border-collapse:collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; }  img { border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; }  p { display: block; margin: 13px 0; }		</style>
					<!--[if !mso]><!-->
					<style type="text/css">
					@media only screen and (max-width:480px) {    @-ms-viewport { width:320px; }    @viewport { width:320px; }  }		</style>
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
					<link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet" type="text/css" />
					<style type="text/css">
					@import url(https://fonts.googleapis.com/css?family=Open+Sans);		</style>
					<!--<![endif]-->
					<style type="text/css">
					@media only screen and (min-width:480px) {    .mj-column-per-100 { width:100%!important; }  }		</style>
				</head>
				<body style="background: #FFFFFF;">
					<div class="mj-container" style="background-color:#FFFFFF;">
						<!--[if mso | IE]>      
							<table role="presentation" border="0" cellpadding="0" cellspacing="0" width="600" align="center" style="width:600px;">
								<tr>
								<td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;">
									<![endif]-->
						<table border="0" cellpadding="0" cellspacing="0" role="presentation" style="background:#21A2E2;font-size:0px;width:100%;">
							<tbody>
								<tr>
									<td>
										<div style="margin:0px auto;max-width:600px;">
											<table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="font-size:0px;width:100%;">
												<tbody>
													<tr>
														<td style="text-align:center;vertical-align:top;direction:ltr;font-size:0px;padding:26px 0px 26px 0px;">
															<!--[if mso | IE]>      
																<table role="presentation" border="0" cellpadding="0" cellspacing="0">
																	<tr>
																	<td style="vertical-align:top;width:600px;">
																		<![endif]-->
															<div class="mj-column-per-100 outlook-group-fix" style="vertical-align:top;display:inline-block;direction:ltr;font-size:13px;text-align:left;width:100%;">
																<table border="0" cellpadding="0" cellspacing="0" role="presentation" width="100%">
																	<tbody>
																		<tr>
																			<td align="center" style="word-wrap:break-word;font-size:0px;padding:0px 0px 0px 0px;">
																				<table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="border-collapse:collapse;border-spacing:0px;">
																					<tbody>
																						<tr>
																							<td style="width:360px;">
																								<img alt="" height="auto" src="https://topolio.s3-eu-west-1.amazonaws.com/uploads/5a85acd87d1a9/1519050141.jpg" style="border:none;border-radius:0px;display:block;font-size:13px;outline:none;text-decoration:none;width:100%;height:auto;" title="" width="360" /></td>
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
																<![endif]--></td>
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
							<![endif]--><!--[if mso | IE]>      
							<table role="presentation" border="0" cellpadding="0" cellspacing="0" width="600" align="center" style="width:600px;">
								<tr>
								<td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;">
									<![endif]-->
						<table border="0" cellpadding="0" cellspacing="0" role="presentation" style="background:#FFFFFF;font-size:0px;width:100%;">
							<tbody>
								<tr>
									<td>
										<div style="margin:0px auto;max-width:600px;">
											<table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="font-size:0px;width:100%;">
												<tbody>
													<tr>
														<td style="text-align:center;vertical-align:top;direction:ltr;font-size:0px;padding:26px 0px 26px 0px;">
															<!--[if mso | IE]>      
																<table role="presentation" border="0" cellpadding="0" cellspacing="0">
																	<tr>
																	<td style="vertical-align:top;width:600px;">
																		<![endif]-->
															<div class="mj-column-per-100 outlook-group-fix" style="vertical-align:top;display:inline-block;direction:ltr;font-size:13px;text-align:left;width:100%;">
																<table border="0" cellpadding="0" cellspacing="0" role="presentation" width="100%">
																	<tbody>
																		<tr>
																			<td align="justify" style="word-wrap:break-word;font-size:0px;padding:0px 0px 0px 0px;">
																				<div style="cursor:auto;color:#505050;font-family:Open Sans, sans-serif;;font-size:11px;line-height:22px;text-align:justify;">
																					<p class="MsoNormal" style="margin-bottom:0cm;margin-bottom:.0001pt">
																						<strong><span style="font-size:24px;">Um novo cliente gostaria de ser VIP na Roi Hero!</span></strong></p>
																					<p style="margin-bottom:.0001pt">
																						&nbsp;</p>
																					<p class="MsoNormal">
																						<span style="font-size:14px;"><font color="#3b3838" face="Open Sans, sans-serif">Um novo cliente gostaria de ser VIP na Roi Hero. Escreveu a seguinte mensagem:</font></span></p>
																					<p class="MsoNormal">
																						<span style="font-size:14px;">&quot;'.$mensagem.'&quot;</span></p>
																					<p class="MsoNormal">
																						&nbsp;</p>
																					<p class="MsoNormal">
																						<span style="font-size:14px;"><font color="#3b3838"><font face="Open Sans, sans-serif">Informa&ccedil;&otilde;es do Cliente:</font></font></span></p>
																					<ul>
																						<li class="MsoNormal">
																							<span style="font-size:14px;">Nome do Respons&aacute;vel: '.$nomeResponsavel.'</span></li>
																						<li class="MsoNormal">
																							<span style="font-size:14px;">Nome da Loja: '.$nome.'</span></li>
																						<li class="MsoNormal">
																							<span style="font-size:14px;">Plataforma: '.$plataforma.'</span></li>
																						<li class="MsoNormal">
																							<span style="font-size:14px;">Telefone: '.$telefone.'</span></li>
																						<li class="MsoNormal">
																							<span style="font-size:14px;">Site: '.$site.'</span></li>
																						<li class="MsoNormal" style="margin-bottom: 0.0001pt;">
																							<span style="font-size:14px;">ID: '.$idCli.'</span></li>
																						<li class="MsoNormal" style="margin-bottom: 0.0001pt;">
																							<span style="font-size:14px;">Email Financeiro: '.$emailFinanceiro.'</span></li>
																						<li class="MsoNormal" style="margin-bottom: 0.0001pt;">
																							<span style="font-size:14px;">Email Padr&atilde;o: '.$email.'</span></li>
																					</ul>
																				</div>
																			</td>
																		</tr>
																	</tbody>
																</table>
															</div>
															<!--[if mso | IE]>      
																	</td>
																	</tr>
																</table>
																<![endif]--></td>
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
							<![endif]--></div>
					<p>
						&nbsp;</p>
				</body>
			</html>';


		mail("davi.bernardes@roihero.com.br" , "Cliente interessado em ser VIP", $msg, $headers);
		//mail("hochlucassilva@gmail.com" , "Cliente interessado em ser VIP", $msg, $headers);

	}

	//essa função insere na superlógica se não for gratuito e atualiza cadastro e cliente com o id_sl
	function insereDados($idCli, $novoPlano, $tempo, $metodoPagamento){
		global $nomeNovoPlano;
		global $conCad;
		global $cupom;

		

		$qSelectCliente = 
		"SELECT 
				CLI_nome,
				CLI_site,
				CLI_id_sl,
				CLI_senha,
				CLI_email
		FROM 	
				cliente
		WHERE
				CLI_id = $idCli";
		$resultSelectCliente = mysqli_query($conCad,$qSelectCliente);

		$array = mysqli_fetch_array($resultSelectCliente);

		$idSuperLogica = $array['CLI_id_sl'];

		$nomeFantasia = $array['CLI_nome'];
		$senha = $array['CLI_senha'];
		$email = $array['CLI_email'];
		$site = $array['CLI_site'];


		$nomeEmpresa = mysqli_real_escape_string($conCad, $_POST['nomeEmpresa']);
		$nomeResponsavel = mysqli_real_escape_string($conCad, $_POST['nomeResponsavel']);
		$cep = mysqli_real_escape_string($conCad, $_POST['cep']);
		$telefone = mysqli_real_escape_string($conCad, $_POST['telefone']);
		$cnpj = mysqli_real_escape_string($conCad, $_POST['cnpj']);
		$rua = mysqli_real_escape_string($conCad, $_POST['rua']);
		$numero = mysqli_real_escape_string($conCad, $_POST['numero']);
		$bairro = mysqli_real_escape_string($conCad, $_POST['bairro']);
		$inscricaoEstadual = mysqli_real_escape_string($conCad, $_POST['inscricaoEstadual']);
		$cidade = mysqli_real_escape_string($conCad, $_POST['cidade']);
		$emailResponsavel = mysqli_real_escape_string($conCad, $_POST['emailResponsavel']);
		$emailFinanceiro = mysqli_real_escape_string($conCad, $_POST['emailFinanceiro']);
		$estado = mysqli_real_escape_string($conCad, $_POST['estado']);
		//$complemento = mysqli_real_escape_string($conCad, $_POST['complemento']); tiraram o complemento de novo

		
		

		if($tempo != 360 && str_replace(" ", "", $cupom) != ""){ //
			echo json_encode(array('status' => '0', 'msg' => 'Cupom inserido inválido'));
			return false;
		}

		$resultUpdateCliente = true;

		$linkDoBoleto = "";

		if(!$idSuperLogica){ //se o id for nulo ou vazio, precisa cadastrar o novo cliente na superlógica

			$mPag = ($metodoPagamento == "Cartão de crédito" or $metodoPagamento == "cartao") ? 1 : 0;

			
			$planos = array(
				0 => array( //boleto
					30 => array(0, 0, 53, 44),
					360 => array(0, 0, 43, 47)
				),
				1 => array( //cartao
					30 => array(0, 0, 57, 58),
					360 => array(0, 0, 55, 56)
				)
			);

			$plano = $planos[$mPag][$tempo][$novoPlano];

			if($metodoPagamento == "cartao"){
				$bandeira = mysqli_real_escape_string($conCad, $_POST['bandeira']);
				$parametros = array(
					'plano' => $plano,
					'nomeEmpresa' => $nomeEmpresa,
					'nomeFantasia' => $nomeFantasia,
					'cnpj' => $cnpj,
					'inscricaoEstadual' => $inscricaoEstadual,
					'cep' => $cep,
					'rua' => $rua,
					'numero' => $numero,
					'bairro' => $bairro,
					'cidade' => $cidade,
					'estado' => $estado,
					'emailFinanceiro' => $emailFinanceiro,
					'telefone' => $telefone,
					'senha' => $senha,
					'cupom' => $cupom,
					'email' => $email,
					'bandeira' => $bandeira
				);
				$inseriuCartao = insereCartao($parametros, $idCli);
				if(!$inseriuCartao)
					return false;

				$linkDoBoleto = $inseriuCartao;
				
			} else{
				try{

					$ch = curl_init();
	
					curl_setopt($ch, CURLOPT_URL, "https://api.superlogica.net/v2/financeiro/checkout");
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
					curl_setopt($ch, CURLOPT_HEADER, FALSE);
	
					curl_setopt($ch, CURLOPT_POST, TRUE);
	
					$dados = array(
						"idplano" => $plano,
						"ST_NOME_SAC" => $nomeEmpresa,
						"ST_NOMEREF_SAC" => $nomeFantasia,
						"ST_DIAVENCIMENTO_SAC" => date("d"),
						"ST_CGC_SAC" => $cnpj,
						"ST_INSCRICAO_SAC" => $inscricaoEstadual,
						"ST_CEP_SAC" => $cep,
						"ST_ENDERECO_SAC" => $rua,
						"ST_NUMERO_SAC" => $numero,
						"ST_BAIRRO_SAC" => $bairro,
						"ST_CIDADE_SAC" => $cidade,
						"ST_ESTADO_SAC" => $estado,
						"FL_MESMOEND_SAC" => "1",
						"ST_EMAIL_SAC" => $emailFinanceiro,
						"ST_TELEFONE_SAC" => $telefone,
						"senha" => $senha,
						"senha_confirmacao" => $senha,
						"cupom" => $cupom,
						"FL_PAGAMENTOPREF_SAC" => "0"
					);
	
					$dados = json_encode($dados);
	
					curl_setopt($ch, CURLOPT_POSTFIELDS, $dados);
	
					curl_setopt($ch, CURLOPT_HTTPHEADER, array(
								"Content-Type: application/json",
								"app_token: BzL462rPGlXD",
								"access_token: H3mUEJQd37E1"
							));
	
					$response = curl_exec($ch);
	
	
					$json = json_decode($response, true);
	
					
	
					if(curl_getinfo($ch, CURLINFO_HTTP_CODE) >= 300){
						echo json_encode(array('status' => '0', 'msg' => $json['msg']));
						return false;
					}
					
					curl_close($ch);
					
	
					if($json['id_plano_pla'] != $plano){
						echo json_encode(array('status' => '0', 'msg' => 'Erro inesperado. Por favor, contate nosso suporte'));
						return false;
					}
	
					$idSuperLogica = $json['id_sacado_sac'];
					$linkDoBoleto = $json['link_boleto'];
					
					$retorno = json_encode(array('status' => '1', 'msg' => $linkDoBoleto));
				} catch (Exception $e){
					echo json_encode(array('status' => '0', 'msg' => 'Não foi possível finalizar a contratação. Por favor, verifique sua conexão ou contate nosso suporte'));
					return false;
	
				}

				$inseriuCobranca = insereCobranca($idCli, $idSuperLogica);

				if(!$inseriuCobranca){
					echo json_encode(array('status' => '0', 'msg' => 'Não foi possível finalizar a contratação. Por favor, verifique sua conexão ou contate nosso suporte'));
					return false;
				}


				$qUpdateCliente = "UPDATE cliente SET CLI_id_sl = '$idSuperLogica' WHERE CLI_id = $idCli";
				$resultUpdateCliente = mysqli_query($conCad,$qUpdateCliente);

				if(!$resultUpdateCliente){
					echo json_encode(array('status' => '0', 'msg' => 'Não foi possível finalizar a contratação. Por favor, verifique sua conexão ou contate nosso suporte'));
					return false;
				}

			}		

			
		}

		$qUpdateCadastro = 
		"UPDATE 
			cadastro 
		SET 
			CAD_cnpj = '$cnpj',
			CAD_inscricao_estadual = '$inscricaoEstadual',
			CAD_nome_cli = '$nomeResponsavel',
			CAD_rua = '$rua',
			CAD_numero = '$numero',
			CAD_bairro = '$bairro',
			CAD_cidade = '$cidade',
			CAD_estado = '$estado',
			CAD_CEP = '$cep',
			CAD_tel_sec = '$telefone',
			CAD_email_sec = '$emailFinanceiro' 
		WHERE
			CAD_id_cli = $idCli";

		$resultUpdateCadastro = mysqli_query($conCad,$qUpdateCadastro);
		//se algum dos result foi falso, retorna falso
		if(!$resultUpdateCliente or !$resultUpdateCadastro){
			echo json_encode(array('status' => '0', 'msg' => 'Erro inesperado. Por favor, contate nosso suporte'));
		    return false;
		}


		$traducao = array(
			'email_contato' => $email, // Texto, 100 caracteres, email, OBRIGATÓRIO
			'nome_contato' => $nomeResponsavel, // Texto, 100 caracteres
			'nome_empresa' => $nomeEmpresa, // Texto, 100 caracteres
			'site_empresa' => $site, // Texto, 100 caracteres
			'tel_empresa' => $telefone, // Numérico (pode receber número formatado, ex. (14) 3222-1415)
			'email_empresa' => $emailFinanceiro, // Texto, 100 caracteres, email
			'tags' => "clienteNovo" // Texto, 100 caracteres, termos separados por vírgula
		 );
		integra_api_lahar($traducao);

		if($metodoPagamento != "cartao"){

			$qNotContrataPlano = "INSERT INTO notificacoes (NOT_id_cli, NOT_titulo, NOT_texto, NOT_data, NOT_status, NOT_icone, NOT_link) VALUES ($idCli, 'Plano Contratado', 'Parabéns, você acaba de adquirir um plano Roi Hero. Aproveite as vantagens de seu novo plano', CURRENT_DATE(), 1, 'success', '$linkDoBoleto')";
			$resultNotContrataPlano = mysqli_query($conCad, $qNotContrataPlano);

			if(!$resultNotContrataPlano){
				echo json_encode(array('status' => '0', 'msg' => 'Não foi possível finalizar a contratação. Por favor, verifique sua conexão ou contate nosso suporte'));
				return false;
			}
		}
		
		integra_api_slack_app($nomeFantasia, $email, $telefone, $novoPlano, $tempo, $metodoPagamento, $cupom);
		

		echo json_encode(array('status' => '1', 'msg' => $linkDoBoleto));
		return true;
	}

	/*
		ESSA FUNÇÃO USA EXTRACT. Precisa receber um vetor com TODOS esses campos, com esses exatos nomes e preenchidos, todos!
		$parametros = array(
				plano => "valor",
				nomeEmpresa => "valor",
				nomeFantasia => "valor",
				cnpj => "valor",
				inscricaoEstadual => "valor",
				cep => "valor",
				rua => "valor",
				numero => "valor",
				bairro => "valor",
				cidade => "valor",
				estado => "valor",
				emailFinanceiro => "valor",
				telefone => "valor",
				senha => "valor"
			);
	*/
	function cadastraCliente($parametros){

		extract($parametros);
        
        try{
			$ch = curl_init();

			curl_setopt($ch, CURLOPT_URL, "https://api.superlogica.net/v2/financeiro/clientes");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_HEADER, FALSE);
			
			curl_setopt($ch, CURLOPT_POST, TRUE);
			
			$dados = array(
				"idplano" => $plano,
				"ST_NOME_SAC" => $nomeEmpresa,
				"ST_NOMEREF_SAC" => $nomeFantasia,
				"ST_DIAVENCIMENTO_SAC" => date("d"),
				"ST_CGC_SAC" => $cnpj,
				"ST_INSCRICAO_SAC" => $inscricaoEstadual,
				"ST_CEP_SAC" => $cep,
				"ST_ENDERECO_SAC" => $rua,
				"ST_NUMERO_SAC" => $numero,
				"ST_BAIRRO_SAC" => $bairro,
				"ST_CIDADE_SAC" => $cidade,
				"ST_ESTADO_SAC" => $estado,
				"FL_MESMOEND_SAC" => "1",
				"ST_EMAIL_SAC" => $emailFinanceiro,
				"ST_TELEFONE_SAC" => $telefone,
				"senha" => $senha,
				"senha_confirmacao" => $senha
			);
			
			
			$dados = json_encode($dados);
			
			curl_setopt($ch, CURLOPT_POSTFIELDS, $dados);
			
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				"Content-Type: application/json",
				"app_token: BzL462rPGlXD",
				"access_token: H3mUEJQd37E1"
			));

			$response = curl_exec($ch);
	
			$json = json_decode($response, true);
			
			if(curl_getinfo($ch, CURLINFO_HTTP_CODE) >= 300){
				echo json_encode(array('status' => '0', 'msg' => $json['msg']));
				return false;
			}
			
			curl_close($ch);

			

			if(!isset($json[0]['msg']) || $json[0]['msg'] != "Sucesso"){
				echo json_encode(array('status' => '0', 'msg' => $json[0]['msg']));
				return false;
			}

			$idSuperLogica = $json[0]['data']['id_sacado_sac'];
		} catch (Exception $e){
			echo json_encode(array('status' => '0', 'msg' => 'Não foi possível finalizar a contratação. Por favor, verifique sua conexão ou contate nosso suporte'));
			return false;
		}

		return $idSuperLogica;
        
	}
	
	function insereCartaoCliente($email, $bandeira){

		$bandeira = $_POST['bandeira']; 

		try{
			
			$ch = curl_init();
			$url = "https://api.superlogica.net/v2/financeiro/clientes/urlcartao";
			
			$dados = array(
				"email" => $email,
				"bandeira" => $bandeira,
				"callback" => "https://www.roihero.com.br/dashboard/resource/confirma_cartao.php"
			);
			

			$url .= '?'.http_build_query($dados);
			
			
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_HEADER, FALSE);
			
			$dados = json_encode($dados);
			
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				"Content-Type: application/json",
				"app_token: BzL462rPGlXD",
				"access_token: H3mUEJQd37E1"
			));
			
			$response = curl_exec($ch);
			if(curl_getinfo($ch, CURLINFO_HTTP_CODE) >= 300){
				echo json_encode(array('status' => '0', 'msg' => $json['msg']));
				return false;
			}
			
			curl_close($ch);
			
			$a = json_decode($response, true);

			return $a['url'];

		} catch (Exception $e){
			echo json_encode(array('status' => '0', 'msg' => 'Não foi possível finalizar a contratação. Por favor, verifique sua conexão ou contate nosso suporte'));
			return false;
		}
        
    }


	function insereCartao($parametros, $idCli){ //retorna o id ou false se der errado

		global $conCad;
		$email = $parametros['emailFinanceiro'];

		$cadastrouCliente = cadastraCliente($parametros);

		if(!$cadastrouCliente){
			return false;
		}
		
		$idSuperLogica = $cadastrouCliente;

		$inseriuCartaoCliente = insereCartaoCliente($email, $parametros['bandeira']);

		if(!$inseriuCartaoCliente){
			return false;
		}

		$linkInsereCartao = $inseriuCartaoCliente;	

		$qUpdateCliente = "UPDATE cliente SET CLI_id_sl = '$idSuperLogica' WHERE CLI_id = $idCli";
		$resultUpdateCliente = mysqli_query($conCad,$qUpdateCliente);

		if(!$resultUpdateCliente){
			echo json_encode(array('status' => '0', 'msg' => 'Não foi possível finalizar a contratação. Por favor, verifique sua conexão ou contate nosso suporte'));
			return false;
		}

		$qUpdateCliente = "UPDATE controle SET CONT_contratou_cartao = 2 WHERE CONT_id_cli = $idCli";
		$resultUpdateCliente = mysqli_query($conCad,$qUpdateCliente);

		if(!$resultUpdateCliente){
			echo json_encode(array('status' => '0', 'msg' => 'Não foi possível finalizar a contratação. Por favor, verifique sua conexão ou contate nosso suporte'));
			return false;
		}
		
		return $linkInsereCartao;

	}

	function insereCobranca($idCli, $idCliSuperLogica){

		global $conCad;

		//pega dois anos a frenteL
		$ano = date("Y");
		$ano += 2;

		try{
			$ch = curl_init();		

			$url = "https://api.superlogica.net/v2/financeiro/cobranca?status=todos&todasDoClienteComIdentificador=&doClienteComId=".$idCliSuperLogica."&dtInicio=01%2F01%2F18&dtFim=12%2F31%2F".$ano;
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_HEADER, FALSE);

			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
					  	"Content-Type: application/json",
						"app_token: BzL462rPGlXD",
					    "access_token: H3mUEJQd37E1"
					));

			$response = curl_exec($ch);


			if(curl_getinfo($ch, CURLINFO_HTTP_CODE) != 200){
				echo json_encode(array('status' => '0', 'msg' => $json['msg']));
				$myfile = fopen("newfile2.txt", "w") or die("Unable to open file!");
				$txt = $response . "\t" . curl_getinfo($ch, CURLINFO_HTTP_CODE);
				fwrite($myfile, $txt);
				fclose($myfile);
				return false;
			}

			curl_close($ch);

			$json = json_decode($response, true);

			$limit = count($json);
			$i = 0;

			for($i = 0; $i < $limit; $i++){
				 
				$idTrans = $json[$i]['id_recebimento_recb'];
				$dataEmiss = $json[$i]['dt_geracao_recb'];
				$valor = $json[$i]['vl_emitido_recb']."<br>";
				$status = $json[$i]['fl_status_recb'];
				$dataVenc = $json[$i]['dt_vencimento_recb'];
				$dataLiq = $json[$i]['dt_liquidacao_recb'];
				$link2Via = $json[$i]['link_2via'];


				$qInsertPag = "INSERT INTO pagamento 
				(PAG_id_cli, PAG_id_trans, PAG_data_emiss, PAG_valor, PAG_status, PAG_data_venc, PAG_data_liq, PAG_link_2_via) VALUES 
				('$idCli', '$idTrans', STR_TO_DATE('$dataEmiss', '%m/%d/%Y'), '$valor', '$status', STR_TO_DATE('$dataVenc', '%m/%d/%Y'), STR_TO_DATE('$dataLiq', '%m/%d/%Y'), '$link2Via') ON DUPLICATE KEY UPDATE PAG_valor = '$valor', PAG_status = '$status', PAG_data_venc = STR_TO_DATE('$dataVenc', '%m/%d/%Y'), PAG_data_liq = STR_TO_DATE('$dataLiq', '%m/%d/%Y'), PAG_link_2_via = '$link2Via'"; //precisa de atenção. Ao atualizar o dia de pagamento tem que descongelar o cliente
				$resultInsertPag = mysqli_query($conCad,$qInsertPag);

				if(!$resultInsertPag){
					echo json_encode(array('status' => '0', 'msg' => 'Não foi possível finalizar a contratação. Por favor, verifique sua conexão ou contate nosso suporte'));
					return false;
				}
			}
		} catch (Exception $e){

		}

		return true;

	}

	function insereWidget($widget){
		global $conCad;
		global $idCli;

		//PROVISÓRIO ATÉ IMPLEMENTAR O FRONT DE CADA UMA DESSAS WIDGETS
		//	 TMB NÃO CRIA PRO MELHOR AVALIADOS
		$widgetProblematicas = ["25"];
		if(in_array($widget[0], $widgetProblematicas)){
			return true;
		}

		//se tem mais de um formato, pega o primeiro
		$formato = explode("-", $widget[1])[0];
		//pega todas as paginas nas quais esse widget pode ser implementado
		$paginas = explode("-", $widget[3]);

		$token = sha1($idCli);

		//pra cada uma das páginas, insere com o id da página
		//1:home; 2:produto; 3:busca; 4:categoria; 5:carrinho; 6:compra; 7:busca vazia
		foreach ($paginas as $pagina) { 
			$insertWidgets = "INSERT INTO widget (WID_id_cli,  WID_owa_token,  WID_inteligencia,  WID_formato,  WID_nome,  WID_texto,  WID_status,  WID_data, WID_pagina) VALUES 
			('$idCli', '$token', ".$widget[0].", ".$formato.", '".$widget[2]."', '".$widget[4]."', 0, CURRENT_DATE(), $pagina)";
			$queryInsertWidgets = mysqli_query($conCad, $insertWidgets);

			if(!$queryInsertWidgets){
				echo json_encode(array('status' => '0', 'msg' => 'Erro inesperado. Por favor, verifique sua conexão ou contate nosso suporte'));
				return false;
			}

			$ultimoId = mysqli_insert_id($conCad);


			if($widget[5]){ // se possui configuração, insere um registro em widget_config vazio
				$insertWidgets = "INSERT INTO widget_config (WC_id_wid) VALUES ('$ultimoId')";
				$queryInsertWidgets = mysqli_query($conCad, $insertWidgets);

				if(!$queryInsertWidgets){
					echo json_encode(array('status' => '0', 'msg' => 'Erro inesperado. Por favor, verifique sua conexão ou contate nosso suporte'));
					return false;
				}
			}						
		}

		return true;

	}

	function retornaLinkCobranca($idCli){
		global $conCad;

		$qSelectCobranca = "SELECT PAG_link_2_via FROM pagamento WHERE PAG_id_cli = $idCli";
		$resultSelectCobranca = mysqli_query($conCad,$qSelectCobranca);

		$array = mysqli_fetch_array($resultSelectCobranca);

		$link = $array['PAG_link_2_via'];

		return $link;

	}

	function integra_api_slack_app($nome, $email, $telefone, $plano, $tempo, $metodoPagamento, $cupom){

		$valores = array(
			30 => array(0, 0, 39.0, 99.0),
			360 => array(0, 0, 374.4, 950.4)
		);
		$nomesPlanos = array(
			2 => "Startup",
			3 => "Pro"
		);
		$freq = array(
			30 => "Mensal",
			360 => "Anual"
		);
		$valor = $valores[$tempo][$plano];
		$valor = "R$ ".number_format($valor, 2, ',', '.');
		$plano = $nomesPlanos[$plano] . " " . $freq[$tempo];



		$msg = '{'; 
		$msg .= '"text" : "';
		$msg .= 'Nome do Cliente: '.$nome;
		$msg .= '\nE-mail: '.$email;
		$msg .= '\nTelefone: '.$telefone;
		$msg .= '\nPlano: '.$plano;
		$msg .= '\nValor do plano: '.$valor;
		$msg .= '\nMetodo de Pagamento: '.$metodoPagamento;
		$msg .= '\nCupom: '.$cupom;
		$msg .= '"';
		$msg .= ',"username": "nova_contratacao"';
		$msg .= '}';


		try{

			$ch = curl_init();

			curl_setopt($ch, CURLOPT_URL, "https://hooks.slack.com/services/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");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $msg);
			curl_setopt($ch, CURLOPT_POST, 1);

			$headers = array();
			$headers[] = "Content-Type: application/x-www-form-urlencoded";
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

			$result = curl_exec($ch);
			curl_close ($ch);

			$json = json_decode($result, true);
			$retorno = $json;
		} catch (Exception $e) {
			$retorno = array(
				'status' => 'erro',
				'data' => array(
					'error' => array(
						'code' => 404,
						'message' => 'Erro imprevisto da api Slack App.'
					)
				)
			);
		}

		return $retorno;
	}


	function integra_api_lahar($campos) {
		
		$token_api_lahar = "roihero1ctS81KM5D9EXC7SEa2dNZ7Y7TKmAcrur1EKA2CG9568UoPCibOkSIaw";
		
		$endpoint_full_url = 'https://app.lahar.com.br/api/conversions';
		$campos['token_api_lahar'] = $token_api_lahar;
		$campos['nome_formulario'] = "contratacao";
		$campos['url_origem'] = 'https://www.roihero.com.br/';
		
		try {
			$post_fields = http_build_query($campos);
			$ch = curl_init($endpoint_full_url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_POST, TRUE);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			$curl_response = curl_exec($ch);
			curl_close($ch);
			$json = json_decode($curl_response, true);
			$retorno = $json;
		} catch (Exception $e) {
			$retorno = array(
				'status' => 'erro',
				'data' => array(
					'error' => array(
						'code' => 404,
						'message' => 'Erro imprevisto da api Lahar.'
					)
				)
			);
		}
		return $retorno; 
	}
?>
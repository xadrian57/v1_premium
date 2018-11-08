<?php
	session_name('premium');
	session_start();
	function redirecionaDash(){
		header('Location: ../dashboard/overview');
	}


	include "../bd/conexao_bd_cadastro.php";
	include "../bd/conexao_bd_owa.php";
	//include "central_de_apis.class.php";

	//$centralApis = new CentralDeApis();

	$pedidos = mysqli_real_escape_string($conCad, $_POST['pedidos']);
	$token = mysqli_real_escape_string($conCad, $_POST['token']);
	$telefone = mysqli_real_escape_string($conCad, $_POST['telefone']);
	$senha = mysqli_real_escape_string($conCad, $_POST['senha']);
	$site = mysqli_real_escape_string($conCad, $_POST['site']);
	$nomeResponsavel = mysqli_real_escape_string($conCad, $_POST['nomeResponsavel']);
	//normaliza site
	$proto_scheme = parse_url($site ,PHP_URL_SCHEME);
	if($proto_scheme != 'http' and $proto_scheme != 'https'){
		$site= 'http://'.$site ;
	}
	$telefone = mysqli_real_escape_string($conCad, $_POST['telefone']);
	$plataforma = mysqli_real_escape_string($conCad, $_POST['plataforma']);
	$nome = mysqli_real_escape_string($conCad, $_POST['nomeLoja']);
	if ($token == "" or empty($token) or $telefone == "" or empty($telefone) or $senha == "" or empty($senha) or $site == "" or empty($site)){
		exit("Existem campos vazios");
	}
	$senha = sha1($senha);
	// PEGA EMAIL
	$select = "SELECT CLI_email FROM cliente WHERE CLI_token = '$token'";
	$query = mysqli_query($conCad,$select);
	$email = mysqli_fetch_array($query)['CLI_email'];
	$idPlataforma = 0;
	$plataforma = strtolower($plataforma);
	if($plataforma == "vtex"){
		$idPlataforma = 1;
	} elseif ($plataforma == "loja_integrada"){
		$idPlataforma = 2;
	} elseif ($plataforma == "woo_commerce"){
		$idPlataforma = 3;
	} elseif ($plataforma == "magento"){
		$idPlataforma = 4;
	} elseif ($plataforma == "iset"){
		$idPlataforma = 5;
	} elseif ($plataforma == "anyshop"){
		$idPlataforma = 6;
	} elseif ($plataforma == "signativa"){
		$idPlataforma = 7;
	} elseif ($plataforma == "rp_commerce"){
		$idPlataforma = 8;
	} elseif ($plataforma == "xtech"){
		$idPlataforma = 9;
	} elseif ($plataforma == "plataforma_online"){
		$idPlataforma = 10;
	} elseif ($plataforma == "moovin"){
		$idPlataforma = 11;
	} elseif ($plataforma == "e-com_club"){
		$idPlataforma = 12;
	} elseif ($plataforma == "tmw"){
		$idPlataforma = 13;
	} elseif ($plataforma == "irroba"){
		$idPlataforma = 14;
	} elseif ($plataforma == "adsmanager"){
		$idPlataforma = 15;
	} elseif ($plataforma == "luqro"){
		$idPlataforma = 16;
	} elseif ($plataforma == "vannon"){
		$idPlataforma = 17;
	} elseif ($plataforma == "piraweb"){
		$idPlataforma = 18;
	} elseif ($plataforma == "tray"){
		$idPlataforma = 19;
	} elseif ($plataforma == "a2store"){
		$idPlataforma = 20;
	} elseif ($plataforma == "bizcommerce"){
		$idPlataforma = 21;
	} elseif ($plataforma == "ecshop"){
		$idPlataforma = 22;
	} elseif ($plataforma == "nuvemshop"){
		$idPlataforma = 23;
	} elseif ($plataforma == "rakuten"){
		$idPlataforma = 24;
	} elseif ($plataforma == "n2nvirtual"){
		$idPlataforma = 25;
	} 
	$queryCLI = false;
	if(!empty($token) && $token != ''){
		// UPDATE NA TABELA CLIENTE
		$updateCLI = "UPDATE cliente SET CLI_token = NULL, CLI_nome = '$nome', CLI_site = '$site', CLI_id_plataforma = $idPlataforma, CLI_plataforma = '$plataforma', CLI_senha = '$senha', CLI_ativo = 1 WHERE CLI_token = '$token'";
		$queryCLI = mysqli_query($conCad, $updateCLI);
	}

	// se a query for executada com sucesso, loga no dash
	if ($queryCLI) {
		// A vriavel $result pega as varias $email e $senha, faz uma pesquisa na tabela de usuarios
		$query = "SELECT CLI_nome, CLI_id, CLI_ativo FROM cliente WHERE CLI_email = '$email' AND CLI_senha = '$senha'";
		$result = mysqli_query($conCad,$query);
		
		/* Logo abaixo temos um bloco com if e else, verificando se a variável $result foi bem sucedida,
		ou seja se ela estiver encontrado algum registro idêntico o seu valor será igual a 1, se não,
		se não tiver registros seu valor será 0. Dependendo do resultado ele redirecionará para a pagina index.html ou
		retornara  para a pagina do formulário inicial para que se possa tentar novamente realizar o email */
		
		
		
		if(mysqli_num_rows($result) > 0)
		{
			$array = mysqli_fetch_array($result);
			$ativado = $array['CLI_ativo'];
			$id = $array['CLI_id'];
			$nome = $array['CLI_nome'];
			//insert na tabela cadastro com o telefone
			$qInsertCAD = "INSERT INTO cadastro (CAD_id_cli, CAD_telefone, CAD_nome_cli) VALUES ($id, '$telefone', '$nomeResponsavel')";
			$resultInsertCad = mysqli_query($conCad,$qInsertCAD);
			//insere registro em config
			$qInsertConfig = "INSERT INTO config (CONF_id_cli, CONF_cor_prim, CONF_cor_sec) VALUES ($id, '000', '888888')";
			$resultInsertConfig = mysqli_query($conCad,$qInsertConfig);
			//controle de pixel
			$qInsertControle = "INSERT INTO controle (CONT_id_cli, CONT_pixel_instalado) VALUES ($id, 0) ON DUPLICATE KEY UPDATE CONT_pixel_instalado = 0";
			$resultInsertControle = mysqli_query($conCad,$qInsertControle);
			// insere as widgets "padrão" no banco depois que deu certo a criação do cliete
			/*
			Páginas do widget:
			0: todas
			1: home
			2: produto
			3: busca vazia
			4: categoria
			5: carrinho
			*/
			$formatos = 1;
			$arrayWidgets = array(            
				1	=> array(1, $formatos, "Mais Desejados", "1-7", "Os produtos mais desejados", false),
				2	=> array(3, $formatos, "Mais Vendidos da Categoria", "4", "Os mais vendidos da categoria", false),
				3	=> array(4, $formatos, "Remarketing On-site", "1-2-7", "Os produtos que você queria!", false),
				4	=> array(5, $formatos, "Similar Por Produto", "2", "Aproveite também essas ofertas!", false),
				5	=> array(10, 6, "Oferta Limitada", "1", "Oferta Limitada", true),
				6	=> array(13, 5, "Não vá Embora", "0", "Olhe o que separamos para você!", true),
				7	=> array(15, $formatos, "Lançamentos", "1-7", "Os Lançamentos", false),
				8	=> array(2, $formatos, "Mais Vendidos", "1-7", "Os mais desejados!", false),
				9	=> array(6, "1", "Liquidação", "1-7", "Aproveite essas promoções!", false),
				// A PEDIDO DE PAULO - 25/05/2018  // 10	=> array(14, $formatos, "Baixou de Preço", "1-7", "Olhe essas Ofertas", false)
			);
			$token = sha1($id);
			$i = 1;
			while ($i <= 9){
				//pega todas as paginas nas quai esse widget pode ser implementado
				$paginas = explode("-", $arrayWidgets[$i][3]);
				//pra cada uma das páginas, insere com o id da página
				//1:home; 2:produto; 3:busca vazia; 4:categoria; 5:carrinho
				foreach ($paginas as $pagina) {
					// -- PEDIDO DO PAULO 05/04 - PROVISÓRIO
					if($idPlataforma == 2){ // SE FOR LOJA INTEGRADA, INSERE AS DIVS DEFINIDAS PELO PAULO
						//if($pagina == "3" or $pagina == "7")
							//continue; // não insere widget pra busca vazia
						if($i == 1 and $pagina == 1){ // se for Mais desejados na home
							$insertWidgets = "INSERT INTO widget
							(WID_id_cli,  WID_owa_token,  WID_inteligencia,  WID_formato,  WID_nome,  WID_texto,  WID_status,  WID_data, WID_pagina, WID_div)
							VALUES
							('$id', '$token', ".$arrayWidgets[$i][0].", ".$arrayWidgets[$i][1].", '".$arrayWidgets[$i][2]."', '".$arrayWidgets[$i][4]."', 1, CURRENT_DATE(),'".$pagina."', 'blank-home-position3')";
							$queryInsertWidgets = mysqli_query($conCad, $insertWidgets);
						} elseif($i == 3 and $pagina == 1) { // se for remarketing na home
							$insertWidgets = "INSERT INTO widget
							(WID_id_cli,  WID_owa_token,  WID_inteligencia,  WID_formato,  WID_nome,  WID_texto,  WID_status,  WID_data, WID_pagina, WID_div)
							VALUES
							('$id', '$token', ".$arrayWidgets[$i][0].", ".$arrayWidgets[$i][1].", '".$arrayWidgets[$i][2]."', '".$arrayWidgets[$i][4]."', 1, CURRENT_DATE(),'".$pagina."', 'blank-home-position4')";
							$queryInsertWidgets = mysqli_query($conCad, $insertWidgets);
						} elseif($i == 3 and $pagina == 2) { // se for remarketing no produto
							$insertWidgets = "INSERT INTO widget
							(WID_id_cli,  WID_owa_token,  WID_inteligencia,  WID_formato,  WID_nome,  WID_texto,  WID_status,  WID_data, WID_pagina, WID_div)
							VALUES
							('$id', '$token', ".$arrayWidgets[$i][0].", ".$arrayWidgets[$i][1].", '".$arrayWidgets[$i][2]."', '".$arrayWidgets[$i][4]."', 1, CURRENT_DATE(),'".$pagina."', 'blank-product-position3')";
							$queryInsertWidgets = mysqli_query($conCad, $insertWidgets);
						} elseif($i == 4 and $pagina == 2) { // se for similar por produto na página de produto
							$insertWidgets = "INSERT INTO widget
							(WID_id_cli,  WID_owa_token,  WID_inteligencia,  WID_formato,  WID_nome,  WID_texto,  WID_status,  WID_data, WID_pagina, WID_div)
							VALUES
							('$id', '$token', ".$arrayWidgets[$i][0].", ".$arrayWidgets[$i][1].", '".$arrayWidgets[$i][2]."', '".$arrayWidgets[$i][4]."', 1, CURRENT_DATE(),'".$pagina."', 'blank-product-position1')";
							$queryInsertWidgets = mysqli_query($conCad, $insertWidgets);
						} elseif($i == 8 and $pagina == 1) { // mais vendidos home
							$insertWidgets = "INSERT INTO widget
							(WID_id_cli,  WID_owa_token,  WID_inteligencia,  WID_formato,  WID_nome,  WID_texto,  WID_status,  WID_data, WID_pagina, WID_div)
							VALUES
							('$id', '$token', ".$arrayWidgets[$i][0].", ".$arrayWidgets[$i][1].", '".$arrayWidgets[$i][2]."', '".$arrayWidgets[$i][4]."', 1, CURRENT_DATE(),'".$pagina."', 'blank-home-position3')";
							$queryInsertWidgets = mysqli_query($conCad, $insertWidgets);
						} elseif($i == 9 and $pagina == 1) { // liquidação home
							$insertWidgets = "INSERT INTO widget
							(WID_id_cli,  WID_owa_token,  WID_inteligencia,  WID_formato,  WID_nome,  WID_texto,  WID_status,  WID_data, WID_pagina, WID_div)
							VALUES
							('$id', '$token', ".$arrayWidgets[$i][0].", ".$arrayWidgets[$i][1].", '".$arrayWidgets[$i][2]."', '".$arrayWidgets[$i][4]."', 1, CURRENT_DATE(),'".$pagina."', 'blank-home-position3')";
							$queryInsertWidgets = mysqli_query($conCad, $insertWidgets);
						} elseif($i == 7 and $pagina == 1) { // novidades home
							$insertWidgets = "INSERT INTO widget
							(WID_id_cli,  WID_owa_token,  WID_inteligencia,  WID_formato,  WID_nome,  WID_texto,  WID_status,  WID_data, WID_pagina, WID_div)
							VALUES
							('$id', '$token', ".$arrayWidgets[$i][0].", ".$arrayWidgets[$i][1].", '".$arrayWidgets[$i][2]."', '".$arrayWidgets[$i][4]."', 1, CURRENT_DATE(),'".$pagina."', 'blank-home-position3')";
							$queryInsertWidgets = mysqli_query($conCad, $insertWidgets);
						} elseif($i == 6){ // se overlay de saída
							$insertWidgets = "INSERT INTO widget
							(WID_id_cli,  WID_owa_token,  WID_inteligencia,  WID_formato,  WID_nome,  WID_texto,  WID_status,  WID_data, WID_pagina, WID_div)
							VALUES
							('$id', '$token', ".$arrayWidgets[$i][0].", ".$arrayWidgets[$i][1].", '".$arrayWidgets[$i][2]."', '".$arrayWidgets[$i][4]."', 1, CURRENT_DATE(),'".$pagina."', 'rhOverlay')";
							$queryInsertWidgets = mysqli_query($conCad, $insertWidgets);
						} elseif($i == 5){ // se oferta limitada
							$insertWidgets = "INSERT INTO widget
							(WID_id_cli,  WID_owa_token,  WID_inteligencia,  WID_formato,  WID_nome,  WID_texto,  WID_status,  WID_data, WID_pagina, WID_div)
							VALUES
							('$id', '$token', ".$arrayWidgets[$i][0].", ".$arrayWidgets[$i][1].", '".$arrayWidgets[$i][2]."', '".$arrayWidgets[$i][4]."', 1, CURRENT_DATE(),'".$pagina."', 'rhOfertaLimitada')";
							$queryInsertWidgets = mysqli_query($conCad, $insertWidgets);
						} else {
							$insertWidgets = "INSERT INTO widget
							(WID_id_cli,  WID_owa_token,  WID_inteligencia,  WID_formato,  WID_nome,  WID_texto,  WID_status,  WID_data, WID_pagina)
							VALUES
							('$id', '$token', ".$arrayWidgets[$i][0].", ".$arrayWidgets[$i][1].", '".$arrayWidgets[$i][2]."', '".$arrayWidgets[$i][4]."', 1, CURRENT_DATE(),'".$pagina."')";
							$queryInsertWidgets = mysqli_query($conCad, $insertWidgets);
						}
					} elseif($idPlataforma == 9 || $idPlataforma == 12){
						if($i == 1 and $pagina == 1){ // se for Mais desejados na home
							$insertWidgets = "INSERT INTO widget
							(WID_id_cli,  WID_owa_token,  WID_inteligencia,  WID_formato,  WID_nome,  WID_texto,  WID_status,  WID_data, WID_pagina, WID_div)
							VALUES
							('$id', '$token', ".$arrayWidgets[$i][0].", ".$arrayWidgets[$i][1].", '".$arrayWidgets[$i][2]."', '".$arrayWidgets[$i][4]."', 1, CURRENT_DATE(),'".$pagina."', 'Rh_home1')";
							$queryInsertWidgets = mysqli_query($conCad, $insertWidgets);
						} elseif($i == 3 and $pagina == 1) { // se for remarketing na home
							$insertWidgets = "INSERT INTO widget
							(WID_id_cli,  WID_owa_token,  WID_inteligencia,  WID_formato,  WID_nome,  WID_texto,  WID_status,  WID_data, WID_pagina, WID_div)
							VALUES
							('$id', '$token', ".$arrayWidgets[$i][0].", ".$arrayWidgets[$i][1].", '".$arrayWidgets[$i][2]."', '".$arrayWidgets[$i][4]."', 1, CURRENT_DATE(),'".$pagina."', 'Rh_home2')";
							$queryInsertWidgets = mysqli_query($conCad, $insertWidgets);
						} else {
							$insertWidgets = "INSERT INTO widget
							(WID_id_cli,  WID_owa_token,  WID_inteligencia,  WID_formato,  WID_nome,  WID_texto,  WID_status,  WID_data, WID_pagina)
							VALUES
							('$id', '$token', ".$arrayWidgets[$i][0].", ".$arrayWidgets[$i][1].", '".$arrayWidgets[$i][2]."', '".$arrayWidgets[$i][4]."', 1, CURRENT_DATE(),'".$pagina."')";
							$queryInsertWidgets = mysqli_query($conCad, $insertWidgets);
						}

					} else { // ------ FIM DO PEDIDO DO PAULO
						if($i == 6){ // se overlay de saída
							$insertWidgets = "INSERT INTO widget
							(WID_id_cli,  WID_owa_token,  WID_inteligencia,  WID_formato,  WID_nome,  WID_texto,  WID_status,  WID_data, WID_pagina, WID_div)
							VALUES
							('$id', '$token', ".$arrayWidgets[$i][0].", ".$arrayWidgets[$i][1].", '".$arrayWidgets[$i][2]."', '".$arrayWidgets[$i][4]."', 1, CURRENT_DATE(),'".$pagina."', 'rhOverlay')";
							$queryInsertWidgets = mysqli_query($conCad, $insertWidgets);
						} elseif($i == 5){ // se oferta limitada
							$insertWidgets = "INSERT INTO widget
							(WID_id_cli,  WID_owa_token,  WID_inteligencia,  WID_formato,  WID_nome,  WID_texto,  WID_status,  WID_data, WID_pagina, WID_div)
							VALUES
							('$id', '$token', ".$arrayWidgets[$i][0].", ".$arrayWidgets[$i][1].", '".$arrayWidgets[$i][2]."', '".$arrayWidgets[$i][4]."', 1, CURRENT_DATE(),'".$pagina."', 'rhOfertaLimitada')";
							$queryInsertWidgets = mysqli_query($conCad, $insertWidgets);
						} else {
							$insertWidgets = "INSERT INTO widget
							(WID_id_cli,  WID_owa_token,  WID_inteligencia,  WID_formato,  WID_nome,  WID_texto,  WID_status,  WID_data, WID_pagina)
							VALUES
							('$id', '$token', ".$arrayWidgets[$i][0].", ".$arrayWidgets[$i][1].", '".$arrayWidgets[$i][2]."', '".$arrayWidgets[$i][4]."', 0, CURRENT_DATE(),'".$pagina."')";
							$queryInsertWidgets = mysqli_query($conCad, $insertWidgets);
						}
					}

					$ultimoId = mysqli_insert_id($conCad);


					if($arrayWidgets[$i][5]){ // se possui configuração, insere um registro em widget_config vazio
						$insertWidgets = "INSERT INTO widget_config (WC_id_wid) VALUES ('$ultimoId')";
						$queryInsertWidgets = mysqli_query($conCad, $insertWidgets);
					}
				}
				
				$i++;
			}
			$qUpdateMelhorAvaliados = "UPDATE widget SET WID_status = 2 WHERE WID_inteligencia = 20";
			$resultUpdateMelhorAvaliados = mysqli_query($conCad, $qUpdateMelhorAvaliados);
			//------
			//insere o plano Trial no banco
			$insertTrial = "INSERT INTO plano (PLAN_id_cli, PLAN_id_plano, PLAN_valor, PLAN_views, PLAN_data_venc, PLAN_status) VALUES ('$id', 0, 0, 10000, ADDDATE(CURRENT_DATE, 7), 0)";
			$queryTrial = mysqli_query($conCad, $insertTrial);
			//insere notificação de "Bem-vindo"
			$qNotBemVindo = "INSERT INTO notificacoes (NOT_id_cli, NOT_titulo, NOT_texto, NOT_data, NOT_status, NOT_icone) VALUES ($id, 'Bem-vindo', 'Bem-vindo à Roi Hero! A partir de agora iremos atuar em sua loja para fazer você lucrar muito mais!', CURRENT_DATE(), 1, 'success')";
			$resultNotBemVindo = mysqli_query($conCad, $qNotBemVindo);

			$traducao = array(
					'email_contato' => $email, // Texto, 100 caracteres, email, OBRIGATÓRIO
					'nome_contato' => $nomeResponsavel, // Texto, 100 caracteres
					'nome_empresa' => $nome, // Texto, 100 caracteres
					'site_empresa' => $site, // Texto, 100 caracteres
					'tel_empresa' => $telefone, // Numérico (pode receber número formatado, ex. (14) 3222-1415)
					'email_empresa' => $email // Texto, 100 caracteres, email
			);
			integra_api_lahar($traducao);
			integra_api_slack_app($nome, $plataforma, $email, $telefone, $site, $nomeResponsavel, $pedidos);
			
			if($pedidos == "enterprise" || $pedidos == "enterprise2")
				integra_api_pipedrive($nome, $email, $telefone, $nomeResponsavel);

			if($idPlataforma == 2){ // -- PROVISÓRIO LOJA INTEGRADA
				$css = file_get_contents("../widget/templates/kit_loja_integrada/css/styles.css");
				file_put_contents ( "../widget/css/rh_".sha1($id).".css", $css);
			} else {
				$css = file_get_contents("../widget/templates/kit_1/css/styles.css");
				file_put_contents ( "../widget/css/rh_".sha1($id).".css", $css);
			}
			
			$site_id = sha1($id);
			$qInsertOWA = "INSERT INTO owa_site (id, site_id, domain, name) VALUES (
						'$id',
						'$site_id',
						'$site',
						'$nome'
						)";
			$resultInsertOwa = mysqli_query($conOWA, $qInsertOWA);
			$updateCliOwa = "UPDATE cliente SET CLI_id_owa = '$site_id' WHERE CLI_id = $id";
			$resultUpdateCliOwa = mysqli_query($conCad, $updateCliOwa);

			$select = "SELECT PLAN_id_plano FROM plano WHERE PLAN_id_cli = '$id'";
			$query = mysqli_query($conCad, $select);
			$array = mysqli_fetch_array($query);
			$idPlan = $array['PLAN_id_plano'];
			$_SESSION['nome'] = $nome;
			$_SESSION['email'] = $email;
			$_SESSION['senha'] = $senha;
			$_SESSION['id'] = $id;
			$_SESSION['idPlan'] = $idPlan;
			$_SESSION['idPlataforma'] = $idPlataforma;
			$insert = "INSERT INTO login (LOG_id_cli) VALUES ('$id')";
			$query = mysqli_query($conCad, $insert);
			// verifica se o plano é trial e quantos dias restantes
			$queryPlano = "SELECT PLAN_valor, PLAN_views, PLAN_data_venc, PLAN_status FROM plano WHERE PLAN_id_cli = '$id' and PLAN_id_plano = 0";
			$resultPlano = mysqli_query($conCad, $queryPlano);
			while($arrayPlano = mysqli_fetch_array($resultPlano))
			{
				$dataVencimento = $arrayPlano['PLAN_data_venc'];
			}
			if(mysqli_num_rows($resultPlano) > 0)
			{
				$date1 = date_create("$dataVencimento");
				$date2 = new DateTime('today');
				$diff = date_diff($date1,$date2);
				$diasRestantes =  $diff->format("%a"); //em string
			}
			else
			{
				$diasRestantes =  "-1"; //em string
			}
			redirecionaDash();
			
		}
		// SENHA INCORRETA
		else
		{
			unset ($_SESSION['nome']);
			unset ($_SESSION['email']);
			unset ($_SESSION['senha']);
			unset ($_SESSION['id']);
		}
	}



	function integra_api_lahar($campos) {
    
		$token_api_lahar = "roihero1ctS81KM5D9EXC7SEa2dNZ7Y7TKmAcrur1EKA2CG9568UoPCibOkSIaw";
		$endpoint_full_url = 'https://app.lahar.com.br/api/conversions';
		$campos['token_api_lahar'] = $token_api_lahar;
		$campos['nome_formulario'] = "cadastro";
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
	function integra_api_slack_app($nome, $plataforma, $email, $telefone, $site, $nomeResponsavel, $pedidos){
		
		$msg = '{';
		$msg .= '"text" : "';
		$msg .= 'Nome do Cliente: '.$nome;
		$msg .= '\nPlataforma: '.$plataforma;
		$msg .= '\nE-mail: '.$email;
		$msg .= '\nTelefone: '.$telefone;
		$msg .= '\nSite: '.$site;
		$msg .= '\nNome Responsável: '.$nomeResponsavel;
		$msg .= '\nNúmero de Pedidos: '.$pedidos;
		$msg .= '"';
		$msg .= ',"username": "novo_cadastro"';
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
			if (curl_errno($ch)) {
				echo 'Error:' . curl_error($ch);
			}
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
	function integra_api_pipedrive($nome, $email, $telefone, $nomeResponsavel){
		$idOrg = -1;
		$data = array(
				"name" => $nome
		);
		$data = json_encode($data);
		
		try{
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "https://roihero.pipedrive.com/v1/organizations?api_token=678b0d0abd5976bd462a65aa80dfd56295972124");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
					"Content-Type: application/json",
			));
			$result = curl_exec($ch);
			if (curl_errno($ch)) {
				echo 'Error:' . curl_error($ch);
			}
			curl_close ($ch);
			$json = json_decode($result, true);
			$retorno = $json;
			if($retorno['success'] == 'true') //se a inclusão deu certo, continua e recupera o id da person inserida
				$idOrg = $retorno['data']['id'];
				else //se não teve sucesso, retorna nessa ponto com o erro
					return $retorno;
		} catch (Exception $e) {
			$retorno = array(
					'status' => 'erro',
					'data' => array(
							'error' => array(
									'code' => 404,
									'message' => 'Erro imprevisto da api PipeDrive.'
							)
					)
			);
			return $retorno;
		}
		
		
		
		//depois de org, insere person
		
		$idCliPipe = -1;
		$data = array(
				"name" => $nomeResponsavel,
				"email" => $email,
				"phone" => $telefone,
				"org_id" => $idOrg
		);
		$data = json_encode($data);
		
		try{
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "https://roihero.pipedrive.com/v1/persons?api_token=678b0d0abd5976bd462a65aa80dfd56295972124");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
					"Content-Type: application/json",
			));
			$result = curl_exec($ch);
			if (curl_errno($ch)) {
				echo 'Error:' . curl_error($ch);
			}
			curl_close ($ch);
			$json = json_decode($result, true);
			$retorno = $json;
			if($retorno['success'] == 'true') //se a inclusão deu certo, continua e recupera o id da person inserida
				$idCliPipe = $retorno['data']['id'];
				else //se não teve sucesso, retorna nessa ponto com o erro
					return $retorno;
		} catch (Exception $e) {
			$retorno = array(
					'status' => 'erro',
					'data' => array(
							'error' => array(
									'code' => 404,
									'message' => 'Erro imprevisto da api PipeDrive.'
							)
					)
			);
			return $retorno;
		}
		//depois de inserida a pessoa, insere o Deal
		$data = array(
				"title" => $nome,
				"person_id" => $idCliPipe,
				"stage_id" => 18, //id do stage do pipedrive. Esse stage tá dentro de um funil (q chama pidedrive na api)
				"user_id" => 4141853,
				"org_id" => $idOrg
		);
		$data = json_encode($data);
		$idDeal = -1;
		
		try{
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "https://roihero.pipedrive.com/v1/deals?api_token=678b0d0abd5976bd462a65aa80dfd56295972124");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
					"Content-Type: application/json",
			));
			$result = curl_exec($ch);
			if (curl_errno($ch)) {
				echo 'Error:' . curl_error($ch);
			}
			curl_close ($ch);
			$json = json_decode($result, true);
			$retorno = $json;
			if($retorno['success'] == 'true') //se a inclusão deu certo, continua e recupera o id da person inserida
				$idDeal = $retorno['data']['id'];
				else //se não teve sucesso, retorna nessa ponto com o erro
					return $retorno;
		} catch (Exception $e) {
			$retorno = array(
					'status' => 'erro',
					'data' => array(
							'error' => array(
									'code' => 404,
									'message' => 'Erro imprevisto da api PipeDrive.'
							)
					)
			);
			return $retorno;
		}
		//depois de inserido o Deal, insere a Atividade
		$hoje = date("Y-m-d");
		$umaHoraDepois = (date("H")+1).":".date("i");
		$data = array(
				"subject" => "Ligar",
				"type" => "call",
				"due_date" => $hoje,
				"due_time" => $umaHoraDepois,
				"user_id" => 4141853,
				"deal_id" => $idDeal,
				"person_id" => $idCliPipe,
				"org_id" => $idOrg
		);
		$data = json_encode($data);
		
		try{
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "https://roihero.pipedrive.com/v1/activities?api_token=678b0d0abd5976bd462a65aa80dfd56295972124");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
					"Content-Type: application/json",
			));
			$result = curl_exec($ch);
			if (curl_errno($ch)) {
				echo 'Error:' . curl_error($ch);
			}
			curl_close ($ch);
			$json = json_decode($result, true);
			$retorno = $json;
			if($retorno['success'] != 'true') //se a inclusão deu certo, continua e recupera o id da person inserida
				return $retorno;
				return $result;
		} catch (Exception $e) {
			$retorno = array(
					'status' => 'erro',
					'data' => array(
							'error' => array(
									'code' => 404,
									'message' => 'Erro imprevisto da api PipeDrive.'
							)
					)
			);
			return $retorno;
		}
	}

?>
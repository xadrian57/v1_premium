<?php
	require_once('../../bd/conexao_bd_cadastro.php');
	require_once('../../bd/conexao_bd_dados.php');

	function carregaWids($conCad){
		// CARREGA INFORMACOES WIDGETS
		$idCli = mysqli_real_escape_string($conCad, $_POST['idCli']);
		$query = 'SELECT WID_id, WID_nome, WID_pagina, WID_status, WID_formato, WID_inteligencia FROM widget WHERE WID_id_cli = "'.$idCli.'" AND WID_status <> "2" Order By WID_nome';
		$result = mysqli_query($conCad, $query) or print(mysqli_error());
		$i = 0;
		while ($fields = mysqli_fetch_array($result)) {
			$paginaWidget[$i] = $fields['WID_pagina'];
			$nomeWidget[$i] = $fields['WID_nome'];
			$widAtivo[$i] = $fields['WID_status'];
			$idWidget[$i] = $fields['WID_id'];
			$formatoWidget[$i] = $fields['WID_formato'];
			$inteligenciaWidget[$i] = $fields['WID_inteligencia'];
			$i++;
		}

		// VERIFICA SE É UM WIDGET BÁSICO
		function ehWidgetBasico($formato){
			return ($formato == 6 || $formato == 5);
		}

		$widgetsBasicos = [];
		$widgetsHome = []; 
		$widgetsProduto = []; 
		$widgetsBusca = []; 
		$widgetsCategoria = []; 
		$widgetsCarrinho = [];

		if (isset($idWidget)){
			for ($i=0; $i < count($idWidget); $i++) {

				if ($inteligenciaWidget[$i] == 22) {
					array_push($widgetsBusca, array('nome' => $nomeWidget[$i], 'id' => $idWidget[$i], 'ativo' => $widAtivo[$i]));
				} else {
					switch ($paginaWidget[$i]) {
						case '1': // pagina de busca
							if (ehWidgetBasico($formatoWidget[$i])){
								array_push($widgetsBasicos, array('nome' => $nomeWidget[$i], 'id' => $idWidget[$i], 'ativo' => $widAtivo[$i]));
							} else {
								array_push($widgetsHome, array('nome' => $nomeWidget[$i], 'id' => $idWidget[$i], 'ativo' => $widAtivo[$i]));
							}
							break;
						case '2': // pagina de produto
							if (ehWidgetBasico($formatoWidget[$i])){
								array_push($widgetsBasicos, array('nome' => $nomeWidget[$i], 'id' => $idWidget[$i], 'ativo' => $widAtivo[$i]));
							} else {
								array_push($widgetsProduto, array('nome' => $nomeWidget[$i], 'id' => $idWidget[$i], 'ativo' => $widAtivo[$i]));
							}
							break;
						case '4': // pagina de categoria
							if (ehWidgetBasico($formatoWidget[$i])){
								array_push($widgetsBasicos, array('nome' => $nomeWidget[$i], 'id' => $idWidget[$i], 'ativo' => $widAtivo[$i]));
							} else {
								array_push($widgetsCategoria, array('nome' => $nomeWidget[$i], 'id' => $idWidget[$i], 'ativo' => $widAtivo[$i]));
							}
							break;
						case '5': // pagina de carrinho
							if (ehWidgetBasico($formatoWidget[$i])){
								array_push($widgetsBasicos, array('nome' => $nomeWidget[$i], 'id' => $idWidget[$i], 'ativo' => $widAtivo[$i]));
							} else {
								array_push($widgetsCarrinho, array('nome' => $nomeWidget[$i], 'id' => $idWidget[$i], 'ativo' => $widAtivo[$i]));
							}
							break;			
						default:
							if (ehWidgetBasico($formatoWidget[$i])){
								array_push($widgetsBasicos, array('nome' => $nomeWidget[$i], 'id' => $idWidget[$i], 'ativo' => $widAtivo[$i]));
							} 
							break;
					}
				}
			}
		}
			

		$widgets = array(
			'widgetsHome' => $widgetsHome, 
			'widgetsProduto' => $widgetsProduto, 
			'widgetsBusca' => $widgetsBusca, 
			'widgetsCategoria' => $widgetsCategoria,
			'widgetsCarrinho' => $widgetsCarrinho,
			'widgetsBasicos' => $widgetsBasicos,
			'widgetsBusca' => $widgetsBusca,
		);
		echo json_encode($widgets);
	}

	function carregaInfoWidget($conCad, $id){
		global $conDados;
		$query = 'SELECT WID_status, WID_formato, WID_inteligencia, WID_div_type, WID_hide, WID_show, WID_texto, WID_nome, WID_id, WID_utm, WID_div, WID_updown FROM widget WHERE WID_id ='.$id.'';
		$result = mysqli_query($conCad, $query);
		$result = mysqli_fetch_array($result);

		// CARREGA CONFIGURACOES WIDGETS
		$queryWidConfig = 'SELECT * FROM widget_config WHERE WC_id_wid = '.$id.'';
		$resultWidConfig = mysqli_query($conCad, $queryWidConfig);		

		// CASO Ñ EXISTA O CAMPO CONFIG, INSERE NA TABELA
		if (mysqli_num_rows($resultWidConfig) == 0){
			$resultWidConfig = [];
		} else {
			$resultWidConfig = mysqli_fetch_array($resultWidConfig);
			
			$result['WID_div_type'] = strtoupper($result['WID_div_type']);	

			$resultWidConfig['WC_cj_p'] = explode(",", $resultWidConfig['WC_cj_p']); 
			$resultWidConfig['WC_cj_f'] = explode(",", $resultWidConfig['WC_cj_f']); 

			$resultWidConfig['tx_tipo_pai'] = explode(",", $resultWidConfig['tx_tipo_pai']); 
			$resultWidConfig['tx_tipo_filho'] = explode(",", $resultWidConfig['tx_tipo_filho']); 

			$resultWidConfig['tx_param_pai'] = explode(",", $resultWidConfig['tx_param_pai']); 
			$resultWidConfig['tx_param_filho'] = explode(",", $resultWidConfig['tx_param_filho']); 

			$resultWidConfig['tx_tipo_param_pai'] = explode(",", $resultWidConfig['tx_tipo_param_pai']); 
			$resultWidConfig['tx_tipo_param_filho'] = explode(",", $resultWidConfig['tx_tipo_param_filho']); 

			$resultWidConfig['tx_negativa_pai'] = explode(",", $resultWidConfig['tx_negativa_pai']); 
			$resultWidConfig['tx_negativa_filho'] = explode(",", $resultWidConfig['tx_negativa_filho']); 
		}	
		

		$data = array_merge($result,$resultWidConfig);

		if($data['WID_inteligencia'] == '9' && isset($data['WC_id_produto']) && $data['WC_id_produto'] != ''){
			$idsProdutos = explode("," ,$data['WC_id_produto']);
			$idsProdutos = implode(" or XML_id = ", $idsProdutos);
			
			$selectTitulos = "Select XML_titulo from XML_22 WHERE XML_id = ". $idsProdutos;

			$resultSelectTitulos = mysqli_query($conDados, $selectTitulos);
			if($resultSelectTitulos && mysqli_num_rows($resultSelectTitulos)>0){

				$titulos = [];

				while($row = mysqli_fetch_array($resultSelectTitulos)){
					$titulo = str_replace(",", ".", $row["XML_titulo"]);
					$titulos[] = $titulo;
				}
				$titulos = implode(",", $titulos);
			} else {
				$titulos = "";
			}
			$data["WC_titulos_produtos"] = $titulos;

		}

		echo json_encode($data);
	}

	// ATUALIZA AS INFORMAÇOES DO WIDGET NO BANCO COM O QUE FOI EDITADO
	function atualizaWidget($conCad, $idWid, $info){ 


		$camposBDWID = array( // campos do widget
			'nome'=>'WID_nome',
			'titulo'=>'WID_texto',
			'utm'=>'WID_utm',
			'inteligenciaWidget' => 'WID_inteligencia',
			'widDiv' => 'WID_div',
			'widDivType' => 'WID_div_type',
			'widShow' => 'WID_show',
			'UpDown' => 'WID_updown',
			'widHide' => 'WID_hide',
			'formatoWidget' => 'WID_formato'
			// 'pagina'=>'WID_pagina' não vai ser possível alterar a página, por enquanto
		);
		$camposBDWIDCONFIG = array( // campos configuracao widget
			'produtosCollection' => 'WC_collection',
			'produtosWidget' => 'WC_id_produto',
			'p_chave_pai' => 'WC_cj_p',
			'p_chave_filho' => 'WC_cj_f',
			'p_chave' => 'WC_collection',
			'categoriaManual' => 'WC_categoria',
			'bossChoiceProdId' => 'WC_id_produto',
			'tp_chave_pai' => 'tx_tipo_pai',
			'tp_chave_filho' => 'tx_tipo_filho',
			'parametro_pai' => 'tx_param_pai',
			'parametro_filho' => 'tx_param_filho',
			'tp_parametro_pai' => 'tx_tipo_param_pai',
			'tp_parametro_filho' => 'tx_tipo_param_filho',
			'negativa_pai' => 'tx_negativa_pai',
			'negativa_filho' => 'tx_negativa_filho',
			'palavrasPaiFilho' => 'WC_cj_p, WC_cj_f'
		);
		foreach($info as $k1 => $v1)
			foreach($v1 as $k => $v)
				if($k == "produtosWidget")
					unset($camposBDWIDCONFIG["bossChoiceProdId"]);

/*
		foreach($compreJunto as $campo => $valor){
			$valor = implode(",", $valor);
			$valor = strtoupper($valor);
			$valores .= ", '".$valor."'";
			$campos .= ", ".$campo;
		}  */

		$updateWid = '';
		$updateWidConfig = '';

		//--tratamentos
		$compreJunto = ['p_chave_pai',
						'p_chave_filho',
						'tp_chave_pai',
						'tp_chave_filho',
						'parametro_pai',
						'parametro_filho',
						'tp_parametro_pai',
						'tp_parametro_filho',
						'negativa_pai',
						'negativa_filho'];


		$primeiros = array(
			'widHide' => [true, 0],
			'widShow' => [true, 0],
			'p_chave_pai' => [true, 0],
			'p_chave_filho'=> [true, 0],
			'tp_chave_pai'=> [true, 0],
			'tp_chave_filho'=> [true, 0],
			'parametro_pai'=> [true, 0],
			'parametro_filho'=> [true, 0],
			'tp_parametro_pai'=> [true, 0],
			'tp_parametro_filho'=> [true, 0],
			'negativa_pai'=> [true, 0],
			'negativa_filho'=> [true, 0]
		);
		$evitar = [];
		$hides = "";
		$shows = "";


		foreach($info as $k1 => $v1){
			foreach($v1 as $k => $v){
				if($k == "widHide"){
					if($primeiros[$k][0]){
						$primeiros[$k][0] = false;
						$primeiros[$k][1] = $k1;
						$info[$k1]->$k = $v;
					} else {
						$info[$primeiros[$k][1]]->$k .= ",".$v;
						$evitar[] = $k1;
					}
				}

				if($k == "widShow"){
					if($primeiros[$k][0]){
						$primeiros[$k][0] = false;
						$primeiros[$k][1] = $k1;
						$info[$k1]->$k = $v;
					} else {
						$info[$primeiros[$k][1]]->$k .= ",".$v;
						$evitar[] = $k1;
					}
				}

				if(in_array($k, $compreJunto)){
					$v = strtoupper($v);
					if($primeiros[$k][0]){
						$primeiros[$k][0] = false;
						$primeiros[$k][1] = $k1;
						$info[$k1]->$k = $v;
					} else {
						$info[$primeiros[$k][1]]->$k .= ",".$v;
						$evitar[] = $k1;
					}
				}

			}
		}

		// -- fim tratamentos

		

		for ($i=0; $i < count($info); $i++) {
			if (in_array($i, $evitar))
				continue;
			foreach ($info[$i] as $key => $value) {
				if($key == "widDiv" and $value == "")
					continue;
				if(in_array($key, $compreJunto))
					$value = strtoupper($value);
				if(isset($camposBDWID[$key])){
					$updateWid = $updateWid.$camposBDWID[$key].' = "'.$value.'", ';
				} 
				else if (isset($camposBDWIDCONFIG[$key])){
					if($key == "palavrasPaiFilho"){
						//$palavrasPaiFilho = str_replace(" ", "", $value);
						$partes = explode(",", $value);
						
						$filhos = [];
						$pais = [];
						foreach($partes as $k => $parte){
							$pai_filho = explode("->", $parte);
							
							$filhos[] = $pai_filho[1];
							$pais[] = $pai_filho[0];
						}
						
						$pais = implode(",", $pais);
						$filhos = implode(",", $filhos);

						$updateWidConfig = 'WC_cj_p = "'.$pais.'", WC_cj_f = "'.$filhos.'", ';
					} else{
						$updateWidConfig = $updateWidConfig.$camposBDWIDCONFIG[$key].' = "'.$value.'", ';
					}
				}
			}
		}

		$updateWid = substr($updateWid,0,-2); // Remove a última vírgula

		$queryWid = 'UPDATE widget SET '.$updateWid.' WHERE WID_id = "'.$idWid.'"';
		$executa = mysqli_query($conCad, $queryWid);

		// Se tiver algum campo adicional de configuracao, executa a query
		if ($updateWidConfig !== ''){
			$updateWidConfig = substr($updateWidConfig,0,-2); // Remove a última vírgula
			$updateWid = substr($updateWid,0,-2);
			$queryWidConfig = 'UPDATE widget_config SET '.$updateWidConfig.' WHERE WC_id_wid = "'.$idWid.'"';
			$executa = mysqli_query($conCad, $queryWidConfig);
		}		
	}

	// ATIVA/DESATIVA WIDGET
	function toggleWidget($conCad, $id, $t){
		echo $t;
		if ($t == 'true' || $t == 'on'){
			$query = 'UPDATE widget SET WID_status = 1 WHERE WID_id = "'.$id.'"';
		} else {
			$query = 'UPDATE widget SET WID_status = 0 WHERE WID_id = "'.$id.'"';
		}
		mysqli_query($conCad,$query) or print(mysqli_error($conCad));
	}

	// SETA O STATUS DO WIDGET PRA 2(APAGADO)
	function deleteWidget($conCad, $id){
		$query = 'UPDATE widget SET WID_status = 2 WHERE WID_id = "'.$id.'"';
		mysqli_query($conCad,$query) or print(mysqli_error($conCad));
	}

	$operacao = mysqli_real_escape_string($conCad, $_POST['op']);
	switch ($operacao) {
		case '1': // CARREGA TODOS WIDGETS
			carregaWids($conCad);
			break;
		case '2': // CARREGA INFORMAÇÕES DO WIDGET
			$idWid = mysqli_real_escape_string($conCad, $_POST['idWid']);
			carregaInfoWidget($conCad,$idWid);
			break;
		case '3': // ATUALIZA INFORMAÇÕES DO WIDGET
			$idWid = mysqli_real_escape_string($conCad, $_POST['idWid']);
			$infoWid = $_POST['widInfo'];
			$infoWid = stripcslashes($infoWid); //removendo caracteres de escape
			$infoWid = (array) json_decode($infoWid);
			atualizaWidget($conCad, $idWid, $infoWid);
			break;
		case '4': // ATIVA/DESATIVA WIDGET
			$idWid = mysqli_real_escape_string($conCad, $_POST['idWid']);
			$toggle = mysqli_real_escape_string($conCad, $_POST['val']);
			toggleWidget($conCad, $idWid, $toggle);
			break;
		case '5': // APAGA WIDGET
			$idWid = mysqli_real_escape_string($conCad, $_POST['idWid']);
			deleteWidget($conCad, $idWid);
			break;
		default:
			break;
	}
	
?>
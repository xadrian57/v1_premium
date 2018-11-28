<?php
	// conexão com banco de dados
	require_once('../../bd/conexao_bd_dados.php');
	require_once('../../bd/conexao_bd_cadastro.php');

	$idCLI = mysqli_real_escape_string($conDados,$_POST['id']);
	$operacao = mysqli_real_escape_string($conDados,$_POST['op']); // 1 consulta, 2 salva

	function getData($conCad, $conDados, $idCLI)
	{		
	// SELECIONA ULTIMA ATUALIZACAO DO XML---------
		$selectAtualizacao = "SELECT CONF_at_xml FROM config WHERE CONF_id_cli = '$idCLI'";
		$queryAtualizacao = mysqli_query($conCad, $selectAtualizacao);
		$arrayAt = mysqli_fetch_array($queryAtualizacao);
		$atualizacao = $arrayAt['CONF_at_xml'];

		$explodeAt = explode(" ", $atualizacao);
		$explodeData = explode("-", $explodeAt[0]);
		$explodeHora = explode(":", $explodeAt[1]);

		$dataAtualizacao = $explodeData[2]."/".$explodeData[1]."/".$explodeData[0];
		$horaAtualizacao = $explodeHora[0].":".$explodeHora[1];

		$data = [];
		$data['ultimaAtualizacao'] = array(
			'data' => $dataAtualizacao,
			'hora' => $horaAtualizacao
		);

	//---------------------------------------------
	// SELECIONA PRODUTOS DA TABELA XML------------
	$select = 'SELECT XML_titulo, XML_id, XML_sku, XML_price, XML_sale_price, XML_type,XML_image_link,XML_link,XML_availability  FROM XML_'.$idCLI;
	$queryProdutos = mysqli_query($conDados, $select);
	// caso exista a tabela do XML criada
	if ($queryProdutos){
		$i = 0;
		while($produtos = mysqli_fetch_array($queryProdutos)){
			$data['produtos'][$i] = array(
				'XML_titulo' => $produtos['XML_titulo'],
				'XML_id' => $produtos['XML_id'],
				'XML_sku' => $produtos['XML_titulo'],
				'XML_price' => $produtos['XML_price'],
				'XML_sale_price' => $produtos['XML_sale_price'],
				'XML_type' => $produtos['XML_type'],
				'XML_image_link' => $produtos['XML_image_link'],
				'XML_link' => $produtos['XML_link'],
				'XML_availability' => $produtos['XML_availability']
			);
			$i++;
		}

		// SELECIONA URL DO XML
		$select = "SELECT CONF_XML FROM config WHERE CONF_id_cli = '$idCLI'";
		$query = mysqli_query($conCad, $select) or print(mysqli_error($conCad));
		$result = mysqli_fetch_array($query);
		$result = ($result['CONF_XML'] === null) ? '':$result['CONF_XML'];
		$data['url'] = urlencode($result);

		if ($data['url'] === ""){
			echo "0";
		} else {
			echo json_encode($data);
		}			
	} else {		
		echo "0";
	}
	//------------------------------------------------
	}

	switch ($operacao) {
		case '1':
			getData($conCad,$conDados, $idCLI);
			break;
	}

?>
<?php
	// conexão com banco de dados
	require_once('../../bd/conexao_bd_cadastro.php');
	require_once('../../bd/conexao_bd_dashboard.php');

	//inclui o objeto de comunicação com a api cloudflare
	include 'api_cloudflare.class.php';

	// sessão
	$idCLI = mysqli_real_escape_string($conCad,$_POST['id']);
	$operacao = mysqli_real_escape_string($conCad,$_POST['op']); // 1 consulta e 2 salva

	function getData($conCad,$idCLI){

	// Site cliente e plataforma----
		$select = "SELECT CLI_site, CLI_id_plataforma FROM cliente WHERE CLI_id = '$idCLI'";
		$query = mysqli_query($conCad,$select);
		$array = mysqli_fetch_array($query);

		$dados = [];
		$dados = array(
			'site' => $array['CLI_site'],
			'plataforma' => $array['CLI_id_plataforma'],
		);

		$data = $dados;
	//-------------------------------

		$selectConf = "SELECT CONF_cor_prim, CONF_cor_sec, CONF_desc_boleto, CONF_tv FROM config WHERE CONF_id_cli = '$idCLI'";
		$queryConf = mysqli_query($conCad, $selectConf) or print(mysqli_error($conCad));
		$result = mysqli_fetch_array($queryConf);

		$dados = [];
		$dados = array(
			'desconto' => $result['CONF_desc_boleto'],
			'corPrimaria' => $result['CONF_cor_prim'],
	        'corSecundaria' => $result['CONF_cor_sec'],
	        'trustvoxAtiva' => ($result['CONF_tv'] == 1 ? true : false),
			'idSHA1' => sha1($idCLI)
		);

		$data = array_merge($data,$dados);

		echo json_encode($data);
	}


	function saveData($conCad,$idCLI,$data)
	{
		$site = mysqli_real_escape_string($conCad,$data['site']);
		$desconto = mysqli_real_escape_string($conCad,$data['desconto']);
		$numeroParcelas = mysqli_real_escape_string($conCad,$data['numeroParcelas']);
		$valorParcelas = mysqli_real_escape_string($conCad,$data['valorParcelas']);
		$corPrimaria = mysqli_real_escape_string($conCad,$data['corPrimaria']);
		$corSecundaria = mysqli_real_escape_string($conCad,$data['corSecundaria']);

		$corPrimaria = str_replace("#", "", $corPrimaria);
		$corSecundaria = str_replace("#", "", $corSecundaria);

		$updateCli = "UPDATE cliente SET CLI_site = '$site' WHERE CLI_id = '$idCLI'";
		$queryCli = mysqli_query($conCad,$updateCli);

		$updateConf = "UPDATE config SET CONF_cor_prim = '$corPrimaria', CONF_cor_sec = '$corSecundaria', CONF_desc_boleto = '$desconto' WHERE CONF_id_cli = '$idCLI'";
		$queryConf = mysqli_query($conCad, $updateConf) or print(mysqli_error($conCad));

		if ($queryConf && $queryCli) {
			echo "1";
		} else {
			echo "0";
		}

		// altera CSS cliente
		// pega id template no banco
		$selectConfig = "SELECT CONF_template FROM config WHERE CONF_id_cli = ".$idCLI;
		$resultConfig = mysqli_query($conCad, $selectConfig);
		$arrayTemplate = mysqli_fetch_array($resultConfig);
		$idTemplate = $arrayTemplate['CONF_template'];
		
		$result = $api->purgeArquivos($ident,$arquivos );
		//echo json_encode($result); 
		// -------
		
		$query = 'SELECT CONF_cor, CONF_template_overlay FROM config WHERE CONF_id_cli = '.$idCLI;
		$exec = mysqli_query($conCad, $query);
		$result = mysqli_fetch_array($exec);
		$template = $result['CONF_template_overlay'];
	
		$colors = json_decode($result['CONF_cor'], true);
		$template = $result['CONF_template_overlay'];
	}

	switch ($operacao) {
		case '1':
			getData($conCad,$idCLI);
			break;
		case '2':
			$data = $_POST;
			saveData($conCad,$idCLI,$data);
			break;
	}


?>
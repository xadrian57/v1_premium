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

		$selectConf = "SELECT CONF_cor, CONF_cor_prim, CONF_cor_sec, CONF_template_overlay FROM config WHERE CONF_id_cli = '$idCLI'";
		$queryConf = mysqli_query($conCad, $selectConf) or print(mysqli_error($conCad));
		$result = mysqli_fetch_array($queryConf);

		$dados = [];
		$dados = array(
			'corPrimaria' => $result['CONF_cor_prim'],
	        'corSecundaria' => $result['CONF_cor_sec'],
			'idSHA1' => sha1($idCLI),
			'cores' => $result['CONF_cor'],
			'templateOverlay' => $result['CONF_template_overlay'],
		);

		$data = array_merge($data,$dados);

		echo json_encode($data);
	}


	function saveData($conCad,$idCLI,$data)
	{
		// cor primária e secundária
		$corPrimaria = mysqli_real_escape_string($conCad,$data['corPrimaria']);
		$corSecundaria = mysqli_real_escape_string($conCad,$data['corSecundaria']);

		$selectPegaCor = 'SELECT CONF_cor from config WHERE CONF_id_cli = '.$idCLI;
		$queryPegaCor = mysqli_query($conCad, $selectPegaCor);
		$cores = mysqli_fetch_array($queryPegaCor)['CONF_cor'];
		$cores = json_decode($cores, true);

		$cores['primary'] = $corPrimaria;
		$cores['secondary'] = $corSecundaria;

		$coresJson = json_encode($cores);
		$updateSalvaCor = "UPDATE config SET CONF_cor = '$coresJson' WHERE CONF_id_cli = '$idCLI'";

		$querySalvaCor = mysqli_query($conCad, $updateSalvaCor);

		$updateCli = "UPDATE cliente SET CLI_site = '$site' WHERE CLI_id = '$idCLI'";
		$queryCli = mysqli_query($conCad,$updateCli);
	
		if ($queryCli && $querySalvaCor && $queryPegaCor) {
			echo "1";
		} else {
			echo "0";
		}
		
		// atualiza css overlays		
		// pega id do template
		$select = 'SELECT CONF_template_overlay FROM config WHERE CONF_id_cli = '.$idCLI;
		$query = mysqli_query($conCad, $select);
		$template = mysqli_fetch_array($query)['CONF_template_overlay'];

		// Pega o arquivo css e substitui as cores
        $css = @file_get_contents('../../widget/templates/overlay/kit_'.$template.'/style_to_replace.css');

        if(!empty($css))
        {
            $css = str_replace( '{PRIMARY_COLOR}' , $corPrimaria , $css );
            $css = str_replace( '{SECONDARY_COLOR}' , $corSecundaria , $css );

            file_put_contents('../../widget/css/overlay/rh_overlay_'.sha1($idCLI).'.css',$css );

            //da purge no cache com a cloudflare
            $api = new cloudflare_api('moises.dourado@roihero.com.br','1404cc5e783d0287897bfb2ebf7faa9e87eb5');

            $ident = $api->identificador('roihero.com.br');

            $arquivos = [
                'https://roihero.com.br/widget/css/overlay/rh_overlay_'.sha1($idCLI).'.css'
            ];

            $api->purgeArquivos($ident,$arquivos);
		}
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
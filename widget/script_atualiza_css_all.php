<?php
	
	//CONEXÃO BD CADASTRO
	include '../bd/conexao_bd_cadastro.php';

	$selectCliente = "SELECT CLI_id FROM cliente WHERE CLI_ativo = 1";
	$reseultCliente = mysqli_query($conCad, $selectCliente);

	while($arrayCli = mysqli_fetch_array($reseultCliente))
	{
		$selectConfig = "SELECT CONF_template FROM config WHERE CONF_id_cli = ".$arrayCli['CLI_id'] ."AND CONF_template = 1";
		$resultConfig = mysqli_query($conCad, $selectConfig);

		$arrayConf = mysqli_fetch_array($resultConfig);

		$css = file_get_contents("templates/kit_".$arrayConf['CONF_template']."/css/styles.css");
		file_put_contents("css/rh_".sha1($arrayCli['CLI_id']).".css", $css);

		echo "CSS Atualizado ID CLiente ".$arrayCli['CLI_id']." Template ".$arrayConf['CONF_template']."<br/>";
	}
	
?>
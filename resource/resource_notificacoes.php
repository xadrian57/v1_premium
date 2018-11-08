<?php

	include "../bd/conexao_bd_cadastro.php";

	//NOT_id NOT_id_cli NOT_titulo NOT_texto NOT_link NOT_icone NOT_data NOT_status
	$qNot = "SELECT NOT_titulo as titulo, NOT_texto as texto, NOT_link as link, NOT_data as data, NOT_status as status FROM notificacoes WHERE NOT_id_cli = $idCli";
	$result = mysqli_query($conCad,$qNot);

	while ($dados = mysqli_fetch_assoc($result)){
		$notificacoes[] = array(
			'titulo' => $dados['titulo'],
			'texto' => $dados['texto'],
			'link' => $dados['link'],
			'data' => $dados['data'],
			'status' => $dados['status']
		);
	}


	echo json_enconde($notificacoes);

?>
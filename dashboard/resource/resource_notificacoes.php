<?php
	include "../../bd/conexao_bd_cadastro.php";

	$idCli = mysqli_real_escape_string($conCad,$_POST['id']);


	function getNotifications($idCli,$conCad)
	{
		//NOT_id NOT_id_cli NOT_titulo NOT_texto NOT_link NOT_icone NOT_data NOT_status
		$qNot = "SELECT NOT_titulo as titulo,NOT_icone as icone, NOT_texto as texto, NOT_link as link, NOT_data as data, NOT_status as status, NOT_id as id FROM notificacoes WHERE NOT_id_cli = $idCli AND NOT_status != 0 ORDER BY NOT_id DESC";		
		$result = mysqli_query($conCad,$qNot) or print(mysqli_error($conCad));
		$notificacoes = [];

		while ($dados = mysqli_fetch_assoc($result)){
			$notificacao = array(
				'titulo' => $dados['titulo'],
				'texto' => $dados['texto'],
				'link' => $dados['link'],
				'data' => $dados['data'],
				'status' => $dados['status'],
				'id' => $dados['id'],
				'icone' => $dados['icone']
			);
			array_push($notificacoes,$notificacao); // empurrando notificacao na array de notificacoes
		}
		$notificacoes = json_encode($notificacoes);
		echo $notificacoes;
	}		

	function updateNotification($idCli,$conCad, $idNot)
	{ 
		$qDelNot = "UPDATE notificacoes SET NOT_status = 2 WHERE NOT_id_cli = $idCli and NOT_id = $idNot";
		$result = mysqli_query($conCad,$qDelNot) or print(mysqli_error($conCad));
	}		

	function deleteNotification($idCli,$conCad, $idNot)
	{
		$qDelNot = "UPDATE notificacoes SET NOT_status = 0 WHERE NOT_id_cli = $idCli and NOT_id = $idNot";
		$result = mysqli_query($conCad,$qDelNot) or print(mysqli_error($conCad));
	}


	// 1 - pega notificacoes do banco
	// 2 - atualiza notificacao no banco, seta como lida
	// 3 - apaga notificacao no banco
	$op = mysqli_real_escape_string($conCad,$_POST['op']);

	switch ($op) {
		case '1':
			getNotifications($idCli,$conCad);
			break;
		case '2':
			$idNot = mysqli_real_escape_string($conCad,$_POST['idNot']);// id notificação
			updateNotification($idCli,$conCad,$idNot);
			break;
		case '3':
			$idNot = mysqli_real_escape_string($conCad,$_POST['idNot']);// id notificação
			deleteNotification($idCli,$conCad,$idNot);
			break;
		default:
			break;
	}	
?>
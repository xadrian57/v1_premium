<?php
	// conexão com banco de dados
	require_once('../../bd/conexao_bd_cadastro.php');

	$idCLI = mysqli_real_escape_string($conCad,$_POST['id']);

	$query1 = "SELECT PLAN_id_plano, PLAN_valor, PLAN_views, PLAN_data_venc FROM plano WHERE PLAN_id_cli = '$idCLI'";
	$result1 = mysqli_query($conCad, $query1);
	$array1 = mysqli_fetch_array($result1);

	$valor = $array1['PLAN_valor'];
	$idPlano = $array1['PLAN_id_plano'];
	$views = $array1['PLAN_views'];
	$expiracao = $array1['PLAN_data_venc'];

	if($idPlano == 42)
	{
	    $views = "Infinito";
	    // todas funcionalidades
	    
	}
	else if($idPlano > 7)
	{
	    // funcionalidades do médio
	   
	}
	else 
	{
	    // funcionalidades do pequeno
	}

	$data = array('valor' => $valor, 'idPlano' => $idPlano, 'views' => $views, 'expiracao' => $expiracao);
	echo json_encode($data);



?>
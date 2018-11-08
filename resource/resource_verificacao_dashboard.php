<?php

	include "../bd/conexao_bd_cadastro.php";

	$qPlano = "SELECT PLAN_id_plano FROM plano WHERE PLAN_id_cli = $idCli";
	$result = mysqli_query($conCad,$qPlano);

	$array = mysqli_fetch_array($result);

	$idPlano = $array['PLAN_id_plano'];
	echo $idPlano;
?>
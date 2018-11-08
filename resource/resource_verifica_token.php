<?php
	require '../bd/conexao_bd_cadastro.php';
	$token = mysqli_real_escape_string($conCad, $_POST['token']);
	$select = "SELECT CLI_token, CLI_ativo FROM cliente WHERE CLI_token = '$token'";
	$query = mysqli_query($conCad,$select);
	$array = mysqli_fetch_array($query);
	$ativo = $array['CLI_ativo'];

	if (mysqli_num_rows($query) === 0 || $ativo == 1) {
		echo 0;
	} else {
		echo 1;
	}
?>
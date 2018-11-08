<?php
	// verifica se o email já está cadastrado no banco
	require_once '../bd/conexao_bd_cadastro.php';

	$email = mysqli_real_escape_string($conCad,$_POST['email']);
	
	$select = "SELECT CLI_email, CLI_ativo FROM cliente WHERE CLI_email = '$email'";
	$query = mysqli_query($conCad,$select);
	$array = mysqli_fetch_array($query);
	$email = $array['CLI_email'];
	$ativo = ($array['CLI_ativo'] == 1) ? true:false;
	if (!$ativo) {
		echo '0';
	} else {
		echo '1';
	}
?>
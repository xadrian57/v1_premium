<?php

	include "../bd/conexao_bd_cadastro.php";

	//pega o id do cliente
	$idCli = mysqli_real_escape_string($conCad, $_POST['id']);

	//pega o site do cliente 
	$qSelectSiteCliente = "SELECT CLI_site FROM cliente WHERE CLI_id = $idCli";
	$resultSelectSiteCliente =  mysqli_query($conCad, $qSelectSiteCliente);
	$site = mysqli_fetch_array($resultSelectSiteCliente)['CLI_site'];


	header("Content-Type: text/plain");

	$ch = curl_init();
	        $timeout = 0;
	        curl_setopt($ch, CURLOPT_URL, $site);
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	        $conteudo = curl_exec ($ch);

	$pos1 = strpos($conteudo, "push(['_storeId', '");
	$pos2 = strpos(substr($conteudo, $pos1), "']);");

	$idTrustvox = substr($conteudo, $pos1+19, $pos2-19);

	$qUpdateCliente = "UPDATE cliente SET CLI_id_tv = '$idTrustvox' WHERE CLI_id = $idCli";
	$resultUpdateCliente = mysqli_query($conCad, $qUpdateCliente);


?>
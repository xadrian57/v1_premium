<?php

	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: POST');

	//CONEXÃO BD Dados
	include '../bd/conexao_bd_dados.php';

	//CONEXÃO BD CADASTRO
	include '../bd/conexao_bd_cadastro.php';

	$idcli_cryp = mysqli_escape_string($conCad, $_POST['idCli']);
    $busca = urldecode(mysqli_escape_string($conCad, $_POST['busca']));

    if(!empty($idcli_cryp) && !empty($busca))
    {
    	$selectCli = "SELECT CLI_id FROM cliente WHERE CLI_id_owa = '$idcli_cryp' AND CLI_ativo = 1";
    	$resultCli = mysqli_query($conCad, $selectCli);

    	if(mysqli_num_rows($resultCli) > 0)
    	{
    		$arrayCli = mysqli_fetch_array($resultCli);

    		$idcli = $arrayCli['CLI_id'];

    		createBusca($conDados, $idcli);

    		$insert = "INSERT INTO BUSCA_".$idcli." (tx_busca) VALUES ('$busca')";
    		mysqli_query($conDados, $insert);
    	}
    }

    function createBusca($conDados, $idcli)
    {
    	$criaBusca = ("CREATE TABLE IF NOT EXISTS BUSCA_".$idcli." (
            id int(10) NOT NULL  AUTO_INCREMENT,
            tx_busca VARCHAR(255) NOT NULL,
            dh_data datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
            )
        ");
    	mysqli_query($conDados, $criaBusca);
    }


?>
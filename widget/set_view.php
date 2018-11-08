<?php

	header('content-type: application/json; charset=utf-8');
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: POST');

	//CONEXÃO BD Dados
	include '../bd/conexao_bd_dados.php';

	//CONEXÃO BD CADASTRO
	include '../bd/conexao_bd_cadastro.php';

	$idcli_cryp = mysqli_escape_string($conDados, $_POST['idcli']);
    $idWids = mysqli_escape_string($conDados, $_POST['idsWidget']);

    if(!empty($idcli_cryp))
    {
    	$selectCli = "SELECT CLI_id FROM cliente WHERE CLI_id_owa = '$idcli_cryp' AND CLI_ativo = 1";
    	$resultCli = mysqli_query($conCad, $selectCli);

    	if(mysqli_num_rows($resultCli) > 0)
    	{
    		$arrayCli = mysqli_fetch_array($resultCli);

    		$idcli = $arrayCli['CLI_id'];

    		$insert = "INSERT INTO VIEW_".$idcli." (VIEW_id_wid) VALUES ('$idWids')";
    		$resultInsert = mysqli_query($conDados, $insert);

    		if(mysqli_affected_rows($conDados))
    		{
    			echo '{
    					"sucess":"Dados inseridos com sucesso.",
    					"status":1
    				}';
    		}
    		else
    		{
    			echo '{
    					"erro":"Falha na inserção dos dados.",
    					"status":0
    				}';
    		}
    	}
    	else
    	{
    		echo '{
    				"erro":"Cliente não encontrado ou desativado.",
    				"status":0
    			}';
    	}
    }
    else
    {
    	echo '{
    			"erro":"Id do cliente vazio.",
    			"status":0
    		}';
    }

?>
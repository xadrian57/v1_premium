<?php
	header('Access-Control-Allow-Origin: '.$_SERVER['HTTP_ORIGIN']);
 	header('Access-Control-Allow-Methods: POST, GET');

 	include "../bd/conexao_bd_cadastro.php";

 	//ve se cliente id existe. ve se tem trustvox. se existir, verifica se tá cadastrando ou atualizando trustvox


 	if(!empty($_GET["trustvoxStoreId"]) and !empty($_GET["clientId"])){
 		$idCli = $_GET["clientId"];
		$qSelectCliente = "SELECT * FROM cliente WHERE CLI_id = $idCli";
		$resultQuery = mysqli_query($conCad, $qSelectJson);

		if (mysql_num_rows($resultQuery) != 0){
			$qUpdateCliente = "UPDATE cliente SET CLI_id_tv = $_GET["trustvoxStoreId"]";
			$resultQuery = mysqli_query($conCad, $qUpdateCliente);

			$qCreateTableTrustVox = "CREATE TABLE admin_cadastro.tv_".$idCli." ( TV_opinion TEXT NULL , TV_rate INT NULL , TV_created_at DATE NULL , TV_user INT NOT NULL , TV_id INT NOT NULL) ENGINE = InnoDB";
			$resultQuery = mysqli_query($conCad, $qCreateTableTrustVox);


		} else {
			echo "{
				 "status": 0,
				 "alerts":[],
				 "errors":["error": "Id do cliente não encontrada"],
				 "obj":[]
				}";

		}

	} else {
		echo "{
			 "status": 0,
			 "alerts":[],
			 "errors":["error": "Entradas não definidas"],
			 "obj":[]
			}";
	}
?>
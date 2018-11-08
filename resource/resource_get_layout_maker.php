<?php
	//conexão com banco
	include "../bd/conexao_bd_dashboard.php";
	

	function getGenders($con){

		$qSelectGender = "SELECT * FROM lm_genero WHERE GEN_ativo = 1";
		$resultQuery = mysqli_query($con, $qSelectGender);

		$result = [];
		$i = 0;

		while($row = mysqli_fetch_array($resultQuery)){
			$result[$i]['name'] = $row['GEN_nome'];
			$result[$i]['id_gender'] = $row['GEN_id'];
			$result[$i]['icon'] = $row['GEN_icone'];
			$i++;
		}

		echo json_encode(array(
			   'status' => 1,
			   'alerts' => array (),
			   'errors' => 
				  array (
				  ),
				   'obj' => $result,
			));
	}



	function getKit($con, $idGen){
		$qSelectKits = "SELECT * FROM lm_kits WHERE KIT_gender = $idGen";
		$resultQuery = mysqli_query($con, $qSelectKits);

		$result = [];
		$i = 0;

		while($row = mysqli_fetch_array($resultQuery)){
			$result[$i]['name'] = $row['KIT_nome'];
			$result[$i]['id'] = $row['KIT_id'];
			$result[$i]['img'] = $row['KIT_imagem'];
			$i++;
		}

		echo json_encode(array(
			   'status' => 1,
			   'alerts' => array (),
			   'errors' => 
				  array (
				  ),
				   'obj' => $result,
			));
	}

	function getJsonKit($con, $idKit){
		$qSelectJson = "SELECT KIT_json FROM lm_kits WHERE KIT_id = $idKitKit";
		$resultQuery = mysqli_query($con, $qSelectJson);

		$json = mysqli_fetch_array($resultQuery);

		echo $json['KIT_json'];
	}

	
	if(!empty($_GET["tipo"])){
		switch($_GET["tipo"])){
			case "1":
				getGenders($conDash);
				break;
			case "2":
				getKit($conDash);
				break;
			case "3":
				getJsonKit($conDash);
				break;
		}

	} else {
		echo "{
			 "status": 0,
			 "alerts":[],
			 "errors":["error": "Tipo não definido"],
			 "obj":[]
			}";
	}
?>


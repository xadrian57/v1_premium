<?php

	//CONEXÃO BD Dados
	include 'bd/conexao_bd_dados.php';

	//CONEXÃO BD CADASTRO
	include 'bd/conexao_bd_cadastro.php';

	date_default_timezone_set('America/Sao_Paulo');

	$numCliRodando = 0;
	$numReq = 0;
	$numRec = 0;

	$selectCli = "SELECT CLI_id FROM cliente WHERE CLI_ativo = 1";
	$queryCli = mysqli_query($conCad, $selectCli);

	while($arrayCli = mysqli_fetch_array($queryCli))
	{
		$selectVIEW = "SELECT count(*) as total FROM VIEW_".$arrayCli['CLI_id']." WHERE DATE(VIEW_data) = '". date("Y-m-d", strtotime('-1 days')) ."'";
		$queryVIEW = mysqli_query($conDados, $selectVIEW);

		if($queryVIEW)
		{
			$arrayVIEW = mysqli_fetch_array($queryVIEW);

			if($arrayVIEW['total'] > 1)
			{
				$numCliRodando++;
				$numReq += $arrayVIEW['total'];

				$selectVIEWRec = "SELECT VIEW_id_wid FROM VIEW_".$arrayCli['CLI_id']." WHERE DATE(VIEW_data) = '". date("Y-m-d", strtotime('-1 days')) ."'";
				$queryVIEWRec = mysqli_query($conDados, $selectVIEWRec);

				$arrayWid = [];
				$i = 0;

				while($arrayVIEWRec = mysqli_fetch_array($queryVIEWRec))
				{
					$arrayWid[$i] = $arrayVIEWRec['VIEW_id_wid'];
					$i++; 
				}

				$arrayWid = implode(',', $arrayWid);
				$arrayWid = explode(',', $arrayWid);

				$numRec += count($arrayWid);
			}
		}
	}

	echo "Numero de cliente rodando ontem: ". $numCliRodando ." Numero de requisicoes geradas ontem: ". $numReq ." Numero de recomendacoes: ". $numRec;

?>
<?php

	header('content-type: application/json; charset=utf-8');
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: POST');

	//CONEXÃO BD Dados
	include '../bd/conexao_bd_dados.php';

	//CONEXÃO BD CADASTRO
	include '../bd/conexao_bd_cadastro.php';

	$idcli_cryp = mysqli_escape_string($conCad, $_POST['idcli']);
    $idwid = mysqli_escape_string($conCad, $_POST['idwid']);
    $url = mysqli_escape_string($conCad, $_POST['url']);

    if(!empty($idcli_cryp))
    {
    	$selectCli = "SELECT CLI_id FROM cliente WHERE CLI_id_owa = '$idcli_cryp' AND CLI_ativo = 1";
    	$resultCli = mysqli_query($conCad, $selectCli);

    	if(mysqli_num_rows($resultCli) > 0)
    	{
    		$arrayCli = mysqli_fetch_array($resultCli);

    		$idcli = $arrayCli['CLI_id'];

    		$selectWidgets = "SELECT WID_id, WID_show, WID_hide FROM widget WHERE WID_id = '$idwid' AND WID_status = 1";
	    	$resultWidgets = mysqli_query($conCad, $selectWidgets);

	    	if(mysqli_num_rows($resultWidgets) > 0)
	    	{
	    		$arrayWidgets = mysqli_fetch_array($resultWidgets);
				
				if(showHide($url, $arrayWidgets['WID_show'], $arrayWidgets['WID_hide']))
		        {
		        	$posts = [];

		        	$posts[] = buscaTermos($idcli, $conDados);

		        	$posts[] = termosWid($idwid, $conCad);

					$posts[] = getHTML($idcli, $conCad);
					
					$posts[] = getCfg($id, $conCad);
	        		
	        		echo $json_data = json_encode($posts);
		        }
		        else
		    	{
		    		echo '{"erro":"Widget não pode ser exibido nesta página."}';
		    	}
	    	}
	    	else
	    	{
	    		echo '{"erro":"Widget destivado ou não existe."}';
	    	}
    	}
    	else
		{
			echo '{"erro":"Cliente não existe."}';
		}
    }
    else
    {
    	echo '{"erro":"Id do cliente vazio."}';
    }

    function showHide($url, $widShow, $widHide)
	{
		$check = 1;    	

    	if($widShow != NULL && $widShow != "")
    	{
    		$arrayShow = explode(",", $widShow);

    		foreach ($arrayShow as $key => $value) 
    		{
    			if(strcmp($arrayShow[$key], $url))
        		{
        			$check = 0;
        		}
        		else
        		{
        			$check = 1;
        			break;
        		}
    		}
    	}        	

    	if($widHide != NULL && $widHide != "")
    	{
    		$arrayHide = explode(",", $widHide);

    		foreach ($arrayHide as $key => $value) 
    		{
    			if(!strcmp($arrayHide[$key], $url))
        		{
        			$check = 0;
        			break;
        		}
    		}
    	}
        
        return $check;
	}

	function buscaTermos($id, $conDados)
	{
		$result = mysqli_query($conDados, "SHOW TABLES LIKE 'BUSCA_".$id."'");
	    
		if(mysqli_num_rows($result) > 0)
		{
        
            $selectBusca = "SELECT COUNT(id) as total, tx_busca FROM BUSCA_".$id." WHERE DATE(dh_data) BETWEEN SUBDATE(CURRENT_DATE,7) AND DATE(CURRENT_DATE) GROUP BY tx_busca ORDER BY total DESC LIMIT 50";
            $queryBusca = mysqli_query($conDados, $selectBusca);
            
            if(mysqli_num_rows($queryBusca) > 0)
            {
                $k = 0;
                $termos = [];
                while($arrayBusca = mysqli_fetch_array($queryBusca))
                {
                    $termos[$k] = urlencode($arrayBusca['tx_busca']);
                    $k++;
                }
                
                return $termos;
            }
            else
            {
                return array('termos' => '');
            }
		}
		else
		{
		    return array('termos' => '');
		}
	}

	function termosWid($idwid, $conCad)
	{
		$selectWid = "SELECT tx_termo, 
							tx_link,
							tx_titulo,
							tx_descricao,
							tx_imagem 
							FROM widget_config 
							WHERE WC_id_wid = '$idwid'";
        $queryWid = mysqli_query($conCad, $selectWid);
        
        if(mysqli_num_rows($queryWid) > 0)
        {
            $arrayWid = mysqli_fetch_array($queryWid);
            
            return array('termo' => $arrayWid['tx_termo'], 'link' => $arrayWid['tx_link'], 'titulo' => $arrayWid['tx_titulo'], 'descri' => $arrayWid['tx_descricao'], 'imagem' => $arrayWid['tx_imagem']);
        }
        else
        {
            return array('termo' => '', 'link' => '', 'titulo' => '', 'descri' => '', 'imagem' => '');
        }
	}

	function getHTML($id, $conCad)
	{
		$idHash = SHA1($id);
		$html = '';

		// 1 = busca, 2 = autocomplete
		$select = 'SELECT CONF_busca_tipo from config WHERE CONF_id_cli = '.$id;
		$query = mysqli_query($conCad, $select);

		if ($query) {
			$tipo = mysqli_fetch_assoc($query)['CONF_busca_tipo'];

			if ($tipo == 1) {
				$html = file_get_contents("templates/search_".$idHash."/searchbar/searchbar.html");
			} else {
				$html = file_get_contents("templates/search_".$idHash."/autocomplete/autocomplete.html");
			}
		}		
            
        return array('html' => $html);
	}

	function getCfg($id, $conCad) {
		$select = 'SELECT CONF_autocomplete_formato from config WHERE CONF_id_cli = '.$id;
		$query = mysqli_query($conCad, $select);
		$formato = 1;

		if ($query) {
			$formato = mysqli_fetch_assoc($query)['CONF_autocomplete_formato'];
		}

		$cfg = array(
			'formatoAutoComplete' => $formato
		);

		return $cfg;
	}

?>
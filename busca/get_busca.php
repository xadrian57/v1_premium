<?php

	header('content-type: application/json; charset=utf-8');
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: POST, GET');

	//CONEXÃO BD Dados
	include '../bd/conexao_bd_dados.php';

	//CONEXÃO BD CADASTRO
	include '../bd/conexao_bd_cadastro.php';

	//CONEXÃO BD CADASTRO
	include '../bd/conexao_bd_busca.php';

	//FONETIZAR
	include '../../pt_metaphone/portuguese_metaphone.php';
	
	if(count($_POST))
	{
		$idcli_cryp = mysqli_escape_string($conCad, $_POST['idcli']);
	    $busca = trim(mysqli_escape_string($conCad, $_POST['termo']));
	    $encoding = 'UTF-8'; // ou ISO-8859-1...
    	$busca = mb_convert_case($busca, MB_CASE_UPPER, $encoding);
	    $limite = mysqli_escape_string($conCad, $_POST['limite']);
	}
	else
	{
		$idcli_cryp = mysqli_escape_string($conCad, $_GET['idcli']);
	    $busca = urldecode(trim(mysqli_escape_string($conCad, $_GET['termo'])));
	    $encoding = 'UTF-8'; // ou ISO-8859-1...
    	$busca = mb_convert_case($busca, MB_CASE_UPPER, $encoding);
	    $limite = mysqli_escape_string($conCad, $_GET['limite']);
	}

    if(empty($limite))
    {
    	$limite = 24;
    }    

    if(!empty($idcli_cryp))
    {
    	$selectCli = "SELECT CLI_id FROM cliente WHERE CLI_id_owa = '$idcli_cryp' AND CLI_ativo = 1";
    	$resultCli = mysqli_query($conCad, $selectCli);

    	if(mysqli_num_rows($resultCli) > 0)
    	{
    		$arrayCli = mysqli_fetch_array($resultCli);

    		$idcli = $arrayCli['CLI_id'];

    		$busca = retornaSinonimo($idcli, $busca, $conCad);

    		$termo = $busca;

    		$usaFonetico = usaFonetico($idcli_cryp);

    		// if(strlen($busca) < 6)
    		// {
    		// 	$usaFonetico = false;
    		// }    

		    if($usaFonetico)
		    {
		    	$busca = fonetizar($busca);
		    }

			if($busca != '')
		    {
		        $numProd = 0;
		        //$busca = strtoupper($busca);

			   	$busca = str_replace(' ', '* ', $busca);

	            if(!empty($busca))
	            {
	                if($usaFonetico)
	                {
	                	$select = "SELECT id, titulo_fonetico, click, titulo
	                           FROM BUSCA_".$idcli."
	                           WHERE ". consulta($busca, $usaFonetico, str_replace(' ', '* ', $termo)) ."
	                           ". usarCustom($idcli, $termo);
	                
	                	$result = mysqli_query($conBusca, $select);
	                }
	                else
	                {
	                	$select = "SELECT ". campos($usaFonetico) ."
	                           FROM XML_".$idcli."
	                           WHERE XML_availability = 1
	                           ". consulta($busca, $usaFonetico) ."
	                           ". usarCustom($idcli, $busca);
	                
	                	$result = mysqli_query($conDados, $select);
	                }
	                
	        
	                if($result && mysqli_num_rows ($result) > 0 )
	                {
	                    while($linha = mysqli_fetch_array($result))
	                    {
		                   	$posts[] = geraArray($linha, $usaFonetico);
	                    }
	                }
	            }
	            else
	            {
	                break;
	            }

		        if(count($posts))
		        {
			        if($usaFonetico)
			        {
			        	$post = scoreFonetico(fonetizar($termo), $posts);
			        	$post = score($termo, $post);
			        	$post = geraProdsFonetico($post, $idcli, $conDados);

			        	if(count($post) > 0)
			        	{
				        	$post = array_slice($post, 0, $limite, true);
				        }
			        }
			        else
			        {
			        	$post = score($termo, $posts);
			        	$post = array_slice($post, 0, $limite, true);
			        }
			        
			        if(count($post) > 0)
			        {
			            echo $json_data = json_encode($post);
			        }
			        else
			        {
			            echo "[]";
			        }
			    }
			    else
			    {
			    	echo "[]";
			    }
		    }
		    else
		    {
		    	echo '{"erro":"Termo de busca vazio."}';
		    }
		}
		else
		{
			echo '{"erro":"Cliente não encontrado ou desativado."}';
		}
	}
	else
	{
		echo '{"erro":"Id do cliente vazio."}';
	}

	function retornaSinonimo($id, $busca, $conCad)
	{
		$select = "SELECT tx_pesquisado, tx_retornado FROM busca WHERE id_cli = '$id'";
		$result = mysqli_query($conCad, $select);

		$encoding = 'UTF-8'; // ou ISO-8859-1...

		if(mysqli_num_rows($result) > 0)
		{
			$arrayBusca = explode(' ', $busca);

			while($arrayResultado = mysqli_fetch_array($result)) 
			{
				for($i=0; $i < count($arrayBusca); $i++)
				{
					if($arrayBusca[$i] === mb_convert_case(trim($arrayResultado['tx_pesquisado']), MB_CASE_UPPER, $encoding))
					{
						$arrayBusca[$i] = mb_convert_case(trim($arrayResultado['tx_retornado']), MB_CASE_UPPER, $encoding);
						break;
					}
				}
			}

			return implode(' ', $arrayBusca);
		}
		else
		{
			return $busca;
		}
	}

	function ordenaPorScore($a,$b) 
	{
		if ($a['score'] <= $b['score']) {
			return 1;
		} else {
			return 0;
		}
	}

	function scoreFonetico($termo, $result)
	{
		//$termo = strtolower( $termo );
		for($i = 0; $i < count($result); $i++) {
			
			$score = 0; // pontuação de cada produto, referente à similaridade
			
			$nomeProd = urldecode($result[$i]['fonetic_title']);
			//$nomeProd = strtolower( $nomeProd );
			$nomeProd = trim( $nomeProd );
			//$nomeProd = str_replace('_', ' ', $nomeProd);

			if ($termo === $nomeProd) { // se é exatamenten igual
		        $score+=100;
		    }

		    if (strpos($nomeProd, $termo) === 0) { // se o começo é exatamente igual
		    	$score+=50;
		    }

		    if (strpos($nomeProd, $termo) > 0) { // se possui tudo digitado
		    	$score+=50;
		    }

		    if (strpos($nomeProd, $termo)) {
		    	$score+=40;
		    }

		    // palavra por palavra
		    $palavrasTermo = explode(' ',$termo);
		    $palavrasProd = explode(' ', $nomeProd);

		    if (count($palavrasTermo) > 0) {
		    	if ($palavrasTermo[0] === $palavrasProd[0]) { // se a primeira palavra for idêntica
		    		$score+=80;
		    	}

		    	for($j = 0; $j < count($palavrasTermo); $j++) {

		    		$palavrasTermo[$j] = trim( $palavrasTermo[$j] );

		    		if ( !empty($palavrasTermo[$j]) &&// checa se é vazio
		    			 strpos($nomeProd, $palavrasTermo[$j]) > 0 // checa se está dentro do prod
					) {
		    			$score+=30;
					}
		    	}
		    }     

		    if ($score > 0) {
		    	$result[$i]['score'] = $score;
		    }
		}

		//usort($result,'ordenaPorScore');

		$result = array_multi_sort($result,'score','venda'); 

		return $result;
	}

	function score($termo, $result)
	{
		for($i = 0; $i < count($result); $i++) {
			
			$score = $result[$i]['score']; // pontuação de cada produto, referente à similaridade
			
			$nomeProd = urldecode($result[$i]['title']);
			//$nomeProd = strtolower( $nomeProd );
			$encoding = 'UTF-8'; // ou ISO-8859-1...
    		$nomeProd = mb_convert_case($nomeProd, MB_CASE_UPPER, $encoding);
			$nomeProd = trim( $nomeProd );
			//$nomeProd = str_replace('_', ' ', $nomeProd);

			if ($termo === $nomeProd || trataPlural($termo) === $nomeProd) { // se é exatamenten igual
		        $score+=100;
		    }

		    if (strpos($nomeProd, $termo) === 0 || strpos($nomeProd, trataPlural($termo)) === 0) { // se o começo é exatamente igual
		    	$score+=50;
		    }

		    if (strpos($nomeProd, $termo) > 0 || strpos($nomeProd, trataPlural($termo))) { // se possui tudo digitado
		    	$score+=50;
		    }

		    if (strpos($nomeProd, $termo)) {
		    	$score+=40;
		    }

		    // palavra por palavra
		    $palavrasTermo = explode(' ',$termo);
		    $palavrasProd = explode(' ', $nomeProd);

		    if (count($palavrasTermo) > 0) {
		    	if ($palavrasTermo[0] === $palavrasProd[0] || trataPlural($palavrasTermo[0]) === $palavrasProd[0]) { // se a primeira palavra for idêntica
		    		$score+=80;
		    	}

		    	for($j = 0; $j < count($palavrasTermo); $j++) {

		    		$palavrasTermo[$j] = trim( $palavrasTermo[$j] );

		    		if ( !empty($palavrasTermo[$j]) &&// checa se é vazio
		    			 strpos($nomeProd, $palavrasTermo[$j]) > 0 // checa se está dentro do prod
					) {
		    			$score+=30;
					}
		    	}
		    }     

		    if ($score > 0) {
		    	$result[$i]['score'] = $score;
		    }
		}

		//usort($result,'ordenaPorScore');

		$result = array_multi_sort($result,'score','venda'); 

		return $result;
	}

	function array_multi_sort($array,$on1,$on2,$order=SORT_DESC) 
	{
        if(count($array) > 0)
        {
    	    for($i = 0; $i < count($array); $i++){
    	        $one_way_fares[$i] = $array[$i][$on2];
    	        $return_fares[$i] = $array[$i][$on1];
    	    }
    
    	    array_multisort($return_fares,$order,$one_way_fares,$order,$array);
        }

	    return $array;
	}

	function geraArray($linha, $fonetico=false, $score=0)
	{
		if($fonetico)
		{
			return array(
    					'id'=>$linha['id'], 
    					'fonetic_title'=>$linha['titulo_fonetico'],
    					'title'=>$linha['titulo'],
    					'venda' => intval($linha['click']),
        				'score' => 0
        			);
		}
		else
		{
			return array(
    					'id'=> strval($linha['XML_id']), 
    					'sku'=> $linha['XML_sku'], 
    					'title'=> urlencode($linha['XML_titulo']),
    					'in_stock'=> $linha['XML_availability'], 
        				'price'=> floatval($linha['XML_price']), 
        				'sale_price'=> floatval($linha['XML_sale_price']), 
        				'link'=> urlencode($linha['XML_link']), 
        				'link_image'=> urlencode($linha['XML_image_link']), 
        				'type'=> urlencode($linha['XML_type']), 
        				'amount'=> floatval($linha['XML_vparcela']), 
        				'months'=> intval($linha['XML_nparcelas']), 
        				'venda' => intval($linha['XML_click_7']),
        				'desconto' => $linha['XML_desconto'],
        				'score' => $score
        			);
		}
	}

	function geraProdsFonetico($post, $idcli, $conDados)
	{
		$ids = '';

		for($i=0; $i < count($post); $i++)
		{
			$ids .= "','". $post[$i]['id'];
		}

		$select = "SELECT ". campos() ."
           FROM XML_".$idcli."
           WHERE XML_id IN ('". $ids ."')";	
    
    	$result = mysqli_query($conDados, $select);

    	if($result && mysqli_num_rows ($result) > 0 )
        {
            while($linha = mysqli_fetch_array($result))
            {	
            	$auxScore = 0;
            	for($x=0; $x < count($post); $x++)
            	{
            		if($linha['XML_id'] == $post[$x]['id'])
            		{
            			$auxScore = $post[$x]['score'];            			
            			break;
            		}
            	}

               	$posts[] = geraArray($linha, false, $auxScore);
            }
        }

     	$posts = array_multi_sort($posts,'score','venda'); 

        return $posts;
	}

	function usarCustom($idcli, $busca)
	{
		if($idcli == 292 || $idcli == 1210)
		{
			return " OR MATCH(custom_1) AGAINST(\"+ " . $busca . "*\" IN BOOLEAN MODE) ";
		}
		else if($idcli == 598) 
		{
			return " OR id = '". $busca ."' ";
		}
		else
		{
			return " OR MATCH(custom_1) AGAINST(\"+ " . $busca . "*\" IN BOOLEAN MODE) OR id = '". $busca ."' ";
		}
	}

	function consulta($busca, $fonetico, $termo=NULL)
	{
		if($fonetico)
		{
			return "MATCH(titulo) AGAINST(\"+ " . $termo . "*\" IN BOOLEAN MODE)
					OR MATCH(titulo) AGAINST(\"+ " . trataPlural($busca) . "*\" IN BOOLEAN MODE)
					OR MATCH(titulo_fonetico) AGAINST(\"+ " . $busca . "*\" IN BOOLEAN MODE)";
		}
		else
		{
			//return "AND XML_titulo_upper LIKE '%". $busca ."%'";
			return "AND MATCH(XML_titulo_upper) AGAINST(\"+ " . $busca . "*\" IN BOOLEAN MODE)";
		}
	}

	function campos($fonetico=false)
	{
		if($fonetico)
		{
			return "XML_id, 
	                XML_sku, 
	                XML_titulo,
	                XML_titulo_fonetico, 
	                XML_availability, 
	                XML_price, 
	                XML_sale_price, 
	                XML_link, 
	                XML_image_link, 
	                XML_type, 
	                XML_nparcelas, 
	                XML_vparcela, 
	                XML_click_7, 
	                XML_desconto";
		}
		else
		{
			return "XML_id, 
	                XML_sku, 
	                XML_titulo, 
	                XML_availability, 
	                XML_price, 
	                XML_sale_price, 
	                XML_link, 
	                XML_image_link, 
	                XML_type, 
	                XML_nparcelas, 
	                XML_vparcela, 
	                XML_click_7, 
	                XML_desconto";
		}
	}

	function trataPlural($busca)
	{
		$arrayPalavras = explode(' ', $busca);

		for($i = 0; $i < count($arrayPalavras); $i++)
		{
			if(substr($arrayPalavras[$i], -1) == 'S')
			{
				$arrayPalavras[$i] = substr($arrayPalavras[$i], 0, strlen($arrayPalavras[$i]) -1);
			}
		}

		return implode(' ', $arrayPalavras);
	}

	function usaFonetico($idcli_cryp)
	{
		if($idcli_cryp == 'f754e904dfc75ae544977f3f441a1d1b486392cc' || 
			$idcli_cryp == '85d0662ad5825ba33f4259d0d06ac035abac67bf' ||
			$idcli_cryp == '91eb375e8e71d9ce2f7cde8b0a757f66c94c998a' ||
			$idcli_cryp == '683e725c03a87baaad2623231644e944e537acab' ||
			$idcli_cryp == '4a4ab45448022f0c738fe9e310148ea1eb7f856b' ||
			$idcli_cryp == '85f1002bf139bebdb7f0d07b31fa14155aea9dfc' ||
			$idcli_cryp == '7c7b84eeaec18233e982d101637ab2a4033c6fb0' ||
			$idcli_cryp == 'b3f0c7f6bb763af1be91d9e74eabfeb199dc1f1f' ||
			$idcli_cryp == '2ad96cc16e625017327b9aa877bbddfb3532a718' ||
			$idcli_cryp == '5b17adc969018b102b802193f65ffebc07494a2c' ||
			$idcli_cryp == '52fdb9f68c503e11d168fe52035901864c0a4861' ||
			$idcli_cryp == '3d7b4f23b8f853910e4c64f09cdf897a59db524a' ||
			$idcli_cryp == '18c85e8f2c6d60773372ef600c979ff3874a91db')
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function fonetizar($titulo)
	{
	    $arrayPalavras = explode(' ', $titulo);

	    $arrayPalavrasAux = [];
	    
	    for($i=0; $i < count($arrayPalavras); $i++)
	    {
	        $arrayPalavrasAux[] = portuguese_metaphone($arrayPalavras[$i]);
	    }
	    
	    return implode($arrayPalavrasAux, ' ');
	}
?>
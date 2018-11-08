<?php

	header('content-type: application/json; charset=utf-8');
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: POST, GET');

	//CONEXÃO BD Dados
	include '../bd/conexao_bd_dados.php';

	//CONEXÃO BD CADASTRO
	include '../bd/conexao_bd_cadastro.php';
	
	if(count($_POST))
	{
		$idcli_cryp = mysqli_escape_string($conCad, $_POST['idcli']);
	    $busca = strtoupper(trim(mysqli_escape_string($conCad, $_POST['termo'])));
	    $limite = mysqli_escape_string($conCad, $_POST['limite']);
	}
	else
	{
		$idcli_cryp = mysqli_escape_string($conCad, $_GET['idcli']);
	    $busca = urldecode(strtoupper(trim(mysqli_escape_string($conCad, $_GET['termo']))));
	    $limite = mysqli_escape_string($conCad, $_GET['limite']);
	}

    if(empty($limite))
    {
    	$limite = 24;
    }

    $termo = $busca;

    if(!empty($idcli_cryp))
    {
    	$selectCli = "SELECT CLI_id FROM cliente WHERE CLI_id_owa = '$idcli_cryp' AND CLI_ativo = 1";
    	$resultCli = mysqli_query($conCad, $selectCli);

    	if(mysqli_num_rows($resultCli) > 0)
    	{
    		$arrayCli = mysqli_fetch_array($resultCli);

    		$idcli = $arrayCli['CLI_id'];

			if($busca != '')
		    {
		        $numProd = 0;
		        //$busca = strtoupper($busca);
		        $busca = str_replace(' ', '%', $busca);

	        	while (!empty($busca)) 
	        	{		            
		            if(!empty($busca))
		            {
		                $select = "SELECT XML_id, 
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
						                XML_desconto
		                           FROM XML_".$idcli."
		                           WHERE XML_availability = 1
		                           AND (XML_sku LIKE '%". $busca ."%'
		                           OR XML_titulo_upper LIKE '%". $busca ."%')
		                           GROUP BY XML_link";
		                
		                $result = mysqli_query($conDados, $select);
		        
		                if($result && mysqli_num_rows ($result) > 0 )
		                {
		                    while($linha = mysqli_fetch_array($result))
		                    {
		                    	$check = true;
		                    	
		                    	for($i = 0; $i < count($post); $i++)
		                    	{
		                    		if($post[$i]['link'] == $linha['XML_link'])
		                    		{
		                    			$check = false;
		                    			break;
		                    		}
		                    	}
		                    	
		                    	if($check)
		                    	{
			                    	$posts[] = array(
				                    					'id'=> strval($linha['XML_id']), 
				                    					'sku'=> $linha['XML_sku'], 
				                    					'title'=> urlencode($linha['XML_titulo']), 
				                    					'in_stock'=> $linha['XML_availability'], 
		                                				'price'=> floatval($linha['XML_price']), 
		                                				'sale_price'=> floatval($linha['XML_sale_price']), 
		                                				'link'=> urlencode($linha['XML_link']), 
		                                				'link_image'=> urlencode($linha['XML_image_link']), 
		                                				'type'=> urlencode($linha['XML_type']), 
		                                				'amount'=> floatval($linha['XML_nparcelas']), 
		                                				'months'=> intval($linha['XML_vparcela']), 
		                                				'venda' => intval($linha['XML_click_7']),
		                                				'desconto' => $linha['XML_desconto'],
		                                				'score' => 0
		                                			);
		                        }
		                    }
		                }
		            }
		            else
		            {
		                break;
		            }

		            // Recuperando a posição do último espaço
		            $ultEspaco = strrpos($busca, "%");
		            // Limitando a string até a posição do último espaço
		            $busca = substr($busca, 0, $ultEspaco);
	        	}

		        if(count($posts) > 0)
		        {
			        $post = score($termo, $posts);
			        $post = array_slice($post, 0, $limite, true);
			        echo $json_data = json_encode($post);
			    }
			    else
			    {
			    	$select = "SELECT XML_id, 
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
						                XML_desconto
		                           FROM XML_".$idcli."
		                           WHERE XML_availability = 1
		                           GROUP BY XML_link
		                           ORDER BY XML_click_7 DESC";
		                
	                $result = mysqli_query($conDados, $select);
	        
	                if($result && mysqli_num_rows ($result) > 0 )
	                {
	                    while($linha = mysqli_fetch_array($result))
	                    {
	                    	$check = true;
	                    	
	                    	for($i = 0; $i < count($post); $i++)
	                    	{
	                    		if($post[$i]['link'] == $linha['XML_link'])
	                    		{
	                    			$check = false;
	                    			break;
	                    		}
	                    	}
	                    	
	                    	if($check)
	                    	{
		                    	$posts[] = array(
			                    					'id'=> strval($linha['XML_id']), 
			                    					'sku'=> $linha['XML_sku'], 
			                    					'title'=> urlencode($linha['XML_titulo']), 
			                    					'in_stock'=> $linha['XML_availability'], 
	                                				'price'=> floatval($linha['XML_price']), 
	                                				'sale_price'=> floatval($linha['XML_sale_price']), 
	                                				'link'=> urlencode($linha['XML_link']), 
	                                				'link_image'=> urlencode($linha['XML_image_link']), 
	                                				'type'=> urlencode($linha['XML_type']), 
	                                				'amount'=> floatval($linha['XML_nparcelas']), 
	                                				'months'=> intval($linha['XML_vparcela']), 
	                                				'venda' => intval($linha['XML_click_7']),
	                                				'desconto' => $linha['XML_desconto'],
	                                				'score' => 0
	                                			);
	                        }
	                    }
	                }
			    }

			    if(count($posts) > 0)
		        {
			        //$post = score($termo, $posts);
			        $posts = array_slice($posts, 0, $limite, true);
			        echo $json_data = json_encode($posts);
			    }
			    else
			    {
			    	echo '[]';
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

	function ordenaPorScore($a,$b) 
	{
		if ($a['score'] <= $b['score']) {
			return 1;
		} else {
			return 0;
		}
	}

	function score($termo, $result)
	{
		$termo = strtolower( $termo );
		for($i = 0; $i < count($result); $i++) {
			
			$score = 0; // pontuação de cada produto, referente à similaridade
			
			$nomeProd = urldecode($result[$i]['title']);
			$nomeProd = strtolower( $nomeProd );
			$nomeProd = trim( $nomeProd );
			//$nomeProd = str_replace('_', ' ', $nomeProd);

			if ($termo === $nomeProd) { // se é exatamenten igual
		        $score+=100;
		    }

		    if (strpos($nomeProd, $termo) === 0) { // se o começo é exatamente igual
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

		usort($result,'ordenaPorScore');

		return $result;
	}

?>
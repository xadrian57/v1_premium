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

		    $busca = fonetizar($busca);

			if($busca != '')
		    {
		        $numProd = 0;
		        //$busca = strtoupper($busca);

			   	$busca = str_replace(' ', '* ', $busca);

	            if(!empty($busca))
	            {
                	$select = "SELECT id, titulo_fonetico, click, titulo
                           FROM BUSCA_".$idcli."
                           WHERE ". consulta($busca, str_replace(' ', '* ', $termo)) ."
                           ". usarCustom($idcli, $termo);
                
                	$result = mysqli_query($conBusca, $select);
	                
	        
	                if($result && mysqli_num_rows ($result) > 0 )
	                {
	                    while($linha = mysqli_fetch_array($result))
	                    {
		                   	$posts[] = geraArray($linha);
	                    }
	                }
	            }
	            else
	            {
	                break;
	            }

		        if(count($posts))
		        {
		        	$post = scoreFonetico(fonetizar($termo), $posts);
		        	$post = score($termo, $post);			        	

		        	if(count($post) > 0)
		        	{
			        	$post = array_slice($post, 0, $limite, true);
			        	$post = geraProdsFonetico($post, $idcli, $conDados);
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

	function geraArray($linha, $score=0)
	{
		return array(
					'id'=>$linha['id'], 
					'fonetic_title'=>$linha['titulo_fonetico'],
					'title'=>$linha['titulo'],
					'venda' => intval($linha['click']),
    				'score' => 0
    			);
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

               	$posts[] = geraArray($linha, $auxScore);
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
			return " OR custom_1 = '" . $busca . "' OR custom_2 = '" . $busca . "' OR id = '". $busca ."' ";
		}
	}

	function consulta($busca, $termo=NULL)
	{
		return "MATCH(titulo) AGAINST(\"+ " . $termo . "*\" IN BOOLEAN MODE)
				OR MATCH(titulo) AGAINST(\"+ " . trataPlural($termo) . "*\" IN BOOLEAN MODE)
				OR MATCH(titulo_fonetico) AGAINST(\"+ " . $busca . "*\" IN BOOLEAN MODE)";
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
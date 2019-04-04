<?php

namespace roihero\search\util;

class Util
{	
	// Classe apenas para chamada de métodos static
    private function __construct() 
    {
    }

    public static function UP_CASE($str)
    {
	    $encoding = 'UTF-8'; // ou ISO-8859-1...
		return mb_convert_case($str, MB_CASE_UPPER, $encoding);
    }

    public static function retornaSinonimo($arrayBusca, $result)
	{
		while($arrayResultado = mysqli_fetch_array($result)) 
		{
			for($i=0; $i < count($arrayBusca); $i++)
			{
				if($arrayBusca[$i] === self::UP_CASE(trim($arrayResultado['tx_pesquisado'])))
				{
					$arrayBusca[$i] = self::UP_CASE(trim($arrayResultado['tx_retornado']));
					break;
				}
			}
		}

		return implode(' ', $arrayBusca);
	}

	public static function fonetizar($titulo)
	{
	    $arrayPalavras = explode(' ', $titulo);

	    $arrayPalavrasAux = [];
	    
	    for($i=0; $i < count($arrayPalavras); $i++)
	    {
	        $arrayPalavrasAux[] = portuguese_metaphone($arrayPalavras[$i]);
	    }
	    
	    return implode($arrayPalavrasAux, ' ');
	}

	public static function trataPlural($busca)
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

	public static function array_multi_sort($array,$on1,$on2,$order=SORT_DESC) 
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

	public static function scoreFonetico($termo, $result)
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

		$result = self::array_multi_sort($result,'score','venda'); 

		return $result;
	}

	public static function score($termo, $result)
	{
		for($i = 0; $i < count($result); $i++) {
			
			$score = $result[$i]['score']; // pontuação de cada produto, referente à similaridade
			
			$nomeProd = urldecode($result[$i]['title']);
			//$nomeProd = strtolower( $nomeProd );
			$encoding = 'UTF-8'; // ou ISO-8859-1...
    		$nomeProd = mb_convert_case($nomeProd, MB_CASE_UPPER, $encoding);
			$nomeProd = trim( $nomeProd );
			//$nomeProd = str_replace('_', ' ', $nomeProd);

			if ($termo === $nomeProd || self::trataPlural($termo) === $nomeProd) { // se é exatamenten igual
		        $score+=100;
		    }

		    if (strpos($nomeProd, $termo) === 0 || strpos($nomeProd, self::trataPlural($termo)) === 0) { // se o começo é exatamente igual
		    	$score+=50;
		    }

		    if (strpos($nomeProd, $termo) > 0 || strpos($nomeProd, self::trataPlural($termo))) { // se possui tudo digitado
		    	$score+=50;
		    }

		    if (strpos($nomeProd, $termo)) {
		    	$score+=40;
		    }

		    // palavra por palavra
		    $palavrasTermo = explode(' ',$termo);
		    $palavrasProd = explode(' ', $nomeProd);

		    if (count($palavrasTermo) > 0) {
		    	if ($palavrasTermo[0] === $palavrasProd[0] || self::trataPlural($palavrasTermo[0]) === $palavrasProd[0]) { // se a primeira palavra for idêntica
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

		$result = self::array_multi_sort($result,'score','venda'); 

		return $result;
	}

	public static function geraArray($linha, $score=0)
	{
		return array(
				'id'=>$linha['id'], 
				'fonetic_title'=>$linha['titulo_fonetico'],
				'title'=>$linha['titulo'],
				'venda' => intval($linha['click']),
				'score' => 0
			);		
	}

	public static function geraArraryXML($linha, $score=0)
	{
		$descBoleto = $this->search->getDescBoleto();

		if($descBoleto != '0' && !empty($descBoleto))
        {
            $linha['XML_sale_price'] = $linha['XML_sale_price'] - ($linha['XML_sale_price'] * ($descBoleto / 100));
        }

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
?>
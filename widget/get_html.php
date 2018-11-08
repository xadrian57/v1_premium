<?php

function get_HTML($obj, $template, $formato, $titulo, $moeda, $numProd, $idWid)
{
	if(!empty($obj[0]['link']))
	{
		$sumValue = 0.00;
		// troca slider por bloco
		if($formato == 3 && count($obj) < 5)
		{
			$formato = 1;
		}

		if($numProd == 0)
		{
			$numProd = getNumProd($formato);
		}		
		
		$formato = getFormatName($formato);

		$html = file_get_contents("templates/kit_".$template."/".$formato.".html");

		$html = str_replace('{TITLE_BLOCK}', $titulo, $html);
		$html = str_replace('{ID_WIDGET}', $idWid, $html);

		$htmlArray = explode("<!-- REPEAT PRODUCTS -->", $html);

		if(count($htmlArray) > 2)
		{
		    $head = $htmlArray[0];
			$body = '';
			$footer = $htmlArray[2];
			$elementRepeat = $htmlArray[1];

			for($i = 0; $i < $numProd; $i++)
			{
				if($formato == 'remarketing' && $i == 0)
				{
					if(!empty($obj[$i]['link']))
					{
						$aux = $head;

						$aux = str_replace('{PRODUCT_ID_0}', $obj[$i]['id'], $aux);
						$aux = str_replace('{PRODUCT_URL_0}', $obj[$i]['link'], $aux);
						$aux = str_replace('{PRODUCT_NAME_0}', $obj[$i]['name'], $aux);
						$aux = str_replace('{PRODUCT_IMG_0}', $obj[$i]['link_image'], $aux);
						$aux = str_replace('{PRODUCT_IMG_2_0}', $obj[$i]['link_image_2'], $aux);
						
						// VERIFICAR SE É 0 PARA EXIBIR UM SÓ
						// estudar a melhor forma, talvez utilizar comentarios ao redor
						if(($obj[$i]['sale_price'] != '0,00') && ($obj[$i]['sale_price'] != $obj[$i]['price']))
						{
							// REMOVE O FALSE
							$arrayAux = explode ('<!-- PRICE FALSE -->', $aux);
							$aux = $arrayAux[0].''.$arrayAux[2];

							$aux = str_replace('{VALUE_DE_0}', $moeda.' '.$obj[$i]['price'], $aux);
							$aux = str_replace('{VALUE_0}', $moeda.' '.$obj[$i]['sale_price'], $aux);

							$sumValue += number_format(floatval(str_replace('.','',$obj[$i]['sale_price'])), 2, '.', '');
						}
						else
						{
							// REMOVE O TRUE
							$arrayAux = explode ('<!-- PRICE TRUE -->', $aux);
							$aux = $arrayAux[0].''.$arrayAux[2];

							$aux = str_replace('{VALUE_0}', $moeda.' '.$obj[$i]['price'], $aux);

							$sumValue += number_format(floatval(str_replace('.','',$obj[$i]['price'])), 2, '.', '');
						}			
						

						// VERIFICAR SE É 0 PARA NÃO EXIBIR
						// estudar a melhor forma, talvez utilizar comentarios ao redor
						if($obj[$i]['mount'] != 0)
						{
							// REMOVE O FALSE
							$arrayAux = explode ('<!-- AMOUNT FALSE -->', $aux);
							$aux = $arrayAux[0].''.$arrayAux[2];

							$aux = str_replace('{AMOUNT_PLOTS_0}', $obj[$i]['mount'], $aux);
							$aux = str_replace('{VALUE_PLOTS_0}', $moeda.' '.$obj[$i]['amount'], $aux);
						}
						else
						{
							// REMOVE O TRUE
							$arrayAux = explode ('<!-- AMOUNT TRUE -->', $aux);
							$aux = $arrayAux[0].''.$arrayAux[2];
						}
						
						$arrayDiscount = explode ('<!-- DISCOUNT -->', $aux);
						if(count($arrayDiscount) > 2)
						{
							if($obj[$i]['discount'] != 0 && $obj[$i]['discount'] != 100)
							{
								// REMOVE O FALSE
								$arrayAux = explode ('<!-- DISCOUNT FALSE -->', $aux);
								$aux = $arrayAux[0].''.$arrayAux[2];

								$aux = str_replace('{PRODUCT_DISCOUNT_0}', $obj[$i]['discount'], $aux);
							}
							else
							{
								// REMOVE O TRUE
								$arrayAux = explode ('<!-- DISCOUNT TRUE -->', $aux);
								$aux = $arrayAux[0].''.$arrayAux[2];
							}
						}		

						$head = $aux;
					}
				}
				else if(!empty($obj[$i]['link']))
				{
					$aux = $elementRepeat;

					$aux = str_replace('{PRODUCT_ID}', $obj[$i]['id'], $aux);
					$aux = str_replace('{PRODUCT_URL}', $obj[$i]['link'], $aux);
					$aux = str_replace('{PRODUCT_NAME}', $obj[$i]['name'], $aux);
					$aux = str_replace('{PRODUCT_IMG}', $obj[$i]['link_image'], $aux);
					$aux = str_replace('{PRODUCT_IMG_2}', $obj[$i]['link_image_2'], $aux);
					
					// VERIFICAR SE É 0 PARA EXIBIR UM SÓ
					// estudar a melhor forma, talvez utilizar comentarios ao redor
					if(($obj[$i]['sale_price'] != '0,00') && ($obj[$i]['sale_price'] != $obj[$i]['price']))
					{
						// REMOVE O FALSE
						$arrayAux = explode ('<!-- PRICE FALSE -->', $aux);
						$aux = $arrayAux[0].''.$arrayAux[2];

						$aux = str_replace('{VALUE_DE}', $moeda.' '.$obj[$i]['price'], $aux);
						$aux = str_replace('{VALUE}', $moeda.' '.$obj[$i]['sale_price'], $aux);

						$sumValue += number_format(floatval(str_replace('.','',$obj[$i]['sale_price'])), 2, '.', '');
					}
					else
					{
						// REMOVE O TRUE
						$arrayAux = explode ('<!-- PRICE TRUE -->', $aux);
						$aux = $arrayAux[0].''.$arrayAux[2];

						$aux = str_replace('{VALUE}', $moeda.' '.$obj[$i]['price'], $aux);

						$sumValue += number_format(floatval(str_replace('.','',$obj[$i]['price'])), 2, '.', '');
					}			
					

					// VERIFICAR SE É 0 PARA NÃO EXIBIR
					// estudar a melhor forma, talvez utilizar comentarios ao redor
					if($obj[$i]['mount'] != 0)
					{
						// REMOVE O FALSE
						$arrayAux = explode ('<!-- AMOUNT FALSE -->', $aux);
						$aux = $arrayAux[0].''.$arrayAux[2];

						$aux = str_replace('{AMOUNT_PLOTS}', $obj[$i]['mount'], $aux);
						$aux = str_replace('{VALUE_PLOTS}', $moeda.' '.$obj[$i]['amount'], $aux);
					}
					else
					{
						// REMOVE O TRUE
						$arrayAux = explode ('<!-- AMOUNT TRUE -->', $aux);
						$aux = $arrayAux[0].''.$arrayAux[2];
					}

					$arrayDiscount = explode ('<!-- DISCOUNT -->', $aux);
					if(count($arrayDiscount) > 2)
					{
						if($obj[$i]['discount'] != 0 && $obj[$i]['discount'] != 100)
						{
							// REMOVE O FALSE
							$arrayAux = explode ('<!-- DISCOUNT FALSE -->', $aux);
							$aux = $arrayAux[0].''.$arrayAux[2];

							$aux = str_replace('{PRODUCT_DISCOUNT}', $obj[$i]['discount'], $aux);
						}
						else
						{
							// REMOVE O TRUE
							$arrayAux = explode ('<!-- DISCOUNT TRUE -->', $aux);
							$aux = $arrayAux[0].''.$arrayAux[2];
						}
					}				

					$body .= $aux;
				}
			}

			$response = $head."".$body."".$footer;
		}
		else
		{					
			$aux = $html;

			for($i = 0; $i < $numProd; $i++)
			{

				$aux = str_replace('{PRODUCT_ID_'.$i.'}', $obj[$i]['id'], $aux);
				$aux = str_replace('{PRODUCT_URL_'.$i.'}', $obj[$i]['link'], $aux);
				$aux = str_replace('{PRODUCT_NAME_'.$i.'}', $obj[$i]['name'], $aux);
				$aux = str_replace('{PRODUCT_IMG_'.$i.'}', $obj[$i]['link_image'], $aux);
				$aux = str_replace('{PRODUCT_IMG_2_'.$i.'}', $obj[$i]['link_image_2'], $aux);
				
				// VERIFICAR SE É 0 PARA EXIBIR UM SÓ
				// estudar a melhor forma, talvez utilizar comentarios ao redor
				if($obj[$i]['sale_price'] != '0,00' && ($obj[$i]['sale_price'] != $obj[$i]['price']))
				{
					// REMOVE O FALSE
					$arrayAux = explode ('<!-- PRICE FALSE '.$i.' -->', $aux);
					$aux = $arrayAux[0].''.$arrayAux[2];

					$aux = str_replace('{VALUE_DE_'.$i.'}', $moeda.' '.$obj[$i]['price'], $aux);
					$aux = str_replace('{VALUE_'.$i.'}', $moeda.' '.$obj[$i]['sale_price'], $aux);

					$sumValue += number_format(floatval(str_replace('.','',$obj[$i]['sale_price'])), 2, '.', '');
				}
				else
				{
					// REMOVE O TRUE
					$arrayAux = explode ('<!-- PRICE TRUE '.$i.' -->', $aux);
					$aux = $arrayAux[0].''.$arrayAux[2];

					$aux = str_replace('{VALUE_'.$i.'}', $moeda.' '.$obj[$i]['price'], $aux);

					$sumValue += number_format(floatval(str_replace('.','',$obj[$i]['price'])), 2, '.', '');
				}			
				

				// VERIFICAR SE É 0 PARA NÃO EXIBIR
				// estudar a melhor forma, talvez utilizar comentarios ao redor
				if($obj[$i]['mount'] != 0)
				{
					// REMOVE O FALSE
					$arrayAux = explode ('<!-- AMOUNT FALSE '.$i.' -->', $aux);
					$aux = $arrayAux[0].''.$arrayAux[2];

					$aux = str_replace('{AMOUNT_PLOTS_'.$i.'}', $obj[$i]['mount'], $aux);
					$aux = str_replace('{VALUE_PLOTS_'.$i.'}', $moeda.' '.$obj[$i]['amount'], $aux);
				}
				else
				{
					// REMOVE O TRUE
					$arrayAux = explode ('<!-- AMOUNT TRUE '.$i.' -->', $aux);
					$aux = $arrayAux[0].''.$arrayAux[2];
				}

				$arrayDiscount = explode ('<!-- DISCOUNT '.$i.' -->', $aux);
				if(count($arrayDiscount) > 2)
				{
					if($obj[$i]['discount'] != 0 && $obj[$i]['discount'] != 100)
					{
						// REMOVE O FALSE
						$arrayAux = explode ('<!-- DISCOUNT FALSE '.$i.' -->', $aux);
						$aux = $arrayAux[0].''.$arrayAux[2];

						$aux = str_replace('{PRODUCT_DISCOUNT_'.$i.'}', $obj[$i]['discount'], $aux);
					}
					else
					{
						// REMOVE O TRUE
						$arrayAux = explode ('<!-- DISCOUNT TRUE '.$i.' -->', $aux);
						$aux = $arrayAux[0].''.$arrayAux[2];
					}
				}	

			}

			$response = $aux;
		}

		$response = str_replace('{VALUE_SUM}', $moeda.' '.number_format($sumValue, 2, ',', '.'), $response);

		return $response;
	}
	else
	{
		return '';
	}
}

function getFormatName($formato)
{
	switch ($formato) 
	{
	    case 1:
	        $formato = 'prateleira';
	        break;
	    case 2:
	        $formato = 'prateleira_dupla';
	        break;
	    case 3:
	        $formato = 'carrossel';
	        break;
	    case 4:
	        $formato = 'compre_junto_2';
	        break;
	    case 5:
	        $formato = 'nao_va_embora';
	        break;
	    case 6:
	        $formato = 'oferta_limitada';
	        break;
	    case 7:
	        $formato = 'barra_de_busca';
	        break;
	    case 8:
	        $formato = 'vitrine';
	        break;
	    case 10:
	        $formato = 'rodape_produto';
	        break;
	    case 11:
	        $formato = 'totem';
	        break;
	    case 12:
	        $formato = 'compre_junto_3';
	        break;
	    case 13:
	        $formato = 'remarketing';
	        break;
	}

	return $formato;
}

function getNumProd($formato)
{
	switch ($formato) 
	{
	    case 1:
	        $numProd = 4;
	        break;
	    case 2:
	        $numProd = 8;
	        break;
	    case 3:
	        $numProd = 24;
	        break;
	    case 4:
	        $numProd = 2;
	        break;
	    case 5:
	        $numProd = 24;
	        break;
	    case 6:
	        $numProd = 1;
	        break;
	    case 7:
	        $numProd = 24;
	        break;
	    case 8:
	        $numProd = 24;
	        break;
	    case 10:
	        $numProd = 24;
	        break;
	    case 11:
	        $numProd = 3;
	        break;
	    case 12:
	        $numProd = 3;
	        break;
	    case 13:
	        $numProd = 24;
	        break;
	}

	return $numProd;
}	

?>
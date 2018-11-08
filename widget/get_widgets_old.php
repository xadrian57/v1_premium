<?php
	
	header('content-type: application/json; charset=utf-8');
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: POST');

	//CONEXÃO BD Dados
	include '../bd/conexao_bd_dados.php';

	//CONEXÃO BD CADASTRO
	include '../bd/conexao_bd_cadastro.php';

	//INTELIGENCIAS
	include 'get_inteligencia.php';

	//GET HTML
	include 'get_html.php';
	
	// RECEBE POST
	$idcli_cryp = mysqli_escape_string($conCad, $_POST['idcli']);
    $cookiedata = mysqli_escape_string($conCad, $_POST['cookiedata']);
    $cookieprod = mysqli_escape_string($conCad, $_POST['cookieprod']);
    $cookieevent = mysqli_escape_string($conCad, $_POST['cookieevent']);
    $url = mysqli_escape_string($conCad, $_POST['url']);
    $ofertaID = mysqli_escape_string($conCad, $_POST['ofertaID']);
    $page = mysqli_escape_string($conCad, $_POST['page']);
    $prodId = mysqli_escape_string($conCad, $_POST['idProd']);
    $categoria = mysqli_escape_string($conCad, $_POST['categoria']);

    // VARIAVEIS
    $JSON_widgets = '';

    if(!empty($idcli_cryp))
    {
    	$selectCli = "SELECT CLI_id FROM cliente WHERE CLI_id_owa = '$idcli_cryp' AND CLI_ativo = 1";
    	$resultCli = mysqli_query($conCad, $selectCli);

    	if(mysqli_num_rows($resultCli) > 0)
    	{
    		$arrayCli = mysqli_fetch_array($resultCli);

    		$idcli = $arrayCli['CLI_id'];

	    	// SELECT WIDGETS
	    	$selectWidgets = "SELECT WID_id, WID_prod_esg, WID_show, WID_hide, WID_inteligencia, WID_inject, WID_div_type, WID_updown, WID_div, WID_formato, WID_texto, WID_utm, WID_dias, WID_num_prod FROM widget WHERE WID_id_cli = '$idcli' AND WID_status = 1".queryPage($page);
	    	$resultWidgets = mysqli_query($conCad, $selectWidgets);

	    	if(mysqli_num_rows($resultWidgets) > 0)
	    	{
	    		while($arrayWidgets = mysqli_fetch_array($resultWidgets))
				{
					if($arrayWidgets['WID_esgotado'] == 1)
	    			{
	    				$estoque = getEstoque($conDados, $idcli, $prodId);
	    			}
	    			else
	    			{
	    				$estoque = 0;
	    			}

		        	if(showHide($url, $arrayWidgets['WID_show'], $arrayWidgets['WID_hide']) && !$estoque)
		        	{
		        		$idWid = $arrayWidgets['WID_id'];
		    			$widTipo = $arrayWidgets['WID_inteligencia'];
		    			$inject = $arrayWidgets['WID_inject'];	    			

		    			// SELECT WIDGET_CONFIG
				    	$selectWidgetConfig = "SELECT WC_collection FROM widget_config WHERE WC_id_wid = ".$idWid;
				    	$resultWidgetConfig = mysqli_query($conCad, $selectWidgetConfig);

				    	if(mysqli_num_rows($resultWidgetConfig) > 0)
				    	{
				    		$arrayWidgetConfig = mysqli_fetch_array($resultWidgetConfig);
				    	}
				    	else
				    	{
				    		$arrayWidgetConfig['WC_collection'] = '';
				    	}			    	

				    	$obj = getProducts($idWid, $conDados, $idcli, $widTipo, $prodId, $arrayWidgets['WID_utm'], $arrayWidgets['WID_dias'], $categoria, $cookiedata, $cookieprod, $cookieevent, $arrayWidgetConfig['WC_collection'], $ofertaID);

				    	$obj = formatValues($obj);

				    	if($inject)
				    	{
				    		// SELECT TEMPLATE
					    	$selectConfig = "SELECT CONF_template, CONF_moeda FROM config WHERE CONF_id_cli = '$idcli'";
					    	$resultConfig = mysqli_query($conCad, $selectConfig);

					    	$arrayConfig = mysqli_fetch_array($resultConfig);

					    	$widTemplate = $arrayConfig['CONF_template'];
                            $widMoeda = $arrayConfig['CONF_moeda'];

				    		$html = get_HTML($obj, $widTemplate, $arrayWidgets['WID_formato'], $arrayWidgets['WID_texto'], $widMoeda, $arrayWidgets['WID_num_prod'], $arrayWidgets['WID_id']);
				    		set_JSON_widget($inject, $idWid, $html, $arrayWidgets['WID_div'], $arrayWidgets['WID_div_type'], $arrayWidgets['WID_updown'], getJSON($obj));
				    	}
				    	else
				    	{
				    		set_JSON_widget($inject, $idWid, $html, $arrayWidgets['WID_div'], $arrayWidgets['WID_div_type'], $arrayWidgets['WID_updown'], getJSON($obj));
				    	}
				    	
		        	}

		        	$obj = [];
	    		}

	    		// ECHO RESPONSE
	    		// ADD TRUSTVOX DA LOJA
	    		// FECHAR AS TAGS DE JSON
	    		echo '{"widgets":['.$JSON_widgets.']}';
	    	}
	    	else
	    	{
	    	    echo '{"erro":"Esta página não possui widgets."}';
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

    function queryPage($page)
	{
		// 'todas'=>0,'home'=>1,'produto'=>2,'buscaVazia'=>3,'categoria'=>4,'carrinho'=>5 ,'checkout'=>6

    	switch (trim($page)) 
    	{
    		case 'product':
    			$result = " AND WID_pagina IN (0,2) ";
    			break;
    		case 'cart':
    			$result = " AND WID_pagina IN (0,5) ";
    			break;
    		case 'transaction':
    			$result = " AND WID_pagina IN (0,6) ";
    			break;
    		case 'home':
    			$result = " AND WID_pagina IN (0,1) ";
    			break;
    		case 'category':
    			$result = " AND WID_pagina IN (0,4) ";
    			break;    		
    		default:
    			$result = " ";
    			break;
    	}

	    return $result;
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

	function limpaURL($url)
	{
		$arrayURL = explode("/", $url);
	    $n = count($arrayURL);
	    $n--;
		$pesquisa = $arrayURL[$n];

	    if($pesquisa == 'p' || $pesquisa == '')
	    {
	    	$n--;
	    	$pesquisa = $arrayURL[$n];
	    }

	    return $pesquisa;
	}

	function getEstoque($conDados, $idcli, $prodId)
	{
		$sql = "SELECT XML_availability FROM XML_".$idcli." WHERE XML_id = '$prodId' LIMIT 1";
		$result = mysqli_query($conDados, $sql);

		if(mysqli_num_rows($result) > 0)
		{
			$array = mysqli_fetch_array($result);

			return $array['XML_availability'];
		}
		else
		{
			return 0;
		}    	
	}

	function set_JSON_widget($inject, $idWid, $html, $injectName, $injectType, $injectUpDown, $products)
	{
		global $JSON_widgets;

		if(!empty($JSON_widgets))
		{
			$JSON_widgets .= ',';
		}

		if($inject)
		{
			$JSON_widgets .= '{  
		        "widget_id":'.$idWid.',
		        "inject":true,
		        "inject_name":"'.$injectName.'",
		        "inject_type":"'.$injectType.'",
		        "inject_up":'.$injectUpDown.',
		        "html":'.json_encode($html).'
	      	}';
		}
		else
		{
			$JSON_widgets .= '{  
	         "widget_id":'.$idWid.',
	         "inject":false,
	         '.$products.'
	      }';
		}
	}

	function getJSON ($obj)
    {
        $json = '"product":[';

        foreach ($obj as $key => $value) 
        {
            if($key != 0)
            {
                $json .= ',';
            }
           
            $json .= '  
                      {  
                         "id":"'.$value['id'].'",
                         "sku":"'.$value['sku'].'",
                         "name":"'.$value['name'].'",
                         "price":"'.$value['price'].'",
                         "sale_price":"'.$value['sale_price'].'",
                         "link_image":"'.$value['link_image'].'",
                         "link_image_2":"'.$value['link_image_2'].'",
                         "link":"'.$value['link'].'",
                         "description":"'.$value['description'].'",
                         "mount":'.$value['mount'].',
                         "amount":"'.$value['amount'].'",
                         "discount":'.$value['discount'].',
                         "custom_var":[  
                            "'.$value['custom_var'][0].'",
                            "'.$value['custom_var'][1].'",
                            "'.$value['custom_var'][2].'",
                            "'.$value['custom_var'][3].'",
                            "'.$value['custom_var'][4].'"
                         ],
                         "trustvox":{  
                            "rate":"",
                            "num_rate":"",
                            "coments":[  
                               "",
                               "",
                               ""
                            ],
                            "date":[  
                               "",
                               "",
                               ""
                            ],
                            "users":[  
                               "",
                               "",
                               ""
                            ]
                         }
                      }';
        }

        $json .=   ']';

        return $json;
    }

    function formatValues($obj)
    {
    	foreach ($obj as $key => $value) 
        {
        	$obj[$key]['price'] = number_format($obj[$key]['price'], 2, ',', '.');
        	$obj[$key]['sale_price'] = number_format($obj[$key]['sale_price'], 2, ',', '.');
        	$obj[$key]['amount'] = number_format($obj[$key]['amount'], 2, ',', '.');
        }

        return $obj;
    }


?>
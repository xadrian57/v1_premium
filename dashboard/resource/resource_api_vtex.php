<?php

function API_VTEX($id, $url, $conCad, $conDados)
{
    $time = time();

    $idProdAux = '0';

    $checkCreateXML = false;

    $posts = [];

    $arrayIdsProd = [];
    $arrayEstoqueProd = [];

    if(($url == '') || ($url == NULL))
    {
        $queryXML = "SELECT CONF_XML as URL_XML FROM config WHERE CONF_id_cli = '$id'";
        $resultadoXML = mysqli_query($conCad, $queryXML);
        $array = mysqli_fetch_array($resultadoXML);
        $url = $array['URL_XML'];
    }

    /*$ch = curl_init();
    $timeout = 0;
    curl_setopt($ch, CURLOPT_URL, $url.'/api/catalog_system/pub/category/tree/1');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    $categoria = curl_exec ($ch);

    $categoria = (json_decode($categoria, true));

    curl_close($ch);

    for($i = 0; $i < count($categoria); $i++)
    {*/
        $page_begin = 1;
        $page_end = 50;

        for($j = 0; $j < 25; $j++) 
        {  

            $ch = curl_init();
            $timeout = 0;
            //curl_setopt($ch, CURLOPT_URL, $url.'/api/catalog_system/pub/products/search?fq='. $categoria[$i]['id'] .'&_from='. $page_begin .'&_to='. $page_end);
            curl_setopt($ch, CURLOPT_URL, $url.'/api/catalog_system/pub/products/search?_from='. $page_begin .'&_to='. $page_end);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            $conteudo = curl_exec ($ch);

            $conteudo = (json_decode($conteudo, true));

            curl_close($ch);

            if(empty($conteudo))
            {
                break;
            }

            foreach ($conteudo as $key => $value) 
            {
                // ID

                $titulo = $value['productName'];

                $descricao =  $value['description'];                    
                
                $idprod = $value['productId'];
                
                $link = limpaLink_API($value['link']);

                $type = ajustaCategoria_API($value['categories'][0]);
                
                if(!empty($value['items'][0]['images']) && $id != 14)
                {

                    $image_link = $value['items'][0]['images'][0]['imageUrl'];

                    $image_link_2 = $value['items'][0]['images'][1]['imageUrl'];

                }
                else
                {
                    //imagem bazar http://bazarhorizonte.vteximg.com.br/arquivos/
                    $image_link = 'http://bazarhorizonte.vteximg.com.br/arquivos/'.$value['Imagem Principal'][0];

                    if($image_link == 'http://bazarhorizonte.vteximg.com.br/arquivos/')
                    {
                        $image_link = ajustaImagem_API($value['Amostra'][0]);

                        if($image_link == '')
                        {
                            $image_link = $value['items'][0]['images'][0]['imageUrl'];
                        } 
                    } 
                }

                // ID
                
                // SKU

                $skuKey = 0;
                $sku = [];
                $variante = [];
                $variante2 = [];
                $availabilityArray = [];

                foreach ($conteudo[$key]['items'] as $keySKU => $valueSKU)
                {
                    $sku[$skuKey] = $valueSKU['itemId'];
                    
                    if(!empty($valueSKU['COR']))
                    {
                        $variante[$skuKey] = $valueSKU['COR'][0];
                    }
                    else if(!empty($valueSKU['Cor'])) 
                    {
                        $variante[$skuKey] = $valueSKU['Cor'][0];
                    }
                    else if(!empty($valueSKU['Colors'])) 
                    {
                        $variante[$skuKey] = $valueSKU['Colors'][0];
                    }
                    else if(!empty($valueSKU['Cores'])) 
                    {
                        $variante[$skuKey] = $valueSKU['Cores'][0];
                    }

                    if(!empty($valueSKU['Sizes']))
                    {
                        $variante2[$skuKey] = $valueSKU['Sizes'][0];
                    }
                    else if(!empty($valueSKU['Tamanho']))
                    {
                        $variante2[$skuKey] = $valueSKU['Tamanho'][0];
                    }                

                    $availabilityArray[$skuKey] = $valueSKU['sellers'][0]['commertialOffer']['AvailableQuantity'];

                    $skuKey++;
                }

                $skuSTR = implode(',', $sku);
                $varianteSTR = implode(',', $variante);
                $variante2STR = implode(',', $variante2);

                $availability = array_sum ( $availabilityArray );           
                
                $price = $value['items'][0]['sellers'][0]['commertialOffer']['ListPrice'];
                
                $sale_price = $value['items'][0]['sellers'][0]['commertialOffer']['Price'];

                if($price == 0.00 && $sale_price == 0.00)
                {
                    foreach ($value['items'] as $keyPrec => $valuePrec) 
                    {                    
                        $price = $valuePrec['sellers'][0]['commertialOffer']['ListPrice'];
                        $sale_price = $valuePrec['sellers'][0]['commertialOffer']['Price'];

                        if($price != 0.00 && $sale_price != 0.00)
                        {
                            break;
                        }
                    }
                }
                
                //$availability = $value['items'][0]['sellers'][0]['commertialOffer']['AvailableQuantity'];

                $posParcela = posParcela_API($value);
                
                $months = $value['items'][0]['sellers'][0]['commertialOffer']['Installments'][$posParcela]['NumberOfInstallments'];
                $amount = $value['items'][0]['sellers'][0]['commertialOffer']['Installments'][$posParcela]['Value'];        

                // SKU
                
                $SALE_PRICE = $sale_price;
                $PRICE = $price;
                $AMOUNT = $amount;
                
                $desconto = calculaDesconto_API($PRICE, $SALE_PRICE);
                
                $availability = geraEstoque_API($availability);
                
                if($idprod != "" && $idprod != null)
                {
                    if(!$checkCreateXML)
                    {
                        createXML_API($id, $conDados);
                        $checkCreateXML = true;
                    }
                    
                    if(verificaProd_API($arrayIdsProd, $arrayEstoqueProd, $idprod, $availability))
                    {
                        $arrayIdsProd[] = $idprod;
                        $arrayEstoqueProd[] = $availability;
                    

                        $update ="UPDATE XML_".$id." SET XML_time='$time', XML_titulo='" . htmlspecialchars($titulo) . "', XML_descricao = '" . $descricao . "', XML_titulo_upper=UPPER('" .  $titulo . "'), XML_sku='$skuSTR', XML_price = '$PRICE', XML_sale_price = '$SALE_PRICE', XML_desconto = '$desconto', XML_availability = '$availability', XML_link = '$link',XML_type ='" . htmlspecialchars($type) . "',XML_type_upper=UPPER('" .  $type . "'),  XML_image_link = '$image_link', XML_vparcela = '$AMOUNT', XML_nparcelas = '$months', XML_custom_nome1 = 'variante', XML_custom_nome2 = 'variante2', XML_custom1 = '$varianteSTR', XML_custom2 = '$variante2STR', XML_image_link2 = '$image_link_2' WHERE XML_id = '$idprod'";
                        $resultado = mysqli_query($conDados, $update);
                        
                        if(mysqli_affected_rows($conDados) < 1)
                        {
                            $insere=("INSERT INTO XML_".$id." (XML_descricao, XML_time, XML_time_insert, XML_titulo, XML_titulo_upper, XML_id, XML_sku, XML_price, XML_sale_price, XML_desconto, XML_availability, XML_link, XML_type, XML_type_upper, XML_image_link, XML_vparcela, XML_nparcelas, XML_custom_nome1, XML_custom1, XML_custom_nome2, XML_custom2, XML_image_link2) VALUES ('" . $descricao . "', '$time', '$time', '" . htmlspecialchars($titulo) . "',UPPER('" .  $titulo . "'), '$idprod', '$skuSTR', '$PRICE', '$SALE_PRICE','$desconto', '$availability', '$link','" . htmlspecialchars($type) . "',UPPER('" .  $type . "'),'$image_link', '$AMOUNT', '$months', 'variante', '$varianteSTR', 'variante2', '$variante2STR', '$image_link_2')");
                            $resultadoInsere = mysqli_query($conDados, $insere);                   
                        }
        
        
                        $select = "SELECT XML_venda_7 FROM XML_".$id." WHERE XML_id = '$idprod'";
                        $querySelect = mysqli_query($conDados, $select);
                        
                        $arraySelect = mysqli_fetch_array($querySelect);
                        
                        $posJSON = atualizaProdJSON_API($posts, $idprod);

                        if($posJSON != false)
                        {
                            $posts[$posJSON] = array
                                            (
                                                'id'=> strval($idprod), 
                                                'sku'=> $sku, 
                                                'title'=> urlencode($titulo), 
                                                'in_stock'=> $availability,
                                                'price'=> $PRICE, 
                                                'sale_price'=> $SALE_PRICE, 
                                                'link'=> urlencode($link), 
                                                'link_image'=> urlencode($image_link), 
                                                'type'=> urlencode($type), 
                                                'amount'=> $AMOUNT, 
                                                'months'=> $months, 
                                                'venda' => $arraySelect['XML_venda_7'], 
                                                'desconto' => $desconto,
                                                'productReference' => $value['productReference']
                                            );
                        }
                        else
                        {
                            $posts[] = array
                                            (
                                                'id'=> strval($idprod), 
                                                'sku'=> $sku, 
                                                'title'=> urlencode($titulo), 
                                                'in_stock'=> $availability,
                                                'price'=> $PRICE, 
                                                'sale_price'=> $SALE_PRICE, 
                                                'link'=> urlencode($link), 
                                                'link_image'=> urlencode($image_link), 
                                                'type'=> urlencode($type), 
                                                'amount'=> $AMOUNT, 
                                                'months'=> $months, 
                                                'venda' => $arraySelect['XML_venda_7'], 
                                                'desconto' => $desconto,
                                                'productReference' => $value['productReference']
                                            );
                        }
                        
                    }
                }
                else
                {
                    return '3';
                }
            }

            $page_begin += 50;
            $page_end += 50;
        }
        //} //fim do while

    if($insere OR $update)
    {
        $atualiza = "UPDATE config SET CONF_XML = '$url', CONF_at_xml = CURRENT_TIMESTAMP() WHERE CONF_id_cli = '$id'";
        $insert3 = mysqli_query($conCad, $atualiza);
        
        $updatestats ="UPDATE XML_".$id." SET XML_availability = 0 WHERE XML_time != '$time' OR XML_time IS NULL";
        $resultadostats = mysqli_query($conDados, $updatestats);

        if($id == 14)
        {
            $updateCat ="UPDATE XML_14 SET `XML_availability` = 0 WHERE `XML_type` LIKE '%xx%'";
            $resultadoCat = mysqli_query($conDados, $updateCat);
        }

        notificaXML_API($id, $conCad);

        geraJSON_API($id, $posts);
        
        return '1';
    }
    else
    {
        return '0';
    }
}

// FUNÇÕES AUXILIARES

function createXML_API($id, $conDados)
{
    $criaXML = ("CREATE TABLE IF NOT EXISTS XML_".$id." (
            XML_titulo VARCHAR(256),
            XML_titulo_upper varchar(256),
            XML_id VARCHAR(256) NOT NULL,
            XML_sku VARCHAR(1000) DEFAULT NULL,
            XML_price DECIMAL(16,2) NOT NULL,
            XML_sale_price DECIMAL(16,2) NOT NULL,
            XML_desconto INT(3) NOT NULL,
            XML_availability boolean NOT NULL,
            XML_link VARCHAR(1024),
            XML_type VARCHAR(256),
            XML_type_upper varchar(256),
            XML_image_link VARCHAR(1024),
            XML_image_link2 VARCHAR(1024),
            XML_descricao VARCHAR(1024),
            XML_brand VARCHAR(256),
            XML_nparcelas int (3) DEFAULT '0',
            XML_vparcela DECIMAL(16,2) DEFAULT '0',
            XML_time int(11) DEFAULT NULL,
            XML_time_insert int(11) DEFAULT NULL,
            XML_custom_nome1 VARCHAR(256),
            XML_custom1 VARCHAR(1000),
            XML_custom_nome2 VARCHAR(256),
            XML_custom2 VARCHAR(256),
            XML_custom_nome3 VARCHAR(256),
            XML_custom3 VARCHAR(256),
            XML_custom_nome4 VARCHAR(256),
            XML_custom4 VARCHAR(256),
            XML_custom_nome5 VARCHAR(256),
            XML_custom5 VARCHAR(256),
            xml_navegou_complementar VARCHAR(1000),
            xml_carrinho_complementar VARCHAR(1000),
            xml_compra_complementar VARCHAR(1000),
            XML_click_7 int(16) DEFAULT 0,
            XML_click_3 int(16) DEFAULT 0,
            XML_click_1 int(16) DEFAULT 0,
            XML_venda_7 int(16) DEFAULT 0,
            XML_venda_3 int(16) DEFAULT 0,
            XML_venda_1 int(16) DEFAULT 0,
            FULLTEXT(XML_link,XML_type,XML_titulo),
            INDEX(XML_titulo_upper, XML_type_upper, XML_click_7, XML_click_3, XML_click_1, XML_availability, XML_venda_7, XML_venda_3, XML_venda_1, XML_time_insert),
            PRIMARY KEY (XML_id)
            )
        ");
    mysqli_query($conDados, $criaXML);
    
    
    $criaRWID = ("CREATE TABLE IF NOT EXISTS RWID_".$id." (
            RWID_id int(10) NOT NULL  AUTO_INCREMENT,
            RWID_id_wid VARCHAR(255) NOT NULL,
            RWID_evento int(1) NOT NULL,
            RWID_data datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            RWID_id_prod VARCHAR(1000) NOT NULL,
            RWID_quant VARCHAR(255) NOT NULL,
            RWID_valor VARCHAR(255) NOT NULL,
            PRIMARY KEY (RWID_id)
            )
        ");
    mysqli_query($conDados, $criaRWID);
    
    $criaVIEW = ("CREATE TABLE IF NOT EXISTS VIEW_".$id." (
            VIEW_id int(10) NOT NULL AUTO_INCREMENT,
            VIEW_id_wid VARCHAR(255) NOT NULL,
            VIEW_data datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (VIEW_id)
            )
        ");
    mysqli_query($conDados, $criaVIEW);
    
    $criaRGER = ("CREATE TABLE IF NOT EXISTS RGER_".$id." (
            RGER_id int(10) NOT NULL  AUTO_INCREMENT,
            RGER_evento int(1) NOT NULL,
            RGER_data datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            RGER_id_prod VARCHAR(1000) NOT NULL,
            RGER_quant VARCHAR(255) NOT NULL,
            RGER_valor VARCHAR(255) NOT NULL,
            FULLTEXT(RGER_id_prod),
            PRIMARY KEY(RGER_id)
            )
        ");
    mysqli_query($conDados, $criaRGER);
}

function notificaXML_API($id, $conCad)
{
    $qNotXMLCadastrado = "INSERT INTO notificacoes (NOT_id_cli, NOT_titulo, NOT_texto, NOT_data, NOT_status) VALUES ($id, 'XML cadastrado com sucesso', 'Seu XML foi cadastrado com sucesso. Agora você já pode aproveitar todas as vantagens da ROI Hero no seu site!', CURRENT_DATE(), 1)";
    $resultNotXMLCadastrado = mysqli_query($conCad, $qNotXMLCadastrado);
}

function limpaLink_API($link)
{
    $pos = strpos($link, '?');
    
    if($pos !== false)
    {
        $link = substr($link, 0, $pos);
    }
    
    return $link;
}

function ajustaImagem_API($link)
{
    $link = str_replace('<img src="', '', $link);
    $pos = strpos ($link , '.jpg');
    $link = substr ($str , 0, $pos + 4);
    //$link = str_replace('" align ="center"> Foto meramente ilustrativa', '', $link);
    //$link = str_replace('" align ="center" alt ="xxx">  <br>Foto meramente ilustrativa', '', $link);
    //$link = str_replace('" align ="center" alt ="xxx"> Foto meramente ilustrativa', '', $link); 
    //$link = str_replace('" align ="center" alt ="xxx"> Foto meramente ilustrativa ', '', $link); 
    //$link = str_replace('" align ="center">', '', $link);

    return $link;
}

function ajustaCategoria_API($type)
{
    $type = explode('/', $type);

    foreach ($type as $key => $value) 
    {
        if(empty($type[$key]) || $type[$key] == '' || $type[$key] == null)
        {
            unset($type[$key]);
        }
    }

    $type = implode(' - ', $type);
    
    return $type;
}

function calculaDesconto_API($PRICE, $SALE_PRICE)
{
    if($PRICE > 0)
    {
        $desconto = 100 - (($SALE_PRICE * 100) / $PRICE);
        $desconto = round($desconto,0);
    }
    else
        $desconto = 0;
        
        return $desconto;
}

function geraEstoque_API($availability)
{
    if(intval($availability) > 0)
    {
        $availability = 1;
    }
    else
    {
        $availability = 0;
    }

    return $availability;
}

function geraJSON_API($id, $posts)
{
    $json_data = json_encode($posts);
    file_put_contents('../../JSON/JSON_'.sha1($id).'.json', $json_data);
}

function verificaProd_API($arrayIdsProd, $arrayEstoqueProd, $idProd, $estoqueProd)
{
    for($i=0; $i < count($arrayIdsProd); $i++)
    {
        if($arrayIdsProd[$i] == $idProd)
        {
            if($arrayEstoqueProd[$i] == 1)
            {
                return false;
            }
        }
    }

    return true;
}

function atualizaProdJSON_API($posts, $idprod)
{
    for($i=0; $i < count($posts); $i++)
    {
        if($posts[$i]['id'] == $idprod)
        {
            return $i;
        }
    }

    return false;
}

function posParcela_API($value)
{
    $auxParcNumb = 0;
    
    foreach ($value['items'][0]['sellers'][0]['commertialOffer']['Installments'] as $key => $valueParc) 
    {
        if($valueParc['InterestRate'] != '0.0')
        {
            return $key - 1;
        }
        else if($valueParc['NumberOfInstallments'] <= $auxParcNumb)
        {
            return $key - 1;
        }
        else if($key == count($value['items'][0]['sellers'][0]['commertialOffer']['Installments']))
        {
            return $key;
        }

        $auxParcNumb = $valueParc['NumberOfInstallments'];
    } 
}

?>
<?php

    //error_reporting(0);
    //ini_set('display_errors', FALSE);

    $obj = [];

    function getProdName($conDados, $idproduto, $idCli)
    {
        $select = ("SELECT XML_titulo FROM XML_".$idCli." WHERE XML_id = '$idproduto'");
        $result = mysqli_query($conDados, $select);

        if(mysqli_num_rows($result) > 0)
        {
            $select = mysqli_fetch_array($result);

            return $select['XML_titulo'];
        }
        else
        {
            return '';
        }
    }

    function setOBJ($idWid, $array, $i, $utm)
    {
        global $obj;

        foreach ($obj as $key => $value) 
        {
            if($value['link'] == $array['XML_link']."?rhWid=".$idWid."&".$utm)
            {
                return false;
            }
        }

        $obj[$i]['id'] = $array['XML_id'];
        $obj[$i]['sku'] = $array['XML_sku'];
        $obj[$i]['name'] = $array['XML_titulo'];
        $obj[$i]['price'] = $array['XML_price'];
        $obj[$i]['sale_price'] = $array['XML_sale_price'];
        $obj[$i]['link_image'] = $array['XML_image_link'];
        $obj[$i]['link_image_2'] = $array['XML_image_link2'];
        $obj[$i]['link'] = $array['XML_link']."?rhWid=".$idWid."&".$utm;
        $obj[$i]['description'] = $array['XML_descricao'];
        $obj[$i]['mount'] = $array['XML_nparcelas'];
        $obj[$i]['amount'] = $array['XML_vparcela'];
        $obj[$i]['discount'] = $array['XML_desconto'];

        return true;

    }

    //function relevancia($diaInt, $idCli, $widTipo, $categoria, $cookiedata, $cookieprod, $idproduto, $palavraChave, $compreJunto, $prodNome, $negativaPai, $negativaFilho, $tipoPCPai, $tipoPCFilho, $paramPai, $paramFilho, $tipoParamPai, $tipoParamFilho, $widprod)
    function getProducts($idWid, $conDados, $idCli, $widTipo, $idProdVista, $utm, $dias, $categoria, $cookiedata, $cookieprod, $cookieevento, $collection, $ofertaID)
    {          
        mysqli_set_charset($conDados, 'utf8');

        $i = 0;

        global $obj;


        $XML_select = "XML_id, XML_sku, XML_image_link, XML_image_link2, XML_titulo, XML_link, XML_type, XML_sale_price, XML_price, XML_type, XML_desconto, XML_nparcelas, XML_vparcela, XML_descricao";

        
        //if(mysqli_num_rows(mysqli_query($conDados, "SHOW TABLES LIKE 'XML_".$idCli."'"))==1)
        //{
            /*
            $select = ("SELECT XML_link, XML_type, XML_sale_price, XML_price, XML_desconto FROM XML_".$idCli." WHERE XML_id = '$idProdVista'");
            $result = mysqli_query($conDados, $select);
    
            if(mysqli_num_rows($result) > 0)
            {
                $select = mysqli_fetch_array($result);
    
                $urlProdVista = $select['XML_link'];
                $typeProd = $select['XML_type'];
                $salePriceProd = $select['XML_sale_price'];
                $priceProd = $select['XML_price'];
                $descontoProd = $select['XML_desconto'];
            }
            */
    
            if($widTipo == 1 || $widTipo == 13) // Top Trends / Mais Clicados
            {
                // FEITO
            }            
            else if($widTipo == 2) // Mais Vendidos
            {
                // FEITO
            }            
            else if($widTipo == 3) // Mais Vendidos da Categoria
            {
                // FEITO
            }            
            else if($widTipo == 4) // Remarketing On site
            {
                $date = explode(',', $cookiedata);
                $idprod = explode(',', $cookieprod);
                $evento = explode(',', $cookieevento);

                array_multisort($date, SORT_DESC, $idprod, $evento);

                // Remove os produtos que já foram adquiridos pelo cliente
                foreach($evento as $key => $value)
                { 
                    if($value == 'checkout')
                    {
                        unset($evento[$key]);
                        unset($idprod[$key]);
                        unset($date[$key]);
                    }
                }

                // Remove o(s) produto(s) que ele está visualizando no momento
                foreach($evento as $key => $value)
                { 
                    if($idprod[$key] == $idproduto)
                    {
                        unset($evento[$key]);
                        unset($idprod[$key]);
                        unset($date[$key]);
                    }
                }

                // Verifica a disponibilidade do produto
                foreach($evento as $key => $value)
                { 
                    $select = "SELECT XML_availability FROM XML_$idCli WHERE XML_id = '$idprod[$key]'";
                    $result = mysqli_query($conDados, $select);
                    
                    if(mysqli_num_rows ($result) > 0 )
                    {
                        $select = mysqli_fetch_array($result);
                        if($select['XML_availability'] == '0')
                        {
                            // Remove os itens com estoque zerado
                            unset($evento[$key]);
                            unset($idprod[$key]);
                            unset($date[$key]);
                        }
                    }
                    else
                    {
                        // Remove porque não encontrou no xml
                        unset($evento[$key]);
                        unset($idprod[$key]);
                        unset($date[$key]);
                    }

                }
                
                $i = 0;

                // Verifica se o produto foi adicionado ao carrinho pelo menos no dia anterior
                // não menos que isso
                foreach($evento as $key => $value)
                {                     
                    if($value == 'cart')
                    {
                        if($date[$key] <= ((time() * 1000) - (1000 * 60 * 5))) //dia anterior
                        {
                            $cart[$i] = $idprod[$key];
                            $i++;
                        }

                        unset($evento[$key]);
                        unset($idprod[$key]);
                        unset($date[$key]);                        
                    }                                
                }

                $evento = array_values($evento);
                $idprod = array_values($idprod);
                $date = array_values($date);

                array_multisort($date, SORT_DESC, $idprod, $evento);

                $cont = count($cart);

                if($cont > 0)
                {
                    if($cont == 1)
                    {
                        $select = "SELECT ".$XML_select." FROM XML_".$idCli." WHERE XML_id != '$idProdVista' AND XML_availability = '1' AND XML_id = '".$cart[0]."'" ;
                        $result = mysqli_query($conDados, $select);

                        if(mysqli_num_rows ($result) > 0 )
                        {
                            while($select = mysqli_fetch_array($result))
                            {
                                setOBJ($idWid, $select, 0, $utm);

                                unset($idprod[$cart[0]]);
                                unset($evento[$cart[0]]);
                                unset($date[$cart[0]]);
                            }
                        }

                        array_multisort($date, SORT_DESC, $idprod, $evento);

                        for($i = 1; $i < 4; $i++)
                        { 
                            $select = "SELECT ".$XML_select." FROM XML_".$idCli." WHERE XML_id != '$idProdVista' AND XML_availability = '1' AND XML_id = '".$idprod[$i-1]."'" ;
                            $result = mysqli_query($conDados, $select);

                            if(mysqli_num_rows ($result) > 0 )
                            {
                                while($select = mysqli_fetch_array($result))
                                {
                                    setOBJ($idWid, $select, $i, $utm);
                                }
                            }
                        }
                    }
                    else
                    {
                        for($i = 0; $i < 2; $i++)
                        {
                            $select = "SELECT ".$XML_select." FROM XML_".$idCli." WHERE XML_id != '$idProdVista' AND XML_availability = '1' AND XML_id = '".$cart[$i]."'" ;
                            $result = mysqli_query($conDados, $select);

                            if(mysqli_num_rows ($result) > 0 )
                            {
                                while($select = mysqli_fetch_array($result))
                                {
                                    setOBJ($idWid, $select, $i, $utm);

                                    unset($idprod[$cart[$i]]);
                                    unset($evento[$cart[$i]]);
                                    unset($date[$cart[$i]]);
                                }
                            }
                        }

                        array_multisort($date, SORT_DESC, $idprod, $evento);

                        for($i = 2; $i < 4; $i++)
                        { 
                            $select = "SELECT ".$XML_select." FROM XML_".$idCli." WHERE XML_id != '$idProdVista' AND XML_availability = '1' AND XML_id = '".$idprod[$i-2]."'" ;
                            $result = mysqli_query($conDados, $select);

                            if(mysqli_num_rows ($result) > 0 )
                            {
                                while($select = mysqli_fetch_array($result))
                                {
                                    setOBJ($idWid, $select, $i, $utm);
                                }
                            }
                        }
                    }
                }
                else
                {
                    $numprod = 0;

                    if(count($idprod) > 0)
                    {
                        for($i = 0; $i < 4; $i++)
                        { 
                            $select = "SELECT ".$XML_select." FROM XML_".$idCli." WHERE XML_id != '$idProdVista' AND XML_availability = '1' AND XML_id = '".$idprod[$i]."'" ;
                            $result = mysqli_query($conDados, $select);

                            if(mysqli_num_rows ($result) > 0 )
                            {
                                $select = mysqli_fetch_array($result);

                                setOBJ($idWid, $select, $i, $utm);
                                $numprod++;
                            }
                        }
                    }                    
                }               
            }            
            else if($widTipo == 5) // PRODUTO SIMILAR
            {
                // FEITO             
            }
            else if($widTipo == 6) // LIQUIDAÇÃO
            {
                // FEITO  
            }            
            else if($widTipo == 7) // COLLECTION
            {
                // FEITO
            }
            /*
            else if($widTipo == 8) // COMPRE JUNTO
            {
                $tamanhoString = [];
                $arrayNegativaPai = [];
                $arrayNegativaFilho = [];
                $arrayTipoPCPai = [];
                $arrayTipoPCFilho = [];
                $arrayParamPai = [];
                $arrayParamFilho = [];
                $arrayTipoParamPai = [];
                $arrayTipoParamFilho = [];
                $arraySimilar = [];
    
                $arrayPChave = explode(',', $palavraChave);
                $arrayCJunto = explode(',', $compreJunto);
                $arrayNegativaPai = explode(',', $negativaPai);
                $arrayNegativaFilho = explode(',', $negativaFilho);
                $arrayTipoPCPai = explode(',', $tipoPCPai);
                $arrayTipoPCFilho = explode(',', $tipoPCFilho);
                $arrayParamPai = explode(',', $paramPai);
                $arrayParamFilho = explode(',', $paramFilho);
                $arrayTipoParamPai = explode(',', $tipoParamPai); // 0- palavra-chave, 1- categoria, 2- preço, 3- porcentagem de desconto
                $arrayTipoParamFilho = explode(',', $tipoParamFilho);
    
                if(count($arrayPChave) > 1)
                {
                    foreach ($arrayPChave as $key => $value) 
                    {
                        $tamanhoString[$key] = strlen($arrayPChave[$key]);
                    }
    
                    if(count($arrayNegativaPai) > 1)
                    {
                        array_multisort($tamanhoString, SORT_DESC, $arrayPChave, $arrayCJunto, $arrayNegativaPai, $arrayNegativaFilho, $arrayTipoPCPai, $arrayTipoPCFilho,$arrayParamPai, $arrayParamFilho, $arrayTipoParamPai, $arrayTipoParamFilho);
                    }               
                    else
                    {
                        array_multisort($tamanhoString, SORT_DESC, $arrayPChave, $arrayCJunto);
                    }
                }
    
                $check = -1;
                
                foreach ($arrayPChave as $key => $value) 
                {
                    if($arrayTipoPCPai[$key] == '1')
                    {
                        $pos = strripos($typeProd, $arrayPChave[$key]);
                        if($pos !== false)
                        {
                            if($arrayNegativaPai[$key] != '' && $arrayNegativaPai[$key] != NULL)
                            {
                                $pos = strripos($typeProd, $arrayNegativaPai[$key]);
                                if($pos === false)
                                {
                                    if($arrayTipoParamPai[$key] == '0')
                                    {
                                        $pos = strripos($prodNome, $arrayParamPai[$key]);
                                        if($pos !== false)
                                        {
                                            $check = $key;
                                            break;
                                        }                                   
                                    }
                                    else if($arrayTipoParamPai[$key] == '1')
                                    {
                                        $pos = strripos($typeProd, $arrayParamPai[$key]);
                                        if($pos !== false)
                                        {
                                            $check = $key;
                                            break;
                                        }
                                    }
                                    else if($arrayTipoParamPai[$key] == '2')
                                    {
                                        if($salePriceProd == '' || $salePriceProd == '0.00' || $salePriceProd == NULL)
                                        {
                                            $salePriceProd = $priceProd;
                                        }
    
                                        $pos = strripos($arrayParamPai[$key], "<");
                                        if($pos !== false)
                                        {
                                            $arrayParamPai[$key] = substr($arrayParamPai[$key], 1);
    
                                            if(floatval($salePriceProd) < floatval($arrayParamPai[$key]))
                                            {
                                                $check = $key;
                                                break;
                                            }                               
                                        }
                                        else
                                        {
                                            $arrayParamPai[$key] = substr($arrayParamPai[$key], 1);
    
                                            if(floatval($salePriceProd) > floatval($arrayParamPai[$key]))
                                            {
                                                $check = $key;
                                                break;
                                            }   
                                        }
                                    }
                                    else if($arrayTipoParamPai[$key] == '3')
                                    {
                                        $pos = strripos($arrayParamPai[$key], "<");
                                        if($pos !== false)
                                        {
                                            $arrayParamPai[$key] = substr($arrayParamPai[$key], 1);
    
                                            if(intval($descontoProd) < intval($arrayParamPai[$key]))
                                            {
                                                $check = $key;
                                                break;
                                            }                               
                                        }
                                        else
                                        {
                                            $arrayParamPai[$key] = substr($arrayParamPai[$key], 1);
    
                                            if(intval($descontoProd) > intval($arrayParamPai[$key]))
                                            {
                                                $check = $key;
                                                break;
                                            }   
                                        }
                                    }
                                    else
                                    {
                                        $check = $key;
                                        break;
                                    }
                                }
                            }
                            else
                            {
                                if($arrayTipoParamPai[$key] == '0')
                                {
                                    $pos = strripos($prodNome, $arrayParamPai[$key]);
                                    if($pos !== false)
                                    {
                                        $check = $key;
                                        break;
                                    }                                   
                                }
                                else if($arrayTipoParamPai[$key] == '1')
                                {
                                    $pos = strripos($typeProd, $arrayParamPai[$key]);
                                    if($pos !== false)
                                    {
                                        $check = $key;
                                        break;
                                    }
                                }
                                else if($arrayTipoParamPai[$key] == '2')
                                {
                                    if($salePriceProd == '' || $salePriceProd == '0.00' || $salePriceProd == NULL)
                                    {
                                        $salePriceProd = $priceProd;
                                    }
    
                                    $pos = strripos($arrayParamPai[$key], "<");
                                    if($pos !== false)
                                    {
                                        $arrayParamPai[$key] = substr($arrayParamPai[$key], 1);
    
                                        if(floatval($salePriceProd) < floatval($arrayParamPai[$key]))
                                        {
                                            $check = $key;
                                            break;
                                        }                               
                                    }
                                    else
                                    {
                                        $arrayParamPai[$key] = substr($arrayParamPai[$key], 1);
    
                                        if(floatval($salePriceProd) > floatval($arrayParamPai[$key]))
                                        {
                                            $check = $key;
                                            break;
                                        }   
                                    }
                                }
                                else if($arrayTipoParamPai[$key] == '3')
                                {
                                    $pos = strripos($arrayParamPai[$key], "<");
                                    if($pos !== false)
                                    {
                                        $arrayParamPai[$key] = substr($arrayParamPai[$key], 1);
    
                                        if(intval($descontoProd) < intval($arrayParamPai[$key]))
                                        {
                                            $check = $key;
                                            break;
                                        }                               
                                    }
                                    else
                                    {
                                        $arrayParamPai[$key] = substr($arrayParamPai[$key], 1);
    
                                        if(intval($descontoProd) > intval($arrayParamPai[$key]))
                                        {
                                            $check = $key;
                                            break;
                                        }   
                                    }
                                }
                                else
                                {
                                    $check = $key;
                                    break;
                                }
                            }
                        }
                    }
                    else
                    {
                        $pos = strripos($prodNome, $arrayPChave[$key]);
                        if($pos !== false)
                        {
                            if($arrayNegativaPai[$key] != '' && $arrayNegativaPai[$key] != NULL)
                            {
                                $pos = strripos($prodNome, $arrayNegativaPai[$key]);
                                if($pos === false)
                                {
                                    if($arrayTipoParamPai[$key] == '0')
                                    {
                                        $pos = strripos($prodNome, $arrayParamPai[$key]);
                                        if($pos !== false)
                                        {
                                            $check = $key;
                                            break;
                                        }                                   
                                    }
                                    else if($arrayTipoParamPai[$key] == '1')
                                    {
                                        $pos = strripos($typeProd, $arrayParamPai[$key]);
                                        if($pos !== false)
                                        {
                                            $check = $key;
                                            break;
                                        }
                                    }
                                    else if($arrayTipoParamPai[$key] == '2')
                                    {
                                        if($salePriceProd == '' || $salePriceProd == '0.00' || $salePriceProd == NULL)
                                        {
                                            $salePriceProd = $priceProd;
                                        }
    
                                        $pos = strripos($arrayParamPai[$key], "<");
                                        if($pos !== false)
                                        {
                                            $arrayParamPai[$key] = substr($arrayParamPai[$key], 1);
    
                                            if(floatval($salePriceProd) < floatval($arrayParamPai[$key]))
                                            {
                                                $check = $key;
                                                break;
                                            }                               
                                        }
                                        else
                                        {
                                            $arrayParamPai[$key] = substr($arrayParamPai[$key], 1);
    
                                            if(floatval($salePriceProd) > floatval($arrayParamPai[$key]))
                                            {
                                                $check = $key;
                                                break;
                                            }   
                                        }
                                    }
                                    else if($arrayTipoParamPai[$key] == '3')
                                    {
                                        $pos = strripos($arrayParamPai[$key], "<");
                                        if($pos !== false)
                                        {
                                            $arrayParamPai[$key] = substr($arrayParamPai[$key], 1);
    
                                            if(intval($descontoProd) < intval($arrayParamPai[$key]))
                                            {
                                                $check = $key;
                                                break;
                                            }                               
                                        }
                                        else
                                        {
                                            $arrayParamPai[$key] = substr($arrayParamPai[$key], 1);
    
                                            if(intval($descontoProd) > intval($arrayParamPai[$key]))
                                            {
                                                $check = $key;
                                                break;
                                            }   
                                        }
                                    }
                                    else
                                    {
                                        $check = $key;
                                        break;
                                    }
                                }
                            }
                            else
                            {
                                if($arrayTipoParamPai[$key] == '0')
                                {
                                    $pos = strripos($prodNome, $arrayParamPai[$key]);
                                    if($pos !== false)
                                    {
                                        $check = $key;
                                        break;
                                    }                                   
                                }
                                else if($arrayTipoParamPai[$key] == '1')
                                {
                                    $pos = strripos($typeProd, $arrayParamPai[$key]);
                                    if($pos !== false)
                                    {
                                        $check = $key;
                                        break;
                                    }
                                }
                                else if($arrayTipoParamPai[$key] == '2')
                                {
                                    if($salePriceProd == '' || $salePriceProd == '0.00' || $salePriceProd == NULL)
                                    {
                                        $salePriceProd = $priceProd;
                                    }
    
                                    $pos = strripos($arrayParamPai[$key], "<");
                                    if($pos !== false)
                                    {
                                        $arrayParamPai[$key] = substr($arrayParamPai[$key], 1);
    
                                        if(floatval($salePriceProd) < floatval($arrayParamPai[$key]))
                                        {
                                            $check = $key;
                                            break;
                                        }                               
                                    }
                                    else
                                    {
                                        $arrayParamPai[$key] = substr($arrayParamPai[$key], 1);
    
                                        if(floatval($salePriceProd) > floatval($arrayParamPai[$key]))
                                        {
                                            $check = $key;
                                            break;
                                        }   
                                    }
                                }
                                else if($arrayTipoParamPai[$key] == '3')
                                {
                                    $pos = strripos($arrayParamPai[$key], "<");
                                    if($pos !== false)
                                    {
                                        $arrayParamPai[$key] = substr($arrayParamPai[$key], 1);
    
                                        if(intval($descontoProd) < intval($arrayParamPai[$key]))
                                        {
                                            $check = $key;
                                            break;
                                        }                               
                                    }
                                    else
                                    {
                                        $arrayParamPai[$key] = substr($arrayParamPai[$key], 1);
    
                                        if(intval($descontoProd) > intval($arrayParamPai[$key]))
                                        {
                                            $check = $key;
                                            break;
                                        }   
                                    }
                                }
                                else
                                {
                                    $check = $key;
                                    break;
                                }
                            }
                        }
                    }
                }
        
                if($check != -1)
                {
                    $arrayCJunto[$check] = strtoupper($arrayCJunto[$check]);
                    $arrayNegativaFilho[$check] = strtoupper($arrayNegativaFilho[$check]);
    
                    if($arrayParamFilho[$check] != '' && $arrayParamFilho[$check] != NULL)
                    {
                        if($arrayTipoParamFilho[$check] == '0')
                        {
                            $auxPar = strtoupper($arrayParamFilho[$check]);
                            $arrayParamFilho[$check] = " AND UPPER(XML_titulo) LIKE '%".$auxPar."%' ";
                        }
                        else if($arrayTipoParamFilho[$check] == '1')
                        {
                            $auxPar = strtoupper($arrayParamFilho[$check]);
                            $arrayParamFilho[$check] = " AND UPPER(XML_type) LIKE '%".$auxPar."%' ";
                        }
                        else if($arrayTipoParamFilho[$check] == '2')
                        {
                            $pos = strpos($arrayParamFilho[$key], "<");
                            if($pos !== false)
                            {
                                $auxPar = substr($arrayParamFilho[$check], 1);
                                $arrayParamFilho[$check] = " AND XML_sale_price < ".$auxPar;                            
                            }
                            else
                            {
                                $auxPar = substr($arrayParamFilho[$check], 1);
                                $arrayParamFilho[$check] = " AND XML_sale_price > ".$auxPar;
                            }
                        }
                        else if($arrayTipoParamFilho[$check] == '3')
                        {
                            $pos = strpos($arrayParamFilho[$key], "<");
                            if($pos !== false)
                            {
                                $auxPar = substr($arrayParamFilho[$check], 1);
                                $arrayParamFilho[$check] = " AND XML_desconto < ".$auxPar;                          
                            }
                            else
                            {
                                $auxPar = substr($arrayParamFilho[$check], 1);
                                $arrayParamFilho[$check] = " AND XML_desconto > ".$auxPar;
                            }
                        }                   
                    }
                    else
                    {
                        $arrayParamFilho[$check] = "";
                    }
    
                    if($arrayTipoPCFilho[$check] == '1')
                    {
                        if($arrayNegativaFilho[$check] != '' && $arrayNegativaFilho[$check] != NULL)
                        {
                            $auxNeg = $arrayNegativaFilho[$check];
                            $arrayNegativaFilho[$check] = " AND UPPER(XML_type) NOT LIKE '%".$auxNeg."%'";
                        }
                        else
                        {
                            $arrayNegativaFilho[$check] = "";
                        }
    
    
                        $select = ("SELECT XML_sku, XML_id, XML_link, XML_image_link, XML_titulo, XML_price, XML_sale_price FROM XML_".$idCli." WHERE XML_link != '$urlProdVista' AND XML_availability = '1' AND  UPPER(XML_type) LIKE '%".$arrayCJunto[$check]."%' ".$arrayNegativaFilho[$check]." ".$arrayParamFilho[$check]." GROUP BY XML_link HAVING COUNT(1) = 1 ORDER BY RAND() LIMIT 1");
    
                        file_put_contents('RespostaCJ1.txt', date('Y-m-d H:i:s') . "\r\n\r\n" . $select);
                        $result = mysqli_query($conDados, $select);
    
                        if(mysqli_num_rows ($result) > 0 )
                        {
                            $select = mysqli_fetch_array($result);
    
                            $link[1] = $select['XML_link'];
                            $linki[1] = $select['XML_image_link'];
                            $nome[1] = $select['XML_titulo'];
                            $precoN[1] = $select['XML_price'];
                            $precoP[1] = $select['XML_sale_price'];
                            $idProdCJ2 = $select['XML_id'];
                            $skuProdCJ2 = $select['XML_sku'];                       
                        }
                    }
                    else
                    {
                        if($arrayNegativaFilho[$check] != '' && $arrayNegativaFilho[$check] != NULL)
                        {
                            $auxNeg = $arrayNegativaFilho[$check];
                            $arrayNegativaFilho[$check] = " AND UPPER(XML_titulo) NOT LIKE '%".$auxNeg."%'";
                        }
                        else
                        {
                            $arrayNegativaFilho[$check] = "";
                        }
    
                        $arrayCJunto[$check] = str_replace(" ", "%", $arrayCJunto[$check]);
    
                        $select = ("SELECT XML_sku, XML_id, XML_link, XML_image_link, XML_titulo, XML_price, XML_sale_price FROM XML_".$idCli." WHERE XML_link != '$urlProdVista' AND XML_availability = '1' AND  UPPER(XML_titulo) LIKE '%".$arrayCJunto[$check]."%' ".$arrayNegativaFilho[$check]." ".$arrayParamFilho[$check]." GROUP BY XML_link HAVING COUNT(1) = 1 ORDER BY RAND() LIMIT 1");
                        $result = mysqli_query($conDados, $select);
    
                        file_put_contents('RespostaCJ.txt', date('Y-m-d H:i:s') . "\r\n\r\n" . $select);
    
                            
                        if(mysqli_num_rows ($result) > 0 )
                        {
                            $select = mysqli_fetch_array($result);
    
                            $link[1] = $select['XML_link'];
                            $linki[1] = $select['XML_image_link'];
                            $nome[1] = $select['XML_titulo'];
                            $precoN[1] = $select['XML_price'];
                            $precoP[1] = $select['XML_sale_price'];
                            $idProdCJ2 = $select['XML_id'];
                            $skuProdCJ2 = $select['XML_sku'];
                        }
                    }
    
                    if(!empty($link[1]))
                    {
                        $select = ("SELECT XML_link, XML_sku, XML_id, XML_image_link, XML_titulo, XML_price, XML_sale_price FROM XML_".$idCli." WHERE XML_link = '$urlProdVista' AND XML_availability = '1'");
                        $result = mysqli_query($conDados, $select);
    
                        if(mysqli_num_rows ($result) == 1)
                        {
    
                            $select = mysqli_fetch_array($result);
    
                            $link[0] = $select['XML_link'];
                            $linki[0] = $select['XML_image_link'];
                            $nome[0] = $select['XML_titulo'];
                            $precoN[0] = $select['XML_price'];
                            $precoP[0] = $select['XML_sale_price'];
                            $idProdCJ1 = $select['XML_id'];
                            $skuProdCJ1 = $select['XML_sku'];
                        
                            $select = "SELECT CLI_plataforma FROM cliente WHERE CLI_id = '$idCli'";
                            $result = mysqli_query($conDados, $select);
    
                            if(mysqli_num_rows ($result) > 0 )
                            {
                                $select = mysqli_fetch_array($result);
    
                                $cliPlataforma = $select['CLI_plataforma'];
    
                                if($cliPlataforma == 1) // VTEX
                                {
                                    $select = "SELECT URL_site FROM URL WHERE URL_id_cliente = '$idCli'";
                                    $result = mysqli_query($conDados, $select);
    
                                    if(mysqli_num_rows ($result) > 0 )
                                    {
                                        $select = mysqli_fetch_array($result);
    
                                        $cliSite = $select['URL_site'];
    
                                        $ultString = substr($cliSite, -1);
    
                                        if($ultString == '/')
                                        {
                                            $cliSite = substr($cliSite, 0, -1);
                                        }
    
                                        if($skuProdCJ1 != '' || $skuProdCJ1 != null || $skuProdCJ2 != '' || $skuProdCJ2 != null)
                                        {                                            
                                            $cliSite = $cliSite."/checkout/cart/add?sku=".$skuProdCJ1."&sku=".$skuProdCJ2."&qty=1&qty=1&seller=1&seller=1&redirect=true&sc=1";
                                        }
                                        else
                                        {
                                            $cliSite = $cliSite."/checkout/cart/add?sku=".$idProdCJ1."&sku=".$idProdCJ2."&qty=1&qty=1&seller=1&seller=1";
                                        }                                   
    
                                        $link[2] = $cliSite;
                                        $linki[2] = "0,0";
                                        $nome[2] = "5";
                                        $precoN[2] = $cliSite;
                                        $precoP[2] = "COMPRE JUNTO";
                                    }
                                }
                                else
                                {
                                    $select = "SELECT URL_site FROM URL WHERE URL_id_cliente = '$idCli'";
                                    $result = mysqli_query($conDados, $select);
    
                                    if(mysqli_num_rows ($result) > 0 )
                                    {
                                        $select = mysqli_fetch_array($result);
    
                                        $cliSite = $select['URL_site'];
                                    }
                                    
                                    $link[2] = $link[1];
                                    $linki[2] = $idProdCJ1.",".$idProdCJ2; //ids prods
                                    $nome[2] = $cliPlataforma; //plataforma
                                    $precoN[2] = $cliSite; //site cliente
                                    $precoP[2] = "COMPRE JUNTO";
                                }
                            }
                        }
                    }               
                }
            }
            else if($widTipo == 9) // MANUAL
            {
                $widprod = explode(',', $widprod);

                foreach ($widprod as $key => $value) 
                {
                    $select = ("SELECT XML_link, XML_image_link, XML_titulo, XML_price, XML_sale_price FROM XML_".$idcli." WHERE XML_id = '$value'");
                    $result = mysqli_query($conDados, $select);

                    if(mysqli_num_rows ($result) > 0 )
                    {
                        $select = mysqli_fetch_array($result);

                        $link[$key] = $select['XML_link'];
                        $linki[$key] = $select['XML_image_link'];
                        $nome[$key] = $select['XML_titulo'];
                        $precoN[$key] = $select['XML_price'];
                        $precoP[$key] = $select['XML_sale_price'];
                    }                       
                }
            }
            */
            else if($widTipo == 10) // Remarketing On-site dinamico
            {
                // FEITO
            }
            /*
            else if($widTipo == 11) // COMPLEMENTAR CARRINHO
            {
                $date = explode(',', $cookiedata);
                $idprod = explode(',', $cookieprod);
                $evento = explode(',', $cookieevento);

                array_multisort($date, SORT_DESC, $idprod, $evento);

                foreach($evento as $key => $value)
                { 
                    if($value != 'cart')
                    {
                        unset($evento[$key]);
                        unset($idprod[$key]);
                        unset($date[$key]);
                    }
                }
                
                $idprod = array_values($idprod);
                
                if(count($idprod) > 0)
                {
                    if(count($idprod) == 1) // 16
                    {
                        $select = ("SELECT XML_comp_carrinho FROM XML_".$idCli." WHERE XML_id = ".$idprod[0]." AND XML_availability = '1'");
                        $result = mysqli_query($conDados, $select);
            
                        if(mysqli_num_rows ($result) > 0 )
                        {
                            $select = mysqli_fetch_array($result);
                            
                            $arrayComp = explode(',', $select['XML_comp_carrinho']);
                            
                            $i = 0;
                            
                            foreach($arrayComp as $key => $value)
                            {
                                $select = ("SELECT XML_id, XML_brand, XML_sku, XML_link, XML_image_link, XML_titulo, XML_price, XML_sale_price  FROM XML_".$idCli." WHERE XML_id = ".$arrayComp[$key]." AND XML_availability = '1'");
                                $result = mysqli_query($conDados, $select);
                    
                                if(mysqli_num_rows ($result) > 0 )
                                {
                                    $select = mysqli_fetch_array($result);
                                    
                                    $check = 0;
                                    foreach ($link as $key => $value) 
                                    {
                                        if($value == $select['XML_link'])
                                        {
                                            $check = 1;
                                        }
                                    }
                                    if(!$check)
                                    {
                                        $link[$i] = $select['XML_link'];
                                        $linki[$i] = $select['XML_image_link'];
                                        $nome[$i] = $select['XML_titulo'];
                                        $precoN[$i] = $select['XML_price'];
                                        $precoP[$i] = $select['XML_sale_price'];
                                        $sku[$i] =$select['XML_sku'];
                                        $brand[$i] = $select['XML_brand'];
                                        $idProd[$i] = $select['XML_id'];
                                        $i++;
                                    }
                                }
                            }
                        }
                    }
                    if(count($idprod) == 2) // 12 + 12 = 24
                    {
                        $select = ("SELECT XML_comp_carrinho FROM XML_".$idCli." WHERE XML_id = ".$idprod[0]." AND XML_availability = '1'");
                        $result = mysqli_query($conDados, $select);
            
                        if(mysqli_num_rows ($result) > 0 )
                        {
                            $select = mysqli_fetch_array($result);
                            
                            $arrayComp = explode(',', $select['XML_comp_carrinho']);
                            
                            $i = 0;
                            
                            foreach($arrayComp as $key => $value)
                            {
                                $select = ("SELECT XML_id, XML_brand, XML_sku, XML_link, XML_image_link, XML_titulo, XML_price, XML_sale_price  FROM XML_".$idCli." WHERE XML_id != ".$idprod[1]." AND XML_id = ".$arrayComp[$key]." AND XML_availability = '1'");
                                $result = mysqli_query($conDados, $select);
                    
                                if(mysqli_num_rows ($result) > 0 )
                                {
                                    $select = mysqli_fetch_array($result);
                                    
                                    $check = 0;
                                    foreach ($link as $key => $value) 
                                    {
                                        if($value == $select['XML_link'])
                                        {
                                            $check = 1;
                                        }
                                    }
                                    if(!$check)
                                    {
                                        $link[$i] = $select['XML_link'];
                                        $linki[$i] = $select['XML_image_link'];
                                        $nome[$i] = $select['XML_titulo'];
                                        $precoN[$i] = $select['XML_price'];
                                        $precoP[$i] = $select['XML_sale_price'];
                                        $sku[$i] = $select['XML_sku'];
                                        $brand[$i] = $select['XML_brand'];
                                        $idProd[$i] = $select['XML_id'];
                                        $i = $i + 2;
                                        
                                        if($i == 26)
                                        {
                                            break;
                                        }
                                    }
                                }
                            }
                        }
                        
                        $select = ("SELECT XML_comp_carrinho FROM XML_".$idCli." WHERE XML_id = ".$idprod[1]." AND XML_availability = '1'");
                        $result = mysqli_query($conDados, $select);
            
                        if(mysqli_num_rows ($result) > 0 )
                        {
                            $select = mysqli_fetch_array($result);
                            
                            $arrayComp = explode(',', $select['XML_comp_carrinho']);
                            
                            $i = 1;
                            
                            foreach($arrayComp as $key => $value)
                            {
                                $select = ("SELECT XML_id, XML_brand, XML_sku, XML_link, XML_image_link, XML_titulo, XML_price, XML_sale_price  FROM XML_".$idCli." WHERE XML_id != ".$idprod[0]." AND XML_id = ".$arrayComp[$key]." AND XML_availability = '1'");
                                $result = mysqli_query($conDados, $select);
                    
                                if(mysqli_num_rows ($result) > 0 )
                                {
                                    $select = mysqli_fetch_array($result);
                                    
                                    $check = 0;
                                    foreach ($link as $key => $value) 
                                    {
                                        if($value == $select['XML_link'])
                                        {
                                            $check = 1;
                                        }
                                    }
                                    if(!$check)
                                    {
                                        $link[$i] = $select['XML_link'];
                                        $linki[$i] = $select['XML_image_link'];
                                        $nome[$i] = $select['XML_titulo'];
                                        $precoN[$i] = $select['XML_price'];
                                        $precoP[$i] = $select['XML_sale_price'];
                                        $sku[$i] = $select['XML_sku'];
                                        $brand[$i] = $select['XML_brand'];
                                        $idProd[$i] = $select['XML_id'];
                                        $i = $i + 2;
                                        
                                        if($i == 25)
                                        {
                                            break;
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if(count($idprod) == 3) // 8 + 8 + 8 = 24
                    {
                        $select = ("SELECT XML_comp_carrinho FROM XML_".$idCli." WHERE XML_id = ".$idprod[0]." AND XML_availability = '1'");
                        $result = mysqli_query($conDados, $select);
            
                        if(mysqli_num_rows ($result) > 0 )
                        {
                            $select = mysqli_fetch_array($result);
                            
                            $arrayComp = explode(',', $select['XML_comp_carrinho']);
                            
                            $i = 0;
                            
                            foreach($arrayComp as $key => $value)
                            {
                                $select = ("SELECT XML_id, XML_brand, XML_sku, XML_link, XML_image_link, XML_titulo, XML_price, XML_sale_price  FROM XML_".$idCli." WHERE XML_id != ".$idprod[1]." AND XML_id != ".$idprod[2]." AND XML_id = ".$arrayComp[$key]." AND XML_availability = '1'");
                                $result = mysqli_query($conDados, $select);
                    
                                if(mysqli_num_rows ($result) > 0 )
                                {
                                    $select = mysqli_fetch_array($result);
                                    
                                    $check = 0;
                                    foreach ($link as $key => $value) 
                                    {
                                        if($value == $select['XML_link'])
                                        {
                                            $check = 1;
                                        }
                                    }
                                    if(!$check)
                                    {
                                        $link[$i] = $select['XML_link'];
                                        $linki[$i] = $select['XML_image_link'];
                                        $nome[$i] = $select['XML_titulo'];
                                        $precoN[$i] = $select['XML_price'];
                                        $precoP[$i] = $select['XML_sale_price'];
                                        $sku[$i] = $select['XML_sku'];
                                        $brand[$i] = $select['XML_brand'];
                                        $idProd[$i] = $select['XML_id'];
                                        $i = $i + 3;
                                        
                                        if($i == 27)
                                        {
                                            break;
                                        }
                                    }
                                }
                            }
                        }
                        
                        $select = ("SELECT XML_comp_carrinho FROM XML_".$idCli." WHERE XML_id = ".$idprod[1]." AND XML_availability = '1'");
                        $result = mysqli_query($conDados, $select);
            
                        if(mysqli_num_rows ($result) > 0 )
                        {
                            $select = mysqli_fetch_array($result);
                            
                            $arrayComp = explode(',', $select['XML_comp_carrinho']);
                            
                            $i = 1;
                            
                            foreach($arrayComp as $key => $value)
                            {
                                $select = ("SELECT XML_id, XML_brand, XML_sku, XML_link, XML_image_link, XML_titulo, XML_price, XML_sale_price  FROM XML_".$idCli." WHERE XML_id != ".$idprod[0]." AND XML_id != ".$idprod[2]." AND XML_id = ".$arrayComp[$key]." AND XML_availability = '1'");
                                $result = mysqli_query($conDados, $select);
                    
                                if(mysqli_num_rows ($result) > 0 )
                                {
                                    $select = mysqli_fetch_array($result);
                                    
                                    $check = 0;
                                    foreach ($link as $key => $value) 
                                    {
                                        if($value == $select['XML_link'])
                                        {
                                            $check = 1;
                                        }
                                    }
                                    if(!$check)
                                    {
                                        $link[$i] = $select['XML_link'];
                                        $linki[$i] = $select['XML_image_link'];
                                        $nome[$i] = $select['XML_titulo'];
                                        $precoN[$i] = $select['XML_price'];
                                        $precoP[$i] = $select['XML_sale_price'];
                                        $sku[$i] = $select['XML_sku'];
                                        $brand[$i] = $select['XML_brand'];
                                        $idProd[$i] = $select['XML_id'];
                                        $i = $i + 3;
                                        
                                        if($i == 26)
                                        {
                                            break;
                                        }
                                    }
                                }
                            }
                        }
                        $select = ("SELECT XML_comp_carrinho FROM XML_".$idCli." WHERE XML_id = ".$idprod[2]." AND XML_availability = '1'");
                        $result = mysqli_query($conDados, $select);
            
                        if(mysqli_num_rows ($result) > 0 )
                        {
                            $select = mysqli_fetch_array($result);
                            
                            $arrayComp = explode(',', $select['XML_comp_carrinho']);
                            
                            $i = 2;
                            
                            foreach($arrayComp as $key => $value)
                            {
                                $select = ("SELECT XML_id, XML_brand, XML_sku, XML_link, XML_image_link, XML_titulo, XML_price, XML_sale_price  FROM XML_".$idCli." WHERE XML_id != ".$idprod[1]." AND XML_id != ".$idprod[0]." AND XML_id = ".$arrayComp[$key]." AND XML_availability = '1'");
                                $result = mysqli_query($conDados, $select);
                    
                                if(mysqli_num_rows ($result) > 0 )
                                {
                                    $select = mysqli_fetch_array($result);
                                    
                                    $check = 0;
                                    foreach ($link as $key => $value) 
                                    {
                                        if($value == $select['XML_link'])
                                        {
                                            $check = 1;
                                        }
                                    }
                                    if(!$check)
                                    {
                                        $link[$i] = $select['XML_link'];
                                        $linki[$i] = $select['XML_image_link'];
                                        $nome[$i] = $select['XML_titulo'];
                                        $precoN[$i] = $select['XML_price'];
                                        $precoP[$i] = $select['XML_sale_price'];
                                        $sku[$i] = $select['XML_sku'];
                                        $brand[$i] = $select['XML_brand'];
                                        $idProd[$i] = $select['XML_id'];
                                        $i = $i + 3;
                                        
                                        if($i == 25)
                                        {
                                            break;
                                        }
                                    }
                                }
                            }
                        }                        
                    }
                    if(count($idprod) >= 4) // 6 + 6 + 6 + 6 = 24
                    {
                        $select = ("SELECT XML_comp_carrinho FROM XML_".$idCli." WHERE XML_id = ".$idprod[0]." AND XML_availability = '1'");
                        $result = mysqli_query($conDados, $select);
            
                        if(mysqli_num_rows ($result) > 0 )
                        {
                            $select = mysqli_fetch_array($result);
                            
                            $arrayComp = explode(',', $select['XML_comp_carrinho']);
                            
                            $i = 0;
                            
                            foreach($arrayComp as $key => $value)
                            {
                                $select = ("SELECT XML_id, XML_brand, XML_sku, XML_link, XML_image_link, XML_titulo, XML_price, XML_sale_price  FROM XML_".$idCli." WHERE XML_id != ".$idprod[1]." AND XML_id != ".$idprod[2]." AND XML_id != ".$idprod[3]." AND XML_id = ".$arrayComp[$key]." AND XML_availability = '1'");
                                $result = mysqli_query($conDados, $select);
                    
                                if(mysqli_num_rows ($result) > 0 )
                                {
                                    $select = mysqli_fetch_array($result);
                                    
                                    $check = 0;
                                    foreach ($link as $key => $value) 
                                    {
                                        if($value == $select['XML_link'])
                                        {
                                            $check = 1;
                                        }
                                    }
                                    if(!$check)
                                    {
                                        $link[$i] = $select['XML_link'];
                                        $linki[$i] = $select['XML_image_link'];
                                        $nome[$i] = $select['XML_titulo'];
                                        $precoN[$i] = $select['XML_price'];
                                        $precoP[$i] = $select['XML_sale_price'];
                                        $sku[$i] = $select['XML_sku'];
                                        $brand[$i] = $select['XML_brand'];
                                        $idProd[$i] = $select['XML_id'];
                                        $i = $i + 4;
                                        
                                        if($i == 28)
                                        {
                                            break;
                                        }
                                    }
                                }
                            }
                        }
                        
                        $select = ("SELECT XML_comp_carrinho FROM XML_".$idCli." WHERE XML_id = ".$idprod[1]." AND XML_availability = '1'");
                        $result = mysqli_query($conDados, $select);
            
                        if(mysqli_num_rows ($result) > 0 )
                        {
                            $select = mysqli_fetch_array($result);
                            
                            $arrayComp = explode(',', $select['XML_comp_carrinho']);
                            
                            $i = 1;
                            
                            foreach($arrayComp as $key => $value)
                            {
                                $select = ("SELECT XML_id, XML_brand, XML_sku, XML_link, XML_image_link, XML_titulo, XML_price, XML_sale_price  FROM XML_".$idCli." WHERE XML_id != ".$idprod[0]." AND XML_id != ".$idprod[2]." AND XML_id != ".$idprod[3]." AND XML_id = ".$arrayComp[$key]." AND XML_availability = '1'");
                                $result = mysqli_query($conDados, $select);
                    
                                if(mysqli_num_rows ($result) > 0 )
                                {
                                    $select = mysqli_fetch_array($result);
                                    
                                    $check = 0;
                                    foreach ($link as $key => $value) 
                                    {
                                        if($value == $select['XML_link'])
                                        {
                                            $check = 1;
                                        }
                                    }
                                    if(!$check)
                                    {
                                        $link[$i] = $select['XML_link'];
                                        $linki[$i] = $select['XML_image_link'];
                                        $nome[$i] = $select['XML_titulo'];
                                        $precoN[$i] = $select['XML_price'];
                                        $precoP[$i] = $select['XML_sale_price'];
                                        $sku[$i] = $select['XML_sku'];
                                        $brand[$i] = $select['XML_brand'];
                                        $idProd[$i] = $select['XML_id'];
                                        $i = $i + 4;
                                        
                                        if($i == 27)
                                        {
                                            break;
                                        }
                                    }
                                }
                            }
                        }
                        $select = ("SELECT XML_comp_carrinho FROM XML_".$idCli." WHERE XML_id = ".$idprod[2]." AND XML_availability = '1'");
                        $result = mysqli_query($conDados, $select);
            
                        if(mysqli_num_rows ($result) > 0 )
                        {
                            $select = mysqli_fetch_array($result);
                            
                            $arrayComp = explode(',', $select['XML_comp_carrinho']);
                            
                            $i = 2;
                            
                            foreach($arrayComp as $key => $value)
                            {
                                $select = ("SELECT XML_id, XML_brand, XML_sku, XML_link, XML_image_link, XML_titulo, XML_price, XML_sale_price  FROM XML_".$idCli." WHERE XML_id != ".$idprod[1]." AND XML_id != ".$idprod[0]." AND XML_id != ".$idprod[3]." AND XML_id = ".$arrayComp[$key]." AND XML_availability = '1'");
                                $result = mysqli_query($conDados, $select);
                    
                                if(mysqli_num_rows ($result) > 0 )
                                {
                                    $select = mysqli_fetch_array($result);
                                    
                                    $check = 0;
                                    foreach ($link as $key => $value) 
                                    {
                                        if($value == $select['XML_link'])
                                        {
                                            $check = 1;
                                        }
                                    }
                                    if(!$check)
                                    {
                                        $link[$i] = $select['XML_link'];
                                        $linki[$i] = $select['XML_image_link'];
                                        $nome[$i] = $select['XML_titulo'];
                                        $precoN[$i] = $select['XML_price'];
                                        $precoP[$i] = $select['XML_sale_price'];
                                        $sku[$i] = $select['XML_sku'];
                                        $brand[$i] = $select['XML_brand'];
                                        $idProd[$i] = $select['XML_id'];
                                        $i = $i + 4;
                                        
                                        if($i == 26)
                                        {
                                            break;
                                        }
                                    }
                                }
                            }
                        }
                        $select = ("SELECT XML_comp_carrinho FROM XML_".$idCli." WHERE XML_id = ".$idprod[3]." AND XML_availability = '1'");
                        $result = mysqli_query($conDados, $select);
            
                        if(mysqli_num_rows ($result) > 0 )
                        {
                            $select = mysqli_fetch_array($result);
                            
                            $arrayComp = explode(',', $select['XML_comp_carrinho']);
                            
                            $i = 3;
                            
                            foreach($arrayComp as $key => $value)
                            {
                                $select = ("SELECT XML_id, XML_brand, XML_sku, XML_link, XML_image_link, XML_titulo, XML_price, XML_sale_price  FROM XML_".$idCli." WHERE XML_id != ".$idprod[1]." AND XML_id != ".$idprod[2]." AND XML_id != ".$idprod[0]." AND XML_id = ".$arrayComp[$key]." AND XML_availability = '1'");
                                $result = mysqli_query($conDados, $select);
                    
                                if(mysqli_num_rows ($result) > 0 )
                                {
                                    $select = mysqli_fetch_array($result);
                                    
                                    $check = 0;
                                    foreach ($link as $key => $value) 
                                    {
                                        if($value == $select['XML_link'])
                                        {
                                            $check = 1;
                                        }
                                    }
                                    if(!$check)
                                    {
                                        $link[$i] = $select['XML_link'];
                                        $linki[$i] = $select['XML_image_link'];
                                        $nome[$i] = $select['XML_titulo'];
                                        $precoN[$i] = $select['XML_price'];
                                        $precoP[$i] = $select['XML_sale_price'];
                                        $sku[$i] = $select['XML_sku'];
                                        $brand[$i] = $select['XML_brand'];
                                        $idProd[$i] = $select['XML_id'];
                                        $i = $i + 4;
                                        
                                        if($i == 25)
                                        {
                                            break;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                else
                {
                    $date = explode(',', $cookiedata);
                    $idprod = explode(',', $cookieprod);
                    $evento = explode(',', $cookieevento);
        
                    array_multisort($date, SORT_DESC, $idprod, $evento);
        
                    foreach($evento as $key => $value)
                    { 
                        if($value != 'product')
                        {
                            unset($evento[$key]);
                            unset($idprod[$key]);
                            unset($date[$key]);
                        }
                    }
                    
                    $idprod = array_values($idprod);
                    
                    if(count($idprod) > 0) // 16
                    {
                        $select = ("SELECT XML_comp_carrinho FROM XML_".$idCli." WHERE XML_id = ".$idprod[0]." AND XML_availability = '1'");
                        $result = mysqli_query($conDados, $select);
            
                        if(mysqli_num_rows ($result) > 0 )
                        {
                            $select = mysqli_fetch_array($result);
                            
                            $arrayComp = explode(',', $select['XML_comp_carrinho']);
                            
                            $i = 0;
                            
                            foreach($arrayComp as $key => $value)
                            {
                                $select = ("SELECT XML_id, XML_brand, XML_sku, XML_link, XML_image_link, XML_titulo, XML_price, XML_sale_price  FROM XML_".$idCli." WHERE XML_id = ".$arrayComp[$key]." AND XML_availability = '1'");
                                $result = mysqli_query($conDados, $select);
                    
                                if(mysqli_num_rows ($result) > 0 )
                                {
                                    $select = mysqli_fetch_array($result);
                                    
                                    $check = 0;
                                    foreach ($link as $key => $value) 
                                    {
                                        if($value == $select['XML_link'])
                                        {
                                            $check = 1;
                                        }
                                    }
                                    if(!$check)
                                    {
                                        $link[$i] = $select['XML_link'];
                                        $linki[$i] = $select['XML_image_link'];
                                        $nome[$i] = $select['XML_titulo'];
                                        $precoN[$i] = $select['XML_price'];
                                        $precoP[$i] = $select['XML_sale_price'];
                                        $sku[$i] = $select['XML_sku'];
                                        $brand[$i] = $select['XML_brand'];
                                        $idProd[$i] = $select['XML_id'];
                                        $i++;
                                    }
                                }
                            }
                        }
                    }
                }
            }
            else if($widTipo == 12) // COMPLEMENTAR VENDAS
            {
                $select = ("SELECT XML_comp_venda FROM XML_".$idCli." WHERE XML_id = '$idproduto' AND XML_availability = '1'");
                $result = mysqli_query($conDados, $select);

                if(mysqli_num_rows ($result) > 0 )
                {
                    $select = mysqli_fetch_array($result);
                    
                    $arrayComp = explode(',', $select['XML_comp_venda']);
                    
                    $i = 0;
                    
                    foreach($arrayComp as $key => $value)
                    {
                        if($arrayComp[$key] != '' && $arrayComp[$key] != null)
                        {
                            $select = ("SELECT XML_link, XML_image_link, XML_titulo, XML_price, XML_sale_price  FROM XML_".$idCli." WHERE XML_id = ".$arrayComp[$key]." AND XML_availability = '1'");
                            $result = mysqli_query($conDados, $select);
                
                            if(mysqli_num_rows ($result) > 0 )
                            {
                                $select = mysqli_fetch_array($result);
                                
                                $link[$i] = $select['XML_link'];
                                $linki[$i] = $select['XML_image_link'];
                                $nome[$i] = $select['XML_titulo'];
                                $precoN[$i] = $select['XML_price'];
                                $precoP[$i] = $select['XML_sale_price'];
                                $i++;
                            }
                        }
                    }
                }
            }
            */

            return $obj;
        //}
        //else
        //{
        //    return '';
        //}
    }
?>
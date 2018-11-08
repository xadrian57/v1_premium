<?php
namespace roihero\widget\inteligencia;

class CompreJuntoInteligencia extends AbstractInteligencia {
    
    private $tamanhoString = [];
    private $arrayPai = [];
    private $arrayFilho = [];
    private $arrayNegativaPai = [];
    private $arrayNegativaFilho = [];
    private $arrayTipoPai = [];
    private $arrayTipoFilho = [];
    private $arrayParamPai = [];
    private $arrayParamFilho = [];
    private $arrayTipoParamPai = [];// 0- palavra-chave, 1- categoria, 2- preço, 3- porcentagem de desconto
    private $arrayTipoParamFilho = [];
    private $arraySimilar = [];
    private $prodDados;

    /**
     * 
     * {@inheritDoc}
     * @see \roihero\widget\inteligencia\IInteligencia::processar()
     */
    public function processar() {
        
        if($this->widget->getProdId() != 'undefined')
        {
            // Preparando as variáveis
            $this->popularVariaveis();
            $this->ordernarVariaveis();
            
            // Recupera o index de acordo com a estrutura
            $index = $this->getIndexValue();
            
            // Só realiza o processamento se encontrar o index
            if($index != -1)
            {
                $this->arrayFilho[$index] = strtoupper($this->arrayFilho[$index]);
                $this->arrayNegativaFilho[$index] = strtoupper($this->arrayNegativaFilho[$index]);
                
                if($this->arrayParamFilho[$index] != '' && $this->arrayParamFilho[$index] != NULL)
                {
                    if($this->arrayParamFilho[$index] == '0')
                    {
                        $auxPar = strtoupper($this->arrayParamFilho[$index]);
                        $this->arrayParamFilho[$index] = " AND MATCH(XML_titulo_upper) AGAINST('\"". strtoupper($auxPar) ."\"' IN BOOLEAN MODE) ";
                        
                    }
                    else if($this->arrayParamFilho[$index] == '1')
                    {
                        $auxPar = strtoupper($this->arrayParamFilho[$index]);
                        $this->arrayParamFilho[$index] = " AND MATCH(XML_type_upper) AGAINST('\"". strtoupper($auxPar) ."\"' IN BOOLEAN MODE) ";
                    }
                    else if($this->arrayParamFilho[$index] == '2')
                    {
                        $pos = strpos($this->arrayParamFilho[$key], "<");
                        if($pos !== false)
                        {
                            $auxPar = substr($this->arrayParamFilho[$index], 1);
                            $this->arrayParamFilho[$index] = " AND XML_sale_price < ".$auxPar;
                        }
                        else
                        {
                            $auxPar = substr($this->arrayParamFilho[$index], 1);
                            $this->arrayParamFilho[$index] = " AND XML_sale_price > ".$auxPar;
                        }
                    }
                    else if($this->arrayParamFilho[$index] == '3')
                    {
                        $pos = strpos($this->arrayParamFilho[$key], "<");
                        if($pos !== false)
                        {
                            $auxPar = substr($this->arrayParamFilho[$index], 1);
                            $this->arrayParamFilho[$index] = " AND XML_desconto < ".$auxPar;
                        }
                        else
                        {
                            $auxPar = substr($this->arrayParamFilho[$index], 1);
                            $this->arrayParamFilho[$index] = " AND XML_desconto > ".$auxPar;
                        }
                    }
                }
                else
                {
                    $this->arrayParamFilho[$index] = "";
                }
                
                if($this->arrayTipoFilho[$index] == '1')
                {
                    if($this->arrayNegativaFilho[$index] != '' && $this->arrayNegativaFilho[$index] != NULL)
                    {
                        $auxNeg = $this->arrayNegativaFilho[$index];
                        $this->arrayNegativaFilho[$index] = " AND NOT (MATCH(XML_type_upper) AGAINST('\"". strtoupper($auxNeg) ."\"' IN BOOLEAN MODE)) ";
                    }
                    else
                    {
                        $this->arrayNegativaFilho[$index] = "";
                    }
                    
                    $select = "SELECT " . $this->XML_select . "
                               FROM XML_".$this->widget->getIdCli()."
                               WHERE XML_link != '" . $this->widget->getUrl() . "'
                               AND XML_availability = '1'
                               AND MATCH(XML_type_upper) AGAINST(\"" . strtoupper($this->arrayFilho[$index]) ."\" IN BOOLEAN MODE) "
                               . $this->arrayNegativaFilho[$index] . " " . $this->arrayParamFilho[$index]."
                               GROUP BY XML_link
                               HAVING COUNT(1) = 1
                               ORDER BY RAND() LIMIT 1";
                    
                    $this->executeSelectCompreJunto($select, 1);
                }
                else
                {
                    if($this->arrayNegativaFilho[$index] != '' && $this->arrayNegativaFilho[$index] != NULL)
                    {
                        $auxNeg = $this->arrayNegativaFilho[$index];
                        $this->arrayNegativaFilho[$index] = " AND NOT (MATCH(XML_titulo_upper) AGAINST('\"". strtoupper($auxNeg) ."\"' IN BOOLEAN MODE)) ";
                    }
                    else
                    {
                        $this->arrayNegativaFilho[$index] = "";
                    }
                    
                    $this->arrayFilho[$index] = str_replace(" ", "%", $this->arrayFilho[$index]);
                    
                    $select = "SELECT " . $this->XML_select . "
                               FROM XML_".$this->widget->getIdCli()."
                               WHERE XML_link != '" . $this->widget->getUrl() . "'
                               AND XML_availability = '1'
                               AND MATCH(XML_titulo_upper) AGAINST(\"" . strtoupper($this->arrayFilho[$index]) ."\" IN BOOLEAN MODE) "
                               . $this->arrayNegativaFilho[$index] . " " . $this->arrayParamFilho[$index]."
                               GROUP BY XML_link
                               HAVING COUNT(1) = 1
                               ORDER BY RAND() LIMIT 1";
                    
                    $this->executeSelectCompreJunto($select, 1);
                }
                
                $tempObj = $this->widget->getObj();
                
                if(!empty($tempObj))
                {
                    $select = "SELECT " . $this->XML_select . "
                               FROM XML_". $this->widget->getIdCli()."
                               WHERE XML_id = '" . $this->widget->getProdId() . "'
                               AND XML_availability = '1'";
                    
                    $this->executeSelectCompreJunto($select, 0);
                }
            }
        }
    }
    
    /**
     * Executa o select básico
     *
     * @param string $select
     */
    protected function executeSelectCompreJunto($select, $posicao) {
        $result = mysqli_query($this->widget->getConDados(), $select);
        
        $i = 0;
        if($result && mysqli_num_rows ($result) > 0 )
        {
            $linha = mysqli_fetch_array($result);
            
            if($this->setOBJ($this->widget->getIdWid(), $linha, $this->widgetProps['WID_utm'], $posicao))
            {
                $i++;
            }
            
        }
        
        return $i;
    }
    
    /**
     * Popula as variáveis usada em todo o código
     */
    private function popularVariaveis() {
        
        $this->prodDados = $this->getProdDados();
        
        $selectWidgetConfig = "SELECT 
                               WC_id_wid,
                               WC_id_produto,
                               WC_cupom,
                               WC_categoria,
                               WC_param_compre_junto,
                               WC_hora_ini,
                               WC_hora_fim,
                               WC_cj_f,
                               WC_cj_p,
                               WC_collection,
                               tx_negativa_pai,
                               tx_negativa_filho,
                               tx_tipo_pai,
                               tx_tipo_filho,
                               tx_param_pai,
                               tx_param_filho,
                               tx_tipo_param_pai,
                               tx_tipo_param_filho
                               FROM widget_config WHERE WC_id_wid = ". $this->widget->getIdWid();
        
        $result = mysqli_query($this->widget->getConCad(), $selectWidgetConfig);
        
        $linha = mysqli_fetch_array($result);
        
        $this->arrayPai = explode(',', $linha['WC_cj_p']);
        $this->arrayFilho = explode(',', $linha['WC_cj_f']);
        $this->arrayNegativaPai = explode(',', $linha['tx_negativa_pai']);
        $this->arrayNegativaFilho = explode(',', $linha['tx_negativa_filho']);
        $this->arrayTipoPai = explode(',', $linha['tx_tipo_pai']);
        $this->arrayTipoFilho = explode(',', $linha['tx_tipo_filho']);
        $this->arrayParamPai = explode(',', $linha['tx_param_pai']);
        $this->arrayParamFilho = explode(',', $linha['tx_param_filho']);
        $this->arrayTipoParamPai = explode(',', $linha['tx_tipo_param_pai']); // 0- palavra-chave, 1- categoria, 2- preço, 3- porcentagem de desconto
        $this->arrayTipoParamFilho = explode(',', $linha['tx_tipo_param_filho']);
    }
    
    /**
     * Coloca os arrays em ordem
     */
    private function ordernarVariaveis() {
        if(count($this->arrayPai) > 1)
        {
            foreach ($this->arrayPai as $key => $value)
            {
                $tamanhoString[$key] = strlen($this->arrayPai[$key]);
            }
            
            if(count($this->arrayNegativaPai) > 1)
            {
                array_multisort ( 
                                $tamanhoString,
                                SORT_DESC,
                                $this->arrayPai,
                                $this->arrayFilho,
                                $this->arrayNegativaPai,
                                $this->arrayNegativaFilho,
                                $this->arrayTipoPai,
                                $this->arrayTipoFilho,
                                $this->arrayParamPai,
                                $this->arrayParamFilho,
                                $this->arrayTipoParamPai,
                                $this->arrayTipoParamFilho );
            }
            else
            {
                array_multisort ( 
                                $tamanhoString,
                                SORT_DESC,
                                $this->arrayPai,
                                $this->arrayFilho );
            }
        }
    }
    
    /**
     * Recupera o valor do check
     */
    private function getIndexValue() {
        $index = -1;
        
        foreach ($this->arrayPai as $key => $value)
        {
            if($this->arrayTipoPai[$key] == '1')
            {
                $pos = strripos($this->widget->getCategoria(), $this->arrayPai[$key]);
                if($pos !== false)
                {
                    if($this->arrayNegativaPai[$key] != '' && $this->arrayNegativaPai[$key] != NULL)
                    {
                        $pos = strripos($this->widget->getCategoria(), $this->arrayNegativaPai[$key]);
                        if($pos === false)
                        {
                            if($this->arrayTipoParamPai[$key] == '0')
                            {
                                $pos = strripos($this->prodDados['XML_titulo'], $this->arrayParamPai[$key]);
                                if($pos !== false)
                                {
                                    return $key;
                                }
                            }
                            else if($this->arrayTipoParamPai[$key] == '1')
                            {
                                $pos = strripos($this->widget->getCategoria(), $this->arrayParamPai[$key]);
                                if($pos !== false)
                                {
                                    return $key;
                                }
                            }
                            else if($this->arrayTipoParamPai[$key] == '2')
                            {
                                if($this->prodDados['XML_sale_price'] == '' || $this->prodDados['XML_sale_price'] == '0.00' || $this->prodDados['XML_sale_price'] == NULL)
                                {
                                    $this->prodDados['XML_sale_price'] = $priceProd;
                                }
                                
                                $pos = strripos($this->arrayParamPai[$key], "<");
                                if($pos !== false)
                                {
                                    $this->arrayParamPai[$key] = substr($this->arrayParamPai[$key], 1);
                                    
                                    if(floatval($this->prodDados['XML_sale_price']) < floatval($this->arrayParamPai[$key]))
                                    {
                                        return $key;
                                    }
                                }
                                else
                                {
                                    $this->arrayParamPai[$key] = substr($this->arrayParamPai[$key], 1);
                                    
                                    if(floatval($this->prodDados['XML_sale_price']) > floatval($this->arrayParamPai[$key]))
                                    {
                                        return $key;
                                    }
                                }
                            }
                            else if($this->arrayTipoParamPai[$key] == '3')
                            {
                                $pos = strripos($this->arrayParamPai[$key], "<");
                                if($pos !== false)
                                {
                                    $this->arrayParamPai[$key] = substr($this->arrayParamPai[$key], 1);
                                    
                                    if(intval($this->prodDados['XML_desconto']) < intval($this->arrayParamPai[$key]))
                                    {
                                        return $key;
                                    }
                                }
                                else
                                {
                                    $this->arrayParamPai[$key] = substr($this->arrayParamPai[$key], 1);
                                    
                                    if(intval($this->prodDados['XML_desconto']) > intval($this->arrayParamPai[$key]))
                                    {
                                        return $key;
                                    }
                                }
                            }
                            
                            return $key;
                        }
                    }
                    else
                    {
                        if($this->arrayTipoParamPai[$key] == '0')
                        {
                            $pos = strripos($this->prodDados['XML_titulo'], $this->arrayParamPai[$key]);
                            if($pos !== false)
                            {
                                return $key;
                            }
                        }
                        else if($this->arrayTipoParamPai[$key] == '1')
                        {
                            $pos = strripos($this->widget->getCategoria(), $this->arrayParamPai[$key]);
                            if($pos !== false)
                            {
                                return $key;
                            }
                        }
                        else if($this->arrayTipoParamPai[$key] == '2')
                        {
                            if($this->prodDados['XML_sale_price'] == '' || $this->prodDados['XML_sale_price'] == '0.00' || $this->prodDados['XML_sale_price'] == NULL)
                            {
                                $this->prodDados['XML_sale_price'] = $priceProd;
                            }
                            
                            $pos = strripos($this->arrayParamPai[$key], "<");
                            if($pos !== false)
                            {
                                $this->arrayParamPai[$key] = substr($this->arrayParamPai[$key], 1);
                                
                                if(floatval($this->prodDados['XML_sale_price']) < floatval($this->arrayParamPai[$key]))
                                {
                                    return $key;
                                }
                            }
                            else
                            {
                                $this->arrayParamPai[$key] = substr($this->arrayParamPai[$key], 1);
                                
                                if(floatval($this->prodDados['XML_sale_price']) > floatval($this->arrayParamPai[$key]))
                                {
                                    return $key;
                                }
                            }
                        }
                        else if($this->arrayTipoParamPai[$key] == '3')
                        {
                            $pos = strripos($this->arrayParamPai[$key], "<");
                            if($pos !== false)
                            {
                                $this->arrayParamPai[$key] = substr($this->arrayParamPai[$key], 1);
                                
                                if(intval($this->prodDados['XML_desconto']) < intval($this->arrayParamPai[$key]))
                                {
                                    return $key;
                                }
                            }
                            else
                            {
                                $this->arrayParamPai[$key] = substr($this->arrayParamPai[$key], 1);
                                
                                if(intval($this->prodDados['XML_desconto']) > intval($this->arrayParamPai[$key]))
                                {
                                    return $key;
                                }
                            }
                        }
                        
                        return $key;
                    }
                }
            }
            else
            {
                $pos = @strripos($this->prodDados['XML_titulo'], $this->arrayPai[$key]);
                if($pos !== false)
                {
                    if($this->arrayNegativaPai[$key] != '' && $this->arrayNegativaPai[$key] != NULL)
                    {
                        $pos = strripos($this->prodDados['XML_titulo'], $this->arrayNegativaPai[$key]);
                        if($pos === false)
                        {
                            if($this->arrayTipoParamPai[$key] == '0')
                            {
                                $pos = strripos($this->prodDados['XML_titulo'], $this->arrayParamPai[$key]);
                                if($pos !== false)
                                {
                                    return $key;
                                }
                            }
                            else if($this->arrayTipoParamPai[$key] == '1')
                            {
                                $pos = strripos($this->widget->getCategoria(), $this->arrayParamPai[$key]);
                                if($pos !== false)
                                {
                                    return $key;
                                }
                            }
                            else if($this->arrayTipoParamPai[$key] == '2')
                            {
                                if($this->prodDados['XML_sale_price'] == '' || $this->prodDados['XML_sale_price'] == '0.00' || $this->prodDados['XML_sale_price'] == NULL)
                                {
                                    $this->prodDados['XML_sale_price'] = $priceProd;
                                }
                                
                                $pos = strripos($this->arrayParamPai[$key], "<");
                                if($pos !== false)
                                {
                                    $this->arrayParamPai[$key] = substr($this->arrayParamPai[$key], 1);
                                    
                                    if(floatval($this->prodDados['XML_sale_price']) < floatval($this->arrayParamPai[$key]))
                                    {
                                        return $key;
                                    }
                                }
                                else
                                {
                                    $this->arrayParamPai[$key] = substr($this->arrayParamPai[$key], 1);
                                    
                                    if(floatval($this->prodDados['XML_sale_price']) > floatval($this->arrayParamPai[$key]))
                                    {
                                        return $key;
                                    }
                                }
                            }
                            else if($this->arrayTipoParamPai[$key] == '3')
                            {
                                $pos = strripos($this->arrayParamPai[$key], "<");
                                if($pos !== false)
                                {
                                    $this->arrayParamPai[$key] = substr($this->arrayParamPai[$key], 1);
                                    
                                    if(intval($this->prodDados['XML_desconto']) < intval($this->arrayParamPai[$key]))
                                    {
                                        return $key;
                                    }
                                }
                                else
                                {
                                    $this->arrayParamPai[$key] = substr($this->arrayParamPai[$key], 1);
                                    
                                    if(intval($this->prodDados['XML_desconto']) > intval($this->arrayParamPai[$key]))
                                    {
                                        return $key;
                                    }
                                }
                            }
                            return $key;
                        }
                    }
                    else
                    {
                        if($this->arrayTipoParamPai[$key] == '0')
                        {
                            $pos = strripos($this->prodDados['XML_titulo'], $this->arrayParamPai[$key]);
                            if($pos !== false)
                            {
                                return $key;
                            }
                        }
                        else if($this->arrayTipoParamPai[$key] == '1')
                        {
                            $pos = strripos($this->widget->getCategoria(), $this->arrayParamPai[$key]);
                            if($pos !== false)
                            {
                                return $key;
                            }
                        }
                        else if($this->arrayTipoParamPai[$key] == '2')
                        {
                            if($this->prodDados['XML_sale_price'] == '' || $this->prodDados['XML_sale_price'] == '0.00' || $this->prodDados['XML_sale_price'] == NULL)
                            {
                                $this->prodDados['XML_sale_price'] = $priceProd;
                            }
                            
                            $pos = strripos($this->arrayParamPai[$key], "<");
                            if($pos !== false)
                            {
                                $this->arrayParamPai[$key] = substr($this->arrayParamPai[$key], 1);
                                
                                if(floatval($this->prodDados['XML_sale_price']) < floatval($this->arrayParamPai[$key]))
                                {
                                    return $key;
                                }
                            }
                            else
                            {
                                $this->arrayParamPai[$key] = substr($this->arrayParamPai[$key], 1);
                                
                                if(floatval($this->prodDados['XML_sale_price']) > floatval($this->arrayParamPai[$key]))
                                {
                                    return $key;
                                }
                            }
                        }
                        else if($this->arrayTipoParamPai[$key] == '3')
                        {
                            $pos = strripos($this->arrayParamPai[$key], "<");
                            if($pos !== false)
                            {
                                $this->arrayParamPai[$key] = substr($this->arrayParamPai[$key], 1);
                                
                                if(intval($this->prodDados['XML_desconto']) < intval($this->arrayParamPai[$key]))
                                {
                                    return $key;
                                }
                            }
                            else
                            {
                                $this->arrayParamPai[$key] = substr($this->arrayParamPai[$key], 1);
                                
                                if(intval($this->prodDados['XML_desconto']) > intval($this->arrayParamPai[$key]))
                                {
                                    return $key;
                                }
                            }
                        }
                        
                        return $key;
                    }
                }
            }
        }
        
        return $index;
    }
    
}
?>
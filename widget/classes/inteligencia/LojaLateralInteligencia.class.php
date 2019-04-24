<?php

namespace roihero\widget\inteligencia;

/**
 * 
 * @author Nolasco
 */

class LojaLateralInteligencia extends AbstractInteligencia {
	/**
     * Processamento, referente a esta inteligência
     * 
     * {@inheritDoc}
     * @see \roihero\widget\inteligencia\IInteligencia::processar()
     */
    public function processar() {
        
        // Recomendações
        $idprod = $this->widget->getCookieProd();
        if(!empty($idprod) && $idprod != null){
            $this->executarSmartHome(0,8, 1, $idprod);
        }else{
            $this->executarMaisVendidos(0,8);
        }
        
        // Histórico
        $this->executarRemarketingOnSite(8, 8);
        

        // Melhores ofertas
        $this->executarLiquidacao(16, 8);
        
    }
    
    //INICIO PRIMEIRA VITRINE
    private function executarSmartHome($posicao=0, $maxProd=24, $sum=1, $idprod){
        $idprod = explode(',', $idprod);
        $cookieDate = explode(',', $this->widget->getCookieData());

        array_multisort($cookieDate, SORT_DESC, $idprod);

        foreach ($idprod as $key => $value) 
        {
            $this->executarComplementar($value, $this->widget->getObjNumItens(), 4 - $this->widget->getObjNumItens());

            if($this->widget->getObjNumItens() < 4)
            {
                $this->executarSimilar($value, $this->widget->getObjNumItens(), 1);

                if($this->widget->getObjNumItens() >= 4)
                {
                    break;
                }
            }
            else
            {
                break;
            }
        }

        if($this->widget->getObjNumItens() < 4)
        {
            foreach ($idprod as $key => $value) 
            {
                $this->executarCategoria($value, $this->widget->getObjNumItens(), 4 - $this->widget->getObjNumItens());

                if($this->widget->getObjNumItens() >= 4)
                {
                    break;
                }
            }                
        }

        $obj = $this->widget->getObj();
        $qtdObj = count($obj);
        if($qtdObj < $maxProd){
            $numProdCat = $this->executarCategoria($obj[$x]['id'], $qtdObj, $maxProd - $qtdObj);
        }

        $obj = $this->widget->getObj();
        $qtdObj = count($obj);
        if($qtdObj < $maxProd){
            $this->executarMaisClicados($qtdObj, $maxProd - $qtdObj) ;                           
        }
    }

    /**
     * Este método, cria uma instância da classe ProdutoSimilarInteligencia
     * e realiza a adaptação da mesma para a execução do seu processamento.
     */
    private function executarComplementar($produto, $posicao=0, $maxProd=24, $sum=1) {
        
        $select = 'SELECT xml_compra_complementar FROM XML_' . $this->widget->getIdCli() . '
                   WHERE XML_id = \'' . $produto . '\'';
        
        $result = mysqli_query($this->widget->getConDados(), $select);
        $linha = mysqli_fetch_array($result);
        
        $arrayProd = $linha['xml_compra_complementar'];
        
        $select = "SELECT " . $this->XML_select .  "
                   FROM XML_".$this->widget->getIdCli()."
                   WHERE XML_id IN (" . $arrayProd . ')
                   AND XML_availability = 1';
        
        $result = mysqli_query($this->widget->getConDados(), $select);
        
        $numProd = 0;
        if($result && mysqli_num_rows ($result) > 0 )
        {
            while($linha = mysqli_fetch_array($result))
            {
                //setOBJ($idWid, $array, $utm, $index=null)
                if($this->setOBJ($this->widget->getIdWid(), $linha, $this->widgetProps['WID_utm'], $posicao))
                {
                    $numProd++;
                    $posicao += $sum;

                    if($numProd >= $maxProd)
                    {
                        break;
                    }
                }
            }
        }

        return $numProd;
    }

     /**
     * Este método, cria uma instância da classe ProdutoSimilarInteligencia
     * e realiza a adaptação da mesma para a execução do seu processamento.
     */
    private function executarSimilar($produto, $posicao=0, $maxProd=24, $sum=1) {
        
        $prodNome = $this->getProdNameManual($produto);
        
        if($prodNome != '')
        {
            $numProd = 0;
            $prodNome = strtoupper($prodNome);
            $prodNome = str_replace(' ', ' +', $prodNome);
            
            while (!empty($prodNome) && $numProd <= $maxProd) {
                
                // Recuperando a posição do último espaço
                $ultEspaco = strrpos($prodNome, " ");
                // Limitando a string até a posição do último espaço
                $prodNome = substr($prodNome, 0, $ultEspaco);
                
                $select = "SELECT ".$this->XML_select."
                           FROM XML_".$this->widget->getIdCli()."
                           WHERE XML_availability = 1
                           AND XML_id != ". $produto ."
                           AND MATCH(XML_titulo) AGAINST(\"+" . $prodNome . "\" IN BOOLEAN MODE)
                           GROUP BY XML_link
                           LIMIT 24";
                
                $result = mysqli_query($this->widget->getConDados(), $select);
        
                if($result && mysqli_num_rows ($result) > 0 )
                {
                    while($linha = mysqli_fetch_array($result))
                    {
                        if($this->setOBJ($this->widget->getIdWid(), $linha, $this->widgetProps['WID_utm'], $posicao)) {
                            $numProd ++;
                            $posicao += $sum;

                            if($numProd >= $maxProd) {
                                break;
                            }
                        }
                    }
                }

                if($numProd >= $maxProd) {
                    break;
                }
            }
        }
        else
        {
            return 0;
        }

        return $numProd;
    }
    /**
     * Este método, cria uma instância da classe ProdutoSimilarInteligencia
     * e realiza a adaptação da mesma para a execução do seu processamento.
     */
    private function executarCategoria($produto, $posicao=0, $maxProd=24, $sum=1) {
        
        $categoria = $this->getProdTypeManual($produto);

        if($categoria != '')
        {
            $select = "SELECT ".$this->XML_select." FROM XML_". $this->widget->getIdCli() ." 
                        WHERE XML_availability = 1
                        AND XML_type_upper = '". strtoupper($categoria) ."' 
                        GROUP BY XML_link
                        ORDER BY XML_click_". $this->widgetProps['WID_dias'] ."
                        DESC LIMIT 24";
            $result = mysqli_query($this->widget->getConDados(), $select);
        
            $numProd = 0;
            if($result && mysqli_num_rows ($result) > 0 )
            {
                while($linha = mysqli_fetch_array($result))
                {
                    //setOBJ($idWid, $array, $utm, $index=null)
                    if($this->setOBJ($this->widget->getIdWid(), $linha, $this->widgetProps['WID_utm'], $posicao))
                    {
                        $numProd++;
                        $posicao += $sum;

                        if($numProd >= $maxProd)
                        {
                            break;
                        }
                    }
                }
            }
        }
        else
        {
            return 0;
        }       

        return $numProd;
    }
    /**
     * Este método, cria uma instância da classe ProdutoSimilarInteligencia
     * e realiza a adaptação da mesma para a execução do seu processamento.
     */
    private function executarMaisClicados($posicao=0, $maxProd=24, $sum=1) {
        
        $select = "SELECT ".$this->XML_select." FROM XML_" . $this->widget->getIdCli() . "
                   WHERE XML_availability = 1
                   GROUP BY XML_link ORDER BY XML_click_" . $this->widgetProps['WID_dias'] . " DESC
                   LIMIT 24";
        
        $result = mysqli_query($this->widget->getConDados(), $select);
        
        $numProd = 0;
        if($result && mysqli_num_rows ($result) > 0 )
        {
            while($linha = mysqli_fetch_array($result))
            {
                //setOBJ($idWid, $array, $utm, $index=null)
                if($this->setOBJ($this->widget->getIdWid(), $linha, $this->widgetProps['WID_utm'], $posicao))
                {
                    $numProd++;
                    $posicao += $sum;

                    if($numProd >= $maxProd)
                    {
                        break;
                    }
                }
            }
        }

        return $numProd;
    }

    /**
     * Este método, cria uma instância da classe MaisVendidosInteligencia
     * e realiza a adaptação da mesma para a execução do seu processamento.
     */
    private function executarMaisVendidos($posicao=0, $maxProd=24, $sum=1){
            $select = "SELECT ".$this->XML_select."
                       FROM XML_".$this->widget->getIdCli()."
                       WHERE XML_availability = 1
                       AND  XML_id != '" . $this->widget->getProdId() ."'
                       GROUP BY XML_link ORDER BY XML_venda_".$this->widgetProps['WID_dias']." DESC
                       LIMIT " . $this->numMaxProdutos;
            
            $result = mysqli_query($this->widget->getConDados(), $select);
        
            $numProd = 0;
            if($result && mysqli_num_rows ($result) > 0 )
            {
                while($linha = mysqli_fetch_array($result))
                {
                    //setOBJ($idWid, $array, $utm, $index=null)
                    if($this->setOBJ($this->widget->getIdWid(), $linha, $this->widgetProps['WID_utm'], $posicao))
                    {
                        $numProd++;
                        $posicao += $sum;

                        if($numProd >= $maxProd)
                        {
                            break;
                        }
                    }
                }
            }

            return $numProd;
    }
    //FIM PRIMEIRA VITRINE


    //INICIO SEGUNDA VITRINE
    /**
     * Recupera os produtos do histórico do usuário
     * 
     * @param number $posicao
     * @param number $maxProd
     * @param number $sum
     * @return number
     */
    private function executarRemarketingOnSite($posicao=0, $maxProd=24, $sum=1) {
        
        $select = "SELECT ".$this->XML_select." FROM XML_".$this->widget->getIdCli()."
                   WHERE XML_id IN (" . $this->widget->getCookieProd() . ")
                   ORDER BY XML_click_7 DESC
                   LIMIT " . $maxProd;
        
        $result = mysqli_query($this->widget->getConDados(), $select);
        
        $numProd = 0;
        if($result && mysqli_num_rows ($result) > 0 )
        {
            while($linha = mysqli_fetch_array($result))
            {
                //setOBJ($idWid, $array, $utm, $index=null)
                if($this->setOBJ($this->widget->getIdWid(), $linha, $this->widgetProps['WID_utm'], $posicao))
                {
                    $numProd++;
                    $posicao += $sum;
                    
                    if($numProd >= $maxProd)
                    {
                        break;
                    }
                }
            }
        }
        
        return $numProd;
    }
    //FIM SEGUNDA VITRINE


    //INICIO TERCEIRA VITRINE
    private function executarLiquidacao($posicao=0, $maxProd=24, $sum=1) {
        
        $select = "SELECT ".$this->XML_select." FROM XML_".$this->widget->getIdCli()."
                   WHERE XML_availability = 1 AND XML_id != '" . $this->widget->getProdId() . "'
                   ORDER BY XML_desconto DESC
                   LIMIT " . $maxProd;
        
        $result = mysqli_query($this->widget->getConDados(), $select);
        
        $numProd = 0;
        if($result && mysqli_num_rows ($result) > 0 )
        {
            while($linha = mysqli_fetch_array($result))
            {
                //setOBJ($idWid, $array, $utm, $index=null)
                if($this->setOBJ($this->widget->getIdWid(), $linha, $this->widgetProps['WID_utm'], $posicao))
                {
                    $numProd++;
                    $posicao += $sum;
                    
                    if($numProd >= $maxProd)
                    {
                        break;
                    }
                }
            }
        }
        
        return $numProd;
    }
    //FIM TERCEIRA VITRINE
}
    
?>
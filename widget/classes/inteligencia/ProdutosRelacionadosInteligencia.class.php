<?php
namespace roihero\widget\inteligencia;

class ProdutosRelacionadosInteligencia extends AbstractInteligencia {
    
    private $tamanhoString = [];
    private $arrayPai = [];
    private $arrayFilho = [];
    private $arrayTipoPai = [];
    private $arrayTipoFilho = [];
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
                
                if($this->arrayTipoFilho[$index] == '1')
                {                
                    $select = "SELECT " . $this->XML_select . "
                               FROM XML_".$this->widget->getIdCli()."
                               WHERE XML_link != '" . $this->widget->getUrl() . "'
                               AND XML_availability = '1'".
                               $this->getRangePrice() ."
                               AND MATCH(XML_type_upper) AGAINST(\"+ " . $this->arrayFilho[$index] ."\" IN BOOLEAN MODE) 
                               GROUP BY XML_link
                               ORDER BY XML_click_" . $this->widgetProps['WID_dias'] . " DESC
                               LIMIT " . $this->numMaxProdutos;
                    
                    $this->executeSelect($select);
                }
                else
                {
                    $select = "SELECT " . $this->XML_select . "
                               FROM XML_".$this->widget->getIdCli()."
                               WHERE XML_link != '" . $this->widget->getUrl() . "'
                               AND XML_availability = '1'".
                               $this->getRangePrice() ."
                               AND MATCH(XML_titulo_upper) AGAINST(\"+ " . $this->arrayFilho[$index] ."\" IN BOOLEAN MODE) 
                               GROUP BY XML_link
                               ORDER BY XML_click_" . $this->widgetProps['WID_dias'] . " DESC
                               LIMIT " . $this->numMaxProdutos;
                    
                    $this->executeSelect($select);
                }
                
            }
        }
    }
    
    /**
     * Popula as variáveis usada em todo o código
     */
    private function popularVariaveis() {
        
        $this->prodDados = $this->getProdDados();
        
        $selectWidgetConfig = "SELECT 
                               WC_cj_f,
                               WC_cj_p,
                               tx_tipo_pai,
                               tx_tipo_filho
                               FROM widget_config WHERE WC_id_wid = ". $this->widget->getIdWid();
        
        $result = mysqli_query($this->widget->getConCad(), $selectWidgetConfig);
        
        $linha = mysqli_fetch_array($result);
        
        $this->arrayPai = explode(',', $linha['WC_cj_p']);
        $this->arrayFilho = explode(',', $linha['WC_cj_f']);
        $this->arrayTipoPai = explode(',', $linha['tx_tipo_pai']);
        $this->arrayTipoFilho = explode(',', $linha['tx_tipo_filho']);
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

            array_multisort ( 
                            $tamanhoString,
                            SORT_DESC,
                            $this->arrayPai,
                            $this->arrayFilho,
                            $this->arrayTipoPai,
                            $this->arrayTipoFilho );
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
                    return $key;
                }
            }
            else
            {
                $pos = strripos($this->prodDados['XML_titulo'], $this->arrayPai[$key]);
                if($pos !== false)
                {                    
                    return $key;
                }
            }
        }
        
        return $index;
    }
    
}
?>
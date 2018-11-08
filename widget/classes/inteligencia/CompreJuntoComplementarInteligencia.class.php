<?php
namespace roihero\widget\inteligencia;

class CompreJuntoComplementarInteligencia extends AbstractInteligencia {
    
    /**
     * Processamento, referente a esta inteligência
     *
     * {@inheritDoc}
     * @see \roihero\widget\inteligencia\IInteligencia::processar()
     */
    public function processar() {
        
        // Adicionando um produto do cookie
        if($this->widget->getProdId() != 'undefined') {
            
            // Produto que o usuário está visualizando
            $select = 'SELECT ' . $this->XML_select . '
                       FROM XML_'.$this->widget->getIdCli().'
                       WHERE XML_id = ' . $this->widget->getProdId();
            
            // Se adicionou um item, então realiza o restante do processamento
            // Zero, para adicionar no inicio
            if($this->rComplementarExecuteSelect($select, 0)) {
                
                $select = 'SELECT xml_compra_complementar FROM XML_' . $this->widget->getIdCli() . '
                           WHERE XML_id = \'' . $this->widget->getProdId() . '\'';
                
                $result = mysqli_query($this->widget->getConDados(), $select);
                
                $linha = mysqli_fetch_array($result);

                if(empty($linha['xml_compra_complementar']))
                {
                     $this->widget->setObj(array());
                }
                else
                {                
                    $arrayProd = explode(',', $linha['xml_compra_complementar']);
                    
                    // O layout possui 4 posições
                    if(count($arrayProd) > 1) {
                        for($i = 0; $i < count($arrayProd); $i++) {
                            
                            $select = "SELECT " . $this->XML_select .  "
                                       FROM XML_".$this->widget->getIdCli()."
                                       WHERE XML_id = '".$arrayProd[$i]. '\'
                                       AND XML_availability = 1';
                            
                            $this->rComplementarExecuteSelect($select, $i + 1);
                        }
                    }
                    else
                    {
                        $this->widget->setObj(array());
                    }

                    if($this->widget->getObjNumItens() < 3)
                    {
                        $this->widget->setObj(array());
                    }
                }
            }
        }
    }
    
    /**
     * Executa o select básico
     *
     * @param string $select
     */
    protected function rComplementarExecuteSelect($select, $posicao) {
        $result = mysqli_query($this->widget->getConDados(), $select);
        
        $i = 0;
        if($result && mysqli_num_rows ($result) > 0 )
        {
            while($linha = mysqli_fetch_array($result))
            {
                if($this->setOBJ($this->widget->getIdWid(), $linha, $this->widgetProps['WID_utm'], $posicao))
                {
                    $i++;
                }
            }
        }
        
        return $i;
    }
    
    
}
?>
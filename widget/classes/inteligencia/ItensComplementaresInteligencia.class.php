<?php
namespace roihero\widget\inteligencia;

/**
 * Compras complementares.
 * Quem comprou esse também comprou esse.
 *  
 * @author tiago
 */
class ItensComplementaresInteligencia extends AbstractInteligencia {
    
    /**
     *
     * {@inheritDoc}
     * @see \roihero\widget\inteligencia\IInteligencia::processar()
     */
    public function processar() {
        
        $select = 'SELECT xml_compra_complementar FROM XML_' . $this->widget->getIdCli() . '
                   WHERE XML_id = \'' . $this->widget->getProdId() . '\'';
        
        $result = mysqli_query($this->widget->getConDados(), $select);
        $linha = mysqli_fetch_array($result);
        
        $arrayProd = $linha['xml_compra_complementar'];
        
        $select = "SELECT " . $this->XML_select .  "
                   FROM XML_".$this->widget->getIdCli()."
                   WHERE XML_id IN (" . $arrayProd . ')
                   AND XML_availability = 1'. $this->getRangePrice();
        
        $this->executeSelect($select);
    }
    
}
?>
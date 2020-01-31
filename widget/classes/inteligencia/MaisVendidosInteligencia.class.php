<?php
namespace roihero\widget\inteligencia;

class MaisVendidosInteligencia extends AbstractInteligencia {
    
    /**
     * Mais vendidos
     * 
     * {@inheritDoc}
     * @see \roihero\widget\inteligencia\IInteligencia::processar()
     */
    public function processar() {
        $select = "SELECT ".$this->XML_select."
                   FROM XML_".$this->widget->getIdCli()."
                   WHERE XML_availability = 1
                   AND  XML_id != '" . $this->widget->getProdId() ."'".
                   $this->getRangePrice() . "
                   GROUP BY XML_link ORDER BY XML_venda_".$this->widgetProps['WID_dias']." DESC
                   LIMIT " . $this->numMaxProdutos;
        
        $this->executeSelect($select);
    }
    
}
?>
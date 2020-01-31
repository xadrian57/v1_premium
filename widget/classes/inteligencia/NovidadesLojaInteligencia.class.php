<?php
namespace roihero\widget\inteligencia;

class NovidadesLojaInteligencia extends AbstractInteligencia {
    
    /**
     * 
     * {@inheritDoc}
     * @see \roihero\widget\inteligencia\IInteligencia::processar()
     */
    public function processar() {
        
        $select = "SELECT ".$this->XML_select." FROM XML_" . $this->widget->getIdCli() . "
                   WHERE XML_availability = 1 " . 
                   $this->getRangePrice() . "
                   AND XML_id != '" . $this->widget->getProdId() . "'
                   GROUP BY XML_link ORDER BY XML_time_insert DESC
                   LIMIT " . $this->numMaxProdutos;
        
        $this->executeSelect($select);
        
    }
    
}
?>
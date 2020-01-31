<?php
namespace roihero\widget\inteligencia;

class MaisClicadosInteligencia extends AbstractInteligencia {

    /**
     * Processamento, referente a esta inteligência
     * 
     * {@inheritDoc}
     * @see \roihero\widget\inteligencia\IInteligencia::processar()
     */
    public function processar() {
        
        if($this->widget->getIdCli() == '756')
        {
           $paramOrder = "XML_custom5";
        }
        else
        {
           $paramOrder = "XML_click_" . $this->widgetProps['WID_dias'];
        }

        $select = "SELECT ".$this->XML_select." FROM XML_" . $this->widget->getIdCli() . "
                   WHERE XML_availability = 1 AND XML_id != '" . $this->widget->getProdId() . "'" . 
                   $this->getRangePrice() . "
                   GROUP BY XML_link ORDER BY " . $paramOrder . " DESC
                   LIMIT " . $this->numMaxProdutos;
        
        $this->executeSelect($select);
    }
}
?>
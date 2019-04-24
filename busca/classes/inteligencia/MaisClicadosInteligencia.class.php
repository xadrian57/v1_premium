<?php
namespace roihero\search\inteligencia;

class MaisClicadosInteligencia extends AbstractInteligencia {

    /**
     * Processamento, referente a esta inteligência
     * 
     * {@inheritDoc}
     * @see \roihero\widget\inteligencia\IInteligencia::processar()
     */
    public function processar() {

        $select = "SELECT ".$this->XML_select." FROM XML_" . $this->search->getIdCli() . "
                   WHERE XML_availability = 1
                   GROUP BY XML_link ORDER BY XML_click_7 DESC
                   LIMIT " . $this->numMaxProdutos;
        
        $this->executeSelect($select);
    }
}
?>
<?php
namespace roihero\widget\inteligencia;

class LiquidacaoInteligencia extends AbstractInteligencia {

	/**
     * Processamento, referente a esta inteligência
     * 
     * {@inheritDoc}
     * @see \roihero\widget\inteligencia\IInteligencia::processar()
     */
    public function processar() {
        
        $select = "SELECT ".$this->XML_select." FROM XML_". $this->widget->getIdCli() ." 
					WHERE XML_id != '". $this->widget->getProdId() ."' AND XML_availability = 1 ". $this->getRangePrice() ."
                    AND XML_desconto != 100 
					GROUP BY XML_link
                   ORDER BY XML_desconto DESC
                   LIMIT " . $this->numMaxProdutos;
        
        $this->executeSelect($select);
    }
}
    
?>
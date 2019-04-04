<?php

namespace roihero\widget\inteligencia;

/**
 * 
 * @author Nolasco
 */

class ScrollCheckoutInteligencia extends AbstractInteligencia {

	/**
     * Processamento, referente a esta inteligência
     * 
     * {@inheritDoc}
     * @see \roihero\widget\inteligencia\IInteligencia::processar()
     */
	public function processar() {

		$select = "SELECT ".$this->XML_select.", XML_views_hour FROM XML_" . $this->widget->getIdCli() . "
                   WHERE XML_id = '" . $this->widget->getProdId() . "';"
        
        $this->executeSelect($select);

	}

}

    
?>
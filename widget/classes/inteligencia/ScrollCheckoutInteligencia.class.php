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

		$select = "SELECT XML_views_hour FROM XML_" . $this->widget->getIdCli() . "
                   WHERE XML_id = '" . $this->widget->getProdId() . "'";

        $result = mysqli_query($this->widget->getConDados(), $select);
        $linha = mysqli_fetch_array($result);


        $views_hour = explode(",", $linha['XML_views_hour']);
        $viewsNow = $views_hour[date('H')];

        $this->widget->setViewsNow($viewsNow);
	}

}

    
?>
<?php
namespace roihero\widget\inteligencia;

class RemarketingOnSiteDinamicoInteligencia extends AbstractInteligencia {
	/**
     * Processamento, referente a esta inteligência
     * 
     * {@inheritDoc}
     * @see \roihero\widget\inteligencia\IInteligencia::processar()
     */
    public function processar() {
        
        $pesquisaProdUnico = explode(',', $this->widget->getOfertaID());

        if($pesquisaProdUnico[0] != "undefined")
        {
            $select = "SELECT ".$this->XML_select." FROM XML_". $this->widget->getIdCli() ." 
            			WHERE XML_id = '".$pesquisaProdUnico[0]."'";

            $this->executeSelect($select);
        }       
        
    }
    
}
?>
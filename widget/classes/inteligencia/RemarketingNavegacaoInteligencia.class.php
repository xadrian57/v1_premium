<?php
namespace roihero\widget\inteligencia;

/**
 * Quem viu esse também viu este outro
 * 
 * @author tiago
 */
class RemarketingNavegacaoInteligencia extends AbstractInteligencia {
    
    /**
     *
     * {@inheritDoc}
     * @see \roihero\widget\inteligencia\IInteligencia::processar()
     */
    public function processar() {
        
        $idProd = $this->getUltimoProdVisto();
        
        if($idProd) {
            
            $select = 'SELECT xml_navegou_complementar FROM XML_' . $this->widget->getIdCli() . ' WHERE XML_id = ' . $idProd;
            
            $result = mysqli_query($this->widget->getConDados(), $select);
            $linha = mysqli_fetch_array($result);
            
            $arrayProd = $linha['xml_navegou_complementar'];
            
            $select = "SELECT " . $this->XML_select . "
                       FROM XML_".$this->widget->getIdCli()."
                       WHERE XML_id IN (".$arrayProd.')
                       AND XML_availability = 1';
            
            $this->executeSelect($select);
        }
    }
    
    /**
     * Recupera do cookie o último produto visto
     * 
     * @return string
     */
    private function getUltimoProdVisto() {
        $ultimoId = '';
        $events = explode(',', $this->widget->getCookieEvent());
        $prods = explode(',', $this->widget->getCookieProd());
        
        for($i = 0; $i < count($events); $i++) {
            if('product' == $events[$i]) {
                
                $ultimoId = $prods[$i];
            }
        }
        
        return $ultimoId;
    }
    
}
?>
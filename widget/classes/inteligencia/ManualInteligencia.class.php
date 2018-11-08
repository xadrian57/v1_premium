<?php
namespace roihero\widget\inteligencia;

/**
 * Esta classe é para realizar um agrupamento
 * de acordo com o que o cliente escolheu no dashboard.
 *  
 * @author tiago
 */
class ManualInteligencia extends AbstractInteligencia {
    
    /**
     *
     * {@inheritDoc}
     * @see \roihero\widget\inteligencia\IInteligencia::processar()
     */
    public function processar() {
        
        $select = 'SELECT WC_id_produto
                   FROM widget_config
                   WHERE WC_id_wid = ' . $this->widget->getIdWid();
        
        $result = mysqli_query($this->widget->getConCad(), $select);
        $linha = mysqli_fetch_array($result);
        
        $arrayProd = explode(',', $linha['WC_id_produto']);
        
        for($i = 0; $i < count($arrayProd); $i++) {
            
            $select = "SELECT " . $this->XML_select .  "
                       FROM XML_".$this->widget->getIdCli()."
                       WHERE XML_id = '".$arrayProd[$i]."'";
            
            $this->executeSelect($select);
        }
    }
    
}
?>
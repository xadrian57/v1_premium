<?php
namespace roihero\widget\inteligencia;

/**
 * Esta classe, realiza o processamento para retornar 
 * os mais vendidos de uma categoria, que foi definica
 * pelo usuário do dashboard
 * 
 * @author tiago
 */
class MaisVendidosMarcaManualInteligencia extends AbstractInteligencia {
    
    /**
     * 
     * {@inheritDoc}
     * @see \roihero\widget\inteligencia\IInteligencia::processar()
     */
    public function processar() {

        // if($this->widget->getIdCli() == '756')
        // {
        //    $paramOrder = "XML_custom5 DESC, XML_click_" . $this->widgetProps['WID_dias'];
        // }
        // else
        // {
           $paramOrder = "XML_click_" . $this->widgetProps['WID_dias'];
        // }
        
        $select = 'SELECT WC_marca FROM widget_config WHERE WC_id_wid = ' . $this->widget->getIdWid();  
        
        $result = mysqli_query($this->widget->getConCad(), $select);
        $linha = mysqli_fetch_array($result);

        $marca = str_replace(' ', ' +', $linha['WC_marca']);
        
        $select = "SELECT ".$this->XML_select."
                   FROM XML_". $this->widget->getIdCli() ."
                   WHERE XML_availability = 1
                   AND MATCH(XML_brand) AGAINST(\"+". $marca ."\" IN BOOLEAN MODE)
                   GROUP BY XML_link
                   ORDER BY ". $paramOrder ." DESC 
                   LIMIT " . $this->numMaxProdutos;
        
        $this->executeSelect($select);
    }

}
?>
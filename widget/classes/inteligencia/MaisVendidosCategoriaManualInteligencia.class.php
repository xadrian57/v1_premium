<?php
namespace roihero\widget\inteligencia;

/**
 * Esta classe, realiza o processamento para retornar 
 * os mais vendidos de uma categoria, que foi definica
 * pelo usuário do dashboard
 * 
 * @author tiago
 */
class MaisVendidosCategoriaManualInteligencia extends AbstractInteligencia {
    
    /**
     * 
     * {@inheritDoc}
     * @see \roihero\widget\inteligencia\IInteligencia::processar()
     */
    public function processar() {
        
        $select = 'SELECT WC_categoria FROM widget_config WHERE WC_id_wid = ' . $this->widget->getIdWid();  
        
        $result = mysqli_query($this->widget->getConCad(), $select);
        $linha = mysqli_fetch_array($result);

        $cat = str_replace(' ', ' +', $linha['WC_categoria']);
        
        $select = "SELECT ".$this->XML_select."
                   FROM XML_". $this->widget->getIdCli() ."
                   WHERE XML_availability = 1
                   AND MATCH(XML_type_upper) AGAINST(\"+".  strtoupper($cat) ."\" IN BOOLEAN MODE)
                   GROUP BY XML_link
                   ORDER BY XML_venda_". $this->widgetProps['WID_dias'] ."
                   DESC LIMIT " . $this->numMaxProdutos;
        
        $this->executeSelect($select);
    }

}
?>
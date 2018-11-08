<?php
namespace roihero\widget\inteligencia;

/**
 * Esta classe, realiza o processamento para retornar 
 * os mais vendidos de uma categoria, que foi definica
 * pelo usuário do dashboard
 * 
 * @author tiago
 */
class PalavraChaveInteligencia extends AbstractInteligencia {
    
    /**
     * 
     * {@inheritDoc}
     * @see \roihero\widget\inteligencia\IInteligencia::processar()
     */
    public function processar() {
        
        $select = 'SELECT WC_collection FROM widget_config WHERE WC_id_wid = ' . $this->widget->getIdWid();
        
        $result = mysqli_query($this->widget->getConCad(), $select);
        $linha = mysqli_fetch_array($result);
        
        $select = "SELECT ".$this->XML_select."
                   FROM XML_". $this->widget->getIdCli() ."
                   WHERE XML_availability = 1
                   AND MATCH(XML_titulo) AGAINST(\"".  strtoupper($linha['WC_collection']) ."\" IN BOOLEAN MODE)
                   GROUP BY XML_link
                   ORDER BY XML_venda_". $this->widgetProps['WID_dias'] ."
                   DESC LIMIT " . $this->numMaxProdutos;
        
        $this->executeSelect($select);
    }

}
?>
<?php
namespace roihero\widget\inteligencia;

class MaisVendidosCategoriaInteligencia extends AbstractInteligencia {
    
    /**
     * Processamento, referente a esta inteligência
     * 
     * {@inheritDoc}
     * @see \roihero\widget\inteligencia\IInteligencia::processar()
     */
    public function processar() {
        
       if($this->widget->getCategoria() != 'undefined')
       {
            $cat = str_replace(' ', ' +', $this->widget->getCategoria());
            $cat = '+' . $cat;

            // Adicionando
            $select = "SELECT ".$this->XML_select." FROM XML_". $this->widget->getIdCli() ." 
            			WHERE XML_availability = 1
                        AND MATCH(XML_type_upper) AGAINST(\"". strtoupper($cat) ."\" IN BOOLEAN MODE) 
            			GROUP BY XML_link
                        ORDER BY XML_venda_". $this->widgetProps['WID_dias'] ."
                        DESC LIMIT " . $this->numMaxProdutos;

            $this->executeSelect($select);
        }
    }
}

?>
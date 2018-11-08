<?php
namespace roihero\widget\inteligencia;

class ProdutoSimilarInteligencia extends AbstractInteligencia {
    
    /**
     * 
     * {@inheritDoc}
     * @see \roihero\widget\inteligencia\IInteligencia::processar()
     */
    public function processar() {
        $prodNome = $this->getProdName();
        
        if($prodNome != '')
        {
            $numProd = 0;
            $prodNome = strtoupper($prodNome);
            $prodNome = str_replace(' ', ' +', $prodNome);
            
            while (!empty($prodNome) && $numProd <= $this->numMaxProdutos) {
                
                // Recuperando a posição do último espaço
                $ultEspaco = strrpos($prodNome, " ");
                // Limitando a string até a posição do último espaço
                $prodNome = substr($prodNome, 0, $ultEspaco);
                
                if($prodNome != '')
                {
                    $select = "SELECT ".$this->XML_select."
                               FROM XML_".$this->widget->getIdCli()."
                               WHERE XML_id != '" . $this->widget->getProdId() . "'
                               AND XML_availability = 1
                               AND MATCH(XML_titulo) AGAINST(\"+" . $prodNome . "\" IN BOOLEAN MODE)
                               GROUP BY XML_link
                               LIMIT " . $this->numMaxProdutos;
                    
                    $result = mysqli_query($this->widget->getConDados(), $select);
            
                    if($result && mysqli_num_rows ($result) > 0 )
                    {
                        while($linha = mysqli_fetch_array($result))
                        {
                            if ($this->setOBJ ( 
                                                $this->widget->getIdWid (),
                                                $linha,
                                                $this->widgetProps ['WID_utm'] )) {
                                $numProd ++;
                                
                                if ($numProd >= $this->numMaxProdutos) {
                                    break;
                                }
                            }
                        }
                    }
                }
                else
                {
                    break;
                }
            }
        }
    }
}
?>
<?php
namespace roihero\widget\inteligencia;

class CarrinhoComplementarInteligencia extends AbstractInteligenciaComposta {
    
    /**
     *
     * {@inheritDoc}
     * @see \roihero\widget\inteligencia\IInteligencia::processar()
     */
    public function processar() {
        // Ids dos produtos cujo evento é carrinho
        $ids = $this->getCartProds();
        
        $select = 'SELECT xml_carrinho_complementar
                   FROM XML_' . $this->widget->getIdCli() . ' WHERE XML_id IN (' . $ids . ')';
        
        $result = mysqli_query($this->widget->getConDados(), $select);

        if($result)
        {
            $linhas = [];
            while($linha = mysqli_fetch_array($result)) {
                $linhas[] = $linha;
            }
            
            for($i = 0; $i < count($linhas); $i++) {
                
                $arrayProd = $linhas[$i]['xml_carrinho_complementar'];
                
                $select = "SELECT " . $this->XML_select .  "
                           FROM XML_".$this->widget->getIdCli()."
                           WHERE XML_id IN (" . $arrayProd . ')
                           AND XML_availability = 1';
                
                $this->executeSelect($select);
            }
            
            $this->ordenarObjetos();

            if($this->widget->getObjNumItens() < 4)
            {
                $obj = [];
                $this->widget->setObj($obj);
            }
        }
    }
    
    private function getCartProds() {
        $ids = '';
        $events = explode(',', $this->widget->getCookieEvent());
        $prods = explode(',', $this->widget->getCookieProd());
        
        for($i = 0; $i < count($events); $i++) {
            if('cart' == $events[$i]) {
                
                if(!empty($ids)) {
                    $ids .= ',';
                }
                
                $ids .= $prods[$i];
            }
        }
        
        return $ids;
    }
    
}
?>
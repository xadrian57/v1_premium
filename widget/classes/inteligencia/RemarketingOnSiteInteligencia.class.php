<?php
namespace roihero\widget\inteligencia;

/**
 * Esta classe, irá recuperar os produtos que o cliente navegou
 * e, retornar estes produtos com as condições:
 * - O produto não pode já ter sido comprado pelo cliente
 * - Ter adicionado ao carrinho pelo menos um dia antes, não menos que isso.
 * 
 * @author tiago
 */
class RemarketingOnSiteInteligencia extends AbstractInteligencia {
    
    private $cart = [];
    
    /**
     * 
     * {@inheritDoc}
     * @see \roihero\widget\inteligencia\IInteligencia::processar()
     */
    public function processar() {
        
        $date = explode(',', $this->widget->getCookieData());
        $idprod = explode(',', $this->widget->getCookieProd());
        $evento = explode(',', $this->widget->getCookieEvent());
        
        // Limpando os produtos indesejáveis
        $this->getArrayLimpoDeProdutos($date, $idprod, $evento);
        
        $cont = count($this->cart);

        if(count($idprod) > 3 || (count($idprod) > 2 && count($cart) > 0) || (count($idprod) > 1 && count($cart) > 1))
        {

            $numprod = 0;
            
            if($cont > 0)
            {
                if($cont == 1)
                {
                    $select = "SELECT ".$this->XML_select."
                               FROM XML_".$this->widget->getIdCli()."
                               WHERE XML_availability = 1
                               AND XML_id = '".$this->cart[0]."'" ;
                    
                    $result = mysqli_query($this->widget->getConDados(), $select);
                    
                    if(mysqli_num_rows ($result) > 0)
                    {
                        $linha = mysqli_fetch_array($result);
                        $this->setOBJ($this->widget->getIdWid(), $linha, $this->widgetProps['WID_utm'], 0);

                        $numprod++;
                    }
                }
                else
                {
                    // Adicionando os dois primeiros itens que o cliente
                    // em algum momento adicionou ao carrinho
                    for($i = 0; $i < 2; $i++)
                    {
                        $select = "SELECT ".$this->XML_select."
                                   FROM XML_".$this->widget->getIdCli()."
                                   WHERE XML_availability = 1
                                   AND XML_id = '".$cart[$i]."'" ;
                        
                        $this->executeSelect($select);

                        $numprod++;
                    }
                }
                
            }                

            $cont = 0;

            for($i = $numprod; ($i < $this->numMaxProdutos) && ($i < count($idprod)); $i++)
            {
                $select = "SELECT ".$this->XML_select."
                           FROM XML_".$this->widget->getIdCli()."
                           WHERE XML_availability = 1
                           AND XML_id = '".$idprod[$cont]."'" ;
                $result = mysqli_query($this->widget->getConDados(), $select);
                
                if(mysqli_num_rows ($result) > 0 )
                {
                    $linha = mysqli_fetch_array($result);
                    
                    $this->setOBJ($this->widget->getIdWid(), $linha, $this->widgetProps['WID_utm'], $i);
                    $numprod++;
                }

                $cont++;
            }
        }
          
    }
    
    /**
     * Limpa o array, removendo os produtos não desejados
     * e retorna a nova lista de produtos
     */
    private function getArrayLimpoDeProdutos(&$date, &$idprod, &$evento) {
        
        $this->removeProdutosJaAdquiridosPeloCliente($date, $idprod, $evento);
        $this->removeProdutosVisualizadosNoMomento($date, $idprod, $evento);
        $this->removeProdutosSemEstoque($date, $idprod, $evento);
        $this->removeProdutosRecentementeAdicionadosAoCarrinho($date, $idprod, $evento);
        
        $evento = array_values($evento);
        $idprod = array_values($idprod);
        $date = array_values($date);
        
        array_multisort($date, SORT_DESC, $idprod, $evento);
    }
    
    /**
     * Remove do array principal, os produtos que o cliente já adquiriu
     * 
     * @param array $date
     * @param array $idprod
     * @param array $evento
     */
    private function removeProdutosJaAdquiridosPeloCliente(&$date, &$idprod, &$evento) {
        
        array_multisort($date, SORT_DESC, $idprod, $evento);
        
        // Remove os produtos que já foram adquiridos pelo cliente
        foreach($evento as $key => $value)
        {
            if($value == 'checkout')
            {
                unset($evento[$key]);
                unset($idprod[$key]);
                unset($date[$key]);
            }
        }
    }
    
    /**
     * Remove o(s) produto(s) que ele está visualizando no momento
     *
     * @param array $date
     * @param array $idprod
     * @param array $evento
     */
    private function removeProdutosVisualizadosNoMomento(&$date, &$idprod, &$evento) {
        
        foreach($evento as $key => $value)
        {
            if($idprod[$key] == $this->widget->getProdId())
            {
                unset($evento[$key]);
                unset($idprod[$key]);
                unset($date[$key]);
            }
        }
    }
    
    /**
     * Remove o(s) produto(s) com estoque zerado
     *
     * @param array $date
     * @param array $idprod
     * @param array $evento
     */
    private function removeProdutosSemEstoque(&$date, &$idprod, &$evento) {
        // Verifica a disponibilidade do produto
        foreach($evento as $key => $value)
        {
            $select = "SELECT XML_availability FROM XML_" . $this->widget->getIdCli() . " WHERE XML_id = '$idprod[$key]'";
            $result = mysqli_query($this->widget->getConDados(), $select);

            mysqli_error($this->widget->getConDados());
            
            if(mysqli_num_rows ($result) > 0 )
            {
                $select = mysqli_fetch_array($result);
                if($select['XML_availability'] == '0')
                {
                    // Remove os itens com estoque zerado
                    unset($evento[$key]);
                    unset($idprod[$key]);
                    unset($date[$key]);
                }
            }
            else
            {
                // Remove porque não encontrou no xml
                unset($evento[$key]);
                unset($idprod[$key]);
                unset($date[$key]);
            }
        }
    }
    
    /**
     * Remove o(s) produto(s) que foram adicionados ao carrinho recentemente.
     * Eles devem ter sido adicionados ao menos um dia (ou mais) antes do dia atual.
     *
     * @param array $date
     * @param array $idprod
     * @param array $evento
     */
    private function removeProdutosRecentementeAdicionadosAoCarrinho(&$date, &$idprod, &$evento) {
        // Verifica se o produto foi adicionado ao carrinho pelo menos no dia anterior
        // não menos que isso
        $i = 0;
        foreach($evento as $key => $value)
        {
            if($value == 'cart')
            {
                if($date[$key] <= ((time() * 1000) - (1000 * 60 * 5))) //dia anterior
                {
                    $this->cart[$i] = $idprod[$key];
                    $i++;
                }
                
                unset($evento[$key]);
                unset($idprod[$key]);
                unset($date[$key]);
            }
        }
    }
}
?>
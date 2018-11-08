<?php
namespace roihero\widget\inteligencia;

/**
 * 
 * @author tiago
 */
class OverlayDeSaidaInteligencia extends AbstractInteligencia {
    
    
    /**
     * Este método realiza o processamento principal.
     * Para inteligências compostas, o processamento
     *
     * {@inheritDoc}
     * @see \roihero\widget\inteligencia\IInteligencia::processar()
     */
    public function processar() {
        
        $idprod = explode(',', $this->widget->getCookieProd());
        
        if(!empty($idprod)) 
        {
            
            $produtoBase = '';
            
            for($i = 0; $i < count($idprod); $i++) {
                
                $prodId = $this->widget->getProdId();
                if(empty($prodId) || $prodId == 'undefined') {
                    $this->widget->setProdId($idprod[$i]);
                }
                
                // Recupera os dados do último produto visualizado pelo cliente
                $sql = "SELECT ".$this->XML_select."
                        FROM XML_".$this->widget->getIdCli()."
                        WHERE XML_id = '".$this->widget->getProdId()."'
                        AND XML_availability = 1";
                
                $result = mysqli_query($this->widget->getConDados(), $sql);
                
                if(mysqli_num_rows ($result) > 0)
                {
                    $linha = mysqli_fetch_array($result);
                    $this->setOBJ($this->widget->getIdWid(), $linha, $this->widgetProps['WID_utm'], 0);
                    
                    break;
                }
            }
            
            if($this->widget->getObjNumItens() > 0) 
            {                
                // Procura os similares
                $this->executarProdutoSimilarInteligencia();

                if($this->widget->getObjNumItens() < 4) 
                {
                    // Pegar os mais clicados
                    $this->executarMaisClicadosInteligencia();
                }
            } 
            else 
            {                
                // Pegar os mais clicados
                $this->executarMaisClicadosInteligencia();                
            }            
        } 
        else 
        {
            // Pegar os mais clicados
            $this->executarMaisClicadosInteligencia();
        }
        
    }
    
    /**
     * Este método, cria uma instância da classe ProdutoSimilarInteligencia
     * e realiza a adaptação da mesma para a execução do seu processamento.
     */
    private function executarProdutoSimilarInteligencia() {
        $inteligencia = new ProdutoSimilarInteligencia();
        $this->executarInteligencia($inteligencia);
    }
    
    /**
     * Este método, cria uma instância da classe MaisClicadosInteligencia
     * e realiza a adaptação da mesma para a execução do seu processamento.
     */
    private function executarMaisClicadosInteligencia() {
        $inteligencia = new MaisClicadosInteligencia();
        $this->executarInteligencia($inteligencia);
    }
    
    /**
     * Executa o processamento do inteligência passada como parâmetro.
     * 
     * @param IInteligencia $inteligencia
     * @param string $produtoBase
     */
    private function executarInteligencia($inteligencia) {
        
        $numMaxProdutos = $inteligencia->getNumMaxProdutos() - $this->widget->getObjNumItens();
        $inteligencia->setNumMaxProdutos($numMaxProdutos);
        
        $inteligencia->setWidget($this->widget);
        $inteligencia->setWidgetProps($this->widgetProps);
        $inteligencia->setWidgetConfig($this->widgetConfig);
        
        $inteligencia->processar();
    }
    
}
?>
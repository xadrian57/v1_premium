<?php
namespace roihero\widget\inteligencia;

/**
 * Classe abstrata para os métodos prontos no fluxo da inteligência
 * 
 * @author tiago
 */
abstract class AbstractInteligencia implements IInteligencia {
    
    protected $numMaxProdutos = 24;

    protected $XML_select = "XML_id, XML_sku, XML_image_link, XML_image_link2, XML_titulo, XML_link, XML_type, XML_sale_price, XML_price, XML_type, XML_desconto, XML_nparcelas, XML_vparcela, XML_descricao, XML_brand, XML_custom1, XML_custom2, XML_custom3, XML_custom4, XML_custom5";
    
    protected $idWid;
    
    protected $widget;
    
    protected $widgetProps;
    
    protected $widgetConfig;
    
    public function setNumMaxProdutos($numMaxProdutos) {
        $this->numMaxProdutos = $numMaxProdutos;
    }
    
    public function getNumMaxProdutos() {
        return $this->numMaxProdutos;
    }
    
    public function setIdWid($idWid) {
        $this->idWid = $idWid;
    }
    
    public function setWidget($widget) {
        $this->widget = $widget;
    }
    
    public function setWidgetProps($widgetProps) {
        $this->widgetProps = $widgetProps;
    }
    
    public function setWidgetConfig($widgetConfig) {
        $this->widgetConfig = $widgetConfig;
    }
    
    protected function setOBJ($idWid, $array, $utm, $index=null)
    {
        $obj = $this->widget->getObj();
        
        foreach ($obj as $key => $value)
        {
            // Impede que retorne produtos repetidos
            // Verifica se o link já foi adicionado
            if($value['link'] == $array['XML_link']."?rhWid=".$idWid."&".$utm)
            {
                return false;
            }
        }

        $descBoleto = $this->widget->getDescBoleto();

        if($descBoleto != '0' && !empty($descBoleto))
        {
            $array['XML_sale_price'] = $array['XML_sale_price'] - ($array['XML_sale_price'] * ($descBoleto / 100));
        }        
        
        if(strval($index) == null) {
            $index = count($obj);
        }
        
        $obj[$index]['id'] = $array['XML_id'];
        $obj[$index]['sku'] = $array['XML_sku'];
        $obj[$index]['name'] = $array['XML_titulo'];
        $obj[$index]['price'] = $array['XML_price'];
        $obj[$index]['sale_price'] = $array['XML_sale_price'];
        $obj[$index]['link_image'] = $array['XML_image_link'];
        $obj[$index]['link_image_2'] = $array['XML_image_link2'];
        $obj[$index]['link'] = $array['XML_link']."?rhWid=".$idWid."&".$utm;
        $obj[$index]['description'] = $array['XML_descricao'];
        $obj[$index]['mount'] = $array['XML_nparcelas'];
        $obj[$index]['amount'] = $array['XML_vparcela'];
        $obj[$index]['discount'] = $array['XML_desconto'];
        $obj[$index]['brand'] = $array['XML_brand'];
        $obj[$index]['custom1'] = $array['XML_custom1'];
        $obj[$index]['custom2'] = $array['XML_custom2'];
        $obj[$index]['custom3'] = $array['XML_custom3'];
        $obj[$index]['custom4'] = $array['XML_custom4'];
        $obj[$index]['custom5'] = $array['XML_custom5'];
        
        $this->widget->setObj($obj);
        
        return true;
    }
    
    /**
     * Executa o select básico
     * 
     * @param string $select
     */
    protected function executeSelect($select) {
        $result = mysqli_query($this->widget->getConDados(), $select);
        
        $i = 0;
        if($result && mysqli_num_rows ($result) > 0 )
        {
            while($linha = mysqli_fetch_array($result))
            {
                if($this->setOBJ($this->widget->getIdWid(), $linha, $this->widgetProps['WID_utm']))
                {
                    $i++;
                }
            }
        }
        
        return $i;
    }
    
    /**
     * Este método recupera o XML_titulo
     * 
     * @return string
     */
    protected function getProdName() {
        $select = "SELECT XML_titulo FROM XML_".$this->widget->getIdCli()." WHERE XML_id = '" . $this->widget->getProdId() . "'";
        $result = mysqli_query($this->widget->getConDados(), $select);
        
        if(mysqli_num_rows($result) > 0) {
            
            $linha = mysqli_fetch_array($result);
            return $linha['XML_titulo'];
        }
        return '';
    }
    
    /**
     * Este método recupera o XML_titulo
     *
     * @return string
     */
    protected function getProdDados() {
        $select = "SELECT * FROM XML_".$this->widget->getIdCli()." WHERE XML_id = '" . $this->widget->getProdId() . "'";
        $result = mysqli_query($this->widget->getConDados(), $select);
        
        if(mysqli_num_rows($result) > 0) {
            
            $linha = mysqli_fetch_array($result);
            return $linha;
        }
        return '';
    }

     /**
     * Este método recupera o XML_titulo
     *
     * @return string
     * @param string
     */
    protected function getProdNameManual($produto) {

        $select = "SELECT XML_titulo
                    FROM XML_".$this->widget->getIdCli()."
                    WHERE XML_id = ". $produto;
                
        $result = mysqli_query($this->widget->getConDados(), $select);

        if($result)
        {
            $lista = mysqli_fetch_array($result);
            
            return $lista['XML_titulo'];
        }
        else
        {
            return '';
        }
    }

     /**
     * Este método recupera o XML_type
     *
     * @return string
     * @param string
     */
    protected function getProdTypeManual($produto) {

        $select = "SELECT XML_type
                    FROM XML_".$this->widget->getIdCli()."
                    WHERE XML_id = ". $produto;
                
        $result = mysqli_query($this->widget->getConDados(), $select);

        if($result)
        {
            $lista = mysqli_fetch_array($result);
            
            return $lista['XML_type'];
        }
        else
        {
            return '';
        }
    }    
}
?>
<?php
namespace roihero\widget\inteligencia;

/**
 * @author tiago
 */
class ColecaoInteligencia extends AbstractInteligencia {
    
    /**
     * 
     * {@inheritDoc}
     * @see \roihero\widget\inteligencia\IInteligencia::processar()
     */
    public function processar() {
        $prodNome = $this->getProdName();
        
        $arrayPChave = explode(',', $this->widgetConfig['WC_collection']);
        
        $check = -1;
        
        foreach ($arrayPChave as $key => $value)
        {
            $pos = strripos($prodNome, $arrayPChave[$key]);
            if($pos !== false)
            {
                $check = $key;
                break;
            }
        }
        
        if($check != -1)
        {
            if($this->widget->getIdCli() == '756')
            {
               $paramOrder = "XML_custom5";
            }
            else
            {
               $paramOrder = "XML_click_" . $this->widgetProps['WID_dias'];
            }

            $select = "SELECT ".$this->XML_select."
                       FROM XML_".$this->widget->getIdCli()."
                       WHERE XML_id != '" . $this->widget->getProdId() . "'
                       AND XML_availability = 1
                       AND MATCH(XML_titulo_upper) AGAINST(\"". strtoupper($arrayPChave[$check]) ."\" IN BOOLEAN MODE)
                       ORDER BY ".$paramOrder." DESC 
                       LIMIT " . $this->numMaxProdutos;
            $result = mysqli_query($this->widget->getConDados(), $select);
            
            $numItens = 0;
            if(mysqli_num_rows ($result) > 0 )
            {
                while($linha = mysqli_fetch_array($result))
                {
                    if($this->setOBJColecao($this->widget->getIdWid(), $linha, $numItens, $this->widgetProps['WID_utm']))
                    {
                        $numItens++;
                    }
                }
            }
            
            $arrayNumPalavra = explode(" ", $prodNome);
            $nomeProdQuebra = $prodNome;
            
            $vezesRodar = count($arrayNumPalavra);
            
            for($i = $numItens; ($i < $vezesRodar) && ($i < $this->numMaxProdutos);)
            {
                $select = "SELECT ".$this->XML_select."
                           FROM XML_".$this->widget->getIdCli()."
                           WHERE XML_id != '" . $this->widget->getProdId() . "'
                           AND XML_availability = 1
                           AND MATCH(XML_titulo_upper) AGAINST(\"" . strtoupper($nomeProdQuebra) . "\" IN BOOLEAN MODE)
                           ORDER BY ".$paramOrder." DESC 
                           LIMIT " . $this->numMaxProdutos;
                $result = mysqli_query($this->widget->getConDados(), $select);
                
                if(mysqli_num_rows ($result) > 0 )
                {
                    while($linha = mysqli_fetch_array($result))
                    {
                        if($this->setOBJColecao($this->widget->getIdWid(), $linha, $i, $this->widgetProps['WID_utm']))
                        {
                            $i++;
                        }
                    }
                }
                
                $pos = strripos($nomeProdQuebra, " ");
                $nomeProdQuebra = substr($nomeProdQuebra, 0, $pos);
            }
        }
    }
    
    protected function setOBJColecao($idWid, $array, $i, $utm)
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
        
        $obj[$i]['id'] = $array['XML_id'];
        $obj[$i]['sku'] = $array['XML_sku'];
        $obj[$i]['name'] = $array['XML_titulo'];
        $obj[$i]['price'] = $array['XML_price'];
        $obj[$i]['sale_price'] = $array['XML_sale_price'];
        $obj[$i]['link_image'] = $array['XML_image_link'];
        $obj[$i]['link_image_2'] = $array['XML_image_link2'];
        $obj[$i]['link'] = $array['XML_link']."?rhWid=".$idWid."&".$utm;
        $obj[$i]['description'] = $array['XML_descricao'];
        $obj[$i]['mount'] = $array['XML_nparcelas'];
        $obj[$i]['amount'] = $array['XML_vparcela'];
        $obj[$i]['discount'] = $array['XML_desconto'];
        
        $this->widget->setObj($obj);
        
        return true;
    }
    
}
?>
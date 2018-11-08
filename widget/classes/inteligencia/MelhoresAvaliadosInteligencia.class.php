<?php
namespace roihero\widget\inteligencia;

class MelhoresAvaliadosInteligencia extends AbstractInteligencia {
    
    /**
     * Melhores avaliados
     *
     * {@inheritDoc}
     * @see \roihero\widget\inteligencia\IInteligencia::processar()
     */
    public function processar() {
        $idCli = $this->widget->getIdCli();
        
        $select = "SELECT ".$this->XML_select.", nota as rate, ni_num_avaliacoes as num_rate
                   FROM XML_".$idCli."
                   INNER JOIN tv_estatistica_".$idCli."
                   ON tv_estatistica_".$idCli.".id_produto = XML_".$idCli.".XML_id
                   WHERE XML_availability = 1
                   ORDER BY rate DESC, ni_num_avaliacoes DESC
                   LIMIT " . $this->numMaxProdutos;
        
        $this->executeSelect($select);
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
                if($this->setOBJMelhoresAvaliados($this->widget->getIdWid(), $linha, $this->widgetProps['WID_utm']))
                {
                    $i++;
                }
            }
        }
        
        return $i;
    }
    
    protected function setOBJMelhoresAvaliados($idWid, $array, $utm, $index=null)
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
        $obj[$index]['rate'] = $array['rate'];
        $obj[$index]['num_rate'] = $array['num_rate'];
        
        $this->widget->setObj($obj);
        
        return true;
    }
}
?>
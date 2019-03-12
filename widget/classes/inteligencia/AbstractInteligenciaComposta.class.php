<?php
namespace roihero\widget\inteligencia;

/**
 * Classe abstrata para os métodos prontos no fluxo da inteligência composta
 * 
 * @author tiago
 */
abstract class AbstractInteligenciaComposta extends AbstractInteligencia {

    private $listaObjs = [];
    protected $idsProd = [];
    protected $numMaxProdutos = 24;
    
    /**
     * Este método retorna o idsProd se este atributo já estiver preenchido.
     * Caso ainda não esteja preenchido este método realiza a ordenação pela
     * data dos ids de produtos e então os retorna.
     * 
     * @return array
     */
    protected function getIdsProd() {
        
        // Se já estiver populado, apenas retorna
        if(count($this->idsProd) > 0) {
            return $this->idsProd;
        }
        
        // Senão estiver populado, realiza o processamento para retornar        
        $date = explode(',', $this->widget->getCookieData());
        $idprod = explode(',', $this->widget->getCookieProd());
        
        array_multisort($date, SORT_DESC, $idprod);
        
        foreach($idprod as $key => $value)
        {
            if($idprod[$key] == $this->widget->getProdId())
            {
                unset($idprod[$key]);
                unset($date[$key]);
            }
        }
        
        $idprod = array_values($idprod);
        $date = array_values($date);
        
        array_multisort($date, SORT_DESC, $idprod);
        
        return $idprod;
    }
    
    /**
     * Este método realiza a leitura da estrutura dos objetos
     * e os organiza para uma exibição mais natural para o cliente
     */
    protected function ordenarObjetos() {
        
        if(count($this->listaObjs)) {
            
            $newObj = [];
            $maxColum = $this->getNumMaxColunas();
            $pos = 0;
            $numProdsAdicionados = 0;

            $nomeInteligenciaGA = $this->getNomeInteligenciaGA($this->widget->getWidInteligencia());
            
            for($i = 0; $i < count($this->listaObjs); $i++)
            {
                
                $array = $this->listaObjs[$i][$pos];
                
                if(!empty($array))
                {
                    $newObj[$numProdsAdicionados]['id'] = $array['id'];
                    $newObj[$numProdsAdicionados]['sku'] = $array['sku'];
                    $newObj[$numProdsAdicionados]['name'] = $array['name'];
                    $newObj[$numProdsAdicionados]['price'] = $array['price'];
                    $newObj[$numProdsAdicionados]['sale_price'] = $array['sale_price'];
                    $newObj[$numProdsAdicionados]['link_image'] = $array['link_image'];
                    $newObj[$numProdsAdicionados]['link_image_2'] = $array['link_image_2'];
                    $newObj[$numProdsAdicionados]['link'] = $array['link']."?rhWid=". $this->widget->getIdWid() . "&" . $nomeInteligenciaGA . "&" . $this->widgetProps['WID_utm'];
                    $newObj[$numProdsAdicionados]['description'] = $array['description'];
                    $newObj[$numProdsAdicionados]['mount'] = $array['mount'];
                    $newObj[$numProdsAdicionados]['amount'] = $array['amount'];
                    $newObj[$numProdsAdicionados]['discount'] = $array['discount'];
                    $numProdsAdicionados++;
                }
                
                if($i > count($this->listaObjs)){
                    $i = -1;
                    $pos++;
                    
                    if($pos > $maxColum){
                        break;
                    }
                }
                
                // Verifica se já adicionou a quantidade máxima de produtos para o retorno
                if($numProdsAdicionados >= $this->numMaxProdutos) {
                    break;
                }
            }
            
            $this->widget->setObj($newObj);
        }
    }
    
    /**
     * Recupera qual é o número máximo de colunas para ler
     *
     * @return int
     */
    private function getNumMaxColunas() {
        $maxColum = 0;
        $array = $this->listaObjs;
        
        for($i = 0; $i < count($array); $i++)
        {
            if($i == 0)
            {
                $maxColum = count($array[$i]);
            }
            else if($maxColum < count($array[$i]))
            {
                $maxColum = count($array[$i]);
            }
        }
        
        return $maxColum;
    }
    
    /**
     * Este método retorna estrutura do comando select, para inteligências compostas
     * 
     * @param string $nomeProdQuebra
     * @return string
     */
    protected function getSelectPorNomeProdQuebra($nomeProdQuebra) {
        return "SELECT " . $this->XML_select . "
                FROM XML_".$this->widget->getIdCli()."
                WHERE XML_id != '" . $this->widget->getProdId() . "'
                AND XML_availability = 1
                AND MATCH(XML_titulo_upper) AGAINST(\"" . strtoupper($nomeProdQuebra) . "\" IN BOOLEAN MODE)
                ORDER BY XML_click_".$this->widgetProps['WID_dias']."
                LIMIT " . $this->numMaxProdutos;
    }
    
    /**
     * Recupera a estrutura obj
     */
    protected function getOBJ($idWid, $array, $utm)
    {
        $nomeInteligenciaGA = $this->getNomeInteligenciaGA($this->widget->getWidInteligencia());

        $linkCompleto = $array['XML_link']."?rhWid=".$idWid."&".$nomeInteligenciaGA."&".$utm;
        
        // Verifica se o produto já foi adicionado
        if($this->linkJaAdicionadoAoRetorno($linkCompleto)) {
            return false;
        }
        
        $obj = [];
        
        $obj['id'] = $array['XML_id'];
        $obj['sku'] = $array['XML_sku'];
        $obj['name'] = $array['XML_titulo'];
        $obj['price'] = $array['XML_price'];
        $obj['sale_price'] = $array['XML_sale_price'];
        $obj['link_image'] = $array['XML_image_link'];
        $obj['link_image_2'] = $array['XML_image_link2'];
        $obj['link'] = $linkCompleto;
        $obj['description'] = $array['XML_descricao'];
        $obj['mount'] = $array['XML_nparcelas'];
        $obj['amount'] = $array['XML_vparcela'];
        $obj['discount'] = $array['XML_desconto'];
        
        return $obj;
    }
    
    /**
     * Este método, varre o array $this->listaObjs para verificar pelo link
     * se um determinado produto já foi adicionar ao retorno.
     * 
     * @param string $link
     */
    private function linkJaAdicionadoAoRetorno($link) {
        
        for ($i = 0; $i < count($this->listaObjs); $i++) {
            
            for ($j = 0; $j < count($this->listaObjs[$i]); $j++) {
                
                $tempObj = $this->listaObjs[$i][$j];
                
                if($tempObj['link'] == $link) {
                    
                    return true;
                }
            }
            
        }
        return false;
    }
    
    /**
     * Executa o select básico
     *
     * @param string $select
     */
    protected function executeSelect($select) {
        $result = mysqli_query($this->widget->getConDados(), $select);
        $arrayObj = [];
        
        if($result)
        {
            $i = 0;
            while($linha = mysqli_fetch_array($result))
            {
                $tempObj = $this->getOBJ($this->widget->getIdWid(), $linha, $this->widgetProps['WID_utm']);
                if($tempObj)
                {
                    $i++;
                }
                $arrayObj[] = $tempObj;
            }
        }
        $this->listaObjs[] = $arrayObj;
    }

    /**
     * Este método retorna o nome da inteligencia para captura no GA
     *
     * @return string
     * @param string
     */
    protected function getNomeInteligenciaGA($idInteligencia) 
    {
        $getInt = 'rh_int=';
        switch ($idInteligencia) {
            case '1':
                $getInt .= 'md';
                break;
            case '2':
                $getInt .= 'mv';
                break;
            case '3':
                $getInt .= 'mvc';
                break;
            case '4':
                $getInt .= 'rmkt';
                break;
            case '5':
                $getInt .= 'sp';
                break;
            case '6':
                $getInt .= 'liquid';
                break;
            case '7':
                $getInt .= 'col';
                break;
            case '9':
                $getInt .= 'man';
                break;
            case '10':
                $getInt .= 'ol';
                break;
            case '12':
                $getInt .= 'ic';
                break;
            case '13':
                $getInt .= 'os';
                break;
            case '14':
                $getInt .= 'bp';
                break;
            case '15':
                $getInt .= 'lanc';
                break;
            case '22':
                $getInt .= 'search';
                break;
            case '24':
                $getInt .= 'mvcm';
                break;
            case '25':
                $getInt .= 'pc';
                break;
            case '34':
                $getInt .= 'pr';
                break;
            case '36':
                $getInt .= 'sh';
                break;
            case '38':
                $getInt .= 'mvmm';
                break;
            case '39':
                $getInt .= 'spp';
                break;
            case '40':
                $getInt .= 'lancm';
                break;
            
            default:
                $getInt .= '';
                break;
        }

        return $getInt;
    }
}
?>
<?php
namespace roihero\widget;

use roihero\widget\util\Util;

/**
 * Classe abstrata, que contém métodos prontos para a herança dos widgets
 *  
 * @author tiago
 */
abstract class AbstractWidget {
    
    protected $conCad;
    protected $conDados;
    protected $conToken;
    
    protected $idcli_cryp;
    protected $cookiedata;
    protected $cookieprod;
    protected $cookieevent;
    protected $url;
    protected $ofertaID;
    protected $page;
    protected $prodId;
    protected $categoria;
    
    protected $JSON_widgets;
    
    protected $obj = [];

    protected $descBoleto;
    
    protected $idcli;
    protected $idWid;
    protected $widInteligencia;
    protected $overlayNaoExibido = 0;
    
    // Só fornecido no fluxo do tagflag
    protected $tokenTagflag;
    protected $widgetId;

    //Scroll Checkout
    protected $viewsNow;
    
    /**
     * Seta todos os parâmetros usados no processamento
     * 
     * @param array $arrayRequest
     */
    public function setParametros($arrayRequest) {
        //CONEXÃO BD Dados
        include '../bd/conexao_bd_dados.php';
        $this->conDados = $conDados;
        
        //CONEXÃO BD CADASTRO
        include '../bd/conexao_bd_cadastro.php';
        $this->conCad = $conCad;
        
        $this->idcli_cryp = mysqli_escape_string($conCad, $arrayRequest['idcli']);
        $this->cookiedata = mysqli_escape_string($conCad, $arrayRequest['cookiedata']);
        $this->cookieprod = mysqli_escape_string($conCad, $arrayRequest['cookieprod']);
        $this->cookieevent = mysqli_escape_string($conCad, $arrayRequest['cookieevent']);
        $this->url = mysqli_escape_string($conCad, $arrayRequest['url']);
        $this->ofertaID = mysqli_escape_string($conCad, $arrayRequest['ofertaID']);
        $this->page = mysqli_escape_string($conCad, $arrayRequest['page']);
        $this->prodId = mysqli_escape_string($conCad, $arrayRequest['idProd']);
        
        if(array_key_exists('categoria', $arrayRequest)) {
            $this->categoria = mysqli_escape_string($conCad, $arrayRequest['categoria']);
        }
        
        if(array_key_exists('overlayNaoExibido', $arrayRequest)) {
            $this->overlayNaoExibido = mysqli_escape_string($conCad, $arrayRequest['overlayNaoExibido']);
        }
        
        // Só fornecido no fluxo do tagflag
        if(array_key_exists('Roihero-Token', $arrayRequest)) {
            include '../bd/conexao_adm_tokn.php';
            $this->conToken = $conToken;
            $this->tokenTagflag = mysqli_escape_string($conCad, $arrayRequest['Roihero-Token']);
        }
        
        // Só fornecido no fluxo do tagflag
        if(array_key_exists('widgetId', $arrayRequest)) {
            $this->widgetId = mysqli_escape_string($conCad, $arrayRequest['widgetId']);
        }
    }
    
    protected function inject($arrayWidgets) {
        $inject = $arrayWidgets['WID_inject'];
        $html = '';
        if($inject)
        {
            // SELECT TEMPLATE
            $selectConfig = "SELECT CONF_template, CONF_template_overlay, CONF_moeda FROM config WHERE CONF_id_cli = '$this->idcli'";
            $resultConfig = mysqli_query($this->conCad, $selectConfig);
            
            $arrayConfig = mysqli_fetch_array($resultConfig);
            
            if ($arrayWidgets['WID_formato'] == 41) {
                $html = Util::get_HTML_Loja_Lateral($this,$arrayConfig,$arrayWidgets);
            }else if($arrayWidgets['WID_formato'] == 44){
                $html = Util::get_HTML_sc($this->obj, $arrayConfig, $arrayWidgets, $this->getViewsNow());
            }else{
                $html = Util::get_HTML($this->obj, $arrayConfig, $arrayWidgets);
            }   

            $this->JSON_widgets = Util::set_JSON_widget($this->JSON_widgets, $inject, $this->idWid, $html, $arrayWidgets, $this->obj, $this->widInteligencia);
        }
        else
        {
            $this->JSON_widgets = Util::set_JSON_widget($this->JSON_widgets, $inject, $this->idWid, $html, $arrayWidgets, $this->obj, $this->widInteligencia);
        }
    }
    
    protected function queryPage()
    {
        // 'todas'=>0,'home'=>1,'produto'=>2,'buscaVazia'=>3,'categoria'=>4,'carrinho'=>5 ,'checkout'=>6
        
        switch (trim($this->page))
        {
            case 'product':
                $result = "AND WID_pagina IN (0,2) ";
                break;
            case 'cart':
                $result = "AND WID_pagina IN (0,5) ";
                break;
            case 'transaction':
                $result = "AND WID_pagina IN (0,6) ";
                break;
            case 'home':
                $result = "AND WID_pagina IN (0,1) ";
                break;
            case 'category':
                $result = "AND WID_pagina IN (0,4) ";
                break;
            case 'search':
                $result = "AND WID_pagina IN (0,3) ";
                break;
            case 'searchEmpty':
                $result = "AND WID_pagina IN (0,7) ";
                break;
            default:
                $result = " ";
                break;
        }
        
        return $result;
    }
    
    protected function getEstoque($esgotado)
    {
        if($esgotado == 0) {
            return 0;
        }
        
        $sql = "SELECT XML_availability FROM XML_".$this->idcli." WHERE XML_id = '$this->prodId' LIMIT 1";
        $result = mysqli_query($this->conDados, $sql);
        
        if(mysqli_num_rows($result) > 0)
        {
            $array = mysqli_fetch_array($result);
            
            return $array['XML_availability'];
        }
        
        return 0;
    }
    
    public function getIdCli() {
        return $this->idcli;
    }

    public function getDescBoleto() {
        return $this->descBoleto;
    }
    
    public function getConDados() {
        return $this->conDados;
    }
    
    public function getConCad() {
        return $this->conCad;
    }
    
    public function getJsonWidgets() {
        return $this->JSON_widgets;
    }
    
    public function getCookieData() {
        $arrayData = explode(',', $this->cookiedata);

        array_multisort($arrayData, SORT_DESC);

        return implode(',', $arrayData);
    }
    
    public function getCookieProd() {

        $arrayProd = explode(',', $this->cookieprod);
        $arrayData = explode(',', $this->cookiedata);

        array_multisort($arrayData, SORT_DESC, $arrayProd);

        return implode(',', $arrayProd);
    }
    
    public function getCategoria() {
        
        if(empty($this->categoria))
        {
            if(!empty($this->prodId))
            {
                $sql = "SELECT XML_type FROM XML_".$this->idcli." WHERE XML_id = '$this->prodId' LIMIT 1";
                $result = mysqli_query($this->conDados, $sql);
                
                if(mysqli_num_rows($result) > 0)
                {
                    $array = mysqli_fetch_array($result);
                    
                    return $array['XML_type'];
                }
            }
            else
            {
                return '';
            }
        }
        else
        {
            return $this->categoria;
        }        
    }

    public function getParametroXML($parm) {        
        
        if(!empty($this->prodId))
        {
            $sql = "SELECT XML_". $parm ." FROM XML_".$this->idcli." WHERE XML_id = '$this->prodId' LIMIT 1";
            $result = mysqli_query($this->conDados, $sql);
            
            if(mysqli_num_rows($result) > 0)
            {
                $array = mysqli_fetch_array($result);

                $key = 'XML_'. $parm;
                
                return $array[$key];
            }
            else
            {
                return '';
            }
        }
        else
        {
            return '';
        }       
    }
    
    public function getCookieEvent() {
        
        $arrayEvent = explode(',', $this->cookieevent);
        $arrayData = explode(',', $this->cookiedata);

        array_multisort($arrayData, SORT_DESC, $arrayEvent);

        return implode(',', $arrayEvent);
    }
    
    public function getOfertaID() {
        return $this->ofertaID;
    }
    
    public function getProdId() {
        return $this->prodId;
    }
    
    public function setProdId($prodId) {
        $this->prodId = $prodId;
    }
    
    public function getObj() {
        return $this->obj;
    }
    
    public function setObj($obj) {
        $this->obj = $obj;
    }
    
    public function getObjNumItens() {
        return count($this->obj);
    }
    
    public function getIdWid() {
        return $this->idWid;
    }

    public function getWidInteligencia() {
        return $this->widInteligencia;
    }
    
    public function getUrl() {
        return $this->url;
    }
    
    public function getOverlayNaoExibido() {
        return $this->overlayNaoExibido;
    }
    
    public function getTokenTagflag() {
        return $this->tokenTagflag;
    }
    
    public function getWidgetId() {
        return $this->widgetId;
    }

    public function setViewsNow($views_now) {
        $this->viewsNow = $views_now;
    }
    
    public function getViewsNow() {
        return $this->viewsNow;
    }
}
?>
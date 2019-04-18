<?php
namespace roihero\widget\util;

class Util {
    
    // Classe apenas para chamada de métodos static
    private function __construct() {
    }
    
    public static function showHide($url, $arrayWidgets)
    {
        $check = 1;
        $widShow = $arrayWidgets['WID_show'];
        $widHide = $arrayWidgets['WID_hide'];
        
        if($widShow != NULL && $widShow != "")
        {
            $arrayShow = explode(",", $widShow);
            
            foreach ($arrayShow as $key => $value)
            {
                if(strcmp($arrayShow[$key], $url))
                {
                    $check = 0;
                }
                else
                {
                    $check = 1;
                    break;
                }
            }
        }
        
        if($widHide != NULL && $widHide != "")
        {
            $arrayHide = explode(",", $widHide);
            
            foreach ($arrayHide as $key => $value)
            {
                if(!strcmp($arrayHide[$key], $url))
                {
                    $check = 0;
                    break;
                }
            }
        }
        
        return $check;
    }
    
    public static function limpaURL($url)
    {
        $arrayURL = explode("/", $url);
        $n = count($arrayURL);
        $n--;
        $pesquisa = $arrayURL[$n];
        
        if($pesquisa == 'p' || $pesquisa == '')
        {
            $n--;
            $pesquisa = $arrayURL[$n];
        }
        
        return $pesquisa;
    }
    
    public static function set_JSON_widget($JSON_widgets, $inject, $idWid, $html, $arrayWidgets, $obj, $inteligencia)
    {
        $injectName = $arrayWidgets['WID_div'];
        $position = $arrayWidgets['WID_placement'];
        $injectType = $arrayWidgets['WID_div_type'];
        $injectUpDown = $arrayWidgets['WID_updown'];
        $products = Util::getJSON($obj);
        
        if(!empty($JSON_widgets))
        {
            $JSON_widgets .= ',';
        }
        
        if($inject)
        {
            $JSON_widgets .= '{
                "widget_id":'.$idWid.',
                "widget_inteligencia":'.$inteligencia.',
                "inject":true,
                "inject_name":"'.$injectName.'",
                "inject_position":"'.$position.'",
                "inject_type":"'.$injectType.'",
                "inject_up":'.$injectUpDown.',
                "html":'.json_encode($html).'
            }';
        }
        else
        {
            $JSON_widgets .= '{
             "widget_id":'.$idWid.',
             "inject":false,
             '.$products.'
          }';
        }
        
        return $JSON_widgets;
    }
    
    public static function getJSON ($obj)
    {
        $json = '"product":[';
        
        //foreach ($obj as $key => $value)
        for($i = 0; $i < count($obj); $i++)
        {
            if($i != 0)
            {
                $json .= ',';
            }
            
            $value = $obj[$i];
            
            $json .= '
                      {
                         "id":"'.$value['id'].'",
                         "sku":"'.$value['sku'].'",
                         "name":"'.$value['name'].'",
                         "price":"'.$value['price'].'",
                         "sale_price":"'.$value['sale_price'].'",
                         "link_image":"'.$value['link_image'].'",
                         "link_image_2":"'.$value['link_image_2'].'",
                         "link":"'.$value['link'].'",
                         "description":"'.$value['description'].'",
                         "mount":'.$value['mount'].',
                         "amount":"'.$value['amount'].'",
                         "discount":'.$value['discount'].',
                         "custom_var":['
//                             "'.$value['custom1'].'",
//                             "'.$value['custom2'].'",
//                             "'.$value['custom_var'][2].'",
//                             "'.$value['custom_var'][3].'",
//                             "'.$value['custom_var'][4].'"
                         . '],
                         "trustvox":{
                            "rate":"",
                            "num_rate":"",
                            "coments":[
                               "",
                               "",
                               ""
                            ],
                            "date":[
                               "",
                               "",
                               ""
                            ],
                            "users":[
                               "",
                               "",
                               ""
                            ]
                         }
                      }';
        }
        
        $json .=   ']';
        
        return $json;
    }
    
    public static function formatValues($obj, $moeda)
    {
        if($moeda == '$')
        {
            $parmFormatOne = '.';
            $parmFormatTwo = ',';
        }
        else
        {
            $parmFormatOne = ',';
            $parmFormatTwo = '.';
        }

        foreach ($obj as $key => $value)
        {
            if(array_key_exists('price', $obj[$key])) {
                $obj[$key]['price'] = number_format($obj[$key]['price'], 2, $parmFormatOne, $parmFormatTwo);
            }
            
            if(array_key_exists('sale_price', $obj[$key])) {
                $obj[$key]['sale_price'] = number_format($obj[$key]['sale_price'], 2, $parmFormatOne, $parmFormatTwo);
            }
            
            if(array_key_exists('amount', $obj[$key])) {
                $obj[$key]['amount'] = number_format($obj[$key]['amount'], 2, $parmFormatOne, $parmFormatTwo);
            }
        }

        return $obj;
    }
    
    // Funções do HTML
    public static function get_HTML($obj, $arrayConfig, $arrayWidgets)
    {
        $template = $arrayConfig['CONF_template'];
        $templateOverlay = $arrayConfig['CONF_template_overlay'];
        $moeda = $arrayConfig['CONF_moeda'];
        $linkBanner = $arrayWidgets['WID_link_banner'];
        $banner = $arrayWidgets['WID_banner'];
        $idCli = $arrayWidgets['WID_id_cli'];
        $formato = $arrayWidgets['WID_formato'];
        $titulo = $arrayWidgets['WID_texto'];
        $subTitulo = $arrayWidgets['WID_sub_titulo'];
        $numProd = $arrayWidgets['WID_num_prod'];
        $idWid = $arrayWidgets['WID_id'];
        $cupom = $arrayWidgets['WID_cupom'];

        // caso seja um compre junto 3, chama a função especifica
        if($formato == 12)
        {
            $response = self::get_HTML_cj_3($obj, $arrayConfig, $arrayWidgets);
            return $response;
        }
        
        if(!empty($obj[0]['link']) || $formato == 45)
        {
            $sumValue = 0.00;
            $sumValueDe = 0.00;
            // troca slider por bloco
            if($formato == 3 && count($obj) < 5)
            {
                $formato = 1;
            }

            // esconde blocos com menos de 4 produtos
            if(($formato == 1 && count($obj) < 4) || ($formato == 14 && count($obj) < 4))
            {
                return '';
            }

            if($numProd == 0)
            {
                $numProd = self::getNumProd($formato);
            }

            //NOVOS OVERLAYS PADRÃO
            if($formato == 5 || $formato == 6 || $formato == 45)
            {
                $formato = self::getFormatName($formato);
                $html = @file_get_contents("templates/overlay/kit_".$templateOverlay."/".$formato.".html");

                if(empty($html))
                {
                    $html = file_get_contents("templates/kit_".$template."/".$formato.".html");
                }
            }
            else
            {
                $formato = self::getFormatName($formato);
                $html = file_get_contents("templates/kit_".$template."/".$formato.".html");
            }            

            $html = str_replace('{TITLE_BLOCK}', $titulo, $html);
            $html = str_replace('{SUB_TITLE}', $subTitulo, $html);
            $html = str_replace('{ID_WIDGET}', $idWid, $html);
            $html = str_replace('{LINK_BANNER_BLOCK}', $linkBanner, $html);
            $html = str_replace('{BANNER_BLOCK}', 'https://roihero.com.br/widget/images/overlay/'.$banner, $html);
            $html = str_replace('{RCO_CUPOM}', $cupom, $html);

            $htmlArray = explode("<!-- REPEAT PRODUCTS -->", $html);

            if(count($htmlArray) > 2)
            {
                $head = $htmlArray[0];
                $body = '';
                $footer = $htmlArray[2];
                $elementRepeat = $htmlArray[1];

                for($i = 0; $i < $numProd; $i++)
                {
                    if($formato == 'remarketing' && $i == 0)
                    {
                        if(!empty($obj[$i]['link']))
                        {
                            $aux = $head;

                            $aux = str_replace('{PRODUCT_ID_0}', $obj[$i]['id'], $aux);
                            $aux = str_replace('{PRODUCT_SKU_0}', $obj[$i]['sku'], $aux);
                            $aux = str_replace('{PRODUCT_CUSTOMVAR1_0}', $obj[$i]['custom1'], $aux);
                            $aux = str_replace('{PRODUCT_CUSTOMVAR2_0}', $obj[$i]['custom2'], $aux);
                            $aux = str_replace('{PRODUCT_CUSTOMVAR3_0}', $obj[$i]['custom3'], $aux);
                            $aux = str_replace('{PRODUCT_CUSTOMVAR4_0}', $obj[$i]['custom4'], $aux);
                            $aux = str_replace('{PRODUCT_CUSTOMVAR5_0}', $obj[$i]['custom5'], $aux);
                            $aux = str_replace('{PRODUCT_URL_0}', $obj[$i]['link'], $aux);
                            $aux = str_replace('{PRODUCT_NAME_0}', $obj[$i]['name'], $aux);
                            $aux = str_replace('{PRODUCT_IMG_0}', $obj[$i]['link_image'], $aux);
                            $aux = str_replace('{PRODUCT_BRAND_0}', $obj[$i]['brand'], $aux);

                            if($obj[$i]['link_image_2'] == '' || $obj[$i]['link_image_2'] == NULL)
                            {
                                $aux = str_replace('{PRODUCT_IMG_2_0}', $obj[$i]['link_image'], $aux);
                            }
                            else
                            {
                                $aux = str_replace('{PRODUCT_IMG_2_0}', $obj[$i]['link_image_2'], $aux);
                            }

                            $aux = str_replace('{PRODUCT_DESCRIPTION_0}', $obj[$i]['description'], $aux);
                            
                            // VERIFICAR SE É 0 PARA EXIBIR UM SÓ
                            // estudar a melhor forma, talvez utilizar comentarios ao redor
                            if(($obj[$i]['sale_price'] != '0,00') && ($obj[$i]['sale_price'] != $obj[$i]['price']))
                            {
                                // REMOVE O FALSE
                                $arrayAux = explode ('<!-- PRICE FALSE -->', $aux);
                                $aux = $arrayAux[0].''.$arrayAux[2];

                                $aux = str_replace('{VALUE_DE_0}', $moeda.' '.$obj[$i]['price'], $aux);
                                $aux = str_replace('{VALUE_0}', $moeda.' '.$obj[$i]['sale_price'], $aux);

                                $auxValue = '';

                                if($moeda != '$')
                                {
                                    $auxValue = str_replace('.','',$obj[$i]['sale_price']);
                                    $auxValue = str_replace(',','.',$auxValue);
                                }
                                else
                                {
                                    $auxValue = str_replace(',','',$obj[$i]['sale_price']);
                                }

                                $sumValue += number_format(floatval($auxValue), 2, '.', '');

                                $auxValue = '';

                                if($moeda != '$')
                                {
                                    $auxValue = str_replace('.','',$obj[$i]['price']);
                                    $auxValue = str_replace(',','.',$auxValue);
                                }
                                else
                                {
                                    $auxValue = str_replace(',','',$obj[$i]['price']);
                                }

                                $sumValueDe += number_format(floatval($auxValue), 2, '.', '');
                            }
                            else
                            {
                                // REMOVE O TRUE
                                $arrayAux = explode ('<!-- PRICE TRUE -->', $aux);
                                $aux = $arrayAux[0].''.$arrayAux[2];

                                $aux = str_replace('{VALUE_0}', $moeda.' '.$obj[$i]['price'], $aux);

                                $auxValue = '';

                                if($moeda != '$')
                                {
                                    $auxValue = str_replace('.','',$obj[$i]['price']);
                                    $auxValue = str_replace(',','.',$auxValue);
                                }
                                else
                                {
                                    $auxValue = str_replace(',','',$obj[$i]['price']);
                                }

                                $sumValue += number_format(floatval($auxValue), 2, '.', '');
                                $sumValueDe += number_format(floatval($auxValue), 2, '.', '');
                            }           
                            

                            // VERIFICAR SE É 0 PARA NÃO EXIBIR
                            // estudar a melhor forma, talvez utilizar comentarios ao redor
                            if($obj[$i]['mount'] != 0 && ($obj[$i]['mount'] != 1 || $idCli == '598'))
                            {
                                // REMOVE O FALSE
                                $arrayAux = explode ('<!-- AMOUNT FALSE -->', $aux);
                                $aux = $arrayAux[0].''.$arrayAux[2];

                                $aux = str_replace('{AMOUNT_PLOTS_0}', $obj[$i]['mount'], $aux);
                                $aux = str_replace('{VALUE_PLOTS_0}', $moeda.' '.$obj[$i]['amount'], $aux);
                            }
                            else
                            {
                                // REMOVE O TRUE
                                $arrayAux = explode ('<!-- AMOUNT TRUE -->', $aux);
                                $aux = $arrayAux[0].''.$arrayAux[2];
                            }
                            
                            $arrayDiscount = explode ('<!-- DISCOUNT -->', $aux);
                            if(count($arrayDiscount) > 2)
                            {
                                if($obj[$i]['discount'] != 0 && $obj[$i]['discount'] != 100)
                                {
                                    // REMOVE O FALSE
                                    $arrayAux = explode ('<!-- DISCOUNT FALSE -->', $aux);
                                    $aux = $arrayAux[0].''.$arrayAux[2];

                                    $aux = str_replace('{PRODUCT_DISCOUNT_0}', $obj[$i]['discount'], $aux);
                                }
                                else
                                {
                                    // REMOVE O TRUE
                                    $arrayAux = explode ('<!-- DISCOUNT TRUE -->', $aux);
                                    $aux = $arrayAux[0].''.$arrayAux[2];
                                }
                            }       

                            $head = $aux;
                        }
                    }
                    else if(!empty($obj[$i]['link']))
                    {
                        $aux = $elementRepeat;

                        $aux = str_replace('{PRODUCT_ID}', $obj[$i]['id'], $aux);
                        $aux = str_replace('{PRODUCT_SKU}', $obj[$i]['sku'], $aux);
                        $aux = str_replace('{PRODUCT_CUSTOMVAR1}', $obj[$i]['custom1'], $aux);
                        $aux = str_replace('{PRODUCT_CUSTOMVAR2}', $obj[$i]['custom2'], $aux);
                        $aux = str_replace('{PRODUCT_CUSTOMVAR3}', $obj[$i]['custom3'], $aux);
                        $aux = str_replace('{PRODUCT_CUSTOMVAR4}', $obj[$i]['custom4'], $aux);
                        $aux = str_replace('{PRODUCT_CUSTOMVAR5}', $obj[$i]['custom5'], $aux);
                        $aux = str_replace('{PRODUCT_URL}', $obj[$i]['link'], $aux);
                        $aux = str_replace('{PRODUCT_NAME}', $obj[$i]['name'], $aux);
                        $aux = str_replace('{PRODUCT_IMG}', $obj[$i]['link_image'], $aux);
                        $aux = str_replace('{PRODUCT_BRAND}', $obj[$i]['brand'], $aux);
                        
                        if($obj[$i]['link_image_2'] == '' || $obj[$i]['link_image_2'] == NULL)
                        {
                            $aux = str_replace('{PRODUCT_IMG_2}', $obj[$i]['link_image'], $aux);
                        }
                        else
                        {
                            $aux = str_replace('{PRODUCT_IMG_2}', $obj[$i]['link_image_2'], $aux);
                        }

                        $aux = str_replace('{PRODUCT_DESCRIPTION}', $obj[$i]['description'], $aux);
                        
                        // VERIFICAR SE É 0 PARA EXIBIR UM SÓ
                        // estudar a melhor forma, talvez utilizar comentarios ao redor
                        if(($obj[$i]['sale_price'] != '0,00') && ($obj[$i]['sale_price'] != $obj[$i]['price']))
                        {
                            // REMOVE O FALSE
                            $arrayAux = explode ('<!-- PRICE FALSE -->', $aux);
                            $aux = $arrayAux[0].''.$arrayAux[2];

                            $aux = str_replace('{VALUE_DE}', $moeda.' '.$obj[$i]['price'], $aux);
                            $aux = str_replace('{VALUE}', $moeda.' '.$obj[$i]['sale_price'], $aux);

                            $auxValue = '';

                            if($moeda != '$')
                            {
                                $auxValue = str_replace('.','',$obj[$i]['sale_price']);
                                $auxValue = str_replace(',','.',$auxValue);
                            }
                            else
                            {
                                $auxValue = str_replace(',','',$obj[$i]['sale_price']);
                            }

                            $sumValue += number_format(floatval($auxValue), 2, '.', '');

                            $auxValue = '';

                            if($moeda != '$')
                            {
                                $auxValue = str_replace('.','',$obj[$i]['price']);
                                $auxValue = str_replace(',','.',$auxValue);
                            }
                            else
                            {
                                $auxValue = str_replace(',','',$obj[$i]['price']);
                            }

                            $sumValueDe += number_format(floatval($auxValue), 2, '.', '');
                        }
                        else
                        {
                            // REMOVE O TRUE
                            $arrayAux = explode ('<!-- PRICE TRUE -->', $aux);
                            $aux = $arrayAux[0].''.$arrayAux[2];

                            $aux = str_replace('{VALUE}', $moeda.' '.$obj[$i]['price'], $aux);

                            $auxValue = '';

                            if($moeda != '$')
                            {
                                $auxValue = str_replace('.','',$obj[$i]['price']);
                                $auxValue = str_replace(',','.',$auxValue);
                            }
                            else
                            {
                                $auxValue = str_replace(',','',$obj[$i]['price']);
                            }

                            $sumValue += number_format(floatval($auxValue), 2, '.', '');
                            $sumValueDe += number_format(floatval($auxValue), 2, '.', '');
                        }           
                        

                        // VERIFICAR SE É 0 PARA NÃO EXIBIR
                        // estudar a melhor forma, talvez utilizar comentarios ao redor
                        if($obj[$i]['mount'] != 0 && ($obj[$i]['mount'] != 1 || $idCli == '598'))
                        {
                            // REMOVE O FALSE
                            $arrayAux = explode ('<!-- AMOUNT FALSE -->', $aux);
                            $aux = $arrayAux[0].''.$arrayAux[2];

                            $aux = str_replace('{AMOUNT_PLOTS}', $obj[$i]['mount'], $aux);
                            $aux = str_replace('{VALUE_PLOTS}', $moeda.' '.$obj[$i]['amount'], $aux);
                        }
                        else
                        {
                            // REMOVE O TRUE
                            $arrayAux = explode ('<!-- AMOUNT TRUE -->', $aux);
                            $aux = $arrayAux[0].''.$arrayAux[2];
                        }

                        $arrayDiscount = explode ('<!-- DISCOUNT -->', $aux);
                        if(count($arrayDiscount) > 2)
                        {
                            if($obj[$i]['discount'] != 0 && $obj[$i]['discount'] != 100)
                            {
                                // REMOVE O FALSE
                                $arrayAux = explode ('<!-- DISCOUNT FALSE -->', $aux);
                                $aux = $arrayAux[0].''.$arrayAux[2];

                                $aux = str_replace('{PRODUCT_DISCOUNT}', $obj[$i]['discount'], $aux);
                            }
                            else
                            {
                                // REMOVE O TRUE
                                $arrayAux = explode ('<!-- DISCOUNT TRUE -->', $aux);
                                $aux = $arrayAux[0].''.$arrayAux[2];
                            }
                        }               

                        $body .= $aux;
                    }
                }

                $response = $head."".$body."".$footer;
            }
            else
            {                   
                $aux = $html;

                for($i = 0; $i < $numProd; $i++)
                {

                    $aux = str_replace('{PRODUCT_ID_'.$i.'}', $obj[$i]['id'], $aux);
                    $aux = str_replace('{PRODUCT_SKU_'.$i.'}', $obj[$i]['sku'], $aux);
                    $aux = str_replace('{PRODUCT_CUSTOMVAR1_'.$i.'}', $obj[$i]['custom1'], $aux);
                    $aux = str_replace('{PRODUCT_CUSTOMVAR2_'.$i.'}', $obj[$i]['custom2'], $aux);
                    $aux = str_replace('{PRODUCT_CUSTOMVAR3_'.$i.'}', $obj[$i]['custom3'], $aux);
                    $aux = str_replace('{PRODUCT_CUSTOMVAR4_'.$i.'}', $obj[$i]['custom4'], $aux);
                    $aux = str_replace('{PRODUCT_CUSTOMVAR5_'.$i.'}', $obj[$i]['custom5'], $aux);
                    $aux = str_replace('{PRODUCT_URL_'.$i.'}', $obj[$i]['link'], $aux);
                    $aux = str_replace('{PRODUCT_NAME_'.$i.'}', $obj[$i]['name'], $aux);
                    $aux = str_replace('{PRODUCT_IMG_'.$i.'}', $obj[$i]['link_image'], $aux);
                    $aux = str_replace('{PRODUCT_BRAND_'.$i.'}', $obj[$i]['brand'], $aux);

                    if($obj[$i]['link_image_2'] == '' || $obj[$i]['link_image_2'] == NULL)
                    {
                        $aux = str_replace('{PRODUCT_IMG_2_'.$i.'}', $obj[$i]['link_image'], $aux);
                    }
                    else
                    {
                        $aux = str_replace('{PRODUCT_IMG_2_'.$i.'}', $obj[$i]['link_image_2'], $aux);
                    }

                    $aux = str_replace('{PRODUCT_DESCRIPTION_'.$i.'}', $obj[$i]['description'], $aux);
                    
                    // VERIFICAR SE É 0 PARA EXIBIR UM SÓ
                    // estudar a melhor forma, talvez utilizar comentarios ao redor
                    if($obj[$i]['sale_price'] != '0,00' && ($obj[$i]['sale_price'] != $obj[$i]['price']))
                    {
                        // REMOVE O FALSE
                        $arrayAux = explode ('<!-- PRICE FALSE '.$i.' -->', $aux);
                        $aux = $arrayAux[0].''.$arrayAux[2];

                        $aux = str_replace('{VALUE_DE_'.$i.'}', $moeda.' '.$obj[$i]['price'], $aux);
                        $aux = str_replace('{VALUE_'.$i.'}', $moeda.' '.$obj[$i]['sale_price'], $aux);

                        $auxValue = '';

                        if($moeda != '$')
                        {
                            $auxValue = str_replace('.','',$obj[$i]['sale_price']);
                            $auxValue = str_replace(',','.',$auxValue);
                        }
                        else
                        {
                            $auxValue = str_replace(',','',$obj[$i]['sale_price']);
                        }

                        $sumValue += number_format(floatval($auxValue), 2, '.', '');

                        $auxValue = '';

                        if($moeda != '$')
                        {
                            $auxValue = str_replace('.','',$obj[$i]['price']);
                            $auxValue = str_replace(',','.',$auxValue);
                        }
                        else
                        {
                            $auxValue = str_replace(',','',$obj[$i]['price']);
                        }

                        $sumValueDe += number_format(floatval($auxValue), 2, '.', '');
                    }
                    else
                    {
                        // REMOVE O TRUE
                        $arrayAux = explode ('<!-- PRICE TRUE '.$i.' -->', $aux);

                        $aux = $arrayAux[0].''.$arrayAux[2];

                        $aux = str_replace('{VALUE_'.$i.'}', $moeda.' '.$obj[$i]['price'], $aux);

                        $auxValue = '';

                        if($moeda != '$')
                        {
                            $auxValue = str_replace('.','',$obj[$i]['price']);
                            $auxValue = str_replace(',','.',$auxValue);
                        }
                        else
                        {
                            $auxValue = str_replace(',','',$obj[$i]['price']);
                        }

                        $sumValue += number_format(floatval($auxValue), 2, '.', '');
                        $sumValueDe += number_format(floatval($auxValue), 2, '.', '');
                    }           
                    

                    // VERIFICAR SE É 0 PARA NÃO EXIBIR
                    // estudar a melhor forma, talvez utilizar comentarios ao redor
                    if($obj[$i]['mount'] != 0 && ($obj[$i]['mount'] != 1 || $idCli == '598'))
                    {
                        // REMOVE O FALSE
                        $arrayAux = explode ('<!-- AMOUNT FALSE '.$i.' -->', $aux);
                        $aux = $arrayAux[0].''.$arrayAux[2];

                        $aux = str_replace('{AMOUNT_PLOTS_'.$i.'}', $obj[$i]['mount'], $aux);
                        $aux = str_replace('{VALUE_PLOTS_'.$i.'}', $moeda.' '.$obj[$i]['amount'], $aux);
                    }
                    else
                    {
                        // REMOVE O TRUE
                        $arrayAux = explode ('<!-- AMOUNT TRUE '.$i.' -->', $aux);
                        $aux = $arrayAux[0].''.$arrayAux[2];
                    }

                    $arrayDiscount = explode ('<!-- DISCOUNT '.$i.' -->', $aux);
                    if(count($arrayDiscount) > 2)
                    {
                        if($obj[$i]['discount'] != 0 && $obj[$i]['discount'] != 100)
                        {
                            // REMOVE O FALSE
                            $arrayAux = explode ('<!-- DISCOUNT FALSE '.$i.' -->', $aux);
                            $aux = $arrayAux[0].''.$arrayAux[2];

                            $aux = str_replace('{PRODUCT_DISCOUNT_'.$i.'}', $obj[$i]['discount'], $aux);
                        }
                        else
                        {
                            // REMOVE O TRUE
                            $arrayAux = explode ('<!-- DISCOUNT TRUE '.$i.' -->', $aux);
                            $aux = $arrayAux[0].''.$arrayAux[2];
                        }
                    }   

                }

                $response = $aux;
            }

            if($sumValueDe != 0)
            {
                $response = str_replace('{DISCOUNT_SUM}', round(100 - (($sumValue * 100) / $sumValueDe), 0) , $response);
            }
            else
            {
                $response = str_replace('{DISCOUNT_SUM}', '' , $response);
            }
            
            if($moeda == '$')
            {
                $parmFormatOne = '.';
                $parmFormatTwo = ',';
            }
            else
            {
                $parmFormatOne = ',';
                $parmFormatTwo = '.';
            }

            $response = str_replace('{VALUE_SUM}', $moeda.' '.number_format($sumValue, 2, $parmFormatOne, $parmFormatTwo), $response);
            $response = str_replace('{VALUE_SUM_DE}', $moeda.' '.number_format($sumValueDe, 2, $parmFormatOne, $parmFormatTwo), $response);


            return $response;
        }
        else
        {
            return '';
        }
    }

    // Funções do HTML
    public static function get_HTML_Loja_Lateral($fullObj, $arrayConfig, $arrayWidgets)
    {
        $obj = $fullObj->getObj();
        $template = $arrayConfig['CONF_template'];
        $titulo = $arrayWidgets['WID_texto'];
        $subtitulo = $arrayWidgets['WID_sub_titulo'];
        $idWid = $arrayWidgets['WID_id'];
        $templateOverlay = $arrayConfig['CONF_template_overlay'];
        $template = $arrayConfig['CONF_template'];
        $thumb_link = $arrayWidgets['WID_thumb'];
        $banner_link = $arrayWidgets['WID_banner'];
        
        if(!empty($obj[0]['link']))
        {
            $sumValue = 0.00;
            $sumValueDe = 0.00;
                    
            
            $html = @file_get_contents("templates/overlay/kit_".$templateOverlay."/loja_lateral.html");

            if(empty($html))
            {
                $html = file_get_contents("templates/kit_".$template."/loja_lateral.html");
            }
            
            $html = str_replace('{TITLE_BLOCK}', $titulo, $html);
            $html = str_replace('{SUBTITLE_BLOCK}', $subtitulo, $html);
            $html = str_replace('{ID_WIDGET}', $idWid, $html);
            $html = str_replace('{BANNER_BLOCK}', 'https://roihero.com.br/widget/images/overlay/' . $banner_link, $html);
            $html = str_replace('{THUMB_BLOCK}', 'https://roihero.com.br/widget/images/overlay/' . $thumb_link, $html);
            
            $htmlArray = explode("<!-- REPEAT PRODUCTS -->", $html);
            
            if(count($htmlArray) > 2)
            {
                // Detalhando as partes do html a ser trabalhado
                $head = $htmlArray[0];
                $body = '';
                $htmlsEntreElementosDeRepeticao = array($htmlArray[2], $htmlArray[4]);
                $elementsRepeat = array($htmlArray[1], $htmlArray[3], $htmlArray[5]);
                $footer = $htmlArray[6];
                
                // Cravando 24, pois o array já será definido
                for($i = 0; $i < 24; $i++)
                {
                    if($i < 8) {
                        $aux = $elementsRepeat[0];
                    } else if($i < 16) {
                        $aux = $elementsRepeat[1];
                    } else {
                        $aux = $elementsRepeat[2];
                    }
                    
                    if(!empty($obj[$i]['link']))
                    {
                        
                        $aux = str_replace('{PRODUCT_ID}', $obj[$i]['id'], $aux);
                        $aux = str_replace('{PRODUCT_SKU}', $obj[$i]['sku'], $aux);
                        $aux = str_replace('{PRODUCT_URL}', $obj[$i]['link'], $aux);
                        $aux = str_replace('{PRODUCT_NAME}', $obj[$i]['name'], $aux);
                        $aux = str_replace('{PRODUCT_IMG}', $obj[$i]['link_image'], $aux);
                        $aux = str_replace('{PRODUCT_IMG_2}', $obj[$i]['link_image_2'], $aux);
                        $aux = str_replace('{PRODUCT_DESCRIPTION}', $obj[$i]['description'], $aux);
                        
                        // VERIFICAR SE É 0 PARA EXIBIR UM SÓ
                        // estudar a melhor forma, talvez utilizar comentarios ao redor
                        if(($obj[$i]['sale_price'] != '0,00') && ($obj[$i]['sale_price'] != $obj[$i]['price']))
                        {
                            // REMOVE O FALSE
                            $arrayAux = explode ('<!-- PRICE FALSE -->', $aux);
                            $aux = $arrayAux[0].''.$arrayAux[2];
                            
                            $aux = str_replace('{VALUE_DE}', 'R$ '.$obj[$i]['price'], $aux);
                            $aux = str_replace('{VALUE}', 'R$ '.$obj[$i]['sale_price'], $aux);
                            
                            $auxValue = '';
                            
                            $auxValue = str_replace('.','',$obj[$i]['sale_price']);
                            $auxValue = str_replace(',','.',$auxValue);
                            
                            $sumValue += number_format(floatval($auxValue), 2, '.', '');
                            
                            $auxValue = '';
                            
                            $auxValue = str_replace('.','',$obj[$i]['price']);
                            $auxValue = str_replace(',','.',$auxValue);
                            
                            $sumValueDe += number_format(floatval($auxValue), 2, '.', '');
                        }
                        else
                        {
                            // REMOVE O TRUE
                            $arrayAux = explode ('<!-- PRICE TRUE -->', $aux);
                            $aux = $arrayAux[0].''.$arrayAux[2];
                            
                            $aux = str_replace('{VALUE}', 'R$ '.$obj[$i]['price'], $aux);
                            
                            $auxValue = '';
                            
                            $auxValue = str_replace('.','',$obj[$i]['price']);
                            $auxValue = str_replace(',','.',$auxValue);
                            
                            $sumValue += number_format(floatval($auxValue), 2, '.', '');
                            $sumValueDe += number_format(floatval($auxValue), 2, '.', '');
                        }
                        
                        
                        // VERIFICAR SE É 0 PARA NÃO EXIBIR
                        // estudar a melhor forma, talvez utilizar comentarios ao redor
                        if($obj[$i]['mount'] != 0)
                        {
                            // REMOVE O FALSE
                            $arrayAux = explode ('<!-- AMOUNT FALSE -->', $aux);
                            $aux = $arrayAux[0].''.$arrayAux[2];
                            
                            $aux = str_replace('{AMOUNT_PLOTS}', $obj[$i]['mount'], $aux);
                            $aux = str_replace('{VALUE_PLOTS}', 'R$ '.$obj[$i]['amount'], $aux);
                        }
                        else
                        {
                            // REMOVE O TRUE
                            $arrayAux = explode ('<!-- AMOUNT TRUE -->', $aux);
                            $aux = $arrayAux[0].''.$arrayAux[2];
                        }
                        
                        $arrayDiscount = explode ('<!-- DISCOUNT -->', $aux);
                        if(count($arrayDiscount) > 2)
                        {
                            if($obj[$i]['discount'] != 0 && $obj[$i]['discount'] != 100)
                            {
                                // REMOVE O FALSE
                                $arrayAux = explode ('<!-- DISCOUNT FALSE -->', $aux);
                                $aux = $arrayAux[0].''.$arrayAux[2];
                                
                                $aux = str_replace('{PRODUCT_DISCOUNT}', $obj[$i]['discount'], $aux);
                            }
                            else
                            {
                                // REMOVE O TRUE
                                $arrayAux = explode ('<!-- DISCOUNT TRUE -->', $aux);
                                $aux = $arrayAux[0].''.$arrayAux[2];
                            }
                        }
                        
                        if($i == 8) {
                            $body .= $htmlsEntreElementosDeRepeticao[0] . $aux;
                        } else if($i == 16) {
                            $body .= $htmlsEntreElementosDeRepeticao[1] . $aux;
                        } else {
                            $body .= $aux;
                        }
                    }
                }
                
                $response = $head."".$body."".$footer;
            }
            else
            {
                // Arquivo sem <!-- REPEAT PRODUCTS -->
                return '';
            }
            
            $response = str_replace('{VALUE_SUM}', 'R$ '.number_format($sumValue, 2, ',', '.'), $response);
            $response = str_replace('{VALUE_SUM_DE}', 'R$ '.number_format($sumValueDe, 2, ',', '.'), $response);
            
            return $response;
        }
        else
        {
            return '';
        }
    }

    // Funções do HTML
    public static function get_HTML_sc($obj, $arrayConfig, $arrayWidgets, $viewsNow){
        $template = $arrayConfig['CONF_template'];
        $templateOverlay = $arrayConfig['CONF_template_overlay'];
        $formato = $arrayWidgets['WID_formato'];
        $idWid = $arrayWidgets['WID_id'];

        
        if($viewsNow != -1 && $formato == 43){

            $formato = self::getFormatName($formato);
            $html = @file_get_contents("templates/overlay/kit_".$templateOverlay."/".$formato.".html");

            if(empty($html)){
                $html = file_get_contents("templates/kit_".$template."/".$formato.".html");
            }
                       

            if(intval($viewsNow) < 3)
                $viewsNow = 3;
            
            $html = str_replace('{SC_PEOPLE}', $viewsNow, $html);
            $response = $html;

            return $response;
        }
        else
        {
            return '';
        }
    }

    public static function get_HTML_cj_3($obj, $arrayConfig, $arrayWidgets)
    {
        $template = $arrayConfig['CONF_template'];
        $moeda = $arrayConfig['CONF_moeda'];
        $idCli = $arrayWidgets['WID_id_cli'];
        $formato = $arrayWidgets['WID_formato'];
        $titulo = $arrayWidgets['WID_texto'];
        $numProd = $arrayWidgets['WID_num_prod'];
        $idWid = $arrayWidgets['WID_id'];

        $obj = array_values($obj);

        if(!empty($obj[0]['link']))
        {
            $sumValue = 0.00;
            $sumValueDe = 0.00;

            if($numProd == 0)
            {
                $numProd = self::getNumProd($formato);
            }

            $formato = self::getFormatName($formato);

            $html = file_get_contents("templates/kit_".$template."/".$formato.".html");

            $html = str_replace('{TITLE_BLOCK}', $titulo, $html);
            $html = str_replace('{ID_WIDGET}', $idWid, $html);

            $htmlArray = explode("<!-- REPEAT PRODUCTS -->", $html);

            if(count($htmlArray) > 4)
            {
                $body = '';
                $contElements = 0;                
                $footer = $htmlArray[4];
                $interseccao = $htmlArray[2];
                $elementRepeat = array($htmlArray[0], $htmlArray[1], $htmlArray[3]);

                $auxMat = (count($obj) - 1) / 2;

                if((count($obj) - 1) % 2 == 0)
                {
                     //par                    
                    $numProdRep = array(1, $auxMat, $auxMat);
                } 
                else 
                {
                     //impar
                    $numProdRep = array(1, $auxMat + 1, $auxMat);
                }

                for($j = 0; $j < 3; $j++)
                {
                    
                    $limite = $numProdRep[$j];

                    for($i = 0; $i < $limite; $i++)
                    {
                        $aux = $elementRepeat[$j];

                        $aux = str_replace('{PRODUCT_ID}', $obj[$contElements]['id'], $aux);
                        $aux = str_replace('{PRODUCT_SKU}', $obj[$contElements]['sku'], $aux);
                        $aux = str_replace('{PRODUCT_CUSTOMVAR1}', $obj[$contElements]['custom1'], $aux);
                        $aux = str_replace('{PRODUCT_CUSTOMVAR2}', $obj[$contElements]['custom2'], $aux);
                        $aux = str_replace('{PRODUCT_CUSTOMVAR3}', $obj[$contElements]['custom3'], $aux);
                        $aux = str_replace('{PRODUCT_CUSTOMVAR4}', $obj[$contElements]['custom4'], $aux);
                        $aux = str_replace('{PRODUCT_CUSTOMVAR5}', $obj[$contElements]['custom5'], $aux);
                        $aux = str_replace('{PRODUCT_URL}', $obj[$contElements]['link'], $aux);
                        $aux = str_replace('{PRODUCT_NAME}', $obj[$contElements]['name'], $aux);
                        $aux = str_replace('{PRODUCT_IMG}', $obj[$contElements]['link_image'], $aux);

                        if($obj[$contElements]['link_image_2'] == '' || $obj[$contElements]['link_image_2'] == NULL)
                        {
                            $aux = str_replace('{PRODUCT_IMG_2}', $obj[$contElements]['link_image'], $aux);
                        }
                        else
                        {
                            $aux = str_replace('{PRODUCT_IMG_2}', $obj[$contElements]['link_image_2'], $aux);
                        }

                        $aux = str_replace('{PRODUCT_DESCRIPTION}', $obj[$contElements]['description'], $aux);
                        
                        // VERIFICAR SE É 0 PARA EXIBIR UM SÓ
                        // estudar a melhor forma, talvez utilizar comentarios ao redor
                        if($obj[$contElements]['sale_price'] != '0,00' && ($obj[$contElements]['sale_price'] != $obj[$contElements]['price']))
                        {
                            // REMOVE O FALSE
                            $arrayAux = explode ('<!-- PRICE FALSE -->', $aux);
                            $aux = $arrayAux[0].''.$arrayAux[2];

                            $aux = str_replace('{VALUE_DE}', $moeda.' '.$obj[$contElements]['price'], $aux);
                            $aux = str_replace('{VALUE}', $moeda.' '.$obj[$contElements]['sale_price'], $aux);

                            $auxValue = '';

                            if($moeda != '$')
                            {
                                $auxValue = str_replace('.','',$obj[$contElements]['sale_price']);
                                $auxValue = str_replace(',','.',$auxValue);
                            }
                            else
                            {
                                $auxValue = str_replace(',','',$obj[$contElements]['sale_price']);
                            }

                            $sumValue += number_format(floatval($auxValue), 2, '.', '');

                            $auxValue = '';

                            if($moeda != '$')
                            {
                                $auxValue = str_replace('.','',$obj[$contElements]['price']);
                                $auxValue = str_replace(',','.',$auxValue);
                            }
                            else
                            {
                                $auxValue = str_replace(',','',$obj[$contElements]['price']);
                            }

                            $sumValueDe += number_format(floatval($auxValue), 2, '.', '');
                        }
                        else
                        {
                            // REMOVE O TRUE
                            $arrayAux = explode ('<!-- PRICE TRUE -->', $aux);

                            $aux = $arrayAux[0].''.$arrayAux[2];

                            $aux = str_replace('{VALUE}', $moeda.' '.$obj[$contElements]['price'], $aux);

                            $auxValue = '';

                            if($moeda != '$')
                            {
                                $auxValue = str_replace('.','',$obj[$contElements]['price']);
                                $auxValue = str_replace(',','.',$auxValue);
                            }
                            else
                            {
                                $auxValue = str_replace(',','',$obj[$contElements]['price']);
                            }

                            $sumValue += number_format(floatval($auxValue), 2, '.', '');
                            $sumValueDe += number_format(floatval($auxValue), 2, '.', '');
                        }           
                        

                        // VERIFICAR SE É 0 PARA NÃO EXIBIR
                        // estudar a melhor forma, talvez utilizar comentarios ao redor
                        if($obj[$contElements]['mount'] != 0 && ($obj[$contElements]['mount'] != 1 || $idCli == '598'))
                        {
                            // REMOVE O FALSE
                            $arrayAux = explode ('<!-- AMOUNT FALSE -->', $aux);
                            $aux = $arrayAux[0].''.$arrayAux[2];

                            $aux = str_replace('{AMOUNT_PLOTS}', $obj[$contElements]['mount'], $aux);
                            $aux = str_replace('{VALUE_PLOTS}', $moeda.' '.$obj[$contElements]['amount'], $aux);
                        }
                        else
                        {
                            // REMOVE O TRUE
                            $arrayAux = explode ('<!-- AMOUNT TRUE -->', $aux);
                            $aux = $arrayAux[0].''.$arrayAux[2];
                        }

                        $arrayDiscount = explode ('<!-- DISCOUNT -->', $aux);
                        if(count($arrayDiscount) > 2)
                        {
                            if($obj[$contElements]['discount'] != 0 && $obj[$contElements]['discount'] != 100)
                            {
                                // REMOVE O FALSE
                                $arrayAux = explode ('<!-- DISCOUNT FALSE -->', $aux);
                                $aux = $arrayAux[0].''.$arrayAux[2];

                                $aux = str_replace('{PRODUCT_DISCOUNT}', $obj[$contElements]['discount'], $aux);
                            }
                            else
                            {
                                // REMOVE O TRUE
                                $arrayAux = explode ('<!-- DISCOUNT TRUE -->', $aux);
                                $aux = $arrayAux[0].''.$arrayAux[2];
                            }
                        }

                        $contElements++;
                        $body .= $aux;
                    }

                    if($j == 1)
                    {
                        $body .= $interseccao;
                    }
                    
                }

                $response = $body."".$footer;

                if($sumValueDe != 0)
                {
                    $response = str_replace('{DISCOUNT_SUM}', round(100 - (($sumValue * 100) / $sumValueDe), 0) , $response);
                }
                else
                {
                    $response = str_replace('{DISCOUNT_SUM}', '' , $response);
                }
                
                if($moeda == '$')
                {
                    $parmFormatOne = '.';
                    $parmFormatTwo = ',';
                }
                else
                {
                    $parmFormatOne = ',';
                    $parmFormatTwo = '.';
                }

                $response = str_replace('{VALUE_SUM}', $moeda.' '.number_format($sumValue, 2, $parmFormatOne, $parmFormatTwo), $response);
                $response = str_replace('{VALUE_SUM_DE}', $moeda.' '.number_format($sumValueDe, 2, $parmFormatOne, $parmFormatTwo), $response);


                return $response;
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
    
    public static function getFormatName($formato)
    {
        switch ($formato)
        {
            case 1:
                $formato = 'prateleira';
                break;
            case 2:
                $formato = 'prateleira_dupla';
                break;
            case 3:
                $formato = 'carrossel';
                break;
            case 4:
                $formato = 'compre_junto_2';
                break;
            case 5:
                $formato = 'nao_va_embora';
                break;
            case 6:
                $formato = 'oferta_limitada';
                break;
            case 7:
                $formato = 'barra_de_busca';
                break;
            case 8:
                $formato = 'vitrine';
                break;
            case 10:
                $formato = 'rodape_produto';
                break;
            case 11:
                $formato = 'totem';
                break;
            case 12:
                $formato = 'compre_junto_3';
                break;
            case 13:
                $formato = 'remarketing';
                break;
            case 14:
                $formato = 'carrinho_complementar';
                break;
            case 15:
                $formato = 'template_custom';
                break;
            case 16:
                $formato = 'compre_junto_dinamico';
                break;
            case 41:
                $formato = 'loja_lateral';
                break;
            case 42:
                $formato = 'autocomplete';
                break;
            case 44:
                $formato = 'scroll_checkout';
                break;
            case 45:
                $formato = 'rec_cart_onsite';
                break;
        }
        
        return $formato;
    }
    
    /**
     * Um de/para que retorna o número de produtos que serão exibidos na tela
     * 
     * @param number $formato
     * @return number
     */
    public static function getNumProd($formato)
    {
        switch ($formato)
        {
            case 1:
                $numProd = 4;
                break;
            case 2:
                $numProd = 8;
                break;
            case 3:
                $numProd = 24;
                break;
            case 4:
                $numProd = 2;
                break;
            case 5:
                $numProd = 24;
                break;
            case 6:
                $numProd = 1;
                break;
            case 7:
                $numProd = 24;
                break;
            case 8:
                $numProd = 24;
                break;
            case 10:
                $numProd = 24;
                break;
            case 11:
                $numProd = 3;
                break;
            case 12:
                $numProd = 3;
                break;
            case 13:
                $numProd = 24;
                break;
            case 14:
                $numProd = 24;
                break;
            case 15:
                $numProd = 24;
                break;
            case 16:
                $numProd = 8;
                break;
        }
        
        return $numProd;
    }
    
    public static function minify_html($input)
    {
        if(trim($input) === "") return $input;
        // Remove extra white-space(s) between HTML attribute(s)
        $input = preg_replace_callback('#<([^\/\s<>!]+)(?:\s+([^<>]*?)\s*|\s*)(\/?)>#s', function($matches) {
            return '<' . $matches[1] . preg_replace('#([^\s=]+)(\=([\'"]?)(.*?)\3)?(\s+|$)#s', ' $1$2', $matches[2]) . $matches[3] . '>';
        }, str_replace("\r", "", $input));
            // Minify inline CSS declaration(s)
            if(strpos($input, ' style=') !== false) {
                $input = preg_replace_callback('#<([^<]+?)\s+style=([\'"])(.*?)\2(?=[\/\s>])#s', function($matches) {
                    return '<' . $matches[1] . ' style=' . $matches[2] . minify_css($matches[3]) . $matches[2];
                }, $input);
            }
            if(strpos($input, '</style>') !== false) {
                $input = preg_replace_callback('#<style(.*?)>(.*?)</style>#is', function($matches) {
                    return '<style' . $matches[1] .'>'. minify_css($matches[2]) . '</style>';
                }, $input);
            }
            if(strpos($input, '</script>') !== false) {
                $input = preg_replace_callback('#<script(.*?)>(.*?)</script>#is', function($matches) {
                    return '<script' . $matches[1] .'>'. minify_js($matches[2]) . '</script>';
                }, $input);
            }
            return preg_replace(
                    array(
                            // t = text
                            // o = tag open
                            // c = tag close
                            // Keep important white-space(s) after self-closing HTML tag(s)
                            '#<(img|input)(>| .*?>)#s',
                            // Remove a line break and two or more white-space(s) between tag(s)
                            '#(<!--.*?-->)|(>)(?:\n*|\s{2,})(<)|^\s*|\s*$#s',
                            '#(<!--.*?-->)|(?<!\>)\s+(<\/.*?>)|(<[^\/]*?>)\s+(?!\<)#s', // t+c || o+t
                            '#(<!--.*?-->)|(<[^\/]*?>)\s+(<[^\/]*?>)|(<\/.*?>)\s+(<\/.*?>)#s', // o+o || c+c
                            '#(<!--.*?-->)|(<\/.*?>)\s+(\s)(?!\<)|(?<!\>)\s+(\s)(<[^\/]*?\/?>)|(<[^\/]*?\/?>)\s+(\s)(?!\<)#s', // c+t || t+o || o+t -- separated by long white-space(s)
                            '#(<!--.*?-->)|(<[^\/]*?>)\s+(<\/.*?>)#s', // empty tag
                            '#<(img|input)(>| .*?>)<\/\1>#s', // reset previous fix
                            '#(&nbsp;)&nbsp;(?![<\s])#', // clean up ...
                            '#(?<=\>)(&nbsp;)(?=\<)#', // --ibid
                            // Remove HTML comment(s) except IE comment(s)
                            '#\s*<!--(?!\[if\s).*?-->\s*|(?<!\>)\n+(?=\<[^!])#s'
                    ),
                    array(
                            '<$1$2</$1>',
                            '$1$2$3',
                            '$1$2$3',
                            '$1$2$3$4$5',
                            '$1$2$3$4$5$6$7',
                            '$1$2$3',
                            '<$1$2',
                            '$1 ',
                            '$1',
                            ""
                    ),
                    $input);
    }
}
?>
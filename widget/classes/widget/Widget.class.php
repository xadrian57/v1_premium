<?php
namespace roihero\widget;

use roihero\widget\util\Util;
use roihero\widget\inteligencia\FactoryInteligencia;

/**
 * Esta classe, possui a implementação básica dos Widgets.
 * Caso surja uma nova necessidade, uma outra classe, irmã desta, deverá ser criada.
 *  
 * @author tiago
 */
class Widget extends AbstractWidget {

    /**
     * Executa o processamento principal
     */
    public function executar() {
        
        if($this->conToken) {
            $sql = 'call sValidateTokenTagflag(\'' . $this->getTokenTagflag() . '\')';
            $result = mysqli_query($this->conToken, $sql);
            $row = mysqli_fetch_array($result);
            
            if('1' != $row['result']) {
                echo '{"status":' . $row['result'] . ',"listaErros":["Token tagflag inválido."],"listaAlertas":[],"errors":[]}';
                @mysqli_close($this->conToken);
                exit;
            }
            
            @mysqli_close($this->conToken);
        }
        
        // Verifica o preenchimento do id cript do cliente
        if(!empty($this->idcli_cryp))
        {
            $selectCli = "SELECT cliente.CLI_id FROM cliente INNER JOIN plano ON cliente.CLI_id = plano.PLAN_id_cli WHERE CLI_id_owa = '$this->idcli_cryp' AND CLI_ativo = 1 AND PLAN_status != 4";
            $resultCli = mysqli_query($this->conCad, $selectCli);
            
            if(mysqli_num_rows($resultCli) > 0)
            {
                $arrayCli = mysqli_fetch_array($resultCli);
                
                $this->idcli = $arrayCli['CLI_id'];
                
                $tokenTagflag = $this->getTokenTagflag();
                
                if(empty($tokenTagflag)) {
                    
                    // SELECT WIDGETS
                    $selectWidgets = "SELECT WID_id,
                                             WID_id_cli,
                                             WID_prod_esg,
                                             WID_show,
                                             WID_hide,
                                             WID_inteligencia,
                                             WID_inject,
                                             WID_div_type,
                                             WID_updown,
                                             WID_div,
                                             WID_placement,
                                             WID_formato,
                                             WID_texto,
                                             WID_sub_titulo,
                                             WID_utm,
                                             WID_dias,
                                             WID_num_prod,
                                             WID_link_banner,
                                             WID_banner
                                      FROM widget WHERE WID_id_cli = '$this->idcli' AND WID_inteligencia != 22 AND WID_status = 1 " . $this->queryPage();
                } else {
                    // Select realizado no fluxo do tagflag
                    $selectWidgets = "SELECT WID_id,
                                             WID_id_cli,
                                             WID_prod_esg,
                                             WID_show,
                                             WID_hide,
                                             WID_inteligencia,
                                             WID_inject,
                                             WID_div_type,
                                             WID_updown,
                                             WID_div,
                                             WID_placement,
                                             WID_formato,
                                             WID_texto,
                                             WID_sub_titulo,
                                             WID_utm,
                                             WID_dias,
                                             WID_num_prod,
                                             WID_link_banner,
                                             WID_banner
                                      FROM widget WHERE WID_id = " . $this->widgetId;
                }
                
                
                $resultWidgets = mysqli_query($this->conCad, $selectWidgets);
                
                if(mysqli_num_rows($resultWidgets) > 0)
                {
                    while($widgetProps = mysqli_fetch_array($resultWidgets))
                    {
                        // Recupera se o estoque está esgotado
                        $estoque = $this->getEstoque($widgetProps['WID_prod_esg']);
                        
                        if(Util::showHide($this->url, $widgetProps) && !$estoque)
                        {
                            $selectConfig = "SELECT CONF_moeda, CONF_desc_boleto FROM config WHERE CONF_id_cli = '$this->idcli'";
                            $resultConfig = mysqli_query($this->conCad, $selectConfig);
                            
                            $arrayConfig = mysqli_fetch_array($resultConfig);

                            $this->descBoleto = $arrayConfig['CONF_desc_boleto'];

                            $this->idWid = $widgetProps['WID_id'];
                            $this->$widInteligencia = $widgetProps['WID_inteligencia'];
                            
                            // SELECT WIDGET_CONFIG
                            $selectWidgetConfig = "SELECT WC_collection FROM widget_config WHERE WC_id_wid = ".$this->idWid;
                            $resultWidgetConfig = mysqli_query($this->conCad, $selectWidgetConfig);
                            
                            if(mysqli_num_rows($resultWidgetConfig) > 0)
                            {
                                $widgetConfig = mysqli_fetch_array($resultWidgetConfig);
                            }
                            else
                            {
                                $widgetConfig['WC_collection'] = '';
                            }
                            
                            $inteligencia = FactoryInteligencia::getInteligencia($this->$widInteligencia, $this, $widgetProps, $widgetConfig);
                            if($inteligencia) {
                                $inteligencia->processar();
                            }
                            
                            $this->obj = Util::formatValues($this->obj, $arrayConfig['CONF_moeda']);
                            
                            $this->inject($widgetProps);
                        }
                        
                        $this->obj = [];
                    }
                    
                    // ECHO RESPONSE
                    // ADD TRUSTVOX DA LOJA
                    // FECHAR AS TAGS DE JSON
                    echo '{"widgets":['.$this->JSON_widgets.']}';
                }
                else
                {
                    echo '{"erro":"Esta página não possui widgets."}';
                }
            }
            else
            {
                echo '{"erro":"Cliente não encontrado ou desativado."}';
            }
        }
        else
        {
            echo '{"erro":"Id do cliente vazio."}';
        }
    }
}


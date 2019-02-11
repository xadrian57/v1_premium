<?php
namespace roihero\widget\inteligencia;

/**
 * Esta classe, realiza o processamento para retornar 
 * os mais vendidos de uma categoria, que foi definica
 * pelo usuário do dashboard
 * 
 * @author tiago
 */
class SimilarPorParametrosInteligencia extends AbstractInteligencia {
    
    /**
     * 
     * {@inheritDoc}
     * @see \roihero\widget\inteligencia\IInteligencia::processar()
     */
    public function processar() {

      if($this->widget->getIdCli() == '756')
      {
         $paramOrder = "XML_custom5 DESC, XML_click_" . $this->widgetProps['WID_dias'];
      }
      else
      {
         $paramOrder = "XML_click_" . $this->widgetProps['WID_dias'];
      }
      
      $select = 'SELECT tx_param_pai, tx_param_filho FROM widget_config WHERE WC_id_wid = ' . $this->widget->getIdWid();  
      
      $result = mysqli_query($this->widget->getConCad(), $select);
      $linha = mysqli_fetch_array($result);

      $paramPai = $this->widget->getParametroXML($linha['tx_param_pai']);
      $paramFilho = $this->widget->getParametroXML($linha['tx_param_filho']);

      $paramPai = strtoupper($paramPai);
      $paramPai = str_replace(' ', ' +', $paramPai);

      $paramFilho = strtoupper($paramFilho);
      $paramFilho = str_replace(' ', ' +', $paramFilho);
      
      $select = "SELECT ".$this->XML_select."
                 FROM XML_". $this->widget->getIdCli() ."
                 WHERE XML_id != '" . $this->widget->getProdId() . "'
                 AND XML_availability = 1
                 AND MATCH(XML_". $linha['tx_param_pai'] .") AGAINST(\"+" . $paramPai . "\" IN BOOLEAN MODE)
                 ". checkFilho($linha['tx_param_filho'], $paramFilho) ."
                 GROUP BY XML_link
                 ORDER BY ". $paramOrder ." DESC 
                 LIMIT " . $this->numMaxProdutos;
      
      $result = mysqli_query($this->widget->getConDados(), $select);

      $numProd = 0;
      if($result && mysqli_num_rows ($result) > 0 )
      {
          while($linha = mysqli_fetch_array($result))
          {
              //setOBJ($idWid, $array, $utm, $index=null)
              if($this->setOBJ($this->widget->getIdWid(), $linha, $this->widgetProps['WID_utm'], $numProd))
              {
                  $numProd++;

                  if($numProd >= $this->numMaxProdutos)
                  {
                      break;
                  }
              }
          }
      }
    }

    private function checkFilho($filho, $paramFilho)
    {

      if(!empty($paramFilho) && $paramFilho != '')
      {
        return "AND MATCH(XML_". $filho .") AGAINST(\"+" . $paramFilho . "\" IN BOOLEAN MODE)";
      }
      else
      {
        return "";
      }      
    }

}
?>
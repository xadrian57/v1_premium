<?php
namespace roihero\search\inteligencia;

/**
 * Esta classe, realiza o processamento para retornar 
 * os mais vendidos de uma categoria, que foi definica
 * pelo usuário do dashboard
 * 
 * @author moises
 */
class SimilarMarcaInteligencia extends AbstractInteligencia {
    
    /**
     * 
     * {@inheritDoc}
     * @see \roihero\search\inteligencia\IInteligencia::processar()
     */
    public function processar() {
      
      $select = "SELECT ".$this->XML_select."
                 FROM XML_". $this->search->getIdCli() ."
                 WHERE XML_availability = 1
                 AND MATCH(XML_brand) AGAINST(\"+ " . $this->search->getBrandProdSearch(0) . " *\" IN BOOLEAN MODE)
                 GROUP BY XML_link
                 ORDER BY XML_click_7 DESC 
                 LIMIT " . $this->numMaxProdutos;
      
      $result = mysqli_query($this->search->getConDados(), $select);

      $numProd = 0;
      if($result && mysqli_num_rows ($result) > 0 )
      {
          while($linha = mysqli_fetch_array($result))
          {
              //setOBJ($idWid, $array, $utm, $index=null)
              if($this->setOBJ($linha, $this->search->getUtm(), $numProd))
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
}
?>
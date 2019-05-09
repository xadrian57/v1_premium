<?php
namespace roihero\search;

use roihero\search\util\Util;
use roihero\search\inteligencia\FactoryInteligencia;

/**
 * Esta classe, possui a implementação básica das Searchs.
 * Caso surja uma nova necessidade, uma outra classe, irmã desta, deverá ser criada.
 *  
 * @author moises
 */
class Search extends AbstractSearch {

    /**
     * Executa o processamento principal
     */
    public function executar() 
    {        
        if(!empty($this->idcli_cryp))
        {
            $selectCli = "SELECT CLI_id FROM cliente WHERE CLI_id_owa = '". $this->idcli_cryp ."' AND CLI_ativo = 1";
            $resultCli = mysqli_query($this->conCad, $selectCli);

            if(mysqli_num_rows($resultCli) > 0)
            {
                $arrayCli = mysqli_fetch_array($resultCli);

                $this->idcli = $arrayCli['CLI_id'];

                $this->termo = $this->getSinonimo($this->termo);

                $this->setDescBoleto();

                $this->termoSemFone = $this->termo;

                $this->termo = Util::fonetizar($this->termo);

                if($this->termo != '')
                {                    
                    $busca = str_replace(' ', '* ', $this->termo);

                    if(!empty($busca))
                    {
                        $select = "SELECT id, titulo_fonetico, click, titulo
                               FROM BUSCA_". $this->idcli ."
                               WHERE disponibilidade IS NOT NULL AND ". $this->consulta() ."
                               ". $this->usarCustom() ."
                               ORDER BY disponibilidade DESC";
                    
                        $result = mysqli_query($this->conBusca, $select);                        
                
                        if($result && mysqli_num_rows ($result) > 0 )
                        {
                            while($linha = mysqli_fetch_array($result))
                            {
                                $this->prodsBusca[] = Util::geraArray($linha);
                            }
                        }
                    }

                    if(count($this->prodsBusca))
                    {
                        $this->prodsBusca = Util::scoreFonetico($this->termo, $this->prodsBusca);
                        $this->prodsBusca = Util::score($this->termoSemFone, $this->prodsBusca);                       

                        if(count($this->prodsBusca) > 0)
                        {
                            $this->prodsBusca = array_slice($this->prodsBusca, 0, $this->limite, true);
                            $arraySearch['search'] = $this->geraProdsFonetico($this->prodsBusca);
                            $this->prodsBusca = $arraySearch;
                        }
                        
                        if(count($this->prodsBusca) > 0)
                        {
                            // CHAMA AS INTELIGENCIAS
                            for($i=0; $i < count($this->inteligencia); $i++)
                            {
                                $inteligencia = FactoryInteligencia::getInteligencia($this->inteligencia[$i], $this);
                                if($inteligencia) {
                                    $inteligencia->processar();
                                }

                                $this->prodsBusca[$this->inteligencia[$i]] = $this->obj;

                                $this->obj = [];
                            }

                            echo json_encode($this->prodsBusca);
                        }
                        else
                        {
                            echo "[]";
                        }
                    }
                    else
                    {
                        echo "[]";
                    }
                }
                else
                {
                    echo '{"erro":"Termo de busca vazio."}';
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


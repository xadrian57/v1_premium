<?php
namespace roihero\search;

use roihero\search\util\Util;

/**
 * Classe abstrata, que contém métodos prontos para a herança das searchs
 *  
 * @author moises
 */
abstract class AbstractSearch 
{
	protected $conCad;
    protected $conDados;
    protected $conBusca;

    protected $idcli_cryp;
    protected $limite;
    protected $termo;
    protected $inteligencia;
    protected $idcli;
    protected $descBoleto;
    protected $termoSemFone;

    protected $obj = [];
    protected $utm = 'utmi_medium=roihero&utmi_content=roihero&rh_int=search';

    protected $prodsBusca = [];

    public function setParametros($arrayRequest)
    {
    	//CONEXÃO BD Dados
		include '../bd/conexao_bd_dados.php';
		$this->conDados = $conDados;

        mysqli_set_charset($this->conDados, 'utf8');

		//CONEXÃO BD CADASTRO
		include '../bd/conexao_bd_cadastro.php';
		$this->conCad = $conCad;

		//CONEXÃO BD BUSCA
		include '../bd/conexao_bd_busca.php';
		$this->conBusca = $conBusca;

		$this->termo = Util::UP_CASE(urldecode(trim(mysqli_escape_string($conCad, $arrayRequest['termo']))));
		$this->idcli_cryp = mysqli_escape_string($conCad, $arrayRequest['idcli']);
	    

	    if(array_key_exists('limite', $arrayRequest)) 
	    {
            $this->limite = mysqli_escape_string($conCad, $arrayRequest['limite']);
        }
        else
        {
        	// PADRÃO DE LIMITE
        	$this->limite = 24;
        }

        if(array_key_exists('inteligencia', $arrayRequest)) 
	    {
            $this->inteligencia = explode(',', mysqli_escape_string($conCad, $arrayRequest['inteligencia']));
        }
    }

    protected function inject($arrayWidgets) {
        $this->JSON_widgets = Util::set_JSON_widget($this->JSON_widgets, $inject, $this->idWid, $html, $arrayWidgets, $this->obj);
    }

    public function getObj() {
        return $this->obj;
    }
    
    public function setObj($obj) {
        $this->obj = $obj;
    }

    public function getConDados() {
        return $this->conDados;
    }

    public function getUtm() {
        return $this->utm;
    }

    public function getNameProdSearch($index)
    {
        return urldecode($this->prodsBusca['search'][$index]['title']);
    }

    public function getTypeProdSearch($index)
    {
        return urldecode($this->prodsBusca['search'][$index]['type']);
    }

    public function getBrandProdSearch($index)
    {
        return urldecode($this->prodsBusca['search'][$index]['brand']);
    }

    public function getSinonimo($termo)
    {
    	$select = "SELECT tx_pesquisado, tx_retornado FROM busca WHERE id_cli = '". $this->idcli ."'";
		$result = mysqli_query($this->conCad, $select);

		if(mysqli_num_rows($result) > 0)
		{
			$arrayBusca = explode(' ', $termo);

			return Util::retornaSinonimo($arrayBusca, $result);
		}
		else
		{
			return $termo;
		}
    }

    public function setDescBoleto()
    {
    	$select = "SELECT CONF_desc_boleto FROM config WHERE CONF_id_cli = '". $this->idcli ."'";
		$result = mysqli_query($this->conCad, $select);

		$arrayConf = mysqli_fetch_array($result);

		$this->descBoleto = $arrayConf['CONF_desc_boleto'];
    }

    public function getDescBoleto()
    {
    	return $this->descBoleto;
    }

    public function getIdCli()
    {
        return $this->idcli;
    }

    public function consulta()
	{
		return "MATCH(titulo) AGAINST(\"+ " . str_replace(' ', '* ', $this->termoSemFone) . "*\" IN BOOLEAN MODE)
				OR MATCH(titulo) AGAINST(\"+ " . Util::trataPlural(str_replace(' ', '* ', $this->termoSemFone)) . "*\" IN BOOLEAN MODE)
				OR MATCH(titulo_fonetico) AGAINST(\"+ " . str_replace(' ', '* ', $this->termo) . "*\" IN BOOLEAN MODE)";
	}

	public function usarCustom()
	{
		$busca = str_replace(' ', '* ', $this->termoSemFone);

		if($this->idcli == 292 || $this->idcli == 1210)
		{
			return " OR MATCH(custom_1) AGAINST(\"+ " . $busca . "*\" IN BOOLEAN MODE) ";
		}
		else if($this->idcli == 598) 
		{
			return " OR id = '". $busca ."' ";
		}
		else if($this->idcli == 116 || $this->idcli == 1880 || $this->idcli == 2005)
		{
			return " OR MATCH(custom_1) AGAINST(\"+ " . $busca . "*\" IN BOOLEAN MODE) OR id = '". $busca ."' ";
		}
        else if($this->idcli == 2012)
        {
            return " OR MATCH(custom_1) AGAINST(\"+ " . $busca . "*\" IN BOOLEAN MODE) OR MATCH(custom_1) AGAINST(\"+ 00" . $busca . "*\" IN BOOLEAN MODE) OR id = '". $busca ."' ";
        }
		else
		{
			return " OR custom_1 = '" . $busca . "' OR custom_2 = '" . $busca . "' OR id = '". $busca ."' ";
		}
	}

	public function campos()
	{		
		return "XML_id, 
                XML_sku, 
                XML_titulo, 
                XML_availability, 
                XML_price, 
                XML_sale_price, 
                XML_link, 
                XML_image_link, 
                XML_type,
                XML_brand, 
                XML_nparcelas, 
                XML_vparcela, 
                XML_click_7, 
                XML_desconto";
	}

	public function geraProdsFonetico($post)
	{
		$ids = '';

		for($i=0; $i < count($post); $i++)
		{
			$ids .= "','". $post[$i]['id'];
		}

		$select = "SELECT ". $this->campos() ."
           FROM XML_".$this->idcli."
           WHERE (XML_availability = 1 OR XML_availability = 0)
           AND XML_id IN ('". $ids ."')
           ORDER BY XML_availability DESC";	
    
    	$result = mysqli_query($this->conDados, $select);

    	if($result && mysqli_num_rows ($result) > 0 )
        {
            while($linha = mysqli_fetch_array($result))
            {	
            	$auxScore = 0;
            	for($x=0; $x < count($post); $x++)
            	{
            		if($linha['XML_id'] == $post[$x]['id'])
            		{
            			$auxScore = $post[$x]['score'];            			
            			break;
            		}
            	}

               	$posts[] = Util::geraArrayXML($linha, $this->getDescBoleto(), $auxScore);
            }
        }

     	$posts = Util::array_multi_sort($posts,'score','venda'); 

        return $posts;
	}

}

?>
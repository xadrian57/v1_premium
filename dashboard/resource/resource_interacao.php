<?php
// conexão com banco de dados
require_once('../../bd/conexao_bd_cadastro.php');
require_once('../../bd/conexao_bd_dashboard.php');
require_once('../../bd/conexao_bd_dados.php');

// id cliente
$idCLI = mysqli_real_escape_string($conCad,$_POST['id']);

//datapicker
$begin = mysqli_real_escape_string($conCad,$_POST['begin']);
$end = mysqli_real_escape_string($conCad,$_POST['end']);

// ultimos 7 dias
$beginGrafico = mysqli_real_escape_string($conCad,$_POST['beginGrafico']);
$endGrafico = mysqli_real_escape_string($conCad,$_POST['endGrafico']);

$beginGrafico = str_replace('/','-',$beginGrafico);
$endGrafico = str_replace('/','-',$endGrafico);

function reverseDate($date) {
    $date = str_replace('/','-',$date);
    $date = explode('-', $date);
    return $date[2].'-'.$date[1].'-'.$date[0];
}

$end = reverseDate($end);
$begin = reverseDate($begin);

$beginGrafico = reverseDate($beginGrafico);
$endGrafico = reverseDate($endGrafico);

function getData($conCad, $conDash, $conDados, $idCLI, $begin, $end, $beginGrafico, $endGrafico)
{
	// nome do cliente
	$queryNome = mysqli_query($conCad, 'SELECT CLI_nome FROM cliente WHERE CLI_id = "$id"');
	$fetchNome = mysqli_fetch_array($queryNome);
	$nomeCLI = $fetchNome['CLI_nome'];


//dados principais
	$selectDados = 
	"SELECT 
		sum(REL_faturado_rh) as faturadoRH, 
		sum(REL_faturado_loja) as faturadoLoja, 
		sum(REL_transacoes_rh) as transacoesRH, 
		sum(REL_sessoes_rh) as sessoesRH, 
		sum(REL_impressoes_rh) as impressoesRH, 
		sum(REL_cliques_rh) as cliquesRH, 
		sum(REL_carrinhos_rh) as carrinhosRH 
	FROM 
		rel_".$idCLI." 
	WHERE 
		DATE(REL_data) BETWEEN '$begin' AND '$end'";
	$queryDados = mysqli_query($conDash, $selectDados);

	$dados = [];

	if ($queryDados){
		$dados = mysqli_fetch_array($queryDados);
		$faturadoRH = $dados['faturadoRH']; 
		$faturadoLoja = $dados['faturadoLoja'];
		$sessoesRH = $dados['sessoesRH'];
		$transacoesRH = $dados['transacoesRH'];

		$temp = ($faturadoLoja + $faturadoRH);
		$participacaoFaturado = ($temp != 0) ? ($faturadoRH * 100) / $temp : 0;
		
		$aumentoFaturado = ($faturadoLoja != 0) ? ($faturadoRH * 100) / $faturadoLoja : 0;
		
		$impressoesRH = $dados['impressoesRH'];
		$cliquesRH = $dados['cliquesRH'];
		$carrinhosRH = $dados['carrinhosRH'];
		
		$ticketMedioRH = ($transacoesRH != 0) ? $faturadoRH / $transacoesRH : 0;
		
		$conversaoRH = ($sessoesRH != 0) ? ($transacoesRH * 100) / $sessoesRH : 0;
		
		$aumentoFaturado = round($aumentoFaturado,2);
		$participacaoFaturado = round($participacaoFaturado,2);
		$ticketMedioRH = round($ticketMedioRH,2);
		$conversaoRH = round($conversaoRH,2);
		$dados = array(
			'faturadoRH' => $faturadoRH, 
			'faturadoLoja' => $faturadoLoja, 
			'sessoesRH' => $sessoesRH, 
			'transacoesRH' => $transacoesRH, 
			'aumentoFaturado' => $aumentoFaturado, 
			'participacaoFaturado' => $participacaoFaturado, 
			'impressoesRH' => $impressoesRH, 
			'cliquesRH' => $cliquesRH, 
			'carrinhosRH' => $carrinhosRH, 
			'ticketMedioRH' => $ticketMedioRH, 
			'conversaoRH' => $conversaoRH
		);
	}

	//manutenção de valores nulos
	foreach ($dados as $key => $value){
		if($value == null)
			$dados[$key]= 0;
	}
	$data['principal'] = $dados; 
	
//-----

// grafico faturamento
	$select = 
	"SELECT 
		sum(REL_transacoes_rh) as REL_transacoes_rh,
		sum(REL_sessoes_rh) as REL_sessoes_rh
	from 
		rel_".$idCLI." 
	WHERE 
		REL_data BETWEEN SUBDATE(CURRENT_DATE,7) AND CURRENT_DATE 
	GROUP BY DAY(REL_data) 
	order by REL_data LIMIT 7";
	$query = mysqli_query($conDash, $select);
	$dadosGrafico['graficoConversaoRH'] = [];

	if ($query) {
		$i = 0;
		while ($array = mysqli_fetch_array($query)) {
			$sessaoRH = $array['REL_sessoes_rh'];
			$transacoesRH = $array['REL_transacoes_rh'];

			
			$conversaoRH = ($sessaoRH != 0) ?       (($transacoesRH * 100) / $sessaoRH) : 0;

			$conversaoRH = round($conversaoRH, 2);

			array_push($dadosGrafico['graficoConversaoRH'],$conversaoRH);
			$i++;
		}

		$dif = 7 - count($dadosGrafico['graficoConversaoRH']);

		for ($i=0; $i < $dif; $i++) { 
			array_push($dadosGrafico['graficoConversaoRH'],0);
		}
	} 
	//inverte os arrays porque o gráfico está ao contrário - É gambiarra pq o Eliabe tá sempre ocupado
	$data['graficoFaturamento']['faturadoRH'] = array_reverse($dadosGrafico['graficoConversaoRH']);
//------

// seleciona nomes e inteligencia dos widgets
	$select = 
	'SELECT 
		WID_nome, 
		WID_id, 
		WID_inteligencia 
	FROM 
		widget 
	WHERE WID_id_cli = '.$idCLI;
	$query = mysqli_query($conCad,$select);
	// dicionario inteligencias
	$inteligencias = array(
		1 => 'topTrends', 
		2 => 'maisVendidos', 
		3 => 'maisVendidosMesmaCategoria', 
		4 => 'remarketing', 
		5 => 'similares',
		6 => 'liquidacao', 
		7 => 'collection', 
		8 => 'compreJunto', 
		9 => 'bossChoice', 
		10 => 'ofertaLimitada', 
		11 => 'topCarrinho',
		12 => 'itensComplementares', 
		13 => 'overlayDeSaida',
		14 => 'baixouDePreco',
		15 => 'novidades',
		16 => 'geolocalizacao',
		17 => 'topTrendsRedesSociais',
		18 => 'melhorAvaliadosESimilares',
		19 => 'vitrinesDescobertas',
		20 => 'melhoresAvaliados',
		21 => 'recemAvaliados',
		22 => 'barraDeBusca',
		23 => 'navegacaoComplementar',
		24 => 'maisVendidosDaCategoriaManual',
		25 => 'palavraChave',
		26 => 'porAtributo',
		27 => 'vitrineBusca',
		28 => 'manualCarrinho',
		29 => 'inteligenciasMultiplas',
		30 => 'lojaLateral',
		31 => 'topTrendsGenero',
		32 => 'topTrendsFaixaEtaria',
		33 => 'landingPage',
		34 => 'produtosRelacionados',
		35 => 'remarketingNavegacao'
	 );

	if ($query) {
		$widgets = [0 => array("", "")];
		while($result = mysqli_fetch_array($query)){
			$widgets[$result['WID_id']] = array(
											0 => $result['WID_nome'],
											1 => $inteligencias[ $result['WID_inteligencia'] ]
										);
		}
	}

//-------

// lista widgets desempenho individuale gráfico widgets
	$select = 
	"SELECT 
		RWID_id_wid, 
		RWID_valor
	FROM 
		RWID_".$idCLI."
	WHERE 
		RWID_evento = 3 AND 
		DATE(RWID_data) BETWEEN '$begin' AND '$end' 
	GROUP BY RWID_id_wid";
	$query = mysqli_query($conDados,$select);
	$dados = [];
	$graficoWidgets = [];

	$i = 0;
	if($query){
		while( $result = mysqli_fetch_array($query) ) {
			$partesWidgets = explode("," , $result['RWID_id_wid']);
			$partesValor = explode(",", $result['RWID_valor']);
			foreach($partesWidgets as $k => $wid){
				if ($wid == 0) continue;
				if(!array_key_exists($wid,$dados)){
					$graficoWidgets = array_push_assoc(
						$graficoWidgets, 
						$wid,
						array(
						'vendas' => 0,
						'faturado' => 0,
						'nomeWid' => ucwords($widgets[ $wid ][0]),
						'inteligencia' => ucwords($widgets[ $wid ][1])
						)
					);

					$dados = array_push_assoc(
						$dados, 
						$wid,
						array(
						'faturado' => 0,
						'vendas' => 0,
						'idWid' => $wid,
						'nomeWid' => ucwords($widgets[ $wid ][0]),
						'conversao' => 0
						)
					);
				}

				$dados[$wid]['faturado'] += $partesValor[$k] ;
				$dados[$wid]['vendas'] += 1 ;

				$graficoWidgets[$wid]['faturado'] += $partesValor[$k] ;
				$graficoWidgets[$wid]['vendas'] += 1 ;
			}			
		}
	}		
	$dados2 = [];
	$graficoWidgets2 = array(
		'vendas' => [],
		'faturado' => [],
		'nomeWid' => [],
		'inteligencia' => []
		);
	//manutenção de valores nulos e reajuste do array
	foreach ($dados as $key => $value){
		foreach($value as $k2 => $v){
			if($v == null)
			$dados[$key][$k2] = 0;
		}
		$qSelectImpressoes = 
		"SELECT 
			COUNT(RWID_id) as wids 
		FROM 
			RWID_".$idCLI." 
		WHERE 
			RWID_id_wid = $key and 
			RWID_evento = 1 and 
			DATE(RWID_data) BETWEEN '$begin' AND '$end'";
		$result = mysqli_query($conDados, $qSelectImpressoes);
		if($result){
			$impressoes = mysqli_fetch_array($result)['wids'];
			$value['conversao'] = ($impressoes != 0) ? round($value['vendas'] * 100 / $impressoes, 2) : 0;
			$dados2[] = $value;
			array_push($graficoWidgets2['vendas'], $graficoWidgets[$key]['vendas']);
			array_push($graficoWidgets2['faturado'], $graficoWidgets[$key]['faturado']);
			array_push($graficoWidgets2['nomeWid'], $graficoWidgets[$key]['nomeWid']);
			array_push($graficoWidgets2['inteligencia'], $graficoWidgets[$key]['inteligencia']);
		}
	}

	//pega os 5 melhores widgets segundo o maior faturamento
	$ordem = $graficoWidgets2['faturado'];

	//ordena em ordem decrescente mas mantém os índices
	arsort($ordem);

	$i = 0; //controlador de índice (vai até 5 porque são os 5 melhores)
	foreach ($ordem as $chave => $valor) {
		//deixa os 5 maiores
		if($i<5){
			$i++;
			continue;
		}

		//depois dos 5 melhores, exclui os outros do array do gráfico
		unset($graficoWidgets2['vendas'][$chave]);
		unset($graficoWidgets2['faturado'][$chave]);
		unset($graficoWidgets2['nomeWid'][$chave]);
		unset($graficoWidgets2['inteligencia'][$chave]);
	}

	//reseta os índices do array
	$graficoWidgets2['vendas'] = array_values($graficoWidgets2['vendas']);
	$graficoWidgets2['faturado'] = array_values($graficoWidgets2['faturado']);
	$graficoWidgets2['nomeWid'] = array_values($graficoWidgets2['nomeWid']);
	$graficoWidgets2['inteligencia'] = array_values($graficoWidgets2['inteligencia']);
	//--pegou os 5 melhores




	$dados = $dados2;

	
	$data['graficoWidgets'] = $graficoWidgets2;

	$data['listaWidgets'] = $dados;
//---------


	echo json_encode($data);
}


getData($conCad, $conDash, $conDados, $idCLI, $begin, $end, $beginGrafico, $endGrafico);

	function array_push_assoc($array, $key, $value){
		$array[$key] = $value;
		return $array;
	}
?>


<?php
// conexão com banco de dados
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

function reverseDate($date) {
    $date = explode('/', $date);
    return $date[2].'-'.$date[1].'-'.$date[0];
}

$end = reverseDate($end);
$begin = reverseDate($begin);

$beginGrafico = reverseDate($beginGrafico);
$endGrafico = reverseDate($endGrafico);
// nome do cliente
$queryNome = mysqli_query($conCad, 'SELECT CLI_nome FROM cliente WHERE CLI_id = "$idCLI"');
$fetchNome = mysqli_fetch_array($queryNome);
$nomeCLI = $fetchNome['CLI_nome'];
//dados principais
$selectDados = 
"SELECT 
    sum(REL_faturado_rh) as faturadoRH, 
    sum(REL_faturado_loja) as faturadoLoja, 
    sum(REL_sessoes_rh) as sessoesRH, 
    sum(REL_sessoes_loja) as sessoesLoja, 
    sum(REL_transacoes_rh) as transacoesRH, 
    sum(REL_transacoes_loja) as transacoesLoja, 
    sum(REL_impressoes_rh) as impressoesRH, 
    sum(REL_cliques_rh) as cliquesRH, 
    sum(REL_carrinhos_rh) as carrinhosRH 
FROM 
    rel_".$idCLI." 
WHERE 
    DATE(REL_data) BETWEEN '$begin' AND '$end'";
$queryDados = mysqli_query($conDash, $selectDados);
$dadosPrincipais = [];
$dadosGrafico = [];
if($queryDados){
    $dados = mysqli_fetch_array($queryDados);
    if(count($dados) > 0) {    
        $dadosPrincipais['faturadoRH'] = $dados['faturadoRH'];

        $dadosPrincipais['faturadoLoja'] = $dados['faturadoLoja'];

        $dadosPrincipais['faturadoTotal'] = $dadosPrincipais['faturadoRH']+$dadosPrincipais['faturadoLoja'];

        $dadosPrincipais['sessoesRH'] = $dados['sessoesRH'];

        $dadosPrincipais['sessoesLoja'] = $dados['sessoesLoja'];

        $dadosPrincipais['transacoesRH'] = $dados['transacoesRH'];

        $dadosPrincipais['transacoesLoja'] = $dados['transacoesLoja'];  

        $dadosPrincipais['transacoesTotal'] = $dadosPrincipais['transacoesRH'] + $dadosPrincipais['transacoesLoja'];

        $temp = ($dadosPrincipais['faturadoLoja'] + $dadosPrincipais['faturadoRH']);
        $dadosPrincipais['faturadoPart'] = ($temp != 0) ? (($dadosPrincipais['faturadoRH'] * 100) / $temp) : 0;
        
        $temp = $dadosPrincipais['faturadoLoja'];
        $dadosPrincipais['aumentoFaturado'] = ($temp != 0) ? (($dadosPrincipais['faturadoRH'] * 100) / $temp) : 0;
        
        $temp = $dadosPrincipais['transacoesLoja'];
        $dadosPrincipais['aumentoTransacao'] = ($temp != 0) ? ($dadosPrincipais['transacoesRH'] * 100) / $temp : 0;
        
        
        $temp = ($dadosPrincipais['transacoesLoja'] + $dadosPrincipais['transacoesRH']);
        $dadosPrincipais['participacaoTrans'] = ($temp != 0) ? ($dadosPrincipais['transacoesRH'] * 100) / $temp : 0;
        
        $dadosPrincipais['aumentoTransacao'] = round($dadosPrincipais['aumentoTransacao'], 2);
        $dadosPrincipais['aumentoFaturado'] = round($dadosPrincipais['aumentoFaturado'],2);
        $dadosPrincipais['faturadoPart'] = round($dadosPrincipais['faturadoPart'],2);
        $dadosPrincipais['participacaoTrans'] = round($dadosPrincipais['participacaoTrans'], 2);

        // dados dos graficos e bloco central
        $dadosGrafico['impressoesRH'] = $dados['impressoesRH'];

        $dadosGrafico['cliquesRH'] = $dados['cliquesRH'];

        $dadosGrafico['carrinhosRH']= $dados['carrinhosRH'];

        $temp = $dadosGrafico['impressoesRH'];
        $dadosGrafico['taxaClique'] = ($temp != 0) ? ($dadosGrafico['cliquesRH'] * 100) / $temp : 0;
        
        $dadosGrafico['ticketMedioRH'] = ($dadosPrincipais['transacoesRH'] != 0) ? $dadosPrincipais['faturadoRH'] / $dadosPrincipais['transacoesRH'] : 0;
        
        $dadosGrafico['conversaoRH'] = ($dadosPrincipais['sessoesRH'] != 0) ? ($dadosPrincipais['transacoesRH'] * 100) / $dadosPrincipais['sessoesRH'] : 0;
        
        $dadosGrafico['conversaoLoja'] = ($dadosPrincipais['sessoesLoja'] != 0) ? ($dadosPrincipais['transacoesLoja'] * 100) / $dadosPrincipais['sessoesLoja'] : 0;
        
        $dadosGrafico['qtsXmelhor'] = ($dadosGrafico['conversaoLoja'] != 0) ? (($dadosGrafico['conversaoRH'] / $dadosGrafico['conversaoLoja']) - 1) * 100 : 0;
        
        $temp = ($dadosPrincipais['sessoesLoja'] + $dadosPrincipais['sessoesRH']);
        $dadosGrafico['conversaoTotal'] = ($temp != 0) ? (($dadosPrincipais['transacoesLoja'] + $dadosPrincipais['transacoesRH']) * 100) / $temp : 0;
        
        $dadosGrafico['aumentoConversao'] = ($dadosGrafico['conversaoLoja'] != 0) ? (($dadosGrafico['conversaoTotal'] * 100) / $dadosGrafico['conversaoLoja']) - 100 : 0;
        
        
        $dadosGrafico['conversaoTotal'] = round($dadosGrafico['conversaoTotal'],2);
        $dadosGrafico['qtsXmelhor'] = round($dadosGrafico['qtsXmelhor'], 2);
        $dadosGrafico['conversaoRH'] = round($dadosGrafico['conversaoRH'],2);
        $dadosGrafico['ticketMedioRH'] = round($dadosGrafico['ticketMedioRH'],2);
        $dadosGrafico['taxaClique'] = round($dadosGrafico['taxaClique'],2);
        $dadosGrafico['aumentoConversao'] = round($dadosGrafico['aumentoConversao'],2);
    }
}

// grafico conversao index
$select = 
"SELECT 
    sum(REL_transacoes_rh) as REL_transacoes_rh,
    sum(REL_transacoes_loja) as REL_transacoes_loja,
    sum(REL_sessoes_rh) as REL_sessoes_rh,
    sum(REL_sessoes_loja) as REL_sessoes_loja
from 
    rel_".$idCLI." 
WHERE 
    REL_data BETWEEN SUBDATE(CURRENT_DATE,7) AND CURRENT_DATE 
GROUP BY DAY(REL_data) 
order by REL_data LIMIT 7";
$query = mysqli_query($conDash, $select);
$dadosGrafico['graficoConversaoRH'] = [];
$dadosGrafico['graficoConversaoLoja'] = [];
$dadosGrafico['graficoConversaoTotal'] = [];

if ($query) {
    $i = 0;
    while ($array = mysqli_fetch_array($query)) {
        $sessaoRH = $array['REL_sessoes_rh'];
        $sessaoLoja = $array['REL_sessoes_loja'];
        $transacoesRH = $array['REL_transacoes_rh'];
        $transacoesLoja = $array['REL_transacoes_loja'];

        $sessaoTotal = $sessaoLoja + $sessaoRH;
        $transacoesTotal = $transacoesLoja + $transacoesRH;

        //echo '>>>('.$transacoesRH.' * 100)  / '.$sessaoRH.'<<<';
        $conversaoRH = ($sessaoRH != 0) ?       (($transacoesRH * 100) / $sessaoRH) : 0;
        $conversaoLoja = ($sessaoLoja != 0) ?   (($transacoesLoja * 100) / $sessaoLoja): 0;
        $conversaoTotal = ($sessaoTotal != 0) ?   (($transacoesTotal * 100) / $sessaoTotal): 0;

        $conversaoRH = round($conversaoRH, 2);
        $conversaoLoja = round($conversaoLoja, 2);
        $conversaoTotal = round($conversaoTotal, 2);



        array_push($dadosGrafico['graficoConversaoRH'],$conversaoRH);
        array_push($dadosGrafico['graficoConversaoLoja'],$conversaoLoja);
        array_push($dadosGrafico['graficoConversaoTotal'],$conversaoTotal);
        $i++;
    }

    // O resultado esperado é de 7 dias
    $dif = 7 - count($dadosGrafico['graficoConversaoRH']);

    // Caso não tenhamos 7 dias, vamos cravar o restante das entradas com ZERO.
    // Fique atento, se este não é um cliente novo, provavelmente deu erro no cron
    // que carrega a tabela rel_
    for ($i=0; $i < $dif; $i++) { 
        array_push($dadosGrafico['graficoConversaoRH'],0);
        array_push($dadosGrafico['graficoConversaoLoja'],0);
        array_push($dadosGrafico['graficoConversaoTotal'],0);
    }
} 
//inverte os arrays porque o gráfico está ao contrário - É gambiarra pq o Eliabe tá sempre ocupado
$dadosGrafico['graficoConversaoRH'] = array_reverse($dadosGrafico['graficoConversaoRH']);
$dadosGrafico['graficoConversaoLoja'] = array_reverse($dadosGrafico['graficoConversaoLoja']);
$dadosGrafico['graficoConversaoTotal'] = array_reverse($dadosGrafico['graficoConversaoTotal']);
    
//ranking mais vendidos da loja
$select = "SELECT * FROM XML_".$idCLI." where XML_venda_7 > 0 order by XML_venda_7 desc limit 3";
$query = mysqli_query($conDados,$select);
$dadosProdutos = [];
if($query){
    $i = 0;
    while($dadosRank = mysqli_fetch_array($query)){
        $dadosProdutos['vendasProd'][$i] = $dadosRank['XML_venda_7'];
        $dadosProdutos['cliquesProd'][$i] =  $dadosRank['XML_click_7'];
        $dadosProdutos['nomeProd'][$i] = $dadosRank['XML_titulo'];
        $dadosProdutos['fotoProd'][$i] = $dadosRank['XML_image_link'];
        $dadosProdutos['categoriaProd'][$i] = $dadosRank['XML_type'];
        $dadosProdutos['preco'][$i] = ($dadosRank['XML_sale_price'] > 0 ? $dadosRank['XML_sale_price'] : $dadosRank['XML_price']);
        $dadosProdutos['faturaProd'][$i] = $dadosProdutos['preco'][$i] * $dadosProdutos['vendasProd'][$i];
        $dadosProdutos['faturaProd'][$i] = strval(round($dadosProdutos['faturaProd'][$i],2));
        $dadosProdutos['conversaoProd'][$i] = $dadosProdutos['cliquesProd'][$i] > 0 ? ($dadosProdutos['vendasProd'][$i] * 100) / $dadosProdutos['cliquesProd'][$i] : 0;
        $dadosProdutos['conversaoProd'][$i] = strval(round($dadosProdutos['conversaoProd'][$i],2));
        $i++;
    }    
}

    
$z=0;
$selectG1 = "SELECT SUM(REL_sessoes_rh) as sessoesRH ,SUM(REL_transacoes_rh) as transacoesRH, SUM(REL_faturado_rh) as faturado, DAY(REL_data) FROM `rel_".$idCLI."` WHERE REL_data BETWEEN SUBDATE(CURRENT_DATE,7) AND CURRENT_DATE GROUP BY DAY(REL_data) LIMIT 7";
$queryG1 = mysqli_query($conDash, $selectG1);
if ($queryG1 ){
    while($arrayG1 = mysqli_fetch_array($queryG1))
    {
        $fatura[$z] = $arrayG1['faturado'];
        $dia1[$z] = $arrayG1['DAY(REL_data)'];
        $sessoes[$z] = $arrayG1['sessoesRH'];
        $transacoes[$z] = $arrayG1['transacoesRH'];
        $cr[$z] = ($sessoes[$z] != 0) ? ($transacoes[$z] * 100) / $sessoes[$z] : 0;
        $z++;
    }
}
    
if (!empty($dadosPrincipais) or !empty($dadosGrafico) or !empty($dadosProdutos))
    $data = array_merge($dadosPrincipais, $dadosGrafico, $dadosProdutos);
else
    $data = [];

    echo json_encode($data);
?>
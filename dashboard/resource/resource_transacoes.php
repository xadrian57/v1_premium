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

function getData($conDash, $conDados, $conCad, $idCLI, $begin, $end, $beginGrafico, $endGrafico)
{
    //dados principais
    $selectDados = 
    "SELECT 
        sum(REL_faturado_rh) as faturadoRH, 
        sum(REL_faturado_loja) as faturadoLoja, 
        sum(REL_transacoes_rh) as transacoesRH, 
        sum(REL_transacoes_loja) as transacoesLoja 
    FROM 
        rel_".$idCLI." 
    WHERE DATE(REL_data) BETWEEN '$begin' AND '$end'";
    $queryDados = mysqli_query($conDash, $selectDados);

    $dados = [];
    if($queryDados){
        $dados = mysqli_fetch_array($queryDados);

        $faturadoRH = $dados['faturadoRH']; 
        $faturadoLoja = $dados['faturadoLoja'];
        $totalFaturado = $faturadoRH + $faturadoLoja;
        $transacoesRH = $dados['transacoesRH'];
        $transacoesLoja = $dados['transacoesLoja'];
        $totalTransacoes = $transacoesRH + $transacoesLoja;

        $participacaoFaturado = ($faturadoLoja != 0) ? ($faturadoRH * 100) / $totalFaturado : 0;
        $participacaoFaturado = round($participacaoFaturado,2);

        $participacaoTrasacoes = ($transacoesLoja != 0) ? ($transacoesRH * 100) / $totalTransacoes : 0;
        $participacaoTrasacoes = round($participacaoTrasacoes,2);

        $dados = array(
            'faturadoRH' => $faturadoRH,
            'faturadoLoja' => $faturadoLoja,
            'transacoesRH' => $transacoesRH,
            'transacoesLoja' => $transacoesLoja,
            'participacaoFaturado' => $participacaoFaturado,
            'participacaoTrasacoes' => $participacaoTrasacoes,
            'totalTransacoes' => $totalTransacoes,
            'totalFaturado' => $totalFaturado
        );
    }
    //manutenção de valores nulos
    foreach ($dados as $key => $value){
        if($value == null)
            $dados[$key]= 0;
    }
    $data['principal'] = $dados; 


    // grafico de faturamento na loja
    $selectFaturado = 
    "SELECT 
        sum(REL_faturado_rh) as faturadoRh, 
        sum(REL_faturado_loja) as faturadoLoja, 
        sum(REL_transacoes_loja) as transacoesLoja, 
        sum(REL_transacoes_rh) as transacoesRh, 
        CONCAT(DAY(REL_data), '-',
        MONTHNAME(REL_data), '-',
        YEAR(REL_data)) as data
    FROM 
        rel_".$idCLI." 
    WHERE 
        DATE(REL_data) BETWEEN date_format(date_sub(now(),INTERVAL 1 WEEK), '%Y-%m-%d') and now()
    GROUP BY DAY(REL_data)
    ORDER BY REL_data";
    $queryFaturado = mysqli_query($conDash, $selectFaturado);

    $dados = [];
    if($queryFaturado){
        $i = 0;
        $faturaRH = [0,0,0,0,0,0];
        $faturaLoja = [0,0,0,0,0,0];
        $transLoja = [0,0,0,0,0,0];
        $transRH = [0,0,0,0,0,0];     
        $mes = [0,0,0,0,0,0];      
        $ano = [0,0,0,0,0,0];
        while($dadosFatura = mysqli_fetch_array($queryFaturado))
        {
            $faturaRH[$i] = $dadosFatura['faturadoRh'] + $dadosFatura['faturadoLoja'];
            $faturaLoja[$i] = $dadosFatura['faturadoLoja'];
            $transLoja[$i] = $dadosFatura['transacoesLoja'];
            $transRH[$i] = $dadosFatura['transacoesRh'];

            $faturaRH[$i] = strval(round($faturaRH[$i], 2));
           
            $dataFaturado = explode("-", $dadosFatura['data']);
            $mes[$i] = $dataFaturado[1];
            $ano[$i] = $dataFaturado[2];
            $i++;
        }

        $dados = array(
            'faturaRH' => $faturaRH,
            'faturaLoja' => $faturaLoja,
            'transLoja' => $transLoja,
            'transRH' => $transRH,
            'mes' => $mes,
            'ano' => $ano
        );
    }    

    //manutenção de valores nulos
    foreach ($dados as $key => $value){
        if($value == null)
            $dados[$key]= 0;
    }
    $data['graficoFaturamento'] = $dados; 

    // lista de transacoes
    $y = 0;
    $select = "SELECT PAG_id_trans, PAG_valor, PAG_data, PAG_reffer FROM COB_".$idCLI." WHERE DATE(COB_data) BETWEEN '$begin' AND '$end'";  
    //echo $select;  
    $queryLista = mysqli_query($conDados, $select);

    $dados = [];
    // primeiro, verifica se a query foi bem sucedida, para não gerar erro
    if($queryLista){
        while($arrayLista = mysqli_fetch_array($queryLista))
        {       
            $idTrans[$y] = $arrayLista['COB_id_trans'];
            $valorTrans[$y] = $arrayLista['COB_valor'];
            $dataTrans[$y] = $arrayLista['COB_data'];
            $reffer[$y] = $arrayLista['COB_reffer'];
            $y++;
        }

        $dados = array(
            'idTrans' => $idTrans,
            'valorTrans' => $valorTrans,
            'dataTrans' => $dataTrans,
            'reffer' => $reffer
        );
        
    }
    //manutenção de valores nulos
        foreach ($dados as $key => $value){
            if($value == null)
                $dados[$key]= 0;
        }
        $data['listaTransacoes'] = $dados;
    echo json_encode($data);
}

getData($conDash, $conDados, $conCad, $idCLI, $begin, $end, $beginGrafico, $endGrafico);

?>
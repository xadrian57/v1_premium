<?php
// conexão com banco de dados
require_once('../../bd/conexao_bd_cadastro.php');
require_once('../../bd/conexao_bd_dashboard.php');

//datapicker
$begin = mysqli_real_escape_string($conCad,$_POST['begin']);
$end = mysqli_real_escape_string($conCad,$_POST['end']);

// ultimos 7 dias
$beginSeisMeses = mysqli_real_escape_string($conCad,$_POST['beginSeisMeses']);
$endSeisMeses = mysqli_real_escape_string($conCad,$_POST['endSeisMeses']);

$beginSeisMeses = str_replace('/','-',$beginSeisMeses);
$endSeisMeses = str_replace('/','-',$endSeisMeses);

function reverseDate($date) {
    $date = str_replace('/','-',$date);
    $date = explode('-', $date);
    return $date[2].'-'.$date[1].'-'.$date[0];
}

$end = reverseDate($end);
$begin = reverseDate($begin);


$beginSeisMeses = reverseDate($beginSeisMeses);
$endSeisMeses = reverseDate($endSeisMeses);

$operacao = mysqli_real_escape_string($conCad,$_POST['op']);

// nome do cliente
$queryNome = mysqli_query($conCad, 'SELECT CLI_nome FROM cliente WHERE CLI_id = "$id"');
$fetchNome = mysqli_fetch_array($queryNome);
$nomeCLI = $fetchNome['CLI_nome'];

function getData($conCad, $conDash, $idCLI, $begin, $end, $beginSeisMeses, $endSeisMeses)
{
//dados principais
    $selectDados = 
    "SELECT 
        sum(REL_faturado_rh) as faturadoRH, 
        sum(REL_faturado_loja) as faturadoLoja, 
        sum(REL_sessoes_rh) as sessoesRH, 
        sum(REL_sessoes_loja) as sessoesLoja, 
        sum(REL_transacoes_rh) as transacoesRH, 
        sum(REL_transacoes_loja) as transacoesLoja, 
        avg(REL_dur_sess_rh) as duracaoRH, 
        avg(REL_dur_sess_loja) as duracaoLoja, 
        avg(REL_dur_sess_loja_rh) as duracaoTotal, 
        avg(REL_pag_sess_rh) as pag_sessaoRH, 
        avg(REL_pag_sess_loja) as pag_sessaoLoja, 
        avg(REL_pag_abandon_rh) as pagAbandon_rh, 
        avg(REL_pag_abandon_loja) as pagAbandon_loja 
    FROM 
        rel_".$idCLI." 
    WHERE 
        DATE(REL_data) BETWEEN '$begin' AND '$end'";
        //echo $selectDados;
    $queryDados = mysqli_query($conDash, $selectDados);

    // array temporária
    $dados = [];    
    if ($queryDados){
        $dados = mysqli_fetch_array($queryDados);
        //faturamento
        $faturadoRH = $dados['faturadoRH'];
        $faturadoLoja = $dados['faturadoLoja'];
        $faturadoRH_Loja = $faturadoRH + $faturadoLoja;
        $aumentoFaturado = ($faturadoLoja != 0) ? ($faturadoRH * 100) / $faturadoLoja : 0;
        $faturadoRH = round($faturadoRH,2);
        $faturadoLoja = round($faturadoLoja,2);
        $aumentoFaturado = round($aumentoFaturado,2);
        //conversão
        $sessoesRH = $dados['sessoesRH'];
        $sessoesLoja = $dados['sessoesLoja'];
        $transacoesRH = $dados['transacoesRH'];
        $transacoesLoja = $dados['transacoesLoja'];

        $conversaoRH = ($sessoesRH != 0) ? ($transacoesRH * 100) / $sessoesRH : 0;
        
        $conversaoLoja = ($sessoesLoja != 0) ? ($transacoesLoja * 100) / $sessoesLoja : 0;
        
        $temp = ($sessoesLoja + $sessoesRH);
        $conversaoTotal = ($temp != 0) ? (($transacoesLoja + $transacoesRH) * 100) / $temp : 0;
        
        $aumentoConversao = ($conversaoLoja != 0) ? (($conversaoTotal - $conversaoLoja) * 100) / $conversaoLoja : 0;
        
        //ticket médio
        $ticketMedioRH = ($transacoesRH != 0) ? $faturadoRH / $transacoesRH : 0;
        
        $ticketMedioLoja = ($transacoesLoja != 0) ? $faturadoLoja / $transacoesLoja : 0;
        
        $temp = ($transacoesLoja + $transacoesRH);
        $ticketMedioTotal = ($temp != 0) ? ($faturadoRH + $faturadoLoja) / $temp : 0;
        
        $aumentoTicketMedio = ($ticketMedioLoja != 0) ?(($ticketMedioTotal - $ticketMedioLoja) * 100) / $ticketMedioLoja : 0;
        
        $conversaoRH = round($conversaoRH,2);
        $conversaoLoja = round($conversaoLoja, 2);
        $conversaoTotal = round($conversaoTotal,2);
        $aumentoConversao = round($aumentoConversao,2);
        $ticketMedioRH = round($ticketMedioRH,2);
        $ticketMedioLoja = round($ticketMedioLoja,2);
        $ticketMedioTotal = round($ticketMedioTotal,2);
        $aumentoTicketMedio = round($aumentoTicketMedio, 2);

        // duração da sessão
        $duracaoRH = gmdate("H:i:s", $dados['duracaoRH']);
        $duracaoLoja = gmdate("H:i:s", $dados['duracaoLoja']);

        $segundosRH = $dados['duracaoRH'];
        $segundosLoja = $dados['duracaoLoja'];
        
        $temp = ($sessoesLoja + $sessoesRH);
        $segundosTotal = ($temp != 0) ?(($sessoesRH * $segundosRH) + ($sessoesLoja * $segundosLoja)) / $temp : 0;

        $duracaoTotal = gmdate("H:i:s", $segundosTotal);

        $aumentoDuracao = ($segundosLoja != 0) ? ($segundosTotal - $segundosLoja) * 100 / $segundosLoja : 0;
        $aumentoDuracao = round($aumentoDuracao,2);

        // paginas por sessao
        $pag_sessaoRH = ceil($dados['pag_sessaoRH']);
        $pag_sessaoLoja = ceil($dados['pag_sessaoLoja']);

        // taxa de rejeição
        $rejeicaoRH = ($sessoesRH != 0) ? ($dados['pagAbandon_rh'] * 100) / $sessoesRH : 0;
        $rejeicaoRH = round($rejeicaoRH,2);

        $rejeicaoLoja = ($sessoesLoja != 0) ? ($dados['pagAbandon_loja'] * 100) / $sessoesLoja : 0;
        $rejeicaoLoja = round($rejeicaoLoja,2);

        $temp = ($sessoesLoja + $sessoesRH);
        $rejeicaoTotal = ($temp != 0) ? (($dados['pagAbandon_loja'] + $dados['pagAbandon_rh']) * 100)/$temp : 0;
        $rejeicaoTotal = round($rejeicaoTotal,2);

        $rejeicaoDim = ($rejeicaoLoja != 0) ? (($rejeicaoLoja - $rejeicaoTotal) * 100) / $rejeicaoLoja : 0;
        $rejeicaoDim = round($rejeicaoDim, 2);

        $dados = array(
            'faturadoRH' => $faturadoRH,
            'faturadoLoja' => $faturadoLoja,
            'faturadoRH_Loja' => $faturadoRH_Loja,
            'aumentoFaturado' => $aumentoFaturado,
            'sessoesRH' => $sessoesRH,
            'sessoesLoja' => $sessoesLoja,
            'transacoesRH' => $transacoesRH,
            'transacoesLoja' => $transacoesLoja,
            'conversaoRH' => $conversaoRH,
            'conversaoLoja' => $conversaoLoja,
            'conversaoTotal' => $conversaoTotal,
            'aumentoConversao' => $aumentoConversao,
            'ticketMedioRH' => $ticketMedioRH,
            'ticketMedioLoja' => $ticketMedioLoja,
            'ticketMedioTotal' => $ticketMedioTotal,
            'aumentoTicketMedio' => $aumentoTicketMedio,
            'duracaoRH' => $duracaoRH,
            'duracaoLoja' => $duracaoLoja,
            'segundosRH' => $segundosRH,
            'duracaoTotal' => $duracaoTotal,
            'aumentoDuracao' => $aumentoDuracao,
            'pag_sessaoRH' => $pag_sessaoRH,
            'pag_sessaoLoja' => $pag_sessaoLoja,
            'rejeicaoRH' => $rejeicaoRH,
            'rejeicaoLoja' => $rejeicaoLoja,
            'rejeicaoTotal' => $rejeicaoTotal,
            'rejeicaoDim' => $rejeicaoDim
        );
    }
    //manutenção de valores nulos
    foreach ($dados as $key => $value){
        if($value == null)
            $dados[$key]= 0;
    }
    $data['dadosPrincipais'] = $dados;
//------

// grafico faturamento e tm 
    $selectFaturado = 
    "SELECT 
        sum(REL_pag_abandon_rh) as REL_pag_abandon_rh,
        sum(REL_sessoes_rh) as REL_sessoes_rh,
        sum(REL_sessoes_loja) as REL_sessoes_loja,
        sum(REL_pag_abandon_loja) as REL_pag_abandon_loja,
        sum(REL_faturado_rh) as REL_faturado_rh,
        sum(REL_faturado_loja) as REL_faturado_loja,
        sum(REL_transacoes_loja) as REL_transacoes_loja,
        sum(REL_transacoes_rh) as REL_transacoes_rh,
        CONCAT(MONTHNAME(REL_data), '_' , YEAR(REL_data)) as mes_ano
    FROM 
        rel_".$idCLI." 
    WHERE 
        DATE(REL_data) BETWEEN '$beginSeisMeses' AND '$endSeisMeses' 
    GROUP BY mes_ano
    ORDER BY REL_DATA";
    $queryFaturado = mysqli_query($conDash, $selectFaturado);

    $dados = [];
    if ($queryFaturado){
        $rejeicaoRH = array_fill (0, 6, "0");
        $rejeicaoLoja = array_fill (0, 6, "0");
        $faturaRH = array_fill (0, 6, "0.00");
        $faturaLoja = array_fill (0, 6, "0.00");
        $faturaTotal = array_fill (0, 6, "0.00");
        $transLoja = array_fill (0, 6, "0");
        $transRH = array_fill (0, 6, "0");
        $ticketLoja = array_fill (0, 6, "0.00");
        $ticketLoja_RH = array_fill (0, 6, "0.00");
        $conversaoLoja = array_fill (0, 6, "0.00");
        $conversaoLoja_RH = array_fill (0, 6, "0.00");
        $mes = array_fill (0, 6, "0");
        $ano = array_fill (0, 6, "0");
        if (mysqli_num_rows($queryFaturado) > 1){ // se conseguir alguma informacao apenas
            $i = 6 - mysqli_num_rows($queryFaturado);
            $dadosFaturado = [];
            while($dadosFaturado = mysqli_fetch_array($queryFaturado))
            {

                $faturaRH[$i] = $dadosFaturado['REL_faturado_rh'];
                $faturaLoja[$i] = $dadosFaturado['REL_faturado_loja'];
                $faturaTotal[$i] =  $faturaLoja[$i]+$faturaRH[$i];

                $faturaRH[$i] = strval(round($faturaRH[$i], 2));
                $faturaLoja[$i] = strval(round($faturaLoja[$i], 2));
                $faturaTotal[$i] =  strval(round($faturaTotal[$i], 2));

                $transLoja[$i] = strval($dadosFaturado['REL_transacoes_loja']);
                $transRH[$i] = strval($dadosFaturado['REL_transacoes_rh']);
                
                $ticketLoja[$i] = ($transLoja[$i] != 0) ? $faturaLoja[$i] / $transLoja[$i] : 0;
                $ticketLoja[$i] = strval(round($ticketLoja[$i],2));
                
                $temp = ($transRH[$i] + $transLoja[$i]);
                $ticketLoja_RH[$i] = ($temp != 0) ? ($faturaTotal[$i]) / $temp : 0;
                $ticketLoja_RH[$i] = strval( round($ticketLoja_RH[$i],2) );

                $sessoesLoja = $dadosFaturado['REL_sessoes_loja'];

                $sessoesTotal = $dadosFaturado['REL_sessoes_rh'] + $sessoesLoja;

                $conversaoLoja[$i] = ($sessoesLoja != 0) ? (($transLoja[$i]) * 100) / $sessoesLoja : 0;
                $conversaoLoja[$i] = strval(round($conversaoLoja[$i], 2));

                $conversaoLoja_RH[$i] = ($sessoesTotal != 0) ? (($transLoja[$i] + $transRH[$i]) * 100) / $sessoesTotal : 0;
                $conversaoLoja_RH[$i] = strval(round($conversaoLoja_RH[$i], 2));

                $mes[$i] = explode("_", $dadosFaturado['mes_ano'])[0];
                $i++;
            } 

            $dados = array(
                'transLoja' => $transLoja,
                'transRH' => $transRH,
                'ticketLoja' => $ticketLoja,
                'ticketLoja_RH' => $ticketLoja_RH,
                'mes' => $mes,
                'faturaRH' => $faturaRH,
                'faturaLoja' => $faturaLoja,
                'faturaTotal' => $faturaTotal,
                'conversaoLoja_RH' => $conversaoLoja_RH,
                'conversaoLoja' => $conversaoLoja,
                'rejeicaoRH' => $rejeicaoRH,
                'rejeicaoLoja' => $rejeicaoLoja
            );
        }
    }
    //manutenção de valores nulos
    foreach ($dados as $key => $value){
        if($value == null)
            $dados[$key]= 0;
    }
    $data['faturamentoGrafico'] = $dados;
        
//------


    echo json_encode($data);
}
    
switch ($operacao) {
    case '1':    
        // id cliente
        $idCLI = mysqli_real_escape_string($conCad,$_POST['id']);
        getData($conCad, $conDash, $idCLI, $begin, $end, $beginSeisMeses, $endSeisMeses);
        break;    
    default:
        # code...
        break;
}

?>
<?php
//set_time_limit(3600);

//CONEXÃO BD OWA
include 'bd/conexao_bd_owa.php';

//CONEXÃO BD CADASTRO
include 'bd/conexao_bd_cadastro.php';

//CONEXÃO BD DASHBOARD
include 'bd/conexao_bd_dashboard.php';

//CONEXÃO BD DADOS
include 'bd/conexao_bd_dados.php';

/*
Período: 14/05 até 21/05
IDS:
- 14 (Bazar)
- 20 (Cristale)
- 105 (Aldo Conti)
- 22 (Pandora)
- 220 (Bavera)
- 116 (Catran)
*/

$clientes = array(14,20,105,22,220,116);

for($d = 15; $d < 22; $d++)
{
    for($h = 0; $h < 24; $h++)
    {
        //TIME
        //$time = date('Y-m-d H', strtotime('- 1 hour'));
        //$d = 14; $h = 0;
        $time = '2018-05-'. $d .' '. $h;

        $begin = $time.":00:00.000000";
        $end = $time.":59:59.999999";

        foreach ($clientes as $key => $value) 
        {
            //export($value, sha1($value), $begin, $end);
            createRelatorio($value);
            
            //VARIAVEIS
            $faturaRH = totalVendasRoi(sha1($value), $begin, $end); //REL_faturado_rh
            $faturaLJ = totalVendasSemRoi(sha1($value), $begin, $end); //REL_faturado_loja
            $nVendasRH = numVendasRoi(sha1($value), $begin, $end); //REL_transacoes_rh
            $nVendasLJ = numVendasSemRoi(sha1($value), $begin, $end); //REL_transacoes_loja
            $impressoesRH = numImpressoesRH($value, $begin, $end); //REL_impressoes_rh
            $cliquesRH = numCliquesRoi(sha1($value), $begin, $end); //REL_cliques_rh
            $carrinhosRH = numCarrinhosRoi(sha1($value), $begin, $end); //REL_carrinhos_rh
            //$tempMedioSessaoRH = ''; //tempMedioSessao(sha1($value), $begin, $end, 1); //REL_dur_sess_rh
            //$tempMedioSessaoLJ = ''; //tempMedioSessao(sha1($value), $begin, $end, 2); //REL_dur_sess_loja
            //$tempMedioSessao = ''; //tempMedioSessao(sha1($value), $begin, $end, 3); //REL_dur_sess_loja_rh
            //$mediaPagSessaoRH = ''; //mediaPagSessao(sha1($value), $begin, $end, 1); //REL_pag_sess_loja
            //$mediaPagSessaoLJ = ''; //mediaPagSessao(sha1($value), $begin, $end, 2); //REL_pag_sess_rh
            //REL_pag_abandon
            $numSessoesRH = numSessoes(sha1($value), $begin, $end, 1); //REL_sessoes_rh
            $numSessoesLJ = numSessoes(sha1($value), $begin, $end, 2); //REL_sessoes_loja

            insertRelatorio($value, "(REL_faturado_rh, REL_faturado_loja, REL_transacoes_rh, REL_transacoes_loja, REL_impressoes_rh, REL_cliques_rh, REL_carrinhos_rh, REL_sessoes_rh, REL_sessoes_loja, REL_data) VALUES (".$faturaRH.", ".$faturaLJ.", ".$nVendasRH.", ".$nVendasLJ.", ".$impressoesRH.", ".$cliquesRH.", ".$carrinhosRH.", ".$numSessoesRH.", ".$numSessoesLJ.", '".date("Y-m-d H:i:s", strtotime('+1 hour' , strtotime($begin)))."')");
            
            /*

            * ANTIGO COM SESSÃO

            insertRelatorio($value, "(REL_faturado_rh, REL_faturado_loja, REL_transacoes_rh, REL_transacoes_loja, REL_impressoes_rh, REL_cliques_rh, REL_carrinhos_rh, REL_dur_sess_rh, REL_dur_sess_loja, REL_dur_sess_loja_rh, REL_pag_sess_loja, REL_pag_sess_rh, REL_sessoes_rh, REL_sessoes_loja)
                                             VALUES (".$faturaRH.", ".$faturaLJ.", ".$nVendasRH.", ".$nVendasLJ.", ".$impressoesRH.", ".$cliquesRH.", ".$carrinhosRH.", ".$tempMedioSessaoRH.", ".$tempMedioSessaoLJ.", ".$tempMedioSessao.", ".$mediaPagSessaoRH.", ".$mediaPagSessaoLJ.", ".$numSessoesRH.", ".$numSessoesLJ.")");
            */

            // fim processamento cliente
            //rhLog("\n Rodou Cliente : " . $value . " - " . date("Y-m-d H:i:s"), $nomeArquivo);  

            echo "\n Rodou Cliente ". $value . " Date: ". $begin . " - ". $end;           
        }
    }
}




function export($idCli, $idCliOwa, $begin, $end) {
    global $conDados, $conOWA;
    
    $selectOwa = 'SELECT product_id, values_id, quantity_id, event_id, widget_id, (widget_id IS NULL OR widget_id = \'\' OR CAST(REPLACE(widget_id, \',\', \'\') AS UNSIGNED) = 0) as isNotRoihero, dh_insert FROM owa_roihero WHERE site_id = \'' . $idCliOwa . '\' AND dh_insert BETWEEN \'' . $begin . '\' AND \'' . $end . "'";
    $result = mysqli_query($conOWA, $selectOwa);
    
    while($row = mysqli_fetch_array($result)) {
        
        if($row['isNotRoihero']) {
            $insert = 'INSERT INTO RGER_' . $idCli . ' (RGER_evento, RGER_data, RGER_id_prod, RGER_quant, RGER_valor)
                       VALUES (\'' . $row['event_id'] . '\', \'' . $row['dh_insert'] . '\', \'' . $row['product_id'] . '\', \'' . $row['quantity_id'] . '\', \'' . $row['values_id'] . '\')';
        } else {
            $insert = 'INSERT INTO RWID_' . $idCli . ' (RWID_evento, RWID_data, RWID_id_prod, RWID_quant, RWID_valor, RWID_id_wid)
                       VALUES (\'' . $row['event_id'] . '\', \'' . $row['dh_insert'] . '\', \'' . $row['product_id'] . '\', \'' . $row['quantity_id'] . '\', \'' . $row['values_id'] . '\', \'' . $row['widget_id'] . '\')';
        }
        
        mysqli_query($conDados, $insert);
    }
}


//FUNÇÃO CRIA TABELA
function createRelatorio($idCliente)
{
    global $conDash;
    
    $criaRelatorio = "CREATE TABLE IF NOT EXISTS rel_".$idCliente."
		(
	        REL_id int(10) NOT NULL AUTO_INCREMENT,
            REL_faturado_rh DECIMAL(12,2) NOT NULL,
            REL_faturado_loja DECIMAL(12,2) NOT NULL,
            REL_transacoes_rh INT(8) NOT NULL,
            REL_transacoes_loja INT(8) NOT NULL,
            REL_impressoes_rh INT(12) NOT NULL,
            REL_cliques_rh INT(12) NOT NULL,
            REL_carrinhos_rh INT(12) NOT NULL,
            REL_dur_sess_rh INT(8) NOT NULL,
            REL_dur_sess_loja INT(8) NOT NULL,
            REL_dur_sess_loja_rh INT(8) NOT NULL,
            REL_pag_sess_loja INT(2) NOT NULL,
            REL_pag_sess_rh INT(2) NOT NULL,
            REL_sessoes_rh INT(8) NOT NULL,
            REL_sessoes_loja INT(8) NOT NULL,
            REL_data DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            REL_pag_abandon_rh int(100),
            REL_pag_abandon_loja int(100),
            PRIMARY KEY (REL_id)
        )";
    
    mysqli_query($conDash, $criaRelatorio);
}

//FUNÇÃO INSERT RELATORIO
//$query com os values a serem inseridos ("(column1, column2, column3, ...) VALUES (value1, value2, value3, ...)")
function insertRelatorio($idCliente, $query)
{
    global $conDash;
    
    $insereRelatorio = "INSERT INTO rel_".$idCliente." ".$query;
    mysqli_query($conDash, $insereRelatorio);

    echo mysqli_error($conDash);
}

//FUNÇÃO TOTAL VENDAS ROIHERO
//Total em vendas da loja em R$ feito pela Roi Hero (Somatória do valor das vendas da Roi Hero)
function totalVendasRoi($idCliente, $begin, $end)
{
    global $conOWA;
    
    $totalVendasRoi = 0;
    
    $sql = 'SELECT values_id, quantity_id FROM owa_roihero WHERE site_id = \'' . $idCliente . '\' AND event_id = 3 ' . getRoiheroWidgetClause() . ' AND dh_insert BETWEEN \'' . $begin . '\' AND \'' . $end . "'";
    
    $result = mysqli_query($conOWA, $sql);
    
    while($row = mysqli_fetch_array($result)) {
        $values = explode(',', $row['values_id']);
        $qtds = explode(',', $row['quantity_id']);
        
        for($i = 0; $i < count($values); $i++) {
            $totalVendasRoi += floatval($values[$i]) * $qtds[$i];
        }
    }
    
    //RETORNA O VALOR CALCULADO
    return $totalVendasRoi;
}

//FUNÇÃO TOTAL VENDAS SEM ROIHERO
function totalVendasSemRoi($idCliente, $begin, $end)
{
    global $conOWA;
    
    $totalVendas = 0;
    
    $sql = 'SELECT values_id, quantity_id FROM owa_roihero WHERE site_id = \'' . $idCliente . '\' AND event_id = 3 ' . getSemRoiheroWidgetClause() . ' AND dh_insert BETWEEN \'' . $begin . '\' AND \'' . $end . "'";
    
    $result = mysqli_query($conOWA, $sql);
    
    while($row = mysqli_fetch_array($result)) {
        $values = explode(',', $row['values_id']);
        $qtds = explode(',', $row['quantity_id']);
        
        for($i = 0; $i < count($values); $i++) {
            $totalVendas += floatval($values[$i]) * $qtds[$i];
        }
    }
    
    //RETORNA O VALOR CALCULADO
    return $totalVendas;
}

//FUNÇÃO NUMERO VENDAS ROIHERO
function numVendasRoi($idCliente, $begin, $end)
{
    global $conOWA;
    
    $numVendasRoi = 0;
    
    // event_id = 3 (transações finalizadas)
    $sql = 'SELECT COUNT(*) as num_vendas FROM owa_roihero WHERE site_id = \'' . $idCliente . '\' AND event_id = 3 ' . getRoiheroWidgetClause() . ' AND dh_insert BETWEEN \'' . $begin . '\' AND \'' . $end . "'";
    
    $result = mysqli_query($conOWA, $sql);
    
    while($row = mysqli_fetch_array($result)) {
        $numVendasRoi = $row['num_vendas'];
    }
    
    //RETORNA O VALOR CALCULADO
    return $numVendasRoi;
}

//FUNÇÃO NUMERO VENDAS SEM ROIHERO
function numVendasSemRoi($idCliente, $begin, $end)
{
    global $conOWA;
    
    $numVendas = 0;
    
    $sql = 'SELECT COUNT(*) as num_vendas FROM owa_roihero WHERE site_id = \'' . $idCliente . '\' AND event_id = 3 ' . getSemRoiheroWidgetClause() . ' AND dh_insert BETWEEN \'' . $begin . '\' AND \'' . $end . "'";
    
    $result = mysqli_query($conOWA, $sql);
    
    while($row = mysqli_fetch_array($result)) {
        $numVendas = $row['num_vendas'];
    }
    
    //RETORNA O VALOR CALCULADO
    return $numVendas;
}

function numImpressoesRH($idCliente, $begin, $end)
{
    global $conDados;
    
    $numImpressoesRH = 0;
    
    $sql = 'SELECT COUNT(*) as num_views FROM VIEW_' . $idCliente . ' WHERE VIEW_data BETWEEN \'' . $begin . '\' AND \'' . $end . "'";
    
    $result = @mysqli_query($conDados, $sql);
    
    if(!$result) {
        // Retorna 0 (zero) se a consulta não rodar corretamente
        return 0;
    }
    
    while($row = mysqli_fetch_array($result)) {
        $numImpressoesRH = $row['num_views'];
    }
    
    return $numImpressoesRH;
}

//FUNÇÃO NUMERO CLIQUES ROIHERO
function numCliquesRoi($idCliente, $begin, $end)
{
    global $conOWA;
    
    $numCliquesRoi = 0;
    
    $sql = 'SELECT COUNT(*) as num_views FROM owa_roihero WHERE site_id = \'' . $idCliente . '\' AND event_id = 1 ' . getRoiheroWidgetClause() . ' AND dh_insert BETWEEN \'' . $begin . '\' AND \'' . $end . "'";
    
    $result = mysqli_query($conOWA, $sql);
    
    while($row = mysqli_fetch_array($result)) {
        $numCliquesRoi = $row['num_views'];
    }
    
    //RETORNA O VALOR CALCULADO
    return $numCliquesRoi;
}

//FUNÇÃO NUMERO CARRINHOS ROIHERO
function numCarrinhosRoi($idCliente, $begin, $end)
{
    global $conOWA;
    
    $numCarrinhosRoi = 0;
    
    $sql = 'SELECT COUNT(*) as num_carts FROM owa_roihero WHERE site_id = \'' . $idCliente . '\' AND event_id = 2 ' . getRoiheroWidgetClause() . ' AND dh_insert BETWEEN \'' . $begin . '\' AND \'' . $end . "'";
    
    $result = mysqli_query($conOWA, $sql);
    
    while($row = mysqli_fetch_array($result)) {
        $numCarrinhosRoi = $row['num_carts'];
    }
    
    //RETORNA O VALOR CALCULADO
    return $numCarrinhosRoi;
}

//FUNÇÃO TEMPO MEDIO DE SESSÃO
//$parm define o escopo (1 = só roihero, 2 = só a loja, 3 = tudo)
function tempMedioSessao($idCliente, $begin, $end, $parm)
{
    global $conOWA;
    $tempMedioSessao = 0;
    
    $sql = 'SELECT session_id, TIMEDIFF ( max(dh_insert), min(dh_insert) ) as time
                FROM owa_roihero
                WHERE site_id = \'' . $idCliente . '\'
                AND dh_insert BETWEEN \'' . $begin . '\' AND \'' . $end . "' ";
    
    $sql .= getWidgetClause($parm);
    $sql .= ' GROUP BY session_id';
    
    $result = mysqli_query($conOWA, $sql);
    
    $i = 0;
    
    while($row = mysqli_fetch_array($result)) {
        $i++;
        $tempMedioSessao += convertTimeToSeconds($row['time']);
    }
    
    if($i == 0) {
        return 0;
    }
    
    //RETORNA O VALOR CALCULADO
    return $tempMedioSessao / $i;
}

//FUNÇÃO MEDIA DE PAGINAS POR SESSÃO
//$parm define o escopo (1 = só roihero, 2 = só a loja, 3 = tudo)
function mediaPagSessao($idCliente, $begin, $end, $parm)
{
    global $conOWA;
    $mediaPagSessao = 0;
    
    $sql = 'SELECT session_id, count(event_id) event
                FROM owa_roihero
                WHERE site_id = \'' . $idCliente . '\'
                AND dh_insert BETWEEN \'' . $begin . '\' AND \'' . $end . "' ";
    
    $sql .= getWidgetClause($parm);
    $sql .= ' GROUP BY session_id';
    
    $result = mysqli_query($conOWA, $sql);
    
    $i = 0;
    
    while($row = mysqli_fetch_array($result)) {
        $i++;
        $mediaPagSessao += (int)$row['event'];
    }
    
    if($i == 0) {
        return 0;
    }
    
    //RETORNA O VALOR CALCULADO
    return $mediaPagSessao / $i;
}

//FUNÇÃO NUMERO DE SESSÕES
//$parm define o escopo (1 = só roihero, 2 = só a loja, 3 = tudo)
function numSessoes($idCliente, $begin, $end, $parm)
{
    global $conOWA;
    $numSessoes = 0;
    
    $sql = 'SELECT DISTINCT session_id as sessions
                FROM owa_roihero
                WHERE site_id = \'' . $idCliente . '\'
                AND dh_insert BETWEEN \'' . $begin . '\' AND \'' . $end . "' ";
    
    $sql .= getWidgetClause($parm);
    
    $result = mysqli_query($conOWA, $sql);
    
    $numSessoes = mysqli_num_rows($result);
    
    //RETORNA O VALOR CALCULADO
    return $numSessoes;
}

function getWidgetClause($parm) {
    $parmQuery = '';
    switch ($parm)
    {
        case 1:
            $parmQuery = getRoiheroWidgetClause(); //idwid setado
            break;
        case 2:
            $parmQuery = getSemRoiheroWidgetClause(); //idwid não setado
            break;
        case 3:
            $parmQuery = ""; //manter vazio
            break;
    }
    return $parmQuery;
}

function convertTimeToSeconds($time) {
    $parsed = date_parse($time);
    $seconds = $parsed['hour'] * 3600 + $parsed['minute'] * 60 + $parsed['second'];
    return $seconds;
}

function getRoiheroWidgetClause() {
    return "AND (widget_id IS NOT NULL AND widget_id <> '' AND CAST(REPLACE(widget_id, ',', '') AS UNSIGNED) <> 0)";
}

function getSemRoiheroWidgetClause() {
    return "AND (widget_id IS NULL OR widget_id = '' OR CAST(REPLACE(widget_id, ',', '') AS UNSIGNED) = 0)";
}

?>
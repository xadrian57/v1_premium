<?php
header('Content-type: text/plain; charset=utf-8');
// conexão com banco de dados
require_once('../../bd/conexao_bd_cadastro.php');
require_once('../../bd/conexao_bd_dashboard.php');

$idCLI = $_POST['id'];

$selectdata = mysqli_query($conCad, 'SELECT PLAN_data_venc FROM plano WHERE PLAN_id_cli = '.$idCLI.'');
$array = mysqli_fetch_array($selectdata);
$dataVenc = $array['PLAN_data_venc'];

// Pegar o valor da última fatura do cliente liquidada
$sql = 'SELECT PAG_valor, DATE_FORMAT(PAG_data_liq, \'%Y-%m-%d\') as PAG_data_liq FROM pagamento WHERE PAG_id_cli = ' . $idCLI . ' AND PAG_status = 1 ORDER BY PAG_data_liq DESC LIMIT 1';
$query = mysqli_query($conCad, $sql);
$dados = mysqli_fetch_array($query);

$investimento = $dados['PAG_valor'];
$dataLiquidada = $dados['PAG_data_liq'];

// Somar o faturado da loja pela ROI HERO do dia da liquidação do último boleto liquidado até 30 dias antes dessa data:
$sql = 'SELECT sum(REL_faturado_rh) as faturadoRH FROM `rel_' . $idCLI . '` WHERE REL_data BETWEEN SUBDATE(STR_TO_DATE(\'' . $dataLiquidada . '\', \'%Y-%m-%d\'),30) AND STR_TO_DATE(\'' . $dataLiquidada . '\', \'%Y-%m-%d\')';
$query = mysqli_query($conDash, $sql);
$dados = mysqli_fetch_array($query);

$retorno = $dados['faturadoRH'];

// Investimento = valor pago no último boleto
// Retorno = soma do faturado da ROI Hero
// ROI = Retorno/Investimento

$retornos = array(
        'investimento' => $investimento,
        'retorno' => $retorno,
        'roi' => $retorno / $investimento
);


 $i=0; 
 //SELECT PAG_id, PAG_valor, DATE_FORMAT(PAG_data_venc, '%d/%m/%Y''), DATE_FORMAT(PAG_data_venc, format_mask), PAG_link_2_via, PAG_status FROM 
 $selectFaturas = "SELECT PAG_id, PAG_valor, DATE_FORMAT(PAG_data_venc, '%d/%m/%Y') as PAG_data_venc, PAG_data_liq, PAG_link_2_via, PAG_status FROM pagamento WHERE PAG_id_cli = '$idCLI'";
 $queryFaturas = mysqli_query($conCad, $selectFaturas);

 //dados
 $idFAT = [];
 $valorFAT = [];
 $vencimentoFAT = [];
 $liquidadoFAT = [];
 $linkFAT = [];
 $statusFAT = [];

 while($dadosFatura = mysqli_fetch_array($queryFaturas))
 {
     $idFAT[$i] = $dadosFatura['PAG_id'];
     $valorFAT[$i] = "R$ ".number_format($dadosFatura['PAG_valor'], 2, ',', '.');
     $vencimentoFAT[$i] = $dadosFatura['PAG_data_venc'];
     $liquidadoFAT[$i] = $dadosFatura['PAG_data_liq'];
     $status = $dadosFatura['PAG_status'];
     $linkFAT[$i] = $dadosFatura['PAG_link_2_via'];
     $statusFAT[$i] = "";
     switch($status){
        case 0:
            $statusFAT[$i] = "Pendente";
            break;
        case 1:
            $statusFAT[$i] = "Pago";
            break;
        case 2:
            $statusFAT[$i] = "Cancelado";
            break;
        case 3:
            $statusFAT[$i] = "Vencido";
            break;
     }
     $i++;
 }
 $j = $i;


 $fatura = [];

 for ($i=0; $i < $j; $i++) {     
     $fatura[$i] = array(
         'status' => $statusFAT[$i], 
         'link' => $linkFAT[$i], 
         'valor' => $valorFAT[$i], 
         'dataVenc' => $vencimentoFAT[$i], 
         'dataLiq' => $liquidadoFAT[$i] 
     );
 }

 $envio = [$retornos, $fatura];


 echo json_encode($envio);




/*
fl_status_recb = status da cobrança (0=pendente, 1=liquidada, 2=cancelada)
link_2via = link para visualizar a 2ª via da cobrança
vl_total_recb = valor da cobrança
dt_vencimento_recb = data de vencimento
dt_recebimento_recb = data de crédito
dt_liquidacao_recb = data de liquidação
*/

//echo json_encode($fatura);
?>
<?php

include "../../bd/conexao_bd_cadastro.php";


// isso aqui é um error handler. No final do arquivo tem uma função com o error handler chamada "errHandler"
// essa função é chamda quando acontece um erro ou um warning e ela me envia um email com oq raios aconteceu
set_error_handler('errHandle');

/*
PARA TESTE 
$conteudo = array(
                'validationtoken' => '514cb7f3c9afcd47f9bfbaaff194d9c9444cfbcc',
                'data' => array(
                    'id_sacado_sac' => 284,
                    'id_recebimento_recb' => '22233',
                    'dt_recebimento_recb' => '05/20/2018',
                    'dt_geracao_recb' => '/05/18/2018',
                    'vl_emitido_recb' => '223,55',
                    'fl_status_recb' => 0,
                    'dt_vencimento_recb' => '05/23/2018',
                    'dt_liquidacao_recb'=> '05/19/2018',
                    'link_2via' => 'www.helpmua.com'
                )
            );
*/
$conteudo = $_POST; 
$agora = date("d/m/Y H:i:s");


file_put_contents('HTTPS_Cobranca_liquidada.txt', $agora."\n".json_encode($conteudo)."\n\n", FILE_APPEND);

if(isset($conteudo['data']) && !empty($conteudo['data']))
{
 
    $idCliSL = $conteudo['data']['id_sacado_sac'];
    $idTrans = $conteudo['data']['id_recebimento_recb'];
    $data_superlogica = $conteudo['data']['dt_recebimento_recb'];
    $novoStatus = $conteudo['data']['fl_status_recb'];

    $data_superlogica = date("Y-m-d", strtotime($data_superlogica));
    
    $selectCLI = "SELECT CLI_id, PLAN_tempo FROM cliente left join plano on plano.PLAN_id_cli = cliente.CLI_id WHERE CLI_id_sl = '$idCliSL'";
    file_put_contents('cobranca_liquidada_mysqli_error.txt',  $agora."\n".mysqli_error($conCad), FILE_APPEND);
    $resultCLI = mysqli_query($conCad, $selectCLI);
    if($resultCLI){
        $arrayCLI = mysqli_fetch_array($resultCLI);
        $idCli = $arrayCLI['CLI_id'];
        $tempo = $arrayCLI['PLAN_tempo'];
        $tempo = $tempo / 30;

        $qUpdatePagamento = 
        "UPDATE 
            pagamento 
        SET 
            PAG_status = '$novoStatus', 
            PAG_data_liq = '$data_superlogica' 
        WHERE 
            PAG_id_trans = '$idTrans' and 
            PAG_id_cli = '$idCli'";

        $resultUpdatePagamento = mysqli_query($conCad, $qUpdatePagamento);
        if(!$resultUpdatePagamento || mysqli_affected_rows($conCad) != 1){

            enviaEmail($idCli, $idCliSL, "SOMETHING VERY WRONG, não atualizou só um pagamento ou não rodou. Help!".json_encode($conteudo).mysqli_error($conCad).mysqli_affected_rows($conCad));
        }


        $qUpdatePlano = 
        "UPDATE 
            plano 
        SET 
            PLAN_status = 1, 
            
            PLAN_data_venc = ADDDATE(PLAN_data_venc, INTERVAL $tempo MONTH)
        WHERE 
            PLAN_id_cli = '$idCli'";

        $resultUpdatePlano = mysqli_query($conCad, $qUpdatePlano);
        if(!$resultUpdatePlano || mysqli_affected_rows($conCad) != 1){

            enviaEmail($idCli, $idCliSL, "SOMETHING VERY WRONG, não atualizou só um plano ou não rodou. Help!".json_encode($conteudo).mysqli_error($conCad).mysqli_affected_rows($conCad));
        }
    } else {
        enviaEmail("Não tem", $idCliSL, "Não retornou cliente na consulta pelo id_sl".json_encode($conteudo)."\n<br>".mysqli_error($conCad));
    }
    


} else {
    enviaEmail("Não tem", "Não tem", "conteudo vazio ou data não setado".json_encode($conteudo));
}


echo "{\"status\":200}";


function enviaEmail($idCli, $idCliSL, $mensagem){
    $headers = "Reply-To: ROI HERO <atendimento@roihero.com.br>\r\n";
    $headers .= "Return-Path: Roi Hero <atedimento@roihero.com.br>\r\n";
    $headers .= "From: Roi Hero <atendimento@roihero.com.br>\r\n"; 
    //$headers .= "Cc: julio.vieira@roihero.com.br\r\n";
    //$headers .= 'Cc: lucas_hoch_sv@hotmail.com' . "\r\n";
    $headers .= "Organization: Roi Hero - Recomendações Inteligentes\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/plain; charset=utf-8\r\n";
    $headers .= "X-Priority: 3\r\n";
    $headers .= "X-Mailer: PHP". phpversion() ."\r\n";

    //mensagem do email de novo cliente VIP
    $msg = 
    'IdCli:'.$idCli.'/nId Superlógica:'.$idCliSL.'/nMensagem:'.$mensagem;


    mail("hochlucassilva@gmail.com" , "Cobrança Liquidada", $msg, $headers);
}

function errHandle($errNo, $errStr, $errFile, $errLine) {
    global $idCli, $idCliSL;
    
    $msg = "$errStr in $errFile on line $errLine";
    enviaEmail($idCli, $idCliSL, $msg);
    if ($errNo == E_NOTICE || $errNo == E_WARNING) {
        throw new ErrorException($msg, $errNo);
    } else {
        echo $msg;
    }
    
}

?>




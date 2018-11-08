<?php

include "../../bd/conexao_bd_cadastro.php";
include "../resource/controle.func.php";

// isso aqui é um error handler. No final do arquivo tem uma função com o error handler chamada "errHandler"
// essa função é chamda quando acontece um erro ou um warning e ela me envia um email com oq raios aconteceu
set_error_handler('errHandle');

/*
PARA TESTE 

$conteudo = array(
                'data' => array(
                    'id_sacado_sac' => 284,
                    'id_recebimento_recb' => '22233',
                    'dt_recebimento_recb' => '05/20/2018',
                    'dt_geracao_recb' => '/05/18/2018',
                    'vl_emitido_recb' => '223,55',
                    'fl_status_recb' => 2,
                    'dt_vencimento_recb' => '05/23/2018',
                    'dt_liquidacao_recb'=> '05/19/2018',
                    'link_2via' => 'www.helpmua.com'
                ),
                'tentativa' => 2
            );
*/

$conteudo = $_POST; 
$agora = date("d/m/Y H:i:s");

file_put_contents('HTTPS_Cartao_negado.txt', $agora."\n".json_encode($conteudo)."\n\n", FILE_APPEND);

if(isset($conteudo['data']) && !empty($conteudo['data']))
{
 
    $idCliSL = $conteudo['data']['id_sacado_sac'];
    
    
    $selectCLI = "SELECT CLI_id FROM cliente WHERE CLI_id_sl = '$idCliSL'";
    file_put_contents('Cartao_negado_mysqli_error.txt',  $agora.mysqli_error($conCad), FILE_APPEND);
    $queryCLI = mysqli_query($conCad, $selectCLI);
    if($queryCLI){
        $arrayCLI = mysqli_fetch_array($queryCLI);
        $idCli = $arrayCLI['CLI_id'];

        if($conteudo['tentativa'] == 0 || $conteudo['tentativa'] == 1){
            alertaFalha($idCli, $idCliSL, "0-1Tentativa ".$conteudo['tentativa']."\n<br>".json_encode($conteudo));
        } elseif ($conteudo['tentativa'] > 1) {
            alertaFalha($idCli, $idCliSL, "1+Tentativa ".$conteudo['tentativa']."\n<br>".json_encode($conteudo));
            congela($idCli);
        } else {
            enviaEmail($idCli, $idCliSL, "SOMETHING VERY WRONG ".$conteudo['tentativa']."\n<br>".json_encode($conteudo));
        }
    } else {
        enviaEmail("Não tem", $idCliSL, "Não retornou cliente na consulta pelo id_sl".json_encode($conteudo)."\n<br>".mysqli_error($conCad));
    }


} else {
    enviaEmail("Não tem", "Não tem", "conteudo vazio ou <i>data</i> não setado".json_encode($conteudo));
}

echo "{\"status\":200}";

function alertaFalha($idCli, $idSuperLogica, $mensagem){
    global $conCad;

    $not_texto = "Foi constatado um problema no cartão de crédito informado para pagamento. Nosso suporte entrará em contato em breve.";


    $selectDuplicadaCartaoHoje = "SELECT NOT_titulo FROM notificacoes WHERE NOT_id_cli = $idCli and NOT_texto = '$not_texto' and NOT_data = CURRENT_DATE()";
    $resultDuplicadaCartaoHoje = mysqli_query($conCad, $selectDuplicadaCartaoHoje);

    if(!$resultDuplicadaCartaoHoje || mysqli_num_rows($resultDuplicadaCartaoHoje) == 0){
        $qNotErroCartao = "INSERT INTO notificacoes (NOT_id_cli, NOT_titulo, NOT_texto, NOT_data, NOT_status, NOT_icone) VALUES ($idCli, 'Problema com o cartão', '$not_texto', CURRENT_DATE(), 1, 'danger')";
        $resultNotErroCartao = mysqli_query($conCad, $qNotErroCartao);
    }

}

function enviaEmail($idCli, $idCliSL, $mensagem){
    //echo $mensagem;
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


    mail("hochlucassilva@gmail.com" , "Cartão negado", $msg, $headers);
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




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

file_put_contents('HTTPS_Cobranca_criada.txt', $agora."\n".json_encode($conteudo)."\n\n", FILE_APPEND);

if(isset($conteudo['data']) && !empty($conteudo['data']))
{
 
    $idCliSL = $conteudo['data']['id_sacado_sac'];
    $idTrans = $conteudo['data']['id_recebimento_recb'];
    $data_superlogica = $conteudo['data']['dt_recebimento_recb'];
    $status = 0; //status da cobrança criada é sempre 0;

    
    $selectCLI = "SELECT CLI_id FROM cliente WHERE CLI_id_sl = '$idCliSL'";
    file_put_contents('cobranca_criada_mysqli_error.txt',  $agora."\n".mysqli_error($conCad), FILE_APPEND);
    $resultCLI = mysqli_query($conCad, $selectCLI);
    if($resultCLI){
        $arrayCLI = mysqli_fetch_array($resultCLI);
        $idCli = $arrayCLI['CLI_id'];

        insereNovaFatura($conteudo['data'], $idCli);
    } else {
        enviaEmail("Não tem", "Não tem", "Não retornou cliente na consulta pelo id_sl".json_encode($conteudo)."\n<br>".mysqli_error($conCad));
    }
    


} else {
    enviaEmail("Não tem", "Não tem", "conteudo vazio ou <i>data</i> não setado".json_encode($conteudo));
}


echo "{\"status\":200}";

function insereNovaFatura($faturaSuperlogica, $idCli){
    global $conCad;
    $qInsertPag = 
    "INSERT INTO pagamento
        (PAG_id_cli, 
        PAG_id_trans, 
        PAG_data_emiss, 
        PAG_valor, 
        PAG_status, 
        PAG_data_venc, 
        PAG_data_liq, 
        PAG_link_2_via) 
    VALUES
        ('$idCli', 
        '".$faturaSuperlogica['id_recebimento_recb']."', 
        STR_TO_DATE('".$faturaSuperlogica['dt_geracao_recb']."', '%m/%d/%Y'), 
        '".$faturaSuperlogica['vl_emitido_recb']."', 
        '".$faturaSuperlogica['fl_status_recb']."', 
        STR_TO_DATE('".$faturaSuperlogica['dt_vencimento_recb']."', '%m/%d/%Y'), 
        STR_TO_DATE('".$faturaSuperlogica['dt_liquidacao_recb']."', '%m/%d/%Y'), 
        '".$faturaSuperlogica['link_2via']."')";
    
    $resultInsertPag = mysqli_query($conCad,$qInsertPag);
}

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


    mail("hochlucassilva@gmail.com" , "Cobrança Criada", $msg, $headers);
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




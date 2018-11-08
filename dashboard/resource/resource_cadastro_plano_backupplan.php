<?php

	require_once '../../bd/conexao_bd_cadastro.php';
    $idCli = mysqli_real_escape_string($conCad, $_POST['idCli']);

	$idPlan = mysqli_real_escape_string($conCad, $_POST['plano']);
	$valorPlano = mysqli_real_escape_string($conCad, $_POST['valor']);
	$views = mysqli_real_escape_string($conCad, $_POST['view']);
	$tempo= mysqli_real_escape_string($conCad, $_POST['tempo']);

	//$razaoSocial = mysqli_real_escape_string($conCad, $_POST['razaoSocial']); //aparentemente nao necessario
	$nomeEmpresa = mysqli_real_escape_string($conCad, $_POST['nomeEmpresa']); //aparentemente nao necessario (2)
	$responsavelRoiHero = mysqli_real_escape_string($conCad, $_POST['nomeResponsavel']);
	$endCep = mysqli_real_escape_string($conCad, $_POST['cep']);
	$endLog = mysqli_real_escape_string($conCad, $_POST['rua']);
	$endNum = mysqli_real_escape_string($conCad, $_POST['numero']);
	$endCid = mysqli_real_escape_string($conCad, $_POST['cidade']);
    $endEst = mysqli_real_escape_string($conCad, $_POST['estado']);
    $endBairro = mysqli_real_escape_string($conCad, $_POST['bairro']);
	$telefone = mysqli_real_escape_string($conCad, $_POST['telefone']);
	$ie = mysqli_real_escape_string($conCad, $_POST['inscricaoEstadual']);
	$emailResponsavel =mysqli_real_escape_string($conCad, $_POST['emailResponsavel']);
	$emailFinanceiro = 	mysqli_real_escape_string($conCad, $_POST['emailFinanceiro']);

    $cnpj = mysqli_real_escape_string($conCad, $_POST['cnpj']); // CNPJ
	$inscricaoEstadual = mysqli_real_escape_string($conCad, $_POST['inscricaoEstadual']); // INSCRICAO ESTADUAL


    $ch = curl_init(); 
    curl_setopt($ch, CURLOPT_URL, "https://api.superlogica.net/v2/financeiro/checkout"); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE); 
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); 
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array("ST_NOME_SAC"=>$nomeEmpresa,
                                                                "ST_EMAIL_SAC"=>$emailResponsavel,
                                                                "ST_CGC_SAC"=>$cnpj,
                                                                "ST_INSCRICAO_SAC"=>$inscricaoEstadual,
                                                                "ST_TELEFONE_SAC"=>$telefone,
                                                                "ST_CELULAR_SAC"=>$telefone,
                                                                "ST_CEP_SAC"=>$endCep,
                                                                "ST_ENDERECO_SAC"=>$endLog,
                                                                "ST_NUMERO_SAC"=>$endNum,
                                                                "ST_BAIRRO_SAC"=>$endBairro,
                                                                "ST_CIDADE_SAC"=>$endCid,
                                                                "ST_ESTADO_SAC"=>$endEst,
                                                                "FL_MESMOEND_SAC"=>"1",
                                                                "senha"=>$senha,
                                                                "senha_confirmacao"=>$senhac,
                                                                "FL_PAGAMENTOPREF_SAC"=>"0",
                                                                "idplano"=>$idPlan,
                                                                )));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/x-www-form-urlencoded",
    "app_token: BzL462rPGlXD",
    "access_token: H3mUEJQd37E1",
    ));														
               
    $response = curl_exec($ch);
    $info = curl_getinfo($ch);

    curl_close($ch);

    $erro = json_encode($response, true);
    $array_resp = json_decode($response, true);
    
    file_put_contents('response_SL.txt', print_r($array_resp, true));
        
    $err = 0;
    
    $erro1 = str_replace(":", "", $erro);
    $erro2 = str_replace("\\", "", $erro1);
    $erro3 = str_replace("msg", "", $erro2);
    $erro4 = str_replace("{", "", $erro3);
    $erro5 = str_replace('"', "", $erro4);
    $erro6 = str_replace("u00e1", "á", $erro5);
    $erro7 = str_replace("u00e3", "ã", $erro6);
    $erro8 = str_replace("u00e9", "é", $erro7);
    $erro9 = str_replace("u00e7", "ç", $erro8);
    $errotratado = str_replace("}", "", $erro9);
    
    //$queryPlano = "INSERT INTO plano (PLAN_id_cli, PLAN_plano, PLAN_data_venc, PLAN_valor, PLAN_views, PLAN_status) VALUES ('$id', '$idPlan', ADDDATE(CURRENT_DATE,7), '".$array_resp['valor_boleto']."', '".preg_replace("/[^0-9]/", "", $array_resp['st_descricao_pla'])."', '2')";

    $updatePlano = "UPDATE plano SET 
    PLAN_id_plano = $idPlan, 
    PLAN_data_venc = ADDDATE(CURRENT_DATE,7, $tempo), 
    PLAN_status = 1, 
    PLAN_views = $views
    WHERE PLAN_id_cli = $idCli";
    $update = mysqli_query($conCad, $updatePlano);

    echo $updatePlano;

    $updateCliente = "UPDATE cliente SET 
    CLI_inscricao_estadual = '$ie', 
    CLI_cnpj = '$cnpj', 
    CLI_id_sl = '".$array_resp['id_sacado_sac']."', 
    CLI_numero = $endNum, 
    CLI_rua = '$endLog', 
    CLI_bairro = '$endBairro',  
    CLI_cidade = '$endCid',
    CLI_estado = '$endEst',
    CLI_cep = '$endCep'

    WHERE 
    CLI_id = '$idCli'";
    //faltam os emails responsavel, financeiro e fiscal

    $query = mysqli_query($conCad, $updateCliente);
    

    //$update = "UPDATE cliente SET CLI_id_sl = '".$array_resp['id_sacado_sac']."' WHERE CLI_id = '$id'";
    //$query = mysqli_query($conCad, $update);
?>
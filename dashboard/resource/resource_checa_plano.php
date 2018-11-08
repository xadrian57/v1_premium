<?php
    require_once '../../bd/conexao_bd_cadastro.php';

    $id = mysqli_real_escape_string($conCad,$_POST['id']); // id cliente    

    // verifica se o plano é trial e quantos dias restantes
    $queryPlano = "SELECT PLAN_data_venc, PLAN_id_plano, PLAN_views FROM plano WHERE PLAN_id_cli = '$id'";
    $resultPlano = mysqli_query($conCad, $queryPlano);

    $arrayPlano = mysqli_fetch_array($resultPlano);

    $dataVencimento = $arrayPlano['PLAN_data_venc'];
    $date1 = date_create("$dataVencimento");
    $date2 = new DateTime('today');
    $diff = date_diff($date1,$date2);
    $diasRestantes =  $diff->format("%a"); //em string

    if($date2 > $date1)
        $diasRestantes = "-".$diasRestantes;
    
    $infoPlano = array(
        'dataVencimento' => $arrayPlano['PLAN_data_venc'],
        'viewsRestantes' => $arrayPlano['PLAN_views'],
        'id' => $arrayPlano['PLAN_id_plano'],
        'diasRestantes' => $diasRestantes
    );
    
    $data = json_encode($infoPlano);
    echo $data;  
?>
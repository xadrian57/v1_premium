<?php


    $cupom = $_POST['cupom'];    
    $plano = $_POST['plano'];
    $tempo = $_POST['tempo'];
    $metodo = $_POST['formaPagamento'];

    $valores = [0, 0, 374.4, 950.4];

    $valor = $valores[$plano];

    $novoValor = $valor * (1 - 0.375);


    $cupons = [
        ['RHSTP50C', 37.5, 2, 360, 'cartao'],
        ['RHPRO50C', 37.5, 3, 360, 'cartao'], 
        ['RHPBHEXA', 37.5, 3, 360, 'boleto'], 
        ['RHPCHEXA', 37.5, 3, 360, 'cartao'], 
        ['RHSBHEXA', 37.5, 2, 360, 'boleto'], 
        ['RHSCHEXA', 37.5, 2, 360, 'cartao'],
        ['RHSTP50', 37.5, 2, 360, 'boleto'],
        ['RHPRO50', 37.5, 3, 360, 'boleto']
        ];   

    $cupom = trim(strtoupper($cupom));

    $i = -1;

    foreach($cupons as $k => $v){
        if($cupom == $v[0]){
            $i = $k;
            break;
        }
    }

    $condicao = false;
    if($i != -1){
        $cupomArray = $cupons[$i];
        $condicao = ($cupomArray[2] == $plano) and ($cupomArray[3] == intval($tempo)) and ($cupomArray[4] == $metodo);
    }

    if($condicao){
        $resp = array(
            "status" => 1,
            "msg" => "Cupom validado com sucesso",
            "valor" => $novoValor,
            "desconto" => $cupomArray[1]
        ); 
        echo(json_encode($resp));
    } else {
        $resp = array(
            "status" => 0,
            "msg" => "Cupom não cadastrado",
            "valor" => 0.00,
            "desconto" => 0.00
        ); 
        echo(json_encode($resp));
    }



    

?>
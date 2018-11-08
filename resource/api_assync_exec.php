<?php

    include "central_de_apis.class.php";

    $api = $argv[1];
    $dados = $argv[2];


    $centralApis = new CentralApis();


    $dados = json_decode($dados, true);

    $resposta = "";



    switch($api){
        case 'octadesk':
            $resposta = $centralApis->octadesk($dados);
            break;
        case 'lahar':
            $resposta = $centralApis->lahar($dados);
            break;
        case 'slack':
            $resposta = $centralApis->slack($dados);
            break;
        case 'pipedrive':
            $resposta = $centralApis->pipedrive($dados);
            break;
    }

    print(json_encode($resposta));

?>
<?php

    session_name('premium');
    session_start();

    include("../../bd/conexao_bd_dados.php");
    include("../../bd/conexao_bd_cadastro.php");

    $idCli = $_SESSION['id'];
    echo $idCli;

    $qSelectExisteTabela = "SHOW TABLES LIKE 'XML_".$idCli."'";
    $resultExisteTabela = mysqli_query($conDados, $qSelectExisteTabela);

    if(mysqli_num_rows($resultExisteTabela) == 1){
        $qDropTabela = "DROP TABLE XML_".$idCli;
        $resultDropTabela = mysqli_query($conDados, $qDropTabela);
        if($resultDropTabela){
            $qDeleteCustomXml = "DELETE FROM customXML WHERE CXML_id_cliente = $idCli";
            mysqli_query($conCad, $qDeleteCustomXml);

            $qUpdateConfig = "UPDATE config SET CONF_xml = NULL WHERE CONF_id_cli = $idCli";
            mysqli_query($conCad, $qUpdateConfig);
        }
    }

?>
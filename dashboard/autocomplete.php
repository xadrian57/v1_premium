<?php

include "../bd/conexao_bd_dados.php";
session_name('premium');
session_start();
$idCli = $_SESSION['id'];
session_write_close();
 

if(isset($_POST["query"])){
    $output = '';
    $query = 
    "SELECT XML_titulo, XML_id 
    FROM XML_".$idCli." 
    WHERE XML_titulo LIKE '%".$_POST["query"]."%' order by XML_titulo";

    $result = mysqli_query($conDados, $query);

    //$output = '<ul id="listaAutocomplete" class="list-group">'; 
    $output = '<div id="listaAutocomplete" class="list-group list-group-flush list-group-hover">';
    if(mysqli_num_rows($result) > 0){

        while($row = mysqli_fetch_array($result)){
            //$output .= '<li class="list-group-item" onclick="preencheCampoAuto(\''.$row["XML_titulo"].'\', '.$row["XML_id"].')">'.$row["XML_titulo"].'</li>';
            $output .= '<a onclick="preencheCampoAuto(\''.$row["XML_titulo"].'\', '.$row["XML_id"].', '.$_POST["formato"].')" class="list-group-item" style="padding:12px;">
            '.$row["XML_titulo"].'
          </a>';
        }

    } else 
    {
        //$output.= '<li>Produto não encontrado</li>';
        $output.= '<a class="list-group-item list-group-item-action" style="background-color: #ffcfcf; color: ">Produto não encontrado</a>';
    }
    //$output .= '</ul>';
    $output .= '</div>';
    echo $output;

} 
?>
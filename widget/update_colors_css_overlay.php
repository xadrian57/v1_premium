<?php
    include '../bd/conexao_bd_cadastro.php';
    //inclui o objeto de comunicação com a api cloudflare
	include '../dashboard/resource/api_cloudflare.class.php';

    $idCli = mysqli_real_escape_string($conCad, $_GET['id']);
    $primary = mysqli_real_escape_string($conCad, $_GET['primary']);
    $secondary = mysqli_real_escape_string($conCad, $_GET['secondary']);

    // pega id do template
    $query = 'SELECT CONF_cor, CONF_template_overlay FROM config WHERE CONF_id_cli = '.$idCli;
    $exec = mysqli_query($conCad, $query);
    $result = mysqli_fetch_array($exec);
    $template = $result['CONF_template_overlay'];

    // Pega o arquivo css e substitui as cores
    $css = file_get_contents('templates/overlay_'.$template.'/style_to_replace.css');
    
    $css = str_replace( '{PRIMARY_COLOR}' , $primary , $css );
    $css = str_replace( '{SECONDARY_COLOR}' , $secondary , $css );

    @file_put_contents('css/overlay/rh_overlay_'.sha1($idCli).'.css',$css );

    //da purge no cache com a cloudflare
    $api = new cloudflare_api('davi.bernardes@roihero.com.br','1404cc5e783d0287897bfb2ebf7faa9e87eb5');

    $ident = $api->identificador('roihero.com.br');

    $arquivos = [
        'https://roihero.com.br/widget/css/rh_overlay_'.sha1($idCli).'.css'
    ];

    $api->purgeArquivos($ident,$arquivos);
?>

<div>Primária <div style="width: 50px;height:50px;background: rgb(<?= $primary ?>)"></div></div>
<div>Secundária <div style="width: 50px;height:50px;background: rgb(<?= $secondary ?>)"></div></div>
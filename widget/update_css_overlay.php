<?php
    include '../bd/conexao_bd_cadastro.php';
    //inclui o objeto de comunicação com a api cloudflare
	include '../dashboard/resource/api_cloudflare.class.php';

    // pega id do template
    $query = 'SELECT CONF_cor, CONF_template_overlay FROM config';
    $exec = mysqli_query($conCad, $query);
    
    while($result = mysqli_fetch_array($exec))
    {
        $template = $result['CONF_template_overlay'];
        $cor = json_decode($result['CONF_cor'], true);

        // Pega o arquivo css e substitui as cores
        $css = @file_get_contents('templates/overlay/kit_'.$template.'/style_to_replace.css');

        if(empty($css))
        {
            $css = str_replace( '{PRIMARY_COLOR}' , $cor['primary'] , $css );
            $css = str_replace( '{SECONDARY_COLOR}' , $cor['secondary'] , $css );

            file_put_contents('css/overlay/rh_overlay_'.sha1($idCli).'.css',$css );

            //da purge no cache com a cloudflare
            $api = new cloudflare_api('moises.dourado@roihero.com.br','1404cc5e783d0287897bfb2ebf7faa9e87eb5');

            $ident = $api->identificador('roihero.com.br');

            $arquivos = [
                'https://roihero.com.br/widget/css/overlay/rh_overlay_'.sha1($idCli).'.css'
            ];

            $api->purgeArquivos($ident,$arquivos);
        }
    }
?>
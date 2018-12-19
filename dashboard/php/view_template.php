<?php
	// DADOS DA PÁGINA 
	$pagina_nome = 'visualizar_template';
	$pagina_titulo = 'Visualizar Template';

    require_once('../resource/resource_verifica_sessao.php');
    $idTemplate = $_GET['id'];

    $corPrimaria = $_GET['primary'];
    $corSecundaria = $_GET['secondary'];

    $overlaySaida = file_get_contents('../../widget/templates/overlay/kit_'.$idTemplate.'/nao_va_embora.html');
    $ofertaLimitada = file_get_contents('../../widget/templates/overlay/kit_'.$idTemplate.'/oferta_limitada.html');

    $css = file_get_contents('../../widget/templates/overlay/kit_'.$idTemplate.'/style_to_replace.css');

    // substitui informacoes
    $css = str_replace('{PRIMARY_COLOR}', $corPrimaria, $css);
    $css = str_replace('{SECONDARY_COLOR}', $corSecundaria, $css);

    function setOverlay( $overlaySaida ) {
        $overlaySaida = str_replace('{LINK_BANNER_BLOCK}', '#', $overlaySaida);
        $overlaySaida = str_replace('{BANNER_BLOCK}', '../widget/images/overlay/banner_overlay_default.png', $overlaySaida);

        $overlaySaida = str_replace('{TITLE_BLOCK}', 'Título do bloco exemplo', $overlaySaida);
        $overlaySaida = str_replace('{SUBTITLE_BLOCK}', 'Subtítulo do bloco exemplo', $overlaySaida);
        $overlaySaida = str_replace('{PRODUCT_URL}', '#', $overlaySaida);
        $overlaySaida = str_replace('{PRODUCT_DISCOUNT}', '15', $overlaySaida);
        $overlaySaida = str_replace('{PRODUCT_IMG}', 'assets/images/app/hero.jpg', $overlaySaida);
        $overlaySaida = str_replace('{PRODUCT_NAME}', 'Nome produto exemplo nome produto exemplo', $overlaySaida);
        $overlaySaida = str_replace('{VALUE_DE}', 'R$ 70,00', $overlaySaida);
        $overlaySaida = str_replace('{VALUE}', 'R$ 50,00', $overlaySaida);
        $overlaySaida = str_replace('{AMOUNT_PLOTS}', '15', $overlaySaida);
        $overlaySaida = str_replace('{VALUE_PLOTS}', 'R$ 22,90', $overlaySaida);

        return $overlaySaida;
    }

    function setOfertaLimitada( $ofertaLimitada ) {

        $ofertaLimitada = str_replace('{TITLE_BLOCK_0}', 'Título do bloco exemplo', $ofertaLimitada);
        $overlaySaida = str_replace('{SUBTITLE_BLOCK_0}', 'Subtítulo do bloco exemplo', $ofertaLimitada);
        $ofertaLimitada = str_replace('{PRODUCT_URL_0}', '#', $ofertaLimitada);
        $ofertaLimitada = str_replace('{PRODUCT_DISCOUNT_0}', '15', $ofertaLimitada);
        $ofertaLimitada = str_replace('{PRODUCT_IMG_0}', 'assets/images/app/hero.jpg', $ofertaLimitada);
        $ofertaLimitada = str_replace('{PRODUCT_NAME_0}', 'Nome produto exemplo nome produto exemplo', $ofertaLimitada);
        $ofertaLimitada = str_replace('{VALUE_DE_0}', 'R$ 70,00', $ofertaLimitada);
        $ofertaLimitada = str_replace('{VALUE_0}', 'R$ 50,00', $ofertaLimitada);
        $ofertaLimitada = str_replace('{AMOUNT_PLOTS_0}', '15', $ofertaLimitada);
        $ofertaLimitada = str_replace('{VALUE_PLOTS_0}', 'R$ 22,90', $ofertaLimitada);

        return $ofertaLimitada;
    }

    $overlaySaida = setOverlay( $overlaySaida );
    $ofertaLimitada = setOfertaLimitada( $ofertaLimitada );
    
?>
<!doctype html>
<html style="overflow:hidden;">
    <head>
        <title>Visualizar Template</title>
        <script>rhClientId = 0;</script>
        <style><?= $css ?></style>
        <style>
            body {
                min-height: 100vh;
                position: relative;
            }
        </style>
    </head>
    <body>
        <h4 style="position: absolute; width: 100%;font-family: verdana, arial, sans-serif;top: 45%;text-align:center;color: #333;">
            COLOQUE O MOUSE PARA FORA DO MODAL PARA APARECER O OVERLAY DE SAÍDA
        </h4>

        <div id="containerOverlays">


            <?= $overlaySaida ?>
            <script>
                var list = document.querySelector('.rh-product-overlay-list');
                var product = list.querySelector('.rh-product-overlay');

                for (let i = 0; i < 10; i++) {                    
                    let cp = product.cloneNode(true);

                    list.appendChild(cp);
                }                
            </script>
            <?= $ofertaLimitada ?>
        </div>
        <script src="../widget/js/funcoes_widgets.js"></script>
    </body>
</html>
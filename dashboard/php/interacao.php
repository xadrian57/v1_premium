<?php
    // DADOS DA PÁGINA 
    $pagina_nome = 'relatorio_interacao';
    $pagina_titulo = 'Relatório Interação';

    require_once('../resource/resource_verifica_sessao.php');
    $plano = $_SESSION['idPlan'];
    // Caso seja plano free, redireciona pro overview
    if ($plano == 1) {
    	header('Location: overview');
    }
	require_once('../content/content_header.html');
	require_once('../content/content_interacao.html');
	require_once('../content/content_modal_cadastro.html');
	require_once('../content/content_footer.html');
?>
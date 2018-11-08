<?php
    // DADOS DA PÁGINA 
    $pagina_nome = 'minhas_recomendacoes';
    $pagina_titulo = 'Minhas Recomendações';
    
    include_once '../../bd/conexao_bd_dashboard.php';
    include '../../bd/conexao_bd_dados.php';
    
    include '../resource/resource_verifica_sessao.php';
    
	include '../content/content_header.html';
	include '../content/content_meus_widgets.inc.php';
	include '../content/content_modal_cadastro.html';
	include '../content/content_footer.html';
?>
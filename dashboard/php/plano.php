<?php
	// DADOS DA PÁGINA 
	$pagina_nome = 'meu_plano';
	$pagina_titulo = 'Meu Plano';
	
    require_once('../resource/resource_verifica_sessao.php');
	require_once('../content/content_header.html');
	//TRIAL = 0; FREE = 1; STARTUP = 2; PRO = 3; ROCKET = 4; PREMIUM = 42;
	switch ($_SESSION['idPlan']) {
		case '0': //trial
			require_once('../content/content_plano_pro.html');
			break;
		case '1': //free
			require_once('../content/content_plano_free.html');
			break;
		case '2': //startup
			require_once('../content/content_plano_startup.html');
			break;
		case '3': //pro
			require_once('../content/content_plano_pro.html');
			break;
		case '4': //rocket
			require_once('../content/content_plano_rocket.html');
			# code...
			break;
		case '42': //vip
			require_once('../content/content_plano_vip.html');
			break;
		default:
			# code...
			break;
	}
	require_once('../content/content_modal_cadastro.html');
	require_once('../content/content_footer.html');
?>
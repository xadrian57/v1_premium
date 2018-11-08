<?php
	session_name('premium');
	session_start();	
	if (!(  isset($_SESSION['email']) && isset($_SESSION['senha']) && isset($_SESSION['id'])  ))
	{
        unset ($_SESSION['nome']);
		unset ($_SESSION['email']);
		unset ($_SESSION['senha']);
        unset ($_SESSION['id']);
        unset ($_SESSION['idPlano']);
        unset ($_SESSION['idPlataforma']);
        header('Location: ../login');
    }

    /*
		TODOS OS REDIRECIONAMENTOS DO DASHBOARD SÃO FEITOS POR AQUI
		SEMPRE COMENTAR A PÁGINA QUE ESTÁ SENDO REDIRECIONADA
    */

	// checa se existe o caminho pra puxar o arquivo do lugar correto
	$path = "../bd/conexao_bd_dashboard.php";
	if (!file_exists($path)){
		$path = "../".$path;
	}
	include_once $path;
	

    $plano = $_SESSION['idPlan']; // plano cliente
    $plataforma = $_SESSION['idPlataforma'];
    $idCli = $_SESSION['id'];

    $qContaRwid = "SELECT COUNT(t.REL_id) as inserts FROM (SELECT REL_id FROM rel_".$idCli." LIMIT 1)t";
    $result = mysqli_query($conDash,$qContaRwid);
    $insertsRwid = 0;
    if($result != FALSE){ //se a tabela rwid existe, muda o valor de $insertsRwid
    	$array = mysqli_fetch_array($result);
		$insertsRwid = $array['inserts'];
    }	

   	switch ($pagina_nome) {
		case 'widget_maker':
		    // Caso seja plano FREE ou PRO, redireciona pro overview
		    if ($plano != 42) {
		        header('Location: overview');
		    }
			break;
		case 'relatorio_performance':
			if ($insertsRwid < 1) { // se não tiver pelo menos 200 inserções em rwid, redireciona pra overview
				header('Location: overview');
			}
		    // Caso seja plano FREE, redireciona pro overview
		    if ($plano == '1') {
		    	header('Location: overview');
		    }
			if ($plataforma == 0){ // plataformas não integradas, redirecionar relatórios
		    	header('Location: overview');
			}
			break;
		case 'relatorio_transacoes':
			if ($insertsRwid < 1) { // se não tiver pelo menos 200 inserções em rwid, redireciona pra overview
				header('Location: overview');
			}
			if ($plataforma == 0){ // plataformas não integradas, redirecionar relatórios
		    	header('Location: overview');
			}
			break;
		case 'relatorio_interacao':
			if ($insertsRwid < 1) { // se não tiver pelo menos 200 inserções em rwid, redireciona pra overview
				header('Location: overview');
			}
		case 'faturas':
		    // Caso seja plano free, redireciona pro overview
		    if ($plano == 1 || $plano == 0) {
		    	header('Location: overview');
		    }
			break;		
		default:
			break;
	}


?>
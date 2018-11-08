<?php
	// session_start inicia a sessão
	session_name('premium');
	session_start();

    require_once('../../bd/conexao_bd_cadastro.php');
	
	// as variáveis email e senha recebem os dados digitados na página anterior
	$email = mysqli_real_escape_string($conCad, $_POST['email']);
	$senha = mysqli_real_escape_string($conCad, sha1($_POST['password']));

	// A vriavel $result pega as varias $email e $senha, faz uma pesquisa na tabela de usuarios
	$query = "SELECT CLI_nome, CLI_id, CLI_ativo,CLI_id_plataforma FROM cliente WHERE CLI_email = '$email' AND CLI_senha = '$senha'";
	$result = mysqli_query($conCad,$query);

	/* Logo abaixo temos um bloco com if e else, verificando se a variável $result foi bem sucedida, 
	ou seja se ela estiver encontrado algum registro idêntico o seu valor será igual a 1, se não, 
	se não tiver registros seu valor será 0. Dependendo do resultado ele redirecionará para a pagina index.html ou 
	retornara  para a pagina do formulário inicial para que se possa tentar novamente realizar o email */	
	if(mysqli_num_rows($result)>0)
	{
		$array = mysqli_fetch_array($result);
		$ativado = $array['CLI_ativo'];
        $id = $array['CLI_id'];
        $nome = $array['CLI_nome'];
        $idPlataforma = $array['CLI_id_plataforma'];		
		if( true /*$ativado==1*/)
		{			
		    $select = "SELECT PLAN_id_plano, CONF_moeda FROM plano, config WHERE PLAN_id_cli = '$id' and CONF_id_cli = '$id'";
		    $query = mysqli_query($conCad, $select);
		    $array = mysqli_fetch_array($query);
			$idPlan = $array['PLAN_id_plano'];
			$moeda = $array['CONF_moeda'];

			$_SESSION['nome'] = $nome;
			$_SESSION['email'] = $email;
			$_SESSION['senha'] = $senha;
            $_SESSION['id'] = $id;
            $_SESSION['idPlan'] = $idPlan;
			$_SESSION['idPlataforma'] = $idPlataforma;
			$_SESSION["currency"] = $moeda;

            $insert = "INSERT INTO login (LOG_id_cli, LOG_data) VALUES ('$id', CURRENT_DATE())";
            $query = mysqli_query($conCad, $insert);
            
            header('Location: ../overview');
		}
		else
		{
		    $result2 = mysqli_query($conCad, "SHOW TABLES LIKE 'VIEW_".$id."'");

			if(mysqli_num_rows($result2) > 0)
			{
    		    $select1 = "SELECT count(VIEW_id) as views FROM VIEW_".$id." WHERE VIEW_data BETWEEN SUBDATE(CURRENT_DATE,30) AND CURRENT_DATE()";
    		    $query1 = mysqli_query($conCad, $select1);
    		    $array1 = mysqli_fetch_array($query1);
    		    $impressoes = $array1['views'];
    		    
    		    $select2 = "SELECT PLAN_views FROM plano WHERE PLAN_id_cli = '$id'";
    		    $query2 = mysqli_query($conCad, $select2);
    		    $array2 = mysqli_fetch_array($query2);
    		    $viewsPlano = $array2['PLAN_views'];
    		    
    		    $impressoes = 1.15 * $impressoes;
    		    
    		    // PLANO ESGOTADO
    		    if($impressoes > $viewsPlano)
    		    {        		    
        			unset ($_SESSION['nome']);
        			unset ($_SESSION['email']);
        			unset ($_SESSION['senha']);
                    unset ($_SESSION['id']);
                    echo "Seu plano foi totalmente consumido, por favor entre em contato com o suporte para realizar um upgrade em seu plano.";         
    		    }
			// CONTA PRECISA SER ATIVADA
    		    // else
    		    // {
        		// 	unset ($_SESSION['email']);
        		// 	unset ($_SESSION['senha']);
          //           unset ($_SESSION['id']);
          //           echo "Esta conta ainda não está ativada, acesse seu email para encontrar o link de ativação.";
    		        
    		    // }
			}
			// CONTA PRECISA SER ATIVADA
		    // else
		    // {
    		// 	unset ($_SESSION['email']);
    		// 	unset ($_SESSION['senha']);
      //           unset ($_SESSION['id']);
      //           echo "Esta conta ainda não está ativada, acesse seu email para encontrar o link de ativação.";
		        
		    // }
		    
		}
		
	}
	// SENHA INCORRETA
	else 
	{
        unset ($_SESSION['nome']);
		unset ($_SESSION['email']);
		unset ($_SESSION['senha']);
        unset ($_SESSION['id']);
		unset ($_SESSION['idPlan']);
        unset ($_SESSION['idPlataforma']);
        echo "Email ou senha incorretos!";
        header('Location: ../../login');
	}	
?>

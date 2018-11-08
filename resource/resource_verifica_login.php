<?php	
	session_name('premium');
	session_start();	
	require_once '../bd/conexao_bd_cadastro.php';
	$email = mysqli_real_escape_string($conCad, $_POST['email']);
	$senha = mysqli_real_escape_string($conCad, sha1($_POST['password']));

	// A vriavel $result pega as varias $email e $senha, faz uma pesquisa na tabela de usuarios
	$query = "SELECT CLI_nome, CLI_id, CLI_ativo,CLI_id_plataforma FROM cliente WHERE CLI_email = '$email' AND CLI_senha = '$senha'";
	$result = mysqli_query($conCad,$query);

	echo mysqli_error($conCad);
    
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
		    $select = "SELECT PLAN_id_plano, CONF_moeda FROM plano, config WHERE PLAN_id_cli = '$id' and PLAN_id_cli = CONF_id_cli";
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

            $insert = "INSERT INTO login (LOG_id_cli) VALUES ('$id')";
            $query = mysqli_query($conCad, $insert);
            
            echo "1";
		} else {
            echo "2";
		}
	}
	else {
		echo "0";
	}

?>
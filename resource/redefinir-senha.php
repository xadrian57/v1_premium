<?php
    include '../bd/conexao_bd_cadastro.php';

    $senha = mysqli_real_escape_string($conCad,$_POST['senha']);
    $senhaConfirmacao = mysqli_real_escape_string($conCad,$_POST['senhaConfirmacao']);

    if($senha != $senhaConfirmacao){
        $erro = json_encode(array('status' => '0', 'msg' => 'Senha e Confirmação de senha devem ser iguais.'));
        exit($erro);
    }
    if(strlen($senha) < 6 or strlen($senha) > 12){
        $erro = json_encode(array('status' => '0', 'msg' => 'Senha deve ter entre 6 e 12 caracteres.'));
        exit($erro);
    }

    $senha = sha1($senha);
    
    $token = mysqli_real_escape_string($conCad,$_GET['token']);

    if ($token == null or $token == ""){
        $erro = json_encode(array('status' => '0', 'msg' => 'Link inválido!'));
        exit($erro);
    }
    
    $qUpdateSenha = "UPDATE cliente SET CLI_senha = '$senha', CLI_token_senha = NULL WHERE CLI_token_senha = '$token'";
    $resultUpdateSenha = mysqli_query($conCad, $qUpdateSenha);

    if($resultUpdateSenha){
        header('Location: ../confirmacao-senha-redefinida.html');
    } else {
        $erro = json_encode(array('status' => '0', 'msg' => 'Não foi possível redefinir a senha. Por favor, verifique sua conexão'));
		exit($erro);
    }
?>
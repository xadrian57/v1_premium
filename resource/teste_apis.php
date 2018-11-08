<?php

    include 'central_de_apis.class.php';


    //$idCli = "272";
    $nome = "Loja Teste";
    $email = "email@teste.com";
    $nomeResponsavel = "Nome Teste";
    $site = "www.site.teste.com";
    $telefone = "12987878787";
    $plataforma = "plat";

    $centralApis = new CentralApis();

    $dados = array(
        'email_contato' => $email, // Texto, 100 caracteres, email, OBRIGATÓRIO
        'nome_contato' => $nomeResponsavel, // Texto, 100 caracteres
        'nome_empresa' => $nome, // Texto, 100 caracteres
        'site_empresa' => $site, // Texto, 100 caracteres
        'tel_empresa' => $telefone, // Numérico (pode receber número formatado, ex. (14) 3222-1415)
        'email_empresa' => $email // Texto, 100 caracteres, email
     );

     integra_api_slack_app($nome, $plataforma, $email, $telefone, $site, $nomeResponsavel);



    
	function integra_api_lahar($dados) {
		global $centralApis;
		
		$dados['nome_formulario'] = "cadastro";
		$dados['url_origem'] = 'https://www.roihero.com.br/';

		$centralApis->lahar($dados, false);
		
		
	}

	function integra_api_slack_app($nome, $plataforma, $email, $telefone, $site, $nomeResponsavel){
		global $centralApis;


		$msg = "Nome do Cliente: ".$nome;
		$msg .= "\nPlataforma: ".$plataforma;
		$msg .= "\nE-mail: ".$email;
		$msg .= "\nTelefone: ".$telefone;
		$msg .= "\nSite: ".$site;
		$msg .= "\nNome Responsável: ".$nomeResponsavel;

		$dados = array(
		'text' => $msg,
		'username' => 'novo_cadastro'
		);

		$centralApis->slack($dados, false);
		
	}

	function integra_api_pipedrive($nome, $email, $telefone, $nomeResponsavel){
		global $centralApis;

		$dados = array(
			'nome' => $nome,
			'email' => $email,
			'telefone' => $telefone,
			'nomeResponsavel' => $nomeResponsavel
		);

		$centralApis->pipedrive($dados, false);
	} 

	function postOctaDesk($nome, $email, $plataforma){ 		
		global $centralApis;

		$dados = array(
			'nome' => $nome,
			'email' => $email,
			'plataforma' => $plataforma
		);

		$centralApis->octadesk($dados, false);
	}

    

?>
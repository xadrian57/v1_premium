<?php
/* FOI COMENTADO PARA QUE NÃO DÊ ERRO QUANDO FOR CHAMAR O RESOURCE. DEPOIS ISSO TEM QUE SER RETIRADO

	session_name('premium');
 	session_start();

 	include "../../bd/conexao_bd_cadastro.php";

	$msg = mysqli_real_escape_string($conCad, $_POST['mensagem']);


	$idCli = $_SESSION['id'];
	$idPlano = $_SESSION['idPlan'];
	$nome = $_SESSION['nome'];
	$email = $_SESSION['email'];


	$nomesPlanos = array(
		1 => "Free",
		2 => "Startup",
		3 => "Pro",
		4 => "Rocket",
		42 => "VIP"
	);
	$nomeNovoPlano = $nomesPlanos[$idPlano];


	$emailUrl= urlencode($email);
	$service_url = "https://api.octadesk.services/persons/?email=".$emailUrl;

	$curl = curl_init($service_url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_HTTPHEADER, array(
		"Content-Type: application/json",
		"Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWJkb21haW4iOiJyb2loZXJvIiwiaXNzdWVkYXRlIjoiMjAxOC0wMS0yNFQxOToxODoyNC4wNzJaIiwiaXNzIjoiYXBpLm9jdGFkZXNrLnNlcnZpY2VzIiwicm9sZSI6MiwiZW1haWwiOiJkYW5pbG8ucHJhZG9Acm9paGVyby5jb20uYnIiLCJuYW1lIjoiRGFuaWxvIFByYWRvIiwidHlwZSI6MSwiaWQiOiJiOTc5MzBmMS02ZGE4LTRlMDItOWIzMi0wYjUyMWQ4OGJmNjYiLCJyb2xlVHlwZSI6MiwicGVybWlzc2lvblR5cGUiOjEsInBlcm1pc3Npb25WaWV3IjoxLCJpYXQiOjE1MTY4MjE1MDR9.Xgq-TaWdhIShkc_pubMfuKriCRJRZXFqrkfbfn2bu0k"
	));
	$curl_response = curl_exec($curl);
	curl_close($curl);
	$decoded = json_decode($curl_response, true);

	$idOcta = $decoded['Id'];


	$a = date("Y-m-d H:i:s");

	$url = "https://api.octadesk.services/tickets";
	$data = '{
	  "requester": {
	    "id": "'.$idOcta.'",
	    "email": "'.$email.'",
	    "name": "'.$nome.'"
	  },
	"description": "Primeiras Mensagens:\r\n'.$nome.': '.$msg.'",
	"descriptionAsText": "Primeiras Mensagens:\n'.$nome.': '.$msg.'",
	"typeName" : "Dúvida",
	"summary": "Chat com '.$nome.' iniciada às '.$a.'",
	"customField": {"Plano:" : "'.$nomeNovoPlano.'"},
	"inbox": {
	    "domain": "'.$idOcta.'",
	    "email": "'.$email.'"
	  },
	  "numberChannel": 3,
	  "comments": {
		    "public": {
		      "content": "'.$msg.'"
		    }
		  }
	}';

	$ch = curl_init($url);
	// Adiciona as opções:
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		"Content-Type: application/json",
		"Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWJkb21haW4iOiJyb2loZXJvIiwiaXNzdWVkYXRlIjoiMjAxOC0wMS0yNFQxOToxODoyNC4wNzJaIiwiaXNzIjoiYXBpLm9jdGFkZXNrLnNlcnZpY2VzIiwicm9sZSI6MiwiZW1haWwiOiJkYW5pbG8ucHJhZG9Acm9paGVyby5jb20uYnIiLCJuYW1lIjoiRGFuaWxvIFByYWRvIiwidHlwZSI6MSwiaWQiOiJiOTc5MzBmMS02ZGE4LTRlMDItOWIzMi0wYjUyMWQ4OGJmNjYiLCJyb2xlVHlwZSI6MiwicGVybWlzc2lvblR5cGUiOjEsInBlcm1pc3Npb25WaWV3IjoxLCJpYXQiOjE1MTY4MjE1MDR9.Xgq-TaWdhIShkc_pubMfuKriCRJRZXFqrkfbfn2bu0k"
	));
	      
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

	// Executa:
	$resultado = curl_exec($ch);

	// Encerra CURL:
	curl_close($ch); 
*/
?>
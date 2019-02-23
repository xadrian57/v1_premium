<?php 

$nome = $_POST['nome'];
$telefone = $_POST['telefone'];
$email = $_POST['email'];
$site = $_POST['site'];
$mensagem = $_POST['mensagem'];

$headers .= "Reply-To: ROI HERO <acesso@roihero.com.br>\r\n";
$headers .= "Return-Path: Roi Hero <acesso@roihero.com.br>\r\n";
$headers .= "From: Roi Hero <acesso@roihero.com.br>\r\n";
$headers .= "Organization: Roi Hero - Recomendações Inteligentes\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "X-Priority: 3\r\n";
$headers .= "X-Mailer: PHP". phpversion() ."\r\n";

$message = "Houve uma nova tentativa de contato através do site, os dados do lead são os seguintes:\n\nNome: ".$nome."\nE-mail: ".$email."\nTelefone: ".$telefone."\nSite: ".$site."\nPlataforma: ".$plataforma."\n\nE deixou a mensagem: ".$mensagem.".";
mail('test-a3ahx@mail-tester.com' , "Novo Contato pelo Site",$message,$headers);

?>

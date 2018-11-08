<?php

$nome       = $_POST['nome'];
$email      = $_POST['email'];
$url        = $_POST['url'];
$plataforma = $_POST['plataforma'];



$headers .= "Reply-To: Roi Hero <atendimento@roihero.com.br>\r\n";
$headers .= "Return-Path: Roi Hero <atedimento@roihero.com.br>\r\n";
$headers .= "From: <".$email.">";
$headers .= 'Cc: marcos.jesus@roihero.com.br, davi.bernardes@roihero.com.br' . "\r\n";
$headers .= "Organization: Roi Hero - Recomendações Inteligentes\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-type: text/plain; charset=iso-8859-1\r\n";
$headers .= "X-Priority: 3\r\n";
$headers .= "X-Mailer: PHP". phpversion() ."\r\n";


$message = "Um cliente entrou em contato:\n\n\nNome: " . $nome."\n\nEmail: ".$email."\n\nURL: ".$url."\n\nPlataforma: ".$plataforma;
mail("paulo.castello.branco@roihero.com.br" , "Cliente entrou em contato pela página provisória", $message,$headers);

header("Location: ../index.html");
die();

?>
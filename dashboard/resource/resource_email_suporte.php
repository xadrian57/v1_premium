<?php
session_name('rhlite');
session_start();
session_write_close();

require_once('/home/roihero/public_html/lite/conexao_bd.php');
    
mysqli_set_charset($con, 'utf8');
$name = $_POST['nome'];
$email = $_POST['email'];
$assunto  = $_POST['assunto'];

$headers .= "Reply-To: Roi Hero <atendimento@roihero.com.br>\r\n";
$headers .= "Return-Path: Roi Hero <atedimento@roihero.com.br>\r\n";
$headers .= "From: <".$email.">";
$headers .= 'Cc: keila.wollmer@roihero.com.br' . "\r\n";
$headers .= "Organization: Roi Hero - Recomendações Inteligentes\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-type: text/plain; charset=iso-8859-1\r\n";
$headers .= "X-Priority: 3\r\n";
$headers .= "X-Mailer: PHP". phpversion() ."\r\n";


$message5 = "O cliente ".$name." solicitou ajuda do suporte pelo Dashboard. O e-mail do cliente é: " . $email."\n\n O dúvida é: ".$assunto."";
mail("marcos@roihero.com.br" , "Cliente precisa de ajuda!", $message5,$headers5[$k]);


?>
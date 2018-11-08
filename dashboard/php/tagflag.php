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
    exit;
}

// Criando o token do tagflag
include '../../bd/conexao_adm_tokn.php';
$sql = 'call sCreateTokenTagflag(' . $_SESSION['id'] . ')';
$result = mysqli_query($conToken, $sql);
$linha = mysqli_fetch_array($result);
$token = $linha['txCode'];

@mysqli_close($conToken);



// Realizando a leitura dos dados do cliente
include '../../bd/conexao_bd_cadastro.php';

$sql = 'SELECT CLI_site FROM cliente WHERE CLI_id = ' . $_SESSION['id'];
$result = mysqli_query($conCad, $sql);
$linha = mysqli_fetch_array($result);
$url = $linha['CLI_site'];

@mysqli_close($conToken);

// Verificando se o último caracter não é /
if(substr($url, -1) != '/') {
    $url .= '/';
}

$url .= '?roihero-tagflag=' . $token;

// Redirecionando para o site do cliente
header('Location: ' . $url);
exit;
?>
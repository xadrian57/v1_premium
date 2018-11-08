<?php
// conexão com banco de dados
require_once('../../bd/conexao_bd_cadastro.php');

// id cliente
$idCLI = mysqli_real_escape_string($conCad,$_POST['id']);

// post
$nome = $_POST['nome'];
$cnpj = $_POST['cnpj'];
$inscricao = $_POST['inscricao'];
$rua = $_POST['rua'];
$numero = $_POST['numero'];
$complemento = $_POST['complemento'];
$bairro = $_POST['bairro'];
$cep = $_POST['cep'];
$cidade = $_POST['cidade'];
$estado = $_POST['estado'];
$site = $_POST['site'];
$telefone = $_POST['telefone'];
$email = $_POST['email'];

function saveData(){
    if($rua == NULL OR $rua == '')
    {
        $query = mysqli_query($conCad, 'SELECT CLI_rua FROM cliente WHERE CLI_'.$idCLI.'');
        $dado = mysqli_fetch_array($query);
        $rua = $dado['CLI_rua'];
    }
    if($numero == NULL OR $numero == '')
    {
        $query = mysqli_query($conCad, 'SELECT CLI_numero FROM cliente WHERE CLI_'.$idCLI.'');
        $dado = mysqli_fetch_array($query);
        $numero = $dado['CLI_numero'];
    }
    if($complemento == NULL OR $complemento == '')
    {
        $query = mysqli_query($conCad, 'SELECT CLI_complemento FROM cliente WHERE CLI_'.$idCLI.'');
        $dado = mysqli_fetch_array($query);
        $complemento = $dado['CLI_complemento'];
    }
    if($bairro == NULL OR $bairro == '')
    {
        $query = mysqli_query($conCad, 'SELECT CLI_bairro FROM cliente WHERE CLI_'.$idCLI.'');
        $dado = mysqli_fetch_array($query);
        $bairro = $dado['CLI_bairro'];
    }
    if($cep == NULL OR $cep == '')
    {
        $query = mysqli_query($conCad, 'SELECT CLI_cep FROM cliente WHERE CLI_'.$idCLI.'');
        $dado = mysqli_fetch_array($query);
        $cep = $dado['CLI_cep'];
    }
    if($cidade == NULL OR $cidade == '')
    {
        $query = mysqli_query($conCad, 'SELECT CLI_cidade FROM cliente WHERE CLI_'.$idCLI.'');
        $dado = mysqli_fetch_array($query);
        $cidade = $dado['CLI_cidade'];
    }
    if($estado == NULL OR $estado == '')
    {
        $query = mysqli_query($conCad, 'SELECT CLI_estado FROM cliente WHERE CLI_'.$idCLI.'');
        $dado = mysqli_fetch_array($query);
        $estado = $dado['CLI_estado'];
    }
    if($site == NULL OR $site == '')
    {
        $query = mysqli_query($conCad, 'SELECT CLI_site FROM cliente WHERE CLI_'.$idCLI.'');
        $dado = mysqli_fetch_array($query);
        $site = $dado['CLI_site'];
    }
    if($telefone == NULL OR $telefone == '')
    {
        $query = mysqli_query($conCad, 'SELECT CLI_telefone FROM cliente WHERE CLI_'.$idCLI.'');
        $dado = mysqli_fetch_array($query);
        $telefone = $dado['CLI_telefone'];
    }
    if($email == NULL OR $email == '')
    {
        $query = mysqli_query($conCad, 'SELECT CLI_email FROM cliente WHERE CLI_'.$idCLI.'');
        $dado = mysqli_fetch_array($query);
        $email = $dado['CLI_email'];
    }

    $updateDados = "UPDATE cliente SET CLI_nome = '$nome', CLI_cnpj = '$cnpj', CLI_inscricao = '$inscricao', CLI_rua = '$rua', CLI_numero = '$numero', CLI_complemento = '$complemento', CLI_bairro = '$bairro', CLI_cep = '$cep', CLI_cidade = '$cidade', CLI_estado = '$estado', CLI_site = '$site', CLI_telefone = '$telefone', CLI_email = '$email'";
    $query = mysqli_query($conCad, $updateDados);

}

?>

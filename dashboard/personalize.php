<?php
// Este arquivo é utilizado pelo administrativo.
// A ação de personalizar para o cliente, é executada aqui.
// Muito cuidado em qualquer alteração. Dúvidas: tiago.ferreira@roihero.com.br
if (! (array_key_exists ( 
                        'idClient',
                        $_GET ) && array_key_exists ( 
                                                    'token',
                                                    $_GET ))) {
    // Se não forem passadas as variáveis corretamente na url.
    // Será informado que esta página não existe.
    // E A EXECUÇÃO SERÁ FINALIZADA.
    http_response_code ( 
                        404 );
    exit ();
}

include '../bd/conexao_adm_cli.php';
include '../bd/conexao_bd_cadastro.php';

$idClient = $_GET ['idClient'];
$token = $_GET ['token'];

// Validando o Token
$sqlProc = 'call sValidateToken(\'' . $token . '\')';
$result = mysqli_query ( 
                        $conAdm,
                        $sqlProc );
$row = mysqli_fetch_array ( 
                            $result );

if ($row ['result'] != 1) {
    echo '<h1 style="color: red">Sess&atilde;o encerrada!</h1>';
    exit ();
}

// Fechando a conexão com o banco de dados do admin
@mysqli_close ( 
                $conAdm );

// Iniciando a configuração do usuário na sessão.
session_name ( 
            'premium' );
session_start ();

$sqlCli = 'SELECT
              c.CLI_nome,
              c.CLI_id,
              c.CLI_email,
              c.CLI_senha,
              c.CLI_ativo,
              c.CLI_id_plataforma,
              p.PLAN_id_plano,
              cfg.CONF_moeda
            FROM
              cliente c
            INNER JOIN plano p
            ON
              p.PLAN_id_cli = c.CLI_id
            INNER JOIN config cfg
            ON
              cfg.CONF_id_cli = c.CLI_id
            WHERE
              c.CLI_id      = ' . $idClient . '
            AND c.CLI_ativo = 1
            LIMIT 1';

$result = mysqli_query ( 
                        $conCad,
                        $sqlCli );

if (mysqli_num_rows ( 
                    $result ) > 0) {
    
    $row = mysqli_fetch_array ( 
                                $result );
    
    $_SESSION['nome'] = $row['CLI_nome'];
    $_SESSION['email'] = $row['CLI_email'];
    $_SESSION['senha'] = $row['CLI_senha'];
    $_SESSION['id'] = $row['CLI_id'];
    $_SESSION['idPlan'] = $row['PLAN_id_plano'];
    $_SESSION['idPlataforma'] = $row['CLI_id_plataforma'];
    $_SESSION["currency"] = $row['CONF_moeda'];
    
    $insert = 'INSERT INTO login (LOG_id_cli, tx_adm_token) VALUES (\'' . $idClient . '\', \'' . $token . '\')';
    mysqli_query($conCad, $insert);
    
    
} else {
    echo '<h1 style="color: red">N&atilde;o encontrado cliente ativo com id: ', $idClient, '</h1>';
    exit ();
}

header ( 'location: ../dashboard/' );
?>
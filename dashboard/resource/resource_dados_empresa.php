<?php
	// conexão com banco de dados
	require_once('../../bd/conexao_bd_cadastro.php');

	// sessão
	$idCLI = mysqli_real_escape_string($conCad,$_POST['id']);
	$operacao = mysqli_real_escape_string($conCad,$_POST['op']); // 1 consulta e 2 salva

	function getData($conCad,$idCLI)
	{
		// ENDEREÇO DO CLIENTE
		$query1 = "SELECT CAD_inscricao_estadual, CAD_email_sec, CAD_cnpj, CAD_telefone, CAD_rua, CAD_numero, CAD_bairro, CAD_tel_sec , CAD_cidade, CAD_estado, CAD_complemento, CAD_CEP FROM cadastro WHERE CAD_id_cli = '$idCLI'";
		$result1 = mysqli_query($conCad, $query1) or print(mysqli_error($conCad));
		$array1 = mysqli_fetch_array($result1);

		$telefone = $array1['CAD_telefone'];
		$inscricaoEstadual = $array1['CAD_inscricao_estadual'];
		$cnpj = $array1['CAD_cnpj'];

		$rua = $array1['CAD_rua'];
		$numero = $array1['CAD_numero'];
		$bairro = $array1['CAD_bairro'];
		$cidade = $array1['CAD_cidade'];
		$estado = $array1['CAD_estado'];
		$complemento = $array1['CAD_complemento'];
		$CEP = $array1['CAD_CEP'];
		$telefoneFinanceiro = $array1['CAD_tel_sec'];
		$emailFinanceiro = $array1['CAD_email_sec'];

		// DADOS DA EMPRESA
		$query2 = "SELECT CLI_nome, CLI_email, CLI_site FROM cliente WHERE CLI_id = '$idCLI'";
		$result2 = mysqli_query($conCad, $query2) or print(mysqli_error($conCad));
		$array2 = mysqli_fetch_array($result2);

		$razaoSocial = $array2['CLI_nome'];
		$email = $array2['CLI_email'];

		$data = array(
			'rua' => $rua,
			'numero' => $numero,
			'bairro' => $bairro,
			'cidade' => $cidade,
			'estado' => $estado,
			'complemento' => $complemento,
			'CEP' => $CEP,

			'telefoneFinanceiro' => $telefoneFinanceiro,
			'razaoSocial' => $razaoSocial,
			'email' => $email,
			'emailFinanceiro' => $emailFinanceiro,
			'telefoneAdministrativo' => $telefone,
			'inscricaoEstadual' => $inscricaoEstadual,
			'cnpj' => $cnpj,
		);
		echo json_encode($data);
	}

	function saveData($conCad,$idCLI,$data)	
	{
		$rua = mysqli_real_escape_string($conCad,$data['rua']);
		$numero = mysqli_real_escape_string($conCad,$data['numero']);
		$bairro = mysqli_real_escape_string($conCad,$data['bairro']);
		$cidade = mysqli_real_escape_string($conCad,$data['cidade']);
		$estado = mysqli_real_escape_string($conCad,$data['estado']);
		$complemento = mysqli_real_escape_string($conCad,$data['complemento']);
		$CEP = mysqli_real_escape_string($conCad,$data['CEP']);
		$razaoSocial = mysqli_real_escape_string($conCad,$data['razaoSocial']);
		$email = mysqli_real_escape_string($conCad,$data['email']);
		$telefone = mysqli_real_escape_string($conCad,$data['telefoneAdministrativo']);
		$telefoneFinanceiro = mysqli_real_escape_string($conCad,$data['telefoneFinanceiro']);
		$inscricaoEstadual = mysqli_real_escape_string($conCad,$data['inscricaoEstadual']);
		$cnpj = mysqli_real_escape_string($conCad,$data['cnpj']);
		$emailFinanceiro = mysqli_real_escape_string($conCad,$data['emailFinanceiro']);

		// ENDEREÇO DO CLIENTE
		$query1 = "UPDATE cadastro SET CAD_telefone = '$telefone', CAD_tel_sec = '$telefoneFinanceiro', CAD_email_sec = '$emailFinanceiro', CAD_inscricao_estadual = '$inscricaoEstadual', 
		CAD_cnpj = '$cnpj', CAD_rua = '$rua', CAD_numero = '$numero', CAD_bairro = '$bairro', 
		CAD_cidade = '$cidade', CAD_estado = '$estado', CAD_complemento = '$complemento', CAD_CEP = '$CEP' WHERE CAD_id_cli = '$idCLI'";
		$result1 = mysqli_query($conCad, $query1) or print(mysqli_error($conCad));

		// DADOS DA EMPRESA
		$query2 = "UPDATE cliente SET CLI_nome = '$razaoSocial', CLI_email = '$email' WHERE CLI_id = '$idCLI'";
		$result2 = mysqli_query($conCad, $query2) or print(mysqli_error($conCad));

		if ($result1 && $result2){
			echo "1";
		} else {
			echo "0";
		}
	}

	switch ($operacao) {
		case '1':
			getData($conCad,$idCLI);
			break;
		case '2':
			$data = $_POST;
			saveData($conCad,$idCLI,$data);
			break;
	}
?>
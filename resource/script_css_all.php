<?php
	// conexão com banco de dados
	require_once('../bd/conexao_bd_cadastro.php');

	$selectCli = "SELECT CLI_id FROM cliente WHERE CLI_ativo = 1";
	$resultCli = mysqli_query($conCad, $selectCli);

	while ($arrayCli = mysqli_fetch_array($resultCli)) 
	{
		$idCLI = $arrayCli['CLI_id'];

		// altera CSS cliente
		// pega id template no banco
		$selectConfig = "SELECT CONF_template, CONF_cor_prim, CONF_cor_sec FROM config WHERE CONF_id_cli = ".$idCLI;
		$resultConfig = mysqli_query($conCad, $selectConfig);

		$arrayTemplate = mysqli_fetch_array($resultConfig);

		$idTemplate = $arrayTemplate['CONF_template'];
		$corPrimaria = $arrayTemplate['CONF_cor_prim'];
		$corSecundaria = $arrayTemplate['CONF_cor_sec'];

		if($idTemplate != sha1($idCLI))
		{
			
			if(!empty($corPrimaria) && !empty($corSecundaria))
			{
				$corPrimaria = '#'.$corPrimaria;
				$corSecundaria = '#'.$corSecundaria;
			}
			else
			{
				$corPrimaria = '#000';
				$corSecundaria = '#888888';
			}
			

			$idSHA1 = sha1($idCLI);

			$css = file_get_contents('../widget/templates/kit_'.$idTemplate.'/css/dynamic-style.css');
			$css = str_replace('{PRIMARY_COLOR}', $corPrimaria, $css);
			$css = str_replace('{SECONDARY_COLOR}', $corSecundaria, $css);
			
			// escrevendo as configuracoes no css
			file_put_contents('../widget/css/rh_'.$idSHA1.'.css', $css);

			echo "CSS alterado, id cliente: ".$idCLI."<br />";
		}
	}

?>
<?php
	
	// Retorno em JSON
	header('Content-Type: application/json');

	// Conexão com banco de dados
	require_once('../../bd/conexao_bd_cadastro.php');
	require_once('../../bd/conexao_bd_dashboard.php');

	// Resposta da API
	$apiResponse = array(
		'status' 	=> false,
		'code' 		=> 0,
		'message' 	=> '',
		'response' 	=> array()
	);


	// Sessão
	$id = isset($_POST['id']) ? $_POST['id'] : (isset($_GET['id']) ? $_GET['id'] : null);
	$op = isset($_POST['op']) ? $_POST['op'] : (isset($_GET['op']) ? $_GET['op'] : null);

	function getKitList () {

		global  $apiResponse;
		global  $conCad;
		global  $id;

		$folder	= '../../widget/preview/';

		$list 	= array(
			'default' 	=> array(),
			'client' 	=> array()
		);

		if ($_SERVER['REQUEST_METHOD'] === 'GET') {
	
			if (is_dir($folder)) {
				
				foreach (scandir($folder) as $key => $value) {
					
					if ($value !== '.' && $value !== '..'  && is_dir($folder . $value)) {

						if (is_file($folder . $value . '/index.html') && is_file($folder . $value . '/img/thumb.jpg')) {

							array_push($list['default'], explode('_', $value)[1]);
						}						
					}
				}

				$selectConf = 'SELECT CONF_template from config WHERE CONF_id_cli = ' . $id;
						
				$queryConf = mysqli_query($conCad, $selectConf) or mysqli_error($conCad);

				if ($queryConf) {

					$kit = mysqli_fetch_array($queryConf, MYSQLI_NUM);

					array_push($list['client'], $kit[0]);
				}

				$apiResponse = array(
					'status' 	=> true,
					'code' 		=> 200,
					'message' 	=> 'Lista de kits carregada com sucesso.',
					'response' 	=> $list
				);

			} else {

				$apiResponse = array(
					'status' 	=> false,
					'code' 		=> 01,
					'message' 	=> 'Pasta de previews não localizada.',
					'response' 	=> array()
				);
			}
		
		} else {

			http_response_code(405);

			$apiResponse = array(
				'status' 	=> false,
				'code' 		=> 405,
				'message' 	=> 'Método não aceito neste tipo de request.',
				'response' 	=> array()
			);
		}
	};

	function saveTemplate () {

		global  $apiResponse;
		global  $conCad;
		global  $id;

		$path 			= '../../widget/preview/';
		$prefix 		= 'kit_';

		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	
			if (isset($_POST['kit']) && !empty($_POST['kit'])) {
				
				$kit 			= mysqli_real_escape_string($conCad, $_POST['kit']);
				$defaultFolder 	= $path . $prefix . $kit;

				if (is_dir($defaultFolder)) {

					$updateConf = 'UPDATE config SET CONF_template = "' . $kit . '" WHERE CONF_id_cli = ' . $id;
					
					$queryConf = mysqli_query($conCad, $updateConf) or mysqli_error($conCad);

					if ($queryConf) {
						
						$apiResponse = array(
							'status' 	=> true,
							'code' 		=> 200,
							'message' 	=> 'Kit atualizado com sucesso.',
							'response' 	=> array()
						);

					} else {
						
						$apiResponse = array(
							'status' 	=> false,
							'code' 		=> 02,
							'message' 	=> 'Erro ao salvar informação na base de dados.',
							'response' 	=> array(
								$queryConf
							)
						);	
					}

				} else {
				
					$apiResponse = array(
						'status' 	=> false,
						'code' 		=> 01,
						'message' 	=> 'Pasta de Kits padrão não localizada. Id do kit inválido.',
						'response' 	=> array()
					);	
				}

			} else {
				
				$apiResponse = array(
					'status' 	=> false,
					'code' 		=> 204,
					'message' 	=> 'O id do kit não foi informado',
					'response' 	=> array()
				);
			}
		
		} else {

			http_response_code(405);

			$apiResponse = array(
				'status' 	=> false,
				'code' 		=> 405,
				'message' 	=> 'Método não aceito neste tipo de request.',
				'response' 	=> array()
			);
		}
	};

	if ($id && $op) {
		
		$id = mysqli_real_escape_string($conCad, $id);
		$op = mysqli_real_escape_string($conCad, $op);

		switch ($op) {
			
			case 'list':
				
				getKitList();
				break;
			
			case 'save':

				saveTemplate();
				break;
		}

	} else {

		http_response_code(403);

		$apiResponse = array(
			'status' 	=> false,
			'code' 		=> 403,
			'message' 	=> 'Permissão negada. Nenhuma sessão iniciada.',
			'response' 	=> array()
		);
	}
	
	echo json_encode($apiResponse);
?>
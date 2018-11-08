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

	function getTemplateList () {

		global  $apiResponse;
		global  $id;

		$path 	= '../../widget/templates/';
		$prefix = 'kit_';
		$hash 	= $id ? sha1($id) : 1;
		$folder = (is_dir($path . $prefix . $hash) ? ($path . $prefix . $hash) : ($path . $prefix . '1')) . '/';
		$list 	= array(
			'default' 	=> array(),
			'client' 	=> array()
		);

		if ($_SERVER['REQUEST_METHOD'] === 'GET') {
	
			if (is_dir($folder)) {
				
				foreach (scandir($folder) as $key => $value) {
					
					if (is_file($folder . $value)) {

						$name 		= ucwords(str_replace('_', ' ', explode('.', $value)[0]));
						$key 		= str_replace(' ', '_', strtolower($name));
						$file 		= $value;
						$content 	= file_get_contents($folder . $value);

						$list['client'][$key] = array(
							'name' 		=> $name,
							'file' 		=> $file,
							'content' 	=> $content
						);
					}
				}

				$folder = $path . $prefix . '1/';

				if (is_dir($folder)) {
				
					foreach (scandir($folder) as $key => $value) {
						
						if (is_file($folder . $value)) {

							$name 		= ucwords(str_replace('_', ' ', explode('.', $value)[0]));
							$key 		= str_replace(' ', '_', strtolower($name));
							$file 		= $value;
							$content 	= file_get_contents($folder . $value);

							$list['default'][$key] = array(
								'name' 		=> $name,
								'file' 		=> $file,
								'content' 	=> $content
							);
						}
					}
				}

				if (!empty($list['client']) && !empty($list['default'])) {

					$apiResponse = array(
						'status' 	=> true,
						'code' 		=> 200,
						'message' 	=> 'Lista de templates carregada com sucesso.',
						'response' 	=> $list
					);				

				} else {

					$apiResponse = array(
						'status' 	=> false,
						'code' 		=> 01,
						'message' 	=> 'Pasta de Kits não localizada.',
						'response' 	=> array()
					);
				}
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

		$path 			= '../../widget/templates/';
		$prefix 		= 'kit_';
		$hash 			= sha1($id);		
		$clientFolder 	= $path . $prefix . $hash . '/';
		$defaultFolder 	= is_dir($path . $prefix . '1/') ? ($path . $prefix . '1/') : false;

		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	
			if (isset($_POST['content']) && isset($_POST['file'])) {

				if(!empty($_POST['content']) && !empty($_POST['file'])) {

					if (is_dir($defaultFolder)) {

						if (!is_dir($clientFolder)) {

							mkdir($clientFolder, 0777);
							
							foreach (scandir($defaultFolder) as $key => $value) {
								
								if (is_file($defaultFolder . $value)) {

									copy($defaultFolder . $value, $clientFolder . $value);
								}
							}
						}

						file_put_contents($clientFolder . $_POST['file'], $_POST['content']);

						$updateConf = 'UPDATE config SET CONF_template = "' . $hash . '" WHERE CONF_id_cli = ' . $id;
						
						$queryConf = mysqli_query($conCad, $updateConf) or mysqli_error($conCad);

						if ($queryConf) {
							
							$apiResponse = array(
								'status' 	=> true,
								'code' 		=> 200,
								'message' 	=> 'Template atualizado com sucesso.',
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
							'message' 	=> 'Pasta de Kits padrão não localizada.',
							'response' 	=> array()
						);	
					}						

				} else {

					$apiResponse = array(
						'status' 	=> false,
						'code' 		=> 204,
						'message' 	=> 'O conteúdo e/ou o nome do Template não pode estar vazio.',
						'response' 	=> array()
					);
				}

			} else {
				
				$apiResponse = array(
					'status' 	=> false,
					'code' 		=> 204,
					'message' 	=> 'O conteúdo e/ou o nome do Template não foi informado.',
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
				
				getTemplateList();
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
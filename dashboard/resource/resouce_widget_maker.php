<?php 
	/*
		------INTELIGÊNCIAS:
		1- Top Trends / Mais Clicados: retorna um json de 24 produtos mais clicados nos últimos 7, 5 ou 3 dias.
		2- Mais Vendidos: retorna um json de 24 produtos mais vendidos na loja.
		3- Mais Vendidos da Categoria: INTELIGÊNCIA ÚNICA DA PÁGINA DE CATEGORIA.
		4- Remarketing On-site: recomenda produtos baseado na navegação do cliente.
		5- Similares por Produto: recomenda produtos similares ao produto. Exclusivo para pag de produtos.
		6- Similares por Categoria: recomenda produtos da categoria do produto. Exclusívo para pag de produtos.
		7- Liquidação: produtos com maior desconto.
		8- Palavra Chave / Collection: recomenda produtos que possuem no nome uma palavra chave cadastrada.
		9- Compre Junto / Com SKU:
		10- Manual / Boss Choice: recomenda os produtos manualmente cadastrados.
		11- Carrinho Manual: O cliente escolhe várias palavras chaves como no compre junto dai quando o produto que ele quer está no carrinho tipo quando tênis nike estiver no carrinho mostrar meias nike como recomendação.
		12- Top Carrinho: bloco específico para página de carrinho, mostra itens complementares ao que o cliente adicionou no carrinho. Pode montar até 4 blocos dinamicamente.
		13- Produtos Complementares: recomenda produtos com % de possibilidade de compra de acordo com o produto na página.
		14- Navegação Complementar / Vitrine Nativa / Individual Palavra Chave: recomenda um bloco com produtos similares aos que ele navegou por último.
		15- Baixou de Preço: recomenda os produtos que baixaram de preço na loja nos últimos dias.
		16- Novidades na Loja: recomenda os últimos produtos adicionados na loja.
		17- Vitrine Geolocalização: recomenda produtos baseados na localização do IP.
		18- Por Atributo / Single Rec: Recomenda produtos baseados em atributos cadastrados e/ou navegados.
		19- Barra de Busca: é a barra de busca inteligente da Roi Hero que funciona recomendando produtos e pesquisas. Junto com um overlay.
		20- Vitrine de Busca: bloco que recomenda produtos similares e/ou produtos que ele buscou na barra de busca.
		21- Top Trends Google: Os produtos mais vendidos para pessoas que vem do google.
		22- Top Trends Facebook: Os produtos mais vendidos para pessoas que vem do facebook.
		23- Top Trends Gênero: Os produtos mais vendidos para homens ou mulheres, o cliente escolhe o gênero.
		24- Top Trends Faixa Etária: recomenda produtos mais vendidos pra alguma determinada faixa etária.
		25- Oferta Limitada Dinâmico:
	    26- Oferta Limitada Manual:

	*/
		
	// sessão
	session_name('premium');
	session_start();
	$idCLI = $_SESSION['id'];
	include '../../bd/conexao_bd_cadastro.php';


	$qSelectOwa = "SELECT CLI_id_owa FROM cliente WHERE CLI_id = $idCLI";
	$resultOwa = mysqli_query($conCad, $qSelectOwa);
	$idCliOwa = mysqli_fetch_array($resultOwa)['CLI_id_owa'];


	$inteligencias = array(
		'topTrends' => 1, 
		'maisVendidos' => 2, 
		'maisVendidosMesmaCategoria' => 3, 
		'remarketing' => 4, 
		'similares' => 5,
		'liquidacao' => 6, 
		'collection' => 7, 
		'compreJunto' => 8, 
		'bossChoice' => 9, 
		'ofertaLimitada' => 10, 
		'topCarrinho' => 11,
		'itensComplementares' => 12, 
		'overlayDeSaida' => 13,
		'baixouDePreco' => 14,
		'novidades' => 15,
		'geolocalizacao' => 16,
		'melhoresAvaliados' => 20,
		'recemAvaliados' => 21,
		'barraDeBusca' => 22,
		'navegacaoComplementar' => 23,
		'maisVendidosDaCategoriaManual' => 24,
		'palavraChave' => 25,
		'porAtributo' => 26,
		'vitrineBusca' => 27,
		'manualCarrinho' => 28,
		'inteligenciasMultiplas' => 29,
		'lojaLateral' => 30,
		'topTrendsGenero' => 31,
		'topTrendsFaixaEtaria' => 32,
		'landingPage' => 33,
		'produtosRelacionados' => 34,
		'remarketingComplementar' => 23, // msm q navegacao complementar
		'smartHome' => 36
	);
	//$placements = array('topo'=>1,'meio'=>2,'final'=>3);
	$paginas = array('todas'=>0,'home'=>1,'produto'=>2,'buscaVazia'=>7,'categoria'=>4,'carrinho'=>5);


	// inteligencia do widget
	$inteligencia = mysqli_real_escape_string($conCad, $_POST['inteligencia']); 
	$inteligencia = $inteligencias[$inteligencia];
	// localizacao do widget na pagina: topo/meio/fim
	//$placement = mysqli_real_escape_string($conCad,$_POST['placement']); 
	//$placement = $placements[$placement]; 
	// pagina do widget
	$pagina = mysqli_real_escape_string($conCad,$_POST['pagina']); 
	$pagina = $paginas[$pagina]; 


	// CONFIGURAÇÕES PADRÃO
	//$id = 2123;
	$nomeWidget = mysqli_real_escape_string($conCad, $_POST['nome']);
	$tituloWidget = mysqli_real_escape_string($conCad, $_POST['titulo']);
	$utmWidget = mysqli_real_escape_string($conCad, $_POST['utm']);
	$formato = mysqli_real_escape_string($conCad, $_POST['formato']); 
	$container = mysqli_real_escape_string($conCad, $_POST['container']); 
	$tipoContainer = mysqli_real_escape_string($conCad, $_POST['tipoContainer']); 
	$updown = mysqli_real_escape_string($conCad, $_POST['UpDown']); 

	/*
	// formato: 
		1 - Prateleira ;    
		2 - Dupla   ; 
		3 - Slider;     
		4 - Compre Junto 2;     
		5 - Overlay de Saída;     
		6 - Oferta Limitada;     
		7 - Barra de Busca;     
		8 - Vitrine;     
		10 - Rodapé;      
		11 - Totem;     
		12 - Compre Junto 3
	*/
	$formatos = array(
		'prateleira' => 1,
		'prateleiraDupla' => 2,
		'carrossel' => 3,
		'compre_junto_2' => 4,
		'overlay_saida' => 5,
		'oferta_limitada' => 6,
		'barra_de_busca' => 7,
		'vitrine' => 8,
		'rodape' => 10,
		'totem' => 11,
		'compre_junto_3' => 12,
		'slider_complementar' => 13
	);
	$formato = $formatos[$formato];

	$campos = 'WID_id_cli, WID_div_type, WID_div, WID_nome, WID_texto, WID_utm, WID_pagina, WID_inteligencia, WID_formato, 	WID_owa_token, WID_updown';  // campos do BD a serem preenchidos 
	$valores = $idCLI.",'".$tipoContainer."','".$container."','".$nomeWidget."','".$tituloWidget."','".$utmWidget."','".$pagina."','".$inteligencia."','".$formato."','".$idCliOwa."','".$updown."'"; // valores dos campos 

	$mostraOuEsconde = $_POST['url'];
	$listaUrl = mysqli_real_escape_string($conCad, $_POST['listaUrl']);

	if($mostraOuEsconde == "mostrar"){
		$campos .= ', WID_show';
		$valores .= ",'".$listaUrl."'";
	} elseif($mostraOuEsconde == "esconder"){
		$campos .= ', WID_hide';
		$valores .= ",'".$listaUrl."'";
	}
	// CONFIGURAÇÕES QUE TEM EM TODOS OS TIPOS DE CONTA
	$somenteEsgotados = (isset($_POST['somenteEsgotados'])) ? mysqli_real_escape_string($conCad, $_POST['somenteEsgotados']) : ''; // habilitar somente na página de produtos esgotados

	if(!($somenteEsgotados == '' or $somenteEsgotados == null)){
		$campos .= ', WID_prod_esg';
		$valores .= ",'1'";
	}
	



	// CONFIGURACOES QUE EXISTEM APENAS PARA CLIENTES MEDIOS E VIPS
	if (isset($_POST['botaoProCarrinho'])){
		$botaoProCarrinho = mysqli_real_escape_string($conCad, $_POST['botaoProCarrinho']); // botao comprar redireciona pro carrinho 1-sim/0-nao
		$campos .= ', WID_direto_cart';
		$valores .= ",'".$botaoProCarrinho."'";
	}

	$maisClicados = (isset($_POST['maisClicados'])) ? mysqli_real_escape_string($conCad, $_POST['maisClicados']) : "7"; // mais clicados nos últimos 7,3 ou 1 dia

	$maisVendidos = (isset($_POST['maisVendidos'])) ? mysqli_real_escape_string($conCad, $_POST['maisVendidos']) : "7"; // mais vendidos nos últimos 7,3 ou 1 dia

	$maisVendidosCategoria = (isset($_POST['maisVendidosCategoria'])) ? mysqli_real_escape_string($conCad, $_POST['maisVendidosCategoria']) : "7"; // mais vendidos da categoria nos últimos 7,3 ou 1 dia		

	
	switch ($inteligencia) {
		case '1': // mais clicados
			$campos .= ', WID_dias';
			$valores .= ",'".$maisClicados."'";
			break;
		case '2': // mais vendidos
			$campos .= ', WID_dias';
			$valores .= ",'".$maisVendidos."'";
			break;
		case '3': // mais vendidos da categoria
			$campos .= ', WID_dias';
			$valores .= ",'".$maisVendidosCategoria."'";
			break;
		default:
			$campos .= ', WID_dias';
			$valores .= ",'7'";
			break;
	}

	// QUERY WIDGET
	$query = 'INSERT INTO widget ('.$campos.', WID_status) VALUES ('.$valores.', 1)';
	$insertWID = mysqli_query($conCad, $query) OR print(mysqli_error($conCad));  // INSERÇÃO DOS DADOS NO BANCO DE DADOS
	if(!$insertWID)
		exit('0');


	// CONFIGURACOES DOS WIDGETS NO BANCO
	// PARA INTELIGENCIA ESPECIFICAS
	$campos = '';
	$valores = '';
	// CONFIGURACOES INTELIGENCIAS ESPECIFICAS
	$inteligenciasAvancadas = ['7', '8', '9', '10', '17', '22', '24', '25', '26', '28', '29', '34']; // array com ID's das inteligencias que possuem configuração
	if (in_array($inteligencia, $inteligenciasAvancadas)) { 
		$palavraChave = (isset($_POST['palavraChave'])) ? mysqli_real_escape_string($conCad, $_POST['palavraChave']) : ""; // palavra chave da inteligência -> vitrine nativa com palavra chave // WC_id_prod
		
		$produtos = (isset($_POST['produtos'])) ? mysqli_real_escape_string($conCad, $_POST['produtos']) : ""; // produtos cadastrados manualmente // WC_id_prod
		$carrinhoManual = (isset($_POST['carrinhoManualWidget'])) ? mysqli_real_escape_string($conCad, $_POST['carrinhoManualWidget']) : "";
		

		$maisVendidosDaCategoriaManual = (isset($_POST['maisVendidosDaCategoriaManual'])) ? mysqli_real_escape_string($conCad, $_POST['maisVendidosDaCategoriaManual']) : ""; // categoria cadastrados manualmente // WC_id_prod

		if (isset($_POST['produtoCarrinho'])){			
			$produtoCarrinho = mysqli_real_escape_string($conCad, $_POST['produtoCarrinho']); // produto no carrinho // WC_cj_p	
		}
		if (isset($_POST['estado'])) {
			$estado = mysqli_real_escape_string($conCad, $_POST['estado']); // estado - para inteligencia de geolocalização // WC_estado
		}		
		if (isset($_POST['produtoComplementar'])) {
			$carrinhoComplementar = mysqli_real_escape_string($conCad, $_POST['produtoComplementar']); // produto complementar // WC_cj_f			
		}
		if (isset($_POST['palavrasPaiFilho'])) {
			$palavrasPaiFilho = mysqli_real_escape_string($conCad, $_POST['palavrasPaiFilho']); // produto complementar // WC_cj_f			
		}
		if (isset($_POST['produtosCollection'])) {
			$produtosCollection = mysqli_real_escape_string($conCad, $_POST['produtosCollection']); // produto complementar // WC_cj_f			
		} 


		// CAPTURA ID PRA LINKAR NAS CONFIGURACOES - AI MDS, LUCAS. MELHORA ISSO AQUI
		if ($insertWID){		
			$query = 'SELECT * FROM widget ORDER BY WID_id DESC LIMIT 1';
			$getLastId = mysqli_query($conCad, $query) OR print(mysqli_error($conCad));
			$novoId = mysqli_fetch_array($getLastId)['WID_id'];
		}

		switch ($inteligencia) {
			case '7': // se collection
				$valores .= ", '".$produtosCollection."'";
				$campos .= ", WC_collection";
				break;

			case '8': //compre junto
				$compreJunto = array(
					'WC_cj_p' => $_POST['p_chave_pai'],
					'WC_cj_f' => $_POST['p_chave_filho'],

					'tx_tipo_pai' => $_POST['tp_chave_pai'],
					'tx_tipo_filho' => $_POST['tp_chave_filho'],

					'tx_param_pai' => $_POST['parametro_pai'],
					'tx_param_filho' => $_POST['parametro_filho'],

					'tx_tipo_param_pai' => $_POST['tp_parametro_pai'],
					'tx_tipo_param_filho' => $_POST['tp_parametro_filho'],

					'tx_negativa_pai' => $_POST['negativa_pai'],
					'tx_negativa_filho' => $_POST['negativa_filho']
				);

				foreach($compreJunto as $campo => $valor){
					$valor = implode(",", $valor);
					$valor = strtoupper($valor);
					$valores .= ", '".$valor."'";
					$campos .= ", ".$campo;
				}

				break;
			
			case '9': //boss choice
				/*$produtos = explode(",", $produtos);
				foreach($produtos as $produto){
					$selectProd = //SELECT PROD_ID FROM PRODUTO WHERE
					foreach($produto as $palavra)
					 $selectProd .= // PROD_NOME LIKE "$palavra" and
					$selectProd .= // id cli = $idCli limit 1
					//depois pega esse id e adiciona na lista
				} */
				$valores .= ($produtos == "") ? "" : ", '".$produtos."'";
				$campos .= ($produtos == "") ? "" : ", WC_id_produto";
				break;

			case '24': //mais vendidos da categoria manual
				$valores .= ($maisVendidosDaCategoriaManual == "") ? "" : ", '".$maisVendidosDaCategoriaManual."'";
				$campos .= ($maisVendidosDaCategoriaManual == "") ? "" : ", WC_categoria";
				break;

			
			case '25': //palavra chave
				$valores .= ($palavraChave == "") ? "" : ", '".$palavraChave."'";
				$campos .= ($palavraChave == "") ? "" : ", WC_collection";
				break;

			case '28': //carrinho manual
				if($carrinhoManual != ""){
					$carrinhoManual = str_replace(" ", "", $carrinhoManual);
					$partes = explode(",", $carrinhoManual);
					
					$filhos = [];
					$pais = [];
					foreach($partes as $k => $parte){
						$pai_filho = explode("***", $parte);
						
						$filhos[] = $pai_filho[1];
						$pais[] = $pai_filho[0];
					}
					
					$pais = implode(",", $pais);
					$filhos = implode(",", $filhos);


					$valores .= ", '".$pais."', '".$filhos."'";
					$campos .= ", WC_cj_p, WC_cj_f";
				}
				break;

			case '34': // produtos relacionados
				if($palavrasPaiFilho != ""){
					//$palavrasPaiFilho = str_replace(" ", "", $palavrasPaiFilho);
					$partes = explode(",", $palavrasPaiFilho);
					
					$filhos = [];
					$pais = [];
					foreach($partes as $k => $parte){
						$pai_filho = explode("->", $parte);
						
						$filhos[] = $pai_filho[1];
						$pais[] = $pai_filho[0];
					}
					
					$pais = implode(",", $pais);
					$filhos = implode(",", $filhos);


					$valores .= ", '".$pais."', '".$filhos."'";
					$campos .= ", WC_cj_p, WC_cj_f";
				}
				break;

			

			
			default:
				break;


				/*
				wc_cj_f = compre junto filho
				negativa = MAIUSCULO
				TIPO PAI E FILHO - 0 TITULO - 1 CATEGORIA
				PARAMETRO - MAISÚCSULO
				TIPO  PARAMETRO

				*/
		}

		


		// QUERY WIDGET CONFIG
		$query = 'INSERT INTO widget_config ( WC_id_wid '.$campos.') VALUES ("'.$novoId . '" '.$valores.')';
		$insertWID = mysqli_query($conCad, $query) OR print(mysqli_error($conCad));  // INSERÇÃO DOS DADOS NO BANCO DE DADOS

		if(!$insertWID)
			exit('0');
	}

	echo '1';
	//header('Location: ../minhas_recomendacoes');
?>
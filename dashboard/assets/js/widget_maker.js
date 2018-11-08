//============== Auto Complete
var bossChoiceProdId = 0,
bossChoiceProdTitulo = "";
//==============

ready(function(){

	//============== Auto Complete
	var autoCompleteDivStyle = document.createElement('style')
	autoCompleteDivStyle.innerHTML = ".list-group-hover .list-group-item:hover {background-color: #f5f5ff;} .autocomplete-overlay {box-shadow: rgba(0, 0, 0, 0.3) 2px 2px 3px 0.5px; display: none;max-height:210px;overflow:auto;position:absolute; width: 95%; z-index:100; padding: 0px;}";
	document.head.appendChild(autoCompleteDivStyle);

	$('#inputProdutos').tagsinput({
		itemValue: 'value',
		itemText: 'text',
	});

	
	$('#produto').keyup(function(){
		var query = $(this).val();
		if(query.length > 2){
			$.ajax({
				url:"autocomplete.php",
				method:"POST",
				data:{query:query, formato: 0}, //formato aqui é gambiarra pra reaproveitar código
				success:function(data){
					$('#listaProdutosAutocomplete').fadeIn();
					$('#listaProdutosAutocomplete').html(data);
					
				}
			})
		} else {
			$('#listaProdutosAutocomplete').fadeOut();
			$('#listaProdutosAutocomplete').html("");
		}
	});
	//==============

	$('#FORMWIDGET').append('<input id="___" type="hidden" value="'+$('#infsess').attr('data-cli')+'">');   
	var passo1 = $('#tabPage'), passo2, passo2, passo3, passo4; // passos
// PASSO 1 --------------//
	// WIDGET NA HOME	
	$('#makerHome').on('click',function() {
		passo1.hide();
		passo2 = $('#tabInteligenciasHome');
		passo2.show();
		document.getElementsByClassName('btn-maker')[0].classList.remove('active');
		document.getElementsByClassName('btn-maker')[1].classList.add('active');
		$('#pageWidget').val('home');
		$('#crumbPagina').html('home');
		$('.btnVoltaPasso').show();
	});


	// WIDGET PAGINA DE PRODUTO
	$('#btnMakerProduto').on('click',function() {
		passo1.hide();
		passo2 = $('#tabInteligenciasProduto');
		passo2.show();
		document.getElementsByClassName('btn-maker')[0].classList.remove('active');
		document.getElementsByClassName('btn-maker')[1].classList.add('active');
		$('#pageWidget').val('produto');
		$('#crumbPagina').html('produto');
		$('.btnVoltaPasso').show();
	});

	// WIDGET PAGINA DE CARRINHO
	$('#makerCarrinho').on('click',function() {
		passo1.hide();
		passo2 = $('#tabInteligenciasCarrinho');
		passo2.show();
		document.getElementsByClassName('btn-maker')[0].classList.remove('active');
		document.getElementsByClassName('btn-maker')[1].classList.add('active');
		$('#pageWidget').val('carrinho');
		$('#crumbPagina').html('carrinho');
		$('.btnVoltaPasso').show();
	});

	// WIDGET PAGINA DE CATEGORIA
	$('#makerCategoria').on('click',function() {
		passo1.hide();
		passo2 = $('#tabInteligenciasCategoria');
		passo2.show();
		document.getElementsByClassName('btn-maker')[0].classList.remove('active');
		document.getElementsByClassName('btn-maker')[1].classList.add('active');
		$('#pageWidget').val('categoria');
		$('#crumbPagina').html('categoria');
		$('.btnVoltaPasso').show();
	});
	
	// WIDGET PAGINA DE CATEGORIA
	$('#makerBuscaVazia').on('click',function() {
		passo1.hide();
		passo2 = $('#tabInteligenciasBuscaVazia');
		passo2.show();
		document.getElementsByClassName('btn-maker')[0].classList.remove('active');
		document.getElementsByClassName('btn-maker')[1].classList.add('active');
		$('#pageWidget').val('buscaVazia');
		$('#crumbPagina').html('busca vazia');
		$('.btnVoltaPasso').show();
	});
//-----------------------//

// PASSO 2 PAGINA DE PRODUTO-------------//
	$('.btnSelecionaInteligenciaProdutos').on('click', function(){
		/*switch(this.value){
			case 'compreJunto':
				$('#inteligenciaWidget').val('compreJunto');
				$('#formatoWidget').val('compre_junto_2');
				passo2.hide()
				passo4 = $('#tabConfiguracoes');
				passo4.show();
				document.getElementsByClassName('btn-maker')[1].classList.remove('active');
				document.getElementsByClassName('btn-maker')[3].classList.add('active');
				$('#crumbInteligencia').html('Compre Junto');				
				$('#crumbFormato').html('Compre Junto');
				break;
			case 'produtosRelacionados':
				$('#inteligenciaWidget').val(this.value);
				document.getElementsByClassName('btn-maker')[1].classList.remove('active');
				document.getElementsByClassName('btn-maker')[2].classList.add('active');
				$('#crumbInteligencia').html("this.value");
				passo2.hide();
				passo3 = $('#tabFormatos');
				passo3.show();
				break;
		} */
		if(this.value === 'compreJunto'){			
			$('#inteligenciaWidget').val('compreJunto');
			$('#formatoWidget').val('compre_junto_2');
			passo2.hide()
			//pula passo 3
			passo4 = $('#tabConfiguracoes');
			$('#cadastrarCompreJunto').show();
			passo4.show();
			document.getElementsByClassName('btn-maker')[1].classList.remove('active');
			document.getElementsByClassName('btn-maker')[3].classList.add('active');
			$('#crumbInteligencia').html('Compre Junto');				
			$('#crumbFormato').html('Compre Junto');
		} else if (this.value === 'remarketingNavegacao') {
			$('#inteligenciaWidget').val('remarketingNavegacao');
			$('#formatoWidget').val('slider_complementar');
			passo2.hide()
			//pula passo 3
			passo4 = $('#tabConfiguracoes');
			passo4.show();
			document.getElementsByClassName('btn-maker')[1].classList.remove('active');
			document.getElementsByClassName('btn-maker')[3].classList.add('active');
			$('#crumbInteligencia').html('remarketing navegação');				
			$('#crumbFormato').html('remarketing navegação');
		} else {
			$('#inteligenciaWidget').val(this.value);
			document.getElementsByClassName('btn-maker')[1].classList.remove('active');
			document.getElementsByClassName('btn-maker')[2].classList.add('active');
			$('#crumbInteligencia').html(this.value.replace(/([A-Z])/g, ' $1'));
			passo2.hide();
			passo3 = $('#tabFormatos');
			passo3.show();
		}
	});
//----------------------------------------//

// PASSO 3 HOME ---------------------------//
	$('.btnSelecionaInteligenciaHome').on('click', function(){
		switch(this.value){
			case 'remarketing':
				$('#inteligenciaWidget').val('remarketing');
				passo2.hide();
				passo3 = $('#tabFormatos');
				passo3.show();				
				document.getElementsByClassName('btn-maker')[1].classList.remove('active');
				document.getElementsByClassName('btn-maker')[2].classList.add('active');
				$('#crumbInteligencia').html('remarketing on-site');
				break;
			case 'bossChoice':
				$('#inteligenciaWidget').val('bossChoice');
				passo2.hide()
				passo3 = $('#tabFormatos');
				passo3.show();
				document.getElementsByClassName('btn-maker')[1].classList.remove('active');
				document.getElementsByClassName('btn-maker')[2].classList.add('active');
				$('#crumbInteligencia').html('boss Choice');
				break;
			case 'topTrends':
				$('#inteligenciaWidget').val('topTrends');
				passo2.hide();
				passo3 = $('#tabFormatos');
				passo3.show();
				document.getElementsByClassName('btn-maker')[1].classList.remove('active');
				document.getElementsByClassName('btn-maker')[2].classList.add('active');
				$('#crumbInteligencia').html('top Trends');
				break;
			case 'novidades':
				$('#inteligenciaWidget').val('novidades');
				passo2.hide();
				passo3 = $('#tabFormatos');
				passo3.show();
				document.getElementsByClassName('btn-maker')[1].classList.remove('active');
				document.getElementsByClassName('btn-maker')[2].classList.add('active');
				$('#crumbInteligencia').html('novidades');
				break;
			case 'facebook':
				$('#inteligenciaWidget').val('facebook');
				passo2.hide()
				passo3 = $('#tabFormatos');
				passo3.show();
				document.getElementsByClassName('btn-maker')[1].classList.remove('active');
				document.getElementsByClassName('btn-maker')[2].classList.add('active');
				$('#crumbInteligencia').html('mais vendidos do facebook');
				break;
			case 'google':
				$('#inteligenciaWidget').val('google');
				passo2.hide()
				passo3 = $('#tabFormatos');
				passo3.show();
				document.getElementsByClassName('btn-maker')[1].classList.remove('active');
				document.getElementsByClassName('btn-maker')[2].classList.add('active');
				$('#crumbInteligencia').html('mais vendidos do google');
				break;
			case 'genero':
				$('#inteligenciaWidget').val('genero');
				passo2.hide()
				passo3 = $('#tabFormatos');
				passo3.show();
				document.getElementsByClassName('btn-maker')[1].classList.remove('active');
				document.getElementsByClassName('btn-maker')[2].classList.add('active');
				$('#crumbInteligencia').html('genero');
				break;
			case 'navegacaoComplementar':
				$('#inteligenciaWidget').val('navegacaoComplementar');
				passo2.hide()
				passo3 = $('#tabFormatos');
				passo3.show();
				document.getElementsByClassName('btn-maker')[1].classList.remove('active');
				document.getElementsByClassName('btn-maker')[2].classList.add('active');
				$('#crumbInteligencia').html('navegacao Complementar');
				break;
			case 'palavraChave':
				$('#inteligenciaWidget').val('palavraChave');
				passo2.hide()
				passo3 = $('#tabFormatos');
				passo3.show();
				document.getElementsByClassName('btn-maker')[1].classList.remove('active');
				document.getElementsByClassName('btn-maker')[2].classList.add('active');
				$('#crumbInteligencia').html('palavra Chave');
				break;
			case 'geolocalizacao':
				$('#inteligenciaWidget').val('geolocalizacao');
				passo2.hide()
				passo3 = $('#tabFormatos');
				passo3.show();
				document.getElementsByClassName('btn-maker')[1].classList.remove('active');
				document.getElementsByClassName('btn-maker')[2].classList.add('active');
				$('#crumbInteligencia').html('geolocalizacao');
				break;
			case 'faixaEtaria':
				$('#inteligenciaWidget').val('faixaEtaria');
				passo2.hide()
				passo3 = $('#tabFormatos');
				passo3.show();
				document.getElementsByClassName('btn-maker')[1].classList.remove('active');
				document.getElementsByClassName('btn-maker')[2].classList.add('active');
				$('#crumbInteligencia').html('faixa Etaria');
				break;
			case 'manualCarrinho':
				$('#inteligenciaWidget').val('manualCarrinho');
				passo2.hide();
				passo3 = $('#tabFormatos');
				passo3.show();
				document.getElementsByClassName('btn-maker')[1].classList.remove('active');
				document.getElementsByClassName('btn-maker')[2].classList.add('active');
				$('#crumbInteligencia').html('manual Carrinho');
				break;
			case 'topCarrinho':
				$('#inteligenciaWidget').val('topCarrinho');
				passo2.hide();
				passo3 = $('#tabFormatos');
				passo3.show();
				document.getElementsByClassName('btn-maker')[1].classList.remove('active');
				document.getElementsByClassName('btn-maker')[2].classList.add('active');
				$('#crumbInteligencia').html('top Carrinho');
				break;
			case 'baixouDePreço':
				$('#inteligenciaWidget').val('baixouDePreco');
				passo2.hide();
				passo3 = $('#tabFormatos');
				passo3.show();
				document.getElementsByClassName('btn-maker')[1].classList.remove('active');
				document.getElementsByClassName('btn-maker')[2].classList.add('active');
				$('#crumbInteligencia').html('baixou De Preço');
				break;
			case 'porAtributo':
				$('#inteligenciaWidget').val('porAtributo');
				passo2.hide();
				passo3 = $('#tabFormatos');
				passo3.show();
				document.getElementsByClassName('btn-maker')[1].classList.remove('active');
				document.getElementsByClassName('btn-maker')[2].classList.add('active');
				$('#crumbInteligencia').html('por Atributo');
				break;
			case 'vitrineBusca':
				$('#inteligenciaWidget').val('vitrineBusca');
				passo2.hide();
				passo3 = $('#tabFormatos');
				passo3.show();
				document.getElementsByClassName('btn-maker')[1].classList.remove('active');
				document.getElementsByClassName('btn-maker')[2].classList.add('active');
				$('#crumbInteligencia').html('vitrine Busca');
				break;
			case 'maisVendidos':
				$('#inteligenciaWidget').val('maisVendidos');
				passo2.hide()
				passo3 = $('#tabFormatos');
				passo3.show();
				document.getElementsByClassName('btn-maker')[1].classList.remove('active');
				document.getElementsByClassName('btn-maker')[2].classList.add('active');
				$('#crumbInteligencia').html('mais Vendidos');
				break;
			case 'liquidacao':
				$('#inteligenciaWidget').val('liquidacao');	
				passo2.hide()
				passo3 = $('#tabFormatos');
				passo3.show();
				passo3.find('.card-maker').hide();
				passo3.find('.liquidacao').show();
				document.getElementsByClassName('btn-maker')[1].classList.remove('active');
				document.getElementsByClassName('btn-maker')[2].classList.add('active');
				$('#crumbInteligencia').html('liquidação');
				break;
			case 'barraDeBusca':
				$('#inteligenciaWidget').val('barraDeBusca');
				passo2.hide();
				passo3 = $('#tabConfiguracoes');				
				passo3.show();
				document.getElementsByClassName('btn-maker')[1].classList.remove('active');
				document.getElementsByClassName('btn-maker')[3].classList.add('active');
				$('#crumbInteligencia').html('barra De Busca');
				break;		
			case 'ofertaLimitada':
				$('#inteligenciaWidget').val('ofertaLimitada');
				document.getElementsByClassName('btn-maker')[1].classList.remove('active');
				document.getElementsByClassName('btn-maker')[2].classList.add('active');
				passo2.hide();
				passo3 = $('#tabFormatos');
				passo3.show();
				$('#crumbInteligencia').html('oferta limitada');
				break;
			case 'maisVendidosDaCategoriaManual':
				$('#inteligenciaWidget').val('maisVendidosDaCategoriaManual');
				document.getElementsByClassName('btn-maker')[1].classList.remove('active');
				document.getElementsByClassName('btn-maker')[2].classList.add('active');
				passo2.hide();
				passo3 = $('#tabFormatos');
				passo3.show();
				$('#crumbInteligencia').html('Mais Vendidos da Categoria Manual');
				break;
			case 'remarketingNavegacao':
				$('#inteligenciaWidget').val('remarketingNavegacao');
				$('#formatoWidget').val('slider_complementar');
				passo2.hide()
				//pula passo 3
				passo4 = $('#tabConfiguracoes');
				passo4.show();
				document.getElementsByClassName('btn-maker')[1].classList.remove('active');
				document.getElementsByClassName('btn-maker')[3].classList.add('active');
				$('#crumbInteligencia').html('remarketing navegação');				
				$('#crumbFormato').html('remarketing navegação');
				break;
			case 'smartHome':
				$('#inteligenciaWidget').val('smartHome');
				passo2.hide();
				passo3 = $('#tabFormatos');
				passo3.show();
				passo3.find('.card-maker').hide();
				passo3.find('.topCarrinho').show();
				document.getElementsByClassName('btn-maker')[1].classList.remove('active');
				document.getElementsByClassName('btn-maker')[2].classList.add('active');
				$('#crumbInteligencia').html('Smart Home');
				break;
			default:
				$('#inteligenciaWidget').val(this.value);
				document.getElementsByClassName('btn-maker')[1].classList.remove('active');
				document.getElementsByClassName('btn-maker')[2].classList.add('active');
				passo2.hide();
				passo3 = $('#tabFormatos');
				passo3.show();
				$('#crumbInteligencia').html(this.value.replace(/([A-Z])/g, ' $1'));
				break;	
		}
	});
// ----------------------------------------//

// PASSO 3 PAGINA DE CATEGORIA ------------//
	$('.btnSelecionaInteligenciaCategoria').on('click',function(){
		switch(this.value){
			case 'maisVendidosMesmaCategoria':
				$('#inteligenciaWidget').val('maisVendidosMesmaCategoria');
				document.getElementsByClassName('btn-maker')[1].classList.remove('active');
				document.getElementsByClassName('btn-maker')[2].classList.add('active');
				passo2.hide();
				passo3 = $('#tabFormatos');
				passo3.show();
				$('#crumbInteligencia').html('mais Vendidos da Mesma Categoria');
				break;
			case 'maisVendidosDaCategoriaManual':
				$('#inteligenciaWidget').val('maisVendidosDaCategoriaManual');
				document.getElementsByClassName('btn-maker')[1].classList.remove('active');
				document.getElementsByClassName('btn-maker')[2].classList.add('active');
				passo2.hide();
				passo3 = $('#tabFormatos');
				passo3.show();
				$('#crumbInteligencia').html('Mais Vendidos da Categoria Manual');
				break;	
		}		
	});	
				
// ----------------------------------------//

// PASSO 3 BUSCA VAZIA --------------------//
	$('.btnSelecionaInteligenciaBusca').on('click', function(){
		switch(this.value){
			case 'remarketing':
				$('#inteligenciaWidget').val('remarketing');		
				passo2.hide()
				passo3 = $('#tabFormatos');
				passo3.show();
				document.getElementsByClassName('btn-maker')[1].classList.remove('active');
				document.getElementsByClassName('btn-maker')[2].classList.add('active');
				$('#crumbInteligencia').html('remarketing on-site');
				break;
			case 'bossChoice':
				$('#inteligenciaWidget').val('bossChoice');	
				passo2.hide()
				passo3 = $('#tabFormatos');
				passo3.show();
				document.getElementsByClassName('btn-maker')[1].classList.remove('active');
				document.getElementsByClassName('btn-maker')[2].classList.add('active');
				$('#crumbInteligencia').html('boss Choice');
				break;
			case 'topTrends':
				$('#inteligenciaWidget').val('topTrends');	
				passo2.hide()
				passo3 = $('#tabFormatos');
				passo3.show();
				document.getElementsByClassName('btn-maker')[1].classList.remove('active');
				document.getElementsByClassName('btn-maker')[2].classList.add('active');
				$('#crumbInteligencia').html('top Trends');
				break;
			case 'navegacaoComplementar':
				$('#inteligenciaWidget').val('navegacaoComplementar');
				document.getElementsByClassName('btn-maker')[1].classList.remove('active');
				document.getElementsByClassName('btn-maker')[2].classList.add('active');
				passo2.hide();
				passo3 = $('#tabFormatos');
				passo3.show();
				$('#crumbInteligencia').html('navegação complementar');
				break;
			case 'novidades':
				$('#inteligenciaWidget').val('novidades');	
				passo2.hide()
				passo3 = $('#tabFormatos');
				passo3.show();
				document.getElementsByClassName('btn-maker')[1].classList.remove('active');
				document.getElementsByClassName('btn-maker')[2].classList.add('active');
				$('#crumbInteligencia').html('novidades');
				break;
			case 'faixaEtaria':
				$('#inteligenciaWidget').val('faixaEtaria');	
				passo2.hide()
				passo3 = $('#tabFormatos');
				passo3.show();
				document.getElementsByClassName('btn-maker')[1].classList.remove('active');
				document.getElementsByClassName('btn-maker')[2].classList.add('active');
				$('#crumbInteligencia').html('faixa Etaria');
				break;
			case 'maisVendidos':
				$('#inteligenciaWidget').val('maisVendidos');	
				passo2.hide()
				passo3 = $('#tabFormatos');
				passo3.show();
				document.getElementsByClassName('btn-maker')[1].classList.remove('active');
				document.getElementsByClassName('btn-maker')[2].classList.add('active');
				$('#crumbInteligencia').html('mais Vendidos');
				break;
			case 'liquidacao':
				$('#inteligenciaWidget').val('liquidacao');	
				passo2.hide()
				passo3 = $('#tabFormatos');
				passo3.show();
				passo3.find('.card-maker').hide();
				passo3.find('.liquidacao').show();
				document.getElementsByClassName('btn-maker')[1].classList.remove('active');
				document.getElementsByClassName('btn-maker')[2].classList.add('active');
				$('#crumbInteligencia').html('liquidacao');
				break;
			case 'palavraChave':
				$('#inteligenciaWidget').val('palavraChave');	
				passo2.hide()
				passo3 = $('#tabFormatos');
				passo3.show();
				document.getElementsByClassName('btn-maker')[1].classList.remove('active');
				document.getElementsByClassName('btn-maker')[2].classList.add('active');
				$('#crumbInteligencia').html('palavra chave');
				break;
			case 'vitrineBusca':
				$('#inteligenciaWidget').val('vitrineBusca');	
				passo2.hide()
				passo3 = $('#tabFormatos');
				passo3.show();
				document.getElementsByClassName('btn-maker')[1].classList.remove('active');
				document.getElementsByClassName('btn-maker')[2].classList.add('active');
				$('#crumbInteligencia').html('vitrine de busca');
				break;
			case 'baixouDePreço':
				$('#inteligenciaWidget').val('baixouDePreco');	
				passo2.hide()
				passo3 = $('#tabFormatos');
				passo3.show();
				document.getElementsByClassName('btn-maker')[1].classList.remove('active');
				document.getElementsByClassName('btn-maker')[2].classList.add('active');
				$('#crumbInteligencia').html('baixou De Preço');
				break;
			case 'melhoresAvaliados':
				$('#inteligenciaWidget').val('melhoresAvaliados');
				document.getElementsByClassName('btn-maker')[1].classList.remove('active');
				document.getElementsByClassName('btn-maker')[2].classList.add('active');
				passo2.hide();
				passo3 = $('#tabFormatos');
				passo3.show();
				$('#crumbInteligencia').html('melhores avaliados');
				break;	
			case 'recemAvaliados':
				$('#inteligenciaWidget').val('recemAvaliados');
				document.getElementsByClassName('btn-maker')[1].classList.remove('active');
				document.getElementsByClassName('btn-maker')[2].classList.add('active');
				passo2.hide();
				passo3 = $('#tabFormatos');
				passo3.show();
				$('#crumbInteligencia').html('recem avaliados');
				break;
			case 'maisVendidosDaCategoriaManual':
				$('#inteligenciaWidget').val('maisVendidosDaCategoriaManual');
				document.getElementsByClassName('btn-maker')[1].classList.remove('active');
				document.getElementsByClassName('btn-maker')[2].classList.add('active');
				passo2.hide();
				passo3 = $('#tabFormatos');
				passo3.show();
				$('#crumbInteligencia').html('mais vendidos da categoria manual');
				break;	
			case 'ofertaLimitada':
				$('#inteligenciaWidget').val('ofertaLimitada');
				document.getElementsByClassName('btn-maker')[1].classList.remove('active');
				document.getElementsByClassName('btn-maker')[2].classList.add('active');
				passo2.hide();
				passo3 = $('#tabFormatos');
				passo3.show();
				$('#crumbInteligencia').html('oferta limitada');
				break;	
			case 'remarketingNavegacao':
				$('#inteligenciaWidget').val('remarketingNavegacao');
				$('#formatoWidget').val('slider_complementar');
				passo2.hide()
				//pula passo 3
				passo4 = $('#tabConfiguracoes');
				passo4.show();
				document.getElementsByClassName('btn-maker')[1].classList.remove('active');
				document.getElementsByClassName('btn-maker')[3].classList.add('active');
				$('#crumbInteligencia').html('remarketing navegação');				
				$('#crumbFormato').html('remarketing navegação');
				break;
			default:
				$('#inteligenciaWidget').val(this.value);
				document.getElementsByClassName('btn-maker')[1].classList.remove('active');
				document.getElementsByClassName('btn-maker')[2].classList.add('active');
				passo2.hide();
				passo3 = $('#tabFormatos');
				passo3.show();
				$('#crumbInteligencia').html(this.value.replace(/([A-Z])/g, ' $1'));
				break;
		}
	});
// ----------------------------------------//

// PASSO 3 PAGINA DE CARRINHO -------------//
	$('.btnSelecionaInteligenciaCarrinho').on('click', function(){
		switch(this.value){
			case 'topCarrinho':
				$('#inteligenciaWidget').val('topCarrinho');
				passo2.hide();
				passo3 = $('#tabFormatos');
				passo3.show();
				passo3.find('.card-maker').hide();
				passo3.find('.topCarrinho').show();
				document.getElementsByClassName('btn-maker')[1].classList.remove('active');
				document.getElementsByClassName('btn-maker')[2].classList.add('active');
				$('#crumbInteligencia').html('Carrinho Complementar');
				break;
			case 'manualCarrinho':
				$('#inteligenciaWidget').val('manualCarrinho');
				passo2.hide();
				passo3 = $('#tabFormatos');
				passo3.show();
				passo3.find('.card-maker').hide();
				passo3.find('.manualCarrinho').show();
				document.getElementsByClassName('btn-maker')[1].classList.remove('active');
				document.getElementsByClassName('btn-maker')[2].classList.add('active');
				$('#crumbInteligencia').html('Carrinho manual');
				break;
			/*case 'ofertaLimitada':
				$('#inteligenciaWidget').val('ofertaLimitada');
				document.getElementsByClassName('btn-maker')[1].classList.remove('active');
				document.getElementsByClassName('btn-maker')[2].classList.add('active');
				passo2.hide();
				passo3 = $('#tabFormatos');
				passo3.show();
				$('#crumbInteligencia').html('oferta limitada');
				break;*/	
		}
	});
// ----------------------------------------//

// PASSO 4(FORMATOS) É IGUAL PRA TODO MUNDO
// PASSO 4 --------------------------------//
	$('.btnSelecionaFormato').on('click', function(){
		switch(this.value){
			case 'prateleira':
				$('#formatoWidget').val('prateleira');
				passo3.hide();
				passo4 = $('#tabConfiguracoes');
				passo4.show();
				document.getElementsByClassName('btn-maker')[2].classList.remove('active');
				document.getElementsByClassName('btn-maker')[3].classList.add('active');
				$('#crumbFormato').html('prateleira');
				break;
			case 'prateleiraDupla':
				$('#formatoWidget').val('prateleiraDupla');
				passo3.hide();
				passo4 = $('#tabConfiguracoes');
				passo4.show();
				document.getElementsByClassName('btn-maker')[2].classList.remove('active');
				document.getElementsByClassName('btn-maker')[3].classList.add('active');
				$('#crumbFormato').html('prateleira Dupla');
				break;
			case 'carrossel':
				$('#formatoWidget').val('carrossel');
				passo3.hide();
				passo4 = $('#tabConfiguracoes');
				passo4.show();
				document.getElementsByClassName('btn-maker')[2].classList.remove('active');
				document.getElementsByClassName('btn-maker')[3].classList.add('active');
				$('#crumbFormato').html('carrossel');
				break;
			case 'totem':
				$('#formatoWidget').val('totem');
				passo3.hide();
				passo4 = $('#tabConfiguracoes');
				passo4.show();
				document.getElementsByClassName('btn-maker')[2].classList.remove('active');
				document.getElementsByClassName('btn-maker')[3].classList.add('active');
				$('#crumbFormato').html('totem');
				break;
		}

		switch( $('#inteligenciaWidget').val() ){
			case 'topTrends':
				$('#cadastrarTopTrends').show();
				break;
			case 'maisVendidos':
				$('#cadastrarMaisVendidos').show();
				break;
			case 'maisVendidosDaCategoriaManual':
				$('#cadastrarMaisVendidosDaCategoriaManual').show();
				break;
			case 'palavraChave':
				$('#cadastrarPalavraChave').show();
				break;
			case 'collection':
				$('#cadastrarCollection').show();
				break;
			/* case 'compreJunto': //o compre junto pula o passo 3. Alterações aqui não surtirão efeito se ele não passar por esse passo
				$('#cadastrarCompreJunto').show();
				break; */
			case 'bossChoice':
				$('#cadastrarProdutos').show();
				break;
			case 'manualCarrinho':
				$('#cadastrarCarrinhoManual').show();
				break;
			case 'manualCarrinho':
				$('#cadastrarCarrinhoManual').show();
				break;
			case 'genero':
				$('#cadastrarGenero').show();
				break;
			case 'faixaEtaria':
				$('#cadastrarFaixaEtaria').show();
				break;
			case 'produtosRelacionados':
				$('#cadastrarRelacionados').show();
				break;
		}
	});	
		
//-----------------------------------------//

// BOTAO VOLTAR
	$('.btnVoltaPasso').on('click',function(){
		switch('block'){
			case $(passo2).css('display'):
				passo2.hide();
				passo1.show();
				document.getElementsByClassName('btn-maker')[1].classList.remove('active');
				document.getElementsByClassName('btn-maker')[0].classList.add('active');
				$('#crumbPagina').html('');
				$('.btnVoltaPasso').hide();
				break;
			case $(passo2).css('display'):
				passo2.hide();
				passo2.show();
				document.getElementsByClassName('btn-maker')[1].classList.remove('active');
				document.getElementsByClassName('btn-maker')[1].classList.add('active');
				$('#crumbPlacement').html('');
				break;
			case $(passo3).css('display'):
				passo3.hide();
				passo2.show();
				document.getElementsByClassName('btn-maker')[3].classList.remove('active');
				document.getElementsByClassName('btn-maker')[2].classList.remove('active');
				document.getElementsByClassName('btn-maker')[1].classList.add('active');
				$('#crumbInteligencia').html('');
				break;
			case $(passo4).css('display'):
				if($('#crumbFormato').html() === 'Compre Junto'){ //compre junto					
					passo4.hide();
					passo2.show();
					document.getElementsByClassName('btn-maker')[3].classList.remove('active');
					document.getElementsByClassName('btn-maker')[1].classList.add('active');
					$('#crumbInteligencia').html('');
				} else if($('#crumbFormato').html() === 'remarketing navegação'){ //remarketing navegação					
					passo4.hide();
					passo2.show();
					document.getElementsByClassName('btn-maker')[3].classList.remove('active');
					document.getElementsByClassName('btn-maker')[1].classList.add('active');
					$('#crumbInteligencia').html('');
				} else {
					passo4.hide();
					passo3.show();
					document.getElementsByClassName('btn-maker')[3].classList.remove('active');
					document.getElementsByClassName('btn-maker')[2].classList.add('active');
				}					
				$('#crumbFormato').html('');
				break;
		}

		$('#cadastrarCarrinhoManual').hide();		
		$('#cadastrarEstado').hide();
		$('#cadastrarCompreJunto').hide();
		$('#cadastrarPalavraChave').hide();
		$('#cadastrarMaisVendidosMesmaCategoria').hide();
		$('#cadastrarMaisVendidosDaCategoriaManual').hide();
		$('#cadastrarMaisVendidos').hide();
		$('#cadastrarTopTrends').hide();
		$('#cadastrarProdutos').hide();
		$('#cadastrarGenero').hide();
		$('#cadastrarFaixaEtaria').hide();
		$('#cadastrarRelacionados').hide();
		$('#cadastrarCollection').hide();
		$('#FORMWIDGET')[0].reset();
	});


});

// ADD BUY TOGETHER CONF GROUP
$('.addConfGroup').click(function(event) {
	
	var
		el 	  = $(this).prev(),
		id 	  = parseInt(el.attr('data-id')),
		html  = el[0].outerHTML,
		regex = new RegExp('collapse' + id, 'g');

	html = html.replace(regex, 'collapse' + (id + 1)).replace('Grupo de Configuração ' + id, 'Grupo de Configuração ' + (id + 1));

	html = html.replace('<!-- replaceForCloseButton -->','<a class="btn-apaga-cfg red" onclick="this.parentElement.remove();"><i class="fa fa-trash"></i></a>');

	$(this).before(html);

	el = $(this).prev();
	
	el.attr('data-id', (id + 1));

	event.preventDefault();
});

function preencheCampoAuto(titulo, id, formato){
	$('#produto').val(titulo);
	bossChoiceProdId = id;
	bossChoiceProdTitulo = titulo.replace(",", ".");
	$('#listaProdutosAutocomplete').fadeOut();
	$('#listaProdutosAutocomplete').html("");
}

validaProdutos = function(){
	// produtos manuais
	if ($('#inputProdutos').parent().is(':visible') && $('#inputProdutos').val().split(',').length < 4) {
		if (!$('#divTagsProdutos').next().hasClass('help-block')){ 
			$('#divTagsProdutos').css('border-color', '#FF7588'); // BORDA VERMELHRA
			$('#divTagsProdutos').after('<div style="color: #FF7588 !important;" class="help-block"><ul role="alert"><li>Adicione pelo menos quatro produtos</li></ul></div>');
		}
	} else {		
		$('#divTagsProdutos').css('border-color', '#16D39A'); // BORDA VERMELHRA
		if ($('#divTagsProdutos').next().hasClass('help-block')){
			$('#divTagsProdutos').next().remove();
		}
	}
}

validaCollection = function(){
	if ($('#inpuCollection').parent().is(':visible') && $('#inpuCollection').val().split(',').length < 1) {
		if (!$('#divTagsCollection').next().hasClass('help-block')){ 
			$('#divTagsCollection').css('border-color', '#FF7588'); // BORDA VERMELHRA
			$('#divTagsCollection').after('<div style="color: #FF7588 !important;" class="help-block"><ul role="alert"><li>Adicione pelo menos um termo</li></ul></div>');
		}
	} else {		
		$('#divTagsCollection').css('border-color', '#16D39A'); // BORDA VERMELHRA
		if ($('#divTagsCollection').next().hasClass('help-block')){
			$('#divTagsCollection').next().remove();
		}
	}
}

validaWidget = function(){
	var 
	formulario = document.getElementById('FORMWIDGET'),
	erros = document.getElementsByClassName('error');

	// posicao 0 do erro é sempre o formulario
	for (var i = 1; i < erros.length; i++){
		if ($(erros[i]).is(':visible')){
			return false;
		}
	}

	// bloqueia
	bloqueiaElemento($('.widget_maker_content'));

	var erroProdutos = $('#divTagsProdutos').is(':visible') && $('#divTagsProdutos').next().hasClass('help-block');
	var erroInformacoes = $('#nomeBloco').val() === '' && $('#tituloBloco').val() === '';


	// CHECA CAMPOS ESPECIFICOS
	if (erroProdutos || erroInformacoes){ 
		return false;
	}
	
	var formdata = $(formulario).serialize();
	
	$.post('resource/resouce_widget_maker.php', formdata, function(data, textStatus, xhr) {
		
		if (data) {
			toastr.success('Recomendação criada com sucesso');
			setTimeout(function() {
				//$('.btnVoltaPasso').trigger('click').trigger('click').trigger('click').trigger('click');
				//desbloqueiaElemento($('.widget_maker_content'));
				window.location.reload()
			}, 500);
		}

		console.log('data -> ', data);
	});
}

// adiciona palavra pai e palavra filho produtos relacionados
adicionaPaiFilho = function(){
	var
	pai = $('#prodRelPai'),
	filho = $('#prodRelFilho'),
	input = $('#palavrasPaiFilho'),
	valPai = pai.val().trim(),
	valFilho = filho.val().trim(),
	value = valPai+' -> '+valFilho;

	if (valFilho === '' || valFilho === ''){
		toastr['error']('Você não pode adicionar palavras vazias como parâmetro');
		return false;
	}

	$('#palavrasPaiFilho').tagsinput('add',value);

	// reseta
	pai.val('');
	filho.val('');

	return false;
}

adicionaProdutos = function(){

	if ($('#produto').val().trim() === '') {
		toastr['error']('Você precisa digitar um nome para o produto');
		$('#produto').focus();
		return false;
	}

	var tamanho = $('#inputProdutos').val().split(',').length;

	if (tamanho >= 24){
		toastr['error']('Você pode cadastrar no máximo 24 produtos');
		return false;
	}

	if ($('#produto').val().trim() != bossChoiceProdTitulo.trim()) {
		toastr['error']('Você precisa escolher um dos produtos da lista');
		$('#produto').focus();
		return false;
	}
	$('#inputProdutos').tagsinput('add', {value: bossChoiceProdId, text: bossChoiceProdTitulo});
	$('#produto').val(''); 

	if (tamanho < 24){
		$('#produto').focus();
	}
	return false;
}

adicionaUrl = function(){
	var 
	url = $('#urlInput').val().trim();

	$('#inputUrls').tagsinput('add', url);

	$('#urlInput').val('');
	$('#urlInput').focus();
}
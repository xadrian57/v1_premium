// SLIDER TEMPLATES

// BOTOES DE CONFIGURACAO DE CADA TEMPLATE
$(document).ready(function() {
	$('#btnConfiguraTags').on('click', function(){
		$('#configuracaoTags').show();
	});

	$('#btnConfiguraGrid').on('click', function(){
		$('#configuracaoGrids').show();
	});

	$('#btnConfiguraFonte').on('click', function(){
		$('#configuracaoFontes').show();
	});

	$('#btnConfiguraCor').on('click', function(){
		$('#configuracaoCores').show();
	});

	$('#btnConfiguraBotao').on('click', function(){
		$('#configuracaoBotoes').show();
	});

	$('#btnConfiguraBorda').on('click', function(){
		$('#configuracaoBordas').show();
	});

	$('#btnConfiguraMargens').on('click', function(){
		$('#configuracaoMargem').show();
	});

	// FUNÇÃO FECHAR OPCOES DE CONFIGURACOES
	$('.btn-fecha-layout-cfg li a').on('click', function(){
		this.parentElement.parentElement.parentElement.style.display = 'none';
	});
});


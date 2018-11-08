/*
--	Autor: Eliabe
--	Data: 10/2017
--	Update: 16/02/2018
--	Desc: Script que retorna todos os dados do banco em um JSON
--
*/
'use strict';
(function () {
	window['idCli'] = $('#infsess').attr('data-cli');
	$.ajax({
		type: 'post',
		url: 'resource/resource_configuracoes.php',
		data: { 'id': idCli, 'op': 1 },
		success: function (response) {
			console.log(response);
			window['dados'] = JSON.parse(response);
			carregaConfiguracoes(dados);
		}
	});

	function carregaConfiguracoes(dados) {
		$('#desconto').val(dados.desconto);
		$('#numeroParcelas').val(dados.numeroParcelas);
		$('#valorParcelas').val((dados.valorParcelas));
		$('#site').val(dados.site);
		$('#corPrimaria').val('#' + (dados.corPrimaria || '#fff'));
		$('#corSecundaria').val('#' + (dados.corSecundaria || '#fff'));

		if ($('#desconto').val() !== '') {
			var valor = $('#desconto').val();
			$('#desconto').val(valor + '%');
		}

		// Posiciona o estado de ativação da Trustvox
		$('#ativa-trustvox').prop("checked", window['dados']['trustvoxAtiva']);

		// pixel		
		//VTEX = 1; LOJA INTEGRADA = 2; WOO COMMERCE = 3; MAGENTO = 4; OUTROS = 0
		var pixel = '';
		switch (dados.plataforma) {
			case '0': // OUTROS
				var plat = 'generic';
				// tutorial
				$('#video-tutorial').attr('src', 'https://www.youtube.com/embed/4g_nF8e8DVA?rel=0&amp;controls=0&amp;showinfo=0');
				break;

			case '1': // VTEX
				var plat = 'vtex';
				// tutorial
				$('#video-tutorial').attr('src', 'https://www.youtube.com/embed/DiQh6IF_vWs?rel=0&amp;controls=0&amp;showinfo=0');
				break;

			case '2': // LOJA INTEGRADA
				var idSHA1 = dados.idSHA1;
				$('#codigo-pixel').text(idSHA1);
				$('#inputPixel').val(idSHA1);
				$('#btnAbreModalCfg').html('Visualizar meu ID');
				$('#tituloModal').html('Esse é o seu ID')
				// tutorial
				$('#video-tutorial').attr('src', 'https://www.youtube.com/embed/wYoYeScab9E?rel=0&amp;controls=0&amp;showinfo=0');
				$('#video-tutorial').attr('src', 'https://www.youtube.com/embed/Dezl67UAC9A?rel=0&amp;controls=0&amp;showinfo=0');
				break;

			case '3': // WOO COMMERCE
				var idSHA1 = dados.idSHA1;
				$('#codigo-pixel').text(idSHA1);
				$('#inputPixel').val(idSHA1);
				$('#btnAbreModalCfg').html('Visualizar meu ID');
				$('#tituloModal').html('Esse é o seu ID')
				// tutorial
				$('#video-tutorial').attr('src', 'https://www.youtube.com/embed/noto8PSKl7I?rel=0&amp;controls=0&amp;showinfo=0');
				break;

			case '4': // MAGENTO
				var plat = 'magento';
				// tutorial
				$('#video-tutorial').attr('src', 'https://www.youtube.com/embed/cfK_HvPIPsg?rel=0&amp;controls=0&amp;showinfo=0');
				break;

			case '5': // ISET
				var idSHA1 = dados.idSHA1;
				$('#codigo-pixel').text(idSHA1);
				$('#inputPixel').val(idSHA1);
				$('#btnAbreModalCfg').html('Visualizar meu ID');
				$('#tituloModal').html('Esse é o seu ID')
				// tutorial
				$('#video-tutorial').attr('src', 'https://www.youtube.com/embed/fR3GENvkZHE?rel=0&amp;controls=0&amp;showinfo=0');
				break;

			case '6': // ANYSHOP
				var plat = 'integrado';
				// tutorial
				$('#video-tutorial').attr('src', 'https://www.youtube.com/embed/KqUtdbN4QAE?rel=0&amp;controls=0&amp;showinfo=0');
				break;

			case '7': // SIGNATIVA
				var plat = 'magento';
				$('#video-tutorial').attr('src', 'https://www.youtube.com/embed/hCBYHHHsLKY?rel=0&amp;controls=0&amp;showinfo=0');
				break;

			case '8': // RP COMMERCE
				var idSHA1 = dados.idSHA1;
				$('#codigo-pixel').text(idSHA1);
				$('#inputPixel').val(idSHA1);
				$('#btnAbreModalCfg').html('Visualizar meu ID');
				$('#tituloModal').html('Esse é o seu ID')
				// tutorial
				$('#video-tutorial').attr('src', 'https://www.youtube.com/embed/1P27S_Ple0I?rel=0&amp;controls=0&amp;showinfo=0');
				break;

			case '9': // XTECH
				var idSHA1 = dados.idSHA1;
				$('#codigo-pixel').text(idSHA1);
				$('#inputPixel').val(idSHA1);
				$('#btnAbreModalCfg').html('Visualizar meu ID');
				$('#tituloModal').html('Esse é o seu ID')
				// tutorial
				$('#video-tutorial').attr('src', 'https://www.youtube.com/embed/JRc2TpPR7J0?rel=0&amp;controls=0&amp;showinfo=0');
				break;

			case '10': // PLATAFORMA ONLINE 
				var idSHA1 = dados.idSHA1;
				$('#codigo-pixel').text(idSHA1);
				$('#inputPixel').val(idSHA1);
				$('#btnAbreModalCfg').html('Visualizar meu ID');
				$('#tituloModal').html('Esse é o seu ID')
				// tutorial
				$('#video-tutorial').attr('src', 'https://www.youtube.com/embed/tE3SWHCWw6Y?rel=0&amp;controls=0&amp;showinfo=0');
				break;

			case '11': // MOOVIN
				var plat = 'moovin';
				// tutorial
				$('#video-tutorial').attr('src', 'https://www.youtube.com/embed/4g_nF8e8DVA?rel=0&amp;controls=0&amp;showinfo=0');
				break;

			case '12': // E-COM CLUB
				var idSHA1 = dados.idSHA1;
				$('#codigo-pixel').text(idSHA1);
				$('#inputPixel').val(idSHA1);
				$('#btnAbreModalCfg').html('Visualizar meu ID');
				$('#tituloModal').html('Esse é o seu ID')
				// tutorial
				$('#video-tutorial').attr('src', 'https://www.youtube.com/embed/XJRHsdr14vk?rel=0&amp;controls=0&amp;showinfo=0');
				break;

			case '13': // TMW
				var idSHA1 = dados.idSHA1;
				$('#codigo-pixel').text(idSHA1);
				$('#inputPixel').val(idSHA1);
				$('#btnAbreModalCfg').html('Visualizar meu ID');
				$('#tituloModal').html('Esse é o seu ID')
				// tutorial
				$('#video-tutorial').attr('src', 'https://www.youtube.com/embed/JFSyHP-2zSw?rel=0&amp;controls=0&amp;showinfo=0');
				break;

			case '14': // Irroba
				var idSHA1 = dados.idSHA1;
				$('#codigo-pixel').text(idSHA1);
				$('#inputPixel').val(idSHA1);
				$('#btnAbreModalCfg').html('Visualizar meu ID');
				$('#tituloModal').html('Esse é o seu ID')
				// tutorial
				$('#video-tutorial').attr('src', 'https://www.youtube.com/embed/nJBXlgYagDM?rel=0&amp;controls=0&amp;showinfo=0');
				break;

			case '15': // Adsmanager
				var idSHA1 = dados.idSHA1;
				$('#codigo-pixel').text(idSHA1);
				$('#inputPixel').val(idSHA1);
				$('#btnAbreModalCfg').html('Visualizar meu ID');
				$('#tituloModal').html('Esse é o seu ID')
				// tutorial
				$('#video-tutorial').attr('src', 'https://www.youtube.com/embed/Oz5beUs-3Z8?rel=0&amp;controls=0&amp;showinfo=0');
				break;

			case '16': // luqro
				var idSHA1 = dados.idSHA1;
				$('#codigo-pixel').text(idSHA1);
				$('#inputPixel').val(idSHA1);
				$('#btnAbreModalCfg').html('Visualizar meu ID');
				$('#tituloModal').html('Esse é o seu ID')
				// tutorial
				$('#video-tutorial').attr('src', 'https://www.youtube.com/embed/Oz5beUs-3Z8?rel=0&amp;controls=0&amp;showinfo=0');
				break;

			case '17': // vannon
				var idSHA1 = dados.idSHA1;
				$('#codigo-pixel').text(idSHA1);
				$('#inputPixel').val(idSHA1);
				$('#btnAbreModalCfg').html('Visualizar meu ID');
				$('#tituloModal').html('Esse é o seu ID')
				// tutorial
				$('#video-tutorial').attr('src', 'https://www.youtube.com/embed/ODuJkbQCT-E?rel=0&amp;controls=0&amp;showinfo=0');
				break;

			case '18': // piraweb
				var idSHA1 = dados.idSHA1;
				$('#codigo-pixel').text(idSHA1);
				$('#inputPixel').val(idSHA1);
				$('#btnAbreModalCfg').html('Visualizar meu ID');
				$('#tituloModal').html('Esse é o seu ID')
				// tutorial
				$('#video-tutorial').attr('src', 'https://www.youtube.com/embed/Oz5beUs-3Z8?rel=0&amp;controls=0&amp;showinfo=0');
				break;

			case '19': // TRAY
				var plat = 'tray';
				// tutorial
				$('#video-tutorial').attr('src', 'https://www.youtube.com/embed/4g_nF8e8DVA?rel=0&amp;controls=0&amp;showinfo=0');
				break;

			case '20': // A2 STORE
				var plat = 'integrado';
				// tutorial
				$('#video-tutorial').attr('src', 'https://www.youtube.com/embed/4g_nF8e8DVA?rel=0&amp;controls=0&amp;showinfo=0');

			case '21': // bizcommerce
				var idSHA1 = dados.idSHA1;
				$('#codigo-pixel').text(idSHA1);
				$('#inputPixel').val(idSHA1);
				$('#btnAbreModalCfg').html('Visualizar meu ID');
				$('#tituloModal').html('Esse é o seu ID')
				// tutorial
				$('#video-tutorial').attr('src', 'https://www.youtube.com/embed/F7hDa6mc9Dk?rel=0&amp;controls=0&amp;showinfo=0');
				break;

			case '22': // ecshop 
				var idSHA1 = dados.idSHA1;
				$('#codigo-pixel').text(idSHA1);
				$('#inputPixel').val(idSHA1);
				$('#btnAbreModalCfg').html('Visualizar meu ID');
				$('#tituloModal').html('Esse é o seu ID')
				// tutorial
				$('#video-tutorial').attr('src', 'https://www.youtube.com/embed/nSgyO8OAT9M?rel=0&amp;controls=0&amp;showinfo=0');
				break;

			case '23': // nuvem shop
				var plat = 'integrado';
				// tutorial 
				$('#video-tutorial').attr('src', 'https://www.youtube.com/embed/QzfJrFbAPvc?rel=0&amp;controls=0&amp;showinfo=0');
				break;

			case '24': // rakuten
				var plat = 'rakuten';
				// tutorial 
				$('#video-tutorial').attr('src', 'https://www.youtube.com/embed/4g_nF8e8DVA?rel=0&amp;controls=0&amp;showinfo=0');
				break;

			case '25': // n2n virtual
				var idSHA1 = dados.idSHA1;
				$('#codigo-pixel').text(idSHA1);
				$('#inputPixel').val(idSHA1);
				$('#btnAbreModalCfg').html('Visualizar meu ID');
				$('#tituloModal').html('Esse é o seu ID')
				// tutorial
				$('#video-tutorial').attr('src', 'https://www.youtube.com/embed/C32dumiqbvA?rel=0&amp;controls=0&amp;showinfo=0');
				break;

			default:
				break;
			// tutorial iset
			// $('#video-tutorial').attr('src','https://www.youtube.com/embed/?rel=0&amp;controls=0&amp;showinfo=0');
		}

		// se for por script, altera os campos do pixel, vídeo, etc
		if (plat) {
			pixel =
				"<!-- Start Roi Hero Analytics Tracker -->\n" +
				"<script type='text/javascript'>\n" +
				"var rhClientId = '" + dados.idSHA1 + "';\n" +
				"(function() {\n" +
				"var _rh = document.createElement('script'); _rh.type = 'text/javascript'; _rh.async = true;\n" +
				"_rh.src = 'https://roihero.com.br/analytics/modules/base/js/roihero-tracker-" + plat + ".min.js';\n" +
				"var _rh_s = document.getElementsByTagName('script')[0]; _rh_s.parentNode.insertBefore(_rh, _rh_s);\n" +
				"})();\n" +
				"</script>\n" +
				"<!-- End Roi Hero Analytics Code -->\n";
			$('#codigo-pixel').text(pixel);
			$('#inputPixel').val(pixel);
		}

	}

	$('#btnSalvaConfig').on('click', function () {
		var desconto = $('#desconto').val().replace('%', '');
		var numeroParcelas = $('#numeroParcelas').val();
		var valorParcelas = $('#valorParcelas').val().replace('R$', '').trim();
		var site = $('#site').val();
		var corPrimaria = $('#corPrimaria').val();
		var corSecundaria = $('#corSecundaria').val();

		$.ajax({
			type: 'post',
			url: 'resource/resource_configuracoes.php',
			data:
				{
					'id': idCli,
					'op': 2,
					'desconto': desconto,
					'numeroParcelas': numeroParcelas,
					'valorParcelas': valorParcelas,
					'site': site,
					'corPrimaria': corPrimaria,
					'corSecundaria': corSecundaria
				},
			success: function (response) {
				console.log(response);
				if (response === '1') {
					toastr['success']('Suas informações foram atualizadas com sucesso');
				} else {
					toastr['error']('Não foi possível atualizar as suas informações, verique a sua conexão com a internet');
				}

			}
		});
	});

	var errorActiveTrustvox = false;
	$(document).ready(function () {
		// Função para controlar alterações no estado do componente
		$('#ativa-trustvox').change(function () {
			if (!errorActiveTrustvox) {
				bloqueiaElemento($('#cardTrustvox'));
				$.ajax({
					type: 'post',
					url: 'resource/resource_cadastra_trustvox.php',
					data: { id: idCli, enable: this.checked ? 1 : 0 },
					success: function (response) {
						if (response.status == 1) {
							toastr['success'](response.successMsg);
							errorActiveTrustvox = false;
						} else {
							toastr['error'](response.errorMsg);
							errorActiveTrustvox = true;
							$('#ativa-trustvox').prop("checked", false);
						}
						desbloqueiaElemento($('#cardTrustvox'));
					},
					error: function (a, response, e) {
						console.log(a, response, e);
						errorActiveTrustvox = true;
						desbloqueiaElemento($('#cardTrustvox'));
					}
				});
			} else {
				errorActiveTrustvox = false;
			}
		});
	});
}());
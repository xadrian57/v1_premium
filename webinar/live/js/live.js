/*
--	Autor: Getulio Monteiro Ribeiro
--	Data: 03/2018
--	Update: 13/04/2018 - Por Lucas H.
--	Desc: Script responsável por carregar e salvar os templates HTML dos clientes
--
*/
'use strict';
(function ($, Private) {
	
	Private = {
		html : {
			titleTop 	 : '.background-title-webinar .title-top',
			tittleBottom : '.background-title-webinar .title-bottom'
		},
		phrase : {
			top 	: 'Não perca nosso webinar. De segunda a sexta, às 14:00 e às 19:30!',
			bottom 	: 'Não perca!'
		}
	};

	Private.init = function () {

		Private.whatPage();
	};

	Private.whatPage = function () {

		if ($('body').attr('id') === 'live') {

			Private.checkRedirect();

		} else {

			Private.getPhrase();
		}
	};

	Private.checkRedirect = function () {

		var
			date = new Date(),
		    hour = date.getHours(),
		    min  = date.getMinutes();

		if (hour < 14 || (hour >= 15 && hour < 19) || (hour == 19 && min <25) ||hour >= 20 ) {

			window.location.href = 'index.html';
		}		
	};

	Private.getPhrase = function () {

		var
			date = new Date(),
		    hour = date.getHours(),
			min  = date.getMinutes();
			
		//Havia duas páginas do webinar: A de entrada e a do próprio vídeo. A de entrada tinha vários estágios
		// que são as mensagens comentadas abaixo. Mas por problemas de lógica do programador (aquele que vos escreve - Lucas),
		// essa página foi retirada e dela é redirecionado diretamente pra página do vídeo
		/*
		if (hour < 12 || hour >= 21 || (hour >= 15 && hour <= 18)) {

			Private.phrase.top 		= 'Não perca nosso webinar. De segunda a sexta, às 14:00 e às 19:30!';
			Private.phrase.bottom 	= 'Não perca!';
		
		} else if ((hour == 12)||( hour === 13 && min < 30)) {

			Private.phrase.top 		= 'Nosso webinar começa as 14:00! Se prepare que falta pouco!';
			Private.phrase.bottom 	= 'Se prepare que falta pouco!';
		
		} else if (hour === 13 && min <= 55) {

			Private.phrase.top 		= 'Falta pouco tempo para o nosso webinar começar! As 14:00 iremos dar inicio';
			Private.phrase.bottom 	= 'Então se prepare!';
		
		} else if (hour == 18|| hour == 19) {

			Private.phrase.top 		= 'Nosso webinar começa as 19:30! Se prepare que falta pouco!';
			Private.phrase.bottom 	= 'Se prepare que falta pouco!';
		
		} else if (hour === 19 && min <= 25) {

			Private.phrase.top 		= 'Falta pouco tempo para o nosso webinar começar! As 19:30 iremos dar inicio';
			Private.phrase.bottom 	= 'Então se prepare!';
		
		}else {

			Private.phrase.top 		= 'O nosso webinar esta em andamento!';
			Private.phrase.bottom 	= 'Você será redirecionado!';
			window.location.href 	= 'live.html';
		} */

		// as três linhas seguintes redircionam para a página do vídeo
		Private.phrase.top 		= 'O nosso webinar esta em andamento!';
		Private.phrase.bottom 	= 'Você será redirecionado!';
		window.location.href 	= 'live.html';

		$(Private.html.titleTop).html(Private.phrase.top);	
		$(Private.html.tittleBottom).html(Private.phrase.bottom.toUpperCase());
	};

	$(document).ready(function() {
		Private.init();
	});

}(jQuery, {}));
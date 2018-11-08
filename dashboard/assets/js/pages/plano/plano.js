/*
--	Autor: Eliabe
--	Data: 10/01/2018
--	Update: 01/02/2018
--	Desc: Script que retorna todos os dados do banco em um JSON
--
*/
'use strict';
window['idCli'] = $('#infsess').attr('data-cli');
window['idPlan'] = $('#infsess').attr('data-plan');

(function(){
	function consultaBanco(){
		$.ajax({
			type: 'post',
			url: 'resource/resource_meu_plano.php',
			data: {'id':idCli,'op':1},
			success: function(response){
				console.log(response);
				window['dados'] = JSON.parse(response);
				carregaPlano(dados);
			}
		});
	};

	consultaBanco();
		
	function toReais(n) {
		if (n === 'GRÁTIS' || n === 'CPA/ROI'){return 'GRÁTIS';}
		if (n === '' || n === undefined || n === null) { return '';}
        var format = new Intl.NumberFormat('pt-BR');
        n = parseFloat(n).toFixed(2);
        n = format.format(n);
        n = (n.indexOf(',') < 0) ? n+',00':n;
		return ('R$&nbsp;'+n);
	}

	function carregaPlano(dados){
		var anoExpiracao = dados.expiracao.split('-');
		console.log(anoExpiracao);
		anoExpiracao = anoExpiracao[2]+'/'+anoExpiracao[1]+'/'+anoExpiracao[0];

		dados.valor = (parseInt(dados.valor) == 0) ? 'GRÁTIS':dados.valor;
		dados.valor = (idPlan !== '42') ? 'CPA/ROI' : toReais(dados.valor);

		var nomesPlanos = {
			'0': 'TRIAL',
			'1': 'FREE',
			'2': 'STARTUP',
			'3': 'PRO',
			'4': 'ROCKET',
			'42': 'PREMIUM'
		}
		if(idPlan == 42){
			$('#valor').html('ROI');
		} else {
			$('#valor').html(dados.valor);
		}
			
		$('#expiracao').html(anoExpiracao);
		$('#views').html(dados.views);
		$('#nomePlano').html(nomesPlanos[idPlan]);
	}
}());
/*
--	Autor: Eliabe
--	Data: 10/01/2018
--	Update: 16/02/2018
--	Desc: Script que retorna todos os dados do banco em um JSON
--
*/
'use strict';
window['idCli'] = $('#infsess').attr('data-cli');

(function(){
	function consultaBanco(){
		$.ajax({
			type: 'post',
			url: 'resource/resource_faturas.php',
			data: {
				'id':idCli,
				'idSuperLogica': 87,
				'op':1
			},
			success: function(response){
				window['dados'] = JSON.parse(response);
				console.log('Faturas:');
				console.log(dados);
				carregaFaturas(dados);
			}
		});
	};

	consultaBanco();	

	function carregaFaturas(dados){
		$('#investimento').html(toReais(dados[0].investimento));
		$('#retorno').html(toReais(dados[0].retorno));
		$('#roi').html(dados[0].roi ? dados[0].roi.toFixed(2) : 0);

		var faturas = document.getElementById('historicoFaturas');		
		dados[1].forEach(function(fatura){
			switch(fatura.status){
				case 'Pago':
					faturas.innerHTML+=
					'<tr>'+
		                '<td class="text-truncate">'+fatura.valor+'</td>'+
		                '<td class="text-truncate">'+fatura.dataVenc+'</td>'+
		                '<td class="text-truncate"><span class="tag tag-default tag-success tag-lg">Pago</span></td>'+
		                '<td class="text-truncate"></td>'+
		           	'</tr>';
					break;
				case 'Pendente':
					faturas.innerHTML+=
					'<tr>'+
		                '<td class="text-truncate">'+fatura.valor+'</td>'+
		                '<td class="text-truncate">'+fatura.dataVenc+'</td>'+
		                '<td class="text-truncate"><span class="tag tag-default tag-warning tag-lg">Pendente</span></td>'+
		                '<td class="text-truncate"><a href="'+fatura.link+'" class="btn btn-info btn-md" target="_blank"> Pagar <i class="fa fa-money" aria-hidden="true"></a></td>'+
		           	'</tr>';
					break;
				case 'Vencido':
					faturas.innerHTML+=
					'<tr>'+
		                '<td class="text-truncate">'+fatura.valor+'</td>'+
		                '<td class="text-truncate">'+fatura.dataVenc+'</td>'+
		                '<td class="text-truncate"><span class="tag tag-default tag-danger tag-lg">Vencido</span></td>'+
		                '<td class="text-truncate"><a href="'+fatura.link+'" class="btn btn-info btn-md" target="_blank"> Pagar <i class="fa fa-money" aria-hidden="true"></a></td>'+
		           	'</tr>';
					break;
			}
					

		});
	}
}());
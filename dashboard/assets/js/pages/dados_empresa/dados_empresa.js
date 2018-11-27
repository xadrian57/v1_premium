/*
--  Autor: Eliabe
--  Data: 09/2017
--  Update: 08/02/2018
--  Desc: Script que retorna todos os dados do perfil do cliente em um JSON
--
*/
'use strict';
(function(){
	window['idCli'] = $('#infsess').attr('data-cli');

	function consultaBanco(){
		$.ajax({
			type: 'post',
			url: 'resource/resource_dados_empresa.php',
			data: {'id':idCli,'op':1},
			success: function(response){
				console.log(response);
				window['dados'] = JSON.parse(response);
				carregaDados(dados);
			}
		});
	};

	consultaBanco();

	/* =======================================================
					CARREGA DADOS NOS INPUTS
	=========================================================*/
	function carregaDados(dados){
		$('#razaoSocial').val(dados.razaoSocial);		
		$('#cnpj').val(dados.cnpj);
		$('#inscricaoEstadual').val(dados.inscricaoEstadual);
		$('#rua').val(dados.rua);
		$('#numero').val(dados.numero);
		$('#complemento').val(dados.complemento);
		$('#bairro').val(dados.bairro);
		$('#CEP').val(dados.CEP);
		$('#cidade').val(dados.cidade);
		$('#estado').val(dados.estado);
		$('#site').val(dados.site);
		$('#telefoneAdministrativo').val(dados.telefoneAdministrativo);
		$('#email').val(dados.email);
		$('#telefoneFinanceiro').val(dados.telefoneFinanceiro);
		$('#site').val(dados.site);
		$('#segmento').val(dados.segmento);
		$('#plataforma').val(dados.plataforma);
		$('#skype').val(dados.skype);
		$('#emailFinanceiro').val(dados.emailFinanceiro);

		$('#razaoSocial').val(dados.razaoSocial);
		$('#cnpj').val(dados.cnpj);
		$('#inscricaoEstadual').val(dados.inscricaoEstadual);
	}

	$('.btnEdita').on('click',function(){
		$('.btnEdita').attr('disabled','true');
		$('#formDadosCliente input').removeAttr('disabled');
		$('.btnSalva').removeAttr('disabled');
		$('.btnCancela').removeAttr('disabled');
	});

	$('.btnCancela').on('click',function(){
		$('.btnEdita').removeAttr('disabled');
		$('#formDadosCliente input').attr('disabled','true');
		$('.btnSalva').attr('disabled','true');
		$('.btnCancela').attr('disabled','true');
	});

	$('#formDadosCliente').on('submit',function(){
		bloqueiaTela(); // bloqueia tela
		var razaoSocial = $('#razaoSocial').val();
		var cnpj = $('#cnpj').val();
		var inscricaoEstadual = $('#inscricaoEstadual').val();
		var rua = $('#rua').val();
		var numero = $('#numero').val();
		var complemento = $('#complemento').val();
		var bairro = $('#bairro').val();
		var CEP = $('#CEP').val();
		var cidade = $('#cidade').val();
		var estado = $('#estado').val();
		var site = $('#site').val();
		var telefoneAdministrativo = $('#telefoneAdministrativo').val();
		var email = $('#email').val();
		var emailFinanceiro = $('#emailFinanceiro').val();
		var telefoneFinanceiro = $('#telefoneFinanceiro').val();
		var segmento = $('#segmento').val();
		var site = $('#site').val();
		var skype = $('#skype').val();

		$.ajax({
			type: 'post',
			url: 'resource/resource_dados_empresa.php',
			data: {
				'id':idCli,
				'op':2,
				'razaoSocial':razaoSocial,
				'cnpj':cnpj,
				'inscricaoEstadual':inscricaoEstadual,
				'rua':rua,
				'numero':numero,
				'complemento':complemento,
				'bairro':bairro,
				'CEP':CEP,
				'cidade':cidade,
				'estado':estado,
				'site':site,
				'telefoneAdministrativo':telefoneAdministrativo,
				'email':email,
				'emailFinanceiro':emailFinanceiro,
				'telefoneFinanceiro':telefoneFinanceiro,
				'site':site,
				'segmento':segmento,
				'skype':skype
			},

			success: function(response){
				console.log(response);
				if (response === '1'){
					toastr['success']('Suas informações foram atualizadas com sucesso');
				} else {
					toastr['error']('Não foi possível atualizar as suas informações, tente novamente mais tarde');
				}
				
				$('input').attr('disabled','true');
				$('.btnSalva').attr('disabled','true');
				$('.btnCancela').attr('disabled','true');
				$('.btnEdita').removeAttr('disabled');

				desbloqueiaTela(); // desbloqueia tela
			}
		});
		return false;
	});
}());
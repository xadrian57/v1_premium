/*
--	Autor: Eliabe
--	Data: 01/2018
--	Update: 19/02/2018
--	Desc: Script cadastro plano
--
*/
// FUNCAO QUE TRANSFORMA A ARRAY DE OBJ EM UM UNICO OBJ
	serializedFormArrayToOBJ = function(arr){
		var newObj = {};
		arr.forEach(function(obj){
			newObj[obj.name] = obj.value;
		});
		return newObj;
	}



	// QUANDO O USUÁRIO SELECIONA OUTRA MODALIDADE DE PAGAMENTO: MENSAL/TRIMESTRAL/SEMESTRAL
	$('.modalidade-pill').click(function(){
		var modalidade = $(this).attr('data-modalidade');
		$('.modalidade-pagamento').html(modalidade);

		function desbloqueiaPlanoStartup(){
			desbloqueiaElemento($('#cardPlanoStartup'));
			$('#cardPlanoStartup').css('pointer-events','initial');
		}

		switch(modalidade){
			case 'Mensais':
				resetaFormaPagamento(); // sempre reseta a forma de pagamento
				$('.preco-pro1').html('R$ 39,00');
				$('.preco-pro2').html('R$ 99,00');
				$('.preco-pro3').html('R$ 199,99');
				$('.container-plano.pro1')[0].setAttribute('data-price','R$ 39,00');
				$('.container-plano.pro2')[0].setAttribute('data-price','R$ 99,00');
				//$('.container-plano.pro3')[0].setAttribute('data-price','R$ 199,99');
				$('#_t_').val('30'); // TEMPO
				$('.texto-desconto-plano').css('opacity','0');
				break;
			case 'Anuais':
				resetaFormaPagamento(); // sempre reseta a forma de pagamento
				$('.preco-pro1').html('R$ 374,40');
				$('.preco-pro2').html('R$ 950,40');
				$('.preco-pro3').html('R$ 1.910,40');
				$('.container-plano.pro1')[0].setAttribute('data-price','R$ 374,40');
				$('.container-plano.pro2')[0].setAttribute('data-price','R$ 950,40');
				//$('.container-plano.pro3')[0].setAttribute('data-price','R$ 1.910,40');
				$('#_t_').val('360'); // TEMPO
				// DESBLOQUEIA PLANO STARTUP
				desbloqueiaPlanoStartup();
				break;
		}
	});

	// QUANDO O USUÁRIO SELECIONA O RADIO BUTTON
	var containerRadio = document.getElementsByClassName('eliabe-radio');
	for (var i = 0; i < containerRadio.length; i++) {
		var radio = containerRadio[i].getElementsByTagName('input')[0];
		radio.addEventListener('click',function(){
			// removendo ativo
			var containers = document.getElementsByClassName('container-plano');
			for (var x = 0; x < containers.length;x++){
				var container = containers[x];
				if (container.classList.contains('active')){
					container.classList.remove('active');
				}
			}
			// opacidade
			var containers = document.getElementsByClassName('container-plano');
			for (var x = 0; x < containers.length;x++){
				var container = containers[x];
				if (!container.classList.contains('active')){
					container.style.opacity = '.6';
				}
			}

			$(this).parent().parent().parent().addClass('active');
			$(this).parent().parent().parent().css('opacity','1');

			var views = $(this).attr('data-view');
			var valor = $('.container-plano.active')[0].getAttribute('data-price');

			$('#_p_').val(this.value);
			$('#_v_').val(valor);  // PREÇO
			window['valorSemDesconto'] = $('#_v_').val();
			$('#_vw_').val(views); // VIEWS

			// MOSTRA BOTAO AVANCAR
			$('#plano-next').css('padding-top','20px');
			$('#plano-next').css('height','70px');
			window.location.href = '#plano-next';
		});
	}


	// $('.eliabe-radio input[type="radio"]').click(function(){
	// 	$('.container-plano').removeClass('active');
	// 	$('.container-plano').not('.active').css('opacity','.6');
	// 	$(this).parent().parent().parent().addClass('active');
	// 	$(this).parent().parent().parent().css('opacity','1');

	// 	var views = $(this).attr('data-view');
	// 	var valor = $('.container-plano.active')[0].getAttribute('data-price');

	// 	$('#_p_').val(this.value);
	// 	$('#_v_').val(valor);  // PREÇO
	// 	$('#_vw_').val(views); // VIEWS

	// 	// MOSTRA BOTAO AVANCAR
	// 	$('#plano-next').css('padding-top','20px');
	// 	$('#plano-next').css('height','70px');
	// 	window.location.href = '#plano-next';
	// });

	// QUANDO CLICA EM CIMA DO PLANO TBM SELECIONA
	var containers = document.getElementsByClassName('container-plano');
	for (var i = 0; i < containers.length; i++) {
		containers[i].addEventListener('click',function(){
			$(this).find('.container-radio-plano .eliabe-radio input').click();
		});
	}

	// QUANDO AVANÇA TELA
	$('.btn-avanca-passo').click(function(){
		if ($('#modalPlanos input[type="radio"]:checked').length !== 0) { // SÓ AVANÇA SE ALGUM PLANO FOR ESCOLHIDO
			var precoPlano = $('.container-plano.active')[0].getAttribute('data-price');
			var nomePlano = $('#modalPlanos input[type="radio"]:checked')[0].getAttribute('data-name');
			var modalidade = $('.modalidade-pill.active').attr('data-modalidade').toLowerCase();
			// SE FOR PREMIUM, MOSTRA OUTRA TELA
			if (precoPlano === 'CPA') {
				// ARASTA PRIMEIRA TELA PRA ESQUERDA
				$('#sectionEscolhePlano').addClass('left');
				$('#sectionEscolhePlano').removeClass('active');

				// ARRASTA SEGUNDA TELA PRA DIREITA
				$('#sectionPremium').addClass('active');
			} else {
				// tela que esta mostrando no momento
				var telaAtiva = $('#modalPlanos section.active');
				// ARRASTA TELA ATIVA PRA ESQUERDA
				$(telaAtiva).addClass('left');
				$(telaAtiva).removeClass('active');

				// descobrindo pra qual tela voltar
				switch(telaAtiva[0].id){
					// CASO ESTEJA NA PRIMEIRA TELA
					case 'sectionEscolhePlano':
						// ARRASTA SEGUNDA TELA PRA DIREITA
						$('#sectionTela2').addClass('active');
						// ALTERA O TEXTO QUE MOSTRA QUAL PLANO O USUÁRIO ESCOLHEU, NA SEGUNDA TELA
						$('.valor-plano-selecionado').html('<b>'+nomePlano+', '+precoPlano+' '+modalidade+'</b>');
						// INPUT QUE ARMAZENA O PLANO
						var plano = $('#modalPlanos input[type="radio"]:checked')[0].value;
						$('#_p_').val(plano);
						break;
					// CASO ESTEJA NA SEGUNDA TELA
					case 'sectionTela2':
						// ARRASTA TERCEIRA TELA PRA DIREITA
						$('#sectionTela3').addClass('active');
						break;
					// CASO ESTEJA NA TERCEIRA TELA
					case 'sectionTela3':
						// ARRASTA TERCEIRA TELA PRA DIREITA
						$('#sectionTela4').addClass('active');
						break;
				}
				//$('#modalPlanos .modal-body').css('height','65vh'); // alterando tamanho modal

			}
		}
	});

	// QUANDO CLICA EM VOLTAR PRA ESCOLHER OUTRO PLANO
	$('.btn-volta-tela-plano').click(function(){
		resetaFormaPagamento();

		// ARASTA PRIMEIRA TELA PRA ESQUERDA
		$('#sectionEscolhePlano').removeAttr('style');

		// tela que esta mostrando no momento
		var
		telaAtiva = $('#modalPlanos section.active'),
		id = telaAtiva[0].id;

		// ARRASTA SEGUNDA TELA PRA DIREITA
		$(telaAtiva).addClass('left');
		$(telaAtiva).removeClass('active');

		// descobrindo pra qual tela voltar
		switch(id){
			case 'sectionTela4':
				$('#sectionTela3').addClass('active');
				break;
			case 'sectionTela3':
				$('#sectionTela2').addClass('active');
				break;
			default:
				$('#sectionEscolhePlano').addClass('active');
				break;
		}

		//$('#modalPlanos .modal-body').css('height','auto'); // alterando tamanho modal
	});

	//QUANDO PREENCHE O CAMPO CEP, AUTOCOMPLETE DOS DEMAIS CAMPOS
	$(document).ready(function(){
		$('#cep').blur(function(){
			var cep = $('#cep').val() || '';
			if(!cep){
				return
			}
			var url = 'https://viacep.com.br/ws/' + cep + '/json';
			$.getJSON(url, function(data){
				if("error" in data){
					return
				}
				else
				{
					$('#rua').val(data.logradouro);
					$('#bairro').val(data.bairro);
					$('#localidade').val(data.localidade);
					$('#estado').val(data.uf);
				}

			});
		});
	});

	// PAGAMENTO EM CARTAO
	$('#formaPagamento').change(function(){
		if (this.value === 'boleto'){
			$('#selecionaBandeira').hide();
		} else {
			$('#selecionaBandeira').show();
			if($('#_t_').val() === '360'){
				var valor = $('#_v_').val();
				valor = parseFloat(valor.replace(',','.').replace('R$',''));
				parcelamento = {
					'1x': valor.toFixed(2).toString().replace('.',','),
					'2x': (valor / 2).toFixed(2).toString().replace('.',','),
					'3x': (valor / 3).toFixed(2).toString().replace('.',',')
				}
				$('#selecionaParcelamento').html(
					'<label>Escolha a forma de parcelamento:</label>'+
					'<div class="form-group">'+
					    '<label class="bold">'+
					      '<input type="radio" id="parcelamento1x" name="quantidadeParcelas" value="1"> <b>1x de R$ '+parcelamento['1x']+'</b>'+
					    '</label>'+
					'</div>'+
					'<div class="form-group">'+
						'<label class="bold">'+
					      '<input type="radio" id="parcelamento2x" name="quantidadeParcelas" value="2"> <b>2x de R$ '+parcelamento['2x']+'</b>'+
					    '</label>'+
					'</div>'+
					'<div class="form-group">'+
						'<label class="bold">'+
					      '<input type="radio" id="parcelamento3x" name="quantidadeParcelas" value="3"> <b>3x de R$ '+parcelamento['3x']+'</b>'+
					    '</label>'+
					'</div>'
				);
			} else {
				$('#selecionaParcelamento').html('');
			}

		}
	});

	// valida cupom
	$('#btnValidaCupom').click(validaCupom);
	function validaCupom() {
		var cupom = $('#inputCupom').val();
		bloqueiaElemento($('#btnValidaCupom'));
		$.ajax({
			type: 'post',
			url: 'resource/resource_checa_cupom.php',
			data: {'cupom':cupom,'plano': $('#_p_').val(), 'tempo': $('#_t_').val(), 'formaPagamento': $('#formaPagamento').val()},
			success: function(response) {
				console.log(response);
				try {
					var r = JSON.parse(response);
					if(r.status == 1){
						toastr['success'](r.msg);
						$('#btnValidaCupom').off('click');
						//$('#inputCupom').attr('disabled','true');
						$('#btnValidaCupom').attr('disabled','true');
						$('#btnValidaCupom').addClass('disabled');
						$('#btnValidaCupom').addClass('btn-success');
						$('#btnValidaCupom').removeClass('btn-primary');
						$('#btnValidaCupom').text('Cupom validado');

						// altera preços
						var planos = {
							'0':'TRIAL',
							'1':'FREE',
							'2':'STARTUP',
							'3':'PRO',
							'4':'ROCKET',
							'42':'PREMIUM',
						}

						var plano = planos[ $('#_p_').val() ];
						$('#_v_').val(toReais(r.valor));

						$('.valor-plano-selecionado').html('<b>'+plano+', '+$('#_v_').val()+'</b>');

						// altera valor parcelamento
						if ($('#selecionaParcelamento').html() !== ''){var
							valor = parseFloat($('#_v_').val().replace('R$','')),

							parcelamento = {
								'1x': valor.toFixed(2).toString().replace('.',','),
								'2x': (valor / 2).toFixed(2).toString().replace('.',','),
								'3x': (valor / 3).toFixed(2).toString().replace('.',',')
							}
							$('#selecionaParcelamento').html(
								'<label>Escolha a forma de parcelamento:</label>'+
								'<div class="form-group">'+
								    '<label class="bold">'+
								      '<input type="radio" id="parcelamento1x" name="quantidadeParcelas" value="1"> <b>1x de R$ '+parcelamento['1x']+'</b>'+
								    '</label>'+
								'</div>'+
								'<div class="form-group">'+
									'<label class="bold">'+
								      '<input type="radio" id="parcelamento2x" name="quantidadeParcelas" value="2"> <b>2x de R$ '+parcelamento['2x']+'</b>'+
								    '</label>'+
								'</div>'+
								'<div class="form-group">'+
									'<label class="bold">'+
								      '<input type="radio" id="parcelamento3x" name="quantidadeParcelas" value="3"> <b>3x de R$ '+parcelamento['3x']+'</b>'+
								    '</label>'+
								'</div>'
							);
						}



					} else {
						toastr['error'](r.msg);
					}
				} catch (e) {
					console.log(e);
					toastr['error']('Algo deu errado, tente novamente mais tarde ou entre em contato com o nosso suporte.');
				}

				desbloqueiaElemento($('#btnValidaCupom'));
			}
		});
	}

	function resetaFormaPagamento(){
		$('#formaPagamento').val('boleto');
		$('#selecionaBandeira').hide();
		$('#selecionaParcelamento').html('');

		// reseta Cupom
		$('#inputCupom').val('');
		$('#btnValidaCupom').off('click');
		$('#btnValidaCupom').click(validaCupom);
		$('#btnValidaCupom').removeClass('disabled');
		$('#btnValidaCupom').removeClass('btn-success');
		$('#btnValidaCupom').addClass('btn-primary');
		$('#btnValidaCupom').text('Validar cupom');
		$('#btnValidaCupom').removeAttr('disabled');
		//$('#inputCupom').removeAttr('disabled');

		var planos = {
			'0':'TRIAL',
			'1':'FREE',
			'2':'STARTUP',
			'3':'PRO',
			'4':'ROCKET',
			'42':'PREMIUM',
		}
		var plano = planos[ $('#_p_').val() ];
		$('#_v_').val(window['valorSemDesconto']);
		$('.valor-plano-selecionado').html('<b>'+plano+', '+window['valorSemDesconto']+'</b>');
	}

	 function validaCampos()
	 {
	 	var mensagem="";
			if (!(document.getElementById("telCadastro").value))
			{
				document.getElementById("telCadastro").style.borderColor = "red";
				mensagem = mensagem+"<br>Telefone" ;
			}
			else
			{
				document.getElementById("telCadastro").style.borderColor = "";
			}
			if (!(document.getElementById("nomeEmpresa").value))
			{
				document.getElementById("nomeEmpresa").style.borderColor = "red";
				mensagem = mensagem+"<br>Nome da Empresa" ;
			}
			else
			{
				document.getElementById("nomeEmpresa").style.borderColor = "";
			}
			if (!(document.getElementById("nomeResponsavel").value))
			{
				document.getElementById("nomeResponsavel").style.borderColor = "red";
				mensagem = mensagem+"<br>Responsável Roi Hero" ;
			}
			else
			{
				document.getElementById("nomeResponsavel").style.borderColor = "";
			}
			if (!(document.getElementById("cnpj").value))
			{
				document.getElementById("cnpj").style.borderColor = "red";
				mensagem = mensagem+"<br>CNPJ" ;
			}
			else
			{
				document.getElementById("cnpj").style.borderColor = "";
			}
			if (!(document.getElementById("inscricaoEstadual").value))
			{
				document.getElementById("inscricaoEstadual").style.borderColor = "red";
				mensagem = mensagem+"<br>Inscrição Estadual" ;
			}
			else
			{
				document.getElementById("inscricaoEstadual").style.borderColor = "";
			}
			if (!(document.getElementById("emailResponsavel").value))
			{
				document.getElementById("emailResponsavel").style.borderColor = "red";
				mensagem = mensagem+"<br>Email do Responsável" ;
			}
			else
			{
				document.getElementById("emailResponsavel").style.borderColor = "";
			}
			if (!(document.getElementById("emailFinanceiro").value))
			{
				document.getElementById("emailFinanceiro").style.borderColor = "red";
				mensagem = mensagem+"<br>Email Financeiro" ;
			}
			else
			{
				document.getElementById("emailFinanceiro").style.borderColor = "";
			}
			if (!(document.getElementById("cep").value))
			{
				document.getElementById("cep").style.borderColor = "red";
				mensagem = mensagem+"<br>CEP" ;
			}
			else
			{
				document.getElementById("cep").style.borderColor = "";
			}
			if (!(document.getElementById("rua").value))
			{
				document.getElementById("rua").style.borderColor = "red";
				mensagem = mensagem+"<br>Rua" ;
			}
			else
			{
				document.getElementById("rua").style.borderColor = "";
			}
			if (!(document.getElementById("numero").value))
			{
				document.getElementById("numero").style.borderColor = "red";
				mensagem = mensagem+"<br>Número" ;
			}
			else
			{
				document.getElementById("numero").style.borderColor = "";
			}
			if (!(document.getElementById("bairro").value))
			{
				document.getElementById("bairro").style.borderColor = "red";
				mensagem = mensagem+"<br>Bairro" ;
			}
			else
			{
				document.getElementById("bairro").style.borderColor = "";
			}
			if (!(document.getElementById("localidade").value))
			{
				document.getElementById("localidade").style.borderColor = "red";
				mensagem = mensagem+"<br>Cidade" ;
			}
			else
			{
				document.getElementById("localidade").style.borderColor = "";
			}
			if ((document.getElementById("estado").value) == 0)
			{
				document.getElementById("estado").style.borderColor = "red";
				mensagem = mensagem+"<br>Estado" ;
			}
			else
			{
				document.getElementById("estado").style.borderColor = "";
			}

			return mensagem;
	 }


	// QUANDO CLICA EM FINALIZAR
	$('#btnFinalizaCadPlano').click(function(){
		var dados = $('#formCadPlano').serializeArray();

		// CASO N TENHA PREENCHIDO TODOS OS CAMPOS, N CONTINUA
		if (
		dados.some(function(d){
			if (d.name == 'cupom'){
				return false;
			} else {
				return d.value.trim() === '';
			}
		}))
		{
			var mensagem = validaCampos();

			toastr['error']("Por favor preencha o(s) campo(s) abaixo : "+mensagem);
			return false;
		} else {
			dados = serializedFormArrayToOBJ(dados);
			dados.idCli = idCli;
			bloqueiaElemento($('#modalPlanos .modal-content'));
			$.ajax({
				type: 'post',
				url: 'resource/resource_cadastro_plano.php',
				data: dados,
				success: function(response){
					desbloqueiaElemento(('#modalPlanos .modal-content'));
					var resposta = JSON.parse(response);
					console.log('resposta-----');
					console.log(resposta);
					var formaPagamento = $('#formaPagamento').val();
					if (resposta.status == '0'){
						toastr['error'](resposta.msg);
					} else {

						if (formaPagamento === 'boleto'){
							// visualizar boleto
							$('#btn-fatura').attr('href',resposta.msg);
							// ESCONDE MODAL PLANOS
							$('#modalPlanos').modal('hide');
							$('#modalParabens').modal('show');
							$('#modalPlanos').html('');
						} else {
							window.location.href = resposta.msg;
						}
						// remove botao
						$('#menu-item-contratar').remove();
					}
				}
			});
		}
	});

	// QUANDO CLICA EM FINALIZAR - ENTERPRISE
	$('#btnFinalizaCadPlanoPremium').click(function(){
		var nome = $('#nome-cad-vip').val();
		var email = $('#email-cad-vip').val();
		var msg = $('#msg-cad-vip').val();

		if (email === '' || nome === ''){
			toastr['error']('Preencha todos os campos para finalizar');
		}
		else {
			bloqueiaElemento($('#modalPlanos .modal-content'));
			$.ajax({
				type: 'post',
				url: 'resource/resource_cadastro_plano.php',
				data: {nomeResponsavel:nome,emailResponsavel:email,mensagem:msg},
				success: function(response){
					desbloqueiaElemento(('#modalPlanos .modal-content'));
					console.log(response);
					$('#modalPlanos').modal('hide');
					$('#modalPlanos').html('');
					$('#modalParabensVip').modal('show');
				}
			});
		}
	});


	// QUANDO CLICA NO BOTAO DO MENU, MOSTRA MODAL E SELECIONA A OPCAO TRIMESTRAL ( FIZ A PARTE DO MODAL POR AQUI PQ POR ALGUM MOTIVO N TA ABRINDO PELO DATA- DO JQUERY)
	$('#menu-item-contratar').click(function(){
		$('#modalPlanos').modal('show');
	});

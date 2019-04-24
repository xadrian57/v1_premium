'use strict'
$(document).ready(function() {

	var widgets = {
		init: function ()
		{
			$.ajax( {
				type: 'POST',
				url: 'resource/resource_widget_edit.php',
				data: { 'idCli': idCli, 'op': 10 },
				success: function ( result )
				{
					var data = JSON.parse(result)

					widgets.loadRecBoleto(data.boleto);
					widgets.loadRecCarrinho(data.carrinho);

					widgets.toggleSwitches();

					widgets.botoesEditar();
					widgets.botoesSalvar();

					window['diasVencBoleto'] = data.diasVencBoleto;
					
				}
			})
		
		},

		loadRecBoleto: function(wids) {
			var $list = document.getElementById('widgetsBoleto');
			wids.innerHTML = "";
			wids.forEach(function (wid) {
				var ativo = (wid.WID_status === '1') ? 'checked' : '';

				var sw = '<input type="checkbox" class="switch pull-right" data-off-label="desativar" data-on-label="ativar" data-switch-always ' + ativo + '/>'

				// lembrete de boleto
				if (wid.WID_inteligencia == 45) {
					ativo = (wid.CONF_lembrete_boleto == 1 && wid.CMAIL_status == 1) ? 'checked' : '';
					sw = '<input type="checkbox" class="switch pull-right lembrete-boleto" data-off-label="desativar" data-on-label="ativar" data-switch-always ' + ativo + '/>';
				}

				$list.innerHTML = $list.innerHTML +
					'<li class="list-group-item" wid-id="' + wid.WID_id + '"><span>' + wid.WID_nome + '</span>' +
					'<div style="width: auto;display: inline-block;position:relative;bottom: 7px;float:right;">' +
					'<!-- <button class="btn btn-danger pull-right" data-delete-wid=' + wid.WID_id + '><i class="ft-x"></i> Deletar</button> -->' +
					'<button class="btn btn-info pull-right mr-1 ml-1 btn-edita-wid"><i class="icon-pencil"></i> Editar</button>' +
					sw +
					'</div>' +
					'</li>';
			});
		},

		loadRecCarrinho: function(wids) {
			var $list = document.getElementById('widgetsCarrinho');
			$list.innerHTML = "";
			wids.forEach(function (wid) {
				var ativo = (wid.WID_status === '1') ? 'checked' : '';

				$list.innerHTML = $list.innerHTML +
					'<li class="list-group-item" wid-id="' + wid.WID_id + '"><span>' + wid.WID_nome + '</span>' +
					'<div style="width: auto;display: inline-block;position:relative;bottom: 7px;float:right;">' +
					'<!-- <button class="btn btn-danger pull-right" data-delete-wid=' + wid.WID_id + '><i class="ft-x"></i> Deletar</button> -->' +
					'<button class="btn btn-info pull-right mr-1 ml-1 btn-edita-wid"><i class="icon-pencil"></i> Editar</button>' +
					'<input type="checkbox" class="switch pull-right" data-off-label="desativar" data-on-label="ativar" data-switch-always ' + ativo + '/>' +
					'</div>' +
					'</li>';
			});
		},

		toggleSwitches: function () {
			// SWITCHS
			'use strict';

			/*  Toggle Starts   */
			$('.switch:checkbox').checkboxpicker();

			$('#switch12').checkboxpicker({
				html: true,
				offLabel: '<span class="icon-remove">',
				onLabel: '<span class="icon-ok">'
			});

			// Switchery
			var i = 0;
			if (Array.prototype.forEach) {
				var elems = $('.switchery');
				$.each(elems, function (key, value) {
					var $size = "", $color = "", $sizeClass = "", $colorCode = "";
					$size = $(this).data('size');
					var $sizes = {
						'lg': "large",
						'sm': "small",
						'xs': "xsmall"
					};
					if ($(this).data('size') !== undefined) {
						$sizeClass = "switchery switchery-" + $sizes[$size];
					}
					else {
						$sizeClass = "switchery";
					}

					$color = $(this).data('color');
					var $colors = {
						'primary': "#967ADC",
						'success': "#37BC9B",
						'danger': "#DA4453",
						'warning': "#F6BB42",
						'info': "#3BAFDA"
					};
					if ($color !== undefined) {
						$colorCode = $colors[$color];
					}
					else {
						$colorCode = "#37BC9B";
					}

					var switchery = new Switchery($(this)[0], { className: $sizeClass, color: $colorCode });
				});
			} else {
				var elems1 = document.querySelectorAll('.switchery');

				for (i = 0; i < elems1.length; i++) {
					var $size = elems1[i].data('size');
					var $color = elems1[i].data('color');
					var switchery = new Switchery(elems1[i], { color: '#37BC9B' });
				}
			}

			$('.switch').change(function () {
				var op = 0;
				if (this.classList.contains('lembrete-boleto')) {
					op = 12; // desativa lembrete de boleto
				} else {
					op = 4;
				}
				var idWid = this.parentElement.parentElement.getAttribute('wid-id');
				var val = this.checked;
				$.ajax({
					type: 'POST',
					url: 'resource/resource_widget_edit.php',
					data: { 'idWid': idWid, 'val': val, 'op': op },
					success: function (result) {
					}
				});
			});

		},

		botoesEditar: function () {
			// BOTOES EDITAR WIDGET
			$('.btn-edita-wid').off('click');
			$('.btn-edita-wid').on('click', function () {
				var id = this.parentElement.parentElement.getAttribute('wid-id');
				var form = document.getElementById('campos-wid-edit');
				form.setAttribute('id-wid', id);
	
				$.ajax({
					type: 'POST',
					url: 'resource/resource_widget_edit.php',
					data: { 'idCli': idCli, 'op': 2, idWid: id },
					success: function (result) {
						$('#modalEditarWidget').modal('show');
						var widget = JSON.parse(result);
						console.log(widget);
						$('#nomeWidget').val(widget.WID_nome);
						$('#tituloWidget').val(widget.WID_texto);
						$('#utmWidget').val(widget.WID_utm);
						$('#cupomWidget').val(widget.WID_cupom);
						$('#rhIdWid').html(widget.WID_id);
	
						
						var intels = {
							44: "Recuperação de Carrinho On-site",
							45: "Lembrete de Boleto"
						};
	
						document.getElementById("spec-inteligencia-modal-edit").innerText = intels[widget.WID_inteligencia];
	
						if (widget.WID_formato == 6) {
							document.getElementById("spec-inteligencia-modal-edit").innerText = "Oferta Limitada";
						}
	
						widget.WID_hide = widget.WID_hide ? widget.WID_hide.split(',') : '';
						widget.WID_show = widget.WID_show ? widget.WID_show.split(',') : '';
	
						// campos adicionais
						var camposAdicionais = document.getElementById('widedit-opcoes-adicionais');
						camposAdicionais.innerHTML = '<h4>Configurações Específicas</h4>';
					
						$('#tituloPromocionalLabel').html('Título Promocional');
						$('#inputCupom').hide();
						$('#container-configuracoes').show();
						switch(widget.WID_inteligencia) {
							case '44': // rec carrinho onsite
								$('#inputCupom').show();
								camposAdicionais.innerHTML +=
								'<div id="containerAlteraImagemForm" class="col-md-6 pd-l-0">' +
								'<label>Imagem Atual:</label>' +
								'<div class="form-control">' +
								'<abbr title="Esta é a foto que vai aparecer no banner do overlay." class="info-abbr">' +
								'<i class="icon-info"></i>' +
								'</abbr>' +
								'<div class="rh-input-icon-right">' +
								'<div class="media">' +
								'<div class="media-left">' +
								'<img class="img-banner-small" width="100px" src="..\/widget\/images\/overlay\/' + widget.WID_banner + '">' +
								'</div>' +
								'<div class="media-body">' +
								'<div class="form-group">' +
								'<button class="btn btn-info" id="btnViewBanner" data-target="..\/widget\/images\/overlay\/' + widget.WID_banner + '">Visualizar <i class="ft-eye"></i></button>' +
								'</div>' +
								'<div class="form-group">' +
								'<button class="btn btn-primary" id="btnEditBanner">Alterar <i class="ft-upload"></i></button>' +
								'</div>' +
								'</div>' +
								'</div>' +
								'</div>' +
								'</div>' +
								'<div class="form-group">' +
								'<div class="rh-input-icon-right">' +
								'<input id="imagemBanner" name="imagemBanner" type="file" accept="image/x-png,image/gif,image/jpeg" hidden>' +
								'</div>' +
								'</div>' +
								'</div>' +
								'</div>';
								break;
							case '45': // lembrete boleto
								$('#container-configuracoes').hide();

								$('#tituloPromocionalLabel').html('Título do E-mail');
								camposAdicionais.innerHTML +=
								'<div id="containerAlteraImagemForm" class="col-md-6 pd-l-0">' +
								'<label>Imagem Atual:</label>' +
								'<div class="form-control">' +
								'<abbr title="Esta é a foto que vai aparecer no banner do overlay." class="info-abbr">' +
								'<i class="icon-info"></i>' +
								'</abbr>' +
								'<div class="rh-input-icon-right">' +
								'<div class="media">' +
								'<div class="media-left">' +
								'<img class="img-banner-small" width="100px" src="..\/widget\/images\/overlay\/' + widget.CMAIL_banner + '">' +
								'</div>' +
								'<div class="media-body">' +
								'<div class="form-group">' +
								'<button class="btn btn-info" id="btnViewBanner" data-target="..\/widget\/images\/overlay\/' + widget.CMAIL_banner + '">Visualizar <i class="ft-eye"></i></button>' +
								'</div>' +
								'<div class="form-group">' +
								'<button class="btn btn-primary" id="btnEditBanner">Alterar <i class="ft-upload"></i></button>' +
								'</div>' +
								'</div>' +
								'</div>' +
								'</div>' +
								'</div>' +
								'<div class="form-group">' +
								'<div class="rh-input-icon-right">' +
								'<input id="imagemBanner" name="imagemBanner" type="file" accept="image/x-png,image/gif,image/jpeg" hidden>' +
								'</div>' +
								'</div>' +
								'</div>' +
								'</div>';

								var optionList = (widget.WID_dias == 1) ? '<option value="1" selected>1 dia após a cobrança</option>':'<option value="1">1 dia após a cobrança</option>';

								for (var i = 2; i < window['diasVencBoleto']; i++) {
									var selected = (widget.WID_dias == i) ? 'selected' : '';
									optionList += '<option value="'+i+'" '+selected+'>'+i+' dias após a cobrança</option>';
								}
								
								camposAdicionais.innerHTML +=
								'<div class="col-md-6">'+
									'<div class="form-group">'+
										'<label>Dias para o Vencimento do Boleto</label>'+
										'<div class="rh-input-icon-right">'+
											'<select name="lembreteBoleto" class="form-control" value="'+widget.WID_dias+'">'+
												optionList+
											'</select>'+
										'</div>'+
									'</div>'+
									'<div class="form-group">'+
										'<label>Dias para o Vencimento do Boleto</label>'+
										'<div class="rh-input-icon-right">'+
											'<input id="diasBoletoVenc" name="diasBoleto" class="form-control" type="number" min="1" value='+window['diasVencBoleto']+'>'+
													'<abbr style="position: relative;right: -34px;" title="Essa é quantidade de dias até o boleto vencer na sua loja" class="info-abbr">'+
															'<i class="icon-info"></i>'+
													'</abbr>'+
											'</div>'+
									'</div>'+
								'</div>';


								break;
						}
	
						// ver imagem banner
						if ($('#btnViewBanner').length > 0) {
							rhPhoto($('#btnViewBanner'));
	
							$('#btnEditBanner').click(function () {
								$('#imagemBanner')[0].focus();
								$('#imagemBanner')[0].click();
							});
	
							$('#btnEditBannerLojaLateral').click(function () {
								$('#imagemBannerLojaLateral')[0].focus();
								$('#imagemBannerLojaLateral')[0].click();
							});
	
	
							$('#btnEditThumb').click(function () {
								$('#thumbnail')[0].focus();
								$('#thumbnail')[0].click();
							});
	
	
							$('#imagemBanner').change(function () {
	
								bloqueiaElemento($('#containerAlteraImagemForm')[0]);
	
								var file = this.files[0];
								var reader = new FileReader();
								reader.onload = function (f) {
	
									// pega dimensoes da imagem
									var img = new Image;
									img.src = f.target.result;
	
									img.onload = function () {
										if (file.type !== 'image/png' && file.type !== 'image/jpg' && file.type !== 'image/jpeg' && file.type !== 'image/gif') {
											toastr['error']('O arquivo que você tentou enviar não é uma imagem.');
											$('#imagemBanner').val('');
										}
										// rec cart on site
										else if (widget.WID_inteligencia == 44 && img.width != 680 && img.height != 150) {
											toastr['error']('As dimensões da imagem devem ser de exatamente 680px de largura por 150px de altura.');
											$('#imagemBanner').val('');
										}
										// lembrete de boleto
										else if (widget.WID_inteligencia == 45 && img.width != 800 && img.height != 160) {
											toastr['error']('As dimensões da imagem devem ser de exatamente 800px de largura por 160px de altura.');
											$('#imagemBanner').val('');
									 	} else {
										 	$('.img-banner-small').attr('src', f.target.result);
										}
										desbloqueiaElemento($('#containerAlteraImagemForm')[0]);
									}
	
								}
	
								reader.readAsDataURL(file);
							})
						}
		
						var selectUpDown = '<select name="UpDown" class="form-control">';
						var dicionarioUpDown = {
							1: 'Acima',
							0: 'Abaixo'
						};
		
						Object.keys(dicionarioUpDown).forEach(function (id) { // id: id upDown
	
							if (parseInt(widget.WID_updown) == id) {
	
								selectUpDown += '<option value="' + id + '" selected>' + dicionarioUpDown[id] + '</option>';
	
							} else {
	
								selectUpDown += '<option value="' + id + '">' + dicionarioUpDown[id] + '</option>';
							}
						});
	
						selectUpDown += '</select>';
						//-------------------
	
						var containerID =
							'<div class="form-group">' +
							'<div class="row">' +
							'<div class="col-xs-3">' +
							'<label>Tipo do Container</label>' +
							'<div class="rh-input-icon-right">' +
							'<select id="widDivType" name="widDivType" class="form-control">' +
							'<option id="1"';
						if (widget.WID_div_type == 'id' || widget.WID_div_type == 'ID')
							containerID += 'selected ';
						containerID += 'value="id">ID</option>' +
							'<option id="2"';
						if (widget.WID_div_type == 'class' || widget.WID_div_type == 'CLASS')
							containerID += 'selected ';
						containerID += 'value="class">CLASS</option>' +
							'</select>' +
							'</div>' +
							'</div>' +
							'<div class="col-xs-4">' +
							'<label>Nome da classe ou ID</label>' +
							'<div class="rh-input-icon-right">' +
							'<input id="widDiv" name="widDiv" class="form-control" type="text" value="' + widget.WID_div + '">' +
							'<abbr title="Esse é o identificador do container onde o nosso bloco vai ficar dentro da sua loja" class="info-abbr">' +
							'<i class="icon-info"></i>' +
							'</abbr>' +
							'</div>' +
							'</div>' +
							'<div class="col-xs-5">' +
							'<label>Posição relativa ao Container</label>' +
							'<div class="rh-input-icon-right">' +
							'<select name="UpDown" class="form-control">' +
							'<option id="1" ';
						if (parseInt(widget.WID_updown) == 1)
							containerID += 'selected ';
						containerID += 'value="1">Acima</option>' +
							'<option id="2" ';
						if (parseInt(widget.WID_updown) == 0)
							containerID += 'selected ';
						containerID += 'value="0">Abaixo</option>' +
							'</select>' +
							'</div>' +
							'</div>' +
							'</div>' +
							'</div>';
						
							$('#container-configuracoes').html(
							containerID +
							'<div class="row" id="excessoesPaginas">' +
							'<div class="col-md-6">' +
							'<div class="form-group exceptions">' +
							'<label>Excessões de páginas</label>' +
							'<div class="rh-input-icon-right">' +
							'<input id="widHide" name="widHide" class="form-control" type="url" value="">' +
							'<abbr title="Informe o nome da página que deseja que o widget não seja executado" class="info-abbr">' +
							'<i class="icon-info"></i>' +
							'</abbr>' +
							'</div>' +
							'</div>' +
							'<div class="form-group">' +
							'<div class="rh-input-icon-right">' +
							'<button class="btn btn-primary addHideField" title="Adicionar mais uma página de excessão">' +
							'+' +
							'</button>' +
							'</div>' +
							'</div>' +
							'</div>' +
							'<div class="col-md-6">' +
							'<div class="form-group inclusions">' +
							'<label>Inclusões de páginas</label>' +
							'<div class="rh-input-icon-right">' +
							'<input id="widShow" name="widShow" class="form-control" type="url" value="">' +
							'<abbr title="Informe o nome da página que deseja que o widget seja executado" class="info-abbr">' +
							'<i class="icon-info"></i>' +
							'</abbr>' +
							'</div>' +
							'</div>' +
							'<div class="form-group">' +
							'<div class="rh-input-icon-right">' +
							'<button class="btn btn-primary addShowField" title="Adicionar mais uma página de inclusão">' +
							'+' +
							'</button>' +
							'</div>' +
							'</div>' +
							'</div>' +
							'</div>'
						);

						//Preenchendo campos WID_HIDE
						$.each(widget.WID_hide, function (index, val) {
							if (index > 0) {
								$('.addHideField').trigger('click');
							}
	
							$('input[name=widHide]')[index].value = val;
						});
	
						//Preenchendo campos WID_ShOW
						$.each(widget.WID_show, function (index, val) {
	
							if (index > 0) {
								$('.addShowField').trigger('click');
							}
	
							$('input[name=widShow]')[index].value = val;
						});
						
						if ($('#btnViewBanner').length > 0) {
							rhPhoto($('#btnViewBanner'));
						}
					}			
				});
			});

		},

		botoesSalvar: function () {
			// BOTOES SALVAR WIDGET
			$('#btn-salva-wid').off('click')
			$('#btn-salva-wid').on('click', function () {
				var form = document.getElementById('campos-wid-edit');
				var idWid = form.getAttribute('id-wid');
				var inputs = $('#campos-wid-edit input');
				var selects = $('#campos-wid-edit select');

				var formData = new FormData();
				formData.append('idWid', idWid);
				formData.append('op', 3);

				// PEGA O VALOR DE TODOS OS INPUTS
				for (var i = 0; i < inputs.length; i++) {
					var key = inputs[i].name;
					if (inputs[i].type == 'file') {
						var val = inputs[i].files[0];
					} else {
						var val = inputs[i].value;
					}

					formData.append(key, val);
				}

				// dias venc
				if ($('#diasBoletoVenc').length > 0) {
					window['diasBoletoVenc'] = $('#diasBoletoVenc').val();
				}

				// tratamento widshow e widhide para salvar mais de 1 pagina
				formData.delete('widShow');
				formData.delete('widHide');
				var widShow = [];
				var widHide = [];

				var widS = $('input[name="widShow"]');
				var widH = $('input[name="widHide"]');

				for (var i = 0; i < widS.length; i++) {
					widShow.push(widS[0].value);
				};
				for (var i = 0; i < widH.length; i++) {
					widHide.push(widH[0].value);
				};

				formData.append('widShow', widShow);
				formData.append('widHide', widHide);

				// PEGA O VALOR DE TODOS OS SELECTS
				for (var i = 0; i < selects.length; i++) {
					var key = selects[i].name;
					var val = selects[i].value;
					formData.append(key, val);
				};

				// paginas inclusao widgets
				formData.delete('widShow');
				var widShowValue = '';
				var widShow = document.getElementsByName('widShow');
				for (var i = 0; i < widShow.length; i++) {
					var value = widShow[i].value.trim();
					if (value !== '') {
						widShowValue = (i === 0) ? value : widShowValue + ',' + value;
					}
				}
				formData.append('widShow', widShowValue);

				// paginas exclusao widgets
				formData.delete('widHide');
				var widHideValue = '';
				var widHide = document.getElementsByName('widHide');
				for (var i = 0; i < widHide.length; i++) {
					var value = widHide[i].value.trim();
					if (value !== '') {
						widHideValue = (i === 0) ? value : widHideValue + ',' + value;
					}
				}
				formData.append('widHide', widHideValue);

			
				if (!formData.get('imagemBanner'))
					formData.delete('imagemBanner')

				if (!formData.get('thumbnail'))
					formData.delete('thumbnail')

				$.ajax({
					type: 'POST',
					url: 'resource/resource_widget_edit.php',
					dataType: 'text',
					cache: false,
					contentType: false,
					processData: false,
					data: formData,
					success: function (result) {
						$('#modalEditarWidget').modal('hide');
						toastr['success']('As informações do seu bloco de recomendação foram atualizadas!');
					}
				});
			});
		}
	}

	widgets.init();

	$(document).on('click', '#container-configuracoes .addHideField', function (event) {
		var htmlHide = '<div class="form-group exceptions">' +
							'<div class="rh-input-icon-right">' +
							'<input id="widHide" name="widHide" class="form-control" type="url" value="">' +
							'<span class="btn-delete-form-group"><i class="fa fa-trash red"></i></span>' +
							'</div>' +
							'</div>';						
		$(this).before(htmlHide);
		addListenerBtnDeleteForm();
		event.preventDefault();
	});

	$(document).on('click', '#container-configuracoes .addShowField', function (event) {
		var htmlShow =
						'<div class="form-group inclusions">' +
						'<div class="rh-input-icon-right">' +
						'<input name="widShow" class="form-control" type="url" value="">' +
						'</div>' +
						'<span class="btn-delete-form-group"><i class="fa fa-trash red"></i></span>' +
						'</div>';
		$(this).before(htmlShow);
		addListenerBtnDeleteForm();
		event.preventDefault();
	});

	function addListenerBtnDeleteForm() {
		$('.btn-delete-form-group').off('click', removeForm);
		$('.btn-delete-form-group').on('click', removeForm);
	}

	function removeForm(event) {
		$(event.target).closest('.form-group').remove();
	}

});
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
				}
			})
		
		},

		loadRecBoleto: function(wids) {
			var $list = document.getElementById('widgetsBoleto');
			wids.innerHTML = "";
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

		loadRecCarrinho: function(wids) {
			var $list = document.getElementById('widgetsCarrinho');
			wids.innerHTML = "";
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
				var idWid = this.parentElement.parentElement.getAttribute('wid-id');
				var val = this.checked;
				$.ajax({
					type: 'POST',
					url: 'resource/resource_widget_edit.php',
					data: { 'idWid': idWid, 'val': val, 'op': 4 },
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
	
						$('#rhIdWid').html(widget.WID_id)
	
	
						var intels = {
							41: "Recuperação de Carrinho On-site",
							43: "Recuperação de Boleto"
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
	
						//esconder campos de acordo com a inteligência
						let $tituloWidget = $('#tituloWidget').parent().parent()
						let $subtituloWidget = $('#inputSubtitulo')
						$tituloWidget.show()
						$tituloWidget.show()
	
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
										if (window.template == 3 && img.width != 900 && img.height != 150) {
											toastr['error']('As dimensões da imagem devem ser de exatamente 900px de largura por 150px de altura.');
											$('#imagemBanner').val('');
										} else if (window.template != 3 && img.width != 350 && img.height != 500) {
											toastr['error']('As dimensões da imagem devem ser de exatamente 350px de largura por 500px de altura.');
											$('#imagemBanner').val('');
										} else {
											$('.img-banner-small').attr('src', f.target.result);
										}
										desbloqueiaElemento($('#containerAlteraImagemForm')[0]);
									}
	
								}
	
								reader.readAsDataURL(file);
							})
							$('#imagemBannerLojaLateral').change(function () {
	
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
											$('#imagemBannerLojaLateral').val('');
										}
										if (img.width != 400 && img.height != 300) {
											toastr['error']('As dimensões da imagem devem ser de exatamente 400px de largura por 300px de altura.');
											$('#imagemBannerLojaLateral').val('');
										} else {
											$('.img-banner-small').attr('src', f.target.result);
										}
										desbloqueiaElemento($('#containerAlteraImagemForm')[0]);
									}
	
								}
	
								reader.readAsDataURL(file);
							})
							$('#thumbnail').change(function () {
	
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
											$('#thumbnail').val('');
										}
										if (img.width != 80 && img.height != 80) {
											toastr['error']('As dimensões da imagem devem ser de exatamente 80px de largura por 80px de altura.');
											$('#thumbnail').val('');
										} else {
											$('.img-thumb-small').attr('src', f.target.result);
										}
										desbloqueiaElemento($('#containerAlteraImagemForm')[0]);
									}
	
								}
	
								reader.readAsDataURL(file);
							})
						}
	
						// nome produto manual ol
						if (typeof widget.tx_param_pai != 'undefined') {
							$('#manualOfertaLimitadaInput').val(widget.tx_param_pai[0]);
						}
	
						// se nao for widget basico, mostra id e opcao de alterar o formato
						// opcoes select formato
						var select = '<select name="formatoWidget" class="form-control">';
						var dicionarioFormatos = {
							1: 'Prateleira',
							3: 'Carrossel'
							/*8:'Vitrine',
							11:'Totem'*/
						};
	
						var selectUpDown = '<select name="UpDown" class="form-control">';
						var dicionarioUpDown = {
							1: 'Acima',
							0: 'Abaixo'
						};
	
						Object.keys(dicionarioFormatos).forEach(function (id) { // id: id do formato
							if (widget.WID_formato == id) {
								select += '<option value="' + id + '" selected>' + dicionarioFormatos[id] + '</option>';
							} else {
								select += '<option value="' + id + '">' + dicionarioFormatos[id] + '</option>';
							}
						});
	
						Object.keys(dicionarioUpDown).forEach(function (id) { // id: id upDown
	
							if (parseInt(widget.WID_updown) == id) {
	
								selectUpDown += '<option value="' + id + '" selected>' + dicionarioUpDown[id] + '</option>';
	
							} else {
	
								selectUpDown += '<option value="' + id + '">' + dicionarioUpDown[id] + '</option>';
							}
						});
	
						select += '</select>';
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
					}
				});
			});
		}
	}

	widgets.init();



	$(document).on('click', '#container-configuracoes .addHideField', function (event) {
		$(this).before(htmlHide);
		addListenerBtnDeleteForm();
		event.preventDefault();
	});

	$(document).on('click', '#container-configuracoes .addShowField', function (event) {
		$(this).before(htmlShow);
		addListenerBtnDeleteForm();
		event.preventDefault();
	});

});
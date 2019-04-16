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
						console.log(result);
					}
				});
			});

		},
	}

	widgets.init();

});
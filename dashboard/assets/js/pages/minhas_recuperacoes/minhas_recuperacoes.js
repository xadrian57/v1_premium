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
					console.log(data);

					widgets.loadRecBoleto(data.boleto)
					widgets.loadRecCarrinho(data.carrinho)
				}
			})
		
		},

		loadRecBoleto: function(wids) {
			var $list = document.getElementById('widgetsBoleto');
			wids.innerHTML = "";
			wids.forEach(function (wid) {
				var ativo = (wid.ativo === '1') ? 'checked' : '';

				$list.innerHTML = $list.innerHTML +
					'<li class="list-group-item" wid-id="' + wid.id + '"><span>' + wid.nome + '</span>' +
					'<div style="width: auto;display: inline-block;position:relative;bottom: 7px;float:right;">' +
					'<!-- <button class="btn btn-danger pull-right" data-delete-wid=' + wid.id + '><i class="ft-x"></i> Deletar</button> -->' +
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
				var ativo = (wid.ativo === '1') ? 'checked' : '';

				$list.innerHTML = $list.innerHTML +
					'<li class="list-group-item" wid-id="' + wid.id + '"><span>' + wid.nome + '</span>' +
					'<div style="width: auto;display: inline-block;position:relative;bottom: 7px;float:right;">' +
					'<!-- <button class="btn btn-danger pull-right" data-delete-wid=' + wid.id + '><i class="ft-x"></i> Deletar</button> -->' +
					'<button class="btn btn-info pull-right mr-1 ml-1 btn-edita-wid"><i class="icon-pencil"></i> Editar</button>' +
					'<input type="checkbox" class="switch pull-right" data-off-label="desativar" data-on-label="ativar" data-switch-always ' + ativo + '/>' +
					'</div>' +
					'</li>';
			});
		}
	}

	widgets.init();

});
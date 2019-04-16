'use strict'
$(document).ready(function() {

	var widgets = {
		init: function ()
		{
			$.ajax( {
				type: 'POST',
				url: 'resource/resource_widget_edit.php',
				data: { 'idCli': idCli, 'op': 1 },
				success: function ( result )
				{
					var data = JSON.parse(result)
					console.log(data);					
				}
			})
		
		}
	}

	widgets.init();

});
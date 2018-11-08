if(typeof localStorage['_trh'] === 'undefined') {
	window.location.href="inicio";
}

$(document).ready(function(){
	$('#btnValidaToken').on('click',function(){
		if($('#token').val().length < 8) {
			toastr['error']('O token digitado não confere');
		} else {
			$.ajax({
				type: 'post',
				url: 'resource/resource_verifica_token.php',
				data: { token: $('#token').val() },
				success: function(response){
					console.log(response);
					if (response == '1') {
						$('#sessaoToken').hide();
						$('#cadastro').show();					
						toastr['success']('Token validado, digite agora as informações para o seu cadastro');
						$('#formularioCadastro').attr('method','POST');
						$('#formularioCadastro').attr('action','resource/resource_cadastro.php');
						$('#tokenForm').val($('#token').val());
					} else {
						toastr['error']('O token digitado não confere');
					}
				}
			});
		}			
	});
});
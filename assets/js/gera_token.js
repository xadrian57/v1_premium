$('.btn-solicita-demo').on('click',function(){
	var email = $('#email').val();
	var reg = new RegExp(/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/);

	if (reg.test(email)){ // se o email for válido
		$.ajax({
			type: 'post',
			url: 'resource/resource_token.php',
			data: {email: $('#email').val()},
			success: function(response) {				
				//console.log(response);
				if (response === '1'){
					toastr['error']('Esse email já está cadastrado');
					return false;
				}
				$('#sectionInsereEmail').hide();
				$('#sectionConfirmacaoToken').show();
				localStorage.setItem('_trh',email)
				setTimeout(function(){
					$('#sectionConfirmacaoToken').removeClass('flip-animation');	
				},500);
				console.log(response);
			}
		});
	} else {
		toastr['error']('Email inválido');	
	}
});
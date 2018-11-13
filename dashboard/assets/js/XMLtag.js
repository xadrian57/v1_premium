/*
--	Autor: Eliabe
--	Data: final de 2017, n lembro
--	Update: 18/02/2018
--	Desc: Script para o cadastro do XML no banco
--
-- É GAMBIARRA MAI É UMA GAMBIARRA BEM FEITA
--
--
*/

$(document).ready(function(){
	// SELECIONA PRIMEIRA FUNCIONALIDADE
	$(document.getElementsByClassName('funcionalidade')[0]).addClass('active');

	
	XMLtags = {};
	XMLtagsSecundarias = {};

	function reverseOBJ(obj) {
		var newObj = {};
		Object.entries(obj).forEach(([key, val]) => {
		   newObj[val] = key;
		});
		return newObj;
	}

	// quando clica em uma funcionalidade para selecionar
	$('.funcionalidade').on('click',function(){
		$('.funcionalidade').removeClass('active');
		$('.tag-cadastrada').removeClass('active');
		$(this).addClass('active');
	});

	// quando seleciona a opção de tag avançada
	$('#checkTagSecundaria').on('change',function(){
		this.value = this.checked;
		var toggle = (this.value === 'true') ? 'show':'hide';
		if($('#tagSecundariaForm').is(':visible')) {
			document.getElementById('tagSecundariaForm').setAttribute('hidden','hidden');
		} else {
			document.getElementById('tagSecundariaForm').removeAttribute('hidden');
		}
	})

	// botão adicionar tag >>
	$('#btnAddTag').on('click', function(){	
		adicionaTag();
	});

	// botão remover tag <<
	$('#btnDelTag').on('click', function(){
		removeTag();
	})

	// botão que salva tudo no banco
	$('#btnSavaXML').on('click', function(){
		var url = $('#cadastraXml').val();
		salvaXML(XMLtags, tagsSecundarias, url);
	});
	
// FUNÇÃO QUE SALVA TUDO NO BANCO
	function salvaXML(tags, tagsSecundarias, url) {
		// N SALVAR SEM NOME,PRECO OU DISPONIBILIDADE
		if (!XMLtags.hasOwnProperty('Nome Produto') || !XMLtags.hasOwnProperty('Preço Normal') 
		 || !XMLtags.hasOwnProperty('Disponibilidade em estoque') || !XMLtags.hasOwnProperty('Link Produto')){

    		toastr['error']('As tags "Nome Produto","Preço Normal" e "Disponibilidade em estoque" são obrigatórias');
			return false;
		}

		var linkXML = encodeURIComponent($('#cadastraXml').val());

		bloqueiaTela(); // bloqueia tela
	    $.ajax({
	       type : 'POST',
	       url : 'resource/resource_leitor_xml.php',
	       data : {'tags':XMLtags, 'tags_secundarias': XMLtagsSecundarias, url: linkXML},
	       success : function(response){
	       		switch(response){
	       			case '0':
		    		   	$('#XMLTAG').modal('hide');
						desbloqueiaTela(); // desbloqueia tela
	    			   toastr['error']('Não foi possível atualizar as suas informações, tente novamente mais tarde');
	    			   console.log(response);
	       				break;
	       			case '1':
		    		   	$('#XMLTAG').modal('hide');
		       			toastr.options = {
						  "closeButton": true,
						  "debug": true,
						  "newestOnTop": false,
						  "progressBar": true,
						  "positionClass": "toast-top-center",
						  "preventDuplicates": true,
						  "onclick": null,
						  "showDuration": "300",
						  "hideDuration": "1000",
						  "timeOut": "5000",
						  "extendedTimeOut": "1000",
						  "showEasing": "swing",
						  "hideEasing": "linear",
						  "showMethod": "fadeIn",
						  "hideMethod": "fadeOut"
						}
						toastr["success"]("Seu XML foi cadastrado com sucesso!\n\nA página irá recarregar automaticamente em 5 segundos para atualizar as informações");
						setTimeout(function(){
							window.location.href = 'controle_produtos';
						}, 5000);
						break;
					case '2':
		    		   	$('#XMLTAG').modal('hide');
						desbloqueiaTela(); // desbloqueia tela
	    			   	toastr['error']('Algo deu errado, faça o login no Dashboard novamente');
						break;
					case '3':
		    		   	$('#XMLTAG').modal('hide');
						desbloqueiaTela(); // desbloqueia tela
						toastr.options = {
						  "closeButton": false,
						  "debug": false,
						  "newestOnTop": false,
						  "progressBar": false,
						  "positionClass": "toast-top-right",
						  "preventDuplicates": false,
						  "showDuration": "99999999999",
						  "hideDuration": "1000",
						  "timeOut": "5000",
						  "extendedTimeOut": "1000",
						  "showEasing": "swing",
						  "hideEasing": "linear",
						  "showMethod": "fadeIn",
						  "hideMethod": "fadeOut"
						}
						toastr["error"]("Seu XML pode estar sem a tag de ID de produto ou você pode ter esquecido de configurar essa tag com a informação correta, <b><u>clique aqui</u></b> para tentar resolver seu problema. ");
						$('#toast-container').click(function(){
							window.open('https://roihero.octadesk.com/kb/','_blank');
						});
						break;
					default:
	    			   	toastr['error']('Algo deu errado, tente novamente mais tarde');
		    		   	$('#XMLTAG').modal('hide');
						desbloqueiaTela(); // desbloqueia tela
						console.log('ERRO NO CADASTRO DO XML:');
						console.log(response);
						break;

	       		}			
				console.log('TAGS\n');
				console.log(XMLtags); 
				console.log('\n\nTAGS SECUNDARIAS');
				console.log(XMLtagsSecundarias);
	       }
	    });
	}
//-------------------------------

// ADICIONA TAGS À LISTA---------
	function adicionaTag(){
		// SE NAO TIVER SELECIONADO NADA, SAI DA FUNCAO
		if (typeof $('.funcionalidade.active')[0] === 'undefined'){ 
			return false;
		}

		var funcionalidade = $('.funcionalidade.active').text();
		var funcionalidadeVal = $('.funcionalidade.active').val();
		var tag = $('#selectTags').val();

		// TAG SECUNDARIA CHECADA?
		if (($('#checkTagSecundaria').val() === 'true') ? true : false){
			var tagSecundaria = $('#tagsSecundarias').val();
			// SE AS DUAS TAGS FOREM IGUAIS, SAI DA FUNÇÃO
			if (tagSecundaria == tag) { return false; }

			$('#tagsCadastradas').append(
				'<li class="form-control list-group-item tag-cadastrada" data-tag="'+tag+'" data-tag-sec="'+tagSecundaria+'" data-func="'+funcionalidade+'"style="padding: 6px 12px !important;"><b>&lt;'+tag+'&gt; → &lt;'+tagSecundaria+'&gt; : </b>'+funcionalidade+'</li>'
			);

			$("#tagsSecundarias option[value='"+tagSecundaria+"']").remove();

			// adiciona tag no obj
			adicionaOBJTAGSECUNDARIA(funcionalidade,tagSecundaria);
			
		} else {
			$('#tagsCadastradas').append(
				'<li class="form-control list-group-item tag-cadastrada" data-tag="'+tag+'" data-tag-sec="" data-func="'+funcionalidade+'"style="padding: 6px 12px !important;"><b>&lt;'+tag+'&gt; : </b>'+funcionalidade+'</li>'
			);

			// REMOVE OPCAO DO DROPDOWN
			$("#selectTags option[value='"+tag+"']").remove();
		}

		// REMOVE OPCAO DO DROPDOWN
		$('.funcionalidade.active').remove();

		// ADICIONANDO LISTENER NA TAG CADASTRADA
		var ultimaTagCadastrada = document.getElementsByClassName('tag-cadastrada');
		var ultimaTagCadastrada = ultimaTagCadastrada[ultimaTagCadastrada.length-1];
		ultimaTagCadastrada.addEventListener('click',function(){
			$('.tag-cadastrada').removeClass('active');
			$('.funcionalidade').removeClass('active');
			$(this).addClass('active');
		});

		// SELECIONA PRIMEIRA FUNCIONALIDADE SEMPRE Q ADICIONA
		$(document.getElementsByClassName('funcionalidade')[0]).addClass('active');

		// adiciona no obj
		adicionaOBJTAG(funcionalidade,tag);

		console.log('-----------TAGS----------');
		console.log(XMLtags);	
		console.log('----TAGS-SECUNDÁRIAS-----');	
		console.log(XMLtagsSecundarias);
	}
//-------------------------------

// VOLTA TAGS--------------------
	function removeTag(){
		// SE NAO TIVER SELECIONADO NADA, SAI DA FUNCAO
		if (typeof $('.tag-cadastrada.active')[0] === 'undefined'){ return false;}
		var tag = $('.tag-cadastrada.active').attr('data-tag');
		var funcionalidade = $('.tag-cadastrada.active').attr('data-func');
		var tagSecundaria = $('.tag-cadastrada.active').attr('data-tag-sec');

		if (tagSecundaria !== ''){
			$('#tagsSecundarias').append(
	    		'<option value="'+tagSecundaria+'">&lt;'+tagSecundaria+'&gt;</option>'
			);

			if(XMLtagsSecundarias[funcionalidade].split(',').length > 1){
				var a = XMLtagsSecundarias[funcionalidade].split(',');
				a.splice(a.indexOf('tagSecundaria'));
				XMLtagsSecundarias[funcionalidade] = a.join(',');
			} else {
				delete XMLtagsSecundarias[funcionalidade];
			}
		} else {
			// ADICIONA NO DROPDOWN
			$('#selectTags').append(
		    	'<option value="'+tag+'">&lt;'+tag+'&gt;</option>'
			);
		}

		$('#listaFuncionalidades').append(
			'<li class="list-group-item form-control funcionalidade" style="padding: 6px 12px !important;">'+funcionalidade+'</li>'
		);


		$('.tag-cadastrada.active').remove();

		delete XMLtags[funcionalidade];

		// ADICIONA LISTENER NOVAMENTE
		var ultimaFuncionalidade = document.getElementsByClassName('funcionalidade');
		ultimaFuncionalidade = ultimaFuncionalidade[ultimaFuncionalidade.length-1];
		ultimaFuncionalidade.addEventListener('click', function(){
			$('.funcionalidade').removeClass('active');
			$('.tag-cadastrada').removeClass('active');
			$(this).addClass('active');
		});


		// SELECIONA PRIMEIRO CADASTRADO SEMPRE Q REMOVE
		$(document.getElementsByClassName('tag-cadastrada')[0]).addClass('active');
	}
//-------------------------------

// BOTOES CADASTRA E EDITA XML---
	$('#btnCadastraXml').on('click', function(){
		bloqueiaTela(); // bloqueia tela
		var url = document.getElementById('cadastraXml').value;
		$.ajax({
			type: 'POST',
			url: 'resource/resource_tags_xml.php',
			data: {'url' : encodeURIComponent(url)},
			success: function(response){
				if (response !== '0'){					
					console.log('RESPOSTA EM TEXTO:\n');
					console.log(response);
					console.log('---------------------\n');
					console.log('RESPOSTA EM JSON:\n');
					console.log(JSON.parse(response));
					xmlTags = JSON.parse(response);
					carregaTags(xmlTags);
					if(Object.entries(XMLtags).length >= 9){						
						var url = $('#cadastraXml').val();
						salvaXML(XMLtags, tagsSecundarias, url);
					} else {
						//reseta tags
						XMLtags = {};
						XMLtagsSecundarias = {};
						$('#XMLTAG').modal('show');
					}
				} 

				desbloqueiaTela(); // desbloqueia tela
			}
		})
	});

	$('#btnEditarXml').on('click', function(){
		bloqueiaTela(); // bloqueia tela
		var url = $('#linkXML a').text();
		$.ajax({
			type: 'POST',
			url: 'resource/resource_tags_xml.php',
			data: {'url' : encodeURIComponent(url)},
			success: function(response){
				if (response !== '0'){					
					console.log('RESPOSTA EM TEXTO:\n');
					console.log(response);
					console.log('---------------------\n');
					console.log('RESPOSTA EM JSON:\n');
					console.log(JSON.parse(response));
					xmlTags = JSON.parse(response);
					carregaTags(xmlTags,'edita');
					if(Object.entries(XMLtags).length >= 9){						
						var url = $('#cadastraXml').val();
						salvaXML(XMLtags, tagsSecundarias, url);
					} else {
						//reseta tags
						XMLtags = {};
						XMLtagsSecundarias = {};
						$('#XMLTAG').modal('show');
					}
				} else {					
					toastr['error']('Não foi possível ler o seu XML, tente novamente mais tarde');
					console.log(response);
				}

				desbloqueiaTela(); // desbloqueia tela
			}
		})
	});
//-------------------------------

// ADICIONA NO OBJ---------------
	adicionaOBJTAG = function(key,value){
		XMLtags[key] = value;
	}
//-------------------------------

// ADICIONA TAGS SECUNDÁRIAS-----
	adicionaOBJTAGSECUNDARIA = function(key,value){
		if (typeof XMLtagsSecundarias[key] === 'undefined') {
			XMLtagsSecundarias[key] = value;
		} else{
			XMLtagsSecundarias[key] = XMLtagsSecundarias[key]+','+value;
			XMLtagsSecundarias[key] = XMLtagsSecundarias[key]+","+tagSecundaria;
		}
	}
//-------------------------------

// CARREGA TODAS AS TAGS---------
	function carregaTags(tags,op){
		if (op === 'edita'){
			Object.entries(tags).forEach(([key, val]) => {
			    $('#selectTags').append('<option value="'+val+'">&lt;'+val+'&gt;</option>');
			    $('#tagsSecundarias').append('<option value="'+val+'">&lt;'+val+'&gt;</option>');
			});
		} else {
			// DICIONÁRIO TAGS PRÉ DEFINIDAS -----------------------------------------
				// tags pré-definidas
				var preTags = {
					'item':'Tag do Produto',
					'entry':'Tag do Produto',
					'g:id':'ID Produto',
					'id':'ID Produto',
					'price':'Preço Normal',
					'g:price':'Preço Normal',
					'sale_price':'Preço Promocional',
					'g:sale_price':'Preço Promocional',
					'availability':'Disponibilidade em estoque',
					'g:availability':'Disponibilidade em estoque',
					'link':'Link Produto',
					'g:link':'Link Produto',
					'product_type':'Categoria',
					'g:product_type':'Categoria',
					'image_link':'Foto Produto',
					'g:image_link':'Foto Produto',
					'title':'Nome Produto',
					'g:title':'Nome Produto',
					'brand':'Marca do produto',
					'g:brand':'Disponibilidade em estoque',
					'reference_id':'Custom 5'
				}

				// tags pre definidas parcelamento
				var nParcPai = { // tag pai qtd parcelas
					'g:installment':'Quantidade de parcelas',
					'installment':'Quantidade de parcelas'
				}

				var vParcPai = { // tag pai valor parcelas
					'g:installment':'Valor das parcelas',
					'installment':'Valor das parcelas'
				}
				
				var nParcFilho = {
					'months':'Quantidade de parcelas',
					'g:months':'Quantidade de parcelas'
				}

				var vParcFilho = {
					'amount':'Valor das parcelas',
					'g:amount':'Valor das parcelas'
				}
			//------------------------------------------------------------------------
			Object.entries(tags).forEach(([key, val]) => {
				// checa se possui as tags padrão do google shopping
				if(preTags.hasOwnProperty(val)){
					var index = Object.keys(preTags).indexOf(val);
					index = Object.keys(preTags)[index];
					//console.log(preTags[val]+' => '+index);

					adicionaOBJTAG(preTags[val],index);
				}
				// PARCELAMENTO
				if (vParcPai.hasOwnProperty(val)){
					var index = Object.keys(vParcPai).indexOf(val);
					index = Object.keys(vParcPai)[index];
					//console.log(vParcPai[val]+' => '+index);

					adicionaOBJTAG(vParcPai[val],index);
				}
				if (nParcPai.hasOwnProperty(val)){
					var index = Object.keys(nParcPai).indexOf(val);
					index = Object.keys(nParcPai)[index];
					//console.log(nParcPai[val]+' => '+index);

					adicionaOBJTAG(nParcPai[val],index);
				}
				if (nParcFilho.hasOwnProperty(val)){
					var index = Object.keys(nParcFilho).indexOf(val);
					index = Object.keys(nParcFilho)[index];
					//console.log(nParcFilho[val]+' => '+index);

					adicionaOBJTAGSECUNDARIA(nParcFilho[val],index);
				}
				if (vParcFilho.hasOwnProperty(val)){
					var index = Object.keys(vParcFilho).indexOf(val);
					index = Object.keys(vParcFilho)[index];
					//console.log(vParcFilho[val]+' => '+index);

					adicionaOBJTAGSECUNDARIA(vParcFilho[val],index);			
				}

			    $('#selectTags').append('<option value="'+val+'">&lt;'+val+'&gt;</option>');
			    $('#tagsSecundarias').append('<option value="'+val+'">&lt;'+val+'&gt;</option>');
			});
		}			
	}
//-------------------------------
});

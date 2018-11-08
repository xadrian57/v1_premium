/*
--	Autor: Getulio Monteiro Ribeiro
--	Data: 03/2018
--	Update: 15/03/2018
--	Desc: Script responsável por carregar e salvar os templates HTML dos clientes
--
*/
'use strict';
(function ($, Private) {

	window.idCli = $('#infsess').attr('data-cli');
	
	Private = {
		API 			: {
			url 	: 'resource/resource_template_library.php',
			params 	: {
				kitId : 0
			}
		},
		scrollToClass   : '.scrollTo',
		templateList 	: '.template-list',
		saveConfirmation: '#saveConfirmation',
		activeButton 	: '.choose-btn',
		previewContainer: '.preview',
		previewIframe 	: '#previewContent',
		previewButton	: '.preview-btn',
		previewUrl 		: '../widget/preview/kit_'
	};

	Private.init = function () {

		Private.listeners();
		Private.loadKits();
	};

	Private.listeners = function () {

		// Scroll screen to the configured destination element
        $(document).on('click', Private.scrollToClass, Private.scrollToDestination);


        $(document).on('click', Private.previewButton, Private.loadPreview);
        $(document).on('click', Private.activeButton, Private.prepareForSave);
        $(document).on('click', Private.saveConfirmation, Private.saveKit);
	};

	Private.saveKit = function () {

		var
			html 	= '',
			active 	= false,
			params 	= {
				id 		: window.idCli,
				op 		: 'save',
				kit 	: Private.API.params.kitId
			};

		$.post(Private.API.url, params, function(data, textStatus, xhr) {
			
			if (data.status) {

				toastr.success(data.message);
				Private.loadKits();

			} else {

				toastr.error(data.message);
			}
		});
	};

	Private.prepareForSave = function () {

		Private.API.params.kitId = $(this).attr('data-kit');
	};

	Private.loadKits = function () {

		var
			html 		 = '',
			active 		 = false,
			personalized = true,
			params 		 = {
				id 		: window.idCli,
				op 		: 'list'
			};

		$.getJSON(Private.API.url, params, function(json, textStatus) {
			
			if (json.status) {

				Private.API.params.kitId = json.response.client[0];

				$.each(json.response.default, function(index, val) {
					
					active = val === json.response.client[0] ? true : false;

					if (val === json.response.client[0]) {

						personalized = false;
					}

					html += '<div class="card bg-light template-card ' + (active ? 'active' : '') + '">' +
								'<div class="card-body">' +
									'<h6 class="card-title">' +
										'Kit ' + (val < 10 ? '0' : '') + val +
									'</h6>	' +
									'<figure>' +
										'<img src="' + Private.previewUrl + val + '/img/thumb.jpg" alt="Kit ' + (val < 10 ? '0' : '') + val + '" />';
					if (active) {

						html += 		'<figcaption>' +
											'Ativo' +
										'</figcaption>';
					}

					html +=			'</figure>' +
									'<a href="#" class="btn btn-outline-warning scrollTo preview-btn" data-kit="' + val + '" data-destination=".preview" data-responsive=\'{"0":"-56"}\'>' +
										'<i class="fa fa-eye"></i> Visualizar' +
									'</a>' +
									'<a href="#" class="btn btn-outline-success choose-btn ' + (active ? 'disabled' : '') + '" data-kit="' + val + '" data-toggle="modal" data-target="#change-kit-modal">' +
										(active ? 'Ativo' : 'Ativar') +
									'</a>' +
								'</div>' +
							'</div>';
				});

				$(Private.templateList).html(html);
				
				if (!personalized) {

					$(Private.previewIframe).attr('src', Private.previewUrl + json.response.client[0]);
					$(Private.previewContainer).fadeIn('slow');
				}
			}
		});
	};

	Private.loadPreview = function (event) {

		var
			kitId = $(this).attr('data-kit');

		bloqueiaElemento($(Private.previewContainer));

		setTimeout(function() {

			$(Private.previewIframe).attr('src', Private.previewUrl + kitId);
			desbloqueiaElemento($(Private.previewContainer));

		}, 900);

		event.preventDefault();
	};

	// Scroll screen to the configured destination element
    Private.scrollToDestination = function(event) {

        var
            i           = 0,
            element     = this,
            responsive  = $(element).attr('data-responsive') ? JSON.parse($(element).attr('data-responsive')) : null,
            speed       = $(element).attr('data-speed') ? $(element).attr('data-speed') : 900,
            target      = $(element).attr('data-destination'),
            position    = null;

        if (target.indexOf('|') > -1) {

            target = target.split('|');

            for (i; i < target.length; i++) {
                
                if ($(target[i]).length > 0) {

                    target = target[i];
                    break;
                }
            }
        }

        position = target && !Array.isArray(target) && $(target).length > 0 ? $(target).offset().top : null;

        if (position) {

	        if (responsive) {

	            $.each(responsive, function(index, val) {

	                if (window.innerWidth >= parseInt(index)) {

	                    if (val.indexOf('+') > -1) {

	                        position += parseInt(val.replace('+', ''));

	                    } else if (val.indexOf('-') > -1) {

	                        position -= parseInt(val.replace('-', ''));
	                    }
	                }
	            });
	        }

            $('html, body').animate({
                scrollTop: position
            }, speed);
        }

        event.preventDefault();
    };

	$(document).ready(function() {
		Private.init();
	});

}(jQuery, {}));
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
		apiUrl 				: 'resource/resource_code_editor.php',
		defaultPhrase 		: 'Selecione o template que deseja editar no seletor acima.',
		templateList 		: 'select[name=template]',
		cancelConfirmation 	: '#cancelConfirmation',
		resetConfirmation 	: '#resetConfirmation',
		saveConfirmation 	: '#saveConfirmation',
		codeMirror 			: null,
		templates 			: null,
		currentTemplate 	: null
	};

	Private.init = function () {

		Private.listeners();
		Private.getTemplateList();
		Private.instanceCodeEditor();
	};

	Private.listeners = function () {

		$(document).on('change', Private.templateList, Private.loadContent);
		$(document).on('click', Private.cancelConfirmation, Private.cancelModifications);
		$(document).on('click', Private.resetConfirmation, Private.resetTemplate);
		$(document).on('click', Private.saveConfirmation, Private.saveModifications);
	};

	Private.saveModifications = function () {

		var
			params 	= {
				id 		: window.idCli,
				op 		: 'save',
				file 	: Private.currentTemplate ? Private.templates.client[Private.currentTemplate].file : false,
				content : Private.codeMirror.getDoc().getValue()
			};

		if (Private.currentTemplate) {

			$.post(Private.apiUrl, params, function(data, textStatus, xhr) {
				

				if (data.status) {

					toastr.success(data.message);

				} else {

					console.log('data -> ', data);
					toastr.error(data.message);
				}
				
				Private.getTemplateList(true);
			});
		
		} else {
			
			Private.codeMirror.getDoc().setValue(Private.defaultPhrase);
		}
	};

	Private.cancelModifications = function () {

		if (Private.currentTemplate) {

			Private.codeMirror.getDoc().setValue(Private.templates.client[Private.currentTemplate].content);
		
		} else {
			
			Private.codeMirror.getDoc().setValue(Private.defaultPhrase);
		}
	};

	Private.resetTemplate = function () {

		if (Private.currentTemplate) {

			Private.codeMirror.getDoc().setValue(Private.templates.default[Private.currentTemplate].content);

			Private.saveModifications();
		
		} else {
			
			Private.codeMirror.getDoc().setValue(Private.defaultPhrase);
		}
	};

	Private.instanceCodeEditor = function () {

		Private.codeMirror = CodeMirror(document.getElementById('codeEditor'), {
			value: Private.defaultPhrase,
			mode:  'htmlmixed',
			tabSize: 4,
			indentUnit: 4,
			smartIndent: true,
			indentWithTabs: true,
			lineWrapping: true,
			theme: 'seti',
			lineNumbers: true
		});
	};

	Private.getTemplateList = function (noRefresh) {

		var
			html 	= '',
			params 	= {
				id : window.idCli,
				op : 'list'
			};

		$.getJSON(Private.apiUrl, params, function(json, textStatus) {

			if (json.status) {

				Private.templates = json.response;

				html = '<option value="">Selecione o Template</option>';		

				$.each(json.response.client, function(index, val) {

					html += '<option value="' + index + '">' + val.name + '</option>';
				});

				if (!noRefresh) {

					$(Private.templateList).html(html);
				}
			}
		});
	};

	Private.loadContent = function (event) {

		//if (!Private.currentTemplate || Private.codeMirror.getDoc().getValue() === Private.templates[Private.currentTemplate].content) {

			Private.currentTemplate = this.value;

			if (this.value) {

				Private.codeMirror.getDoc().setValue(Private.templates.client[this.value].content);

			} else {

				Private.codeMirror.getDoc().setValue(Private.defaultPhrase);
			}	

		/*} else {
			
			$('#btnCancelConfig').trigger('click');
		}*/
	};

	$(document).ready(function() {
		Private.init();
	});

}(jQuery, {}));
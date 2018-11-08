// ----------------------------------
// T E M P L A T E S M A K E R ------
// Created : 03/01/18 17:25 ---------
// ----------------------------------

// W I Z A R D
// A P P L I C A T I O N
var Wapp = Wapp || {};

// TPLMK (templates maker)
Wapp.Tplmk = {};

(function(win, doc, vars) {
	'use strict';

	vars = {
		changeStyle : doc.getElementsByClassName('changeStyle'),
	};

	Wapp.Tplmk.Init = function() {
		console.log('T E M P L A T E S M A K E R LOADED!');

		Wapp.Tplmk.Listen();
		Wapp.Tplmk.GetTemplate('kit_test_slider'); // kit_test_slider (template name), kit_test (kit name), slider (product name)
	};

	Wapp.Tplmk.Listen = function() {
		Wapp.Tplmk.AddEvents('change', vars.changeStyle); // event, class
	};

	Wapp.Tplmk.GetTemplate = function (tpl) {
		var
			ajax = new XMLHttpRequest(),
			template = tpl,
			url = tpl.split('_'),
			urlTemplate = '';

		//url = ["kit", "test", "slider"]
		urlTemplate = win.location.href.replace('/layout', '') + '/content/templates/' + url[0] + '_' + url[1] + '/' + url[2];

		console.log('urlTemplate', urlTemplate);
		// content/templates/kit_test/slider/index.html

		// get styles
		Wapp.Tplmk.LoadStyle(urlTemplate);

		// get scripts
		//Wapp.Tplmk.LoadScript(urlTemplate);

		//urlTemplate += '/index.html';
		//console.log(urlTemplate);

		// get template
		ajax.open('GET', urlTemplate + '/index.html', true);
		ajax.send();

		ajax.onreadystatechange = function() {
			if (ajax.readyState === 4) {
				if (ajax.status === 200){
					console.log('ajax ok');
					// console.log(ajax);
					//console.log(ajax.responseText);
					//data = JSON.parse(ajax.responseText);

					doc.getElementsByClassName('getTemplate')[0].innerHTML = ajax.responseText;

					//Wapp.Tplmk.UrlComplete(urlTemplate);

					//doc.getElementById('cotacao').innerHTML = '<strong>Comercial </strong><b>' + nome + ' $ ' + valor + '</b>';

				} else {
					console.log('ajax error');
					//console.log(ajax);
					console.log(ajax.responseText);
					//doc.getElementById('cotacao').innerHTML = 'Erro ao carregar cotação do dolar';
				}
			}
		}
	};

	Wapp.Tplmk.UrlComplete = function(url) {
		var
			x = 0,
			cls = 'urlComplete',
			tagname = '';

		cls = doc.getElementsByClassName(cls);

		//console.log('url', url);
		//console.log('cls', cls);

		for (x = 0; x < cls.length; x ++) {
			console.log('tagname', cls[x].tagName);

			tagname = cls[x].tagName;

			if (tagname === 'LINK') {
				// css

			} else if (tagname === 'IMG') {

			} else if (tagname === 'SCRIPT') {

			}

			//cls[x].addEventListener(evt, Wapp.Tplmk.ChangeStyle);

		}
	};

	Wapp.Tplmk.LoadStyle = function(url) {
		var
			link = doc.createElement('link');

		console.log('LoadStyle url', url);

		link.href = url + '/css/styles.css';
		link.type = 'text/css';
		link.rel = 'stylesheet';
		link.media = 'screen, print';
		link.id = 'loadStyle';

		console.log('>>> url css', link.href);

		doc.getElementsByClassName('loadStyle').remove;

		doc.getElementsByTagName('head')[0].appendChild(link);
	};

	Wapp.Tplmk.LoadScript = function(url) {
		var
			script = doc.createElement('script');

		console.log('LoadScript url', url);

		script.onload = function () {
			// read js
			console.log('onload js');

		};

		script.src = url + '/js/functions.js';
		script.id = 'loadScript';

		console.log('>>> url js', script.src);

		doc.getElementsByClassName('loadScript').remove;

		doc.head.appendChild(script); //or something of the likes
	};

	Wapp.Tplmk.AddEvents = function(evt, cls) {
		var
			x = 0;

		for (x = 0; x < cls.length; x ++) {
			cls[x].addEventListener(evt, Wapp.Tplmk.ChangeStyle);
		}
	};

	Wapp.Tplmk.ChangeStyle = function (e) {
		var
			target = e.target,
			style = '',
			el = doc.querySelectorAll(target.dataset.element),
			x = 0;

		style = target.dataset.style + ':' + target.value + ';';

		if (el.length > 1) {
			for (x = 0; x < el.length; x ++) {
				el[x].setAttribute('style', style);
			}

		} else {
			el[0].setAttribute('style', style);
		}
	};

	// instanciador do init
	doc.addEventListener("DOMContentLoaded", Wapp.Tplmk.Init, true);

})(window, document, "Private");
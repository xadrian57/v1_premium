/*
- -----------------------------------------------------------
- T E M P L A T E S F U N C T I O N S -----------------------
- all functions for template --------------------------------
- -----------------------------------------------------------
- Created: 06/02/2018 ---------------------------------------
- Updated: 06/02/2018 ---------------------------------------
- -----------------------------------------------------------
- ROI HERO --------------------------------------------------
- https://www.roihero.com.br --------------------------------
- -----------------------------------------------------------
- Courtesy --------------------------------------------------
- WIZARD FLY ------------------------------------------------
- Adonis Vieira - Analist Front End -------------------------
- http://wfly.esy.es ----------------------------------------
- -----------------------------------------------------------
- I T I S T H E G R E A T E ( w ) I Z A R D W H O F L I E S -
- -----------------------------------------------------------
*/

// W I Z A R D
// A P P L I C A T I O N
var Wapp = Wapp || {};

// M O D U L E
Wapp.TplFnc = {};

(function(doc, win, vars) {
    'use strict';

	vars = {
        open 			: doc.getElementsByClassName('rh_tpl_kit_offerslimited_open')[0],
        close 			: doc.getElementsByClassName('rh_tpl_kit_offerslimited_close')[0],
        closeOverlay 	: doc.getElementsByClassName('rh_tpl_close')[0],
    };

	Wapp.TplFnc.Init = function() {
		console.log('-- T E M P L A T E S F U N C T I O N S -- [init]');

        Wapp.TplFnc.Listen();
	};

    Wapp.TplFnc.Listen = function() {
        vars.open.addEventListener('click', Wapp.TplFnc.Sidebar, true);
        vars.close.addEventListener('click', Wapp.TplFnc.Sidebar, true);
        vars.closeOverlay.addEventListener('click', Wapp.TplFnc.Overlay, true);
    };

    Wapp.TplFnc.Sidebar = function(e) {
    	var
    		target = e.target,
    		box = target.nextElementSibling;

		if (target.classList.contains('rh_tpl_kit_offerslimited_close')) {
			box = target.parentElement;
		}

		if (box.classList.contains('active')) {
			box.classList.remove('active');
			vars.open.classList.remove('active');

		} else {
			box.classList.add('active');
			vars.open.classList.add('active');
		}

		e.preventDefault();

		return false;
    };

    Wapp.TplFnc.Overlay = function(e) {
    	var
    		target = e.target,
    		box = target.parentElement.parentElement;

		box.classList.remove('active');

		e.preventDefault();

		return false;
    };

	doc.addEventListener('DOMContentLoaded', Wapp.TplFnc.Init, true);

}(document, window, 'Private'));
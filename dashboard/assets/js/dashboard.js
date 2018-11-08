/*
--	Autor: Eliabe
--	Data: 10/01/2018
--	Update: 19/02/2018
--	Desc: Script geral que roda em todas as páginas do dashboard
--
*/
'use strict';

/* Variáveis globais ------------------------------------------- 
   ATENÇÃO:
   Essas variáveis são acessadas por outros scripts no Dashboard
*/
window['nomeCli'] = $('#infsess').attr('data-nm'); // NOME CLIENTE
window['emailCli'] = $('#infsess').attr('data-em'); // EMAIL CLIENTE
window['idCli'] = $('#infsess').attr('data-cli'); // ID CLIENTE
window['idPlan'] = $('#infsess').attr('data-plan'); // ID PLANO
window['idPlat'] = $('#infsess').attr('data-plat'); // ID PLANO
window['currencyCli'] = $('#infsess').attr('data-currency'); // TIPO DE MOEDA DO CLIENTE
window['rWid'] = $('#infsess').attr('data-rwid'); // Nº DE INSERCOES NA TABELA RWID


var rhDaysOfWeek = ["Dom","Seg","Ter","Qua","Qui","Sex","Sáb"];
var rhMonthNames = ["Janeiro","Fevereiro","Março","Abril","Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro"];
		
/* ------------------------------------------------------------- */


'use strict';

var rhDash = {
	init: function(){
		this.informacoes.init(); // informacoes que n podem carregar, etc
		this.plano.init(); // planos
		this.notificacoes.init(); // notificacoes
		this.menu.init();
		localStorage.removeItem('_trh'); // remove token de confirmação para clientes que fizeram cadastro 
	},		
}

// Informações dos planos etc
rhDash.plano = {
	leBanco: function(){
		// AJAX PARA O BANCO ---------------------------------------------------------------
		var req = new XMLHttpRequest();
		req.open('post','resource/resource_checa_plano.php', true);
		req.onreadystatechange = function(){
			if(this.status == 200 && this.readyState == 4) {
				rhDash.plano.infoPlano = JSON.parse(this.responseText);
				console.log('Informações Plano');
				console.log(rhDash.plano.infoPlano);

				// INICIA QUANDO ESTIVER COM TODAS AS INFORMAÇÕES DO PLANO
				rhDash.plano.checaTrial(); // checa o trial
				rhDash.plano.setMenu();
				rhDash.plano.myWidgets(); // PAGINA MEUS WIDGETS, MOSTRA APENAS OS DAQUELE PLANO
			}
		}
		var formData = new FormData;
		formData.append('id',idCli);
		req.send(formData);
		// ---------------------------------------------------------------------------------			
	},

	checaTrial: function(){
		if (this.infoPlano.id !== '0' && this.infoPlano.id !== '1') {
			$('#trialCadastroPlano').remove();
		 	return false; 
		}			
		// dias e views restantes
		var diasRestantes = this.infoPlano.diasRestantes;
		var viewsRestantes = this.infoPlano.viewsRestantes;

		// só exibe quantidade de dias/views restantes no menu quando é menor ou igual a 1
		if (diasRestantes > 1) {				
			var nv = document.getElementById('mainNav'); // NAVBAR MENU PRINCIPAL
			var msg = (diasRestantes === '1') ? 'FALTA 1 DIA PARA EXPIRAR O SEU TRIAL':'FALTAM '+diasRestantes+' DIAS PARA EXPIRAR O SEU TRIAL';
			nv.innerHTML = nv.innerHTML +''+
						'<li class="nav-item hidden-sm-down"><span class="trial-info text-info darken-3" style="'+
						    'margin-top: 21px;'+
						    'display:  block;'+
							'">'+msg+'</span>'+
						'</li>';

			// ALTERA TITULO MODAL
			$('#modalPlanos .modal-header .modal-title').html('Não espere o seu trial acabar, contrate um plano agora!')
		} 
		// Se tiver com 0 views ou 0 dias restantes e estiver no overview, mostra modal
		else if ((diasRestantes <= 0 || viewsRestantes <= 0) && window.location.href.indexOf('overview') >=0) {
			$(document).ready(function(){
				$('#modalPlanos').modal('show');
			});
		}
	},

	setMenu: function(){
		switch(this.infoPlano.id){
			case '0': // TRIAL			
				// RELATORIOS
				$('#menu-item-relatorios').addClass('disabled');
				$('#menu-item-relatorios').attr('data-toggle','tooltip');
				$('#menu-item-relatorios').attr('data-placement','right');
				$('#menu-item-relatorios').attr('data-original-title','Estamos aprimorando os relatórios para você!');
				// RELATÓRIO TRANSACOES
				$('#menu-list-item-transacoes').addClass('disabled');
				$('#menu-list-item-transacoes').attr('data-toggle','tooltip');
				$('#menu-list-item-transacoes').attr('data-placement','right');
				$('#menu-list-item-transacoes').attr('data-original-title','Estamos aprimorando os relatórios para você!');
				// RELATÓRIO PERFORMANCE
				$('#menu-list-item-performance').addClass('disabled');
				$('#menu-list-item-performance').attr('data-toggle','tooltip');
				$('#menu-list-item-performance').attr('data-placement','right');
				$('#menu-list-item-performance').attr('data-original-title','Estamos aprimorando os relatórios para você!');
				// RELATÓRIO INTERACAO
				$('#menu-list-item-interacao').addClass('disabled');
				$('#menu-list-item-interacao').attr('data-toggle','tooltip');
				$('#menu-list-item-interacao').attr('data-placement','right');
				$('#menu-list-item-interacao').attr('data-original-title','Estamos aprimorando os relatórios para você!');

				// WIDGET MAKER
				$('#menu-list-item-widget-maker').addClass('disabled');
				$('#menu-list-item-widget-maker').attr('data-toggle','tooltip');
				$('#menu-list-item-widget-maker').attr('data-placement','right');
				$('#menu-list-item-widget-maker').attr('data-original-title','Seus blocos já estão criados em "Minhas Recomendações". Para criar um bloco personalizado você precisa ter o plano Premium');
				// BOTAO CONTRATAR PLANO
				$('#menu-item-contratar').show();
				// FATURAS
				$('#menu-list-item-faturas').remove();
				break;
			case '1': // FREE
				// RELATORIOS
				$('#menu-item-relatorios').addClass('disabled');
				$('#menu-item-relatorios').attr('data-toggle','tooltip');
				$('#menu-item-relatorios').attr('data-placement','right');
				$('#menu-item-relatorios').attr('data-original-title','Você precisa assinar um plano para acessar os relatórios');
				// WIDGET MAKER
				$('#menu-list-item-widget-maker').addClass('disabled');
				$('#menu-list-item-widget-maker').attr('data-toggle','tooltip');
				$('#menu-list-item-widget-maker').attr('data-placement','right');
				$('#menu-list-item-widget-maker').attr('data-original-title','Seus blocos já estão criados em "Minhas Recomendações". Para criar um bloco personalizado você precisa ter o plano Premium');
				// LAYOUT MAKER E EDIT
				$('#menu-list-item-layouts').addClass('disabled');
				$('#menu-list-item-layouts').attr('data-toggle','tooltip');
				$('#menu-list-item-layouts').attr('data-placement','right');
				$('#menu-list-item-layouts').attr('data-original-title','Você precisa assinar um plano para visualizar e criar um layout para os seus blocos');
				// FATURAS
				$('#menu-list-item-faturas').remove();
				// $('#menu-list-item-faturas').addClass('disabled');
				// $('#menu-list-item-faturas').attr('data-toggle','tooltip');
				// $('#menu-list-item-faturas').attr('data-placement','right');
				// $('#menu-list-item-faturas').attr('data-original-title','Você precisa assinar um plano para visualizar as suas faturas');
				// BOTAO CONTRATAR PLANO
				$('#menu-item-contratar').show();
				break;
			case '2': // PRO 1
				// WIDGET MAKER
				$('#menu-list-item-widget-maker').addClass('disabled');
				$('#menu-list-item-widget-maker').attr('data-toggle','tooltip');
				$('#menu-list-item-widget-maker').attr('data-placement','right');
				$('#menu-list-item-widget-maker').attr('data-original-title','Seus blocos já estão criados em "Minhas Recomendações". Para criar um bloco personalizado você precisa ter o plano Premium');
				// BOTAO CONTRATAR PLANO
				$('#menu-item-contratar').remove();
				break;
			case '3': // PRO 2
				// WIDGET MAKER
				$('#menu-list-item-widget-maker').addClass('disabled');
				$('#menu-list-item-widget-maker').attr('data-toggle','tooltip');
				$('#menu-list-item-widget-maker').attr('data-placement','right');
				$('#menu-list-item-widget-maker').attr('data-original-title','Seus blocos já estão criados em "Minhas Recomendações". Para criar um bloco personalizado você precisa ter o plano Premium');
				// BOTAO CONTRATAR PLANO
				$('#menu-item-contratar').remove();
				break;
			case '4': // ROCKET
				// WIDGET MAKER
				$('#menu-list-item-widget-maker').addClass('disabled');
				$('#menu-list-item-widget-maker').attr('data-toggle','tooltip');
				$('#menu-list-item-widget-maker').attr('data-placement','right');
				$('#menu-list-item-widget-maker').attr('data-original-title','Seus blocos já estão criados em "Minhas Recomendações". Para criar um bloco personalizado você precisa ter o plano Premium');
				// BOTAO CONTRATAR PLANO
				$('#menu-item-contratar').remove();
				break;
			default:
				break;
		}
	},
	// pagina meus widgets
	myWidgets: function(){
		var pagina = document.title;
		if (pagina == 'Minhas Recomendações'){
			if (this.infoPlano.id === '0'){
				$('#col-blocos-home').hide();
				$('#col-blocos-categoria').hide();
				$('#col-blocos-produto').hide();
				$('#col-blocos-busca').hide();
				$('#col-blocos-carrinho').hide();
			}
		}
	},

	init : function(){
		this.leBanco();
	}	
}

// plataformas
rhDash.menu = {
	setMenu: function(){
		// CHECA SE JA TEM AS INSERÇÕES NO BANCO PRA PODER MOSTRAR AS PAGINAS DE RELATORIO
		if (parseInt(rWid) < 1){
			// RELATÓRIO TRANSACOES
			$('#menu-list-item-transacoes').addClass('disabled');
			$('#menu-list-item-transacoes').attr('data-toggle','tooltip');
			$('#menu-list-item-transacoes').attr('data-placement','right');
			$('#menu-list-item-transacoes').attr('data-original-title','A sua loja ainda está em processo de integração');
			// RELATÓRIO PERFORMANCE
			$('#menu-list-item-performance').addClass('disabled');
			$('#menu-list-item-performance').attr('data-toggle','tooltip');
			$('#menu-list-item-performance').attr('data-placement','right');
			$('#menu-list-item-performance').attr('data-original-title','A sua loja ainda está em processo de integração');
			// RELATÓRIO INTERACAO
			$('#menu-list-item-interacao').addClass('disabled');
			$('#menu-list-item-interacao').attr('data-toggle','tooltip');
			$('#menu-list-item-interacao').attr('data-placement','right');
			$('#menu-list-item-interacao').attr('data-original-title','A sua loja ainda está em processo de integração');
		}			
		// PLATAFORMAS
		switch(idPlat){
			case '0': // TRIAL
				// RELATÓRIO PERFORMANCE
				$('#menu-list-item-transacoes').addClass('disabled');
				$('#menu-list-item-transacoes').attr('data-toggle','tooltip');
				$('#menu-list-item-transacoes').attr('data-placement','right');
				$('#menu-list-item-transacoes').attr('data-original-title','A sua plataforma ainda está em processo de integração');
				// RELATÓRIO PERFORMANCE
				$('#menu-list-item-performance').addClass('disabled');
				$('#menu-list-item-performance').attr('data-toggle','tooltip');
				$('#menu-list-item-performance').attr('data-placement','right');
				$('#menu-list-item-performance').attr('data-original-title','A sua plataforma ainda está em processo de integração');
				break;
			default:
				break;
		}
	},

	init: function(){
		this.setMenu();
	}
}

// Relatórios
rhDash.informacoes = {
	// Verifica qual a plataforma pra saber quais informações é possível mostrar
	setInfo : function(){
		var plataforma = $('#infsess').attr('data-plat');
		if (plataforma === '0'){
			$('.precisa-integracao').addClass('em-desenvolvimento');
		}
	},

	init: function(){
		this.setInfo();
	}
}

// Notificações
rhDash.notificacoes = {
	getNotifications: function(){			
		// AJAX PARA O BANCO ---------------------------------------------------------------
		var req = new XMLHttpRequest();
		req.open('post','resource/resource_notificacoes.php', true);
		req.onreadystatechange = function(){
			if(this.status == 200 && this.readyState == 4) {
				rhDash.notificacoes.notifications = JSON.parse(this.responseText);
				rhDash.notificacoes.setNotifications();
			}
		}
		var formData = new FormData;
		formData.append('id',idCli);
		formData.append('op','1');
		req.send(formData);
		// ---------------------------------------------------------------------------------	
	},

	setUnreadNotifications: function(){			
		var qtd = ($('.not-status-1').length == '0') ? "":$('.not-status-1').length; // se for == 0, recebe string vazia
		$('#navbar-qtd-notificacao').html(qtd); // qtd notificacoes
	},

	setNotifications: function(){
		console.log('Notificações:');
		console.log(this.notifications);
		var notificacoesLista = document.getElementById('navbar-notificacoes');
		var notificacoes = '';
		if (this.notifications.length === 0){
			notificacoes = 
					'<div class="list-group-item notification-item empty-notification">'+
			                '<div class="text-center">'+
			                    '<div class="media-body mt-1 mb-1">'+
			                        '<p class="notification-text font-small-3 text-muted">Você não tem nenhuma notificação no momento</p>'+
			                    '</div>'+
			                '</div>'+
			        '</div>';
		} else {
			this.notifications.forEach(function(notif){
				var target = 'target="_blank"';
				if (notif.link === '' || notif.link === undefined){
					notif.link = '#';
					target = '';
				}
				switch(notif.icone){						
					case 'info':
						notificacoes+=
						'<div class="list-group-item notification-item not-status-'+notif.status+'" data-not-id="'+notif.id+'">'+
				            '<span class="notif-close"><i class="fa fa-times" aria-hidden="true"></i></span>'+
				            '<a href="'+notif.link+'" '+target+'>'+
				                '<div class="media">'+
				                    '<div class="media-left valign-middle">'+
				                        '<i class="ft-info icon-bg-square bg-cyan"></i>'+
				                    '</div>'+
				                    '<div class="media-body">'+
				                        '<h6 class="media-heading">'+notif.titulo+'</h6>'+
				                        '<p class="notification-text font-small-3 text-muted">'+notif.texto+'</p>'+
				                    '</div>'+
				                '</div>'+
				            '</a>'+
				        '</div>';
						break;
					case 'danger':
						notificacoes+=
						'<div class="list-group-item notification-item not-status-'+notif.status+'" data-not-id="'+notif.id+'">'+
				            '<span class="notif-close"><i class="fa fa-times" aria-hidden="true"></i></span>'+
				            '<a href="'+notif.link+'" '+target+'>'+
				                '<div class="media">'+
				                    '<div class="media-left valign-middle">'+
				                        '<i class="ft-alert-octagon icon-bg-square bg-red bg-darken-1"></i>'+
				                    '</div>'+
				                    '<div class="media-body">'+
				                        '<h6 class="media-heading red darken-1">'+notif.titulo+'</h6>'+
				                        '<p class="notification-text font-small-3 text-muted">'+notif.texto+'</p>'+
				                    '</div>'+
				                '</div>'+
				            '</a>'+
				        '</div>';
						break;
					case 'success':
						notificacoes+=
						'<div class="list-group-item notification-item not-status-'+notif.status+'" data-not-id="'+notif.id+'">'+
				            '<span class="notif-close"><i class="fa fa-times" aria-hidden="true"></i></span>'+
				            '<a href="'+notif.link+'" '+target+'>'+
				                '<div class="media">'+
				                    '<div class="media-left valign-middle">'+
				                        '<i class="ft-thumbs-up icon-bg-square bg-teal"></i>'+
				                    '</div>'+
				                    '<div class="media-body">'+
				                        '<h6 class="media-heading">'+notif.titulo+'</h6>'+
				                        '<p class="notification-text font-small-3 text-muted">'+notif.texto+'</p>'+
				                    '</div>'+
				                '</div>'+
				            '</a>'+
				        '</div>';
						break;
					case 'warning':
						notificacoes+=
						'<div class="list-group-item notification-item not-status-'+notif.status+'" data-not-id="'+notif.id+'">'+
				            '<span class="notif-close"><i class="fa fa-times" aria-hidden="true"></i></span>'+
				            '<a href="'+notif.link+'" '+target+'>'+
				                '<div class="media">'+
				                    '<div class="media-left valign-middle">'+
				                        '<i class="ft-alert-triangle icon-bg-square bg-yellow bg-darken-3"></i>'+
				                    '</div>'+
				                    '<div class="media-body">'+
				                        '<h6 class="media-heading">'+notif.titulo+'</h6>'+
				                        '<p class="notification-text font-small-3 text-muted">'+notif.texto+'</p>'+
				                    '</div>'+
				                '</div>'+
				            '</a>'+
				        '</div>';
						break;
					case 'time':
						notificacoes+=
						'<div class="list-group-item notification-item not-status-'+notif.status+'" data-not-id="'+notif.id+'">'+
				            '<span class="notif-close"><i class="fa fa-times" aria-hidden="true"></i></span>'+
				            '<a href="'+notif.link+'" '+target+'>'+
				                '<div class="media">'+
				                    '<div class="media-left valign-middle">'+
				                        '<i class="ft-clock icon-bg-square bg-cyan"></i>'+
				                    '</div>'+
				                    '<div class="media-body">'+
				                        '<h6 class="media-heading">'+notif.titulo+'</h6>'+
				                        '<p class="notification-text font-small-3 text-muted">'+notif.texto+'</p>'+
				                    '</div>'+
				                '</div>'+
				            '</a>'+
				        '</div>';
						break;
					default:
						notificacoes+=
						'<div class="list-group-item notification-item not-status-'+notif.status+'" data-not-id="'+notif.id+'">'+
				            '<span class="notif-close"><i class="fa fa-times" aria-hidden="true"></i></span>'+
				            '<a href="'+notif.link+'" '+target+'>'+
				                '<div class="media">'+
				                    '<div class="media-left valign-middle">'+
				                        '<i class="ft-info icon-bg-square bg-cyan"></i>'+
				                    '</div>'+
				                    '<div class="media-body">'+
				                        '<h6 class="media-heading">'+notif.titulo+'</h6>'+
				                        '<p class="notification-text font-small-3 text-muted">'+notif.texto+'</p>'+
				                    '</div>'+
				                '</div>'+
				            '</a>'+
				        '</div>';
						break;
				}
			});
		}


		notificacoesLista.innerHTML = notificacoes;

		// adicionando evento pra quando fechar a notificacao
		$('.notif-close').click(function(){
			var notificacao = this.parentElement;
			var idNot = $(notificacao).attr('data-not-id');

			$.ajax({
				type: 'post',
				url: 'resource/resource_notificacoes.php',
				data: {'id': idCli, 'idNot':idNot,'op':3},
				success: function(response){
					//console.log(response);
				}
			});

			$(notificacao).css('opacity', '0');
			setTimeout(function(){
				// apagando elemento
				$(notificacao).remove();
				// alterando numero quantidade notificacoes no sino
				rhDash.notificacoes.setUnreadNotifications();
			},300);
		});

		// adicionando evento para quando clicar na notificacao
		$('.notification-item a').click(function(){
			var notificacao = this.parentElement;
			var idNot = $(notificacao).attr('data-not-id');
			$(notificacao).removeClass('not-status-1');
			$.ajax({
				type: 'post',
				url: 'resource/resource_notificacoes.php',
				data: {'id': idCli, 'idNot':idNot,'op':2},
				success: function(response){
					//console.log(response);
				}
			});
			// alterando numero quantidade notificacoes no sino
			rhDash.notificacoes.setUnreadNotifications();
		});

		// alterando numero quantidade notificacoes no sino
		this.setUnreadNotifications();
	},

	init: function(){
		this.getNotifications();
	}
}

// Datas e afins
rhDash.data = {
	// funcao q retorna quantos dias tem no mes
    daysInMonth: function(month,year) {
        return new Date(year, month, 0).getDate();
    },

    // retorna últimos 8 dias
    last7Days: function(){
        var t = new Date();
        // se o mes n tiver mais q 9 dias ainda
        if (t.getDate() < 9){
            var ano = t.getFullYear();
            var mes = t.getMonth() - 1;

            // verifica se ta no começo de janeiro
            if (t.getMonth() === 0){
                ano -= 1;
            }
            var diasNoMes = this.daysInMonth(mes,ano); // verifica quantos dias que tem no mês anterior

            var dia = ( 8 - t.getDate());
            var dia = diasNoMes - dia;

            var dataInicial = (dia > 9 ? dia : "0" + dia)+'/'+(mes < 10 ? "0" : "" ) + (mes+1)+'/'+ano;
        } else {
            var dataInicial = ((t.getDate() - 8) < 10 ? "0" : "") + (t.getDate() - 8)+'/'+(t.getMonth() < 9 ? "0" : "" ) + (t.getMonth()+1)+'/'+t.getFullYear()
        }

        return {
            'inicio':dataInicial,
            'fim': ((t.getDate() - 1) < 10 ? "0" : "") + (t.getDate() - 1)+'/'+(t.getMonth() < 9 ? "0" : "" ) + (t.getMonth()+1)+'/'+t.getFullYear()
        };
    }
}

// INICIALIZANDO DATEPICKER COM A DATA DE HOJE  
    // caso n seja uma data no localSorage, retorna os ultimos 7 dias
    if (typeof window.localStorage['datapicker'] !== 'undefined'){
        if (isNaN(window.localStorage['datapicker'].split(' - ')[0].replace(/\//g,'')) || 
            isNaN(window.localStorage['datapicker'].split(' - ')[1].replace(/\//g,''))){
            var dataInicio = rhDash.data.last7Days()['inicio'];
            var dataFim = rhDash.data.last7Days()['fim'];
        } else {            
            var dataInicio = window.localStorage['datapicker'].split(' - ')[0];                
            dataInicio = dataInicio.replace(/-/g,'/');            
            var dataFim = window.localStorage['datapicker'].split(' - ')[1];                
            dataFim = dataFim.replace(/-/g,'/');
        }
    }

    
    $('#dateRangeDados').daterangepicker({
        locale: {
            format: 'DD/MM/YYYY',
            separator: " - ",
            applyLabel: "Aplicar",
            cancelLabel: "Cancelar",
            daysOfWeek: rhDaysOfWeek,
            monthNames: rhMonthNames
        },
        "startDate":dataInicio,
        "endDate": dataFim
    }, function(start, end, label) {
        setTimeout(function(){
        	var d = $('#dateRangeDados').val();
            localStorage.setItem('datapicker',d);
            carregaDados(d);
        }, 200);
        console.log("New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')");        
    });

    // VARIÁVEIS GLOBAIS DATAPICKER--------------------------
    // datapicker
    if(document.getElementById('dateRangeDados')){
		window['dataInicioGrafico'] = dataInicio || rhDash.data.last7Days()['inicio'];
		window['dataFimGrafico'] = dataFim || rhDash.data.last7Days()['fim'];
		window['dateRange'] =$('#dateRangeDados').val();
    }
//-------------------------------------------------------

// inicializa dash
rhDash.init();


$(document).ready(function(){
	// inicializando dash
	// JAVASCRIPT PARA OS SWITCHS DOS FORMS FUNCIONAREM---------------------------------
	(function(window, document, $) {
	  'use strict';
	  var $html = $('html');

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
	        $.each( elems, function( key, value ) {
	            var $size="", $color="",$sizeClass="", $colorCode="";
	            $size = $(this).data('size');
	            var $sizes ={
	                'lg' : "large",
	                'sm' : "small",
	                'xs' : "xsmall"
	            };
	            if($(this).data('size')!== undefined){
	                $sizeClass = "switchery switchery-"+$sizes[$size];
	            }
	            else{
	                $sizeClass = "switchery";
	            }

	            $color = $(this).data('color');
	            var $colors ={
	                'primary' : "#967ADC",
	                'success' : "#37BC9B",
	                'danger' : "#DA4453",
	                'warning' : "#F6BB42",
	                'info' : "#3BAFDA"
	            };
	            if($color !== undefined){
	                $colorCode = $colors[$color];
	            }
	            else{
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
	    /*  Toggle Ends   */

	})(window, document, jQuery);
    $('.switch').change(function() {
        this.value = this.checked;                                                  
    });

    $('.input-group-addon .fa-calendar').on('click', function(){
        this.parentElement.parentElement.getElementsByTagName('input')[0].click();
    });
	// ---------------------------------------------------------------------------------

	// mensagem para o suporte do menu rápido-------------------------------------------
	$('#btn-msg-suporte').click(function(){
		var container = $('#btn-msg-suporte').parent();
		var msg = $('#form-suporte-mensagem').val();

		bloqueiaElemento(container);
		if (msg === '') {
			toastr['warning']('Escreva uma mensagem');
			desbloqueiaElemento(container);
			
		} else {
			$.ajax({
				
				type:'post',
				url:'resource/resource_enviar_ajuda.php',
				data:{mensagem: msg},
				success: function(response){
					console.log(response);
					toastr['success']('Ticket aberto, aguarde a resposta do suporte');
					desbloqueiaElemento(container);
					$('#form-suporte-mensagem').val('');
				}
			});
		}
	});
	//----------------------------------------------------------------------------------
});


/*!
 * jQuery blockUI plugin
 * Version 2.70.0-2014.11.23
 * Requires jQuery v1.7 or later
 *
 * Examples at: http://malsup.com/jquery/block/
 * Copyright (c) 2007-2013 M. Alsup
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
 * Thanks to Amir-Hossein Sobhi for some excellent contributions!
 */

!function(){"use strict";function e(e){function t(t,n){var s,h,k=t==window,y=n&&void 0!==n.message?n.message:void 0;if(n=e.extend({},e.blockUI.defaults,n||{}),!n.ignoreIfBlocked||!e(t).data("blockUI.isBlocked")){if(n.overlayCSS=e.extend({},e.blockUI.defaults.overlayCSS,n.overlayCSS||{}),s=e.extend({},e.blockUI.defaults.css,n.css||{}),n.onOverlayClick&&(n.overlayCSS.cursor="pointer"),h=e.extend({},e.blockUI.defaults.themedCSS,n.themedCSS||{}),y=void 0===y?n.message:y,k&&p&&o(window,{fadeOut:0}),y&&"string"!=typeof y&&(y.parentNode||y.jquery)){var m=y.jquery?y[0]:y,v={};e(t).data("blockUI.history",v),v.el=m,v.parent=m.parentNode,v.display=m.style.display,v.position=m.style.position,v.parent&&v.parent.removeChild(m)}e(t).data("blockUI.onUnblock",n.onUnblock);var g,I,w,U,x=n.baseZ;g=e(r||n.forceIframe?'<iframe class="blockUI" style="z-index:'+x++ +';display:none;border:none;margin:0;padding:0;position:absolute;width:100%;height:100%;top:0;left:0" src="'+n.iframeSrc+'"></iframe>':'<div class="blockUI" style="display:none"></div>'),I=e(n.theme?'<div class="blockUI blockOverlay ui-widget-overlay" style="z-index:'+x++ +';display:none"></div>':'<div class="blockUI blockOverlay" style="z-index:'+x++ +';display:none;border:none;margin:0;padding:0;width:100%;height:100%;top:0;left:0"></div>'),n.theme&&k?(U='<div class="blockUI '+n.blockMsgClass+' blockPage ui-dialog ui-widget ui-corner-all" style="z-index:'+(x+10)+';display:none;position:fixed">',n.title&&(U+='<div class="ui-widget-header ui-dialog-titlebar ui-corner-all blockTitle">'+(n.title||"&nbsp;")+"</div>"),U+='<div class="ui-widget-content ui-dialog-content"></div>',U+="</div>"):n.theme?(U='<div class="blockUI '+n.blockMsgClass+' blockElement ui-dialog ui-widget ui-corner-all" style="z-index:'+(x+10)+';display:none;position:absolute">',n.title&&(U+='<div class="ui-widget-header ui-dialog-titlebar ui-corner-all blockTitle">'+(n.title||"&nbsp;")+"</div>"),U+='<div class="ui-widget-content ui-dialog-content"></div>',U+="</div>"):U=k?'<div class="blockUI '+n.blockMsgClass+' blockPage" style="z-index:'+(x+10)+';display:none;position:fixed"></div>':'<div class="blockUI '+n.blockMsgClass+' blockElement" style="z-index:'+(x+10)+';display:none;position:absolute"></div>',w=e(U),y&&(n.theme?(w.css(h),w.addClass("ui-widget-content")):w.css(s)),n.theme||I.css(n.overlayCSS),I.css("position",k?"fixed":"absolute"),(r||n.forceIframe)&&g.css("opacity",0);var C=[g,I,w],S=e(k?"body":t);e.each(C,function(){this.appendTo(S)}),n.theme&&n.draggable&&e.fn.draggable&&w.draggable({handle:".ui-dialog-titlebar",cancel:"li"});var O=f&&(!e.support.boxModel||e("object,embed",k?null:t).length>0);if(u||O){if(k&&n.allowBodyStretch&&e.support.boxModel&&e("html,body").css("height","100%"),(u||!e.support.boxModel)&&!k)var E=d(t,"borderTopWidth"),T=d(t,"borderLeftWidth"),M=E?"(0 - "+E+")":0,B=T?"(0 - "+T+")":0;e.each(C,function(e,t){var o=t[0].style;if(o.position="absolute",2>e)k?o.setExpression("height","Math.max(document.body.scrollHeight, document.body.offsetHeight) - (jQuery.support.boxModel?0:"+n.quirksmodeOffsetHack+') + "px"'):o.setExpression("height",'this.parentNode.offsetHeight + "px"'),k?o.setExpression("width",'jQuery.support.boxModel && document.documentElement.clientWidth || document.body.clientWidth + "px"'):o.setExpression("width",'this.parentNode.offsetWidth + "px"'),B&&o.setExpression("left",B),M&&o.setExpression("top",M);else if(n.centerY)k&&o.setExpression("top",'(document.documentElement.clientHeight || document.body.clientHeight) / 2 - (this.offsetHeight / 2) + (blah = document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop) + "px"'),o.marginTop=0;else if(!n.centerY&&k){var i=n.css&&n.css.top?parseInt(n.css.top,10):0,s="((document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop) + "+i+') + "px"';o.setExpression("top",s)}})}if(y&&(n.theme?w.find(".ui-widget-content").append(y):w.append(y),(y.jquery||y.nodeType)&&e(y).show()),(r||n.forceIframe)&&n.showOverlay&&g.show(),n.fadeIn){var j=n.onBlock?n.onBlock:c,H=n.showOverlay&&!y?j:c,z=y?j:c;n.showOverlay&&I._fadeIn(n.fadeIn,H),y&&w._fadeIn(n.fadeIn,z)}else n.showOverlay&&I.show(),y&&w.show(),n.onBlock&&n.onBlock.bind(w)();if(i(1,t,n),k?(p=w[0],b=e(n.focusableElements,p),n.focusInput&&setTimeout(l,20)):a(w[0],n.centerX,n.centerY),n.timeout){var W=setTimeout(function(){k?e.unblockUI(n):e(t).unblock(n)},n.timeout);e(t).data("blockUI.timeout",W)}}}function o(t,o){var s,l=t==window,a=e(t),d=a.data("blockUI.history"),c=a.data("blockUI.timeout");c&&(clearTimeout(c),a.removeData("blockUI.timeout")),o=e.extend({},e.blockUI.defaults,o||{}),i(0,t,o),null===o.onUnblock&&(o.onUnblock=a.data("blockUI.onUnblock"),a.removeData("blockUI.onUnblock"));var r;r=l?e("body").children().filter(".blockUI").add("body > .blockUI"):a.find(">.blockUI"),o.cursorReset&&(r.length>1&&(r[1].style.cursor=o.cursorReset),r.length>2&&(r[2].style.cursor=o.cursorReset)),l&&(p=b=null),o.fadeOut?(s=r.length,r.stop().fadeOut(o.fadeOut,function(){0===--s&&n(r,d,o,t)})):n(r,d,o,t)}function n(t,o,n,i){var s=e(i);if(!s.data("blockUI.isBlocked")){t.each(function(e,t){this.parentNode&&this.parentNode.removeChild(this)}),o&&o.el&&(o.el.style.display=o.display,o.el.style.position=o.position,o.el.style.cursor="default",o.parent&&o.parent.appendChild(o.el),s.removeData("blockUI.history")),s.data("blockUI.static")&&s.css("position","static"),"function"==typeof n.onUnblock&&n.onUnblock(i,n);var l=e(document.body),a=l.width(),d=l[0].style.width;l.width(a-1).width(a),l[0].style.width=d}}function i(t,o,n){var i=o==window,l=e(o);if((t||(!i||p)&&(i||l.data("blockUI.isBlocked")))&&(l.data("blockUI.isBlocked",t),i&&n.bindEvents&&(!t||n.showOverlay))){var a="mousedown mouseup keydown keypress keyup touchstart touchend touchmove";t?e(document).bind(a,n,s):e(document).unbind(a,s)}}function s(t){if("keydown"===t.type&&t.keyCode&&9==t.keyCode&&p&&t.data.constrainTabKey){var o=b,n=!t.shiftKey&&t.target===o[o.length-1],i=t.shiftKey&&t.target===o[0];if(n||i)return setTimeout(function(){l(i)},10),!1}var s=t.data,a=e(t.target);return a.hasClass("blockOverlay")&&s.onOverlayClick&&s.onOverlayClick(t),a.parents("div."+s.blockMsgClass).length>0?!0:0===a.parents().children().filter("div.blockUI").length}function l(e){if(b){var t=b[e===!0?b.length-1:0];t&&t.focus()}}function a(e,t,o){var n=e.parentNode,i=e.style,s=(n.offsetWidth-e.offsetWidth)/2-d(n,"borderLeftWidth"),l=(n.offsetHeight-e.offsetHeight)/2-d(n,"borderTopWidth");t&&(i.left=s>0?s+"px":"0"),o&&(i.top=l>0?l+"px":"0")}function d(t,o){return parseInt(e.css(t,o),10)||0}e.fn._fadeIn=e.fn.fadeIn;var c=e.noop||function(){},r=/MSIE/.test(navigator.userAgent),u=/MSIE 6.0/.test(navigator.userAgent)&&!/MSIE 8.0/.test(navigator.userAgent),f=(document.documentMode||0,e.isFunction(document.createElement("div").style.setExpression));e.blockUI=function(e){t(window,e)},e.unblockUI=function(e){o(window,e)},e.growlUI=function(t,o,n,i){var s=e('<div class="growlUI"></div>');t&&s.append("<h1>"+t+"</h1>"),o&&s.append("<h2>"+o+"</h2>"),void 0===n&&(n=3e3);var l=function(t){t=t||{},e.blockUI({message:s,fadeIn:"undefined"!=typeof t.fadeIn?t.fadeIn:700,fadeOut:"undefined"!=typeof t.fadeOut?t.fadeOut:1e3,timeout:"undefined"!=typeof t.timeout?t.timeout:n,centerY:!1,showOverlay:!1,onUnblock:i,css:e.blockUI.defaults.growlCSS})};l();s.css("opacity");s.mouseover(function(){l({fadeIn:0,timeout:3e4});var t=e(".blockMsg");t.stop(),t.fadeTo(300,1)}).mouseout(function(){e(".blockMsg").fadeOut(1e3)})},e.fn.block=function(o){if(this[0]===window)return e.blockUI(o),this;var n=e.extend({},e.blockUI.defaults,o||{});return this.each(function(){var t=e(this);n.ignoreIfBlocked&&t.data("blockUI.isBlocked")||t.unblock({fadeOut:0})}),this.each(function(){"static"==e.css(this,"position")&&(this.style.position="relative",e(this).data("blockUI.static",!0)),this.style.zoom=1,t(this,o)})},e.fn.unblock=function(t){return this[0]===window?(e.unblockUI(t),this):this.each(function(){o(this,t)})},e.blockUI.version=2.7,e.blockUI.defaults={message:"<h1>Please wait...</h1>",title:null,draggable:!0,theme:!1,css:{padding:0,margin:0,width:"30%",top:"40%",left:"35%",textAlign:"center",color:"#000",border:"3px solid #aaa",backgroundColor:"#fff",cursor:"wait"},themedCSS:{width:"30%",top:"40%",left:"35%"},overlayCSS:{backgroundColor:"#000",opacity:.6,cursor:"wait"},cursorReset:"default",growlCSS:{width:"350px",top:"10px",left:"",right:"10px",border:"none",padding:"5px",opacity:.6,cursor:"default",color:"#fff",backgroundColor:"#000","-webkit-border-radius":"10px","-moz-border-radius":"10px","border-radius":"10px"},iframeSrc:/^https/i.test(window.location.href||"")?"javascript:false":"about:blank",forceIframe:!1,baseZ:1e3,centerX:!0,centerY:!0,allowBodyStretch:!0,bindEvents:!0,constrainTabKey:!0,fadeIn:200,fadeOut:400,timeout:0,showOverlay:!0,focusInput:!0,focusableElements:":input:enabled:visible",onBlock:null,onUnblock:null,onOverlayClick:null,quirksmodeOffsetHack:4,blockMsgClass:"blockMsg",ignoreIfBlocked:!1};var p=null,b=[]}"function"==typeof define&&define.amd&&define.amd.jQuery?define(["jquery"],e):e(jQuery)}();

// FUNÇÃO GLOBAL PARA BLOQUEAR UM ELEMENTO DURANTE UMA REQUISICAO
var bloqueiaElemento = function(el,op,msg){
	switch(op){
		case 'desabilita':
			$(el).block({
		        message: '<p class="text-center">'+msg+'</p><div class="fa fa-ban"></div>',
		        overlayCSS: {
		            backgroundColor: '#fff',
		            opacity: 0.6,
		            cursor: 'not-allowed'
		        },
		        css: {
		            border: 0,
		            padding: 0,
		            backgroundColor: 'transparent'
		        }
		    });
			break;
		default:
			$(el).block({
		        message: '<div class="ft-refresh-cw icon-spin font-medium-2"></div>',
		        overlayCSS: {
		            backgroundColor: '#fff',
		            opacity: 0.8,
		            cursor: 'wait'
		        },
		        css: {
		            border: 0,
		            padding: 0,
		            backgroundColor: 'transparent'
		        }
		    });
			break;
	}
	    
}
// DESBLOQUEIA ELEMENTO
var desbloqueiaElemento = function(el){
    $(el).unblock();
}

// FUNÇÃO GLOBAL PARA BLOQUEAR A TELA DURANTE UMA REQUISICAO
var bloqueiaTela = function(){
	var html = document.getElementsByTagName('html')[0];
	html.style.overflow = "hidden";
	$(html).block({
        message: '<div class="ft-refresh-cw icon-spin font-large-2 white"></div>',
        overlayCSS: {
            backgroundColor: '#000',
            opacity: 0.8,
            cursor: 'wait',
            color: '#fff',
            position:'fixed',
            top:'0',
            left:'0',
        },
        css: {
            border: 0,
            padding: 0,
            backgroundColor: 'transparent'
        }
    });
}

var desbloqueiaTela = function(){
	var html = document.getElementsByTagName('html')[0];
	html.style.overflow = "initial";
	$(html).unblock();
}

/// TOOLTIP ------------------------------------------------------------
window['iniciaTooltip'] = function(){	
	'use strict';

	/******************/
	// Tooltip events //
	/******************/

	// onShow event
	$('#show-tooltip').tooltip({
		title: 'Tooltip Show Event',
		trigger: 'click',
		placement: 'right'
		}).on('show.bs.tooltip', function() {
			alert('Show event fired.');
	});

	// onShown event
	$('#shown-tooltip').tooltip({
		title: 'Tooltip Shown Event',
		trigger: 'click',
		placement: 'top'
	}).on('shown.bs.tooltip', function() {
		alert('Shown event fired.');
	});

	// onHide event
	$('#hide-tooltip').tooltip({
		title: 'Tooltip Hide Event',
		trigger: 'click',
		placement: 'bottom'
	}).on('hide.bs.tooltip', function() {
		alert('Hide event fired.');
	});

	// onHidden event
	$('#hidden-tooltip').tooltip({
		title: 'Tooltip Hidden Event',
		trigger: 'click',
		placement: 'left'
	}).on('hidden.bs.tooltip', function() {
		alert('Hidden event fired.');
	});


	/*******************/
	// Tooltip methods //
	/*******************/

	// Show method
	$('#show-method').on('click', function() {
		$(this).tooltip('show');
	});
	// Hide method
	$('#hide-method').on('mouseenter', function() {
		$(this).tooltip('show');
	});
	$('#hide-method').on('click', function() {
		$(this).tooltip('hide');
	});
	// Toggle method
	$('#toggle-method').on('click', function() {
		$(this).tooltip('toggle');
	});
	// Dispose method
	$('#dispose').on('click', function() {
		$('#dispose-method').tooltip('dispose');
	});

	/* Trigger*/
	$('.manual').on('click', function() {
		$(this).tooltip('show');
	});
	$('.manual').on('mouseout', function() {
		$(this).tooltip('hide');
	});

	/* Default template */
	$(".template").on('click', function(){
		console.log(
			'<div class="tooltip" role="tooltip">' +
			'<div class="tooltip-arrow"></div>' +
			'<div class="tooltip-inner"></div>' +
			'</div>'
		);
	});
}

// Conversão para a moeda corrente do cliente

var toReais = function(value) {

    var
    	currency  = window['currencyCli'] ? window['currencyCli'] : 'R$',
        pattern_1 = /\.?(\d{1,2})$/g,
        pattern_2 = /(\d)(?=(\d{3})+(?!\d))/g;
    
    if (value) {

    	if (value === 'GRÁTIS' || value === 'CPA/ROI'){
            	
        	value = 'GRÁTIS';

        } else {
        
	        if (typeof value === 'string') {

	            value = parseFloat(value);
	        }

	        value = value.toFixed(2);
	        
	        if (currency === 'R$') {

		        value = value.replace(pattern_1, ',$1');
		        value = value.replace(pattern_2, '$1.');
	        
	        } else {

	        	value = value.replace(pattern_1, '.$1');
		        value = value.replace(pattern_2, '$1,');
	        }
	    }

    } else {

        value = currency === 'R$' ? '0,00' : '0.00';
    }

    return currency + ' ' + value;
}

$(document).ready(function(){
	iniciaTooltip();
});

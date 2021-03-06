/*
--	Autor: Eliabe
--	Data: 08/2017
--	Update: 19/02/2018
--	Desc: Script que carrega os widgets do cliente com a opção de editar
--
-- 	SCRITPS MAIS PESADOS E REQUISIÇÕES POR ÚLTIMO
*/

var bossChoiceProdId     = 0,
    bossChoiceProdTitulo = ''

$( document ).ready( function ()
{
  // gambiarra ========================================
  'use strict'
  !function ( a ) { 'function' == typeof define && define.amd ? define( [ 'jquery' ], a ) : 'object' == typeof exports ? module.exports = a( require( 'jquery' ) ) : a( jQuery ) }( function ( a )
  {
    function b( b, c )
    {
      this.element = b, this.$element = a( b )
      var d = this.$element.data()
      return '' === d.reverse && ( d.reverse = !0 ), '' === d.switchAlways && ( d.switchAlways = !0 ), '' === d.html && ( d.html = !0 ), this.options = a.extend( {}, a.fn.checkboxpicker.defaults, c, d ), this.$element.closest( 'label' ).length ? void console.warn( this.options.warningMessage ) : ( this.$group = a.create( 'div' ), this.$buttons = a.create( 'a', 'a' ), this.$off = this.$buttons.eq( this.options.reverse ? 1 : 0 ), this.$on = this.$buttons.eq( this.options.reverse ? 0 : 1 ), void this.init() )
    }

    a.create = function () { return a( a.map( arguments, a.proxy( document, 'createElement' ) ) ) }, b.prototype = {
      init: function ()
      {
        var b = this.options.html ? 'html' : 'text'
        this.$element.addClass( 'hidden' ), this.$group.addClass( this.options.baseGroupCls ).addClass( this.options.groupCls ), this.$buttons.addClass( this.options.baseCls ).addClass( this.options.cls ), this.options.offLabel && this.$off[ b ]( this.options.offLabel ), this.options.onLabel && this.$on[ b ]( this.options.onLabel ), this.options.offIconCls && ( this.options.offLabel && this.$off.prepend( '&nbsp;' ), a.create( 'span' ).addClass( this.options.iconCls ).addClass( this.options.offIconCls ).prependTo( this.$off ) ), this.options.onIconCls && ( this.options.onLabel && this.$on.prepend( '&nbsp;' ), a.create( 'span' ).addClass( this.options.iconCls ).addClass( this.options.onIconCls ).prependTo( this.$on ) ), this.element.checked ? ( this.$on.addClass( 'active' ), this.$on.addClass( this.options.onActiveCls ), this.$off.addClass( this.options.offCls ) ) : ( this.$off.addClass( 'active' ), this.$off.addClass( this.options.offActiveCls ), this.$on.addClass( this.options.onCls ) ), this.element.title ? this.$group.attr( 'title', this.element.title ) : ( this.options.offTitle && this.$off.attr( 'title', this.options.offTitle ), this.options.onTitle && this.$on.attr( 'title', this.options.onTitle ) ), this.$group.on( 'keydown', a.proxy( this, 'keydown' ) ), this.$buttons.on( 'click', a.proxy( this, 'click' ) ), this.$element.on( 'change', a.proxy( this, 'toggleChecked' ) ), a( this.element.labels ).on( 'click', a.proxy( this, 'focus' ) ), a( this.element.form ).on( 'reset', a.proxy( this, 'reset' ) ), this.$group.append( this.$buttons ).insertAfter( this.element ), this.element.disabled ? ( this.$buttons.addClass( 'disabled' ), this.options.disabledCursor && this.$group.css( 'cursor', this.options.disabledCursor ) ) : ( this.$group.attr( 'tabindex', this.element.tabIndex ), this.element.autofocus && this.focus() )
      },
      toggleChecked: function () { this.$buttons.toggleClass( 'active' ), this.$off.toggleClass( this.options.offCls ), this.$off.toggleClass( this.options.offActiveCls ), this.$on.toggleClass( this.options.onCls ), this.$on.toggleClass( this.options.onActiveCls ) },
      toggleDisabled: function () { this.$buttons.toggleClass( 'disabled' ), this.element.disabled ? ( this.$group.attr( 'tabindex', this.element.tabIndex ), this.$group.css( 'cursor', '' ) ) : ( this.$group.removeAttr( 'tabindex' ), this.options.disabledCursor && this.$group.css( 'cursor', this.options.disabledCursor ) ) },
      focus: function () { this.$group.trigger( 'focus' ) },
      click: function ( b )
      {
        var c = a( b.currentTarget )
        c.hasClass( 'active' ) && !this.options.switchAlways || this.change()
      },
      change: function () { this.set( !this.element.checked ) },
      set: function ( a ) { this.element.checked = a, this.$element.trigger( 'change' ) },
      keydown: function ( b ) { -1 != a.inArray( b.keyCode, this.options.toggleKeyCodes ) ? ( b.preventDefault(), this.change() ) : 13 == b.keyCode && a( this.element.form ).trigger( 'submit' ) },
      reset: function () { ( this.element.defaultChecked && this.$off.hasClass( 'active' ) || !this.element.defaultChecked && this.$on.hasClass( 'active' ) ) && this.set( this.element.defaultChecked ) }
    }
    var c = a.extend( {}, a.propHooks )
    a.extend( a.propHooks, {
      checked: {
        set: function ( b, d )
        {
          var e = a.data( b, 'bs.checkbox' )
          e && b.checked != d && e.change( d ), c.checked && c.checked.set && c.checked.set( b, d )
        }
      }, disabled: {
        set: function ( b, d )
        {
          var e = a.data( b, 'bs.checkbox' )
          e && b.disabled != d && e.toggleDisabled(), c.disabled && c.disabled.set && c.disabled.set( b, d )
        }
      }
    } )
    var d = a.fn.checkboxpicker
    return a.fn.checkboxpicker = function ( c, d )
    {
      var e
      return e = this instanceof a ? this : a( 'string' == typeof c ? c : d ), e.each( function ()
      {
        var d = a.data( this, 'bs.checkbox' )
        d || ( d = new b( this, c ), a.data( this, 'bs.checkbox', d ) )
      } )
    }, a.fn.checkboxpicker.defaults = {
      baseGroupCls: 'btn-group',
      baseCls: 'btn',
      groupCls: null,
      cls: null,
      offCls: 'btn-default',
      onCls: 'btn-default',
      offActiveCls: 'btn-danger',
      onActiveCls: 'btn-success',
      offLabel: 'No',
      onLabel: 'Yes',
      offTitle: !1,
      onTitle: !1,
      iconCls: 'glyphicon',
      disabledCursor: 'not-allowed',
      toggleKeyCodes: [ 13, 32 ],
      warningMessage: 'Please do not use Bootstrap-checkbox element in label element.'
    }, a.fn.checkboxpicker.Constructor = b, a.fn.checkboxpicker.noConflict = function () { return a.fn.checkboxpicker = d, this }, a.fn.checkboxpicker
  } )
  // fecha gambiarra ==================================

  var autoCompleteDivStyle       = document.createElement( 'style' )
  autoCompleteDivStyle.innerHTML = '.list-group-hover .list-group-item:hover {background-color: #f5f5ff;} .autocomplete-overlay {box-shadow: rgba(0, 0, 0, 0.3) 2px 2px 3px 0.5px; display: none;max-height:210px;overflow:auto;position:absolute; width: 95%; z-index:100; padding: 0px;}'
  document.head.appendChild( autoCompleteDivStyle )


  // OBJ PRINCIPAL WIDGETS
  var
    htmlCfgCompreJunto = '',
    htmlHide           = '',
    htmlShow           = '',
    widgets            = {
      inicia: function ()
      {
        $.ajax( {
          type: 'POST',
          url: 'resource/resource_widget_edit.php',
          data: { 'idCli': idCli, 'op': 1 },
          success: function ( result )
          {
            //console.log(result);
            var widgetInfo = null
            if ( typeof ( result ) === 'string' )
              widgetInfo = JSON.parse( result )
            else
              widgetInfo = result

            window[ '__home' ]      = widgetInfo.widgetsHome
            window[ '__produto' ]   = widgetInfo.widgetsProduto
            window[ '__busca' ]     = widgetInfo.widgetsBusca
            window[ '__categoria' ] = widgetInfo.widgetsCategoria
            window[ '__carrinho' ]  = widgetInfo.widgetsCarrinho
            window[ '__basicos' ]   = widgetInfo.widgetsBasicos
            window[ '__busca' ]     = widgetInfo.widgetsBusca
            window[ 'busca_be' ]    = widgetInfo.busca_be
            window[ 'template' ]    = widgetInfo.template

            // INICIA AS FUNÇÕES PRINCIPAIS
            widgets.checaPlano()
            widgets.carregaHome()
            widgets.carregaProduto()
            widgets.carregaCarrinho()
            widgets.carregaCategoria()
            // widgets.carregaBusca();
            widgets.caregaBasicos()
            widgets.botoesEditar()
            widgets.botoesSalvar()
            widgets.toggleSwitches()

            console.log( '-------- Widgets Básicos --------' )
            console.log( __basicos )
            console.log( '-------- Widgets Home -----------' )
            console.log( __home )
            console.log( '-------- Widgets Produto --------' )
            console.log( __produto )
            console.log( '-------- Widgets Categoria ------' )
            console.log( __categoria )
            console.log( '-------- Widgets Carrinho -------' )
            console.log( __carrinho )
            console.log( '-------- Widgets Busca -------' )
            console.log( __busca )
          }
        } )
      },

      checaPlano: function ()
      {
        switch ( idPlan ) {
          case '0': //TRIAL
            $( '#coluna-widgets-categoria' ).show()
            break
          case '1': //FREE
            $( '#coluna-widgets-categoria' ).remove()
            break
          case '2': //STARTUP
            $( '#coluna-widgets-categoria' ).remove()
            break
          case '3': //PRO
            $( '#coluna-widgets-categoria' ).show()
            break
          case '4': //ROCKET
            $( '#coluna-widgets-categoria' ).show()
            break
          case '42': //PREMIUM
            $( '#coluna-widgets-categoria' ).show()
            $( '#coluna-widgets-carrinho' ).show()
            break
        }

        if ( __home.length == 0 ) $( '#coluna-widgets-home' ).hide()
        if ( __produto.length == 0 ) $( '#coluna-widgets-produto' ).hide()
        if ( __busca.length == 0 ) $( '#coluna-widgets-busca' ).hide()
        if ( __categoria.length == 0 ) $( '#coluna-widgets-categoria' ).hide()
        if ( __carrinho.length == 0 ) $( '#coluna-widgets-carrinho' ).hide()
        if ( __basicos.length == 0 ) $( '#coluna-widgets-basicos' ).hide()
      },

      caregaBasicos: function ()
      {
        var widgetsBasicos       = document.getElementById( 'widgetsBasicos' )
        widgetsBasicos.innerHTML = ''

        __basicos.forEach( function ( wid )
        {
          var ativo     = ( wid.ativo === '1' ) ? 'checked' : ''
          var btnEditar = wid.inteligencia == 43 ? '' : '<button class="btn btn-info pull-right mr-1 ml-1 btn-edita-wid"><i class="icon-pencil"></i> Editar</button>'

          widgetsBasicos.innerHTML = widgetsBasicos.innerHTML +
            '<li class="list-group-item" wid-id="' + wid.id + '"><span>' + wid.nome + '</span>' +
            '<div style="width: auto;display: inline-block;position:relative;bottom: 7px;float:right;">' +
            '<!-- <button class="btn btn-danger pull-right" data-delete-wid=' + wid.id + '><i class="ft-x"></i> Deletar</button> -->' +
            btnEditar +
            '<input type="checkbox" class="switch pull-right" data-off-label="desativar" data-on-label="ativar" data-switch-always ' + ativo + '/>' +
            '</div>' +
            '</li>'
        } )
      },

      carregaHome: function ()
      {
        var widgetsHome       = document.getElementById( 'widgetsHome' )
        widgetsHome.innerHTML = ''

        __home.forEach( function ( wid )
        {
          var btn = ''

          if ( wid.inteligencia == 40 || // lançamentos da marca
            wid.inteligencia == 38 || // mais vendidos da marca manual
            wid.inteligencia == 25 || // palavra chave
            wid.inteligencia == 24 || // mais vendidos da categoria manual
            wid.inteligencia == 9 )    // manual
          {
            btn = '<button class="btn-duplica-wid btn btn-primary mr-1" data-int="' + wid.inteligencia + '"><i class="fa fa-plus"></i> Duplicar</button>'
          }

          var ativo             = ( wid.ativo === '1' ) ? 'checked' : ''
          widgetsHome.innerHTML = widgetsHome.innerHTML +
            '<li class="list-group-item" wid-id="' + wid.id + '"><span>' + wid.nome + '</span>' +
            '<div style="width: auto;display: inline-block;position:relative;bottom: 7px;float:right;">' +
            '<!-- <button class="btn btn-danger pull-right" data-delete-wid=' + wid.id + '><i class="ft-x"></i> Deletar</button> -->' +
            btn +
            '<button class="btn btn-info pull-right mr-1 ml-1 btn-edita-wid"><i class="icon-pencil"></i> Editar</button>' +
            '<input type="checkbox" class="switch pull-right" data-off-label="desativar" data-on-label="ativar" data-switch-always ' + ativo + '/>' +
            '</div>' +
            '</li>'
        } )
      },

      carregaProduto: function ()
      {
        var widgetsProduto       = document.getElementById( 'widgetsProduto' )
        widgetsProduto.innerHTML = ''
        __produto.forEach( function ( wid )
        {
          var ativo = ( wid.ativo === '1' ) ? 'checked' : ''

          widgetsProduto.innerHTML = widgetsProduto.innerHTML +
            '<li class="list-group-item" wid-id="' + wid.id + '"><span>' + wid.nome + '</span>' +
            '<div style="width: auto;display: inline-block;position:relative;bottom: 7px;float:right;">' +
            '<!-- <button class="btn btn-danger pull-right" data-delete-wid=' + wid.id + '><i class="ft-x"></i> Deletar</button> -->' +
            '<button class="btn btn-info pull-right mr-1 ml-1 btn-edita-wid"><i class="icon-pencil"></i> Editar</button>' +
            '<input type="checkbox" class="switch pull-right" data-off-label="desativar" data-on-label="ativar" data-switch-always ' + ativo + '/>' +
            '</div>' +
            '</li>'
        } )
      },

      carregaBusca: function ()
      {
        var widgetsBusca       = document.getElementById( 'widgetsBusca' )
        widgetsBusca.innerHTML = ''
        __busca.forEach( function ( wid )
        {

          if ( busca_be == '1' ) {
            widgetsBusca.innerHTML = widgetsBusca.innerHTML +
              '<li class="list-group-item" wid-id="' + wid.id + '"><span>' + wid.nome + '</span>' +
              '<div style="width: auto;display: inline-block;position:relative;bottom: 7px;float:right;">' +
              '<button class="btn btn-info pull-right mr-1 ml-1 btn-configura-busca"><i class="ft-cog"></i> Configurações</button>' +
              '<a class="white btn btn-info pull-right mr-1 ml-1" href="/dashboard/php/get_relatorio_busca.php/?id=' + idCli + '">' +
              '<i class="fa fa-table mr-1"></i>' +
              'Baixar relatório' +
              '</a>' +
              '</button>' +
              '</div>' +
              '</li>'
          } else {
            widgetsBusca.innerHTML = widgetsBusca.innerHTML +
              '<li class="list-group-item" wid-id="' + wid.id + '"><span>' + wid.nome + '</span>' +
              '<div style="width: auto;display: inline-block;position:relative;bottom: 7px;float:right;">' +
              '<span class="pull-right primary">ID: 22104</span>' +
              '</div>' +
              '</li>'
          }


        } )
      },

      carregaCategoria: function ()
      {
        var widgetsCategoria = document.getElementById( 'widgetsCategoria' )
        if ( widgetsCategoria ) {
          widgetsCategoria.innerHTML = ''
          __categoria.forEach( function ( wid )
          {
            var ativo                  = ( wid.ativo === '1' ) ? 'checked' : ''
            widgetsCategoria.innerHTML = widgetsCategoria.innerHTML +
              '<li class="list-group-item" wid-id="' + wid.id + '"><span>' + wid.nome + '</span>' +
              '<div style="width: auto;display: inline-block;position:relative;bottom: 7px;float:right;">' +
              '<!-- <button class="btn btn-danger pull-right" data-delete-wid=' + wid.id + '><i class="ft-x"></i> Deletar</button> -->' +
              '<button class="btn btn-info pull-right mr-1 ml-1 btn-edita-wid"><i class="icon-pencil"></i> Editar</button>' +
              '<input type="checkbox" class="switch pull-right" data-off-label="desativar" data-on-label="ativar" data-switch-always ' + ativo + '/>' +
              '</div>' +
              '</li>'
          } )
        }
      },

      carregaCarrinho: function ()
      {
        var widgetsCarrinho       = document.getElementById( 'widgetsCarrinho' )
        widgetsCarrinho.innerHTML = ''
        __carrinho.forEach( function ( wid )
        {
          var ativo                 = ( wid.ativo === '1' ) ? 'checked' : ''
          widgetsCarrinho.innerHTML = widgetsCarrinho.innerHTML +
            '<li class="list-group-item" wid-id="' + wid.id + '"><span>' + wid.nome + '</span>' +
            '<div style="width: auto;display: inline-block;position:relative;bottom: 7px;float:right;">' +
            '<!-- <button class="btn btn-danger pull-right" data-delete-wid=' + wid.id + '><i class="ft-x"></i> Deletar</button> -->' +
            '<button class="btn btn-info pull-right mr-1 ml-1 btn-edita-wid"><i class="icon-pencil"></i> Editar</button>' +
            '<input type="checkbox" class="switch pull-right" data-off-label="desativar" data-on-label="ativar" data-switch-always ' + ativo + '/>' +
            '</div>' +
            '</li>'
        } )
      },

      toggleSwitches: function ()
      {
        // SWITCHS
        'use strict'

        /*  Toggle Starts   */
        $( '.switch:checkbox' ).checkboxpicker()

        $( '#switch12' ).checkboxpicker( {
          html: true,
          offLabel: '<span class="icon-remove">',
          onLabel: '<span class="icon-ok">'
        } )

        // Switchery
        var i = 0
        if ( Array.prototype.forEach ) {
          var elems = $( '.switchery' )
          $.each( elems, function ( key, value )
          {
            var $size = '', $color = '', $sizeClass = '', $colorCode = ''
            $size                                                    = $( this ).data( 'size' )
            var $sizes                                               = {
              'lg': 'large',
              'sm': 'small',
              'xs': 'xsmall'
            }
            if ( $( this ).data( 'size' ) !== undefined ) {
              $sizeClass = 'switchery switchery-' + $sizes[ $size ]
            } else {
              $sizeClass = 'switchery'
            }

            $color      = $( this ).data( 'color' )
            var $colors = {
              'primary': '#967ADC',
              'success': '#37BC9B',
              'danger': '#DA4453',
              'warning': '#F6BB42',
              'info': '#3BAFDA'
            }
            if ( $color !== undefined ) {
              $colorCode = $colors[ $color ]
            } else {
              $colorCode = '#37BC9B'
            }

            var switchery = new Switchery( $( this )[ 0 ], { className: $sizeClass, color: $colorCode } )
          } )
        } else {
          var elems1 = document.querySelectorAll( '.switchery' )

          for ( i = 0; i < elems1.length; i++ ) {
            var $size     = elems1[ i ].data( 'size' )
            var $color    = elems1[ i ].data( 'color' )
            var switchery = new Switchery( elems1[ i ], { color: '#37BC9B' } )
          }
        }

        $( '.switch' ).change( function ()
        {
          var idWid = this.parentElement.parentElement.getAttribute( 'wid-id' )
          var val   = this.checked
          $.ajax( {
            type: 'POST',
            url: 'resource/resource_widget_edit.php',
            data: { 'idWid': idWid, 'val': val, 'op': 4 },
            success: function ( result )
            {
              console.log( result )
            }
          } )
        } )

      },

      botoesEditar: function ()
      {
        // BOTOES EDITAR WIDGET
        $( '.btn-edita-wid' ).off( 'click' )
        $( '.btn-edita-wid' ).on( 'click', function ()
        {
          var id   = this.parentElement.parentElement.getAttribute( 'wid-id' )
          var form = document.getElementById( 'campos-wid-edit' )
          form.setAttribute( 'id-wid', id )

          $.ajax( {
            type: 'POST',
            url: 'resource/resource_widget_edit.php',
            data: { 'idCli': idCli, 'op': 2, idWid: id },
            success: function ( result )
            {
              $( '#modalEditarWidget' ).modal( 'show' )
              var widget = JSON.parse( result )
              console.log( widget )
              $( '#nomeWidget' ).val( widget.WID_nome )
              $( '#tituloWidget' ).val( widget.WID_texto )
              $( '#utmWidget' ).val( widget.WID_utm )

              $( '#rhIdWid' ).html( widget.WID_id )


              var intels = {
                1: 'Top Trends',
                2: 'Mais Vendidos',
                3: 'Mais Vendidos da Categoria',
                4: 'Remarketing On-site',
                5: 'Similar ao Produto',
                6: 'Liquidação',
                7: 'Collection',
                8: 'Compre Junto',
                9: 'Boss Choice',
                10: 'Oferta Limitada',
                11: 'Carrinho Complementar',
                12: 'Itens Complementares',
                13: 'Não Vá Embora',
                14: 'Baixou de Preço',
                15: 'Lançamentos',
                16: 'Geolocaliação',
                17: 'Top Trends Facebook/Google',
                18: 'Melhor Avaliados + Similar por Produto',
                19: 'Descobertas',
                20: 'Melhor Avaliados',
                21: 'Recém Avaliados',
                22: 'Barra de Busca',
                23: 'Remarketing Complementar',
                24: 'Mais Vendidos da Categoria Manual',
                25: 'Palavra-chave',
                26: 'Por Atributo',
                27: 'Vitrine de Busca',
                28: 'Carrinho Manual',
                29: 'Inteligências Mistas',
                34: 'Produtos Relacionados',
                35: 'Remarketing Navegação',
                36: 'Smart Home',
                37: 'Compre Junto Complementar',
                38: 'Mais Desejados da Marca',
                39: 'Similares por Parâmetros',
                40: 'Lançamentos da Marca',
                41: 'Loja Lateral',
                43: 'Scroll Checkout'
              }

              //document.getElementById("titulo-modal-edit").innerHTML='<i class="ft ft-edit"></i>&nbsp;&nbsp;Editar Bloco \'' + intels[widget.WID_inteligencia] + '\'';
              //$("h4.adic").text($("h4.adic").text() + " Coiso");

              document.getElementById( 'spec-inteligencia-modal-edit' ).innerText = intels[ widget.WID_inteligencia ]

              if ( widget.WID_formato == 6 ) {
                document.getElementById( 'spec-inteligencia-modal-edit' ).innerText = 'Oferta Limitada'
              }

              /* var text = document.createTextNode(intels[widget.WID_inteligencia]);
							var child = document.getElementById('titulo-modal-edit');
							//child.parentNode.insertBefore(text, child.nextSibling);

							var btn = document.createElement("span");
							btn.className = "breadcrumb-item";
							btn.appendChild(text);
							child.parentNode.insertBefore(btn, child.nextSibling); */

              widget.WID_hide = widget.WID_hide ? widget.WID_hide.split( ',' ) : ''
              widget.WID_show = widget.WID_show ? widget.WID_show.split( ',' ) : ''

              // campos adicionais
              var camposAdicionais       = document.getElementById( 'widedit-opcoes-adicionais' )
              camposAdicionais.innerHTML = '<h4>Configurações Específicas</h4>'

              //esconder campos de acordo com a inteligência
              let $tituloWidget    = $( '#tituloWidget' ).parent().parent()
              let $subtituloWidget = $( '#inputSubtitulo' )
              $tituloWidget.show()
              $tituloWidget.show()
              switch ( widget.WID_inteligencia ) {
                case '7': // Collection
                  camposAdicionais.innerHTML +=
                    '<div class="form-group">' +
                    '<label>Cadastre as coleções:</label>' +
                    '<div class="input-group">' +
                    '<input  type="text" id="palavraChaveCollection" class="form-control" placeholder="Digite o nome de um produto" onblur="validaProdutos()" aria-invalid="false">' +
                    '<span class="input-group-btn" id="button-addon4">' +
                    '<button id="btnAddPalavraChaveCollection" class="btn btn-primary" type="button"><i class="fa fa-plus" aria-hidden="true"></i></button>' +
                    '</span>' +
                    '</div>' +
                    '</div>' +
                    '<div class="form-group">' +
                    '<label>Coleções:</label>' +
                    '<div id="divTagsCollection" class="form-control">' +
                    '<div class="controls">' +
                    '<input NAME="produtosCollection" id ="inputCollection" type="text"  value="' + widget.WC_collection + '" onchange="$(\'#produtosCollection\').val(this.value);validaProdutos();" data-role="tagsinput" class="form-control typeahead-tags" data-validation-required-message="Cadastre as palavras chave" required style="display: none;" aria-invalid="false">' +
                    '</div>' +
                    '</div>' +
                    '</div>'

                  $( '#inputCollection' ).tagsinput( 'add', widget.WC_collection )

                  $( '#btnAddPalavraChaveCollection' ).click( function ()
                  {
                    $( '#inputCollection' ).tagsinput( 'add', $( '#palavraChaveCollection' ).val() )

                    $( '#palavraChaveCollection' ).val( '' )

                    return false
                  } )
                  break
                case '8': // Compre Junto

                  var i = 0 //iterador da quantidade de grupos

                  htmlCfgCompreJunto =
                    '<div class="col-md-12 config-group mb-1">' +
                    '<div class="panel-group">' +
                    '<div class="panel panel-default">' +
                    '<div class="panel-heading">' +
                    '<h5 class="panel-title mb-0">' +
                    '<a data-toggle="collapse" href="#collapse#index#">' +
                    'Grupo de Configuração #index#' +
                    '<i class="fa fa-caret-down pull-right"></i>' +
                    '</a>' +
                    '</h5>' +
                    '</div>' +
                    '<div id="collapse#index#" class="panel-collapse collapse">' +
                    '<div class="panel-body mt-1">' +
                    '<div class="col-md-6 pd-l-0">' +
                    '<div class="form-group">' +
                    '<label>Palavra Chave Pai</label>' +
                    '<div class="rh-input-icon-right">' +
                    '<input name="p_chave_pai" class="form-control" type="text" value="">' +
                    '<abbr title="Essa é a palavra pai que vai ser usada como referência pela nossa inteligência. Iremos recomendar os produtos da palavra filho quando estiver sendo exibido um produto referente a ela" class="info-abbr">' +
                    '<i class="icon-info"></i>' +
                    '</abbr>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '<div class="col-md-6 pd-l-0 pd-r-0">' +
                    '<div class="form-group">' +
                    '<label>Palavra Chave Filho</label>' +
                    '<div class="rh-input-icon-right">' +
                    '<input name="p_chave_filho" class="form-control" type="text" value="">' +
                    '<abbr title="Essa é a palavra filho que vai ser usada como referência pela nossa inteligência, iremos recomendar produtos de acordo com ela" class="info-abbr">' +
                    '<i class="icon-info"></i>' +
                    '</abbr>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '<div class="col-md-6 pd-l-0">' +
                    '<div class="form-group">' +
                    '<label>Tipo da Palavra Chave Pai</label>' +
                    '<div class="rh-input-icon-right">' +
                    '<select name="tp_chave_pai" type="text"class="form-control" onchange="" data-validation-required-message="Este campo é obrigatório" required>' +
                    '<option selected value="0">Título</option>' +
                    '<option value="1">Categoria</option>' +
                    '</select>' +
                    '<abbr title="Essa é a palavra filho que vai ser usada como referência pela nossa inteligência, iremos recomendar produtos de acordo com ela" class="info-abbr">' +
                    '<i class="icon-info"></i>' +
                    '</abbr>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '<div class="col-md-6 pd-l-0 pd-r-0">' +
                    '<div class="form-group">' +
                    '<label>Tipo da Palavra Chave Filho</label>' +
                    '<div class="rh-input-icon-right">' +
                    '<select name="tp_chave_filho" type="text"class="form-control" onchange="" data-validation-required-message="Este campo é obrigatório" required>' +
                    '<option selected value="0">Título</option>' +
                    '<option value="1">Categoria</option>' +
                    '</select>' +
                    '<abbr title="Essa é a palavra filho que vai ser usada como referência pela nossa inteligência, iremos recomendar produtos de acordo com ela" class="info-abbr">' +
                    '<i class="icon-info"></i>' +
                    '</abbr>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '<div class="col-md-6 pd-l-0">' +
                    '<div class="form-group">' +
                    '<label>Parâmetro Pai</label>' +
                    '<div class="rh-input-icon-right">' +
                    '<input name="parametro_pai" class="form-control" type="text" value="">' +
                    '<abbr title="Essa é a palavra filho que vai ser usada como referência pela nossa inteligência, iremos recomendar produtos de acordo com ela" class="info-abbr">' +
                    '<i class="icon-info"></i>' +
                    '</abbr>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '<div class="col-md-6 pd-l-0 pd-r-0">' +
                    '<div class="form-group">' +
                    '<label>Parâmetro Filho</label>' +
                    '<div class="rh-input-icon-right">' +
                    '<input name="parametro_filho" class="form-control" type="text" value="">' +
                    '<abbr title="Essa é a palavra filho que vai ser usada como referência pela nossa inteligência, iremos recomendar produtos de acordo com ela" class="info-abbr">' +
                    '<i class="icon-info"></i>' +
                    '</abbr>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '<div class="col-md-6 pd-l-0">' +
                    '<div class="form-group">' +
                    '<label>Tipo do Parâmetro Pai</label>' +
                    '<div class="rh-input-icon-right">' +
                    '<select name="tp_parametro_pai" type="text"class="form-control" onchange="" data-validation-required-message="Este campo é obrigatório" required>' +
                    '<option selected value="0">Título</option>' +
                    '<option value="1">Categoria</option>' +
                    '</select>' +
                    '<abbr title="Essa é a palavra filho que vai ser usada como referência pela nossa inteligência, iremos recomendar produtos de acordo com ela" class="info-abbr">' +
                    '<i class="icon-info"></i>' +
                    '</abbr>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '<div class="col-md-6 pd-l-0 pd-r-0">' +
                    '<div class="form-group">' +
                    '<label>Tipo do Parâmetro Filho</label>' +
                    '<div class="rh-input-icon-right">' +
                    '<select name="tp_parametro_filho" type="text"class="form-control" onchange="" data-validation-required-message="Este campo é obrigatório" required>' +
                    '<option selected value="0">Título</option>' +
                    '<option value="1">Categoria</option>' +
                    '</select>' +
                    '<abbr title="Essa é a palavra filho que vai ser usada como referência pela nossa inteligência, iremos recomendar produtos de acordo com ela" class="info-abbr">' +
                    '<i class="icon-info"></i>' +
                    '</abbr>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '<div class="col-md-6 pd-l-0">' +
                    '<div class="form-group">' +
                    '<label>Negativa Pai</label>' +
                    '<div class="rh-input-icon-right">' +
                    '<input name="negativa_pai" class="form-control" type="text" value="">' +
                    '<abbr title="Essa é a palavra filho que vai ser usada como referência pela nossa inteligência, iremos recomendar produtos de acordo com ela" class="info-abbr">' +
                    '<i class="icon-info"></i>' +
                    '</abbr>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '<div class="col-md-6 pd-l-0 pd-r-0">' +
                    '<div class="form-group">' +
                    '<label>Negativa Filho</label>' +
                    '<div class="rh-input-icon-right">' +
                    '<input name="negativa_filho" class="form-control" type="text" value="">' +
                    '<abbr title="Essa é a palavra filho que vai ser usada como referência pela nossa inteligência, iremos recomendar produtos de acordo com ela" class="info-abbr">' +
                    '<i class="icon-info"></i>' +
                    '</abbr>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '<span class="btn-remove-panel-group"><i class="fa fa-trash red"></i></span>' +
                    '</div>' +
                    '</div>'

                  var htmlInicio = ''

                  while ( i < widget.WC_cj_f.length ) {
                    if ( i == 0 ) {
                      htmlInicio +=
                        '<div class="col-md-12 config-group mb-1">' +
                        '<div class="panel-group">' +
                        '<div class="panel panel-default">' +
                        '<div class="panel-heading">' +
                        '<h5 class="panel-title mb-0">' +
                        '<a data-toggle="collapse" href="#collapse#index#">' +
                        'Grupo de Configuração #index#' +
                        '<i class="fa fa-caret-down pull-right"></i>' +
                        '<abbr data-toggle="tooltip" data-placement="right" data-original-title="Essa é a palavra chave do seu bloco, a nossa inteligênca recomendará os produtos de acordo com ela" class="info-abbr" style="right:15px; top:0;">' +
                        '<i class="icon-info"></i>' +
                        '</abbr>' +
                        '</a>' +
                        '</h5>' +
                        '</div>'
                    } else {
                      htmlInicio +=
                        '<div class="col-md-12 config-group mb-1">' +
                        '<div class="panel-group">' +
                        '<div class="panel panel-default">' +
                        '<div class="panel-heading">' +
                        '<h5 class="panel-title mb-0">' +
                        '<a data-toggle="collapse" href="#collapse#index#">' +
                        'Grupo de Configuração #index#' +
                        '<i class="fa fa-caret-down pull-right"></i>' +
                        '</a>' +
                        '</h5>' +
                        '</div>' +
                        '<span class="btn-remove-panel-group"><i class="fa fa-trash red"></i></span>'
                    }
                    htmlInicio +=
                      '<div id="collapse#index#" class="panel-collapse collapse">' +
                      '<div class="panel-body mt-1">' +
                      '<div class="col-md-6 pd-l-0">' +
                      '<div class="form-group">' +
                      '<label>Palavra Chave Pai</label>' +
                      '<div class="rh-input-icon-right">' +
                      '<input name="p_chave_pai" class="form-control" type="text" value="' + widget.WC_cj_p[ i ] + '">' +
                      '<abbr title="Essa é a palavra pai que vai ser usada como referência pela nossa inteligência. Iremos recomendar os produtos da palavra filho quando estiver sendo exibido um produto referente a ela" class="info-abbr">' +
                      '<i class="icon-info"></i>' +
                      '</abbr>' +
                      '</div>' +
                      '</div>' +
                      '</div>' +
                      '<div class="col-md-6 pd-l-0 pd-r-0">' +
                      '<div class="form-group">' +
                      '<label>Palavra Chave Filho</label>' +
                      '<div class="rh-input-icon-right">' +
                      '<input name="p_chave_filho" class="form-control" type="text" value="' + widget.WC_cj_f[ i ] + '">' +
                      '<abbr title="Essa é a palavra filho que vai ser usada como referência pela nossa inteligência, iremos recomendar produtos de acordo com ela" class="info-abbr">' +
                      '<i class="icon-info"></i>' +
                      '</abbr>' +
                      '</div>' +
                      '</div>' +
                      '</div>' +
                      '<div class="col-md-6 pd-l-0">' +
                      '<div class="form-group">' +
                      '<label>Tipo da Palavra Chave Pai</label>' +
                      '<div class="rh-input-icon-right">' +
                      '<select name="tp_chave_pai" type="text"class="form-control" onchange="" data-validation-required-message="Este campo é obrigatório" required>'
                    if ( widget.tx_tipo_pai[ i ] == 0 ) {
                      htmlInicio += '<option selected value="0">Título</option>' +
                        '<option value="1">Categoria</option>'
                    } else {
                      htmlInicio += '<option value="0">Título</option>' +
                        '<option selected value="1">Categoria</option>'
                    }
                    htmlInicio += '</select>' +
                      '<abbr title="Essa é a palavra filho que vai ser usada como referência pela nossa inteligência, iremos recomendar produtos de acordo com ela" class="info-abbr">' +
                      '<i class="icon-info"></i>' +
                      '</abbr>' +
                      '</div>' +
                      '</div>' +
                      '</div>' +
                      '<div class="col-md-6 pd-l-0 pd-r-0">' +
                      '<div class="form-group">' +
                      '<label>Tipo da Palavra Chave Filho</label>' +
                      '<div class="rh-input-icon-right">' +
                      '<select name="tp_chave_filho" type="text"class="form-control" onchange="" data-validation-required-message="Este campo é obrigatório" required>'
                    if ( widget.tx_tipo_filho[ i ] == 0 ) {
                      htmlInicio += '<option selected value="0">Título</option>' +
                        '<option value="1">Categoria</option>'
                    } else {
                      htmlInicio += '<option value="0">Título</option>' +
                        '<option selected value="1">Categoria</option>'
                    }
                    htmlInicio += '</select>' +
                      '<abbr title="Essa é a palavra filho que vai ser usada como referência pela nossa inteligência, iremos recomendar produtos de acordo com ela" class="info-abbr">' +
                      '<i class="icon-info"></i>' +
                      '</abbr>' +
                      '</div>' +
                      '</div>' +
                      '</div>' +
                      '<div class="col-md-6 pd-l-0">' +
                      '<div class="form-group">' +
                      '<label>Parâmetro Pai</label>' +
                      '<div class="rh-input-icon-right">' +
                      '<input name="parametro_pai" class="form-control" type="text" value="' + widget.tx_param_pai[ i ] + '">' +
                      '<abbr title="Essa é a palavra filho que vai ser usada como referência pela nossa inteligência, iremos recomendar produtos de acordo com ela" class="info-abbr">' +
                      '<i class="icon-info"></i>' +
                      '</abbr>' +
                      '</div>' +
                      '</div>' +
                      '</div>' +
                      '<div class="col-md-6 pd-l-0 pd-r-0">' +
                      '<div class="form-group">' +
                      '<label>Parâmetro Filho</label>' +
                      '<div class="rh-input-icon-right">' +
                      '<input name="parametro_filho" class="form-control" type="text" value="' + widget.tx_param_filho[ i ] + '">' +
                      '<abbr title="Essa é a palavra filho que vai ser usada como referência pela nossa inteligência, iremos recomendar produtos de acordo com ela" class="info-abbr">' +
                      '<i class="icon-info"></i>' +
                      '</abbr>' +
                      '</div>' +
                      '</div>' +
                      '</div>' +
                      '<div class="col-md-6 pd-l-0">' +
                      '<div class="form-group">' +
                      '<label>Tipo do Parâmetro Pai</label>' +
                      '<div class="rh-input-icon-right">' +
                      '<select name="tp_parametro_pai" type="text"class="form-control" onchange="" data-validation-required-message="Este campo é obrigatório" required>'
                    if ( widget.tx_tipo_param_pai[ i ] == 0 ) {
                      htmlInicio += '<option selected value="0">Título</option>' +
                        '<option value="1">Categoria</option>'
                    } else {
                      htmlInicio += '<option value="0">Título</option>' +
                        '<option selected value="1">Categoria</option>'
                    }
                    htmlInicio += '</select>' +
                      '<abbr title="Essa é a palavra filho que vai ser usada como referência pela nossa inteligência, iremos recomendar produtos de acordo com ela" class="info-abbr">' +
                      '<i class="icon-info"></i>' +
                      '</abbr>' +
                      '</div>' +
                      '</div>' +
                      '</div>' +
                      '<div class="col-md-6 pd-l-0 pd-r-0">' +
                      '<div class="form-group">' +
                      '<label>Tipo do Parâmetro Filho</label>' +
                      '<div class="rh-input-icon-right">' +
                      '<select name="tp_parametro_filho" type="text"class="form-control" onchange="" data-validation-required-message="Este campo é obrigatório" required>'
                    if ( widget.tx_tipo_param_filho[ i ] == 0 ) {
                      htmlInicio += '<option selected value="0">Título</option>' +
                        '<option value="1">Categoria</option>'
                    } else {
                      htmlInicio += '<option value="0">Título</option>' +
                        '<option selected value="1">Categoria</option>'
                    }
                    htmlInicio += '</select>' +
                      '<abbr title="Essa é a palavra filho que vai ser usada como referência pela nossa inteligência, iremos recomendar produtos de acordo com ela" class="info-abbr">' +
                      '<i class="icon-info"></i>' +
                      '</abbr>' +
                      '</div>' +
                      '</div>' +
                      '</div>' +
                      '<div class="col-md-6 pd-l-0">' +
                      '<div class="form-group">' +
                      '<label>Negativa Pai</label>' +
                      '<div class="rh-input-icon-right">' +
                      '<input name="negativa_pai" class="form-control" type="text" value="' + widget.tx_negativa_pai[ i ] + '">' +
                      '<abbr title="Essa é a palavra filho que vai ser usada como referência pela nossa inteligência, iremos recomendar produtos de acordo com ela" class="info-abbr">' +
                      '<i class="icon-info"></i>' +
                      '</abbr>' +
                      '</div>' +
                      '</div>' +
                      '</div>' +
                      '<div class="col-md-6 pd-l-0 pd-r-0">' +
                      '<div class="form-group">' +
                      '<label>Negativa Filho</label>' +
                      '<div class="rh-input-icon-right">' +
                      '<input name="negativa_filho" class="form-control" type="text" value="' + widget.tx_negativa_filho[ i ] + '">' +
                      '<abbr title="Essa é a palavra filho que vai ser usada como referência pela nossa inteligência, iremos recomendar produtos de acordo com ela" class="info-abbr">' +
                      '<i class="icon-info"></i>' +
                      '</abbr>' +
                      '</div>' +
                      '</div>' +
                      '</div>' +
                      '</div>' +
                      '</div>' +
                      '</div>' +
                      '</div>' +
                      '</div>'
                    i++

                    htmlInicio = htmlInicio.replace( /#index#/g, i )


                  }


                  camposAdicionais.innerHTML += // PALAVRA CHAVE PAI
                    '<div class="form-group">' +
                    '<div class="rh-input-icon-right">' +
                    htmlInicio +
                    '<button class="btn btn-primary addConfGroup" title="Adicionar mais um grupo de configuração" data-index="' + i + '">' +
                    '+ Adicionar novo Grupo' +
                    '</button>' +
                    '</div>' +
                    '</div>'

                  break
                case '9': // oferta limitada manual

                  console.log( window[ 'bossChoiceProdTitulo' ] )
                  if ( widget.WID_formato == 6 ) {
                    camposAdicionais.innerHTML +=
                      '<div class="form-group">' +
                      '<label>Tipo</label>' +
                      '<select name="inteligenciaWidget" class="form-control">' +
                      '<option value="9" selected>Manual</option>' +
                      '<option value="10">Automático</option>' +
                      '</select>' +
                      '</div>'
                    var htmlManualOfertaLimitada =

                          '<div id="manualOfertaLimitada" class="form-group">' +
                          '<label>Nome do Produto</label>' +
                          '<div class="rh-input-icon-right">' +
                          '<input name="manualOfertaLimitada" id="manualOfertaLimitadaInput" class="form-control" type="text"'
                    htmlManualOfertaLimitada +=
                      ' placeholder="Digite o nome do produto que está no seu XML" aria-invalid="false" title="' + widget.tx_param_pai + '">' +
                      '<abbr title="Digite o nome do produto que está no seu XML" class="info-abbr">' +
                      '<i class="icon-info"></i>' +
                      '</abbr>' +
                      '</div>' +
                      '<div id="listaProdutosAutocompleteOfertaManual" class="form-control autocomplete-overlay"></div>' +
                      '</div>'

                    camposAdicionais.innerHTML += htmlManualOfertaLimitada

                    // preenche nome produto
                    preencheCampoAutoOfertaLimitada( widget.tx_param_pai[ 0 ], widget.WC_id_produto )

                    console.log( widget.WC_id_produto )

                    camposAdicionais.getElementsByTagName( 'select' )[ 0 ].addEventListener( 'click', function ()
                    {
                      if ( this.value === '9' ) {
                        $( '#manualOfertaLimitada' ).show()
                      } else {
                        $( '#manualOfertaLimitada' ).hide()
                      }
                    } )

                    $( '#manualOfertaLimitadaInput' ).keyup( function ()
                    {
                      var query = $( '#manualOfertaLimitadaInput' ).val()
                      if ( query.length > 2 ) {
                        $.ajax( {
                          url: 'autocomplete.php',
                          method: 'POST',
                          data: {
                            query: query,
                            formato: widget.WID_formato
                          },
                          success: function ( data )
                          {
                            $( '#listaProdutosAutocompleteOfertaManual' ).fadeIn()
                            $( '#listaProdutosAutocompleteOfertaManual' ).html( data )

                          }
                        } )
                      } else {
                        $( '#listaProdutosAutocompleteOfertaManual' ).fadeOut()
                        $( '#listaProdutosAutocompleteOfertaManual' ).html( '' )
                      }
                    } )

                  } else {

                    camposAdicionais.innerHTML +=
                      '<div class="form-group">' +
                      '<label>Cadastre os produtos manualmente</label>' +
                      '<div class="input-group">' +
                      '<input type="text" id="produtoManual" class="form-control j_autocomplete" placeholder="Digite o nome de um produto" onblur="validaProdutos()" aria-invalid="false">' +
                      '<span class="input-group-btn" id="button-addon4">' +
                      '<button id="btnAddProdutoManual" class="btn btn-primary" type="button"><i class="fa fa-plus" aria-hidden="true"></i></button>' +
                      '</span>' +
                      '</div>' +
                      '<div id="listaProdutosAutocomplete" class="form-control autocomplete-overlay"></div>' +
                      '</div>' +
                      '<div class="form-group">' +
                      '<label>Produtos Cadastrados:</label>' +
                      '<div id="divTagsProdutos" class="form-control">' +
                      '<div class="controls">' +
                      '<input NAME="produtosWidget" id ="inputProdutos" type="text"  value="" onchange="$(\'#produtosWidget\').val(this.value);validaProdutos();" data-role="tagsinput" class="form-control typeahead-tags" data-validation-required-message="Cadastre os produtos" required>' +
                      '</div>' +
                      '</div>' +
                      '</div>'

                    $( '#inputProdutos' ).tagsinput( {
                      itemValue: 'value',
                      itemText: 'text',
                    } )

                    var prodTitulos = String( widget.tx_param_pai ).split( ',' )
                    var prodIds     = String( widget.WC_id_produto ).split( ',' )

                    for ( i = 0; i < prodTitulos.length; i++ ) {
                      if ( prodTitulos[ i ] != '' ) {
                        $( '#inputProdutos' ).tagsinput( 'add', { value: prodIds[ i ], text: prodTitulos[ i ] } )
                      }
                    }
                    //$('#inputProdutos').tagsinput('add', {value: widget.WC_id_produto, text: textoo});

                    $( '#produtoManual' ).keyup( function ()
                    {
                      var query = $( this ).val()
                      if ( query.length > 2 ) {
                        $.ajax( {
                          url: 'autocomplete.php',
                          method: 'POST',
                          data: {
                            query: query,
                            formato: widget.WID_formato
                          },
                          success: function ( data )
                          {
                            $( '#listaProdutosAutocomplete' ).fadeIn()
                            $( '#listaProdutosAutocomplete' ).html( data )

                          }
                        } )
                      } else {
                        $( '#listaProdutosAutocomplete' ).fadeOut()
                        $( '#listaProdutosAutocomplete' ).html( '' )
                      }
                    } )

                    $( '#btnAddProdutoManual' ).click( function ()
                    {
                      if ( $( '#produtoManual' ).val().trim() === '' ) {
                        toastr[ 'error' ]( 'Você precisa digitar um nome para o produto' )
                        $( '#produtoManual' ).focus()
                        return false
                      }

                      var tamanho = $( '#inputProdutos' ).val().split( ',' ).length

                      if ( tamanho >= 24 ) {
                        toastr[ 'error' ]( 'Você pode cadastrar no máximo 24 produtos' )
                        return false
                      }
                      if ( $( '#produtoManual' ).val().trim().length != bossChoiceProdTitulo.trim().length ) {
                        toastr[ 'error' ]( 'Você precisa escolher um dos produtos da lista' )
                        $( '#produtoManual' ).focus()
                        return false
                      }
                      $( '#inputProdutos' ).tagsinput( 'add', { value: bossChoiceProdId, text: bossChoiceProdTitulo } )
                      $( '#produtoManual' ).val( '' )

                      if ( tamanho < 24 ) {
                        $( '#produtoManual' ).focus()
                      }
                      return false
                    } )
                  }
                  break
                case '10':
                  // oferta limitada automático
                  camposAdicionais.innerHTML +=
                    '<div class="form-group">' +
                    '<label>Tipo</label>' +
                    '<select name="inteligenciaWidget" class="form-control">' +
                    '<option value="9">Manual</option>' +
                    '<option value="10" selected>Automático</option>' +
                    '</select>' +
                    '</div>'
                  var htmlManualOfertaLimitada =

                        '<div id="manualOfertaLimitada" class="form-group" style="display:none;">' +
                        '<label>Nome do Produto</label>' +
                        '<div class="rh-input-icon-right">' +
                        '<input name="manualOfertaLimitada" id="manualOfertaLimitadaInput" class="form-control" type="text"'
                  if ( typeof widget.WC_titulos_produtos != 'undefined' && widget.WC_titulos_produtos.lenght > 1 ) {
                    htmlManualOfertaLimitada += ' value="' + widget.WC_titulos_produtos + '"'
                  }
                  htmlManualOfertaLimitada +=
                    ' placeholder="Digite o nome do produto que está no seu XML" aria-invalid="false">' +
                    '<abbr title="Digite o nome do produto que está no seu XML" class="info-abbr">' +
                    '<i class="icon-info"></i>' +
                    '</abbr>' +
                    '</div>' +
                    '<div id="listaProdutosAutocompleteOfertaManual" class="form-control autocomplete-overlay"></div>' +
                    '</div>'

                  camposAdicionais.innerHTML += htmlManualOfertaLimitada

                  camposAdicionais.getElementsByTagName( 'select' )[ 0 ].addEventListener( 'click', function ()
                  {
                    if ( this.value === '9' ) {
                      $( '#manualOfertaLimitada' ).show()
                    } else {
                      $( '#manualOfertaLimitada' ).hide()
                    }
                  } )

                  $( '#manualOfertaLimitadaInput' ).keyup( function ()
                  {
                    var query = $( '#manualOfertaLimitadaInput' ).val()
                    if ( query.length > 2 ) {
                      $.ajax( {
                        url: 'autocomplete.php',
                        method: 'POST',
                        data: {
                          query: query,
                          formato: widget.WID_formato
                        },
                        success: function ( data )
                        {
                          $( '#listaProdutosAutocompleteOfertaManual' ).fadeIn()
                          $( '#listaProdutosAutocompleteOfertaManual' ).html( data )

                        }
                      } )
                    } else {
                      $( '#listaProdutosAutocompleteOfertaManual' ).fadeOut()
                      $( '#listaProdutosAutocompleteOfertaManual' ).html( '' )
                    }
                  } )
                  break
                case '13':
                  $( '#inputSubtitulo' ).show()

                  if ( widget.CONF_template_overlay != 0 ) {
                    camposAdicionais.innerHTML +=
                      '<div id="containerAlteraImagemForm" class="col-md-6 pd-l-0">' +
                      '<label>Imagem Atual:</label>' +
                      '<div class="form-control">' +
                      '<abbr title="Esta é a foto que vai aparecer no banner do overlay." class="info-abbr">' +
                      '<i class="icon-info"></i>' +
                      '</abbr>' +
                      '<div class="rh-input-icon-right">' +
                      '<div class="media">' +
                      '<div class="media-left">' +
                      '<img class="img-banner-small" width="100px" src="..\/widget\/images\/overlay\/' + widget.WID_banner + '">' +
                      '</div>' +
                      '<div class="media-body">' +
                      '<div class="form-group">' +
                      '<button class="btn btn-info" id="btnViewBanner" data-target="..\/widget\/images\/overlay\/' + widget.WID_banner + '">Visualizar <i class="ft-eye"></i></button>' +
                      '</div>' +
                      '<div class="form-group">' +
                      '<button class="btn btn-primary" id="btnEditBanner">Alterar <i class="ft-upload"></i></button>' +
                      '</div>' +
                      '</div>' +
                      '</div>' +
                      '</div>' +
                      '</div>' +
                      '<div class="form-group">' +
                      '<div class="rh-input-icon-right">' +
                      '<input id="imagemBanner" name="imagemBanner" type="file" accept="image/x-png,image/gif,image/jpeg" hidden>' +
                      '</div>' +
                      '</div>' +
                      '</div>' +
                      '<div class="col-md-6 pd-l-0">' +
                      '<div class="form-group">' +
                      '<label for="linkBannerOverlay">Link do Banner</label>' +
                      '<div class="rh-input-icon-right">' +
                      '<input id="linkBannerOverlay" name="linkBannerOverlay" class="form-control" type="text" value="' + widget.WID_link_banner + '">' +
                      '<abbr title="O banner do seu overlay de saída irá redirecionar para esse link." class="info-abbr">' +
                      '<i class="icon-info"></i>' +
                      '</abbr>' +
                      '</div>' +
                      '</div>' +
                      '</div>'
                  } else {
                    camposAdicionais.innerHTML = ''
                  }
                  break
                case '24':
                  camposAdicionais.innerHTML += // NOME DA CATEGORIA
                    '<div class="col-md-6 pd-l-0">' +
                    '<div class="form-group">' +
                    '<label>Nome da Categoria</label>' +
                    '<div class="rh-input-icon-right">' +
                    '<input name="categoriaManual" class="form-control" type="text" value="' + widget.WC_categoria + '">' +
                    '<abbr data-toggle="tooltip" data-placement="right";' +
                    'data-original-title="Esse é o nome da categoria dos produtos a serem recomendados" class="info-abbr">' +
                    '<i class="icon-info"></i>' +
                    '</abbr>' +
                    '</div>' +
                    '</div>' +
                    '</div>'
                  break

                case '25':
                  camposAdicionais.innerHTML += // PALAVRA CHAVE
                    '<div class="col-md-6 pd-l-0">' +
                    '<div class="form-group">' +
                    '<label>Palavra Chave</label>' +
                    '<div class="rh-input-icon-right">' +
                    '<input name="p_chave" class="form-control" type="text" value="' + widget.WC_collection + '">' +
                    '<abbr data-toggle="tooltip" data-placement="right";' +
                    'data-original-title="Essa é a palavra chave do seu bloco, a nossa inteligênca recomendará os produtos de acordo com ela" class="info-abbr">' +
                    '<i class="icon-info"></i>' +
                    '</abbr>' +
                    '</div>' +
                    '</div>' +
                    '</div>'
                  break

                case '34':

                  var palavrasPai      = String( widget.WC_cj_p ).split( ',' )
                  var palavrasFilho    = String( widget.WC_cj_f ).split( ',' )
                  var palavrasPaiFilho = []

                  for ( i = 0; i < palavrasPai.length; i++ ) {
                    var valor = palavrasPai[ i ] + '->' + palavrasFilho[ i ]
                    palavrasPaiFilho.push( valor )
                  }
                  palavrasPaiFilho = palavrasPaiFilho.join()

                  camposAdicionais.innerHTML +=
                    '<div id="cadastrarRelacionados" style="">' +
                    '<div class="div-add">' +
                    '<div class="col-md-6">' +
                    '<div class="form-group validate">' +
                    '<label>Visualizando algum produto contendo:</label>' +
                    '<input id="prodRelPai" class="form-control" type="text">' +
                    '</div>' +
                    '</div>' +
                    '<div class="col-md-6 pd-l-0">' +
                    '<div class="form-group validate">' +
                    '<label>Recomendar produtos contendo:</label>' +
                    '<input id="prodRelFilho" class="form-control" type="text">' +
                    '<button id="btnAddPaiFilho" type="button" class="btn btn-primary btn-add-palavra" style=""><i class="fa fa-plus"></i></button>' +
                    '</div>' +
                    '</div>' +
                    '<div class="col-md-12">' +
                    '<label>Pares de termos cadastrados:</label>' +
                    '<div id="divTagsProdutosRelacionados" class="form-control">' +
                    '<div  class="controls">' +
                    '<input name="palavrasPaiFilho" id="palavrasPaiFilho" type="text" value="' + palavrasPaiFilho + '" data-role="tagsinput" class="form-control typeahead-tags" onchange="$(\'#produtosWidget\').val(this.value);validaProdutos();" data-validation-required-message="Cadastre as palavras chave" hidden="" required="" style="display: none;" readonly>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>'

                  if ( palavrasPaiFilho.replace( '->', '' ).trim() !== '' ) {
                    console.log( palavrasPaiFilho.replace( '->', '' ).trim() )
                    $( '#palavrasPaiFilho' ).tagsinput( 'add', palavrasPaiFilho )
                  } else {
                    console.log( palavrasPaiFilho )
                    $( '#palavrasPaiFilho' ).val( '' )
                    $( '#palavrasPaiFilho' ).tagsinput()
                  }

                  $( '#btnAddPaiFilho' ).click( function ()
                  {

                    var
                      pai      = $( '#prodRelPai' ),
                      filho    = $( '#prodRelFilho' ),
                      input    = $( '#palavrasPaiFilho' ),
                      valPai   = pai.val().trim(),
                      valFilho = filho.val().trim(),
                      value    = valPai + ' -> ' + valFilho

                    if ( valFilho === '' || valFilho === '' ) {
                      toastr[ 'error' ]( 'Você não pode adicionar palavras vazias como parâmetro' )
                      return false
                    }

                    $( '#palavrasPaiFilho' ).tagsinput( 'add', value )

                    // reseta
                    pai.val( '' )
                    filho.val( '' )

                    return false
                  } )

                  break
                case '38':
                  camposAdicionais.innerHTML += // Mais desejados da Marca
                    '<div class="col-md-6 pd-l-0">' +
                    '<div class="form-group">' +
                    '<label>Marca</label>' +
                    '<div class="rh-input-icon-right">' +
                    '<input name="marca" class="form-control" type="text" value="' + widget.WC_marca + '">' +
                    '<abbr title="A marca do produto servirá como parâmetro para o bloco." class="info-abbr">' +
                    '<i class="icon-info"></i>' +
                    '</abbr>' +
                    '</div>' +
                    '</div>' +
                    '</div>'
                  break
                case '40':
                  camposAdicionais.innerHTML += // Lançamentos da Marca
                    '<div class="col-md-6 pd-l-0">' +
                    '<div class="form-group">' +
                    '<label>Marca</label>' +
                    '<div class="rh-input-icon-right">' +
                    '<input name="marca" class="form-control" type="text" value="' + widget.WC_marca + '">' +
                    '<abbr title="A marca do produto servirá como parâmetro para o bloco." class="info-abbr">' +
                    '<i class="icon-info"></i>' +
                    '</abbr>' +
                    '</div>' +
                    '</div>' +
                    '</div>'
                  break

                case '39':
                  camposAdicionais.innerHTML += // PALAVRA CHAVE
                    '<div class="col-md-6 pd-l-0">' +
                    '<div class="form-group">' +
                    '<label>Tag XML 1</label>' +
                    '<div class="rh-input-icon-right">' +
                    '<input name="parametro_pai" class="form-control" type="text" value="' + widget.tx_param_pai + '">' +
                    '<abbr data-toggle="tooltip" data-placement="right";' +
                    'data-original-title="Coloque aqui uma Tag presente no seu XML que você queira levar em consideração na similaridade." class="info-abbr">' +
                    '<i class="icon-info"></i>' +
                    '</abbr>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '<div class="col-md-6 pd-l-0">' +
                    '<div class="form-group">' +
                    '<label>Tag XML 2</label>' +
                    '<div class="rh-input-icon-right">' +
                    '<input name="parametro_filho" class="form-control" type="text" value="' + widget.tx_param_filho + '">' +
                    '<abbr data-toggle="tooltip" data-placement="right";' +
                    'data-original-title="Coloque aqui uma Tag presente no seu XML que você queira levar em consideração na similaridade." class="info-abbr">' +
                    '<i class="icon-info"></i>' +
                    '</abbr>' +
                    '</div>' +
                    '</div>' +
                    '</div>'
                  break

                case '41':
                  $tituloWidget.hide()
                  $subtituloWidget.hide()

                  if ( widget.CONF_template_overlay != 0 ) {
                    camposAdicionais.innerHTML +=
                      '<div id="containerAlteraImagemForm" class="col-md-6 pd-l-0">' +
                      '<label>Imagem Atual:</label>' +
                      '<div class="form-control">' +
                      '<abbr title="Esta é a foto que vai aparecer no banner do overlay." class="info-abbr">' +
                      '<i class="icon-info"></i>' +
                      '</abbr>' +
                      '<div class="rh-input-icon-right">' +
                      '<div class="media">' +
                      '<div class="media-left">' +
                      '<img class="img-banner-small" width="100px" src="..\/widget\/images\/overlay\/' + widget.WID_banner + '">' +
                      '</div>' +
                      '<div class="media-body">' +
                      '<div class="form-group">' +
                      '<button class="btn btn-info" id="btnViewBanner" data-target="..\/widget\/images\/overlay\/' + widget.WID_banner + '">Visualizar <i class="ft-eye"></i></button>' +
                      '</div>' +
                      '<div class="form-group">' +
                      '<button class="btn btn-primary" id="btnEditBannerLojaLateral">Alterar <i class="ft-upload"></i></button>' +
                      '</div>' +
                      '</div>' +
                      '</div>' +
                      '</div>' +
                      '</div>' +
                      '<div class="form-group">' +
                      '<div class="rh-input-icon-right">' +
                      '<input id="imagemBannerLojaLateral" name="imagemBanner" type="file" accept="image/x-png,image/gif,image/jpeg" hidden>' +
                      '</div>' +
                      '</div>' +
                      '</div>' +
                      '<div class="col-md-6 pd-l-0">' +
                      '<div class="form-group">' +
                      '<label for="linkBannerOverlay">Link do Banner</label>' +
                      '<div class="rh-input-icon-right">' +
                      '<input id="linkBannerOverlay" name="linkBannerOverlay" class="form-control" type="text" value="' + widget.WID_link_banner + '">' +
                      '<abbr title="O banner do seu overlay de saída irá redirecionar para esse link." class="info-abbr">' +
                      '<i class="icon-info"></i>' +
                      '</abbr>' +
                      '</div>' +
                      '</div>' +
                      '</div>'

                    camposAdicionais.innerHTML +=
                      '<div id="containerAlteraImagemForm" class="col-md-6 pd-l-0">' +
                      '<label>Thumb Atual:</label>' +
                      '<div class="form-control">' +
                      '<abbr title="Esta é a foto que vai aparecer no ícone do overlay." class="info-abbr">' +
                      '<i class="icon-info"></i>' +
                      '</abbr>' +
                      '<div class="rh-input-icon-right">' +
                      '<div class="media">' +
                      '<div class="media-left">' +
                      '<img class="img-thumb-small" width="100px" src="..\/widget\/images\/overlay\/' + widget.WID_thumb + '">' +
                      '</div>' +
                      '<div class="media-body">' +
                      '<div class="form-group">' +
                      '<button class="btn btn-info" id="btnViewBanner" data-target="..\/widget\/images\/overlay\/' + widget.WID_thumb + '">Visualizar <i class="ft-eye"></i></button>' +
                      '</div>' +
                      '<div class="form-group">' +
                      '<button class="btn btn-primary" id="btnEditThumb">Alterar <i class="ft-upload"></i></button>' +
                      '</div>' +
                      '</div>' +
                      '</div>' +
                      '</div>' +
                      '</div>' +
                      '<div class="form-group">' +
                      '<div class="rh-input-icon-right">' +
                      '<input id="thumbnail" name="thumbnail" type="file" accept="image/x-png,image/gif,image/jpeg" hidden>' +
                      '</div>' +
                      '</div>' +
                      '</div>'
                  } else {
                    camposAdicionais.innerHTML = ''
                  }
                  break

                case '43':

                  $tituloWidget.hide()
                  $subtituloWidget.hide()
                  $( '#widedit-opcoes-adicionais' ).html( '<style> #excessoesPaginas { display: none; } </style>' )


                  break

                default:
                  camposAdicionais.innerHTML = ''
                  break
              }

              // ver imagem banner
              if ( $( '#btnViewBanner' ).length > 0 ) {
                rhPhoto( $( '#btnViewBanner' ) )

                $( '#btnEditBanner' ).click( function ()
                {
                  $( '#imagemBanner' )[ 0 ].focus()
                  $( '#imagemBanner' )[ 0 ].click()
                } )

                $( '#btnEditBannerLojaLateral' ).click( function ()
                {
                  $( '#imagemBannerLojaLateral' )[ 0 ].focus()
                  $( '#imagemBannerLojaLateral' )[ 0 ].click()
                } )


                $( '#btnEditThumb' ).click( function ()
                {
                  $( '#thumbnail' )[ 0 ].focus()
                  $( '#thumbnail' )[ 0 ].click()
                } )


                $( '#imagemBanner' ).change( function ()
                {

                  bloqueiaElemento( $( '#containerAlteraImagemForm' )[ 0 ] )

                  var file      = this.files[ 0 ]
                  var reader    = new FileReader()
                  reader.onload = function ( f )
                  {

                    // pega dimensoes da imagem
                    var img = new Image
                    img.src = f.target.result

                    img.onload = function ()
                    {
                      if ( file.type !== 'image/png' && file.type !== 'image/jpg' && file.type !== 'image/jpeg' && file.type !== 'image/gif' ) {
                        toastr[ 'error' ]( 'O arquivo que você tentou enviar não é uma imagem.' )
                        $( '#imagemBanner' ).val( '' )
                      }
                      if ( window.template == 3 && img.width != 900 && img.height != 150 ) {
                        toastr[ 'error' ]( 'As dimensões da imagem devem ser de exatamente 900px de largura por 150px de altura.' )
                        $( '#imagemBanner' ).val( '' )
                      } else if ( window.template != 3 && img.width != 350 && img.height != 500 ) {
                        toastr[ 'error' ]( 'As dimensões da imagem devem ser de exatamente 350px de largura por 500px de altura.' )
                        $( '#imagemBanner' ).val( '' )
                      } else {
                        $( '.img-banner-small' ).attr( 'src', f.target.result )
                      }
                      desbloqueiaElemento( $( '#containerAlteraImagemForm' )[ 0 ] )
                    }
                  }

                  reader.readAsDataURL( file )
                } )
                $( '#imagemBannerLojaLateral' ).change( function ()
                {

                  bloqueiaElemento( $( '#containerAlteraImagemForm' )[ 0 ] )

                  var file      = this.files[ 0 ]
                  var reader    = new FileReader()
                  reader.onload = function ( f )
                  {

                    // pega dimensoes da imagem
                    var img = new Image
                    img.src = f.target.result

                    img.onload = function ()
                    {
                      if ( file.type !== 'image/png' && file.type !== 'image/jpg' && file.type !== 'image/jpeg' && file.type !== 'image/gif' ) {
                        toastr[ 'error' ]( 'O arquivo que você tentou enviar não é uma imagem.' )
                        $( '#imagemBannerLojaLateral' ).val( '' )
                      }
                      if ( img.width != 400 && img.height != 300 ) {
                        toastr[ 'error' ]( 'As dimensões da imagem devem ser de exatamente 400px de largura por 300px de altura.' )
                        $( '#imagemBannerLojaLateral' ).val( '' )
                      } else {
                        $( '.img-banner-small' ).attr( 'src', f.target.result )
                      }
                      desbloqueiaElemento( $( '#containerAlteraImagemForm' )[ 0 ] )
                    }

                  }

                  reader.readAsDataURL( file )
                } )
                $( '#thumbnail' ).change( function ()
                {

                  bloqueiaElemento( $( '#containerAlteraImagemForm' )[ 0 ] )

                  var file      = this.files[ 0 ]
                  var reader    = new FileReader()
                  reader.onload = function ( f )
                  {

                    // pega dimensoes da imagem
                    var img = new Image
                    img.src = f.target.result

                    img.onload = function ()
                    {
                      if ( file.type !== 'image/png' && file.type !== 'image/jpg' && file.type !== 'image/jpeg' && file.type !== 'image/gif' ) {
                        toastr[ 'error' ]( 'O arquivo que você tentou enviar não é uma imagem.' )
                        $( '#thumbnail' ).val( '' )
                      }
                      if ( img.width != 80 && img.height != 80 ) {
                        toastr[ 'error' ]( 'As dimensões da imagem devem ser de exatamente 80px de largura por 80px de altura.' )
                        $( '#thumbnail' ).val( '' )
                      } else {
                        $( '.img-thumb-small' ).attr( 'src', f.target.result )
                      }
                      desbloqueiaElemento( $( '#containerAlteraImagemForm' )[ 0 ] )
                    }

                  }

                  reader.readAsDataURL( file )
                } )
              }

              // nome produto manual ol
              if ( typeof widget.tx_param_pai != 'undefined' ) {
                $( '#manualOfertaLimitadaInput' ).val( widget.tx_param_pai[ 0 ] )
              }

              // se nao for widget basico, mostra id e opcao de alterar o formato
              // opcoes select formato
              var select             = '<select name="formatoWidget" class="form-control">'
              var dicionarioFormatos = {
                1: 'Prateleira',
                3: 'Carrossel'
                /*8:'Vitrine',
								11:'Totem'*/
              }

              var selectUpDown     = '<select name="UpDown" class="form-control">'
              var dicionarioUpDown = {
                1: 'Acima',
                0: 'Abaixo'
              }

              Object.keys( dicionarioFormatos ).forEach( function ( id )
              { // id: id do formato
                if ( widget.WID_formato == id ) {
                  select += '<option value="' + id + '" selected>' + dicionarioFormatos[ id ] + '</option>'
                } else {
                  select += '<option value="' + id + '">' + dicionarioFormatos[ id ] + '</option>'
                }
              } )

              Object.keys( dicionarioUpDown ).forEach( function ( id )
              { // id: id upDown

                if ( parseInt( widget.WID_updown ) == id ) {

                  selectUpDown += '<option value="' + id + '" selected>' + dicionarioUpDown[ id ] + '</option>'

                } else {

                  selectUpDown += '<option value="' + id + '">' + dicionarioUpDown[ id ] + '</option>'
                }
              } )

              select += '</select>'
              selectUpDown += '</select>'
              //-------------------

              var containerID =
                    '<div class="form-group">' +
                    '<div class="row">' +
                    '<div class="col-xs-3">' +
                    '<label>Tipo do Container</label>' +
                    '<div class="rh-input-icon-right">' +
                    '<select id="widDivType" name="widDivType" class="form-control">' +
                    '<option id="1"'
              if ( widget.WID_div_type == 'id' || widget.WID_div_type == 'ID' )
                containerID += 'selected '
              containerID += 'value="id">ID</option>' +
                '<option id="2"'
              if ( widget.WID_div_type == 'class' || widget.WID_div_type == 'CLASS' )
                containerID += 'selected '
              containerID += 'value="class">CLASS</option>' +
                '</select>' +
                '</div>' +
                '</div>' +
                '<div class="col-xs-4">' +
                '<label>Nome da classe ou ID</label>' +
                '<div class="rh-input-icon-right">' +
                '<input id="widDiv" name="widDiv" class="form-control" type="text" value="' + widget.WID_div + '">' +
                '<abbr title="Esse é o identificador do container onde o nosso bloco vai ficar dentro da sua loja" class="info-abbr">' +
                '<i class="icon-info"></i>' +
                '</abbr>' +
                '</div>' +
                '</div>' +
                '<div class="col-xs-5">' +
                '<label>Posição relativa ao Container</label>' +
                '<div class="rh-input-icon-right">' +
                '<select name="UpDown" class="form-control">' +
                '<option id="1" '
              if ( parseInt( widget.WID_updown ) == 1 )
                containerID += 'selected '
              containerID += 'value="1">Acima</option>' +
                '<option id="2" '
              if ( parseInt( widget.WID_updown ) == 0 )
                containerID += 'selected '
              containerID += 'value="0">Abaixo</option>' +
                '</select>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '</div>'


              if ( widget.WID_inteligencia != 35
                && widget.WID_inteligencia != 8
                && widget.WID_formato != 6
                && widget.WID_formato != 5
                && widget.WID_inteligencia != 41
                && widget.WID_inteligencia != 43
              ) { //diferente de remarketing navegação, compre junto (eles têm formato único), oferta limitada e overlay de saída
                containerID +=
                  //1 - Prateleira ;    2 - Dupla   ; 3 - Carrossel;      11 - Totem;     8 - Vitrine
                  '<div class="form-group">' +
                  '<label>Formato do Bloco</label>' +
                  '<div class="rh-input-icon-right">' +
                  select +
                  '<abbr title="Esse é o formato que o nosso bloco vai aparecer em sua loja" class="info-abbr" style="right:20px;">' +
                  '<i class="icon-info"></i>' +
                  '</abbr>' +
                  '</div>' +
                  '</div>'
              }


              htmlHide = '<div class="form-group exceptions">' +
                '<div class="rh-input-icon-right">' +
                '<input id="widHide" name="widHide" class="form-control" type="url" value="">' +
                '<span class="btn-delete-form-group"><i class="fa fa-trash red"></i></span>' +
                '</div>' +
                '</div>'

              htmlShow =
                '<div class="form-group inclusions">' +
                '<div class="rh-input-icon-right">' +
                '<input name="widShow" class="form-control" type="url" value="">' +
                '</div>' +
                '<span class="btn-delete-form-group"><i class="fa fa-trash red"></i></span>' +
                '</div>'

              $( '#container-configuracoes' ).html(
                containerID +
                '<div class="row" id="excessoesPaginas">' +
                '<div class="col-md-6">' +
                '<div class="form-group exceptions">' +
                '<label>Excessões de páginas</label>' +
                '<div class="rh-input-icon-right">' +
                '<input id="widHide" name="widHide" class="form-control" type="url" value="">' +
                '<abbr title="Informe o nome da página que deseja que o widget não seja executado" class="info-abbr">' +
                '<i class="icon-info"></i>' +
                '</abbr>' +
                '</div>' +
                '</div>' +
                '<div class="form-group">' +
                '<div class="rh-input-icon-right">' +
                '<button class="btn btn-primary addHideField" title="Adicionar mais uma página de excessão">' +
                '+' +
                '</button>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '<div class="col-md-6">' +
                '<div class="form-group inclusions">' +
                '<label>Inclusões de páginas</label>' +
                '<div class="rh-input-icon-right">' +
                '<input id="widShow" name="widShow" class="form-control" type="url" value="">' +
                '<abbr title="Informe o nome da página que deseja que o widget seja executado" class="info-abbr">' +
                '<i class="icon-info"></i>' +
                '</abbr>' +
                '</div>' +
                '</div>' +
                '<div class="form-group">' +
                '<div class="rh-input-icon-right">' +
                '<button class="btn btn-primary addShowField" title="Adicionar mais uma página de inclusão">' +
                '+' +
                '</button>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '</div>'
              )

              // tooltips
              iniciaTooltip()

              //Preenchendo campos WID_HIDE
              $.each( widget.WID_hide, function ( index, val )
              {

                if ( index > 0 ) {
                  $( '.addHideField' ).trigger( 'click' )
                }

                $( 'input[name=widHide]' )[ index ].value = val
              } )

              //Preenchendo campos WID_ShOW
              $.each( widget.WID_show, function ( index, val )
              {

                if ( index > 0 ) {
                  $( '.addShowField' ).trigger( 'click' )
                }

                $( 'input[name=widShow]' )[ index ].value = val
              } )
            }
          } )
        } )

        $( '.btn-configura-busca' ).click( function ()
        {
          var id = this.parentElement.parentElement.getAttribute( 'wid-id' )
          $( '#rhIdWidBusca' ).html( id )

          $.ajax( {
            type: 'POST',
            url: 'resource/resource_widget_edit.php',
            data: { 'idCli': idCli, 'op': 6, idWid: id },
            success: function ( result )
            {
              var dados = JSON.parse( result )

              // carrega sinonimos
              searchbarCfg.loadSynonyms( dados.synonyms )

              $( '#modalConfiguraBusca' ).modal( 'show' )
              $( '#modalConfiguraBusca .rh-id-wid' ).html()
            }
          } )
        } )

        // BOTOESAPAGAR
        $( 'button[data-delete-wid]' ).off( 'click' )
        $( 'button[data-delete-wid]' ).on( 'click', function ()
        {
          var id = $( this ).attr( 'data-delete-wid' )
          $( '#btnConfirmaExclusao' ).attr( 'id-wid', id )
          $( '#modalConfirmaExclusao' ).modal( 'show' )
        } )
      },

      botoesSalvar: function ()
      {
        // BTNS DUPLICAR WID
        $( '.btn-duplica-wid' ).off( 'click' )
        $( '.btn-duplica-wid' ).click( function ()
        {
          var id = this.parentElement.parentElement.getAttribute( 'wid-id' )

          $.ajax( {
            type: 'POST',
            url: 'resource/resource_widget_edit.php',
            data: { 'idCli': idCli, 'op': 8, idWid: id },
            success: function ( result )
            {
              console.log( result )
              widgets.inicia()
            }
          } )
        } )

        // BOTOES SALVAR WIDGET
        $( '#btn-salva-wid' ).off( 'click' )
        $( '#btn-salva-wid' ).on( 'click', function ()
        {
          var form    = document.getElementById( 'campos-wid-edit' )
          var idWid   = form.getAttribute( 'id-wid' )
          var inputs  = $( '#campos-wid-edit input' )
          var selects = $( '#campos-wid-edit select' )

          var formData = new FormData()
          formData.append( 'idWid', idWid )
          formData.append( 'op', 3 )

          // PEGA O VALOR DE TODOS OS INPUTS
          for ( var i = 0; i < inputs.length; i++ ) {
            var key = inputs[ i ].name
            if ( inputs[ i ].type == 'file' ) {
              var val = inputs[ i ].files[ 0 ]
            } else {
              var val = inputs[ i ].value
            }

            formData.append( key, val )
          }

          // tratamento widshow e widhide para salvar mais de 1 pagina
          formData.delete( 'widShow' )
          formData.delete( 'widHide' )
          var widShow = []
          var widHide = []

          var widS = $( 'input[name="widShow"]' )
          var widH = $( 'input[name="widHide"]' )

          for ( var i = 0; i < widS.length; i++ ) {
            widShow.push( widS[ 0 ].value )
          }

          for ( var i = 0; i < widH.length; i++ ) {
            widHide.push( widH[ 0 ].value )
          }


          formData.append( 'widShow', widShow )
          formData.append( 'widHide', widHide )

          // PEGA O VALOR DE TODOS OS SELECTS
          for ( var i = 0; i < selects.length; i++ ) {
            var key = selects[ i ].name
            var val = selects[ i ].value
            formData.append( key, val )
          }


          // bosschoice
          if ( $( '#inputProdutos' )[ 0 ] ) { // checa se existe o input de produtos
            var produtos = $( '#inputProdutos' ).tagsinput( 'items' )
            var p        = []

            for ( var i = 0; i < produtos.length; i++ ) {
              var item = produtos[ i ]
              p.push( item.text )
            }

            p = p.join( ',' )

            formData.append( 'bossChoiceProdTitulo', p )
          }

          // paginas inclusao widgets
          formData.delete( 'widShow' )
          var widShowValue = ''
          var widShow      = document.getElementsByName( 'widShow' )
          for ( var i = 0; i < widShow.length; i++ ) {
            var value = widShow[ i ].value.trim()
            if ( value !== '' ) {
              widShowValue = ( i === 0 ) ? value : widShowValue + ',' + value
            }
          }
          formData.append( 'widShow', widShowValue )

          // paginas exclusao widgets
          formData.delete( 'widHide' )
          var widHideValue = ''
          var widHide      = document.getElementsByName( 'widHide' )
          for ( var i = 0; i < widHide.length; i++ ) {
            var value = widHide[ i ].value.trim()
            if ( value !== '' ) {
              widHideValue = ( i === 0 ) ? value : widHideValue + ',' + value
            }
          }
          formData.append( 'widHide', widHideValue )

          if (
            document.getElementsByName( 'p_chave_pai' ).length > 0 &&
            document.getElementsByName( 'p_chave_filho' ).length > 0 &&
            document.getElementsByName( 'tp_chave_pai' ).length > 0 &&
            document.getElementsByName( 'tp_chave_filho' ).length > 0 &&
            document.getElementsByName( 'parametro_pai' ).length > 0 &&
            document.getElementsByName( 'parametro_filho' ).length > 0
          ) {
            // COMPRE JUNTO
            formData.delete( 'p_chave_pai' )
            formData.delete( 'p_chave_filho' )
            formData.delete( 'tp_chave_pai' )
            formData.delete( 'tp_chave_filho' )

            formData.delete( 'parametro_pai' )
            formData.delete( 'parametro_filho' )
            formData.delete( 'tp_parametro_pai' )
            formData.delete( 'tp_parametro_filho' )

            formData.delete( 'negativa_pai' )
            formData.delete( 'negativa_filho' )

            var palavraChavePai       = document.getElementsByName( 'p_chave_pai' )
            var palavraChaveFilho     = document.getElementsByName( 'p_chave_filho' )
            var tipoPalavraChavePai   = document.getElementsByName( 'tp_chave_pai' )
            var tipoPalavraChaveFilho = document.getElementsByName( 'tp_chave_filho' )
            var parametroPai          = document.getElementsByName( 'parametro_pai' )
            var parametroFilho        = document.getElementsByName( 'parametro_filho' )
            var tipoParametroPai      = document.getElementsByName( 'tp_parametro_pai' )
            var tipoParametroFilho    = document.getElementsByName( 'tp_parametro_filho' )
            var negativaPai           = document.getElementsByName( 'negativa_pai' )
            var negativaFilho         = document.getElementsByName( 'negativa_filho' )

            var p_chave_pai        = []
            var p_chave_filho      = []
            var tp_chave_pai       = []
            var tp_chave_filho     = []
            var parametro_pai      = []
            var parametro_filho    = []
            var tp_parametro_pai   = []
            var tp_parametro_filho = []
            var negativa_pai       = []
            var negativa_filho     = []

            for ( var i = 0; i < palavraChavePai.length; i++ ) {
              p_chave_pai.push( palavraChavePai[ i ].value )
              p_chave_filho.push( palavraChaveFilho[ i ].value )
              tp_chave_pai.push( tipoPalavraChavePai[ i ].value )
              tp_chave_filho.push( tipoPalavraChaveFilho[ i ].value )

              parametro_pai.push( parametroPai[ i ].value )
              parametro_filho.push( parametroFilho[ i ].value )
              tp_parametro_pai.push( tipoParametroPai[ i ].value )
              tp_parametro_filho.push( tipoParametroFilho[ i ].value )

              negativa_pai.push( negativaPai[ i ].value )
              negativa_filho.push( negativaFilho[ i ].value )
            }

            p_chave_pai        = p_chave_pai.join( ',' )
            p_chave_filho      = p_chave_filho.join( ',' )
            tp_chave_pai       = tp_chave_pai.join( ',' )
            tp_chave_filho     = tp_chave_filho.join( ',' )
            parametro_pai      = parametro_pai.join( ',' )
            parametro_filho    = parametro_filho.join( ',' )
            tp_parametro_pai   = tp_parametro_pai.join( ',' )
            tp_parametro_filho = tp_parametro_filho.join( ',' )
            negativa_pai       = negativa_pai.join( ',' )
            negativa_filho     = negativa_filho.join( ',' )

            formData.append( 'p_chave_pai', p_chave_pai )
            formData.append( 'p_chave_filho', p_chave_filho )
            formData.append( 'tp_chave_pai', tp_chave_pai )
            formData.append( 'tp_chave_filho', tp_chave_filho )

            formData.append( 'parametro_pai', parametro_pai )
            formData.append( 'parametro_filho', parametro_filho )
            formData.append( 'tp_parametro_pai', tp_parametro_pai )
            formData.append( 'tp_parametro_filho', tp_parametro_filho )

            formData.append( 'negativa_pai', negativa_pai )
            formData.append( 'negativa_filho', negativa_filho )
          }


          // esses dados soh sao enviados para o oferta limitada manual
          if ( $( '#manualOfertaLimitada' ).length > 0 ) {
            formData.delete( 'tx_param_pai' )

            var key = 'bossChoiceProdId'
            var val = bossChoiceProdId
            formData.append( 'bossChoiceProdId', bossChoiceProdId )

            var key = 'bossChoiceProdTitulo'
            var val = bossChoiceProdTitulo
            formData.append( 'bossChoiceProdTitulo', bossChoiceProdTitulo )
          }

          if ( !formData.get( 'imagemBanner' ) )
            formData.delete( 'imagemBanner' )

          if ( !formData.get( 'thumbnail' ) )
            formData.delete( 'thumbnail' )

          $.ajax( {
            type: 'POST',
            url: 'resource/resource_widget_edit.php',
            dataType: 'text',  // what to expect back from the PHP script, if anything
            cache: false,
            contentType: false,
            processData: false,
            data: formData,
            success: function ( result )
            {
              $( '#modalEditarWidget' ).modal( 'hide' )
              toastr[ 'success' ]( 'As informações do seu bloco de recomendação foram atualizadas!' )
            }
          } )
        } )
      }
    }

  // sempre q fechar o modal, esconde o campo
  $( '#modalEditarWidget' ).on( 'hidden.bs.modal', function ()
  {
    $( '#inputSubtitulo' ).hide() // esconde campo subtitulo
    $( '#tituloWidget' ).parent().parent().show()
  } )

  $( '#btnConfirmaExclusao' ).on( 'click', function ()
  {
    var id = $( this ).attr( 'id-wid' )
    $.ajax( {
      type: 'POST',
      url: 'resource/resource_widget_edit.php',
      data: { 'idWid': id, 'op': 5 },
      success: function ( result )
      {
        $( '#modalConfirmaExclusao' ).modal( 'hide' )
        reloadAllCards()
        widgets.inicia()
      }
    } )
  } )

  $( document ).on( 'click', '#widedit-opcoes-adicionais .addConfGroup', function ( event )
  {
    var index = parseInt( $( this ).attr( 'data-index' ) ) + 1
    $( this ).attr( 'data-index', index )
    $( this ).before( htmlCfgCompreJunto.replace( /#index#/g, index ) )
    addListenerBtnDeleteForm()
    event.preventDefault()
  } )

  $( document ).on( 'click', '#container-configuracoes .addHideField', function ( event )
  {
    $( this ).before( htmlHide )
    addListenerBtnDeleteForm()
    event.preventDefault()
  } )

  $( document ).on( 'click', '#container-configuracoes .addShowField', function ( event )
  {
    $( this ).before( htmlShow )
    addListenerBtnDeleteForm()
    event.preventDefault()
  } )

  function addListenerBtnDeleteForm()
  {
    $( '.btn-remove-panel-group' ).off( 'click', removePanel )
    $( '.btn-remove-panel-group' ).on( 'click', removePanel )
    $( '.btn-delete-form-group' ).off( 'click', removeForm )
    $( '.btn-delete-form-group' ).on( 'click', removeForm )
  }

  // listeners botoes remover
  addListenerBtnDeleteForm()

  function removeForm( event )
  {
    $( event.target ).closest( '.form-group' ).remove()
  }

  function removePanel( event )
  {
    $( event.target ).closest( '.config-group' ).remove()
  }

  // CARREGA TODOS OS WIDGETS
  widgets.inicia()

  function reloadAllCards()
  {
    var block_ele = $( '.card' )
    // Block Element
    block_ele.block( {
      message: '<div class="ft-refresh-cw icon-spin font-medium-2"></div>',
      timeout: 2000, //unblock after 2 seconds
      overlayCSS: {
        backgroundColor: '#FFF',
        cursor: 'wait',
      },
      css: {
        border: 0,
        padding: 0,
        backgroundColor: 'none'
      }
    } )
  }


} )

// modal configuracao busca
var searchbarCfg = {
  getSyns: function ()
  {
    var synonyms = []
    var qtd      = $( '#tableSyn .word' ).length
    for ( let i = 0; i < qtd; i++ ) {
      var word = $( '#tableSyn .word' )[ i ]
      var syn  = $( '#tableSyn .syn' )[ i ]

      synonyms.push( {
        'word': word,
        'synonym': syn
      } )
    }

    return synonyms
  },

  bindListeners: function ()
  {
    // botao editar
    $( '.btn-edit-syn' ).off( 'click' )
    $( '.btn-edit-syn' ).click( function ()
    {
      var word = $( this ).parent().parent().find( '.sb-word' ).html()
      var syn  = $( this ).parent().parent().find( '.sb-syn' ).html()

      // exclui a linha
      $( this ).parent().parent().remove()

      $( '#cfgSbWord' ).val( word )
      $( '#cfgSbSyn' ).val( syn )
      $( '#btnAddSyn' ).removeAttr( 'disabled' )
    } )

    // botão remover
    $( '.btn-remove-syn' ).off( 'click' )
    $( '.btn-remove-syn' ).click( function ()
    {
      // exclui a linha
      $( this ).parent().parent().remove()
    } )

    // campos
    $( '#cfgSbWord' ).off( 'keyup' )
    $( '#cfgSbWord' ).off( 'keydown' )
    $( '#cfgSbSyn' ).off( 'keyup' )
    $( '#cfgSbSyn' ).off( 'keydown' )

    $( '#cfgSbWord' ).keyup( function ()
    {
      if ( $( '#cfgSbWord' ).val().trim() != '' && $( '#cfgSbSyn' ).val().trim() != '' ) {
        $( '#btnAddSyn' ).removeAttr( 'disabled' )
      } else {
        $( '#btnAddSyn' ).attr( 'disabled', 'true' )
      }
    } )

    $( '#cfgSbSyn' ).keyup( function ()
    {
      if ( $( '#cfgSbWord' ).val().trim() != '' && $( '#cfgSbSyn' ).val().trim() != '' ) {
        $( '#btnAddSyn' ).removeAttr( 'disabled' )
      } else {
        $( '#btnAddSyn' ).attr( 'disabled', 'true' )
      }
    } )

    $( '#cfgSbSyn' ).keydown( function ( e )
    {
      if ( e.key == 'Enter' ) {
        $( '#btnAddSyn' ).click()
      }
    } )
    $( '#cfgSbWord' ).keydown( function ( e )
    {
      if ( e.key == 'Enter' ) {
        $( '#btnAddSyn' ).click()
      }
    } )

    // botao adicionar
    $( '#btnAddSyn' ).off( 'click' )
    $( '#btnAddSyn' ).click( function ()
    {
      var w     = searchbarCfg.getValues() // pega os valores para checar se ja estao cadastrados
      var words = []
      for ( var i = 0; i < w.length; i++ ) {
        words.push( w[ i ].word )
      }

      // checa se uma das palavras está vazia
      if ( $( '#cfgSbWord' ).val().trim() == '' || $( '#cfgSbSyn' ).val().trim() == '' ) {
        // só exibe o alerta se ja n estiver exibindo
        if ( $( '#msgSearchBarCfg' ).length == 0 ) {
          $( '#searchbarCfgMsgs' ).html(
            '<div id="msgSearchBarCfg" class="alert alert-danger alert-dismissible fade in mb-2" role="alert">' +
            '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
            '<span aria-hidden="true">×</span>' +
            '</button>' +
            'Você deve cadastrar a <strong>palavra</strong> e o <strong>sinônimo</strong> antes de clicar em adicionar.' +
            '</div>'
          )

          setTimeout( function ()
          {
            $( '#msgSearchBarCfg' ).alert( 'close' )
            window[ 'isAboutToClose' ] = false
          }, 3000 )
        }
        return false
      }
      // checa se a palavra já esta cadastrada
      else if ( words.includes( $( '#cfgSbWord' ).val().trim().toLocaleLowerCase() ) ) {
        // só exibe o alerta se ja n estiver exibindo
        if ( $( '#msgSearchBarCfg' ).length == 0 ) {
          $( '#searchbarCfgMsgs' ).html(
            '<div id="msgSearchBarCfg" class="alert alert-danger alert-dismissible fade in mb-2" role="alert">' +
            '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
            '<span aria-hidden="true">×</span>' +
            '</button>' +
            'A <strong>palavra</strong> que você está tentando cadastrar já foi cadastrada.' +
            '</div>'
          )

          setTimeout( function ()
          {
            $( '#msgSearchBarCfg' ).alert( 'close' )
            window[ 'isAboutToClose' ] = false
          }, 3000 )
        }
        return false
      }

      var html =
            '<tr>' +
            '<td class="sb-word">' + $( '#cfgSbWord' ).val().toLocaleLowerCase() + '</td>' +
            '<td class="sb-syn">' + $( '#cfgSbSyn' ).val().toLocaleLowerCase() + '</td>' +
            '<td class="text-xs-center">' +
            '<button class="btn btn-sm btn-info white btn-edit-syn"><i class="fa fa-pencil"></i></button>' +
            '</td>' +
            '<td class="text-xs-center">' +
            '<button class="btn btn-sm btn-danger white btn-remove-syn"><i class="fa fa-trash"></i></button>' +
            '</td>' +
            '</tr>'

      // limpa campos
      $( '#cfgSbWord' ).val( '' )
      $( '#cfgSbSyn' ).val( '' )

      // foca no primeiro campo
      $( '#cfgSbWord' ).focus()

      // atualiza valores
      $( '#tableSyn tbody' ).html(
        $( '#tableSyn tbody' ).html() + html
      )

      // desabilita botao adicionar
      $( '#btnAddSyn' ).attr( 'disabled', 'true' )

      // Adiciona os listeners novamente
      searchbarCfg.bindListeners()
    } )
  },

  init: function ()
  {
    this.bindListeners()
  },

  getValues: function ()
  {
    var data = []

    // sinonimos
    var syn       = $( '#tableSyn tbody .sb-syn' )
    var word      = $( '#tableSyn tbody .sb-word' )
    data.synonyms = []

    for ( var i = 0; i < syn.length; i++ ) {
      data.push( {
        'word': $( word )[ i ].innerHTML,
        'syn': $( syn )[ i ].innerHTML,
      } )
    }

    return data
  },

  loadSynonyms: function ( syn )
  {
    var html = ''
    // sinonimos
    for ( var i = 0; i < syn.length; i++ ) {
      html +=
        '<tr>' +
        '<td class="sb-word">' + syn[ i ].word + '</td>' +
        '<td class="sb-syn">' + syn[ i ].syn + '</td>' +
        '<td class="text-xs-center">' +
        '<button class="btn btn-sm btn-info white btn-edit-syn"><i class="fa fa-pencil"></i></button>' +
        '</td>' +
        '<td class="text-xs-center">' +
        '<button class="btn btn-sm btn-danger white btn-remove-syn"><i class="fa fa-trash"></i></button>' +
        '</td>' +
        '</tr>'
    }
    $( '#tableSyn tbody' ).html( html )
    searchbarCfg.bindListeners()
  }
}

// BOTAO SALVAR
$( '#btn-salva-busca' ).click( function ()
{
  var idWid = $( '#rhIdWidBusca' ).html()

  var data = {}

  data.synonyms = searchbarCfg.getValues()

  data = JSON.stringify( data )

  $.ajax( {
    'type': 'post',
    'url': 'resource/resource_widget_edit.php',
    'data': { 'idCli': idCli, 'op': 7, 'idWid': idWid, data },
    'success': function ( response )
    {
      $( '#modalConfiguraBusca' ).modal( 'hide' )
      toastr[ 'success' ]( 'As configurações da sua barra foram atualizadas!' )
    }
  } )
} )

searchbarCfg.init()

validaProdutos = function ()
{
  // produtos manuais
  if ( $( '#inputProdutos' ).parent().is( ':visible' ) && $( '#inputProdutos' ).val().split( ',' ).length < 4 ) {
    if ( !$( '#divTagsProdutos' ).next().hasClass( 'help-block' ) ) {
      $( '#divTagsProdutos' ).css( 'border-color', '#FF7588' ) // BORDA VERMELHRA
      $( '#divTagsProdutos' ).after( '<div style="color: #FF7588 !important;" class="help-block"><ul role="alert"><li>Adicione pelo menos quatro produtos</li></ul></div>' )
    }
  } else {
    $( '#divTagsProdutos' ).css( 'border-color', '#16D39A' ) // BORDA VERMELHRA
    if ( $( '#divTagsProdutos' ).next().hasClass( 'help-block' ) ) {
      $( '#divTagsProdutos' ).next().remove()
    }
  }
}

adicionaProdutos = function ()
{
  if ( $( '#produto' ).val().trim() === '' ) {
    toastr[ 'error' ]( 'Você precisa digitar um nome para o produto' )
    $( '#produto' ).focus()
    return false
  }

  var tamanho = $( '#inputProdutos' ).val().split( ',' ).length

  if ( tamanho >= 24 ) {
    toastr[ 'error' ]( 'Você pode cadastrar no máximo 24 produtos' )
    return false
  }
  $( '#inputProdutos' ).tagsinput( 'add', $( '#produto' ).val() )
  $( '#produto' ).val( '' )

  if ( tamanho < 24 ) {
    $( '#produto' ).focus()
  }
  return false
}

function preencheCampoAutoOfertaLimitada( titulo, id )
{
  $( '#manualOfertaLimitadaInput' ).val( titulo )
  bossChoiceProdId     = id
  bossChoiceProdTitulo = titulo.replace( ',', '.' )
  $( '#listaProdutosAutocomplete' ).fadeOut()
  $( '#listaProdutosAutocomplete' ).html( '' )

  window[ 'prodManualOl' ] = {
    'id': id,
    'titulo': titulo.replace( ',', '.' )
  }
}

function preencheCampoAuto( titulo, id, formato )
{
  var nomeInput = '#produtoManual'
  nomeDiv       = '#listaProdutosAutocomplete'

  if ( formato == 6 ) {
    nomeInput = '#manualOfertaLimitadaInput'
    nomeDiv   = '#listaProdutosAutocompleteOfertaManual'
  }

  $( nomeInput ).val( titulo )
  bossChoiceProdId     = id
  bossChoiceProdTitulo = titulo.replace( ',', '.' )
  $( nomeDiv ).fadeOut()
  $( nomeDiv ).html( '' )
}

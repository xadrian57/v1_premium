'use strict'
$( document ).ready( function ()
{
  var widgets = {
    init: function ()
    {
      $.ajax( {
        type: 'POST',
        url: 'resource/resource_widget_edit.php',
        data: { 'idCli': idCli, 'op': 1 },
        success: function ( result )
        {
          //console.log(result);
          var widgetInfo = JSON.parse( result )

          window.__busca             = widgetInfo.widgetsBusca
          window.buscaTipo           = widgetInfo.buscaTipo
          window.autocompleteFormato = widgetInfo.autocompleteFormato

          // INICIA AS FUNÇÕES PRINCIPAIS
          widgets.loadSearch()
        }
      } )
    },

    loadSearch: function ()
    {
      $( '#buscaContent' ).show()
      if ( window.buscaTipo == 1 )
        document.querySelector( '#cardAutocomplete' ).remove() // é BUSCA esconde o outro
      else if ( window.buscaTipo == 2 ) {
        var $autocompleteFormato = $( '#autocompleteFormato' )
        if ( $autocompleteFormato ) $autocompleteFormato.val( parseInt( window.autocompleteFormato ) )

        document.querySelector( '#cardBusca' ).remove() // é AUTOCOMPLETE esconde o outro
      }

      var $widId = document.querySelector( '.wid_id' )

      var wid = __busca[ 0 ]

      $widId.innerHTML = wid.id
    },

    saveFormat: function ()
    {
      //salvar formato de autocomplete
      var $autocompleteFormato = document.getElementById( 'autocompleteFormato' )
      $.ajax( {
        type: 'POST',
        url: 'resource/resource_widget_edit.php',
        data: { 'idCli': idCli, 'op': 9, formato: $autocompleteFormato.value },
        success: function ( result )
        {
          toastr[ 'success' ]( 'As configurações do seu autocomplete foram atualizadas!' )
        }
      } )
    }
  }

  $( '.btn-configura-busca' ).click( function ()
  {
    var id = document.querySelector( '.wid_id' ).innerHTML

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
      }
    } )
  } )

  $( '.btn-configura-autocomplete' ).click( function ()
  {
    widgets.saveFormat()
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
    var id = document.querySelector( '.wid_id' ).innerHTML

    var data = {}

    data.synonyms = searchbarCfg.getValues()

    data = JSON.stringify( data )

    $.ajax( {
      'type': 'post',
      'url': 'resource/resource_widget_edit.php',
      'data': { 'idCli': idCli, 'op': 7, 'idWid': id, data },
      'success': function ( response )
      {
        $( '#modalConfiguraBusca' ).modal( 'hide' )
        toastr[ 'success' ]( 'As configurações da sua barra foram atualizadas!' )
      }
    } )
  } )

  searchbarCfg.init()
  widgets.init()

} )
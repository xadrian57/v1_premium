/*
*   Data: --/09/2017
*   Update: 02/08/2018
*/

// ****** VARIÁVEIS GLOBAIS ****************************************
// resultados_busca <-- ARMAZENA OS RESULTADOS DAS PESQUISAS **
// rh_lite_obj = <-- ARRAY CONTENDO TODOS OS PRODUTOS             **
// *****************************************************************

window['rhSearchBarSendReq'] = function(idCli,idWid){
    // REALIZAR ESSA FUNÇÃO NO INICIO DO PROCESSAMENTO
    var css = document.createElement('link');
    css.async = true;
    css.rel="stylesheet";
    css.type="text/css";
    css.href = 'https://www.roihero.com.br/searchbar/templates/sb_'+idCli+'/sb_'+idCli+'.css';

    // checa se carregou o css
    if (css.readyState) {
        css.onreadystatechange = function () {
            console.log('carregou');
            window['rh_sb_css_ready'] = true;
        };
    } else {
        css.onload = function () {
            console.log('carregou');
            window['rh_sb_css_ready'] = true;
        };
    };

    var rh_s = document.getElementsByTagName('link')[0];
    rh_s.parentNode.insertBefore(css, rh_s);

    var req = new XMLHttpRequest();
    req.open('post','https://www.roihero.com.br/widget/templates/kit_'+idCli+'/get_searchbar.php',true);
   

    // FUNÇÃO QUE LÊ OS MAIS PESQUISADOS E RETORNA O CONTEÚDO HTML DA BARRA DE BUSCA
    var formData = new FormData();
    formData.append('idwid',idWid);
    formData.append('idcli',idCli);
    formData.append('url',window.location.href);

    var req;
    if (window.XMLHttpRequest){
        req = new XMLHttpRequest();
    }
    else if (window.ActiveXObject){
        req = new ActiveXObject("Microsoft.XMLHTTP");
    }

    var url = 'https://www.roihero.com.br/searchbar/get_searchbar.php';
    req.open('post', url, true);

    req.onreadystatechange = function()
    {
        // Verifica se o Ajax realizou todas as operações corretamente (essencial)
        if(req.readyState == 4 && req.status == 200)
        {   
            goOn = function(){
                rh_lite_termos_personalizados = [];
                rh_lite_mais_buscados = [];

                window['rh_lite_termos'] = req.responseText.replace(/\+/g, ' ');


                try {
                    rh_lite_termos = JSON.parse(rh_lite_termos);

                    // decodifica
                    for (var i = 0; i < rh_lite_termos[0].length; i++) {
                        rh_lite_termos[0][i] = decodeURIComponent(rh_lite_termos[0][i]);
                    }

                    rh_lite_termos[1].descri = decodeURIComponent(rh_lite_termos[1].descri);
                    rh_lite_termos[1].imagem = decodeURIComponent(rh_lite_termos[1].imagem);
                    rh_lite_termos[1].link = decodeURIComponent(rh_lite_termos[1].link);
                    rh_lite_termos[1].termo = decodeURIComponent(rh_lite_termos[1].termo);
                    rh_lite_termos[1].titulo = decodeURIComponent(rh_lite_termos[1].titulo);

                    console.log('Termos/conteudo:');
                    console.log(rh_lite_termos);

                    // CHECK DE SEGURANÇA
                    // CASO N TENHA NENHUM RANK DE PRODUTOS MAIS BUSCADOS AINDA NO BANCO, RECEBE VAZIO
                    if (rh_lite_termos[0]["termos"] === "") {
                        rh_lite_mais_buscados = [];
                    } else { // CASO TENHA, RETIRAR OS REPETIDOS
                        rh_lite_mais_buscados = rh_lite_termos.slice(0,rh_lite_termos.length-1)[0];

                        for (var i = 0; i < rh_lite_mais_buscados.length; i++) {
                            rh_lite_mais_buscados[i] = rh_lite_mais_buscados[i].trim().toLowerCase();
                        }
                        // NOVA ARRAY Q RECEBE OS PRODUTOS QUE NAO SAO REPETIDOS
                        var uniqueArray = rh_lite_mais_buscados.filter(function(item, pos) {
                            return rh_lite_mais_buscados.indexOf(item) == pos;
                        })

                        rh_lite_mais_buscados = uniqueArray.slice(0);
                    }

                    // INSERE BARRA DE BUSCA NO CONTAINER
                    document.getElementById('__rh-searchbar__').innerHTML = rh_lite_termos[2]['html'];
                    document.getElementById('rh-searchbar-main').style.display = 'block';

                    var inputCfg = document.getElementById('rh-searchbar-config');
                    window['searchbar'] = window['searchbar'] || {};
                    window['searchbar'].config = {
                        urlJson : 'http://www.roihero.com.br/JSON/get-content.php',
                        searchbar : document.getElementById(inputCfg.getAttribute('data-searchbar')),
                        searchbutton : document.getElementById(inputCfg.getAttribute('data-searchbutton')),
                        searchbarOverlay: document.getElementById(inputCfg.getAttribute('data-searchbar-overlay')),
                        searchbarContainer : document.getElementById(inputCfg.getAttribute('data-container-searchbar')),
                        searchbarResults : document.getElementById(inputCfg.getAttribute('data-container-resultados')),
                        searchbarResultsOverlay: document.getElementById(inputCfg.getAttribute('data-container-resultados-overlay')),
                        overlay : document.getElementById(inputCfg.getAttribute('data-container-overlay')),
                        similarWords: JSON.parse(inputCfg.getAttribute('data-similar-words')),
                        currency: inputCfg.getAttribute('data-currency') || 'R$',
                        customResults: inputCfg.getAttribute('data-custom-results'), // resultados fixos no dropdown com links personalizados para quando usuário clicar a primeira vez na barra
                        customSearchResults: inputCfg.getAttribute('data-rh-custom-results'),
                        paginate: inputCfg.getAttribute('data-paginate'), // paginar busca e coletar resultados do servidor
                        redirect: inputCfg.getAttribute('data-redirect') || false
                    }

                    var cfg = window['searchbar'].config;
                    rhSearchBar(cfg,idCli,idWid);

                } catch(e){
                    rh_lite_termos = JSON.parse('[{"termos":""},{"termo":"","link":"","titulo":"","descri":"","imagem":""}]');
                    console.warn(e);
                }

                // TERMOS PERSONALIZADOS
                // CRIA UM OBJ DE A PARTIR DOS PARAMETROS DOS TERMOS PERSONALIZADOS
                function rh_lite_organizaTermos() {
                    // CHECA PRIMEIRO SE A ARRAY EXISTE
                    if (rh_lite_termos.slice(1)[0].termo === '' || rh_lite_termos.slice(1)[0].termo === null) { return false; }
                    var termo = rh_lite_termos.slice(1)[0].termo.split(',');
                    var link = rh_lite_termos.slice(1)[0].link.split(',');
                    var titulo = rh_lite_termos.slice(1)[0].titulo.split(',');
                    var descri = rh_lite_termos.slice(1)[0].descri.split(',');
                    var imagem = rh_lite_termos.slice(1)[0].imagem.split(',');
                    for (var i = 0; i < rh_lite_termos.slice(1)[0].termo.split(',').length; i++) {
                        rh_lite_termos_personalizados.push(
                            {
                                'termo':termo[i],
                                'link':link[i],
                                'titulo':titulo[i],
                                'descri':descri[i],
                                'imagem':imagem[i]
                            }
                        );
                    }
                }

                // TERMOS PERSONALIZADOS
                rh_lite_organizaTermos();

                // TOUCH
                if (typeof rhInitHammer !== 'undefined') {
                    rhInitHammer();
                }
            }
            
            // checa se carregou o css
            if (window['rh_sb_css_ready']) {
                goOn();
            }
            else if (css.readyState) {
                css.onreadystatechange = function () {
                    console.log('carregou');
                    goOn();
                };
            } else {
                css.onload = function () {
                    console.log('carregou');
                    goOn();
                };
            };
                        
            // MAGIC ends
        }
    }
    req.send(formData);
    // ------------------------------------------------------------------------------

}

// FUNCAO PRINCIPAL Q INICIA TODAS AS OUTRAS FUNCOES
rhSearchBar = function(cfg,idCli,idWid){
    if (cfg.searchbar){
        cfg.searchbar.style.pointerEvents = 'initial'; // DEIXANDO A BARRA CLICÁVEL ASSIM QUE CARREGAR A PÁGINA
        cfg.searchbutton.style.pointerEvents = 'initial'; // DEIXANDO O BOTÃO CLICÁVEL ASSIM QUE CARREGAR A PÁGINA
    } else {
        setTimeout(function(){
            cfg.searchbar.style.pointerEvents = 'initial'; // DEIXANDO A BARRA CLICÁVEL ASSIM QUE CARREGAR A PÁGINA
            cfg.searchbutton.style.pointerEvents = 'initial'; // DEIXANDO O BOTÃO CLICÁVEL ASSIM QUE CARREGAR A PÁGINA
        },1000);
    }

    rh_lite_segundos_sb = 1; // TIMER INPUT
    // FUNÇÃO QUE LÊ O ARQUIVO JSON
    function rh_lite_leProdutos(idCli){
        var
        req2,
        idCli = idCli,
        formData = new FormData();
        formData.append('id',idCli);

        // Verificar o Browser
        // Firefox, Google Chrome, Safari e outros
        if(window.XMLHttpRequest)
        {
            req2 = new XMLHttpRequest();
        }
        // Internet Explorer
        else if(window.ActiveXObject)
        {
            req2 = new ActiveXObject("Microsoft.XMLHTTP");
        }

        var url = 'https://www.roihero.com.br/JSON/get-content.php';
        req2.open("post", url, true);

        // Quando o objeto recebe o retorno, chamamos a seguinte função;
        req2.onreadystatechange = function()
        {
            var idWid = document.getElementById('rh_lite_searchbar_wid').value;
            // Verifica se o Ajax realizou todas as operações corretamente (essencial)
            if(req2.readyState == 4 && req2.status == 200)
            {

                // Resposta retornada pelo widget_view.php
                rh_lite_obj = req2.responseText;
                rh_lite_obj = JSON.parse(rh_lite_obj);

                rh_lite_obj = rh_lite_obj.filter(function(prod){
                    return prod.title !== null && prod.in_stock == 1;
                });

                rh_lite_obj = rh_lite_obj.sort(function(a , b){
                    if (parseFloat(a.click) <= parseFloat(b.click)){
                        return 1;
                    } else {
                        return -1;
                    }
                });

                for (var x = 0;x < rh_lite_obj.length; x++){
                    if (parseInt(rh_lite_obj[x].sale_price) <= 0 // VERIFICANDO SE EXISTE PREÇO PROMOCIONAL
                        || rh_lite_obj[x].sale_price === null    // CASO Ñ EXISTA, TROCA O PREÇO PROMOCIONAL(Q É O Q MOSTRA)
                        || rh_lite_obj[x].sale_price === ''      // PELO PREÇO NORMAL E PREÇO NORMAL RECEBE VAZIO
                        || rh_lite_obj[x].sale_price == rh_lite_obj[x].price)
                    {
                        rh_lite_obj[x].sale_price = window['searchbar'].config.currency+rh_lite_obj[x].price.toFixed(2);
                        rh_lite_obj[x].sale_price = (window['searchbar'].config.currency === 'R$') ? rh_lite_obj[x].sale_price.replace('.',',') : rh_lite_obj[x].sale_price;
                        rh_lite_obj[x].price = '';
                    } else {
                        rh_lite_obj[x].sale_price = window['searchbar'].config.currency+rh_lite_obj[x].sale_price.toFixed(2);
                        rh_lite_obj[x].sale_price = (window['searchbar'].config.currency === 'R$') ? rh_lite_obj[x].sale_price.replace('.',',') : rh_lite_obj[x].sale_price;

                        rh_lite_obj[x].price = window['searchbar'].config.currency+rh_lite_obj[x].price.toFixed(2);
                        rh_lite_obj[x].price = (window['searchbar'].config.currency === 'R$') ? rh_lite_obj[x].price.replace('.',',') : rh_lite_obj[x].price;
                    }

                    if (parseInt(rh_lite_obj[x].months) <= 1 // VERIFICANDO SE EXISTE PARCELAMENTO
                        || rh_lite_obj[x].months === null    // CASO EXISTA, TROCA O A QUANTIDADE DE PARCELAS(Q É O Q MOSTRA)
                        || rh_lite_obj[x].months === '')     // PELA STRING COM AS PARCELAS E A QUANTIDADE
                    {
                        rh_lite_obj[x].months = '';
                    } else {
                        rh_lite_obj[x].months = rh_lite_obj[x].months+'x '+window['searchbar'].config.currency+rh_lite_obj[x].amount.toFixed(2);

                        rh_lite_obj[x].months = (window['searchbar'].config.currency === 'R$') ? rh_lite_obj[x].months.replace('.',',') : rh_lite_obj[x].months;
                    }

                    // CASO OS CLICKS SEJAM == NULL, MUDA PRA ZERO
                    if (rh_lite_obj[x].click === null || rh_lite_obj[x].click === undefined){
                        rh_lite_obj[x].click = 0;
                    }

                    // DECODIFICANDO OS NOMES E OS LINKS
                    rh_lite_obj[x].title =  decodeURIComponent(rh_lite_obj[x].title.replace(/\+/g, ' '));
                    rh_lite_obj[x].link = decodeURIComponent(rh_lite_obj[x].link)+'?idwid='+idWid;
                    rh_lite_obj[x].link_image = decodeURIComponent(rh_lite_obj[x].link_image);
                    rh_lite_obj[x].type = decodeURIComponent(rh_lite_obj[x].type).replace(/\+/g, ' ');
                    
                }


                console.log('Produtos:');
                console.log(rh_lite_obj);
            }
        }
        req2.send(formData);
    }

    // só lê produtos se a configuracao for false
    if (window['searchbar'].config.paginate !== 'true') { 
        rh_lite_leProdutos(idCli); // LENDO PRODUTOS
    } else {
        console.log('paginação servidor ativa');
    }
    

    // NOVA FUNCAO D BUSCA
    rhSearchProducts = function(searchTerm){
        if (rhClientIdSb === "12c6fc06c99a462375eeb3f43dfd832b08ca9e17" || 
            rhClientIdSb === "823b29ffd8dbab9367eddca53376226a17cdc00f" ||
            rhClientIdSb === "7c7b84eeaec18233e982d101637ab2a4033c6fb0" ||
            rhClientIdSb === "1784f0e37c1fdd6200c1e8b28e8caae5402e74e0" ||
            rhClientIdSb === "bda09ba2c0046773a13bfac20bf620d2317adbf6" ||
            rhClientIdSb === "3c2675338c88905be5329fb284b89482fbfc872a"
            ) {
            if (!isNaN(parseInt(searchTerm[0]))) {
                console.log('pesquisa por referencia');
                resultados_busca = rh_lite_obj.filter(function(produto){
                    return (produto.productReference.toLowerCase().indexOf(searchTerm.toLowerCase()) === 0);
                });
                return resultados_busca;
            }
        }

        // pesquisa por sku
        if (rhClientIdSb === '818307e00b8ddd4f8f671d975f099a1adf3b6149') {
            if (!isNaN(parseInt(searchTerm.trim()))) {
                resultados_busca = rh_lite_obj.filter(function(produto){
                    return (produto.id === searchTerm.trim() || produto.sku === searchTerm.trim());
                });
                
                // SE ENCONTRAR O ID, SAI DA FUNÇÃO
                if (resultados_busca.length >= 1) {
                    return false;
                }
            }
        }        

        if (searchTerm[0] === '#'){
            resultados_busca = rh_lite_obj.filter(function(produto){
                return (produto.id === searchTerm.replace('#','') || produto.sku === searchTerm.replace('#',''));
            });
            // SE ENCONTRAR O ID, SAI DA FUNÇÃO
            if (resultados_busca.length >= 1){
                return false;
            }
        }

        searchTerm = rhRemoveAcento(searchTerm.toLowerCase().trim()); // LIMPANDO A STRING
        var
        produtos = rh_lite_obj.slice(0),
        score = 0,
        result = [];

        // (palavras similares / erros gramaticais) cadastrados
        var sm = window['searchbar'].config.similarWords;

        for (var i = 0; i < sm.length; i++) {
            // {'word': ['mistake', 'mistake']}
            var
            mistakes = sm[i],
            correctWord = Object.keys(mistakes)[0],
            mistakes = mistakes[correctWord];

            for (var j = 0; j < mistakes.length; j++) {
                var mistake = mistakes[j];
                if(searchTerm.indexOf(mistake) >= 0){
                    if (searchTerm == mistake){
                        searchTerm = correctWord;
                    } else {
                        var arrayPalavras = searchTerm.split(' ');
                        for (var k = 0; k < arrayPalavras.length; k++) {
                            palavra = arrayPalavras[k];
                            if (palavra === mistake){
                                searchTerm = searchTerm.replace(mistake,correctWord);
                            }
                        }
                    }
                }
            }
        }

        for (var i = 0; i < produtos.length; i++) {
            var
            prod = produtos[i],
            prodName = rhRemoveAcento(prod.title.toLowerCase().trim()),
            wordsInProd = prodName.split(' '),
            wordsInSearch = searchTerm.split(' ');

            if (searchTerm == prodName) {
                score+=10;
            }

            // levenshtein palavra inteira
            if (rh_lite_lev(prodName,searchTerm)) {
                score+=10;
            }

            if (prodName.indexOf(searchTerm) == 0){
                score+=10;
            }

            if (prodName.indexOf(searchTerm) >= 0 && searchTerm.indexOf(searchTerm) >= 0){
                score+=10;
            }
                
            if(wordsInSearch[0] === wordsInProd[0]){
                score+=10;
            }

            if(wordsInSearch[0].indexOf(wordsInProd[0]) >= 0){
				score+=10;
			}

			if( wordsInProd[0].indexOf(wordsInSearch[0]) >= 0){
				score+=10;
			} 

            // levenshtein primeira palavra
            if (rh_lite_lev(wordsInProd[0],wordsInSearch[0])) {
                score+=10;
            }

            for (var j = 0; j < wordsInSearch.length; j++) {
                var word = wordsInSearch[j].trim();

                if (word !== '' && prodName.indexOf(word) >= 0){
                    score+=10;
                }
            }

            if (searchTerm.indexOf(prodName) >= 0){
                score+=10;
            }

            if (prodName.indexOf(searchTerm) >= 0){
                score+=10;
            }

            if (prodName[0] == searchTerm[0]) {
                score+=5;
            }

            prod.score = score;
            result.push(prod);
            score = 0; // reseta score
        }

        // se todos os scores estiverem zerados(nenhum resultado encontrado), ranqueia pelos mais clicados
        if (!result.some(function(prod){
            return prod.score > 0;
        })){
            result = result.sort(function(a , b){
               if (parseFloat(a.click) <= parseFloat(b.click)){
                  return 1;
               } else {
                  return -1;
               }
            });
            console.log('Nenhum produto encontrado para '+searchTerm+'!');
        }
        // senão, ranqueia pelo score
        else {
            result = result.sort(function(a , b){
               if (parseFloat(a.score) <= parseFloat(b.score)){
                  return 1;
               } else {
                  return -1;
               }
            });
        }

        resultados_busca = result.slice(0,15);
    }

    // FUNÇÃO DO TIMER DA BARRA DE BUSCA
    rhIntervaloBusca = function(){
        if (typeof rh_clock_intervalo !== 'undefined'){
            clearInterval(rh_clock_intervalo);
        }

        var interval = (typeof document.getElementById('rh-searchbar-config').getAttribute('data-delay') === 'undefined') ? 500 : parseFloat(document.getElementById('rh-searchbar-config').getAttribute('data-delay'));
        
        cfg.searchbarResults.classList.add('loading');        
        document.getElementById('rh_lite_results').scrollTop = 0; // reseta scroll
        window['__time__'] = Date.now(); // calcular tempo pesquisa

        rh_lite_segundos_sb = 0;
        window['rh_clock_intervalo'] = setInterval(function(){
            rh_lite_segundos_sb++;
            if (rh_lite_segundos_sb >= 1){
                if (!cfg.overlay.classList.contains('active')){
                    // dropdown efeito loading
                    rhTyping(cfg.searchbar);
                    clearInterval(rh_clock_intervalo);
                }

                // barra no overlay
                // if (cfg.searchbarResultsOverlay.classList.contains('active')){
                //     rhTypingOverlay(cfg.searchbarOverlay);
                //     clearInterval(rh_clock_intervalo);
                // }
            }
        }, interval);
    }

    // FUNÇÃO QUE FAZ AS BUSCAS NOS PRODUTOS - BARRA DE PESQUISA PRINCIPAL
    rhTyping = function(el){
        // ESCONDE OVERLAY
        rhHideOverlay();

        // searchbar active
        cfg.searchbarContainer.classList.add('active');

        var
        resultado = document.getElementById('rh_lite_results'),
        relacionados = document.getElementById('rh_lite_related_results');

        // CASO AINDA NÃO TENHA DIGITADO NADA, MOSTRAR OS TERMOS CADASTRADOS MANUALMENTE
        if (el.value.trim() === ''){             
            relacionados.innerHTML = '<ul id="rh_lite_table_result">'; // monta começo UL

            if (rh_lite_mais_buscados.length > 2) {
                // verifica se os termos customizados estao cadastrados
                if (window['searchbar'].config.customResults !== null) {
                    var customR = JSON.parse(window['searchbar'].config.customResults);

                    for (var i = 0; i < customR.length; i++) {
                        var termo = customR[i];

                        if (termo.link) {
                            document.getElementById('rh_lite_table_result').innerHTML+=
                            '<li class="rh_lite_mais_pesquisados"><a href="'+termo.link+'">'+termo.title+'</li>';
                        } else {
                            if (window['searchbar'].config.paginate === 'true') {
                                document.getElementById('rh_lite_table_result').innerHTML+=
                                '<li class="rh_lite_mais_pesquisados"><a onclick="(function(){'+
                                    'let el = document.getElementById(\'rh_lite_searchbar\');'+
                                    'el.value = \''+termo.title+'\';'+
                                    'el.focus();'+
                                    'el.click();'+
                                    'rhTyping(el)'+
                                '}())">'+termo.title+'</li>'; 
                            } else {
                                document.getElementById('rh_lite_table_result').innerHTML+=
                                '<li class="rh_lite_mais_pesquisados"><a onclick="rhPesquisaTermo(\''+termo.title+'\');">'+termo.title+'</li>';

                            }                            
                        }                        
                    };


                    var btnVerTodosOsResultados = document.querySelector('#rh_lite_searchbar_goto_results')

                    if (btnVerTodosOsResultados) {
                        btnVerTodosOsResultados.classList.remove('rh-active')
                    }

                }
                else {
                    document.getElementById('rh_lite_table_result').innerHTML+= '<h2 class="rh_lite_h2_sb">Os Mais Procurados</h2>';
                    for (var i = 0; i < rh_lite_mais_buscados.length; i++) {
                        var termo = rh_lite_mais_buscados[i];

                        document.getElementById('rh_lite_table_result').innerHTML+='<li class="rh_lite_mais_pesquisados"><a onclick="rhPesquisaTermo(this.innerHTML);">'+termo+'</li>';

                        if (i === 5){break;} // MAXIMO DE 5 TERMOS
                    };
                }
            }                

            relacionados.innerHTML += '</ul>';

            cfg.searchbarResults.classList.add('active'); // MOSTRANDO OS RESULTADOS
            resultado.style.display = 'none'; // ESCONDE RESULTADO PESQUISA
            relacionados.style.display = 'block';


            // remove loader
            cfg.searchbarResults.classList.remove('loading');
            return false;
        }

        if (window['searchbar'].config.customSearchResults !== null) {           

            var customR = JSON.parse(window['searchbar'].config.customSearchResults);

            for (var i = 0; i < customR.length; i++) {
                var customResult = customR[i];

                for (var j = 0; j < customResult.terms.length; j++) {
                    var term = customResult.terms[j];

                    if (term === el.value.trim()) {   
                        relacionados.innerHTML = '<ul id="rh_lite_table_result">'; // monta começo UL                     
                        document.getElementById('rh_lite_table_result').innerHTML+=
                        '<li class="rh_lite_mais_pesquisados rh_lite_custom_result"><a href="'+customResult.link+'">'+customResult.title+'</li>';
                        
                        relacionados.innerHTML += '</ul>'; // monta começo UL
                        cfg.searchbarResults.classList.add('active'); // MOSTRANDO OS RESULTADOS
                        resultado.style.display = 'none'; // ESCONDE RESULTADO PESQUISA
                        relacionados.style.display = 'block';


                        // remove loader
                        cfg.searchbarResults.classList.remove('loading');
                        return false;
                    }
                }                                     
            };
            
        }

        // PRIMEIRO, VERIFICA SE N É UM TERMO CADASTRADO MANUALMENTE
        if (rh_lite_termos_personalizados.length >= 1){ // APENAS VERIFICAR SE TIVER PELO MENOS 1 TERMO CADASTRADO
            pesquisa = rhRemoveAcento(el.value.toLowerCase());
            for (var i = 0; i < rh_lite_termos_personalizados.length; i++) {
                var termo = rhRemoveAcento(rh_lite_termos_personalizados[i].termo.toLowerCase());
                if (termo === pesquisa){            

                    var termo = rh_lite_termos_personalizados[i];
                    document.getElementById('rh_lite_results').innerHTML = '<a href="'+termo.link+'" target="_blank">'+
                                                                                '<figure class="rh_lite_searchbar_results_figure" style="width: 120px;">'+
                                                                                    '<img src="'+termo.imagem+'">'+
                                                                                '</figure>'+
                                                                                '<div class="rh_lite_searchbar_results_infoprod" style="top: 5px !important;">'+
                                                                                '<span class="rh_lite_searchbar_results_termotitle">'+
                                                                                        termo.titulo+
                                                                                    '</span>'+
                                                                                    '<span class="rh_lite_searchbar_results_termodesc">'+
                                                                                        termo.descri+
                                                                                    '</span>'+
                                                                                '</div>'+
                                                                           '</a>';

                    cfg.searchbarResults.classList.add('active'); // MOSTRANDO OS RESULTADOS
                    return false; // SAINDO DA FUNÇAO
                }
            };
        }

        relacionados.innerHTML = ""; // RESETANDO OS MAIS PESQUISADOS

        if (window['searchbar'].config.paginate === 'true') {
            rhSearchOnServer(el.value);
            return false;
        }

        rhSearchProducts(el.value); // PESQUISANDO

        resultado.style.display = 'block';
        relacionados.style.display = 'none';
        resultado.innerHTML = ''; // RESETANDO OS RESULTADOS
        resultadoString ='';      // ADICIONANDO O RESULTADO DA PESQUISA
        if (resultados_busca !== ''){
            for(var x=0; x<resultados_busca.length;x++){

                var prod = resultados_busca[x];
                resultadoString+='<a href="'+prod.link+'">'+
                                        '<figure class="rh_lite_searchbar_results_figure">'+
                                            '<img src="'+prod.link_image+'">'+
                                        '</figure>'+
                                        '<div class="rh_lite_searchbar_results_infoprod">'+
                                            '<span class="rh_lite_searchbar_results_nomeprod">'+
                                                prod.title+
                                            '</span>'+
                                            '<span class="rh_lite_searchbar_results_precon">'+
                                                prod.price+
                                            '</span>'+
                                            '<span class="rh_lite_searchbar_results_precop">'+
                                                prod.sale_price+
                                            '</span>'+
                                            '<span class="rh_lite_searchbar_results_parcelamento">'+
                                                prod.months+
                                            '</span>'+
                                        '</div>'+
                                   '</a>';
                if (x === 14){
                    break;
                }
            }

            // ver mais resultados
            resultadoString += '<span style="display: none;" id="rh_lite_searchbar_goto_results"> ver todos os resultados </span>'
            resultado.innerHTML = resultadoString;

            console.log(resultadoString)

            document.querySelector('#rh_lite_searchbar_goto_results').addEventListener('click', function () {
                var input = document.querySelector('#rh_lite_searchbar')
                var termo = input.value.trim()
                var inputCfg = document.querySelector('#rh-searchbar-config')
                var resultsPage = inputCfg.dataset.resultsPage

                if (termo !== '') {
                    window.location.href = resultsPage + termo
                }
            })
        }

        if (resultados_busca.length <= 0){ // CASO ENCONTRAR RESULTADO, MOSTRAR
            resultado.style.display = 'none';
            relacionados.innerHTML = '<ul id="rh_lite_table_result">';
            if (rh_lite_mais_buscados.length > 1) { document.getElementById('rh_lite_table_result').innerHTML+= '<h2 class="rh_lite_h2_sb">Termos mais pesquisados</h2>'; }
            for (var i = 0; i < rh_lite_mais_buscados.length; i++) {
                document.getElementById('rh_lite_table_result').innerHTML+='<li class="rh_lite_mais_pesquisados"><a onclick="rhPesquisaTermo(this.innerHTML);">'+rh_lite_mais_buscados[i]+'</li>';
                if (i === 9){break;} // MAXIMO DE 10 TERMOS
            };
        } else {
            setTimeout(function(){
                if (!cfg.overlay.classList.contains('active')){
                    cfg.searchbarResults.classList.add('active'); // MOSTRANDO OS RESULTADOS
                }
            },1000);
        }

        // remove loader
        setTimeout(function(){
            cfg.searchbarResults.classList.remove('loading');
            console.log('_______________________________________');
            console.log('Tempo da busca: '+((Date.now() - window['__time__']) / 1000 )+'s');
        },100);
    }

    // manda requisicao pra busca no servidor
    rhSearchOnServer = function(termo) {
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (xhr.status == 200 && xhr.readyState == 4) {
                var
                resultado = document.getElementById('rh_lite_results'),
                relacionados = document.getElementById('rh_lite_related_results');


                var result = JSON.parse( xhr.responseText );

                if (result['search']) {
                    if (result['search'][0]) {
                        result = result['search'];   
                    }
                }

                console.log('resultado:');
                console.log(result);
                console.log('--------------------------');

                resultado.style.display = 'block';
                relacionados.style.display = 'none';
                resultado.innerHTML = ''; // RESETANDO OS RESULTADOS
                resultadoString ='';      // ADICIONANDO O RESULTADO DA PESQUISA

                if (result.length > 0){
                    for(var x=0; x<result.length;x++){
                        // ajeita os dados
                            var utm = document.getElementById('rh_lite_searchbar_utm').value;
                            if (parseInt(result[x].sale_price) <= 0 // VERIFICANDO SE EXISTE PREÇO PROMOCIONAL
                                || result[x].sale_price === null    // CASO Ñ EXISTA, TROCA O PREÇO PROMOCIONAL(Q É O Q MOSTRA)
                                || result[x].sale_price === ''      // PELO PREÇO NORMAL E PREÇO NORMAL RECEBE VAZIO
                                || result[x].sale_price == result[x].price)
                            {
                                result[x].sale_price = window['searchbar'].config.currency+result[x].price.toFixed(2);
                                result[x].sale_price = (window['searchbar'].config.currency === 'R$') ? result[x].sale_price.replace('.',',') : result[x].sale_price;
                                result[x].price = '';
                            } else {
                                result[x].sale_price = window['searchbar'].config.currency+result[x].sale_price.toFixed(2);
                                result[x].sale_price = (window['searchbar'].config.currency === 'R$') ? result[x].sale_price.replace('.',',') : result[x].sale_price;

                                result[x].price = window['searchbar'].config.currency+result[x].price.toFixed(2);
                                result[x].price = (window['searchbar'].config.currency === 'R$') ? result[x].price.replace('.',',') : result[x].price;
                            }

                            if (parseInt(result[x].months) <= 1 // VERIFICANDO SE EXISTE PARCELAMENTO
                                || result[x].months === null    // CASO EXISTA, TROCA O A QUANTIDADE DE PARCELAS(Q É O Q MOSTRA)
                                || result[x].months === '')     // PELA STRING COM AS PARCELAS E A QUANTIDADE
                            {
                                result[x].months = '';
                            } else {
                                result[x].months = result[x].months+'x '+window['searchbar'].config.currency+result[x].amount.toFixed(2);

                                result[x].months = (window['searchbar'].config.currency === 'R$') ? result[x].months.replace('.',',') : result[x].months;
                            }


                            // CASO OS CLICKS SEJAM == NULL, MUDA PRA ZERO
                            if (result[x].click === null || result[x].click === undefined){
                                result[x].click = 0;
                            }

                            // DECODIFICANDO OS NOMES E OS LINKS
                            try {
                                result[x].title = decodeURIComponent(result[x].title.replace(/\+/g, ' '));
                                result[x].link = decodeURIComponent(result[x].link)+'?idwid='+idWid+'&utm='+utm;
                                result[x].link_image = decodeURIComponent(result[x].link_image);
                                result[x].type = decodeURIComponent(result[x].type).replace(/\+/g, ' ');
                            } catch(e) {
                                //
                            }
                        // -------------------

                        var prod = result[x];

                        if (prod.in_stock < 1) {
                            resultadoString+='<a href="'+prod.link+'" class="rh-out-of-stock">'+
                                                '<figure class="rh_lite_searchbar_results_figure">'+
                                                    '<img src="'+prod.link_image+'">'+
                                                '</figure>'+
                                                '<div class="rh_lite_searchbar_results_infoprod">'+
                                                    '<span class="rh_lite_searchbar_results_nomeprod">'+
                                                        prod.title+
                                                    '</span>'+
                                                    '<span class="rh_lite_searchbar_results_precon">'+
                                                        prod.price+
                                                    '</span>'+
                                                    '<span class="rh_lite_searchbar_results_precop">'+
                                                        prod.sale_price+
                                                    '</span>'+
                                                    '<span class="rh_lite_searchbar_results_parcelamento">'+
                                                        prod.months+
                                                    '</span>'+
                                                '</div>'+
                                            '</a>';
                        } else {
                            resultadoString+='<a href="'+prod.link+'" class="rh-out-of-stock">'+
                                                '<figure class="rh_lite_searchbar_results_figure">'+
                                                    '<img src="'+prod.link_image+'">'+
                                                '</figure>'+
                                                '<div class="rh_lite_searchbar_results_infoprod">'+
                                                    '<span class="rh_lite_searchbar_results_nomeprod">'+
                                                        prod.title+
                                                    '</span>'+
                                                    '<span class="rh_lite_searchbar_results_precon">'+
                                                        prod.price+
                                                    '</span>'+
                                                    '<span class="rh_lite_searchbar_results_precop">'+
                                                        prod.sale_price+
                                                    '</span>'+
                                                    '<span class="rh_lite_searchbar_results_parcelamento">'+
                                                        prod.months+
                                                    '</span>'+
                                                '</div>'+
                                        '</a>';
                        }

                        
                        
                    }

                    console.log(resultadoString)

                    resultado.innerHTML = resultadoString;

                    var btnVerTodosOsResultados = document.querySelector('#rh_lite_searchbar_goto_results')

                    if (btnVerTodosOsResultados) {
                        btnVerTodosOsResultados.classList.add('rh-active')
                    }

                    setTimeout(function(){
                        if (!cfg.overlay.classList.contains('active')){
                            cfg.searchbarResults.classList.add('active'); // MOSTRANDO OS RESULTADOS
                        }
                    },200);
                    // remove loader
                    setTimeout(function(){
                        cfg.searchbarResults.classList.remove('loading');
                        console.log('_______________________________________');
                        console.log('Tempo da busca: '+((Date.now() - window['__time__']) / 1000 )+'s');
                    },100);

                    resultados_busca = result;
                } else {
                    console.log('Nenhum resultado encontrado');
                    cfg.searchbarResults.classList.add('active'); // MOSTRANDO OS RESULTADOS
                    cfg.searchbarResults.classList.remove('loading'); 
                    resultado.innerHTML = '<b class="rh-no-results-found" style="margin:10px;">Nenhum resultado encontrado.</b>'
                }


            }
        }

        // var formData = new FormData();
        // var termo = rhRemoveAcento(termo).toLowerCase();
        // formData.append('idcli',rhClientIdSb);
        // formData.append('termo',termo);

        // xhr.send(formData);

        if (rhClientId === "b3f0c7f6bb763af1be91d9e74eabfeb199dc1f1f") {
            xhr.open('get','https://roihero.com.br/busca/get_busca.php?idcli='+rhClientIdSb+'&termo='+termo+'&limite=50');
        } else {
            xhr.open('get','https://roihero.com.br/busca/get_busca.php?idcli='+rhClientIdSb+'&termo='+termo+'&limite=24');
        }
        xhr.send(null);
    }

    // FUNÇÃO QUE FAZ AS BUSCAS NOS PRODUTOS - BARRA DE PESQUISA OVERLAY
    rhTypingOverlay = function (el){
        var resultado = document.getElementById('rh_lite_resultado_overlay');

        rhSearchProducts(el.value);

        resultado.innerHTML = ''; // RESETANDO OS RESULTADOS
        resultadoString ='';      // ADICIONANDO O RESULTADO DA PESQUISA
        if (resultados_busca !== ''){
            for(var x=0; x<resultados_busca.length;x++){
                var prod = resultados_busca[x];
                resultadoString+='<a href="'+prod.link+'">'+
                                    '<figure class="rh_lite_searchbar_results_figure">'+
                                        '<img src="'+prod.link_image+'">'+
                                    '</figure>'+
                                    '<div class="rh_lite_searchbar_results_infoprod">'+
                                        '<span class="rh_lite_searchbar_results_nomeprod">'+
                                            prod.title+
                                        '</span>'+
                                        '<span class="rh_lite_searchbar_results_precon">'+
                                            prod.price+
                                        '</span>'+
                                        '<span class="rh_lite_searchbar_results_precop">'+
                                            prod.sale_price+
                                        '</span>'+
                                        '<span class="rh_lite_searchbar_results_parcelamento">'+
                                            prod.months+
                                        '</span>'+
                                    '</div>'+
                               '</a>';
                if (x === 14){
                    break;
                }
            }
            resultado.innerHTML = resultadoString;
        }

        // SE NAO TIVER RESULTADOS, ESCONDER
        if (resultados_busca.length < 0){
            cfg.searchbarResultsOverlay.classList.remove('active');
        } else {
            cfg.searchbarResultsOverlay.classList.add('active');
        }

        document.getElementById('rh_lite_searchbar_slider_produtos').style.left = '0';  // RESETANDO POSIÇÃO DO SLIDER
        document.getElementById('rh_lite_searchbar_slider_produtos').style.right = '0'; // RESETANDO POSIÇÃO DO SLIDER
        return false;
    }

    // FUNÇÃO QUE ESCONDE OS RESULTADOS QUANDO O USUÁRIO CLICA FORA DA BARRA
    rhHideResults = function (){
        setTimeout(function(){
            cfg.searchbarContainer.classList.remove('active');
            cfg.searchbarResults.classList.remove('active');

            // LIMPA CRONOMETRO
            try {
                clearInterval(rh_clock_intervalo); // LIMPA RELÓGIO
            } catch(e){
                //faça nada
            }
        }, 400);
    }

    // FUNÇÃO QUE ESCONDE OS RESULTADOS QUANDO O USUÁRIO CLICA FORA DA BARRA - OVERLAY
    rhHideResultsOverlay = function (){
        cfg.searchbarResultsOverlay.classList.remove('active');
    }

    // FUNÇÃO QUE ORGANIZA AS INFORMACOES DO OVERLAY E JOGA OS PRODUTOS DENTRO DO SLIDER
    rhSetOverlay = function (){
        var termo = cfg.searchbar.value; // VALOR DO INPUT QUE O USUÁRIO DIGITOU PARA PESQUISAR

        if (!window.hasOwnProperty('rh_lite_obj') && !window.hasOwnProperty('rh_lite_termos_personalizados')) { return false;} // SE AINDA NAO TIVER LIDO OS PRODUTOS, SAIR DA FUNÇÃO

        // SEMPRE BUSCA NOVAMENTE PARA SETAR O SLIDER. DESSA FORMA CASO O USUÁRIO DIGITE RÁPIDO DEMAIS, NÃO É EXIBIDO A ÚLTIMA BUSCA QUE FOI FEITA AO INVÉS DA ATUAL
        if (window['searchbar'].config.paginate !== 'true') {
            rhSearchProducts(termo);
        }        

        // reseta tags filtros
        var tags = document.getElementsByClassName('rh_lite_orderby_option');
        for (var i = 0; i < tags.length; i++) {
            tags[i].classList.remove('active'); // DESATIVANDO TODAS AS TAGS
            tags[i].style.pointerEvents = 'initial';
        };

        // ALTERANDO TERMOS RELACIONADOS
        // CASO A BUSCA NÃO RETORNE NENHUM RESULTADO, ORDENAR PELOS MAIS VENDIDOS
        if (resultados_busca.length < 1) {
            resultados_busca = rh_lite_obj.sort(function(a , b){
                if (parseFloat(a.venda) <= parseFloat(b.venda)){
                    return 1;
                } else {
                    return -1;
                }
            });
            document.getElementById('rh_lite_buscou_por').innerHTML = '<h2 class="rh_lite_h2_sb">Não encontramos o que você procurava, esses são os melhores resultados</h2>';
        } else {
            document.getElementById('rh_lite_buscou_por').innerHTML = '<h2 class="rh_lite_h2_sb">Resultados aproximados para:</h2><span id="rh_lite_termo_busca">" '+termo+' "</span>';
        }

        // PESQUISAS RELACIONADAS
        var pesqRelacionadas = rhSimilarSearches(termo,rh_lite_mais_buscados);
        var pesquisasRelacionadas = document.getElementById('rh_lite_pesquisas_relacionadas_sb');
        if (pesqRelacionadas.length > 0){
            pesquisasRelacionadas.innerHTML = '<h2 class="rh_lite_h2_sb"> Pesquisas Relacionadas </h2>';
            if (pesqRelacionadas.length === 1) {
                pesquisasRelacionadas.innerHTML+=' <a onclick="rhPesquisaTermo(this.innerHTML);"> '+pesqRelacionadas[0]+' </a>';
            }
            else {
                for (var i = 0; i < pesqRelacionadas.length; i++) {
                    if (i == 3 || i == pesqRelacionadas.length-1){
                        pesquisasRelacionadas.innerHTML+=' <a onclick="rhPesquisaTermo(this.innerHTML);"> '+pesqRelacionadas[i]+' </a>';
                        break;
                    }
                    pesquisasRelacionadas.innerHTML+='<a onclick="rhPesquisaTermo(this.innerHTML);"> '+pesqRelacionadas[i]+' </a> <span>|<span>  ';

                };
            }

        } else {
            pesquisasRelacionadas.innerHTML = '';
        }

        var produtos = resultados_busca.slice();                              // COPIANDO RESULTADO DOS PRODUTOS PARA UMA ARRAY DIFERENTE
        var slider = document.getElementById('rh_lite_searchbar_slider_produtos'); // CONTAINER HTML ONDE FICAM OS PRODUTOS DO SLIDER
        var listaProdutos = '';         

        for (var x = 0;x<produtos.length;x++){
            // staples
            var tag = '';
            if (rhClientIdSb === '85f1002bf139bebdb7f0d07b31fa14155aea9dfc') {
                // selo papel
                var productName = produtos[x].nome.toLowerCase();
                if ( productName.indexOf('papel') != -1 && productName.indexOf('a0') != -1 ) {
                    tag='<span class="x-image__highlight x-image__highlight--a0">A0</span>';
                }
                
                if ( productName.indexOf('papel') != -1 && productName.indexOf('a1') != -1 ) {
                    tag='<span class="x-image__highlight x-image__highlight--a1">A1</span>';
                }
                
                if ( productName.indexOf('papel') != -1 && productName.indexOf('a2') != -1 ) {
                    tag='<span class="x-image__highlight x-image__highlight--a2">A2</span>';
                }
                
                if ( productName.indexOf('papel') != -1 && productName.indexOf('a3') != -1 ) {
                    tag='<span class="x-image__highlight x-image__highlight--a3">A3</span>';
                }
                
                if ( productName.indexOf('papel') != -1 && productName.indexOf('a4') != -1 ) {
                    tag='<span class="x-image__highlight x-image__highlight--a4">A4</span>';
                }
                
                if ( productName.indexOf('papel') != -1 && productName.indexOf('a5') != -1 ) {
                    tag='<span class="x-image__highlight x-image__highlight--a5">A5</span>';
                }
                
                if ( productName.indexOf('papel') != -1 && productName.indexOf('carta') != -1 ) {
                    tag='<span class="x-image__highlight x-image__highlight--carta">Carta</span>';
                }
            }
            

            listaProdutos+='<div class="rh_lite_produto_bus">'+
                                    '<figure class="rh_lite_figure_bus">'+
                                        '<a href="'+produtos[x].link+'">'+
                                            '<img class="rh_lite_imagem_bus" src="'+produtos[x].link_image+'" alt="'+produtos[x].nome+'">'+
                                            tag+
                                        '</a>'+
                                    '</figure>'+
                                    '<p class="rh_lite_nomeproduto_bus">'+
                                        '<a href="'+produtos[x].link+'">'+
                                            produtos[x].title+
                                        '</a>'+
                                    '</p>'+
                                    '<div class="rh_lite_precon_bus">'+
                                        '<a href="'+produtos[x].link+'">'+
                                            produtos[x].price+
                                        '</a>'+
                                    '</div>'+
                                    '<div class="rh_lite_precop_bus">'+
                                        '<a href="'+produtos[x].link+'">'+
                                            produtos[x].sale_price+
                                        '</a>'+
                                    '</div>'+
                                    '<div class="rh_lite_parcelamento_bus">'+
                                        '<a href="'+produtos[x].link+'">'+
                                            produtos[x].months+
                                        '</a>'+
                                    '</div>'+
                                    '<div class="rh_lite_btn_bus">'+
                                    '<a href="'+produtos[x].link+'">'+
                                        '<button class="rh_lite_button_bus">'+
                                            '<strong>'+
                                                '<span>COMPRAR</span>'+
                                            '</strong>'+
                                        '</button>'+
                                    '</a>'+
                                    '</div>'+
                                '</div>'+
                            '</div>';
            if (x === 14) { break; }
        }

        slider.innerHTML = "";
        slider.innerHTML+=listaProdutos; // ADICIONANDO OS PRODUTOS NO SLIDER
        // SETANDO TAMANHO DO SLIDER DE ACORDO COM A QUANTIDADE DE PRODUTOS
        slider.style.width = document.getElementById('rh_lite_searchbar_slider_produtos').children.length * 220+'px';

        switch(true){ // ESCONDE OU MOSTRA OS BOTOES DE ACORDO COM O TAMANHO DA TELA
            case(window.screen.width <= 480 && produtos.length < 2):
                document.getElementsByClassName('rh_lite_right-button_bus')[0].style.display = 'none';
                document.getElementsByClassName('rh_lite_left-button_bus')[0].style.display = 'none';
                break;
            case(window.screen.width <= 795 && window.screen.width > 480 && produtos.length < 3):
                document.getElementsByClassName('rh_lite_right-button_bus')[0].style.display = 'none';
                document.getElementsByClassName('rh_lite_left-button_bus')[0].style.display = 'none';
                break;
            case(window.screen.width <= 1023 && window.screen.width > 795 && produtos.length < 4):
                document.getElementsByClassName('rh_lite_right-button_bus')[0].style.display = 'none';
                document.getElementsByClassName('rh_lite_left-button_bus')[0].style.display = 'none';
                break;
            case(window.screen.width <= 1200 && window.screen.width > 1023 && produtos.length < 5):
                document.getElementsByClassName('rh_lite_right-button_bus')[0].style.display = 'none';
                document.getElementsByClassName('rh_lite_left-button_bus')[0].style.display = 'none';
                break;
            case (window.screen.width > 1200 && produtos.length <= 5):
                document.getElementsByClassName('rh_lite_right-button_bus')[0].style.display = 'none';
                document.getElementsByClassName('rh_lite_left-button_bus')[0].style.display = 'none';
                break;
            default:
                document.getElementsByClassName('rh_lite_right-button_bus')[0].style.display = 'block';
                document.getElementsByClassName('rh_lite_left-button_bus')[0].style.display = 'block';
                break;
        }
        document.getElementById('rh_lite_searchbar_slider_produtos').style.left = '0';  // RESETANDO POSIÇÃO DO SLIDER
        document.getElementById('rh_lite_searchbar_slider_produtos').style.right = '0'; // RESETANDO POSIÇÃO DO SLIDER
    }

    // FUNÇÃO QUE MOSTRA O OVERLAY
    rhShowOverlay = function (){
        // CHECA SE OS PRODUTOS JA CARREGARAM
        // PRA NAO MOSTRAR UM OVERLAY SEM PRODUTOS
        if (typeof rh_lite_obj === "undefined" && window['searchbar'].config.paginate !== 'true'){
            return false;
        }

        // checa se existe o array de resultados
        if (typeof resultados_busca === 'undefined') { return false }

        rhSetOverlay(); // ajusta OVERLAY
        rhHideResults(); // ESCONDE RESULTADOS BARRA DE BUSCA


        setTimeout(function(){
            cfg.overlay.classList.add('active');
        },100);
    }

    // FUNÇÃO QUE ARMAZENA OS TERMOS PESQUISADOS NO BANCO
    rhSaveSearch = function(p){
        p = p.trim();

        // só salva se não for um número
        if (isNaN(parseInt(p))) {
            if (typeof window['__rh-last-search__'] !== 'undefined') {
                if (window['__rh-last-search__'] === p) {
                    return false;
                } else {
                    window['__rh-last-search__'] = p;
                }
            }

            var data = new FormData;
            data.append('idCli',rhClientIdSb);
            data.append('busca',encodeURI(p));

            var xmlHttp = new XMLHttpRequest();
            xmlHttp.onreadystatechange = function()
            {
                if(xmlHttp.readyState == 4 && xmlHttp.status == 200)
                {
                    window['__rh-last-search__'] = p;
                    console.log('busca "'+p+'" - '+rhClientIdSb+' salva.');
                }
            }

            xmlHttp.open("POST", "https://roihero.com.br/searchbar/set_busca.php");
            xmlHttp.send(data);
        }        
    }

    // FUNÇÃO Q ORGANIZA OS RESULTADOS DE ACORDO COM O PREÇO/ MAIS VENDIDOS E DESCONTO
    rhSortProductsOverlay = function(produtos, cod, el){ // ARRAY DE PRODUTOS, CÓDIGO
        switch(cod){
            case 0: // MAIS VENDIDOS
                produtos = produtos.sort(function(a,b){
                    if (parseFloat(a.venda) <= parseFloat(b.venda)){
                        return 1;
                    } else {
                        return -1;
                    }
                });
                break;
            case 1: // MENOR PREÇO
                produtos = produtos.sort(function(a,b){
                    if (parseFloat(a.sale_price.replace("R$","").replace("$","").replace(",",".")) <= parseFloat(b.sale_price.replace("R$","").replace("$","").replace(",","."))){
                        return -1;
                    } else {
                        return 1;
                    }
                });
                break;
            case 2: // MAIOR PREÇO
                produtos = produtos.sort(function(a,b){
                    if (parseFloat(a.sale_price.replace("R$","").replace("$","").replace(",",".")) <= parseFloat(b.sale_price.replace("R$","").replace("$","").replace(",","."))){
                        return 1;
                    } else {
                        return -1;
                    }
                });
                break;
            case 3: // MAIOR DESCONTO
                produtos = produtos.sort(function(a,b){
                    if (parseFloat(a.desconto.replace("R$","").replace("$","").replace(",",".")) <= parseFloat(b.desconto.replace("R$","").replace("$","").replace(",","."))){
                        return 1;
                    } else {
                        return -1;
                    }
                });
                break;
        }

        // MUDANDO BG DA TAG PARA O USUARIO SABER QUAL ESTÁ SELECIONADO
        var tags = document.getElementsByClassName('rh_lite_orderby_option');
        for (var i = 0; i < tags.length; i++) {
            tags[i].classList.remove('active'); // DESATIVANDO TODAS AS TAGS
            tags[i].style.pointerEvents = 'initial';
        };
        el.classList.add('active'); // ATIVANDO A TAG SELECIONADA
        // desabilita botao
        el.style.pointerEvents = 'none';

        resultados_busca = produtos;

        var produtos = resultados_busca.slice();                              // COPIANDO RESULTADO DOS PRODUTOS PARA UMA ARRAY DIFERENTE
        var slider = document.getElementById('rh_lite_searchbar_slider_produtos'); // CONTAINER HTML ONDE FICAM OS PRODUTOS DO SLIDER
        var listaProdutos = '';                                                    // STRING VAZIA QUE VAI PREENCHER O CONTAINER DO SLIDER

        for (var x = 0;x<produtos.length;x++){
            // staples
            var tag = '';
            if (rhClientIdSb === '85f1002bf139bebdb7f0d07b31fa14155aea9dfc') {
                // selo papel
                var productName = produtos[x].nome.toLowerCase();
                if ( productName.indexOf('papel') != -1 && productName.indexOf('a0') != -1 ) {
                    tag='<span class="x-image__highlight x-image__highlight--a0">A0</span>';
                }
                
                if ( productName.indexOf('papel') != -1 && productName.indexOf('a1') != -1 ) {
                    tag='<span class="x-image__highlight x-image__highlight--a1">A1</span>';
                }
                
                if ( productName.indexOf('papel') != -1 && productName.indexOf('a2') != -1 ) {
                    tag='<span class="x-image__highlight x-image__highlight--a2">A2</span>';
                }
                
                if ( productName.indexOf('papel') != -1 && productName.indexOf('a3') != -1 ) {
                    tag='<span class="x-image__highlight x-image__highlight--a3">A3</span>';
                }
                
                if ( productName.indexOf('papel') != -1 && productName.indexOf('a4') != -1 ) {
                    tag='<span class="x-image__highlight x-image__highlight--a4">A4</span>';
                }
                
                if ( productName.indexOf('papel') != -1 && productName.indexOf('a5') != -1 ) {
                    tag='<span class="x-image__highlight x-image__highlight--a5">A5</span>';
                }
                
                if ( productName.indexOf('papel') != -1 && productName.indexOf('carta') != -1 ) {
                    tag='<span class="x-image__highlight x-image__highlight--carta">Carta</span>';
                }
            }

            if (produtos[x].in_stock < 1) {
                listaProdutos+='<div class="rh_lite_produto_bus" class="rh-out-of-stock">'+
                                    '<figure class="rh_lite_figure_bus">'+
                                        '<a href="'+produtos[x].link+'">'+
                                            '<img class="rh_lite_imagem_bus" src="'+produtos[x].link_image+'" alt="'+produtos[x].nome+'">'+
                                            tag+
                                        '</a>'+
                                    '</figure>'+
                                    '<p class="rh_lite_nomeproduto_bus">'+
                                        '<a href="'+produtos[x].link+'">'+
                                            produtos[x].title+
                                        '</a>'+
                                    '</p>'+
                                    '<div class="rh_lite_precon_bus">'+
                                        '<a href="'+produtos[x].link+'">'+
                                            produtos[x].price+
                                        '</a>'+
                                    '</div>'+
                                    '<div class="rh_lite_precop_bus">'+
                                        '<a href="'+produtos[x].link+'">'+
                                            produtos[x].sale_price+
                                        '</a>'+
                                    '</div>'+
                                    '<div class="rh_lite_parcelamento_bus">'+
                                        '<a href="'+produtos[x].link+'">'+
                                            produtos[x].months+
                                        '</a>'+
                                    '</div>'+
                                    '<div class="rh_lite_btn_bus">'+
                                    '<a href="'+produtos[x].link+'">'+
                                        '<button class="rh_lite_button_bus">'+
                                            '<strong>'+
                                                '<span>COMPRAR</span>'+
                                            '</strong>'+
                                        '</button>'+
                                    '</a>'+
                                    '</div>'+
                                '</div>'+
                            '</div>';
            } else {
                listaProdutos+='<div class="rh_lite_produto_bus">'+
                                    '<figure class="rh_lite_figure_bus">'+
                                        '<a href="'+produtos[x].link+'">'+
                                            '<img class="rh_lite_imagem_bus" src="'+produtos[x].link_image+'" alt="'+produtos[x].nome+'">'+
                                            tag+
                                        '</a>'+
                                    '</figure>'+
                                    '<p class="rh_lite_nomeproduto_bus">'+
                                        '<a href="'+produtos[x].link+'">'+
                                            produtos[x].title+
                                        '</a>'+
                                    '</p>'+
                                    '<div class="rh_lite_precon_bus">'+
                                        '<a href="'+produtos[x].link+'">'+
                                            produtos[x].price+
                                        '</a>'+
                                    '</div>'+
                                    '<div class="rh_lite_precop_bus">'+
                                        '<a href="'+produtos[x].link+'">'+
                                            produtos[x].sale_price+
                                        '</a>'+
                                    '</div>'+
                                    '<div class="rh_lite_parcelamento_bus">'+
                                        '<a href="'+produtos[x].link+'">'+
                                            produtos[x].months+
                                        '</a>'+
                                    '</div>'+
                                    '<div class="rh_lite_btn_bus">'+
                                    '<a href="'+produtos[x].link+'">'+
                                        '<button class="rh_lite_button_bus">'+
                                            '<strong>'+
                                                '<span>COMPRAR</span>'+
                                            '</strong>'+
                                        '</button>'+
                                    '</a>'+
                                    '</div>'+
                                '</div>'+
                            '</div>';
            }
            
            if (x === 14) { break; }
        }

        slider.innerHTML = "";
        slider.innerHTML+=listaProdutos; // ADICIONANDO OS PRODUTOS NO SLIDER
        // SETANDO TAMANHO DO SLIDER DE ACORDO COM A QUANTIDADE DE PRODUTOS
        slider.style.width = document.getElementById('rh_lite_searchbar_slider_produtos').children.length * 220+'px';

        switch(true){ // ESCONDE OU MOSTRA OS BOTOES DE ACORDO COM O TAMANHO DA TELA
            case(window.screen.width <= 480 && produtos.length < 2):
                document.getElementsByClassName('rh_lite_right-button_bus')[0].style.display = 'none';
                document.getElementsByClassName('rh_lite_left-button_bus')[0].style.display = 'none';
                break;
            case(window.screen.width <= 795 && window.screen.width > 480 && produtos.length < 3):
                document.getElementsByClassName('rh_lite_right-button_bus')[0].style.display = 'none';
                document.getElementsByClassName('rh_lite_left-button_bus')[0].style.display = 'none';
                break;
            case(window.screen.width <= 1023 && window.screen.width > 795 && produtos.length < 4):
                document.getElementsByClassName('rh_lite_right-button_bus')[0].style.display = 'none';
                document.getElementsByClassName('rh_lite_left-button_bus')[0].style.display = 'none';
                break;
            case(window.screen.width <= 1200 && window.screen.width > 1023 && produtos.length < 5):
                document.getElementsByClassName('rh_lite_right-button_bus')[0].style.display = 'none';
                document.getElementsByClassName('rh_lite_left-button_bus')[0].style.display = 'none';
                break;
            case (window.screen.width > 1200 && produtos.length <= 5):
                document.getElementsByClassName('rh_lite_right-button_bus')[0].style.display = 'none';
                document.getElementsByClassName('rh_lite_left-button_bus')[0].style.display = 'none';
                break;
            default:
                document.getElementsByClassName('rh_lite_right-button_bus')[0].style.display = 'block';
                document.getElementsByClassName('rh_lite_left-button_bus')[0].style.display = 'block';
                break;
        }
        document.getElementById('rh_lite_searchbar_slider_produtos').style.left = '0';  // RESETANDO POSIÇÃO DO SLIDER
        document.getElementById('rh_lite_searchbar_slider_produtos').style.right = '0'; // RESETANDO POSIÇÃO DO SLIDER
    }

    // DISTÂNCIA DE LEVENSHTEIN
    rh_lite_lev = function (s1, s2) {
      s1 = s1.toLowerCase();
      s2 = s2.toLowerCase();

      var costs = new Array();
      for (var i = 0; i <= s1.length; i++) {
        var lastValue = i;
        for (var j = 0; j <= s2.length; j++) {
          if (i == 0)
            costs[j] = j;
          else {
            if (j > 0) {
              var newValue = costs[j - 1];
              if (s1.charAt(i - 1) != s2.charAt(j - 1))
                newValue = Math.min(Math.min(newValue, lastValue),
                  costs[j]) + 1;
              costs[j - 1] = lastValue;
              lastValue = newValue;
            }
          }
        }
        if (i > 0)
          costs[s2.length] = lastValue;
      }
      if (s1.length > 10 || s2.length > 10) {
        return costs[s2.length] <= 3;
      } else if (s1.length > 7 || s2.length > 7){
        return costs[s2.length] <= 2;
      } else {
        return costs[s2.length] <= 1;
      }
    }

    // FUNÇÃO QUE VERIFICA SE O USUÁRIO DIGITOU ENTER NA BARRA DE BUSCA
    rh_lite_whichKey = function(e,el){
        if (e.keyCode === 13){
            if (window['searchbar'].config.redirect) {
                var word = document.getElementById('rh_lite_searchbar').value;
                var url = window['searchbar'].config.redirect;
                word = word.trim();

                if (word !== '') {
                    rhSaveSearch(word); // SALVANDO PESQUISA NO BANCO
                    window.location.href = url+word;
                }
            } else {
                el.parentElement.children[1].click(); // SE DIGITAR ENTER, CLICA NO BOTAO PESQUISAR
            }
        }
        else if (window['searchbar'].config.paginate === 'true') {
            rhHideOverlay();
            cfg.searchbarResults.classList.add('active');
            cfg.searchbarResults.classList.add('loading');
            resultados_busca = undefined;
        }
    }

    //SLIDER-----------------------------------------------//
    rh_lite_searchbar_slider_arrastaEsquerda = function(){
       var slider = document.getElementById('rh_lite_searchbar_slider_produtos');
      if (window.screen.width <= 480){
         var tamanhoMaximoSlider = (slider.children.length - 1) * 220; // MOSTRA 1
      }
      else if (window.screen.width <= 795){
         var tamanhoMaximoSlider = (slider.children.length - 2) * 220; // MOSTRA 3
      }
      else if (window.screen.width <= 1023){
         var tamanhoMaximoSlider = (slider.children.length - 3) * 220; // MOSTRA 3
      }
      else if (window.screen.width <= 1366){
         var tamanhoMaximoSlider = (slider.children.length - 4) * 220; // MOSTRA 4
      }
      else {
         var tamanhoMaximoSlider = (slider.children.length - 5) * 220; // MOSTRA 5
      }

      if (parseInt(slider.style.right) < tamanhoMaximoSlider){
        slider.style.right = parseFloat(slider.style.right)+220+'px';
        slider.style.left = parseFloat(slider.style.left)-220+'px';
      }
    }

    rh_lite_searchbar_slider_arrastaDireita = function(){
      var slider = document.getElementById('rh_lite_searchbar_slider_produtos');

      if (parseInt(slider.style.right) > 0){ // ARRASTA ENQUANTO NÃO ESTIVER NO PRIMEIRO ELEMENTO
        slider.style.right = parseFloat(slider.style.right)-220+'px';
        slider.style.left = parseFloat(slider.style.left)+220+'px';
      }
    }

    rhGetHammer = function(){
        if (!document.getElementById('__rhHammerJS__')){
            if (typeof require !== 'undefined' && 
                typeof require.config !== 'undefined' && 
                typeof require.version !== 'undefined') {
                // require.config({
                //   paths: {
                //     hammer: 'https://roihero.com.br/widget/js/hammer.min.js'
                //   }
                 
                // });
                // require(['hammer'], function($){ 
                //     console.log('Roi Hero: Hammer carregado via requireJS')
                // });
                setTimeout(function(){
                    var
                    s = document.createElement('script'),
                    doc = document.getElementsByTagName('script');

                    s.id = '__rhHammerJS__';
                    // colocar endereço do HAMMER aqui
                    s.src = 'https://roihero.com.br/widget/js/hammer.min.js';

                    // insere script hammer JS na página
                    doc[doc.length - 1].parentNode.insertBefore(s,doc[doc.length]);
                },2000);
            } 
            else {
                var
                s = document.createElement('script'),
                doc = document.getElementsByTagName('script');

                s.id = '__rhHammerJS__';
                // colocar endereço do HAMMER aqui
                s.src = 'https://roihero.com.br/widget/js/hammer.min.js';

                // insere script hammer JS na página
                doc[doc.length - 1].parentNode.insertBefore(s,doc[doc.length]);
            }

        }
    }

    rhGetHammer();

    rhInitHammer = function(){
        if (typeof Hammer === 'undefined') {
            setTimeout(function(){
                rhInitHammer();    
            },200);
        } else {
            // Create a manager to manager the element
            var manager = new Hammer( document.getElementById('rh_lite_slider_container') );
        
            // Subscribe to a desired event
            manager.on('swiperight',function(e){
                console.log('swiperight');
                rh_lite_searchbar_slider_arrastaDireita(); // passa posicao do slider na funcao
            });
    
            manager.on('swipeleft',function(e){
                console.log('swipeleft');
                rh_lite_searchbar_slider_arrastaEsquerda(); // passa posicao do slider na funcao
            });
        }
    }
    //-----------------------------------------------------//

    // FUNÇÃO QUE REMOVE TODOS OS ACENTOS
    rhRemoveAcento = function(str) {
      var defaultDiacriticsRemovalMap = [
        {'base':'A', 'varters':/[\u0041\u24B6\uFF21\u00C0\u00C1\u00C2\u1EA6\u1EA4\u1EAA\u1EA8\u00C3\u0100\u0102\u1EB0\u1EAE\u1EB4\u1EB2\u0226\u01E0\u00C4\u01DE\u1EA2\u00C5\u01FA\u01CD\u0200\u0202\u1EA0\u1EAC\u1EB6\u1E00\u0104\u023A\u2C6F]/g},
        {'base':'AA','varters':/[\uA732]/g},
        {'base':'AE','varters':/[\u00C6\u01FC\u01E2]/g},
        {'base':'AO','varters':/[\uA734]/g},
        {'base':'AU','varters':/[\uA736]/g},
        {'base':'AV','varters':/[\uA738\uA73A]/g},
        {'base':'AY','varters':/[\uA73C]/g},
        {'base':'B', 'varters':/[\u0042\u24B7\uFF22\u1E02\u1E04\u1E06\u0243\u0182\u0181]/g},
        {'base':'C', 'varters':/[\u0043\u24B8\uFF23\u0106\u0108\u010A\u010C\u00C7\u1E08\u0187\u023B\uA73E]/g},
        {'base':'D', 'varters':/[\u0044\u24B9\uFF24\u1E0A\u010E\u1E0C\u1E10\u1E12\u1E0E\u0110\u018B\u018A\u0189\uA779]/g},
        {'base':'DZ','varters':/[\u01F1\u01C4]/g},
        {'base':'Dz','varters':/[\u01F2\u01C5]/g},
        {'base':'E', 'varters':/[\u0045\u24BA\uFF25\u00C8\u00C9\u00CA\u1EC0\u1EBE\u1EC4\u1EC2\u1EBC\u0112\u1E14\u1E16\u0114\u0116\u00CB\u1EBA\u011A\u0204\u0206\u1EB8\u1EC6\u0228\u1E1C\u0118\u1E18\u1E1A\u0190\u018E]/g},
        {'base':'F', 'varters':/[\u0046\u24BB\uFF26\u1E1E\u0191\uA77B]/g},
        {'base':'G', 'varters':/[\u0047\u24BC\uFF27\u01F4\u011C\u1E20\u011E\u0120\u01E6\u0122\u01E4\u0193\uA7A0\uA77D\uA77E]/g},
        {'base':'H', 'varters':/[\u0048\u24BD\uFF28\u0124\u1E22\u1E26\u021E\u1E24\u1E28\u1E2A\u0126\u2C67\u2C75\uA78D]/g},
        {'base':'I', 'varters':/[\u0049\u24BE\uFF29\u00CC\u00CD\u00CE\u0128\u012A\u012C\u0130\u00CF\u1E2E\u1EC8\u01CF\u0208\u020A\u1ECA\u012E\u1E2C\u0197]/g},
        {'base':'J', 'varters':/[\u004A\u24BF\uFF2A\u0134\u0248]/g},
        {'base':'K', 'varters':/[\u004B\u24C0\uFF2B\u1E30\u01E8\u1E32\u0136\u1E34\u0198\u2C69\uA740\uA742\uA744\uA7A2]/g},
        {'base':'L', 'varters':/[\u004C\u24C1\uFF2C\u013F\u0139\u013D\u1E36\u1E38\u013B\u1E3C\u1E3A\u0141\u023D\u2C62\u2C60\uA748\uA746\uA780]/g},
        {'base':'LJ','varters':/[\u01C7]/g},
        {'base':'Lj','varters':/[\u01C8]/g},
        {'base':'M', 'varters':/[\u004D\u24C2\uFF2D\u1E3E\u1E40\u1E42\u2C6E\u019C]/g},
        {'base':'N', 'varters':/[\u004E\u24C3\uFF2E\u01F8\u0143\u00D1\u1E44\u0147\u1E46\u0145\u1E4A\u1E48\u0220\u019D\uA790\uA7A4]/g},
        {'base':'NJ','varters':/[\u01CA]/g},
        {'base':'Nj','varters':/[\u01CB]/g},
        {'base':'O', 'varters':/[\u004F\u24C4\uFF2F\u00D2\u00D3\u00D4\u1ED2\u1ED0\u1ED6\u1ED4\u00D5\u1E4C\u022C\u1E4E\u014C\u1E50\u1E52\u014E\u022E\u0230\u00D6\u022A\u1ECE\u0150\u01D1\u020C\u020E\u01A0\u1EDC\u1EDA\u1EE0\u1EDE\u1EE2\u1ECC\u1ED8\u01EA\u01EC\u00D8\u01FE\u0186\u019F\uA74A\uA74C]/g},
        {'base':'OI','varters':/[\u01A2]/g},
        {'base':'OO','varters':/[\uA74E]/g},
        {'base':'OU','varters':/[\u0222]/g},
        {'base':'P', 'varters':/[\u0050\u24C5\uFF30\u1E54\u1E56\u01A4\u2C63\uA750\uA752\uA754]/g},
        {'base':'Q', 'varters':/[\u0051\u24C6\uFF31\uA756\uA758\u024A]/g},
        {'base':'R', 'varters':/[\u0052\u24C7\uFF32\u0154\u1E58\u0158\u0210\u0212\u1E5A\u1E5C\u0156\u1E5E\u024C\u2C64\uA75A\uA7A6\uA782]/g},
        {'base':'S', 'varters':/[\u0053\u24C8\uFF33\u1E9E\u015A\u1E64\u015C\u1E60\u0160\u1E66\u1E62\u1E68\u0218\u015E\u2C7E\uA7A8\uA784]/g},
        {'base':'T', 'varters':/[\u0054\u24C9\uFF34\u1E6A\u0164\u1E6C\u021A\u0162\u1E70\u1E6E\u0166\u01AC\u01AE\u023E\uA786]/g},
        {'base':'TZ','varters':/[\uA728]/g},
        {'base':'U', 'varters':/[\u0055\u24CA\uFF35\u00D9\u00DA\u00DB\u0168\u1E78\u016A\u1E7A\u016C\u00DC\u01DB\u01D7\u01D5\u01D9\u1EE6\u016E\u0170\u01D3\u0214\u0216\u01AF\u1EEA\u1EE8\u1EEE\u1EEC\u1EF0\u1EE4\u1E72\u0172\u1E76\u1E74\u0244]/g},
        {'base':'V', 'varters':/[\u0056\u24CB\uFF36\u1E7C\u1E7E\u01B2\uA75E\u0245]/g},
        {'base':'VY','varters':/[\uA760]/g},
        {'base':'W', 'varters':/[\u0057\u24CC\uFF37\u1E80\u1E82\u0174\u1E86\u1E84\u1E88\u2C72]/g},
        {'base':'X', 'varters':/[\u0058\u24CD\uFF38\u1E8A\u1E8C]/g},
        {'base':'Y', 'varters':/[\u0059\u24CE\uFF39\u1EF2\u00DD\u0176\u1EF8\u0232\u1E8E\u0178\u1EF6\u1EF4\u01B3\u024E\u1EFE]/g},
        {'base':'Z', 'varters':/[\u005A\u24CF\uFF3A\u0179\u1E90\u017B\u017D\u1E92\u1E94\u01B5\u0224\u2C7F\u2C6B\uA762]/g},
        {'base':'a', 'varters':/[\u0061\u24D0\uFF41\u1E9A\u00E0\u00E1\u00E2\u1EA7\u1EA5\u1EAB\u1EA9\u00E3\u0101\u0103\u1EB1\u1EAF\u1EB5\u1EB3\u0227\u01E1\u00E4\u01DF\u1EA3\u00E5\u01FB\u01CE\u0201\u0203\u1EA1\u1EAD\u1EB7\u1E01\u0105\u2C65\u0250]/g},
        {'base':'aa','varters':/[\uA733]/g},
        {'base':'ae','varters':/[\u00E6\u01FD\u01E3]/g},
        {'base':'ao','varters':/[\uA735]/g},
        {'base':'au','varters':/[\uA737]/g},
        {'base':'av','varters':/[\uA739\uA73B]/g},
        {'base':'ay','varters':/[\uA73D]/g},
        {'base':'b', 'varters':/[\u0062\u24D1\uFF42\u1E03\u1E05\u1E07\u0180\u0183\u0253]/g},
        {'base':'c', 'varters':/[\u0063\u24D2\uFF43\u0107\u0109\u010B\u010D\u00E7\u1E09\u0188\u023C\uA73F\u2184]/g},
        {'base':'d', 'varters':/[\u0064\u24D3\uFF44\u1E0B\u010F\u1E0D\u1E11\u1E13\u1E0F\u0111\u018C\u0256\u0257\uA77A]/g},
        {'base':'dz','varters':/[\u01F3\u01C6]/g},
        {'base':'e', 'varters':/[\u0065\u24D4\uFF45\u00E8\u00E9\u00EA\u1EC1\u1EBF\u1EC5\u1EC3\u1EBD\u0113\u1E15\u1E17\u0115\u0117\u00EB\u1EBB\u011B\u0205\u0207\u1EB9\u1EC7\u0229\u1E1D\u0119\u1E19\u1E1B\u0247\u025B\u01DD]/g},
        {'base':'f', 'varters':/[\u0066\u24D5\uFF46\u1E1F\u0192\uA77C]/g},
        {'base':'g', 'varters':/[\u0067\u24D6\uFF47\u01F5\u011D\u1E21\u011F\u0121\u01E7\u0123\u01E5\u0260\uA7A1\u1D79\uA77F]/g},
        {'base':'h', 'varters':/[\u0068\u24D7\uFF48\u0125\u1E23\u1E27\u021F\u1E25\u1E29\u1E2B\u1E96\u0127\u2C68\u2C76\u0265]/g},
        {'base':'hv','varters':/[\u0195]/g},
        {'base':'i', 'varters':/[\u0069\u24D8\uFF49\u00EC\u00ED\u00EE\u0129\u012B\u012D\u00EF\u1E2F\u1EC9\u01D0\u0209\u020B\u1ECB\u012F\u1E2D\u0268\u0131]/g},
        {'base':'j', 'varters':/[\u006A\u24D9\uFF4A\u0135\u01F0\u0249]/g},
        {'base':'k', 'varters':/[\u006B\u24DA\uFF4B\u1E31\u01E9\u1E33\u0137\u1E35\u0199\u2C6A\uA741\uA743\uA745\uA7A3]/g},
        {'base':'l', 'varters':/[\u006C\u24DB\uFF4C\u0140\u013A\u013E\u1E37\u1E39\u013C\u1E3D\u1E3B\u017F\u0142\u019A\u026B\u2C61\uA749\uA781\uA747]/g},
        {'base':'lj','varters':/[\u01C9]/g},
        {'base':'m', 'varters':/[\u006D\u24DC\uFF4D\u1E3F\u1E41\u1E43\u0271\u026F]/g},
        {'base':'n', 'varters':/[\u006E\u24DD\uFF4E\u01F9\u0144\u00F1\u1E45\u0148\u1E47\u0146\u1E4B\u1E49\u019E\u0272\u0149\uA791\uA7A5]/g},
        {'base':'nj','varters':/[\u01CC]/g},
        {'base':'o', 'varters':/[\u006F\u24DE\uFF4F\u00F2\u00F3\u00F4\u1ED3\u1ED1\u1ED7\u1ED5\u00F5\u1E4D\u022D\u1E4F\u014D\u1E51\u1E53\u014F\u022F\u0231\u00F6\u022B\u1ECF\u0151\u01D2\u020D\u020F\u01A1\u1EDD\u1EDB\u1EE1\u1EDF\u1EE3\u1ECD\u1ED9\u01EB\u01ED\u00F8\u01FF\u0254\uA74B\uA74D\u0275]/g},
        {'base':'oi','varters':/[\u01A3]/g},
        {'base':'ou','varters':/[\u0223]/g},
        {'base':'oo','varters':/[\uA74F]/g},
        {'base':'p','varters':/[\u0070\u24DF\uFF50\u1E55\u1E57\u01A5\u1D7D\uA751\uA753\uA755]/g},
        {'base':'q','varters':/[\u0071\u24E0\uFF51\u024B\uA757\uA759]/g},
        {'base':'r','varters':/[\u0072\u24E1\uFF52\u0155\u1E59\u0159\u0211\u0213\u1E5B\u1E5D\u0157\u1E5F\u024D\u027D\uA75B\uA7A7\uA783]/g},
        {'base':'s','varters':/[\u0073\u24E2\uFF53\u00DF\u015B\u1E65\u015D\u1E61\u0161\u1E67\u1E63\u1E69\u0219\u015F\u023F\uA7A9\uA785\u1E9B]/g},
        {'base':'t','varters':/[\u0074\u24E3\uFF54\u1E6B\u1E97\u0165\u1E6D\u021B\u0163\u1E71\u1E6F\u0167\u01AD\u0288\u2C66\uA787]/g},
        {'base':'tz','varters':/[\uA729]/g},
        {'base':'u','varters':/[\u0075\u24E4\uFF55\u00F9\u00FA\u00FB\u0169\u1E79\u016B\u1E7B\u016D\u00FC\u01DC\u01D8\u01D6\u01DA\u1EE7\u016F\u0171\u01D4\u0215\u0217\u01B0\u1EEB\u1EE9\u1EEF\u1EED\u1EF1\u1EE5\u1E73\u0173\u1E77\u1E75\u0289]/g},
        {'base':'v','varters':/[\u0076\u24E5\uFF56\u1E7D\u1E7F\u028B\uA75F\u028C]/g},
        {'base':'vy','varters':/[\uA761]/g},
        {'base':'w','varters':/[\u0077\u24E6\uFF57\u1E81\u1E83\u0175\u1E87\u1E85\u1E98\u1E89\u2C73]/g},
        {'base':'x','varters':/[\u0078\u24E7\uFF58\u1E8B\u1E8D]/g},
        {'base':'y','varters':/[\u0079\u24E8\uFF59\u1EF3\u00FD\u0177\u1EF9\u0233\u1E8F\u00FF\u1EF7\u1E99\u1EF5\u01B4\u024F\u1EFF]/g},
        {'base':'z','varters':/[\u007A\u24E9\uFF5A\u017A\u1E91\u017C\u017E\u1E93\u1E95\u01B6\u0225\u0240\u2C6C\uA763]/g}
      ];

      for(var i=0; i<defaultDiacriticsRemovalMap.length; i++) {
        str = str.replace(defaultDiacriticsRemovalMap[i].varters, defaultDiacriticsRemovalMap[i].base);
      }
      return str;
    }

    // FUNÇÃO QUE MOSTRA OS MAIS PESQUISADOS
    rhPesquisaTermo = function(termo) {
        var searchbar = cfg.searchbar; // BARRA DE PESQUISA
        searchbar.value = termo.trim(); // MUDANDO O VALOR DA BARRA DE PESQUIS
        if (window['searchbar'].config.paginate === 'true') {
            setTimeout(function(){
                window['searchbar'].config.searchbar.click();
                document.getElementById('rh_lite_container_results').classList.add('active');
            },400);
        }
        else {
            setTimeout(function(){
                rhSetOverlay(); // SETANDO OVERLAY COM SLIDER E RESULTADOS
                rhShowOverlay();
            },200);
        }
        
    };

    // FUNÇÃO QUE ORGANIZA OS MAIS BUSCADOS E RETORNA OS TERMOS RELACIONADOS À BUSCA
    rhSimilarSearches = function(s,a){
      if (s.length < 1) {
        return false;
      }
      var pesquisa = rhRemoveAcento(s).toLowerCase().trim();
      var palavrasPesquisa = pesquisa.split(' ');
      var termosOrdenados = [];
      var termosPesquisados = [];

      for (var i = 0; i < a.length; i++) {
        var p=a[i];
        p = rhRemoveAcento(p).toLowerCase().trim();
        termosPesquisados.push(p);
        termosOrdenados.push({score:0, nome:p});
      }

      // ITERANDO ENTRE OS TERMOS MAIS PESQUISADOS
      for (var x = 0; x < termosPesquisados.length; x++){
        var score = 0; // SCORE
        var palavrasPesquisados = termosPesquisados[x].split(' ') //
        // ITERANDO ENTRE AS PALAVRAS DOS TERMOS ARMAZENADOS
        for (var z = 0; z < palavrasPesquisados.length; z++){
          // ITERANDO ENTRE AS PALAVRAS DO TERMO QUE FOI DIGITADO NA BARRA DE BUSCA
          var palavraPesquisados = palavrasPesquisados[z].trim();
          for(var i = 0; i < palavrasPesquisa.length ;i++){
            var palavraPesquisa = palavrasPesquisa[i].trim();;
            if (palavraPesquisados === '' || palavraPesquisa === '' || palavraPesquisados.length <= 2){ break; } // CASO A PALAVRA SEJA UMA STRING VAZIA, SAIR
            if(palavraPesquisados.indexOf(palavraPesquisa) === 0) {
              score+=10;
            }
            else if (rh_lite_lev(palavraPesquisados,palavraPesquisa)){
              score+=9;
            }
            else if (rh_lite_compare(palavraPesquisados,palavraPesquisa)){
              score+=7;
            }
            else if (palavraPesquisados.indexOf(palavraPesquisa) >= 0){
              score+=5;
            }
          }
        }
        if (termosPesquisados[x] === pesquisa){ // caso seja identico, tira 100 do score
          score = score - 100;
        }

        // ADICIONANDO SCORE
        termosOrdenados[x].score = score;
      }

      termosOrdenados = termosOrdenados.sort(function(a , b){
        if (parseFloat(a.score) <= parseFloat(b.score)){
          return 1;
        } else {
          return -1;
        }
      });


      termosOrdenados = termosOrdenados.slice(0,5);

      termosOrdenados = termosOrdenados.filter(function(t){
        return t.score >= 10;
      });

      a = [];
      for (var i = 0; i < termosOrdenados.length; i++) {
          a.push(termosOrdenados[i].nome);
      }

      return a;
    }

    // FUNÇÃO QUE FECHA O OVERLAY
    rhHideOverlay = function(){
        cfg.overlay.classList.remove('active'); // DEIXANDO OVERLAY TRANSPARENTE
        document.getElementById('rh_lite_searchbar_slider_produtos').style.left = '0';  // RESETANDO POSIÇÃO DO SLIDER
        document.getElementById('rh_lite_searchbar_slider_produtos').style.right = '0'; // RESETANDO POSIÇÃO DO SLIDER
    }

    // ADICIONANDO LISTENNER NA JANELA PRA QUANDO CLICAR FORA DO OVERLAY OU NO BOTAO DE FECHAR O OVERLAY
    window.addEventListener('click', function(e){
        if ((e.target).toString() === '') {
            return false;
        }
        if (cfg.overlay.classList.contains('active')){
              if (cfg.overlay.children[0].contains(e.target)  // JANELA OVERLAY
              || cfg.searchbutton.contains(e.target) // BOTAO PESQUISAR BARRA DE BUSCA PRINCIPAL
              ){
                if (document.getElementById('rh_lite_searchbar_btn_fecha_overlay').contains(e.target)){ // BOTAO FECHAOVERLAY
                    rhHideOverlay();
                }
                return false;
              } else{
                    rhHideOverlay();
              }
        }
        return false;
    });

    // ADICIONANDO LISTENNER NOS BOTOES DE PESQUISAR PARA SALVAR PESQUISA
    botoesPesquisar = document.getElementsByClassName('rh_lite_searchbutton');
    for (var i = 0; i < botoesPesquisar.length; i++) {
        botoesPesquisar[i].addEventListener('click',function(){
            var textoBarraDeBusca = this.parentElement.children[0].value;
            // AUTOMATICAMENTE IGNORA AS PALAVRAS "QUERO"/"QUERO COMPRAR"/"COMPRAR"
            textoBarraDeBusca = textoBarraDeBusca.replace(/\quero comprar/g, '').replace('quero comprar','');
            textoBarraDeBusca = textoBarraDeBusca.replace(/\comprar/g , '').replace('comprar','');
            textoBarraDeBusca = textoBarraDeBusca.replace(/\quero/g , '').replace('quero','');
            textoBarraDeBusca = textoBarraDeBusca.trim(); // REMOVE ESPAÇOS NO COMEÇO E NO FINAL DA PALAVRA

            if (textoBarraDeBusca !== '' && textoBarraDeBusca !== undefined && textoBarraDeBusca.length > 2
                && (cfg.searchbar.value[0] !== '$' && cfg.searchbar.value[0] !== '$')) {
                rhSaveSearch(textoBarraDeBusca); // SALVANDO PESQUISA NO BANCO
            }
            return false;
        });
    };

    // FUNÇÃO Q COMPARA AS STRINGS
        rh_lite_compare = function(c, u) {
            if (u.toLowerCase().indexOf(c.toLowerCase()) === 0) { return true;}
            var incept = false;
            var ca = c.split(",");
            u = clean(u);
            //ca = correct answer array (Collection of all correct answer)
            //caa = a single correct answer word array (collection of words of a single correct answer)
            //u = array of user answer words cleaned using custom clean function
            for (var z = 0; z < ca.length; z++) {
                caa = (ca[z]).replace(/^\s\s*/, '').replace(/\s\s*$/, '').split(" ");
                var pc = 0;
                for (var x = 0; x < caa.length; x++) {
                    for (var y = 0; y < u.length; y++) {
                        if (rh_lite_soundex(u[y]) != null && rh_lite_soundex(caa[x]) != null) {
                            if (rh_lite_soundex(u[y]) == rh_lite_soundex(caa[x])) {
                                pc = pc + 1;
                            }
                        }
                        else {
                            if (u[y].indexOf(caa[x]) > -1) {
                                pc = pc + 1;
                            }
                        }
                    }
                }
                if ((pc / caa.length) > 0.5) {
                    return true;
                }
            }
            return false;
        }

        // create object listing the SOUNDEX values for each varter
        // -1 indicates that the varter is not coded, but is used for coding
        //  0 indicates that the varter is omitted for modern census archives
        //  but acts like -1 for older census archives
        //  1 is for BFPV
        //  2 is for CGJKQSXZ
        //  3 is for DT
        //  4 is for L
        //  5 is for MN my home state
        //  6 is for R
        rh_lite_makesoundex = function() {
            this.a = -1
            this.b = 1
            this.c = 2
            this.d = 3
            this.e = -1
            this.f = 1
            this.g = 2
            this.h = 0
            this.i = -1
            this.j = 2
            this.k = 2
            this.l = 4
            this.m = 5
            this.n = 5
            this.o = -1
            this.p = 1
            this.q = 2
            this.r = 6
            this.s = 2
            this.t = 3
            this.u = 0
            this.v = 1
            this.w = 0
            this.x = 2
            this.y = -1
            this.z = 2
        }

        var sndx = new rh_lite_makesoundex()

        // check to see that the input is valid
        rh_lite_isSurname = function(name) {
            if (name == "" || name == null) {
                return false
            } else {
                for (var i = 0; i < name.length; i++) {
                    var varter = name.charAt(i)
                    if (!(varter >= 'a' && varter <= 'z' || varter >= 'A' && varter <= 'Z')) {
                        return false
                    }
                }
            }
            return true
        }

        // Collapse out directly adjacent sounds
        // 1. Assume that surname.length>=1
        // 2. Assume that surname contains only lowercase varters
        collapse = function(surname) {
            if (surname.length == 1) {
                return surname
            }
            var right = collapse(surname.substring(1, surname.length))
            if (sndx[surname.charAt(0)] == sndx[right.charAt(0)]) {
                return surname.charAt(0) + right.substring(1, right.length)
            }
            return surname.charAt(0) + right
        }

        // Collapse out directly adjacent sounds using the new National Archives method
        // 1. Assume that surname.length>=1
        // 2. Assume that surname contains only lowercase varters
        // 3. H and W are compvarely ignored
        omit = function(surname) {
            if (surname.length == 1) {
                return surname
            }
            var right = omit(surname.substring(1, surname.length))
            if (!sndx[right.charAt(0)]) {
                return surname.charAt(0) + right.substring(1, right.length)
            }
            return surname.charAt(0) + right
        }

        // Output the coded sequence
        output_sequence =function (seq) {
            var output = seq.charAt(0).toUpperCase() // Retain first varter
            output += "-" // Separate varter with a dash
            var stage2 = seq.substring(1, seq.length)
            var count = 0
            for (var i = 0; i < stage2.length && count < 3; i++) {
                if (sndx[stage2.charAt(i)] > 0) {
                    output += sndx[stage2.charAt(i)]
                    count++
                }
            }
            for (; count < 3; count++) {
                output += "0"
            }
            return output
        }

        // Compute the SOUNDEX code for the surname
        rh_lite_soundex = function (value) {
            if (!rh_lite_isSurname(value)) {
                return null
            }
            var stage1 = collapse(value.toLowerCase())
            //form.result.value=output_sequence(stage1);

            var stage1 = omit(value.toLowerCase())
            var stage2 = collapse(stage1)
            return output_sequence(stage2);

        }

        clean = function(u) {
            var u = u.replace(/\,/g, "");
            u = u.toLowerCase().split(" ");
            var cw = ["ARRAY OF WORDS TO BE EXCLUDED FROM COMPARISON"];
            var n = [];
            for (var y = 0; y < u.length; y++) {
                var test = false;
                for (var z = 0; z < cw.length; z++) {
                    if (u[y] != "" && u[y] != cw[z]) {
                        test = true;
                        break;
                    }
                }
                if (test) {
        //Don't use & or $ in comparison
                    var val = u[y].replace("$", "").replace("&", "");
                    n.push(val);
                }
            }
            return n;
        }

    rhBindEvents = function(cfg){
        var
        barraOverlay = document.getElementById('rh_lite_overlay_searchbar'),
        btnPesquisarOverlay = document.getElementById('rh_lite_searchbutton_overlay');



        var btnVerTodosOsResultados = document.querySelector('#rh_lite_searchbar_goto_results')

        if (btnVerTodosOsResultados) {
            btnVerTodosOsResultados.addEventListener('click', function () {
                var input = document.querySelector('#rh_lite_searchbar')
                var termo = input.value.trim()
                var inputCfg = document.querySelector('#rh-searchbar-config')
                var resultsPage = inputCfg.dataset.resultsPage

                if (termo !== '') {
                    window.location.href = resultsPage + termo
                }
            })
        }


        // barra principal
            // binda listenners barra de busca
            cfg.searchbar.addEventListener('keyup', function(){
                var btnVerTodosOsResultados = document.querySelector('#rh_lite_searchbar_goto_results')

                if (btnVerTodosOsResultados) {
                    btnVerTodosOsResultados.classList.remove('rh-active')
                }

                rhIntervaloBusca();
            });

            cfg.searchbar.addEventListener('blur', function(){

                var btnVerTodosOsResultados = document.querySelector('#rh_lite_searchbar_goto_results')

                if (btnVerTodosOsResultados) {
                    btnVerTodosOsResultados.classList.remove('rh-active')
                }

                rhHideResults();
            });

            cfg.searchbar.addEventListener('click',function(){

                var btnVerTodosOsResultados = document.querySelector('#rh_lite_searchbar_goto_results')

                if (btnVerTodosOsResultados) {
                    btnVerTodosOsResultados.classList.remove('rh-active')
                }


                if (this.value.trim() === '') {
                    if (window.hasOwnProperty('rh_lite_obj') && window.hasOwnProperty('rh_lite_termos_personalizados')) {
                        rhTyping(this);
                    }
                    else if (window['searchbar'].config.paginate === 'true') {
                        rhTyping(this);
                    }
                    return false;
                } else {
                    if (cfg.searchbarResults.classList.contains('active')){
                        return false
                    }
                    cfg.searchbarResults.classList.add('active');
                    rhIntervaloBusca();
                }
            });

            cfg.searchbar.addEventListener('keypress',function(e){

                var btnVerTodosOsResultados = document.querySelector('#rh_lite_searchbar_goto_results')

                if (btnVerTodosOsResultados) {
                    btnVerTodosOsResultados.classList.remove('rh-active')
                }


                rh_lite_whichKey(e,this);
            });

            // se estiver configurado para redirecionar, binda para redirecionar
            if (window['searchbar'].config.redirect) {
                cfg.searchbutton.addEventListener('click',function(){
                    var word = document.getElementById('rh_lite_searchbar').value;
                    var url = window['searchbar'].config.redirect;
                    word = word.trim();
                    if (word !== '') {
                        rhSaveSearch(word); // SALVANDO PESQUISA NO BANCO
                        window.location.href = url+word;
                    }
                });
                    
            } else {
                // binda listenners botão pesquisar
                cfg.searchbutton.addEventListener('click',function(){                
                    setTimeout(function(){
                        rhShowOverlay();
                    },300)
                });
            }

            
        // ---

        // barra overlay
            barraOverlay.addEventListener('keyup', function(){
                rhIntervaloBusca();
            });
            barraOverlay.addEventListener('blur', function(){
                var btnVerTodosOsResultados = document.querySelector('#rh_lite_searchbar_goto_results')

                if (btnVerTodosOsResultados) {
                    btnVerTodosOsResultados.classList.remove('rh-active')
                }

                rhHideResultsOverlay();
            });

            barraOverlay.addEventListener('click', function(){
                rhTypingOverlay(this);
            });
            barraOverlay.addEventListener('keypress', function(e){
                rh_lite_whichKey(e,this);
            });

            btnPesquisarOverlay.addEventListener('click',function(){
                rhSetOverlay();
            });
        // ---
    }
    rhBindEvents(cfg);

    console.log('barra de busca Roi Hero carregada com sucesso.');
}

// CHAMA
rhSearchBarSendReq(rhClientIdSb,sbId);

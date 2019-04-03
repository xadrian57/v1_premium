/**
 * Arquivo de propriedade da RoiHero.
 */

'use strict';

window['rhDynamicSearch'] = {
    config: {
        productHTML: // estrutura produto
        '<div class="rh-dynamic-product" data-rh-sku="{sku}" data-rh-id="{id}" data-rh-pos="{pos}">'+
            '<a href="{link}">'+
                '<div class="rh-discount rh-discount-{discount}"><span>{discount}%<span><p>off</p></div>'+
                '<div class="rh-img-container"><img src="{image}"></div>'+
                '<div class="rh-product-info">'+
                    '<span class="rh-product-name">{name}</span>'+
                    '{check_oldprice} <span class="rh-old-price">{price}</span> {check_oldprice}'+ // <oldprice> para preço normal
                    '<span class="rh-sale-price">{sale_price}</span>'+
                '</div>'+
                '<span class="rh-product-button">COMPRAR</span>'+
            '</a>'+
        '</div>',
    
        resultLimit: 300, // qtd de produtos que vem no resultado
        paginate: true, // paginar os produtos?
        paginationQtd: 24, //  -> 24/48/72/96 qtd inicial de produtos por página inicialmente
        getResultFromServer: true // faz a pesquisa do lado do servidor    
    }
};

// temporário, isso vai ficar na chamada
(function(win,doc,rhDynamicSearch) {
    // biblioteca de utilitários
    rhDynamicSearch.util = rhDynamicSearch.util || {}
    rhDynamicSearch.shelf = rhDynamicSearch.shelf || {};
    rhDynamicSearch.page = 1; // página atual dos resultados
    rhDynamicSearch.paginationQtd = rhDynamicSearch.config.paginationQtd; // qtd de produtos exibidos por pagina
    rhDynamicSearch.vtex = {}; // api vtex

    // filtra o JSON de produtos de acordo com o algorítmo de busca
    rhDynamicSearch.util.sort = function(products,filter) {
        var
        result = [],
        searchTerm = rhDynamicSearch.util.removeSymbols( rhDynamicSearch.util.getSearchTerm() ),
        score = 0;

        switch (filter) {
            case 'discount':
                result = rhDynamicSearch.products.sort(function(a , b){
                   if (parseFloat(a.desconto) <= parseFloat(b.desconto)){
                      return 1;
                   } else {
                      return -1;
                   }
                });
                break;
            case 'higherPrice':
                result = rhDynamicSearch.products.sort(function(a , b){
                   var
                   priceA = rhDynamicSearch.util.realToFloat(a.sale_price),
                   priceB = rhDynamicSearch.util.realToFloat(b.sale_price);

                   if (priceA <= priceB){
                      return 1;
                   } else {
                      return -1;
                   }
                });
                break;
            case 'lowerPrice':
                result = rhDynamicSearch.products.sort(function(a , b){
                   var
                   priceA = rhDynamicSearch.util.realToFloat(a.sale_price),
                   priceB = rhDynamicSearch.util.realToFloat(b.sale_price);

                   if (priceA >= priceB){
                      return 1;
                   } else {
                      return -1;
                   }
                });
                break;
            default:
                for (var y = 0; y < products.length; y++) {
                    var
                    prod = products[y],
                    prodName = rhDynamicSearch.util.removeSymbols(prod.title.toLowerCase().trim()),
                    wordsInProd = prodName.split(' '),
                    wordsInSearch = searchTerm.split(' '),
                    totalWordsInProduct = 0; // total de palavras da busca que esta dentro da outra

                    if (searchTerm == prodName) {
                        score+=100;
                    }

                    // levenshtein palavra inteira
                    if (rhDynamicSearch.util.levDistance(prodName,searchTerm)) {
                        score+=60;
                    }

                    if (prodName.indexOf(searchTerm) == 0){
                        score+=50;
                    }

                    if (prodName.indexOf(searchTerm) >= 0 && searchTerm.indexOf(searchTerm) >= 0){
                        score+=40;
                    }

                    if(wordsInSearch.length > 1){
                        if(wordsInSearch[0] === wordsInProd[0]){
                            score+=30;
                        }

                        // levenshtein primeira palavra
                        if (rhDynamicSearch.util.levDistance(wordsInProd[0],wordsInSearch[0])) {
                            score+=30;
                        }

                        for (var i = 0; i < wordsInSearch.length; i++) {
                            var
                            totalWordsInProduct = 0,
                            word = wordsInSearch[i].trim();

                            if (word !== '' && prodName.indexOf(word) >= 0){
                                score+=20;
                            }
                        }

                    }

                    if (searchTerm.indexOf(prodName) >= 0){
                        score+=20;
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
                    console.log('Nenhum produto encontrado para'+searchTerm+'!');

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
                break;
        }

        result = result.slice(0,rhDynamicSearch.config.resultLimit);
        return result;
    };

    // pega o termo da url
    rhDynamicSearch.util.getSearchTerm = function() {
        if (doc.URL.indexOf('rhSearch') < 0) {
            return '';
        }

        var term = doc.URL.split('/'); // pega final

        term = term[term.length - 1].split('?'); // pega os parâmetros
        term = term[term.length - 1];
        term = term.split('&'); // quebra parâmetros
        for (var i = 0; i < term.length; i++) {
            if(term[i].indexOf('rhSearch') === 0) {
                term = term[i].replace('rhSearch=','');
                term = term.replace(/\+/g,' ');
                break;
            }
        }

        term = decodeURIComponent(term);

        rhDynamicSearch.term = term;

        return term;
    };

    // distancia de levenshtein
    rhDynamicSearch.util.levDistance = function (s1, s2) {
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
    };

    // chamadas ajax simplificadas
    rhDynamicSearch.util.ajax = function(obj){
        var
        req = new XMLHttpRequest(),
        formData = new FormData();

        for (var key in obj.data) {
            if (obj.data.hasOwnProperty(key)) {
                formData.append(key,obj.data[key]);
            }
        }

        req.open(obj.type,obj.url,true);
        req.onreadystatechange = function(){
            if (req.readyState == 4 && req.status == 200){
                obj.callback(req);
            }
        }
        req.send(formData);
    };

    // troca todas as ocorrencias na string
    rhDynamicSearch.util.replaceAll =  function(string,replacement,toBeReplaced){
        var reg = new RegExp(replacement,'g');
        return string.replace(reg,toBeReplaced);
    };

    // converte em real
    rhDynamicSearch.util.toReais = function(value) {
        var
        	currency  = window['currencyCli'] ? window['currencyCli'] : 'R$',
            pattern_1 = /\.?(\d{1,2})$/g,
            pattern_2 = /(\d)(?=(\d{3})+(?!\d))/g;

        if (value) {
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
        } else {

            value = currency === 'R$' ? '0,00' : '0.00';
        }

        return currency + ' ' + value;
    };

    // converte string de preço pra float
    rhDynamicSearch.util.realToFloat = function(s){
        return parseFloat(s.replace('R$','').replace(',','.').trim());
    };

    // remover símbolos e acentos
    rhDynamicSearch.util.removeSymbols = function(str) {
      var defaultDiacriticsRemovalMap = [
        {'base':'A', 'letters':/[\u0041\u24B6\uFF21\u00C0\u00C1\u00C2\u1EA6\u1EA4\u1EAA\u1EA8\u00C3\u0100\u0102\u1EB0\u1EAE\u1EB4\u1EB2\u0226\u01E0\u00C4\u01DE\u1EA2\u00C5\u01FA\u01CD\u0200\u0202\u1EA0\u1EAC\u1EB6\u1E00\u0104\u023A\u2C6F]/g},
        {'base':'AA','letters':/[\uA732]/g},
        {'base':'AE','letters':/[\u00C6\u01FC\u01E2]/g},
        {'base':'AO','letters':/[\uA734]/g},
        {'base':'AU','letters':/[\uA736]/g},
        {'base':'AV','letters':/[\uA738\uA73A]/g},
        {'base':'AY','letters':/[\uA73C]/g},
        {'base':'B', 'letters':/[\u0042\u24B7\uFF22\u1E02\u1E04\u1E06\u0243\u0182\u0181]/g},
        {'base':'C', 'letters':/[\u0043\u24B8\uFF23\u0106\u0108\u010A\u010C\u00C7\u1E08\u0187\u023B\uA73E]/g},
        {'base':'D', 'letters':/[\u0044\u24B9\uFF24\u1E0A\u010E\u1E0C\u1E10\u1E12\u1E0E\u0110\u018B\u018A\u0189\uA779]/g},
        {'base':'DZ','letters':/[\u01F1\u01C4]/g},
        {'base':'Dz','letters':/[\u01F2\u01C5]/g},
        {'base':'E', 'letters':/[\u0045\u24BA\uFF25\u00C8\u00C9\u00CA\u1EC0\u1EBE\u1EC4\u1EC2\u1EBC\u0112\u1E14\u1E16\u0114\u0116\u00CB\u1EBA\u011A\u0204\u0206\u1EB8\u1EC6\u0228\u1E1C\u0118\u1E18\u1E1A\u0190\u018E]/g},
        {'base':'F', 'letters':/[\u0046\u24BB\uFF26\u1E1E\u0191\uA77B]/g},
        {'base':'G', 'letters':/[\u0047\u24BC\uFF27\u01F4\u011C\u1E20\u011E\u0120\u01E6\u0122\u01E4\u0193\uA7A0\uA77D\uA77E]/g},
        {'base':'H', 'letters':/[\u0048\u24BD\uFF28\u0124\u1E22\u1E26\u021E\u1E24\u1E28\u1E2A\u0126\u2C67\u2C75\uA78D]/g},
        {'base':'I', 'letters':/[\u0049\u24BE\uFF29\u00CC\u00CD\u00CE\u0128\u012A\u012C\u0130\u00CF\u1E2E\u1EC8\u01CF\u0208\u020A\u1ECA\u012E\u1E2C\u0197]/g},
        {'base':'J', 'letters':/[\u004A\u24BF\uFF2A\u0134\u0248]/g},
        {'base':'K', 'letters':/[\u004B\u24C0\uFF2B\u1E30\u01E8\u1E32\u0136\u1E34\u0198\u2C69\uA740\uA742\uA744\uA7A2]/g},
        {'base':'L', 'letters':/[\u004C\u24C1\uFF2C\u013F\u0139\u013D\u1E36\u1E38\u013B\u1E3C\u1E3A\u0141\u023D\u2C62\u2C60\uA748\uA746\uA780]/g},
        {'base':'LJ','letters':/[\u01C7]/g},
        {'base':'Lj','letters':/[\u01C8]/g},
        {'base':'M', 'letters':/[\u004D\u24C2\uFF2D\u1E3E\u1E40\u1E42\u2C6E\u019C]/g},
        {'base':'N', 'letters':/[\u004E\u24C3\uFF2E\u01F8\u0143\u00D1\u1E44\u0147\u1E46\u0145\u1E4A\u1E48\u0220\u019D\uA790\uA7A4]/g},
        {'base':'NJ','letters':/[\u01CA]/g},
        {'base':'Nj','letters':/[\u01CB]/g},
        {'base':'O', 'letters':/[\u004F\u24C4\uFF2F\u00D2\u00D3\u00D4\u1ED2\u1ED0\u1ED6\u1ED4\u00D5\u1E4C\u022C\u1E4E\u014C\u1E50\u1E52\u014E\u022E\u0230\u00D6\u022A\u1ECE\u0150\u01D1\u020C\u020E\u01A0\u1EDC\u1EDA\u1EE0\u1EDE\u1EE2\u1ECC\u1ED8\u01EA\u01EC\u00D8\u01FE\u0186\u019F\uA74A\uA74C]/g},
        {'base':'OI','letters':/[\u01A2]/g},
        {'base':'OO','letters':/[\uA74E]/g},
        {'base':'OU','letters':/[\u0222]/g},
        {'base':'P', 'letters':/[\u0050\u24C5\uFF30\u1E54\u1E56\u01A4\u2C63\uA750\uA752\uA754]/g},
        {'base':'Q', 'letters':/[\u0051\u24C6\uFF31\uA756\uA758\u024A]/g},
        {'base':'R', 'letters':/[\u0052\u24C7\uFF32\u0154\u1E58\u0158\u0210\u0212\u1E5A\u1E5C\u0156\u1E5E\u024C\u2C64\uA75A\uA7A6\uA782]/g},
        {'base':'S', 'letters':/[\u0053\u24C8\uFF33\u1E9E\u015A\u1E64\u015C\u1E60\u0160\u1E66\u1E62\u1E68\u0218\u015E\u2C7E\uA7A8\uA784]/g},
        {'base':'T', 'letters':/[\u0054\u24C9\uFF34\u1E6A\u0164\u1E6C\u021A\u0162\u1E70\u1E6E\u0166\u01AC\u01AE\u023E\uA786]/g},
        {'base':'TZ','letters':/[\uA728]/g},
        {'base':'U', 'letters':/[\u0055\u24CA\uFF35\u00D9\u00DA\u00DB\u0168\u1E78\u016A\u1E7A\u016C\u00DC\u01DB\u01D7\u01D5\u01D9\u1EE6\u016E\u0170\u01D3\u0214\u0216\u01AF\u1EEA\u1EE8\u1EEE\u1EEC\u1EF0\u1EE4\u1E72\u0172\u1E76\u1E74\u0244]/g},
        {'base':'V', 'letters':/[\u0056\u24CB\uFF36\u1E7C\u1E7E\u01B2\uA75E\u0245]/g},
        {'base':'VY','letters':/[\uA760]/g},
        {'base':'W', 'letters':/[\u0057\u24CC\uFF37\u1E80\u1E82\u0174\u1E86\u1E84\u1E88\u2C72]/g},
        {'base':'X', 'letters':/[\u0058\u24CD\uFF38\u1E8A\u1E8C]/g},
        {'base':'Y', 'letters':/[\u0059\u24CE\uFF39\u1EF2\u00DD\u0176\u1EF8\u0232\u1E8E\u0178\u1EF6\u1EF4\u01B3\u024E\u1EFE]/g},
        {'base':'Z', 'letters':/[\u005A\u24CF\uFF3A\u0179\u1E90\u017B\u017D\u1E92\u1E94\u01B5\u0224\u2C7F\u2C6B\uA762]/g},
        {'base':'a', 'letters':/[\u0061\u24D0\uFF41\u1E9A\u00E0\u00E1\u00E2\u1EA7\u1EA5\u1EAB\u1EA9\u00E3\u0101\u0103\u1EB1\u1EAF\u1EB5\u1EB3\u0227\u01E1\u00E4\u01DF\u1EA3\u00E5\u01FB\u01CE\u0201\u0203\u1EA1\u1EAD\u1EB7\u1E01\u0105\u2C65\u0250]/g},
        {'base':'aa','letters':/[\uA733]/g},
        {'base':'ae','letters':/[\u00E6\u01FD\u01E3]/g},
        {'base':'ao','letters':/[\uA735]/g},
        {'base':'au','letters':/[\uA737]/g},
        {'base':'av','letters':/[\uA739\uA73B]/g},
        {'base':'ay','letters':/[\uA73D]/g},
        {'base':'b', 'letters':/[\u0062\u24D1\uFF42\u1E03\u1E05\u1E07\u0180\u0183\u0253]/g},
        {'base':'c', 'letters':/[\u0063\u24D2\uFF43\u0107\u0109\u010B\u010D\u00E7\u1E09\u0188\u023C\uA73F\u2184]/g},
        {'base':'d', 'letters':/[\u0064\u24D3\uFF44\u1E0B\u010F\u1E0D\u1E11\u1E13\u1E0F\u0111\u018C\u0256\u0257\uA77A]/g},
        {'base':'dz','letters':/[\u01F3\u01C6]/g},
        {'base':'e', 'letters':/[\u0065\u24D4\uFF45\u00E8\u00E9\u00EA\u1EC1\u1EBF\u1EC5\u1EC3\u1EBD\u0113\u1E15\u1E17\u0115\u0117\u00EB\u1EBB\u011B\u0205\u0207\u1EB9\u1EC7\u0229\u1E1D\u0119\u1E19\u1E1B\u0247\u025B\u01DD]/g},
        {'base':'f', 'letters':/[\u0066\u24D5\uFF46\u1E1F\u0192\uA77C]/g},
        {'base':'g', 'letters':/[\u0067\u24D6\uFF47\u01F5\u011D\u1E21\u011F\u0121\u01E7\u0123\u01E5\u0260\uA7A1\u1D79\uA77F]/g},
        {'base':'h', 'letters':/[\u0068\u24D7\uFF48\u0125\u1E23\u1E27\u021F\u1E25\u1E29\u1E2B\u1E96\u0127\u2C68\u2C76\u0265]/g},
        {'base':'hv','letters':/[\u0195]/g},
        {'base':'i', 'letters':/[\u0069\u24D8\uFF49\u00EC\u00ED\u00EE\u0129\u012B\u012D\u00EF\u1E2F\u1EC9\u01D0\u0209\u020B\u1ECB\u012F\u1E2D\u0268\u0131]/g},
        {'base':'j', 'letters':/[\u006A\u24D9\uFF4A\u0135\u01F0\u0249]/g},
        {'base':'k', 'letters':/[\u006B\u24DA\uFF4B\u1E31\u01E9\u1E33\u0137\u1E35\u0199\u2C6A\uA741\uA743\uA745\uA7A3]/g},
        {'base':'l', 'letters':/[\u006C\u24DB\uFF4C\u0140\u013A\u013E\u1E37\u1E39\u013C\u1E3D\u1E3B\u017F\u0142\u019A\u026B\u2C61\uA749\uA781\uA747]/g},
        {'base':'lj','letters':/[\u01C9]/g},
        {'base':'m', 'letters':/[\u006D\u24DC\uFF4D\u1E3F\u1E41\u1E43\u0271\u026F]/g},
        {'base':'n', 'letters':/[\u006E\u24DD\uFF4E\u01F9\u0144\u00F1\u1E45\u0148\u1E47\u0146\u1E4B\u1E49\u019E\u0272\u0149\uA791\uA7A5]/g},
        {'base':'nj','letters':/[\u01CC]/g},
        {'base':'o', 'letters':/[\u006F\u24DE\uFF4F\u00F2\u00F3\u00F4\u1ED3\u1ED1\u1ED7\u1ED5\u00F5\u1E4D\u022D\u1E4F\u014D\u1E51\u1E53\u014F\u022F\u0231\u00F6\u022B\u1ECF\u0151\u01D2\u020D\u020F\u01A1\u1EDD\u1EDB\u1EE1\u1EDF\u1EE3\u1ECD\u1ED9\u01EB\u01ED\u00F8\u01FF\u0254\uA74B\uA74D\u0275]/g},
        {'base':'oi','letters':/[\u01A3]/g},
        {'base':'ou','letters':/[\u0223]/g},
        {'base':'oo','letters':/[\uA74F]/g},
        {'base':'p','letters':/[\u0070\u24DF\uFF50\u1E55\u1E57\u01A5\u1D7D\uA751\uA753\uA755]/g},
        {'base':'q','letters':/[\u0071\u24E0\uFF51\u024B\uA757\uA759]/g},
        {'base':'r','letters':/[\u0072\u24E1\uFF52\u0155\u1E59\u0159\u0211\u0213\u1E5B\u1E5D\u0157\u1E5F\u024D\u027D\uA75B\uA7A7\uA783]/g},
        {'base':'s','letters':/[\u0073\u24E2\uFF53\u00DF\u015B\u1E65\u015D\u1E61\u0161\u1E67\u1E63\u1E69\u0219\u015F\u023F\uA7A9\uA785\u1E9B]/g},
        {'base':'t','letters':/[\u0074\u24E3\uFF54\u1E6B\u1E97\u0165\u1E6D\u021B\u0163\u1E71\u1E6F\u0167\u01AD\u0288\u2C66\uA787]/g},
        {'base':'tz','letters':/[\uA729]/g},
        {'base':'u','letters':/[\u0075\u24E4\uFF55\u00F9\u00FA\u00FB\u0169\u1E79\u016B\u1E7B\u016D\u00FC\u01DC\u01D8\u01D6\u01DA\u1EE7\u016F\u0171\u01D4\u0215\u0217\u01B0\u1EEB\u1EE9\u1EEF\u1EED\u1EF1\u1EE5\u1E73\u0173\u1E77\u1E75\u0289]/g},
        {'base':'v','letters':/[\u0076\u24E5\uFF56\u1E7D\u1E7F\u028B\uA75F\u028C]/g},
        {'base':'vy','letters':/[\uA761]/g},
        {'base':'w','letters':/[\u0077\u24E6\uFF57\u1E81\u1E83\u0175\u1E87\u1E85\u1E98\u1E89\u2C73]/g},
        {'base':'x','letters':/[\u0078\u24E7\uFF58\u1E8B\u1E8D]/g},
        {'base':'y','letters':/[\u0079\u24E8\uFF59\u1EF3\u00FD\u0177\u1EF9\u0233\u1E8F\u00FF\u1EF7\u1E99\u1EF5\u01B4\u024F\u1EFF]/g},
        {'base':'z','letters':/[\u007A\u24E9\uFF5A\u017A\u1E91\u017C\u017E\u1E93\u1E95\u01B6\u0225\u0240\u2C6C\uA763]/g}
      ];

      for(var i=0; i<defaultDiacriticsRemovalMap.length; i++) {
        str = str.replace(defaultDiacriticsRemovalMap[i].letters, defaultDiacriticsRemovalMap[i].base);
      }
      return str;
    };

    // adiciona listeners no dropdown de qtdd de produtos exibidos e no filtro de ordenação
    rhDynamicSearch.listen = function() {
        // dropdown qtd de produtos exibidos
        var dropdownQtd = doc.querySelector('#rh-app .__rh-select-pagination__');
        dropdownQtd.addEventListener('change',rhDynamicSearch.changePaginationQtd);

        // dropdown filtro ordenacao
        var dropdownOrder = doc.querySelector('#rh-app .__rh-select-order__');
        dropdownOrder.addEventListener('change',function(){
            rhDynamicSearch.currentFilter = this.value // altera no obj a informação sobre o filtro atual
            rhDynamicSearch.shelf.populate(rhDynamicSearch.products, rhDynamicSearch.page, rhDynamicSearch.paginationQtd);
        });

    };

    // altera qtd de itens por página
    rhDynamicSearch.changePaginationQtd = function() {
        var qtd = parseInt(this.value);
        rhDynamicSearch.paginationQtd = qtd;
        rhDynamicSearch.shelf.populate(rhDynamicSearch.products, rhDynamicSearch.page);
    };

    // remove acentos etc
    rhDynamicSearch.setProducts = function() {
        for (var x = 0; x < this.products.length; x++) {
            var prod = this.products[x];

            prod.title = decodeURIComponent(prod.title).replace(/\+/g, ' ');
            prod.link = decodeURIComponent(prod.link);
            prod.link_image = decodeURIComponent(prod.link_image);
            prod.type = decodeURIComponent(prod.type).replace(/\+/g, ' ');

            var
            installmentTimes = (prod.months > 1) ? prod.months : 0,
            installmentPrice = (installmentTimes !== 0) ? prod.price / prod.months : 0;

            installmentPrice = rhDynamicSearch.util.toReais(installmentPrice);
            prod.price = rhDynamicSearch.util.toReais(prod.price);
            prod.sale_price = rhDynamicSearch.util.toReais(prod.sale_price);
            prod.installmentTimes = installmentTimes;
            prod.installnentValue = installmentPrice;

            // checa se é uma string, e se for, converte pra array
            if (typeof prod.sku === 'string') {
                prod.sku = prod.sku.split(',');
            }
        }
    };

    // prateleira
    rhDynamicSearch.shelf.populate = function(products,page,addPagination) { // array de prod, página atual, adicionar nova paginacao?
        var
        qtd = rhDynamicSearch.paginationQtd,
        shelf = doc.querySelector('#rh-app .__rh-shelf__'),
        content = '',
        html = rhDynamicSearch.config.productHTML,
        container = doc.createElement('div');

        rhDynamicSearch.products = products; // altera array de produtos principal

        shelf.innerHTML = rhDynamicSearch.loader; // add loader

        for (var i = 0; i < qtd; i++) {
            if (typeof products[i] === 'undefined'){ // se n existir na posicao, sai do loop
                break;
            }
            var
            s = html,
            prod = products[i];

            s = rhDynamicSearch.util.replaceAll(s,'{pos}',i); // posição do produto na array
            s = rhDynamicSearch.util.replaceAll(s,'{sku}',prod.sku.join(','));
            s = rhDynamicSearch.util.replaceAll(s,'{id}',prod.id);
            s = rhDynamicSearch.util.replaceAll(s,'{link}',prod.link);
            s = rhDynamicSearch.util.replaceAll(s,'{discount}',prod.desconto);
            s = rhDynamicSearch.util.replaceAll(s,'{image}',prod.link_image);
            s = rhDynamicSearch.util.replaceAll(s,'{name}',prod.title);

            // checa se carregou as informações dos preços
            if (typeof prod.ready === 'undefined'){
                s = rhDynamicSearch.util.replaceAll(s,'{installment-times}','<span class="loading-installment-times"></span>');
                s = rhDynamicSearch.util.replaceAll(s,'{installment-price}','<span class="loading-installment-price"></span>');
                
                s = s.split('{check_oldprice}');
                s = s[0] + s[1] + s[2];
                s = rhDynamicSearch.util.replaceAll(s,'{sale_price}','<span class="loading-sale-price"></span>');
                s = rhDynamicSearch.util.replaceAll(s,'{price}','<span class="loading-price"></span>');

                // adiciona loader
                s = s.replace('class="rh-product-info"','class="rh-product-info rh-loading-info"');
            }
            else {
                s = rhDynamicSearch.util.replaceAll(s,'{installment-times}',prod.installmentTimes);
                s = rhDynamicSearch.util.replaceAll(s,'{installment-price}',prod.installmentPrice);
    
                if (prod.sale_price === prod.price) {
                    s = s.split('{check_oldprice}');
                    s = s[0] + s[2];
                    s = rhDynamicSearch.util.replaceAll(s,'{sale_price}',prod.price); // se n tiver preço normal, colocar como preço promocional
                } else {
                    s = s.split('{check_oldprice}');
                    s = s[0] + s[1] + s[2];
                    s = rhDynamicSearch.util.replaceAll(s,'{sale_price}',prod.sale_price);
                    s = rhDynamicSearch.util.replaceAll(s,'{price}',prod.price);
                }
            }
            
            content+=s;
        }
        container.classList.add('__rh-shelf-prepared__');
        container.innerHTML = content;

        setTimeout(function(){
            shelf.innerHTML = '';
            shelf.appendChild(container);
            shelf.querySelector('.__rh-shelf-prepared__').classList.remove('waiting');
        },500);
        
        if (addPagination) {
            rhDynamicSearch.pagination.addPagination(); // paginação e eventos d paginação
        }
    };

    // paginação
    rhDynamicSearch.pagination = {
        // adiciona paginação prateleira
        addPagination: function(){
            let el = doc.querySelectorAll('#rh-app .__rh-pagination__'); // container paginaçao
            let pages = rhDynamicSearch.pagination.getPageAmount(); // qtdd de páginas
            this.pages = pages; // seta qtd de páginas

            // só tem páginação caso haja mais de 1 página
            if (pages > 1) {
                var pagination = '';
                for (var i = 1; i <= pages; i++) {
                    if (i === 1) {
                        pagination+='<span class="__rh-goto-page__ active" data-rh-page="'+i+'">'+i+'</span>';
                    } else {
                        pagination+='<span class="__rh-goto-page__" data-rh-page="'+i+'">'+i+'</span>';
                    }

                    if (i === 5){
                        break;
                    }
                }

                if (pages > 5){
                    pagination =
                        '<span class="__rh-goto-first-page__ disabled">Primeira</span><span class="__rh-goto-previous-page__ disabled">Anterior</span>'+
                            pagination+
                        '<span class="__rh-goto-next-page__">Próxima</span><span class="__rh-goto-last-page__">Última</span>';
                }
                else {
                    pagination =
                        '<span class="__rh-goto-previous-page__ disabled">Anterior</span>'+
                            pagination+
                        '<span class="__rh-goto-next-page__">Próxima</span>';
                }
                
                for (var i = 0; i < el.length; i++) {
                    el[i].innerHTML = pagination;
                    this.listen(el[i]);
                }

            } else {
                for (var i = 0; i < el.length; i++) {
                    el[i].innerHTML = ''
                }
            }
        },

        listen: function(el){
            // botoes referentes às páginas
            var
            gotoButtons = el.querySelectorAll('.__rh-goto-page__');
            for (var i = 0; i < gotoButtons.length; i++) {
                gotoButtons[i].addEventListener('click',function(){
                    rhDynamicSearch.pagination.goToPage(this.dataset.rhPage);
                })
            }

            // avancar página
            var nextPage = el.querySelector('.__rh-goto-next-page__');
            if (nextPage) {
                nextPage.addEventListener('click',rhDynamicSearch.pagination.nextPage);
            }


            // voltar página
            var previousPage = el.querySelector('.__rh-goto-previous-page__');
            if (previousPage) {
                previousPage.addEventListener('click',rhDynamicSearch.pagination.previousPage);
            }

            // ir para última página
            var lastPage = el.querySelector('.__rh-goto-last-page__');
            if (lastPage){
                lastPage.addEventListener('click',rhDynamicSearch.pagination.goToLastPage);
            }

            // ir para primeira página
            var firstPage = el.querySelector('.__rh-goto-first-page__');
            if (firstPage) {
                firstPage.addEventListener('click',function(){
                    rhDynamicSearch.pagination.goToPage(1);
                });
            }
        },

        goToPage: function(page) {
            page = parseInt(page);
            let pagesAmount = rhDynamicSearch.pagination.getPageAmount(); // qtd de páginas

            if (page > pagesAmount) { return false }// se for maior q a qtdd de páginas, sai da função

            let newRange = (page * rhDynamicSearch.paginationQtd) - rhDynamicSearch.paginationQtd; // a partir de onde vai cortar a array de resultados

            // altera botoes
            rhDynamicSearch.pagination.arrangeButtons(page);

            rhDynamicSearch.shelf.populate(rhDynamicSearch.products.slice(newRange), page); // range, pagina, paginacao nova
            rhDynamicSearch.page = page;
        },

        nextPage: function() {
            var
            newPage = rhDynamicSearch.page + 1,
            pagesAmount = rhDynamicSearch.pagination.getPageAmount(); // qtd de páginas

            if (newPage <= pagesAmount) { // se n for maior q a qtdd de páginas
                rhDynamicSearch.pagination.goToPage(newPage);
            }
        },

        previousPage: function() {
            if (rhDynamicSearch.page > 1) { // só retrocede se n estiver na primeira
                var
                newPage = rhDynamicSearch.page - 1,
                pagesAmount = rhDynamicSearch.pagination.getPageAmount(); // qtd de páginas

                if (newPage <= pagesAmount) { // se n for maior q a qtdd de páginas
                    rhDynamicSearch.pagination.goToPage(newPage);
                }
            }
        },

        goToLastPage: function(){
            var pagesAmount = rhDynamicSearch.pagination.getPageAmount();
            rhDynamicSearch.pagination.goToPage(pagesAmount);
        },

        getPageAmount: function(){
            if (rhDynamicSearch.products.length % rhDynamicSearch.paginationQtd > 0) { // checa se sobra
                return parseInt(rhDynamicSearch.products.length/rhDynamicSearch.paginationQtd) + 1;
            } else {
                return parseInt(rhDynamicSearch.products.length/rhDynamicSearch.paginationQtd);
            }
        },

        // ajusta os botões de paginação de acordo com a página
        arrangeButtons: function(page) {
            var paginations = doc.querySelectorAll('#rh-app .__rh-pagination__');

            for (var x = 0; x < paginations.length; x++) {
                var
                pagesAmount = rhDynamicSearch.pagination.getPageAmount(), // qtd de páginas
                btn = paginations[x].querySelectorAll('.__rh-goto-page__');


                var previousPage = paginations[x].querySelector('.__rh-goto-previous-page__'); // btn volta
                var firstPage = paginations[x].querySelector('.__rh-goto-first-page__');// btn avancar
                var lastPage = paginations[x].querySelector('.__rh-goto-last-page__');
                var nextPage = paginations[x].querySelector('.__rh-goto-next-page__');

                if (page <= 3){                             // só altera até o terceiro
                    for (var i = 0; i < btn.length; i++) {  // depois disso muda pelos datasets
                        btn[i].dataset.rhPage = i + 1;
                        btn[i].innerHTML = i + 1;
                        btn[i].classList.remove('active');
                    }
                }
                else if (page + 2 >= pagesAmount) { // se for a última ou a penúltima página
                    var cont = pagesAmount - 4; // sibtrai por causa do looping
                    for (var i = 0; i < btn.length; i++) {
                        btn[i].dataset.rhPage = cont;
                        btn[i].innerHTML = cont;
                        btn[i].classList.remove('active');
                        cont++;
                    }
                } else {
                    var pageDifference = page - 2; // subtrai dois para começar antes por causa do contador

                    for (var i = 0; i < 5; i++) {
                        btn[i].dataset.rhPage = pageDifference;
                        btn[i].innerHTML = pageDifference;
                        btn[i].classList.remove('active');
                        pageDifference++;
                    }
                }

                if (page === 1) {
                    previousPage.classList.add('disabled');
                    if (firstPage) {
                        firstPage.classList.add('disabled');
                    }
                } else {
                    previousPage.classList.remove('disabled');
                    if (firstPage) {
                        firstPage.classList.remove('disabled');
                    }
                }

                if (page === pagesAmount) {
                    nextPage.classList.add('disabled');
                    if (lastPage) {
                        lastPage.classList.add('disabled');
                    }
                } else {
                    nextPage.classList.remove('disabled');
                    if (lastPage) {
                        lastPage.classList.remove('disabled');
                    }
                }

                // ativa botao referente à página
                paginations[x].querySelector('.__rh-goto-page__[data-rh-page="'+page+'"]').classList.add('active');
            }


        },

        resetPagination: function() {
            // verifica se n tem paginação, pra então resetar ou adicionar uma se for o caso
            if (doc.querySelectorAll('#rh-app .__rh-goto-page__').length === 0) {
                this.addPagination();
                return false;
            }

            var
            btn = doc.querySelectorAll('#rh-app .__rh-goto-page__'),
            previousPage = doc.querySelector('#rh-app .__rh-goto-previous-page__'), // btn volta
            firstPage = doc.querySelector('#rh-app .__rh-goto-first-page__'),// btn avancar
            lastPage = doc.querySelector('#rh-app .__rh-goto-last-page__'),
            nextPage = doc.querySelector('#rh-app .__rh-goto-next-page__');

            for (var i = 0; i < btn.length; i++) {
                btn[i].dataset.rhPage = i + 1;
                btn[i].innerHTML = i + 1;
                btn[i].classList.remove('active');
            }
            btn[0].classList.add('active');
            previousPage.classList.add('disabled');
            firstPage.classList.add('disabled');
            nextPage.classList.remove('disabled');
            lastPage.classList.remove('disabled');
        },

        removePagination: function() {
            var el = doc.querySelector('#rh-app .__rh-pagination__');
            el.innerHTML = '';
        }
    };

    // VTEX API
    rhDynamicSearch.vtex.getProdById = function(products,callback){ // array de produtos (MÁXIMO DE 50) 
        products = products.join('&fq=productId:');     
        products = 'fq=productId:' + products;

        let url = '/api/catalog_system/pub/products/search?'+products+'&_from=1&_to=50';
        rhDynamicSearch.util.ajax({
            'type':'get',
            'url': url,
            'data': {'id': rhClientId},
            // função callback da chamada
            callback: callback
        });
    };

    rhDynamicSearch.vtex.getPriceUsingSalesChannel = function( skuList , callback) {
        let salesChannel = $.cookie('salesChannel');
        let items = [];

        for (let i = 0; i < skuList.length; i++) {
            items.push({
                "id": skuList[i],
                "quantity": 1,
                "seller": "1"
            }); 
        }

        $.ajax({
            async: true,
            contentType: 'application/json; charset=UTF-8',
            dataType: 'json',
            type: 'POST',
            url: '/api/checkout/pub/orderForms/simulation?sc='+ salesChannel,
            data: JSON.stringify({
                "items":items,
                "postalCode": null,
                "country": "BRA"
            }),
            success: function(response) {
                callback(response);
            },
        });
    };

    // atualiza preços de acordo com a API
    rhDynamicSearch.updatePrices = function(vtexData){
        let items = vtexData.items;

        // itera entre todos os produtos q vieram na api
        for (let i = 0; i < items.length; i++) {
            let sku = items[i].id;
            let price = items[i].listPrice;
            let sale_price = items[i].sellingPrice;

            // itera entre todos os produtos da roi hero
            for (let j = 0; j < rhDynamicSearch.products.length; j++) {
                if (rhDynamicSearch.products[j].sku[0] == sku) {
                    sale_price = rhDynamicSearch.util.toReais(sale_price/100);
                    price = rhDynamicSearch.util.toReais(price/100);         
                                        
                    rhDynamicSearch.products[j].sale_price = sale_price;
                    rhDynamicSearch.products[j].price = price;
                    rhDynamicSearch.products[j]['ready'] = true;

                    // se tiver o produto na prateleira, troca o preço
                    let prod = doc.querySelector('.rh-dynamic-product[data-rh-sku="'+sku+'"]')
                    if (prod) {
                        let infoContainer = prod.querySelector('.rh-product-info');

                        // remove loader e adiciona preço no produto
                        infoContainer.classList.remove('rh-loading-info');
                        // checa se o preço promocional é menor q o preço antigo
                        if (sale_price < price){
                            prod.querySelector('.rh-old-price').innerHTML = price;
                            prod.querySelector('.rh-sale-price').innerHTML = sale_price;
                        } 
                        else {
                            prod.querySelector('.rh-old-price').innerHTML = '';
                            prod.querySelector('.rh-sale-price').innerHTML = sale_price;
                        }
                    }

                    break;
                } 
            }

            // faz o mesmo pra outra array
            // itera entre todos os produtos da roi hero
            for (let j = 0; j < rhDynamicSearch.productList.length; j++) {
                if (rhDynamicSearch.productList[j].sku[0] == sku) {                                        
                    rhDynamicSearch.productList[j].sale_price = sale_price;
                    rhDynamicSearch.productList[j].price = price;
                    rhDynamicSearch.productList[j]['ready'] = true;

                    break;
                } 
            }
        }
    }

    /*
    * Filtros laterais
    */
    rhDynamicSearch.filters = {
        products: [],

        activeFilters: {            
            category: {
                // 'categoria':'subcategoria ou "all" para todas as subcategorias'
            },

            priceRange: [], // [range1,range2]

            discountRange: [] // [range1,range2]
        },

        // pega as informações dos produtos que vieram na Api e mixa com os que a gente tem
        arrangeVtexFilters: function(data) {
            let products = [];

            data = JSON.parse(data.responseText);

            // itera entre todos os produtos que vieram da API
            for (var i = 0; i < data.length; i++) {
                let product = data[i];
                let sku = [];
                let variations = {};
                for (var j = 0; j <= product.items.length; j++) {                    
                    if (typeof product.items[j] !== 'undefined') {
                        sku.push( product.items[j].itemId ); // pega a lista de sku e variações
                        variations[ product.items[j].itemId  ] = product.items[j];
                    }
                }

                // pega o produto de mesmo SKU
                let sameProd = [];
                for (var j = 0; j < rhDynamicSearch.products.length; j++) {
                    let prod = rhDynamicSearch.products[j];
                    if (sku.contains( prod.sku )) {
                        sameProd = sameProd.concat( prod );

                        // pega categoria principal
                        let category = product.categories[0].slice(1,-1).split('/')[0];
                        // pega subcategorias
                        let subcategories = [];
                        for (let k = 0; k < product.categories.length; k++) {
                            let sub = product.categories[k].slice(1,-1).split('/').slice(1);
                            // itera entre as subcategorias
                            for (let l = 0; k < sub.length; k++) {
                                if (!subcategories.contains( sub[k] )) {
                                    subcategories.push( sub[k] );
                                }
                            }
                        }

                        rhDynamicSearch.products[j].subcategories = subcategories; // adiciona subcategorias nos produtos
                        rhDynamicSearch.products[j].category = category; // adiciona categoria nos produtos
                    }
                }

                
                for (var j = 0; j < rhDynamicSearch.productList.length; j++) {
                    let prod = rhDynamicSearch.productList[j];
                    if (sku.contains( prod.sku )) {
                        // pega categoria principal
                        let category = product.categories[0].slice(1,-1).split('/')[0];
                        // pega subcategorias
                        let subcategories = [];
                        for (let k = 0; k < product.categories.length; k++) {
                            let sub = product.categories[k].slice(1,-1).split('/').slice(1);
                            // itera entre as subcategorias
                            for (let l = 0; k < sub.length; k++) {
                                if (!subcategories.contains( sub[k] )) {
                                    subcategories.push( sub[k] );
                                }
                            }
                        }
                        rhDynamicSearch.productList[j].subcategories = subcategories; // adiciona subcategorias nos produtos
                        rhDynamicSearch.productList[j].category = category; // adiciona categoria nos produtos
                    }
                }
                products = products.concat(sameProd);
            }

            rhDynamicSearch.filters.setVtexFilters(products); // adiciona filtros

            // adiciona informações na outra array de produtos tbm
            for (var j = 0; j < rhDynamicSearch.productList.length; j++) {
                let sku = [];
                let prod = rhDynamicSearch.productList[j];
                if (sku.contains( prod.sku )) {
                    sameProd = sameProd.concat( prod );

                    // pega categoria principal
                    let category = product.categories[0].slice(1,-1).split('/')[0];

                    // pega subcategorias
                    let subcategories = [];
                    for (let k = 0; k < product.categories.length; k++) {
                        let sub = product.categories[k].slice(1,-1).split('/').slice(1);
                        // itera entre as subcategorias
                        for (let l = 0; k < sub.length; k++) {
                            if (!subcategories.contains( sub[k] )) {
                                subcategories.push( sub[k] );
                            }
                        }
                    }

                    rhDynamicSearch.productList[j].subcategories = subcategories; // adiciona subcategorias nos produtos
                    rhDynamicSearch.productList[j].category = category; // adiciona categoria nos produtos
                }
            }
        },

        // seta filtros que necessitam da API Vtex
        setVtexFilters(products) {
            // categoria e subcategoria
            let categoryFilterContainer = doc.querySelector('[data-rh-filter="category"]');
                        
            for (let x = 0; x < products.length; x++) {
                // categoria
                let product = products[x];
                let category = product.category;
                let subcategories = product.subcategories;

                let container = categoryFilterContainer.querySelector('[data-rh-category-filter="'+category+'"]');
    
                if (container == null) { // se n existir a lista de filtros, cria na hr
                    // cria UL referente à categoria
                    let mainUL = doc.createElement('ul');
                    mainUL.dataset.rhCategoryFilter = category;
                    
                    // cria LI categoria pai
                    let li = doc.createElement('li');
                    li.classList = 'rh-category-filter rh-main-category rh-filter-option';
                    li.dataset.rhMainCategory = category;
                    li.innerHTML = '<span>'+category+'</span>';

                    // adiciona listener
                    li.addEventListener('click',function(){
                        // filtra
                        let result = rhDynamicSearch.filters.filterEveryThing('category',this);
                        // popula a prateleira com os resultados
                        rhDynamicSearch.shelf.populate(result,1,true); 
                    });
                    
                    // cria UL subcategoria
                    let childULLI = doc.createElement('li');
                    let childUL = doc.createElement('ul');
                    childUL.classList = 'rh-subcategory-list';
                    childULLI.appendChild(childUL);

                    for (let i = 0; i < subcategories.length; i++) {
                        /*
                        * Checa se alguma subcategoria com o mesmo nome já existe,
                        * caso exista, não cria outra
                        * podem haver algumas subcategorias que tem mais de uma categoria pai
                        * Ex: Limpeza > Papel Toalha || Alimentação > Papel Toalha
                        */
                        if (doc.querySelectorAll('[data-rh-subcategory="'+subcategories[i]+'"]').length < 1) {
                            let sub = subcategories[i];
                            let a = doc.createElement('li');
                            a.classList = 'rh-category-filter rh-sub-category rh-filter-option';
                            a.dataset.rhSubcategory = sub; 
                            a.dataset.rhMainCategory = category;
                            a.innerHTML = '<span>'+sub+'</span>';
                            
                            // adiciona listener
                            a.addEventListener('click',function(){
                                // filtra
                                let result = rhDynamicSearch.filters.filterEveryThing('subcategory',this);
                                // popula a prateleira com os resultados
                                rhDynamicSearch.shelf.populate(result,1,true); 
                            });

                            childUL.appendChild(a);
                        }                        
                    }

                    mainUL.appendChild(li);
                    mainUL.appendChild(childULLI);
                    categoryFilterContainer.appendChild(mainUL);
                    categoryFilterContainer.classList.remove('rh-hidden');
                }
            }
        },

        // seta filtros que não precisam de API
        setFilters: function(){
            /*
            * FILTRO POR PREÇO:
            * Itera entre todos os produtos e pega o maior valor de preço
            * Depois divide esse valor em até no máximo 5x
            * para pegar os intervalos entre os preços.
            * 
            * FILTRO POR DESCONTO:
            * Faz quase a mesma coisa.
            */
            let highestPrice = 0;
            let lowestPrice = 0;
            let discountArray = [];
            for (let i = 0; i < rhDynamicSearch.productList.length; i++) {
                let preco = rhDynamicSearch.productList[i].sale_price;
                preco = rhDynamicSearch.util.realToFloat(preco); // converte a string para float

                let discount = rhDynamicSearch.productList[i].desconto;
                
                if (discount> 0) {
                    discountArray.push(discount);           
                } 
                
                if (preco > highestPrice) {
                    highestPrice = preco;
                }

                if (i === 0) {
                    lowestPrice = preco;
                } 
                else if (preco < lowestPrice) {
                    lowestPrice = preco;
                }

            }
            // seta os filtros para preço
            this.addPriceFilters(highestPrice);

            // filtro por desconto
            this.addDiscountFilters(discountArray);
        },

        // adiciona filtros de desconto
        addDiscountFilters(discountArray) {
            let discounts = [];
            let listOfFilters = doc.createElement('ul');
            
            // ordena do menor para o maior
            discountArray = discountArray.sort(function(a,b){
                if (a >= b) {
                    return 1;
                } else {
                    return -1;
                }
            });

            // remove duplicados
            discountArray = discountArray.filter(function(item, pos) {
                return discountArray.indexOf(item) == pos;
            })

            
            console.log(discountArray);

            // itera até 10 no máximo
            for (let i = 1; i <= 8;i++) {
                let indexA = 0;
                let indexB = 0;

                // se a quantidade de produtos com desconto for pouca
                if (discountArray.length < 10) {
                    indexA = i-1;
                    indexB = i+1;
                    
                    // sai do loop quando ultrapassa o limite do array
                    if (indexA >= discountArray.length || indexB >= discountArray.length) {
                        break;
                    }

                    let discount1 = discountArray[indexA];
                    let discount2 = discountArray[indexB];

                    discounts.push([[discount1],[discount2]]);
                } 
                // se a quantidade de produtos com desconto for maior ou igual a 10                
                else {                    
                    indexA = Math.floor( discountArray.length / (i+1) ) - 1;
                    indexB = Math.floor( discountArray.length / (i) ) - 1;

                    let low = discountArray[0]; // menor desconto

                    if (i === 10 || discountArray[indexA] <= 10 || discountArray[indexB] <= 10) {

                        // se for maior q o menor desconto, altera o index para o primeiro
                        if (discountArray[indexA] > low) {
                            indexA = 0;
                        }
                    }                

                    let discount1 = discountArray[indexA];
                    let discount2 = discountArray[indexB];

                    discounts.push([[discount1],[discount2]]);

                    if (discount1 === low) {
                        break;
                    }
                }
            }
            
            console.log(discounts);
            
            // itera do maior para o menor range
            for (let i = discounts.length - 1; i >= 0; i--) {
                let li = doc.createElement('li');
                li.dataset.rhDiscountFilterRange = discounts[i][0]+'/'+discounts[i][1];
                li.classList = 'rh-discount-filter rh-filter-option';
                li.innerHTML = '<span>'+discounts[i][0]+'% a '+discounts[i][1]+'% </span>';

                li.addEventListener('click',function(){
                    let results = rhDynamicSearch.filters.filterEveryThing('discount',this);
                    // popula prateleira
                    rhDynamicSearch.shelf.populate(results,1,true);
                });

                listOfFilters.append(li);
            }

            // insere nos filtros laterais
            let filterContainer = doc.querySelector('.rh-filter[data-rh-filter="discount"]');
            filterContainer.appendChild(listOfFilters);
            filterContainer.classList.remove('rh-hidden'); // mostra container
        },

        // adiciona filtros de preço
        addPriceFilters: function(highestPrice){
            let priceRange = [];
            let range1 = 0;            
            let range2 = 0;
            let arrayOfElements = [];

            let listOfFilters = doc.createElement('ul');   

            if (highestPrice >= 10) {
                let i = 3;
                
                range2 = (Math.floor(highestPrice/10) * 10) + 10; // arredonda pra um múltiplo de 10 e soma +10 
                range1 = range2 / 2;
                do {
                    let label = ''; // texto q vai no filtro

                    // checa se vai quebrar
                    if (range1 % 2 > 0 || range2 % 2 > 0) {
                        range1 = (Math.floor(range1 / 10) * 10) + 10;
                        range2 = (Math.floor(range2 / 10) * 10) + 10;

                        range1 = range1 / 2;
                        range2 = range2 / 2;
                    }
                    else {
                        range1 = range1 / 2;
                        range2 = range2 / 2;
                    }

                    if (range2 <= 10) {
                        let price = rhDynamicSearch.util.toReais(range2);
                        label = 'Até '+price;
                        priceRange.push(label);

                        range1 = 0;
                    } 
                    else {
                        let price1 = rhDynamicSearch.util.toReais(range1);
                        let price2 = rhDynamicSearch.util.toReais(range2);
                        label = price1+' a '+price2;
                        priceRange.push(label);
                    }
                    
                    // cria elemento
                    let filter = doc.createElement('li');
                    filter.classList = 'rh-price-filter rh-filter-option';
                    filter.dataset.rhPriceFilterRange = range1+'/'+range2;
                    filter.innerHTML = '<span>'+label+'</span>'
                    arrayOfElements.push(filter);

                    filter.addEventListener('click', function(){ 
                        let priceRange = this.dataset.rhPriceFilterRange.split('/');
                        
                        let results = rhDynamicSearch.filters.filterEveryThing('priceRange',this);                        

                        // popula prateleira
                        rhDynamicSearch.shelf.populate(results,1,true);
                    });

                    i+=3;                  
                } while (range2 > 10);                
            }
            
            // insere os filtros no menu lateral, do menor para o maior
            for (let i = arrayOfElements.length - 1; i >=0; i--) {
                listOfFilters.appendChild(arrayOfElements[i]);
            }

            let filterContainer = doc.querySelector('.rh-filter[data-rh-filter="price"]');

            filterContainer.appendChild(listOfFilters);
            filterContainer.classList.remove('rh-hidden');
        },

        // retorna uma array com os produtos filtrados pelo intervalo de preços
        filterByPriceRange(low, high, prodList) {
            let newProdList = [];
      
            for (let i = 0; i < prodList.length; i++) {
                let price = rhDynamicSearch.util.realToFloat( prodList[i].sale_price );

                // checa se está no range
                if (price >= low && price <= high) {
                    newProdList.push( prodList[i] );
                }
            }
            
            // altera produtos filtrados
            this.products = newProdList;
            return newProdList;
        },

        // retorna uma array com os produtos filtrados pela categoria
        filterByCategory: function(category, prodList){
            let newProdList = []; 
            
            let produtosSemCategoria = 'PRODUTOS SEM CATEGORIA:';
            let prodSemCatCount = 0;
            for (let i = 0; i < prodList.length; i++) {
                // caso o produto n tenha categoria
                if (typeof prodList[i].category === 'undefined') {
                    let n = prodList[i].title;
                    n = (n.length > 15) ? n.slice(0,15) + '...' : n;
                    produtosSemCategoria += 'NOME: "'+n+'" SKU: '+prodList[i].sku[0]+' ID:'+prodList[i].id+'\n';
                    prodSemCatCount++;
                } 
                // compara categoria do produto com a do filtro
                else if ( prodList[i].category == category ){
                    newProdList.push( prodList[i] );
                }
            }

            console.log(produtosSemCategoria);
            console.log(prodSemCatCount+' no total!');
            console.log(newProdList);
            
            console.log('filtrado pela categoria "'+category+'"');

            // altera array de produtos filtrados
            this.products = prodList;

            // atualiza filtros
            this.updateTags();
            
            return newProdList;
        },

        // retorna uma array de produtos de acordo com os filtros
        filterBySubcategory: function(subcategoryArray, prodList){  
            // filtra a array de produtos e pega os produtos que tem aquela categoria
            let newProdList = [];
            for (let i = 0; i < prodList.length; i++) {
                let prod = prodList[i];

                // itera entre todas as subcategorias e compara com o produto
                if (typeof prod.subcategories !== 'undefined') {
                    for (let j = 0; j < subcategoryArray.length; j++) {                        
                        if (prod.subcategories.includes( subcategoryArray[j] )) { // compara se o produto tem a subcategoria
                            newProdList.push(prod);
                        }
                    }
                }
            }
            return newProdList;
        },

        filterByDiscountRange: function(low, high, prodList){
            let newProdList = [];
      
            for (let i = 0; i < prodList.length; i++) {
                let discount = prodList[i].desconto;

                // checa se está no range
                if (discount >= low && discount <= high) {
                    newProdList.push( prodList[i] );
                }
            }
            
            // altera produtos filtrados
            this.products = newProdList;

            console.log(newProdList);
            return newProdList;
        },

        /*
        * Filtra tudo!
        */
        filterEveryThing: function(filter, element) {
            let prodList = rhDynamicSearch.productList.slice(0);
            let newProdList = rhDynamicSearch.productList.slice(0);

            switch(filter) {
                case 'discount':
                    // se já estiver selecionado, reseta o obj que armazena o filtro
                    if (element.classList.contains('active-filter')) {
                        this.activeFilters.discountRange = []; // reseta filtro de desconto
                    } 
                    else {                        
                        let discountRange = element.dataset.rhDiscountFilterRange.split('/');
                        /*
                        *                ATENÇÃO!
                        * Temporariamente (ou não), o filtro de desconto 
                        * só pode ser selecionado separadamente
                        * para não correr o risco de retornar um array vazio.                      
                        */                        
                        this.activeFilters.category = {}; // reseta filtros categoria
                        this.activeFilters.priceRange = []; // reseta filtros de preço                        
                        this.activeFilters.discountRange = [discountRange[0],discountRange[1]]; // seta filtro desconto
                    }             
                    break;
                case 'priceRange':
                    /*
                    *                  ATENÇÃO!
                    * Temporariamente (ou não), o filtro de desconto 
                    * só pode ser selecionado separadamente
                    * para não correr o risco de retornar um array vazio.                      
                    */    
                    this.activeFilters.discountRange = []; // reseta filtro de desconto

                    // se já estiver selecionado, reseta o obj que armazena o filtro
                    if (element.classList.contains('active-filter')) {  
                        this.activeFilters.priceRange = [];
                    } 
                    else {                        
                        let priceRange = element.dataset.rhPriceFilterRange.split('/');             
                        // altera obj que armazena os filtros ativos
                        this.activeFilters.priceRange = [priceRange[0],priceRange[1]];  
                    }             
                    break;
                case 'category':
                    /*
                    *                  ATENÇÃO!
                    * Temporariamente (ou não), o filtro de desconto 
                    * só pode ser selecionado separadamente
                    * para não correr o risco de retornar um array vazio.                      
                    */    
                    this.activeFilters.discountRange = []; // reseta filtro de desconto

                    // se já estiver selecionado, reseta o obj que armazena o filtro
                    if (element.classList.contains('active-filter')) {
                        this.activeFilters.category = {}
                    } 
                    else {
                        this.activeFilters.category = {};
                        this.activeFilters.category[element.dataset.rhMainCategory] = 'all';
                    }
                    break;
                case 'subcategory':
                    /*
                    *                  ATENÇÃO!
                    * Temporariamente (ou não), o filtro de desconto 
                    * só pode ser selecionado separadamente
                    * para não correr o risco de retornar um array vazio.                      
                    */    
                    this.activeFilters.discountRange = []; // reseta filtro de desconto

                    let category = element.dataset.rhMainCategory;
                    let subcategory = element.dataset.rhSubcategory;
                    let subCats = []; // array de subcategorias

                    // checa se existe o filtro ativo na categoria
                    if (typeof this.activeFilters.category[category] === 'undefined') {
                        /*  
                            !IMPORTANTE!
                            Temporariamente, toda vez que uma subcategoria for selecionada,
                            desativar todos os filtros de outras categorias
                        */
                        this.activeFilters.category = {}
                        subCats = [subcategory];
                    } 
                    // checa se todos os filtros estão selecionados
                    // se for o caso, remove o filtro selecionado e faz a pesquisa pelos outros
                    else if (this.activeFilters.category[category] === 'all'){
                        let subCatContainer = doc.querySelector('[data-rh-category-filter="'+category+'"]'); // container subcategorias
                        let subcategories = subCatContainer.querySelectorAll('.rh-category-filter.rh-sub-category');

                        // pega todas as subcategorias
                        for (let i = 0; i < subcategories.length; i++) {
                            let s = subcategories[i].dataset.rhSubcategory;
                            subCats.push(s);
                        }

                        // remove a subcategoria da lista
                        subCats.remove(subcategory);
                        this.activeFilters.category[category] = subCats.join('/');
                    } else {
                        let subCatContainer = doc.querySelector('[data-rh-category-filter="'+category+'"]'); // container subcategorias
                        let activeSubCats = subCatContainer.querySelectorAll('.rh-category-filter.rh-sub-category.active-filter'); // subcategorias ativas
                        subCats = this.activeFilters.category[category].split('/');

                        // checa se a subcategoria já não está selecionada
                        // se estiver, remove da array
                        if ( subCats.includes(subcategory) ) {
                            subCats.remove(subcategory);
                        } 
                        // checa se selecionou todas os subcategorias, contando com essa
                        else if (subCats.length === activeSubCats.length) {                    
                            // caso todas as categorias estejam selecionadas, troca pra all
                            this.activeFilters.category[category] = 'all';
                        } else {
                            subCats.push(subcategory);
                        }
                    }
                    
                    if (subCats.length === 0) { 
                        this.activeFilters.category = {};
                    } 
                    else if (this.activeFilters.category[category] !== 'all') {                        
                        // altera o valor do objeto que armazena as categorias e subcategorias,
                        this.activeFilters.category[category] = subCats.join('/');
                    }
                    
                    break;
            }

            // filtra por categoria e subcategoria
            if (Object.keys(this.activeFilters.category).length > 0) { // verifica se tem algum filtro por categoria ativo
                // verifica se todas as categorias estão selecionadas
                let key = Object.keys(this.activeFilters.category)[0]; // por enquanto só da pra selecionar de uma categoria por vez
                // se for 'all', pesquisa pela categoria direto
                if (this.activeFilters.category[key] === 'all') {
                    newProdList = this.filterByCategory( key , newProdList );
                }
                // se não, pesquisa por todas as subcategorias
                else {
                    let subCats = this.activeFilters.category[key].split('/');
                    newProdList = this.filterBySubcategory( subCats, newProdList );
                }
            }

            // filtra por preço
            if (this.activeFilters.priceRange.length > 0) { // verifica se tem algum filtro por preço ativo
                newProdList = this.filterByPriceRange( this.activeFilters.priceRange[0] , this.activeFilters.priceRange[1] , prodList );
            }

            // filtra por desconto
            if (this.activeFilters.discountRange.length > 0) { // verifica se tem algum filtro por preço ativo
                newProdList = this.filterByDiscountRange( this.activeFilters.discountRange[0] , this.activeFilters.discountRange[1] , prodList );
            }

            this.updateTags();
            return newProdList;
        },

        // atualiza as tags de acordo com o OBJ principal de filtros
        updateTags: function() {
            // primeiro, reseta todas as tags de filtros
            this.resetTags();

            // filtros categorias
            let categories = this.activeFilters.category;
            for (let property in categories) {
                // caso a categoria principal esteja selecionada, ativa todas as subcategorias
                if (categories[property] === 'all') {
                    let filterTags = doc.querySelectorAll('.rh-category-filter[data-rh-main-category="'+property+'"]');
                    for (let i = 0; i < filterTags.length; i++) {
                        filterTags[i].classList.remove('active-filter');
                        filterTags[i].classList.add('active-filter');
                    }
                } else {
                    let activeSubCategories = categories[property].split('/');

                    // ativa todos as tags de subcategoria
                    for (let i = 0; i < activeSubCategories.length; i++) {
                        let activeSubCategory = doc.querySelector('.rh-category-filter.rh-sub-category[data-rh-subcategory="'+activeSubCategories[i]+'"]');
                        activeSubCategory.classList.remove('active-filter');
                        activeSubCategory.classList.add('active-filter');
                    }
                    
                }          
            }

            // filtros preços
            let priceRange = this.activeFilters.priceRange;
            let priceTags = doc.querySelectorAll('.rh-price-filter');
            for (let i = 0; i < priceTags.length; i++) {// reseta todas as tags
                priceTags[i].classList.remove('active-filter');
            }
            if (priceRange.length > 0) {
                let dataSet = priceRange.join('/');
                let tag = doc.querySelector('[data-rh-price-filter-range="'+dataSet+'"]');
                tag.classList.remove('active-filter');
                tag.classList.add('active-filter');
            }       

            // filtros descontos
            let discountRange = this.activeFilters.discountRange;
            let discountTags = doc.querySelectorAll('.rh-discount-filter');
            for (let i = 0; i < discountTags.length; i++) {// reseta todas as tags
                discountTags[i].classList.remove('active-filter');
            }
            if (discountRange.length > 0) {
                let dataSet = discountRange.join('/');
                let tag = doc.querySelector('[data-rh-discount-filter-range="'+dataSet+'"]');
                tag.classList.remove('active-filter');
                tag.classList.add('active-filter');
            } 
        },

        resetTags: function() {
            let filters = doc.querySelectorAll('.rh-category-filter');

            for (let i = 0; i < filters.length; i++) {
                filters[i].classList.remove('active-filter');
            }
        }
    }
    
    // função que inicia/carrega APP
    rhDynamicSearch.init = function(){
        // loader
        rhDynamicSearch.loader = doc.querySelector('#rh-app .__rh-shelf__').innerHTML;

        // carrega array de resultados direto do servidor
        if (rhDynamicSearch.config.getResultFromServer === true) {
            let term = this.util.getSearchTerm();
            rhDynamicSearch.util.ajax({
                type:'post',
                url: 'https://www.roihero.com.br/searchbar/get_busca.php',
                data: {'idcli':rhClientId, 'limite': 300, 'termo':term},
                // função callback da chamada
                callback: function(ajax) {
                    // atribui produtos ao objeto
                    rhDynamicSearch.products = JSON.parse(ajax.responseText);
                    rhDynamicSearch.setProducts(); // ajusta preços, nomes, parcelament etc

                    // PRINTA MENSAGENS DE INFORMAÇÃO
                    console.log('========================= ROI HERO ====================================')
                    console.log('Resultados carregados, '+rhDynamicSearch.products.length+' produtos no total.');

                    // PRINTA MENSAGENS DE INFORMAÇÃO
                    console.log('O termo pesquisado foi "'+term+'".')
                    console.log('Foram encontrados '+rhDynamicSearch.products.length+' resultados.');
                    console.log('========================= ROI HERO ====================================');

                    // backup da lista de produtos, caso precise resetar                
                    rhDynamicSearch.productList = rhDynamicSearch.products;

                    /*
                    * Depois que ordena, monta os filtros
                    * e ajusta os preços
                    */
                    let idList = [];
                    let skuList = [];
                    for (var i = 0; i < rhDynamicSearch.products.length; i++) {
                        // enviar requisiçoes de 50 em 50
                        if (i%50 === 0 && i > 0) {
                            rhDynamicSearch.vtex.getProdById( idList , rhDynamicSearch.filters.arrangeVtexFilters );
                            rhDynamicSearch.vtex.getPriceUsingSalesChannel( skuList , rhDynamicSearch.updatePrices );
                            skuList = [];
                            idList = [];
                        }

                        // adiciona sku na array
                        skuList.push( rhDynamicSearch.products[i].sku[0] );

                        // adiciona id na array
                        idList.push( rhDynamicSearch.products[i].id );

                        // checa se está no final - caso n haja 50 produtos restantes, é necessário
                        if (i + 1 >= rhDynamicSearch.products.length) {
                            rhDynamicSearch.vtex.getProdById( idList , rhDynamicSearch.filters.arrangeVtexFilters );
                            rhDynamicSearch.vtex.getPriceUsingSalesChannel( skuList , rhDynamicSearch.updatePrices );
                        }
                    }

                    console.log(rhDynamicSearch.products);
                    // popula HTML da prateleira com produtos
                    rhDynamicSearch.shelf.populate(rhDynamicSearch.products, 1, true); // array de produtos, pagina atual,true para adicionar paginacao

                    rhDynamicSearch.listen(); // listener dropdown qtd produtos
                    
                    // carrega filtros
                    rhDynamicSearch.filters.setFilters();
                }
            });
        }
        // carrega todos os produtos do JSON
        else {
            // primeiro carrega o JSON de produtos
            rhDynamicSearch.util.ajax({
                type:'post',
                url: 'https://www.roihero.com.br/JSON/get-content.php',
                data: {'id':rhClientId},
                // função callback da chamada
                callback: function(ajax) {
                    // atribui produtos ao objeto
                    rhDynamicSearch.products = JSON.parse(ajax.responseText);
                    rhDynamicSearch.setProducts(); // ajusta preços, nomes, parcelament etc

                    // PRINTA MENSAGENS DE INFORMAÇÃO
                    console.log('========================= ROI HERO ====================================')
                    console.log('Foram carregados '+rhDynamicSearch.products.length+' produtos no total.');

                    // ORDENA DE ACORDO COM A BUSCA
                    rhDynamicSearch.products = rhDynamicSearch.util.sort(rhDynamicSearch.products); // sobreescreve os produtos pelos resultados

                    // PRINTA MENSAGENS DE INFORMAÇÃO
                    console.log('O termo pesquisado foi "'+rhDynamicSearch.util.getSearchTerm()+'".')
                    console.log('Foram encontrados '+rhDynamicSearch.products.length+' resultados.');
                    console.log('========================= ROI HERO ====================================');

                    // backup da lista de produtos, caso precise resetar                
                    rhDynamicSearch.productList = rhDynamicSearch.products;

                    // DEPOIS Q ORDENA, MONTA OS FILTROS
                    let idList = [];
                    let skuList = [];
                    for (var i = 0; i < rhDynamicSearch.products.length; i++) {
                        // enviar requisiçoes de 50 em 50
                        if (i%50 === 0 && i > 0) {
                            rhDynamicSearch.vtex.getProdById( idList , rhDynamicSearch.filters.arrangeVtexFilters );
                            rhDynamicSearch.vtex.getPriceUsingSalesChannel( skuList , rhDynamicSearch.updatePrices );
                            skuList = [];
                            idList = [];
                        }

                        // adiciona sku na array
                        skuList.push( rhDynamicSearch.products[i].sku[0] );

                        // adiciona id na array
                        idList.push( rhDynamicSearch.products[i].id );

                        // checa se está no final - caso n haja 50 produtos restantes, é necessário
                        if (i + 1 >= rhDynamicSearch.products.length) {
                            rhDynamicSearch.vtex.getProdById( idList , rhDynamicSearch.filters.arrangeVtexFilters );
                            rhDynamicSearch.vtex.getPriceUsingSalesChannel( skuList , rhDynamicSearch.updatePrices );
                        }
                    }

                    console.log(rhDynamicSearch.products);
                    // popula HTML da prateleira com produtos
                    rhDynamicSearch.shelf.populate(rhDynamicSearch.products, 1, true); // array de produtos, pagina atual,true para adicionar paginacao

                    rhDynamicSearch.listen(); // listener dropdown qtd produtos

                    // carrega filtros
                    rhDynamicSearch.filters.setFilters();
                }
            });
        }
        
    };
 
    // carrega APP
    rhDynamicSearch.init();

}(window,document,rhDynamicSearch));
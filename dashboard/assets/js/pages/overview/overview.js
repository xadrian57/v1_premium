/*
--  Autor: Eliabe
--  Data: 09/2017
--  Update: 09/02/2018
--  Desc: Script que retorna todos os dados do overview em um JSON
--
*/

'use strict';
window['carregaDados'] = function(d){
    var inicio = d.split(' - ')[0];
    var fim = d.split(' - ')[1];
    console.log(d);
	$.ajax({
		type: 'post',
		url: 'resource/resource_overview.php',
		data: {
            'id':idCli,
            'begin':inicio,
            'end':fim,
            'beginGrafico': dataInicioGrafico,
            'endGrafico':dataFimGrafico,
        },
		success: function(response){
            console.log('RESPOSTA DOS DADOS EM TEXTO:');
            console.log(response);
            // dados
			window['dados'] = (JSON.parse(response));
            // produtos mais vendidos
            window['nomeProd'] = dados.nomeProd;
            window['categoriaProd'] = dados.categoriaProd;
            window['vendasProd'] = dados.vendasProd;
            window['precoProd'] = dados.preco;
            window['faturaProd'] = dados.faturaProd;
            window['cliquesProd'] = dados.cliquesProd;
            window['conversaoProd'] = dados.conversaoProd;
            window['fotoProd'] = dados.fotoProd;
            // graficos
            window['graficoConversaoRH'] = dados.graficoConversaoRH;
            window['graficoConversaoTotal'] = dados.graficoConversaoTotal;
            window['graficoConversaoLoja'] = dados.graficoConversaoLoja;
            carregaOverview();
		}
	});

    // funcao q pega o dia da semana no mes
    function dayOfWeek(year,month,day) {
        return new Date(year,month,day).getDay();
    }
    
    // funcao q retorna quantos dias tem no mes
    function daysInMonth(month,year) {
        return new Date(year, month, 0).getDate();
    }

    // funcao q retorna o mes de acordo com a data
    function getDate(string) {
        var dia = parseInt(string.split('-')[0]);
        var mes = parseInt(string.split('-')[1]);
        var ano = parseInt(string.split('-')[2]);

        return {'mes':mes,'ano':ano,'dia':dia};
    }

    function format(n){
        if (isNaN(n)) return false;
        var format = new Intl.NumberFormat('pt-BR');
        return format.format(n);
    }
    
    function carregaOverview(){
        if (idPlan === '0'){
            var 
            aviso = '',
            style = document.getElementsByTagName('style')[0],
            parent = style.parentNode,
            blur = document.createElement('style'),
            msg1 = 'Estamos aprimorando nossos relatórios para que você possa analisar melhor os seus resultados com a Roi Hero!',
            msg2 = 'Enquanto isso, você pode ver esses resultados em seu Google Analytics.\n\nPeça a ajuda da nossa equipe de suporte via chat aqui pelo painel mesmo que iremos te ensinar como ver os resultados agora!';

            aviso = '<div class="trial-warning-relatorios">';
            aviso+= '<div class="warning-aviso">'+msg1+'</div>';   
            aviso+= '<div class="warning-aviso">'+msg2+'</div>'; 
            aviso+= '</div">';

            blur.innerHTML = '.content-body > div {position:relative;filter: blur(6px);} .content-body {position:relative;}';

            parent.insertBefore(blur,style);

            document.getElementsByClassName('content-body')[0].innerHTML = '<div>'+document.getElementsByClassName('content-body')[0].innerHTML+'</div>';
            document.getElementsByClassName('content-body')[0].innerHTML+=aviso;

        }


        Object.keys(dados).forEach(function(key) {
            dados[key] = format(dados[key]);            
        });

        // FATURAMENTO
        $('#faturadoRH').html('R$ ' + (dados.faturadoRH || 0));  
        $('#faturadoPart').html((dados.faturadoPart || 0) + '%');   
        $('#aumentoFaturado').html((dados.aumentoFaturado || 0) + '%');

        // CONVERSAO        
        $('#conversaoRH').html((dados.conversaoRH || 0) + '%');
        $('#aumentoConversao').html((dados.aumentoConversao || 0)+ '%');
        $('#qtsXmelhor').html((dados.qtsXmelhor || 0) + '%');

        // TRANSACOES
        $('#transacoesRH').html(dados.transacoesRH || 0);
        $('#participacaoTrans').html((dados.participacaoTrans || 0)+ '%');
        $('#aumentoTransacao').html((dados.aumentoTransacao || 0)+ '%');

        // IMPRESSOES, CLIQUES E TICKET MEDIO
        $('#impressoesRH').html(dados.impressoesRH || 0);
        $('#cliquesRH').html(dados.cliquesRH || 0);
        $('#taxaClique').html((dados.taxaClique || 0)+ '%');
        $('#ticketMedioRH').html('R$ ' + (dados.ticketMedioRH || 0));

        // GRAFICO FUNIL    
        $('#funilTransacoesRH span').html(dados.transacoesRH);
        $('#funilCliquesRH').html(dados.cliquesRH);
        $('#funilCarrinhosRH').html(dados.carrinhosRH);
        $('#funilImpressoesRH').html(dados.impressoesRH);

        // 3 PRODUTOS MAIS VENDIDOS DA LOJA -----------------------------
        if (typeof nomeProd !== 'undefined' && nomeProd.length > 2) {

            $('#top-sellers').show();
            $('#nomeProd1').html(nomeProd[0]);
            $('#nomeProd2').html(nomeProd[1]);
            $('#nomeProd3').html(nomeProd[2]);

            $('#fotoProd1').attr('src',fotoProd[0]);
            $('#fotoProd2').attr('src',fotoProd[1]);
            $('#fotoProd3').attr('src',fotoProd[2]);

            $('#faturado1').html('Vendas: ' + vendasProd[0] + '<br/> Faturado: ' + toReais(faturaProd[0]));
            $('#faturado2').html('Vendas: ' + vendasProd[1] + '<br/> Faturado: ' + toReais(faturaProd[1]));
            $('#faturado3').html('Vendas: ' + vendasProd[2] + '<br/> Faturado: ' + toReais(faturaProd[2]));

            var botoesEstatisticas = document.getElementsByClassName('btn-show-statistics');
            for(var i = 0; i < botoesEstatisticas.length; i++){
                botoesEstatisticas[i].addEventListener('click', function(){
                    var pos = parseInt(this.getAttribute('data-pos'));
                    carregaModal(pos);
                });
            }

            function carregaModal(n){
                $('#eNome').html(nomeProd[n]);
                $('#eCategoria').html(categoriaProd[n]);
                $('#eFaturado').html(toReais(faturaProd[n]));
                $('#eCliques').html(cliquesProd[n]);
                $('#eFaturado').html(toReais(faturaProd[n]));
                $('#eVendas').html(vendasProd[n]);
                $('#eConversao').html(conversaoProd[n] + '%');

                
                $("#modalEstatisticasProduto").modal('show');
            }

        } else {
            //$('#top-sellers').remove();
        }
        // ------------------------------------------------------------//

        // grafico conversao
        // pega ultimos 7 dias
        var dias = ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'];
        var data = new Date();
        var diaMes = data.getDate(); 
        var ano = data.getFullYear();
        var mes = data.getMonth();        
        var diaSemana = data.getDay();

        var diasDaSemana = []; // correspondentes aos últimos 7 dias

        if (diaMes < 8) {
            var anoMesPassado = (data.getMonth() === 0) ? data.getFullYear() - 1 : data.getFullYear(); // ano do mes passado
            var mesPassado = (data.getMonth() === 0) ? 11 : data.getMonth() - 1; // mes passado
            var qtddDiasMesPassado = daysInMonth(mes,anoMesPassado); // esta função não utiliza o index e sim o número real do mês ou seja sempre use o (index + 1) ou o mes atual para poder pegar os dias dos mês anterior
            var diasNoMesPassado = 8-diaMes; // dias q ficaram no mes anterior
            var primeiroDosSete = qtddDiasMesPassado - (diasNoMesPassado - 1);

            // Datas do mês passado
            for (var i = 0; i < diasNoMesPassado; i++) {
                diasDaSemana.push( dias[ dayOfWeek(anoMesPassado, mesPassado, primeiroDosSete) ] + ' ' +primeiroDosSete+ '/' +(parseInt(mesPassado) + 1));
                primeiroDosSete++
            };

            // Datas do mês atual
            for (var i = 1; i < diaMes; i++) {
                diasDaSemana.push( dias[ dayOfWeek(ano,mes,i) ] + ' ' +i+ '/' +(parseInt(mes) + 1 ));
            }

        } else {
            var primeiroDosSete = diaMes - 7;
            for (var i = primeiroDosSete; i < diaMes; i++) {
                diasDaSemana.push( dias[ dayOfWeek(ano,mes,i) ] + ' ' +i+ '/' +(parseInt(mes) + 1 ));
            }
        }

        // Initialize chart
        // ------------------------------
        var myChart = echarts.init(document.getElementById('graficoLinhaConversao'));
        var option = {
            tooltip: {
                trigger: 'axis'
            },
            legend: {
                data:['Conversão da Loja','Conversão da Roi Hero','Conversão da Loja + Roi Hero']
            },
            grid: {
                left: '3%',
                right: '4%',
                bottom: '3%',
                containLabel: true
            },
            toolbox: {
                feature: {
                    saveAsImage: {}
                }
            },
            xAxis: {
                type: 'category',
                boundaryGap: false,     
                data: diasDaSemana
            },
            yAxis: {
                type: 'value'
            },
            series: [
                {
                    name:'Conversão da Loja',
                    type:'line',
                    //stack: '总量',
                    data: graficoConversaoLoja
                },
                {
                    name:'Conversão da Roi Hero',
                    type:'line',
                    //stack: '总量',
                    data: graficoConversaoRH
                },
                {
                    name:'Conversão da Loja + Roi Hero',
                    type:'line',
                    //stack: '总量',
                    data: graficoConversaoTotal
                }
            ],
            color: ['#c9223b', '#22c922', '#f4ad42']

        };

        // Apply options
        // ------------------------------

        myChart.setOption(option);


        // Resize chart
        // ------------------------------

        $(function () {

            // Resize chart on menu width change and window resize
            $(window).on('resize', resize);
            $(".menu-toggle").on('click', resize);

            // Resize function
            function resize() {
                setTimeout(function() {

                    // Resize chart
                    myChart.resize();
                }, 200);
            }
        });
        if (option && typeof option === "object") {
            myChart.setOption(option, true);
        }
        
    }
    
}
$(document).ready(function(){
    carregaDados(dateRange);
});
    
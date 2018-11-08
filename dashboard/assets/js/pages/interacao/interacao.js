/*
--  Autor: Eliabe
--  Data: 09/2017
--  Update: 05/02/2018
--  Desc: Script que retorna todos os dados da pagina de relatorio de interacao em um JSON
--
*/
'use strict';

/*=============================================
			INFORMACOES E DADOS
=============================================*/
window['carregaDados'] = function(d){
    var inicio = d.split(' - ')[0];
    var fim = d.split(' - ')[1];
    $.ajax({
        type: 'post',
        url: 'resource/resource_interacao.php',
        data: {
            'id':idCli,
            'begin':inicio,
            'end':fim,
            'beginGrafico': dataInicioGrafico,
            'endGrafico':dataFimGrafico},
        success: function(response){
            console.log('RESPOSTA DOS DADOS EM TEXTO:');
            console.log(response);                
            window['dados'] = JSON.parse(response);
            carregaInformacoes(dados);
        }
    });
    
    // funcao q retorna o mes de acordo com a data
    function getDate(string) {
        var dia = parseInt(string.split('-')[0]);
        var mes = parseInt(string.split('-')[1]);
        var ano = parseInt(string.split('-')[2]);
        
        if(dia < 10) {
        	dia = "0" + dia;
        }
        
        if(mes < 10) {
        	mes = "0" + mes;
        }

        return {'mes':mes,'ano':ano,'dia':dia};
    }

    // funcao q pega o dia da semana no mes
    function dayOfWeek(year,month,day) {
        return new Date(year,month,day).getDay();
    }

    function toReais(value, noSigla) {

        var
            pattern_1 = /\.?(\d{1,2})$/g,
            pattern_2 = /(\d)(?=(\d{3})+(?!\d))/g;
        
        if (value) {
            
            if (typeof value === 'string') {

                value = parseFloat(value);
            }

            value = value.toFixed(2);
            value = value.replace(pattern_1, ',$1');
            value = value.replace(pattern_2, '$1.');

        } else {

            value = '0,00';
        }

        return (!noSigla ? 'R$ ' : '') + value;
    }

    function getInterval() {
        var dataInicio = getDate(dataComeco);
        var dataFinal = getDate(dataFim);
        var ano = dataInicio.ano;

        var diaComeco = dataInicio.dia;
        var diaFim = dataFinal.dia;

        var mesComeco = dataInicio.mes;
        var mesFim = dataFinal.mes;

        var comeco = new Date (dataInicio.ano,dataInicio.mes - 1, dataInicio.dia);
        var fim = new Date (dataFinal.ano,dataFinal.mes - 1, dataFinal.dia);;

        var intervalo =Math.abs(Math.floor((comeco  - fim ) / 86400000));

        return intervalo;
    }


    /*=============================================
            CARREGA INFORMACOES E DADOS
    =============================================*/
    function carregaInformacoes(dados){
        /*=============================================
                FATURAMENTO DOS BLOCOS DA ROI HERO
        =============================================*/
        $('#faturadoRH').html(toReais(dados.principal.faturadoRH));
        $('#participacaoFaturado').html(dados.principal.participacaoFaturado + '%');        
        $('#aumentoFaturado').html(dados.principal.aumentoFaturado + '%');

        // grafico linha
        // $(window).on("load", function(){
        //     Morris.Area({
        //         element: 'graficoVendasOverview',
        //         data: [
        //         {y: '1', a: 14, },
        //         {y: '2', a: 12 }, 
        //         {y: '3', a: 4 }, 
        //         {y: '4', a: 9 }, 
        //         {y: '5', a: 3 }, 
        //         {y: '6', a: 6 }, 
        //         {y: '7', a: 11 }, 
        //         {y: '8', a: 10 }, 
        //         {y: '9', a: 13 }, 
        //         {y: '10', a: 9 }, 
        //         {y: '11', a: 14 },
        //         {y: '12', a: 11 }, 
        //         {y: '13', a: 16 }, 
        //         {y: '14', a: 20 }, 
        //         {y: '15', a: 15 }]
        //         xkey: 'y',
        //         ykeys: ['a'],
        //         labels: ['Likes'],
        //         axes: false,
        //         grid: false,
        //         behaveLikeLine: true,
        //         ymax: 20,
        //         resize: true,
        //         pointSize: 0,
        //         smooth: true,
        //         numLines: 6,
        //         lineWidth: 2,
        //         fillOpacity: 0.1,
        //         lineColors: ['#43A047'],
        //         hideHover: true,
        //         hoverCallback: function (index, options, content, row) {
        //             return "";
        //         }
        //     });
        // });

        $('#transacoesRH').html(dados.principal.transacoesRH);
        $('#conversaoRH').html(dados.principal.conversaoRH + '%');

        
        /*====================================================================
             PARTICIPAÇÃO INDIVIDUAL DAS RECOMENDAÇÕES NO FATURAMENTO TOTAL
        =====================================================================*/     
        // grafico pizza
        $(document).ready(function(){
            // Initialize chart
            // ------------------------------
            var myChart = echarts.init(document.getElementById('graficoPizzaParticipacaoBlocos'));

            // dados
            var data = [];
            var sub = [];
            var qtdWidgets = dados.graficoWidgets.faturado.length;
            if (dados.graficoWidgets.faturado.length === 0 || dados.graficoWidgets.faturado.length === undefined){
                data = [100];
                sub = ['Dados sendo coletados'];
            } else {
                for (var i = 0; i < qtdWidgets; i++){ 
                    data.push({
                        value: parseFloat(dados.graficoWidgets.faturado[i]).toFixed(2), 
                        name: dados.graficoWidgets.nomeWid[i]
                    });

                    sub.push(dados.graficoWidgets.nomeWid[i]);
                }
            }

            console.log('data -> ', data);

            // Chart Options
            // ------------------------------
            var chartOptions = {
                // Add tooltip
                tooltip: {
                    trigger: 'item',
                    formatter: "{a} <br/>{b}: R$ {c} ({d}%)"
                },
                // Add legend
                legend: {
                    x: 'left',
                    y: 'top',
                    orient: 'vertical',
                    data: sub
                },
                color: ['#00A5A8', '#FF7D4D', '#FF4558','#626E82', '#16D39A'],
                // Display toolbox
                toolbox: {
                    show: false,
                    orient: 'vertical',
                    feature: {
                        mark: {
                            show: true,
                            title: {
                                mark: 'Markline switch',
                                markUndo: 'Undo markline',
                                markClear: 'Clear markline'
                            }
                        },
                        dataView: {
                            show: true,
                            readOnly: false,
                            title: 'View data',
                            lang: ['View chart data', 'Close', 'Update']
                        },
                        magicType: {
                            show: true,
                            title: {
                                pie: 'Switch to pies',
                                funnel: 'Switch to funnel',
                            },
                            type: ['pie', 'funnel']
                        },
                        restore: {
                            show: true,
                            title: 'Restore'
                        },
                        saveAsImage: {
                            show: true,
                            title: 'Same as image',
                            lang: ['Save']
                        }
                    }
                },
                // Enable drag recalculate
                calculable: true,
                // Add series
                series: [
                    {
                        name: 'Participação no Faturamento',
                        type: 'pie',
                        radius: ['15%', '73%'],
                        center: ['50%', '57%'],
                        roseType: 'area',
                        // Funnel
                        width: '40%',
                        height: '78%',
                        x: '30%',
                        y: '17.5%',
                        max: 450,
                        sort: 'ascending',
                        data: data
                    }
                ]
            };

            // Apply options
            // ------------------------------
            myChart.setOption(chartOptions);


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
        });

        /*=============================================
                CONVERSÃO NOS ÚLTIMOS 7 DIAS
        =============================================*/
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
            var qtddDiasMesPassado = rhDash.data.daysInMonth(mesPassado,anoMesPassado);
            var diasNoMesPassado = 8-diaMes; // dias q ficaram no mes anterior
            var mesPassado = data.getMonth() - 1;
            var primeiroDosSete = qtddDiasMesPassado - diasNoMesPassado;

            for (var i = 0; i < diasNoMesPassado; i++) {
                diasDaSemana.push( primeiroDosSete+ '/' +(parseInt(mesPassado) + 1));
                primeiroDosSete++
            };

            for (var i = 1; i < diaMes; i++) {
                diasDaSemana.push( data.getDate()+ '/' +(parseInt(data.getMonth()) + 1));
            }
        } else {
            var primeiroDosSete = diaMes - 7;
            for (var i = primeiroDosSete; i < diaMes; i++) {
                diasDaSemana.push( i+ '/' +(parseInt(mes) + 1 ));
            }
        }

        //dados.graficoFaturamento.faturadoRH = dados.graficoFaturamento.faturadoRH.slice(1);

        // checa se veio vazio
        if (typeof dados.graficoFaturamento.faturadoRH === 'undefined'){
            dados.graficoFaturamento.faturadoRH = [0,0,0,0,0,0,0];
        } 
        else if (dados.graficoFaturamento.faturadoRH.length < 1){
            dados.graficoFaturamento.faturadoRH = [0,0,0,0,0,0,0];
        }

        $(document).ready(function(){
            var graficoLinhaTicket7dias = echarts.init(document.getElementById('graficoLinhaTicket7dias'));
            var option = {
                tooltip: {
                    trigger: 'axis'
                },
                legend: {
                    data:['Conversão']
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
                        name:'Conversão',
                        type:'line',
                        //stack: '总量',
                        data:dados.graficoFaturamento.faturadoRH
                    },
                ],
                color: ['#2196F3']

            };

            // Apply options
            // ------------------------------

            graficoLinhaTicket7dias.setOption(option);


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
                        graficoLinhaTicket7dias.resize();
                    }, 200);
                }
            });
            if (option && typeof option === "object") {
                graficoLinhaTicket7dias.setOption(option, true);
            }
        });

        /*====================================================================
                    TICKET MÉDIO, CARRINHOS, CLIQUES E IMPRESSOES
        =====================================================================*/
        $('#ticketMedioRH').html(toReais(dados.principal.ticketMedioRH));
        $('#carrinhosRH').html(dados.principal.carrinhosRH);
        $('#cliquesRH').html(dados.principal.cliquesRH);
        $('#impressoesRH').html(dados.principal.impressoesRH);
        $('#carrinhosRH').html(dados.principal.carrinhosRH);
        $('#carrinhosRH').html(dados.principal.carrinhosRH);



        /*====================================================================
                                RANKING RECOMENDAÇÕES
        =====================================================================*/
        $('#tabelaWidgets tbody').html('');

        for (var i = 0; i < dados.listaWidgets.length; i++) {
            $('#tabelaWidgets tbody').append(
                '<tr>'+
                    '<th scope="row">'+(i+1)+'</th>'+
                    '<td>'+dados.listaWidgets[i].nomeWid+'</td>'+
                    '<td>'+toReais(dados.listaWidgets[i].faturado)+'</td>'+
                    '<td>'+dados.listaWidgets[i].vendas+'</td>'+
                    '<td>'+(dados.listaWidgets[i].conversao ? dados.listaWidgets[i].conversao + '%' : 'Não disponível no momento')+'</td>'+
                '</tr>'
            );
            if (i === 5) { break; }
        };            
    }
}
$(document).ready(function(){
    carregaDados(dateRange);
});

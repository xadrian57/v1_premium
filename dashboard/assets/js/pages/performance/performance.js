/*
--  Autor: Eliabe
--  Data: 09/2017
--  Update: 15/02/2018
--  Desc: Retorna dados do relatório de performance via ajax
--
*/
'use strict';
window['carregaDados'] = function(d){
    var inicio = d.split(' - ')[0];
    var fim = d.split(' - ')[1];
    function getLastSixMonths() {
        var data = new Date();
        var mesAtual = data.getMonth() + 1;
        var anoAtual = data.getFullYear();
        var qtddDiasMesAtual = new Date(anoAtual,(mesAtual+1),0).getDate(); 
        var intervalo = [];

        if (mesAtual < 7) {
            var mesInicio = 11 - (6 - mesAtual) - 1,
                inicio = '01-' + (mesInicio < 10 ? "0" + mesInicio : mesInicio) +'-'+(anoAtual - 1),
                fim = qtddDiasMesAtual+'-'+ (mesAtual < 8 ? "0"  + (mesAtual + 1) : (mesAtual + 1) )+'-'+anoAtual;
            
            intervalo.push({ 
                'inicio' : inicio,
                'fim': fim
            })
        } else {
            var mesInicio = (mesAtual - 6) - 1,
	            inicio = '01-'+ (mesInicio < 10 ? "0" + mesInicio : mesInicio) +'-'+anoAtual,
	            fim = qtddDiasMesAtual+'-'+(mesAtual < 11 ? "0" + (mesAtual-1) : (mesAtual-1))+'-'+anoAtual;
            
            intervalo = { 
                'inicio' : inicio,
                'fim': fim
            }
        }

        return intervalo[0];
    }

    $.ajax({
        type: 'post',
        url: 'resource/resource_performance.php',
        data: {
            'id': idCli, 
            'op': 1,
            'begin': inicio, 
            'end': fim,
            'beginSeisMeses': getLastSixMonths()['inicio'], 
            'endSeisMeses': getLastSixMonths()['fim']
        },
        success: function(response){
            console.log('RESPOSTA DOS DADOS EM TEXTO:');
            console.log((response));
            window['dados'] = JSON.parse(response);
            carregaPerformance(dados);
        }
    });


    // funcao q retorna o mes de acordo com a data
    function getDate(string) {
        var dia = parseInt(string.split('-')[0]);
        var mes = parseInt(string.split('-')[1]);
        var ano = parseInt(string.split('-')[2]);

        return {'mes':mes,'ano':ano,'dia':dia};
    }

    // funcao q retorna quantos dias tem no mes
    function daysInMonth(month,year) {
        return new Date(year, month, 0).getDate();
    }

    function carregaPerformance(dados){
        /*===========================================
                    MAIOR FATURAMENTO
        ============================================*/
        $('#aumentoFaturado').val(Math.floor(parseFloat(dados.dadosPrincipais.aumentoFaturado))).closest('.percent-pizza-chart').attr({
            'data-value' : Math.floor(parseFloat(dados.dadosPrincipais.aumentoFaturado)),
            'data-color' : $('#aumentoFaturado').attr('data-inputcolor')
        });

        $('#faturadoRH_Loja').html( toReais( dados.dadosPrincipais.faturadoRH_Loja ));
        $('#faturadoLoja').html(toReais( dados.dadosPrincipais.faturadoLoja ));
        $('#faturadoRH').html(toReais( dados.dadosPrincipais.faturadoRH ));

        var m = ["Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"];

        var today = new Date();
        var d;
        var posMes = [];
        var meses = [];


        for(var i = 5; i > 0; i -= 1) {
            
            d = new Date(today.getFullYear(), today.getMonth() - i, 1);

            var mes = m[d.getMonth()];
            meses.push(mes);
            posMes.push(d.getMonth());
        }

        d = new Date(today.getFullYear(), today.getMonth(), 1);

        mes = m[d.getMonth()];
        meses.push(mes);
        posMes.push(d.getMonth());

        // Tratamento para clientes com menos de 6 Meses
        
        var
            restante    = 0,
            moneyCharts = [
                'faturaLoja',
                'faturaRH',
                'faturaTotal',
                'ticketLoja',
                'ticketLoja_RH'
            ];

        $.each(dados.faturamentoGrafico, function(index, val) {
        
            if (moneyCharts.indexOf(index) > -1) {
         
                $.each(val, function(i, v) {

                    dados.faturamentoGrafico[index][i] = parseFloat(v);
                });
            }
            
            restante = 6 - val.length;

            if (restante > 0) {

                for (var i = 0; i < restante; i++) {
                    
                    if (moneyCharts.indexOf(index) > -1) {

                        val.unshift(0.00);
                    
                    } else if (index !== 'mes') {

                        val.unshift(0);
                    }
                }
            }
        });

        // grafico faturamento
        $(document).ready(function(){
            // Initialize chart
            // ------------------------------
            var myChart = echarts.init(document.getElementById('graficoBarraFaturamento'));

            // Chart Options
            // ------------------------------
            var chartOptions = {
                // Setup grid
                grid: {
                    x: 40,
                    x2: 40,
                    y: 45,
                    y2: 25
                },

                // Add tooltip
                 tooltip : {
                    trigger: 'axis',
                    axisPointer : {            // Pointer axis, the axis trigger effective
                        type : 'shadow'        // The default is a straight line, optionally: 'line' | 'shadow'
                    },
                    /*formatter: function (params){
                        return params[0].name + '<br/>'+ params[0].seriesName + ' : ' + params[0].value + '<br/>'+ params[1].seriesName + ' : ' + (params[1].value + params[0].value);
                    }*/
                },

                // Add toolbox
                toolbox: {
                    show : false,
                    feature : {
                        mark : {show: true},
                        restore : {show: true},
                        saveAsImage : {show: true}
                    }
                },

                // Add legend
                legend: {
                    selectedMode:false,
                    data:['Com Roi Hero','Sem Roi Hero']
                },

                // Add custom colors
                color: [ '#99B898','#F98E76'],

                // Enable drag recalculate
                calculable: true,

                // Horizontal axis
                xAxis: [{
                    type : 'category',
                    data : meses
                }],

                // Vertical axis
                yAxis: [{
                    type : 'value',
                    boundaryGap: [0, 0.1]
                }],

                // Add series
                series : [
                    {
                        name:'Sem Roi Hero',
                        type:'bar',
                        stack: 'sum',
                        barCategoryGap: '50%',
                        itemStyle: {
                            normal: {
                                color: '#FF7588',
                                barBorderColor: '#FF7588',
                                barBorderWidth: 2,
                                barBorderRadius:0,
                                label : {
                                    show: true, position: 'insideTop'
                                }
                            }
                        },
                        data: dados.faturamentoGrafico.faturaLoja || [0,0,0,0,0,0]
                    },
                    {
                        name:'Acréscimo Com Roi Hero',
                        type:'bar',
                        stack: 'sum',
                        itemStyle: {
                            normal: {
                                color: '#43a047',
                                barBorderColor: '#43a047',
                                barBorderWidth: 2,
                                barBorderRadius:0,
                                label : {
                                    show: true,
                                    position: 'top',
                                    /*formatter: function (params) {
                                        for (var i = 0, l = chartOptions.xAxis[0].data.length; i < l; i++) {
                                            if (chartOptions.xAxis[0].data[i] == params.name) {
                                                return chartOptions.series[0].data[i] + params.value;
                                            }
                                        }
                                    },*/
                                    textStyle: {
                                        color: '#F98E76'
                                    }
                                }
                            }
                        },
                        data: dados.faturamentoGrafico.faturaRH || [0,0,0,0,0,0]
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

    
        /*===========================================
                    AUMENTO NA CONVERSÃO
        ============================================*/
        $('#aumentoConversao').val(Math.floor(parseFloat(dados.dadosPrincipais.aumentoConversao))).closest('.percent-pizza-chart').attr({
            'data-value' : Math.floor(parseFloat(dados.dadosPrincipais.aumentoConversao)),
            'data-color' : $('#aumentoConversao').attr('data-inputcolor')
        });

        $('#conversaoRH').html(dados.dadosPrincipais.conversaoRH + '%');
        $('#conversaoLoja').html(dados.dadosPrincipais.conversaoLoja + '%');
        $('#conversaoTotal').html(dados.dadosPrincipais.conversaoTotal + '%');

        $.each(dados.faturamentoGrafico.faturaLoja, function(index, val) {
        
            dados.faturamentoGrafico.faturaLoja[index] = parseFloat(val);

            console.log('val -> ', val);
        });

        $.each(dados.faturamentoGrafico.faturaTotal, function(index, val) {
   
            console.log('val -> ', val);

            dados.faturamentoGrafico.faturaTotal[index] = parseFloat(val);
        });

        // grafico conversao
        $(document).ready(function(){

            // Initialize chart
            // ------------------------------
            var myChart = echarts.init(document.getElementById('graficoBarraConversao'));
            var option = {
                tooltip: {
                    trigger: 'axis'
                },
                legend: {
                    data:['Sem Roi Hero','Com Roi Hero']
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
                    data: meses
                },
                yAxis: {
                    type: 'value'
                },
                series: [
                    {
                        name:'Sem Roi Hero',
                        type:'line',
                        //stack: '总量',
                        data: dados.faturamentoGrafico.conversaoLoja || [0,0,0,0,0,0]
                    },
                    {
                        name:'Com Roi Hero',
                        type:'line',
                        //stack: '总量',
                        data: dados.faturamentoGrafico.conversaoLoja_RH || [0,0,0,0,0,0]
                    }
                ],
                color: ['#c9223b','#22c922']

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

                    }, 600);
                }
            });
            if (option && typeof option === "object") {
                myChart.setOption(option, true);
            }
        });


        /*===========================================
                   AUMENTO NO TICKET MEDIO
        ============================================*/
        $('#aumentoTicketMedio').val(Math.floor(parseFloat(dados.dadosPrincipais.aumentoTicketMedio))).closest('.percent-pizza-chart').attr({
            'data-value' : Math.floor((dados.dadosPrincipais.aumentoTicketMedio)),
            'data-color' : $('#aumentoTicketMedio').attr('data-inputcolor')
        });

        $('#ticketMedioTotal').html('R$ ' + dados.dadosPrincipais.ticketMedioTotal);
        $('#ticketMedioRH').html('R$ ' + dados.dadosPrincipais.ticketMedioRH);
        $('#conversaoRH').html(dados.dadosPrincipais.conversaoRH + '%');
        $('#ticketMedioLoja').html('R$ ' + dados.dadosPrincipais.ticketMedioLoja);

        // grafico ticket medio
        $(document).ready(function(){
            // Initialize chart
            // ------------------------------
            var myChart = echarts.init(document.getElementById('graficoBarraTicketMedio'));

            // Chart Options
            // ------------------------------
            var chartOptions = {
                tooltip : {
                    trigger: 'axis'
                },
                legend: {
                    data:['Ticket médio com Roi hero','Ticket médio sem Roi hero']
                },
                toolbox: {
                    show : false,
                    feature : {
                        dataView : {show: true, readOnly: false},
                        magicType : {show: true, type: ['line', 'bar']},
                        restore : {show: true},
                        saveAsImage : {show: true}                                            
                    }
                },
                calculable : true,
                xAxis : [
                    {
                        type : 'category',
                        data : meses
                    }
                ],
                yAxis : [
                    {
                        type : 'value'
                    }
                ],                                    
                series : [
                    {
                        name:'Ticket médio sem Roi hero',
                        type:'bar',
                        data: dados.faturamentoGrafico.ticketLoja || [0,0,0,0,0,0],
                        markLine : {
                            data : [
                                {type : 'average', name: 'Média'}
                            ]
                        }
                    },
                    {
                        name:'Ticket médio com Roi hero',
                        type:'bar',
                        data: dados.faturamentoGrafico.ticketLoja_RH || [0,0,0,0,0,0],
                        markLine : {
                            data : [
                                {type : 'average', name : 'Média'}
                            ]
                        }
                    }
                ],
                color : ["#f57c00","#FF7588"]
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
        
        /*===========================================
                        MAIS ENGAJAMENTO
        ============================================*/
        $('#aumentoDuracao').val(Math.floor(parseFloat(dados.dadosPrincipais.aumentoDuracao))).closest('.percent-pizza-chart').attr({
            'data-value' : Math.floor(parseFloat(dados.dadosPrincipais.aumentoDuracao)),
            'data-color' : $('#aumentoDuracao').attr('data-inputcolor')
        });

        if (dados.dadosPrincipais.duracaoLoja != 0 && dados.dadosPrincipais.duracaoLoja != undefined) {
            $('#duracaoTotal').html(dados.dadosPrincipais.duracaoTotal.slice(0,8));
        } else {
            $('#duracaoTotal').html('00:00');
        }

        if (dados.dadosPrincipais.duracaoRH != 0 && dados.dadosPrincipais.duracaoRH != undefined){
            $('#duracaoRH').html(dados.dadosPrincipais.duracaoRH.slice(0,8));
        } else {
            $('#duracaoRH').html('00:00');
        }

        if (dados.dadosPrincipais.duracaoLoja != 0 && dados.dadosPrincipais.duracaoLoja != undefined){
            $('#duracaoLoja').html(dados.dadosPrincipais.duracaoLoja.slice(0,8));
        } else {
            $('#duracaoLoja').html('00:00');
        }

        $('#pag_sessaoRH').html(dados.dadosPrincipais.pag_sessaoRH);
        $('#pag_sessaoLoja').html(dados.dadosPrincipais.pag_sessaoLoja);


        /*===========================================
                   QUEDA NA TAXA DE REJEIÇÃO
        ============================================*/
        $('#rejeicaoDim').val(Math.floor(parseFloat(dados.dadosPrincipais.rejeicaoDim))).closest('.percent-pizza-chart').attr({
            'data-value' : Math.floor(parseFloat(dados.dadosPrincipais.rejeicaoDim)),
            'data-color' : $('#rejeicaoDim').attr('data-inputcolor')
        });

        $('#rejeicaoRH').html(dados.dadosPrincipais.rejeicaoRH);
        $('#rejeicaoTotal').html(dados.dadosPrincipais.rejeicaoTotal);
        $('#rejeicaoLoja').html(dados.dadosPrincipais.rejeicaoLoja);
        $('#rejeicaoRH').html(dados.dadosPrincipais.rejeicaoRH);

        $(document).ready(function(){
            // Initialize chart
            // ------------------------------
            //var myChart = echarts.init(document.getElementById('graficoBarraRejeicao'));

            // Chart Options
            // ------------------------------

            var chartOptions = {
                tooltip: {
                    trigger: 'axis'
                },
                legend: {
                    data:['Taxa de Rejeição da Loja Com a Roi Hero','Taxa de Rejeição da Loja']
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
                    data: meses
                },
                yAxis: {
                    type: 'value'
                },
                series: [
                    {
                        name:'Sem Roi Hero',
                        type:'line',
                        stack: '总量',
                        data: dados.faturamentoGrafico.faturaLoja || [0,0,0,0,0,0]
                    },
                    {
                        name:'Com Roi Hero',
                        type:'line',
                        stack: '总量',
                        data: dados.faturamentoGrafico.faturaTotal || [0,0,0,0,0,0]
                    }
                ],
                color: ['#c9223b','#22c922']

            };

            // Apply options
            // ------------------------------

            //myChart.setOption(chartOptions);


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

            // Trigger Pizza Chats
            // ------------------------------

            $(function () {

                setTimeout(function() {

                    $('.percent-pizza-chart input').each(function(index, el) {

                        $(el).trigger('blur');
                    });
                    
                }, 900);
            });
        });
    }    
}
$(document).ready(function(){
    carregaDados(dateRange);
});
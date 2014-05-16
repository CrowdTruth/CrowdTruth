function pieChartGraph(tooltip, matchStr, groupID, chartName, divName, nrPieCharts) {
    this.url = "/api/analytics/piegraph/?";
    this.matchStr = matchStr;
    this.groupID = groupID;
    this.chartName = chartName;
    var pieChart = "";
    var renderTo = divName + "_div";

    var drawPieChart = function(chartData){
            pieChart =  new Highcharts.Chart({
            chart: {
                renderTo: renderTo,
                type: 'pie',
                width: (($('.maincolumn').width() - 50) / nrPieCharts),
                height: 200,
                events: {
                    load: function () {
                        var chart = this,
                            legend = chart.legend;

                        for (var i = 0, len = legend.allItems.length; i < len; i++) {
                            var item = legend.allItems[i].legendItem;
                            var prefix = "";
                            if (tooltip['prefix'] != '') {
                                prefix = tooltip['prefix'] + ' ';
                            }
                            var suffix = "";
                            if (tooltip['suffix'] != '') {
                                suffix = ' ' + tooltip['suffix'];
                            }
                            var tooltipValue = 'Number of ' + tooltip['label'] + ' ' +
                                prefix + legend.allItems[i].name + suffix + '.Click to select/deselect.';

                            item.attr("data-toggle","tooltip");
                            item.attr("title", tooltipValue);

                        }

                    }
                }
            },
            title: {
                text: chartName
            },
            credits: {
                enabled: false
            },
            tooltip: {
                useHTML : true,
                formatter: function() {
                    var prefix = ""
                    if (tooltip['prefix'] != '') {
                        prefix = tooltip['prefix'] + ' ';
                    }
                    var suffix = ""
                    if (tooltip['suffix'] != '') {
                        suffix = ' ' + tooltip['suffix'];
                    }

                    return '<p><b>' + this.key + ': </b>' +
                        this.percentage.toFixed(2) + ' % </br>(' + tooltip['label'] + ' ' +
                        prefix + this.key + suffix + ': ' + this.y + '/' + this.total + ')' +
                        '</p>';
                },
                followPointer : false,
                hideDelay:10

            },

            plotOptions: {
                pie: {
                    dataLabels: {
                        enabled: false
                    },
                    showInLegend: true
                }
            },
            series: [{
            type: 'pie',
            name: chartName,
            hasCustomFlag: 34,
            data: chartData
        }]
        });
    }
    this.drawPieChart = drawPieChart;

    this.createPieChart = function(matchStr){
        if(matchStr == ""){
            matchStr = this.matchStr;
        }
        $.getJSON(this.url + matchStr + '&group=' + groupID, function(data) {
            chartData = [];

            for (var indexData in data){
                var name = data[indexData]['_id'] + '';
                chartData.push([name , data[indexData]['count']]);
            }
            drawPieChart(chartData);
           /* $('#' + renderTo + ' .highcharts-legend text, .highcharts-legend span').each(function(index, element) {
                $(element).hover(function() {
                    pieChart.tooltip.refresh(pieChart.series[0].data[index]);
                },function() {
                    pieChart.tooltip.hide();
                })
            });*/
        });
    }

    this.update = function(matchStr){
        //todo
    }


}
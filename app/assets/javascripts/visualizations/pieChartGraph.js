function pieChartGraph(tooltip, matchStr, groupID, chartName, divName, nrPieCharts) {
    this.url = "/api/analytics/piegraph/?";
    this.matchStr = matchStr;
    this.groupID = groupID;
    this.chartName = chartName;

    var drawPieChart = function(chartData){


        $('#' + divName + "_div").highcharts({
            chart: {
                type: 'pie',
                width: (($('.maincolumn').width() - 50) / nrPieCharts),
                height: 200
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
                chartData.push([data[indexData]['_id'], data[indexData]['count']]);
            }
            drawPieChart(chartData);
        });
    }

    this.update = function(matchStr){
        //todo
    }


}
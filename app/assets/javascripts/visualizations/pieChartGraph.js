function pieChartGraph(matchStr, groupID, chartName, divName) {
    this.url = "/api/analytics/piegraph/?";
    this.matchStr = matchStr;
    this.groupID = groupID;
    this.chartName = chartName;

    var drawPieChart = function(chartData){


        $('#' + divName + "_div").highcharts({
            chart: {
                type: 'pie',
                width: (($('.maincolumn').width() - 50) / 5),
                height: 200
            },
            title: {
                text: chartName
            },
            tooltip: {
                formatter: function() {

                    return '<table><tr><td><b>' + this.key + ':</b></td>' +
                        '<td style="text-align: right">' + this.percentage.toFixed(2) + '% <br/>(' + this.y + '/' + this.total + ')</td></tr>' +
                        '</table>';
                },
                followPointer : false

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
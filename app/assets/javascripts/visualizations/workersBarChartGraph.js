
function workersBarChartGraph(workerUpdateFunction, jobsUpdateFunction, annotationsUpdateFunction) {
    var barChart = "";
    var unitsWordCountChart = "";
    var selectedUnits = [];
    var projectCriteria = "";
    var matchCriteria = "";
    var info = {};
    var infoFields = [{project:'country', field:'country'}, {project:'platform', field:'softwareAgent_id'}, {project:'platformAgentId', field:'platformAgentId'}];

    var colors = ['#528B8B', '#00688B', '#2F4F4F', '#66CCCC' ,'#00CDCD', '#607B8B' ];

    var chartSeriesOptions = {
        'workers': {
            'spamWorkers': {'color': '#A80000', 'field': 'cache.spammer.count', 'name':'# jobs identified as low quality', 'type': 'column'},
            'avgWorkers': {'color': '#A63800', 'field': '', 'name':'avg # of jobs', 'type': 'spline', 'dashStyle':'shortdot'},
            'workers': {'color': '#BF6030', 'field': '', 'name':'# of jobs (total)', 'type': 'column'}},
        'annotations': {
            'spamAnnotations': {'color': '#60D4AE', 'field': 'cache.annotations.spam', 'name':'# of low quality annotations', 'type': 'column'},
            'annotations': {'color': '#207F60', 'field': 'cache.annotations.nonspam', 'name':'# of annotations', 'type': 'column'},
            'avgAnnotations': {'color': '#00AA72', 'field': '', 'name':'avg # of annotations', 'type': 'spline', 'dashStyle':'shortdot'}},
        'counts': { 'mediaFormats': {'color': '#FF9E00', 'field': 'cache.mediaFormats.count', 'name':'# annotated media formats', 'type': 'spline', 'dashStyle':'LongDash'},
                 'mediaDomains': {'color': '#E00000', 'field': 'cache.mediaDomains.count', 'name':'# annotated media domains', 'type': 'spline','dashStyle':'LongDashDot'},
                 'messages': {'color': '#E00000', 'field': 'cache.sentMessagesToWorkers.count', 'name':'# sent messages', 'type': 'spline','dashStyle':'LongDashDot'}},
        'metrics': {
            'platformTrust': {'color': '#00FA9A', 'field': 'cfWorkerTrust', 'name':'platform worker trust', 'type': 'spline', 'dashStyle':'Solid'},
            'workerAgreement': {'color': '#483D8B', 'field': 'cache.avg_agreement', 'name':'avg worker agreement', 'type': 'spline', 'dashStyle':'Solid'},
            'workerCosine': {'color': '#6B8E23', 'field': 'cache.avg_cosine', 'name':'avg worker cosine', 'type': 'spline', 'dashStyle':'Solid'}}
    }

    var chartGeneralOptions = {
        chart: {
            spacingBottom: 135,
            zoomType: 'x',
            renderTo: 'generalBarChart_div',
            marginBottom: 170,
            width: (($('.maincolumn').width() - 50)),
            height: 500,
            marginTop: 100

        },
        title: {
            text: 'Overview of workers'
        },
        subtitle: {
            text: 'Select workers for more information'
        },
        legend:{
            y: 100
        },
        xAxis: {
            events:{
                setExtremes :function (event) {
                    var min = 0;
                    if (event.min != undefined){
                        min = event.min;
                    }
                    var max = barChart.series[0].data.length
                    if (event.max != undefined){
                        max = event.max;
                    }
                    // chart.yAxis[0].options.tickInterval
                    barChart.xAxis[0].options.tickInterval = Math.ceil( (max-min)/50);
                }
            },
            labels: {
                formatter: function () {
                    var arrayUnit = this.value.split("/");
                    var value = arrayUnit[arrayUnit.length - 1];
                   /* if ($.inArray(this.value, spammers) > -1) {
                        return '<span style="fill: red;">' + value + '</span>';
                    } else {
                        return value;
                    }*/
                    return value;
                },
                rotation: -45,
                align: 'right'
            }
        },
        tooltip: {
            useHTML : true,
            formatter: function() {
                var s = '<div style="white-space:normal;">Worker <b>'+ this.x +'</b><br/>';
                for (var index in infoFields) {
                    var field = infoFields[index]['project'];
                    s +=  field + ' : <b>' + info[this.x][field] + '</b><br/>';
                }

                s += '<table>';

                $.each(this.points, function(i, point) {
                    s += '<tr><td style="color: ' + point.series.color + ';text-align: left">' + point.series.name +':</td>'+
                        '<td style="text-align: right"><b>' + point.y.toFixed(2) + '</b> </td></tr>'
                });

                s += '</table></div>';

                return s;
            },
            shared: true
        },

        plotOptions: {
            series: {
                stacking: 'normal',
                //allowPointSelect: true,
                states: {

                    select: {
                        color: null,
                        borderWidth: 2,
                        borderColor:'Blue'
                    }
                },
                pointPadding: 0.01,
                borderWidth: 0.01,
                //cursor: 'pointer',
                point: {
                    events: {
                        click: function () {
                            for (var iterSeries = 0; iterSeries < barChart.series.length; iterSeries++) {
                                barChart.series[iterSeries].data[this.x].select(null,true)
                            }

                            if($.inArray(this.category, selectedUnits) > -1) {
                                selectedUnits.splice( $.inArray(this.category, selectedUnits), 1 );
                            } else {
                                selectedUnits.push(this.category)
                            }
                            workerUpdateFunction.update(selectedUnits);
                            jobsUpdateFunction.update(selectedUnits);
                            annotationsUpdateFunction.update(selectedUnits);

                        }
                    }
                }
            }
        }
    };




    var computeBarChartProjectData = function(){

        projectCriteria = "";
        for (var key in chartSeriesOptions) {
            var yAxisSeries = chartSeriesOptions[key];
            for (var key in yAxisSeries) {
                if(yAxisSeries[key]['field']!= ""){
                    projectCriteria += "&project[" + key + "]=" + yAxisSeries[key]['field'];
                }
            }
        }
        for (var index in infoFields) {
            projectCriteria += "&project[" + infoFields[index]['project'] + "]=" + infoFields[index]['field'];
        }
        //console.log($scope.projectCriteria);

    }

    var getBarChartData = function(newMatchCriteria, sortCriteria){
        if(sortCriteria == ""){
            sortCriteria = '&sort[created_at]=1';
        }
        if(newMatchCriteria == ""){
            newMatchCriteria = matchCriteria;
        }

        var url = '/api/analytics/workergraph/?' +
            newMatchCriteria +
            sortCriteria +
            projectCriteria;

        $.getJSON(url, function(data) {

            for (var indexData in data['id']) {
                var id = data['id'][indexData];
                info[id] = {};
                for (var index in infoFields) {
                    var field = infoFields[index]['project'];
                    info[id][field] = data[field][indexData];
                }
            }

            chartGeneralOptions['xAxis']['categories'] = data["id"];

            //create the yAxis and series option fields
            chartGeneralOptions.yAxis = [];
            chartGeneralOptions.series = [];


            for (var key in chartSeriesOptions) {
                var yAxisSeriesGroup = chartSeriesOptions[key];
                var color = 'black';
                for (var series in yAxisSeriesGroup) {
                    var max = 0;
                    var newSeries = {
                        name: yAxisSeriesGroup[series]['name'],
                        color: yAxisSeriesGroup[series]['color'],
                        yAxis: chartGeneralOptions.yAxis.length,
                        type: yAxisSeriesGroup[series]['type'],
                        data: data[series],
                        visible: false
                    }
                    if ("tooltip" in yAxisSeriesGroup[series]) {
                        newSeries['tooltip'] = yAxisSeriesGroup[series]['tooltip'];
                    }
                    if(yAxisSeriesGroup[series]['type'] == 'column') {
                        newSeries['stack'] =  key;
                        newSeries['visible'] = true;
                    } else {
                        newSeries['dashStyle'] =  yAxisSeriesGroup[series]['dashStyle'];
                    }
                    var newMax = Math.max.apply(Math, data[series]);
                    if(newMax > max) {
                        max = newMax;
                    }
                    chartGeneralOptions.series.push(newSeries);
                    color = yAxisSeriesGroup[series]['color'];

                }
                var yAxisSettings = {
                    gridLineWidth: 0,
                    labels: {
                        formatter: function () {
                            return this.value;
                        },
                        style: {
                            color: color
                        }
                    },
                    min: 0,
                    max:max,
                    title: {
                        text: key,
                        style: {
                            color: color
                        }
                    },
                    opposite: false
                };
                if(key == 'workers' || key =='jobs' || key == 'annotations')
                    yAxisSettings.opposite = true;
                //console.dir($scope.chartGeneralOptions.yAxis);
                chartGeneralOptions.yAxis.push(yAxisSettings);
                //   console.dir(key);
                //   console.dir($scope.chartGeneralOptions.yAxis);
            }

            // console.dir($scope.chartGeneralOptions);
            chartGeneralOptions.xAxis.tickInterval = Math.ceil( data["id"].length/50);
            barChart = new Highcharts.Chart(chartGeneralOptions);

        });
    }

    var drawBarChart = function(matchStr,sortStr) {
        var url = '/api/analytics/jobtypes';
        //add the job type series to graph
        chartSeriesOptions['jobs']={};
        $.getJSON(url, function(data) {
            $.each(data, function (key,value) {

                chartSeriesOptions['jobs'][data[key]] = {'color': colors[key % colors.length],
                    'field': 'cache.jobTypes.types.' + data[key] + '.count', 'name':data[key] + ' jobs', 'type': 'column'};
            });
            computeBarChartProjectData();
            getBarChartData(matchStr, sortStr);
        });
    }

    this.createBarChart = function(matchStr, sortStr){
        matchCriteria = '';
        drawBarChart(matchStr,"");
    }

}
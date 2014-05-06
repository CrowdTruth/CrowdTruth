
function jobsBarChartGraph(workerUpdateFunction, jobsUpdateFunction, annotationsUpdateFunction) {
    var barChart = "";
    var selectedUnits = [];
    var projectCriteria = "";
    var matchCriteria = "";
    var info = {};
    var infoFields = [{project:'title', field:'hasConfiguration.content.title'}, {project:'platform', field:'softwareAgent_id'}, {project:'type', field:'type'}];

    var colors = ['#528B8B', '#00688B', '#2F4F4F', '#66CCCC' ,'#00CDCD', '#607B8B' ];

    var chartSeriesOptions = {
        'workers': {
            'potentialSpamWorkers': {'color': '#FF0000', 'field': '', 'name':'# of potential low quality workers', 'type': 'column'},
            'spamWorkers': {'color': '#A80000', 'field': 'metrics.spammers.count', 'name':'# of low quality workers', 'type': 'column'},
            'avgWorkers': {'color': '#A63800', 'field': '', 'name':'avg # of workers', 'type': 'spline', 'dashStyle':'shortdot'},
            'workers': {'color': '#BF6030', 'field': 'workersCount', 'name':'# of workers', 'type': 'column'}},
        'units': {
            'filteredUnits': {'color': '#689CD2', 'field': 'metrics.filteredUnits.count', 'name':'# of filtered units', 'type': 'column'},
            'units': {'color': '#26517C', 'field': 'unitsCount', 'name':'# of units', 'type': 'column'},
            'avgUnits': {'color': '#0D58A6', 'field': '', 'name':'avg # of units', 'type': 'spline', 'dashStyle':'shortdot'}},
        'annotations': {
            'spamAnnotations': {'color': '#60D4AE', 'field': 'metrics.filteredUnits.count', 'name':'# of low quality annotations', 'type': 'column'},
            'annotations': {'color': '#207F60', 'field': 'annotationsCount', 'name':'# of annotations', 'type': 'column'},
            'avgAnnotations': {'color': '#00AA72', 'field': '', 'name':'avg # of annotations', 'type': 'spline', 'dashStyle':'shortdot'}},
        'time': { 'time': {'color': '#FF9E00', 'field': 'runningTimeInSeconds', 'name':'job duration', 'type': 'spline', 'dashStyle':'LongDash',  'tooltip': { valueSuffix: ' secs' }}},
        'payment': { 'payment': {'color': '#E00000', 'field': 'projectedCost', 'name':'payment', 'type': 'spline','dashStyle':'LongDashDot', 'tooltip': { valueSuffix: ' cents' }}},
        'metrics': {
            'cosineSimilarity': {'color': '#00CED1', 'field': 'metrics.aggUnits.mean.max_relation_Cos.avg', 'name':'avg unit clarity', 'type': 'spline', 'dashStyle':'Solid'},
            'magnitude': {'color': '#00FA9A', 'field': 'metrics.aggUnits.mean.magnitude.avg', 'name':'avg unit magnitude', 'type': 'spline', 'dashStyle':'Solid'},
            'workerAgreement': {'color': '#483D8B', 'field': 'metrics.aggWorker.mean.avg_worker_agreement.avg', 'name':'avg worker agreement', 'type': 'spline', 'dashStyle':'Solid'},
            'workerCosine': {'color': '#6B8E23', 'field': 'metrics.aggWorker.mean.worker_cosine.avg', 'name':'avg worker cosine', 'type': 'spline', 'dashStyle':'Solid'}}
    }

    var chartGeneralOptions = {
        chart: {
            zoomType: 'x',
            spacingBottom: 70,
            renderTo: 'generalBarChart_div',
            marginBottom: 80,
            width: (($('.maincolumn').width() - 50)),
            height: 400,
            marginTop: 100
        },
        legend:{
            y: 60
        },
        title: {
            text: 'Overview of jobs'
        },
        subtitle: {
            text: 'Select jobs for more information'
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
                    barChart.xAxis[0].options.tickInterval = Math.ceil( (max-min)/20);
                }
            },
            labels: {
                formatter: function() {
                    var arrayJob = this.value.split("/");
                    return arrayJob[arrayJob.length - 1];
                }
            }
        },
        tooltip: {
            useHTML : true,
            formatter: function() {
                var s = '<div style="white-space:normal;">Job <b>'+ this.x +'</b><br/>';
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

        var url = '/api/analytics/jobgraph/?' +
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
                if(key == 'workers' || key =='units' || key == 'annotations')
                    yAxisSettings.opposite = true;
                //console.dir($scope.chartGeneralOptions.yAxis);
                chartGeneralOptions.yAxis.push(yAxisSettings);
                //   console.dir(key);
                //   console.dir($scope.chartGeneralOptions.yAxis);
            }

            // console.dir($scope.chartGeneralOptions);
            barChart = new Highcharts.Chart(chartGeneralOptions);

        });
    }

    var drawBarChart = function(matchStr,sortStr) {
        computeBarChartProjectData();
        getBarChartData(matchStr, sortStr);
    }

    this.createBarChart = function(matchStr, sortStr){
        matchCriteria = 'match[documentType][]=job';
        drawBarChart(matchStr,"");
    }

}
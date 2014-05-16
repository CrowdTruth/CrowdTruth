
function jobsBarChartGraph(workerUpdateFunction, jobsUpdateFunction, annotationsUpdateFunction, getSelection, updateSelection) {
    var barChart = "";
    var selectedUnits = [];
    var projectCriteria = "";
    var matchCriteria = "";
    var info = {};
    var infoFields = [{project:'title', field:'hasConfiguration.content.title'}, {project:'platform', field:'softwareAgent_id'}, {project:'type', field:'type'}];

    var colors = ['#E35467', '#5467E3', '#E4D354' ,'#00CDCD', '#607B8B' ];

    var chartSeriesOptions = {
        'workers': {
            'potentialSpamWorkers': {'color': '#FF0000', 'field': '', 'name':'# of potential low quality workers', 'type': 'column',
                tooltip: "Number of workers marked as high quality on a job, but who were marked as low quality in at least one other job. Click to select/deselect."},
            'spamWorkers': {'color': '#A80000', 'field': 'metrics.spammers.count', 'name':'# of low quality workers', 'type': 'column',
                tooltip: "Number of low quality workers. Click to select/deselect."},
            'avgWorkers': {'color': '#A63800', 'field': '', 'name':'avg # of workers', 'type': 'spline', 'dashStyle':'shortdot',
                tooltip: "Average number of workers. Click to select/deselect."},
            'workers': {'color': '#3D0000', 'field': 'workersCount', 'name':'# of high quality workers', 'type': 'column',
                tooltip: "Number of high quality workers. Click to select/deselect."}},
        'units': {
            'filteredUnits': {'color': '#689CD2', 'field': 'metrics.filteredUnits.count', 'name':'# of unclear units', 'type': 'column',
                tooltip: "Number of unclear units. Unit Clarity: the value is defined as the maximum unit annotation score achieved on any annotation for that unit. High agreement over the annotations is represented by high cosine scores, indicating a clear unit. Click to select/deselect."},
            'units': {'color': '#26517C', 'field': 'unitsCount', 'name':'# of clear units', 'type': 'column',
                tooltip: "Number of clear units. Unit Clarity: the value is defined as the maximum unit annotation score achieved on any annotation for that unit. High agreement over the annotations is represented by high cosine scores, indicating a clear unit. Click to select/deselect."},
            'avgUnits': {'color': '#0D58A6', 'field': '', 'name':'avg # of units', 'type': 'spline', 'dashStyle':'shortdot',
                tooltip: "Average number of units. Click to select/deselect."}},
        'annotations': {
            'spamAnnotations': {'color': '#60D4AE', 'field': 'metrics.filteredAnnotations.count', 'name':'# of low quality annotations', 'type': 'column',
            tooltip: "Number of low quality annotations. Click to select/deselect."},
            'annotations': {'color': '#207F60', 'field': 'annotationsCount', 'name':'# of high quality annotations', 'type': 'column',
            tooltip: "Number of high quality annotations. Click to select/deselect."},
            'avgAnnotations': {'color': '#00AA72', 'field': '', 'name':'avg # of annotations', 'type': 'spline', 'dashStyle':'shortdot',
                tooltip: "Average number of annotations. Click to select/deselect."}},
        'time': { 'time': {'color': '#FF9E00', 'field': 'runningTimeInSeconds', 'name':'job duration', 'type': 'spline', 'dashStyle':'LongDash',
            tooltip: "Amount of time the job has taken so far (in seconds). Click to select/deselect.",
            'tooltipSufix': { valueSuffix: ' secs' }}},
        'payment': { 'payment': {'color': '#E00000', 'field': 'projectedCost', 'name':'payment', 'type': 'spline','dashStyle':'LongDashDot', 'tooltipSufix': { valueSuffix: ' cents' },
            tooltip: "Amount paid so far - [# mTasks Complete Actual] * [Cost/mTask] (in cents). Click to select/deselect."}},
        'metrics': {
            'cosineSimilarity': {'color': '#00CED1', 'field': 'metrics.aggUnits.mean.max_relation_Cos.avg', 'name':'avg unit clarity', 'type': 'spline', 'dashStyle':'Solid',
                tooltip: "CrowdTruth Average Unit Clarity: the value is defined as the maximum unit annotation score achieved on any annotation for that unit. High agreement over the annotations is represented by high cosine scores, indicating a clear unit. Click to select/deselect."},
            'magnitude': {'color': '#00FA9A', 'field': 'metrics.aggUnits.mean.magnitude.avg', 'name':'avg unit magnitude', 'type': 'spline', 'dashStyle':'Solid',
                tooltip: "CrowdTruth Average Unit magnitude score. Click to select/deselect."},
            'workerAgreement': {'color': '#483D8B', 'field': 'metrics.aggWorkers.mean.avg_worker_agreement.avg', 'name':'avg worker agreement', 'type': 'spline', 'dashStyle':'Solid',
                tooltip: "CrowdTruth Average Worker Agreement score. Higher scores indicate better quality workers. Click to select/deselect."},
            'workerCosine': {'color': '#6B8E23', 'field': 'metrics.aggWorkers.mean.worker_cosine.avg', 'name':'avg worker cosine', 'type': 'spline', 'dashStyle':'Solid',
                tooltip: "CrowdTruth Average Cosine Similarity.  Higher Scores indicate better quality workers. Click to select/deselect."}}
    }

    var chartGeneralOptions = {
        credits: {
            enabled: false
        },
        chart: {
            zoomType: 'x',
            spacingBottom: 70,
            renderTo: 'generalBarChart_div',
            marginBottom: 100,
            width: (($('.maincolumn').width() - 50)),
            height: 450,
            marginTop: 70,
            events: {
                load: function () {
                    var chart = this,
                        legend = chart.legend;

                    for (var i = 0, len = legend.allItems.length; i < len; i++) {
                        var item = legend.allItems[i].legendItem;
                        var tooltipValue =  legend.allItems[i].userOptions.tooltipValue;
                        item.attr("data-toggle","tooltip");
                        item.attr("title", tooltipValue);

                    }
                    var selectedUnits = getSelection();

                    for (var idUnitIter in selectedUnits){
                        var categoryName = selectedUnits[idUnitIter];
                        for (var iterData = 0; iterData < chart.series[0].data.length; iterData++) {

                            if (categoryName == chart.series[0].data[iterData]['category']) {
                                for (var iterSeries = 0; iterSeries < chart.series.length; iterSeries++) {

                                    chart.series[iterSeries].data[iterData].select(null,true)

                                }
                            }

                        }
                    }

                    var selectedInfo = {};
                    for (var index in selectedUnits) {
                        selectedInfo[selectedUnits[index]] = {};
                        selectedInfo[selectedUnits[index]]['tooltipLegend'] = {};
                        selectedInfo[selectedUnits[index]]['tooltipLegend']['title'] = info[selectedUnits[index]]['title'];
                        selectedInfo[selectedUnits[index]]['tooltipLegend']['type'] = info[selectedUnits[index]]['type'];
                    }
                    workerUpdateFunction.update(selectedUnits, selectedInfo);
                    jobsUpdateFunction.update(selectedUnits, selectedInfo);
                    annotationsUpdateFunction.update(selectedUnits, selectedInfo);


                }
            }
        },
        legend:{
            y: 65
        },
        title: {
            text: 'Overview of jobs'
        },
        subtitle: {
            text: 'Select area to zoom. To see detailed information select individual jobs'
        },
        xAxis: {
            title :{
                text: 'Job ID'
            },
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
                    barChart.xAxis[0].options.tickInterval = Math.ceil( (max-min)/20);
                },
                afterSetExtremes :function(event){
                    var interval = (event.max - event.min + 1);
                    var title = "";
                    if (interval == barChart.series[0].data.length) {
                        title = 'Overview of ' + interval.toFixed(0) + ' Jobs ';
                    } else {
                        title = 'Overview of ' + interval.toFixed(0) + '/' + barChart.series[0].data.length + ' Jobs';
                    }
                    barChart.setTitle({text: title});
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
            hideDelay:10,
            useHTML : true,
            formatter: function() {
                var arrayID = this.x.split("/");
                var id =  arrayID[arrayID.length - 1];
                var s = '<div style="white-space:normal;"><b>Job </b>'+ id +'<br/>';
                for (var index in infoFields) {
                    var field = infoFields[index]['project'];
                    s +=  '<b>' + field + ' : </b>' + info[this.x][field] + '<br/>';
                }


                var seriesOptions = {};
                $.each(this.points, function(i, point) {
                    var pointValue = point.y
                    if (!(pointValue % 1 === 0)) {
                        pointValue = point.y.toFixed(2);
                    }
                    var line = '<tr><td></td><td style="color: ' + point.series.color + ';text-align: left">   ' + point.series.name +':</td>'+
                        '<td style="text-align: right">' + pointValue + '</td></tr>';
                    if (point.series.yAxis.axisTitle.text in seriesOptions) {
                        seriesOptions[point.series.yAxis.axisTitle.text]['items'].push(line);
                        if(point.series.stackKey != "spline"){
                            seriesOptions[point.series.yAxis.axisTitle.text]['totalValue'] += point.y;}
                    } else {
                        seriesOptions[point.series.yAxis.axisTitle.text] = {};
                        seriesOptions[point.series.yAxis.axisTitle.text]['items'] = [];
                        seriesOptions[point.series.yAxis.axisTitle.text]['items'].push(line);
                        seriesOptions[point.series.yAxis.axisTitle.text]['totalValue'] = -1;
                        if(point.series.stackKey != "spline"){
                            seriesOptions[point.series.yAxis.axisTitle.text]['totalValue'] = point.y;}


                    }
                });

                s += '<table calss="table table-condensed">';
                for (var item in seriesOptions)
                {
                    var totalValue = "";
                    if (seriesOptions[item]['totalValue'] != -1) {
                        totalValue = '<td style="text-align: right">'+ seriesOptions[item]['totalValue'] +' </td>';
                    }
                    s += '<tr><td> </td><td style="text-align: left"><b>' + item +':</b></td>' + totalValue + '</tr>';

                    for(var li in seriesOptions[item]['items']) {
                        s += seriesOptions[item]['items'][li];
                    }

                }
                s += '</table>';

                return s;
            },
            shared: true,
            hideDelay:10
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
                            updateSelection(this.category);

                            var selectedInfo = {};
                            for (var index in selectedUnits) {
                                selectedInfo[selectedUnits[index]] = {};
                                selectedInfo[selectedUnits[index]]['tooltipLegend'] = {};
                                selectedInfo[selectedUnits[index]]['tooltipLegend']['title'] = info[selectedUnits[index]]['title'];
                                selectedInfo[selectedUnits[index]]['tooltipLegend']['type'] = info[selectedUnits[index]]['type'];
                            }
                            workerUpdateFunction.update(selectedUnits, selectedInfo);
                            jobsUpdateFunction.update(selectedUnits, selectedInfo);
                            annotationsUpdateFunction.update(selectedUnits, selectedInfo);

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
            var subTitle = "Overview of Jobs";
            var selectionOptions = "";
            for (var option in data['query']) {
                //default query
                if(option != 'documentType') {
                    var columnName = $( 'th[data-query-key*="' + option + '"]').html();
                    selectionOptions += columnName + " ";
                    var connectionStr = " and ";
                    for (var key in data['query'][option]) {
                        if (key == 'like'){
                            selectionOptions += key + ' "' + data['query'][option][key] + '"' + connectionStr;
                        }
                        else {
                            selectionOptions += key + " " + data['query'][option][key] + connectionStr;
                        }
                    }

                    selectionOptions = selectionOptions.substring(0, selectionOptions.length - connectionStr.length) +  ",";
                }
            }
            if (!(selectionOptions === "")) {
                subTitle += " having " + selectionOptions.substring(0, selectionOptions.length - 1);
            }

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
                        tooltipValue : yAxisSeriesGroup[series]['tooltip'],
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

            chartGeneralOptions.subtitle.text = subTitle + '<br/>' + 'Select an area to zoom. To see detailed information select individual jobs.From legend select features';
            chartGeneralOptions.title.text = 'Overview of ' + data['id'].length + ' Jobs';
            chartGeneralOptions.plotOptions.series.minPointLength = 2;
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
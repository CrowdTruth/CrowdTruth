
function workersBarChartGraph(workerUpdateFunction, jobsUpdateFunction, annotationsUpdateFunction, getSelection, updateSelection, openModal) {
    var barChart = "";
    var masterGraph = "";
    var unitsWordCountChart = "";
    var selectedUnits = [];
    var projectCriteria = "";
    var matchCriteria = "";
    var info = {};
    var infoFields = [{project:'country', field:'country'}, {project:'platform', field:'softwareAgent_id'}, {project:'platformAgentId', field:'platformAgentId'}];

    var colors = ['#E35467', '#5467E3', '#E4D354' ,'#00CDCD', '#607B8B' ];

    var chartSeriesOptions = {
        '# jobs(by quality)': {
            'spamWorkers': {'color': '#A80000', 'field': 'cache.spammer.count', 'name':'# jobs identified as low quality', 'type': 'column',
                tooltip: "Number of jobs in which the worker's annotations were identified as low quality. Click to select/deselect."},
            'avgWorkers': {'color': '#A63800', 'field': '', 'name':'avg # of jobs', 'type': 'spline', 'dashStyle':'shortdot',
                tooltip: "Average number of jobs in which a worker participated. Click to select/deselect."},
            'workers': {'color': '#3D0000', 'field': '', 'name':'# jobs identified as high quality', 'type': 'column',
                tooltip: "Number of jobs in which the worker's annotations were identified as high quality. Click to select/deselect."}},
        'judgements': {
            'spamJudgements': {'color': '#60D4AE', 'field': 'cache.workerunits.spam', 'name':'# of low quality judgements', 'type': 'column',
                tooltip: "Number of low quality judgements. Click to select/deselect."},
            'judgements': {'color': '#207F60', 'field': 'cache.workerunits.nonspam', 'name':'# of high quality judgements', 'type': 'column',
                tooltip: "Number of high quality judgements. Click to select/deselect."},
            'avgWorkerunits': {'color': '#00AA72', 'field': '', 'name':'avg # of judgements', 'type': 'spline', 'dashStyle':'shortdot',
                tooltip: "Average number of judgements. Click to select/deselect."}},
        'counts': { 'mediaFormats': {'color': '#689CD2', 'field': 'cache.mediaFormats.count', 'name':'# annotated media formats', 'type': 'spline', 'dashStyle':'LongDash',
            tooltip: "Number of annotated media formats of a worker. Click to select/deselect."},
                 'mediaDomains': {'color': '#FF9E00', 'field': 'cache.mediaDomains.count', 'name':'# annotated media domains', 'type': 'spline','dashStyle':'LongDashDot',
                     tooltip: "Number of annotated media domains of a worker. Click to select/deselect."},
                 'mediaTypes': {'color': '#00CED1', 'field': 'cache.mediaTypes.count', 'name':'# annotated media types', 'type': 'spline','dashStyle':'LongDashDot',
                     tooltip: "Number of annotated media types of a worker. Click to select/deselect."},
                 'messages': {'color': '#E00000', 'field': 'messagesRecieved.count', 'name':'# received messages', 'type': 'spline','dashStyle':'LongDashDot',
                     tooltip: "Number of messages received by a worker from the framework. Click to select/deselect."}},
        'metrics': {
            'platformTrust': {'color': '#00FA9A', 'field': 'cfWorkerTrust', 'name':'platform worker trust', 'type': 'spline', 'dashStyle':'Solid',
                tooltip: "Worker Quality score from the platform. Click to select/deselect."},
            'workerAgreement': {'color': '#483D8B', 'field': 'avg_agreement', 'name':'avg worker agreement', 'type': 'spline', 'dashStyle':'Solid',
                tooltip: "CrowdTruth Average Worker Agreement score. Higher scores indicate better quality workers. Click to select/deselect."},
            'workerCosine': {'color': '#6B8E23', 'field': 'avg_cosine', 'name':'avg worker cosine', 'type': 'spline', 'dashStyle':'Solid',
                tooltip: "CrowdTruth Average Cosine Similarity.  Lower Scores indicate better quality workers. Click to select/deselect."}}
    }

    var chartGeneralOptions = {
        chart: {
            zoomType: 'x',
            renderTo: 'generalBarChart_div',
            width: (($('.maincolumn').width() - 50)),
            height: 500,
            marginTop: 70,
            alignTicks: false,
            events: {
                load: function () {
                    var chart = this,
                        legend = chart.legend;

                    selectedUnits = getSelection();

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
                    var buttonLength = this.exportSVGElements.length;
                    if (selectedUnits.length > 0) {
                        this.exportSVGElements[buttonLength - 2].show();
                    } else {
                        this.exportSVGElements[buttonLength - 2].hide();
                    }

                    var selectedInfo = {};
                    for (var index in selectedUnits) {
                        selectedInfo[selectedUnits[index]] = {};
                        selectedInfo[selectedUnits[index]]['tooltipLegend'] = {}
                        selectedInfo[selectedUnits[index]]['tooltipLegend']['Platform'] = info[selectedUnits[index]]['platform'];
                        selectedInfo[selectedUnits[index]]['tooltipChart'] = {};
                        selectedInfo[selectedUnits[index]]['tooltipChart']['worker trust on ' + info[selectedUnits[index]]['platform']] = info[selectedUnits[index]]['platformTrust'];
                        selectedInfo[selectedUnits[index]]['tooltipChart']['avg worker agreement across all jobs'] = info[selectedUnits[index]]['workerAgreement'];
                        selectedInfo[selectedUnits[index]]['tooltipChart']['avg worker cosine across all jobs'] = info[selectedUnits[index]]['workerCosine'];
                    }
                    workerUpdateFunction.update(selectedUnits, selectedInfo);
                    jobsUpdateFunction.update(selectedUnits, selectedInfo);
                    annotationsUpdateFunction.update(selectedUnits, selectedInfo);


                }
            }
        },
        exporting: {
            buttons: {
                resetButton: {
                    text: "Reset selection",
                    theme: {
                        fill: '#2aabd2',
                        style:{
                            color: 'white'
                        }
                    },
                    onclick: function(e) {
                        if (selectedUnits.length == 0) return;
                        var selectedGraph = barChart;

                        for (var iterData = 0; iterData < selectedGraph.series[0].data.length; iterData++) {
                            for (var iterSeries = 0; iterSeries < selectedGraph.series.length; iterSeries++) {
                                selectedGraph.series[iterSeries].data[iterData].select(false,true);
                            }
                        }

                        for (var iterSelection in selectedUnits) {
                            updateSelection(selectedUnits[iterSelection]);
                        }

                        var buttonLength = this.exportSVGElements.length;
                        this.exportSVGElements[buttonLength - 2].hide();

                        selectedUnits = [];
                        var selectedInfo = {};
                        workerUpdateFunction.update(selectedUnits, selectedInfo);
                        jobsUpdateFunction.update(selectedUnits, selectedInfo);
                        annotationsUpdateFunction.update(selectedUnits , selectedInfo);

                    }
                }
            }
        },
        credits: {
            enabled: false
        },
        title: {
            text: 'Overview of workers'
        },
        subtitle: {
            text: 'Select workers for more information'
        },
        legend:{
            enabled: false
        },
        xAxis: {
            title :{
                text: 'Worker ID'
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
                    // chart.yAxis[0].options.tickInterval
                    barChart.xAxis[0].options.tickInterval = Math.ceil( (max-min)/50);
                    masterGraph.xAxis[0].removePlotBand('mask-select');
                    masterGraph.xAxis[0].addPlotBand({
                        id: 'mask-select',
                        from: min,
                        to: max,
                        color: 'rgba(0, 0, 0, 0.2)'
                    });
                },
                afterSetExtremes :function(event){
                    var interval = (event.max - event.min + 1);
                    var title = "";
                    if (interval == barChart.series[0].data.length) {
                        title = 'Overview of ' + interval.toFixed(0) + ' Workers ';
                    } else {
                        title = 'Overview of ' + interval.toFixed(0) + '/' + barChart.series[0].data.length + ' Workers';
                    }
                    barChart.setTitle({text: title});
                }
            },
            labels: {
                formatter: function () {
                    var arrayUnit = this.value.split("/");
                    var value = arrayUnit[arrayUnit.length - 1];
                    return value;
                },
                rotation: -45,
                align: 'right'
            }
        },
        tooltip: {
            hideDelay:10,
            useHTML : true,
            formatter: function() {
                var arrayID = this.x.split("/");
                var id =  arrayID[arrayID.length - 1];
                var s = '<div style="white-space:normal;"><b>Worker </b>'+ id +'<br/>';
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
                    var percentage = point.percentage

                    if (!(percentage % 1 === 0)) {
                        percentage = percentage.toFixed(2);
                    }
                    var line = '<tr><td></td><td style="color: ' + point.series.color + ';text-align: left">   ' + point.series.name +':</td>'+
                        '<td style="text-align: right">' + pointValue ;
                    if(point.series.stackKey != "spline"){
                        line  += ' (' + percentage +' %)</td></tr>';
                    } else {
                        line  += '</td></tr>';
                    }
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
                        contextmenu: function (e) {
                            anchorModal = $('<a class="testModal" id="' + this.category + '"' +
                                'data-modal-query="unit=' + this.category+
                                '" data-api-target="/api/analytics/unit?" ' +
                                'data-target="' + '#modalIndividualWorker' + '" data-toggle="tooltip" data-placement="top" title="" ' +
                                'data-original-title="Click to see the individual worker page">6345558 </a>');
                            //$('body').append(anchorModal);
                            openModal(anchorModal, "#crowdagents_tab");
                        },
                        click: function () {
                            for (var iterSeries = 0; iterSeries < barChart.series.length; iterSeries++) {
                                barChart.series[iterSeries].data[this.x].select(null,true)
                            }

                            if($.inArray(this.category, selectedUnits) > -1) {
                                selectedUnits.splice( $.inArray(this.category, selectedUnits), 1 );
                            } else {
                                selectedUnits.push(this.category)
                            }


                            var buttonLength = barChart.exportSVGElements.length;
                            if(selectedUnits.length == 0) {
                                barChart.exportSVGElements[buttonLength - 2].hide();
                            } else {
                                barChart.exportSVGElements[buttonLength - 2].show();
                            }
                            updateSelection(this.category);

                            var selectedInfo = {};
                            for (var index in selectedUnits) {
                                selectedInfo[selectedUnits[index]] = {};
                                selectedInfo[selectedUnits[index]]['tooltipLegend'] = {}
                                selectedInfo[selectedUnits[index]]['tooltipLegend']['Platform'] = info[selectedUnits[index]]['platform'];
                                selectedInfo[selectedUnits[index]]['tooltipChart'] = {};
                                selectedInfo[selectedUnits[index]]['tooltipChart']['worker trust on ' + info[selectedUnits[index]]['platform']] = info[selectedUnits[index]]['platformTrust'];
                                selectedInfo[selectedUnits[index]]['tooltipChart']['avg worker agreement across all jobs'] = info[selectedUnits[index]]['workerAgreement'];
                                selectedInfo[selectedUnits[index]]['tooltipChart']['avg worker cosine  across all jobs'] = info[selectedUnits[index]]['workerCosine'];
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

    var getLimit = function(value){
        return value;
    }


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
            var subTitle = "Overview of Workers" ;
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
                info[id]['platformTrust'] = data['platformTrust'][indexData];
                info[id]['workerAgreement'] = data['workerAgreement'][indexData];
                info[id]['workerCosine'] = data['workerCosine'][indexData];

            }

            chartGeneralOptions['xAxis']['categories'] = data["id"];

            //create the yAxis and series option fields
            chartGeneralOptions.yAxis = [];
            chartGeneralOptions.series = [];
            var startIndex = Math.ceil(2* data["id"].length/5);
            var stopIndex = Math.ceil(3* data["id"].length/5);
            if (stopIndex - startIndex < 2) {
                startIndex = 0;
                stopIndex = 2;
            }
            if (stopIndex - startIndex > 100){
                stopIndex = startIndex + 100;
            }

            for (var key in chartSeriesOptions) {
                var yAxisSeriesGroup = chartSeriesOptions[key];
                var color = 'black';
                var totalValue = 0;
                var max = 0;
                for (var series in yAxisSeriesGroup) {
                    var newSeries = {
                        name: yAxisSeriesGroup[series]['name'],
                        color: yAxisSeriesGroup[series]['color'],
                        yAxis: chartGeneralOptions.yAxis.length,
                        type: yAxisSeriesGroup[series]['type'],
                        tooltipValue : yAxisSeriesGroup[series]['tooltip'],
                        data: data[series],
                        visible: false
                    }
                    var newMax = Math.max.apply(Math, data[series]);
                    if(newMax > max) {
                        max = newMax;
                    }
                    if ("tooltip" in yAxisSeriesGroup[series]) {
                        newSeries['tooltip'] = yAxisSeriesGroup[series]['tooltip'];
                    }
                    if(yAxisSeriesGroup[series]['type'] == 'column') {
                        newSeries['stack'] =  key;
                        newSeries['visible'] = true;
                        totalValue += newMax;
                    } else {
                        newSeries['dashStyle'] =  yAxisSeriesGroup[series]['dashStyle'];
                    }

                    chartGeneralOptions.series.push(newSeries);
                    color = yAxisSeriesGroup[series]['color'];

                }
                if (key == '# jobs(by type)') color = '#000000';

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
                    max: 0,
                    title: {
                        text: key,
                        style: {
                            color: color
                        }
                    },
                    opposite: false
                };
                if(key == '# jobs(by quality)' || key =='# jobs(by type)' || key == 'judgements') {
                    yAxisSettings.opposite = true;
                    yAxisSettings.max = getLimit(totalValue);

                } else {
                    yAxisSettings.max = getLimit(max);
                }
                if (key == '# jobs(by type)'){
                    //get the maximum for # jobs(by quality)
                    var maxValueBothAxis = chartGeneralOptions.yAxis[0].max;
                    if (totalValue > maxValueBothAxis) {
                        maxValueBothAxis = totalValue;
                    }
                    chartGeneralOptions.yAxis[0].max = maxValueBothAxis;
                    yAxisSettings.max = maxValueBothAxis;
                }

                chartGeneralOptions.yAxis.push(yAxisSettings);
            }

            chartGeneralOptions.subtitle.text = subTitle + '<br/>' + 'Select an area to zoom. To see detailed information select individual workers.Right click for table view.From legend select features';
            chartGeneralOptions.title.text = 'Overview of ' +  data['id'].length  + ' Workers';
            chartGeneralOptions.xAxis.tickInterval = Math.ceil( data["id"].length/50);
            chartGeneralOptions.plotOptions.series.minPointLength = 2;
            barChart = new Highcharts.Chart(chartGeneralOptions);
            masterGraph = createMaster(chartGeneralOptions.series, chartGeneralOptions['xAxis']['categories'], chartGeneralOptions.yAxis, 'generalBarChartMaster_div', barChart, startIndex, stopIndex);
            barChart.xAxis[0].setExtremes(startIndex, stopIndex);
            barChart.showResetZoom();
        });
    }
    // create the master chart
    function createMaster(seriesData, categories, yAxis, divName, chart, startIndex, stopIndex) {
        var series = []
        for (var iterSeries in seriesData) {
            var serie = {
                //type: 'area',
                name: seriesData[iterSeries].name,
                yAxis : seriesData[iterSeries].yAxis,
                /* pointInterval: 24 * 3600 * 1000,
                 pointStart: Date.UTC(2006, 0, 01),*/
                tooltipValue: seriesData[iterSeries].tooltipValue,
                data: seriesData[iterSeries].data,
                color: seriesData[iterSeries].color,
                visible: seriesData[iterSeries].visible
            }
            series.push(serie);
        }

        var masterChart = new Highcharts.Chart({
            chart: {
                borderWidth: 0,
                renderTo: divName,
                //backgroundColor: null,
                alignTicks: false,
                width: (($('.maincolumn').width() - 50)),
                height: 260,

                zoomType: 'x',
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
                    },

                selection: function(event) {

                        if (event.resetSelection) {
                            return false;
                        }
                        var min = event.xAxis[0].min;
                        var max = event.xAxis[0].max;
                        // move the plot bands to reflect the new detail span
                        this.xAxis[0].removePlotBand('mask-select');
                        this.xAxis[0].addPlotBand({
                            id: 'mask-select',
                            from: min,
                            to: max,
                            color: 'rgba(0, 0, 0, 0.2)'
                        });

                        chart.xAxis[0].setExtremes(min, max);
                        if (chart.resetZoomButton == undefined){
                            chart.showResetZoom();
                        }


                        return false;
                    }
                }
            },
            title: {
                text: null
            },
            xAxis: {
                labels: {
                    formatter: function() {
                        var arrayID = this.value.split("/");
                        return arrayID[arrayID.length - 1];
                    },
                    rotation: -45,
                    align: 'right'
                },

                tickInterval: Math.ceil( categories.length/50),
                categories : categories,
                title :{
                    text: 'Worker ID'
                },
                showLastTickLabel: true,
                plotBands: [{
                    id: 'mask-select',
                    from: startIndex,
                    to: stopIndex,
                    color: 'rgba(0, 0, 0, 0.2)'
                }]

            },
            yAxis: yAxis,/*{
             gridLineWidth: 0,
             labels: {
             enabled: false
             },
             title: {
             text: null
             },
             min: 0.6,
             showFirstLabel: false
             },*/
            tooltip: {
                formatter: function() {
                    return false;
                }
            },
            /*legend: {
             enabled: false
             },*/
            credits: {
                enabled: false
            },
            plotOptions: {
                series: {
                    fillColor: {
                        linearGradient: [0, 0, 0, 70],
                        stops: [
                            [0, Highcharts.getOptions().colors[0]],
                            [1, 'rgba(255,255,255,0)']
                        ]
                    },
                    events: {
                        legendItemClick: function () {
                            if(chart.series[this._i].visible) {
                                chart.series[this._i].hide();
                            } else {
                                chart.series[this._i].show();
                            }
                            //return false;
                            // <== returning false will cancel the default action
                        }
                    },
                    lineWidth: 1,
                    marker: {
                        enabled: false
                    },
                    shadow: false,
                    states: {
                        hover: {
                            lineWidth: 1
                        }
                    },
                    enableMouseTracking: false
                }
            },

            series: series,

            exporting: {
                enabled: false
            }

        })
        return masterChart;
        ; // return chart instance
    }

    var drawBarChart = function(matchStr,sortStr) {
        var url = '/api/analytics/jobtypes';
        //add the job type series to graph
        chartSeriesOptions['# jobs(by type)']={};
        $.getJSON(url, function(data) {
            $.each(data, function (key,value) {

                chartSeriesOptions['# jobs(by type)'][data[key]] = {'color': colors[key % colors.length],
                    'field':  'cache.jobTypes.types.' + data[key] + '.count', 'name':'# of ' + data[key] + ' jobs', 'type': 'column'};
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
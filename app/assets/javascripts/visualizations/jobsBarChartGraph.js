
function jobsBarChartGraph(workerUpdateFunction, jobsUpdateFunction, annotationsUpdateFunction, getSelection, updateSelection, openModal) {

    var barChart = "";
    var masterGraph = "";
    var selectedUnits = [];
    var projectCriteria = "";
    var matchCriteria = "";
    var info = {};
    var infoFields = [{project:'title', field:'hasConfiguration.content.title'}, {project:'platform', field:'softwareAgent_id'}, {project:'type', field:'type'}];

    //var colors = ['#E35467', '#5467E3', '#E4D354' ,'#00CDCD', '#607B8B' ];
    var colors = [ '#0D233A','#2F7ED8','#77A1E5' ,'#F28F43', '#A7C96C', '#492970' ];
    var chartSeriesOptions = {
        'workers': {
            'potentialSpamWorkers': {'color': '#FF0000', 'field': '', 'name':'# of inconsistent quality workers', 'type': 'column',
                tooltip: "Number of workers marked as high quality on a job, but who were marked as low quality in at least one other job. Click to select/deselect."},
            'spamWorkers': {'color': '#A80000', 'field': 'metrics.spammers.count', 'name':'# of low quality workers', 'type': 'column',
                tooltip: "Number of low quality workers. Click to select/deselect."},
            'workers': {'color': '#3D0000', 'field': 'workersCount', 'name':'# of high quality workers', 'type': 'column',
                tooltip: "Number of high quality workers. Click to select/deselect."},
            'avgWorkers': {'color': '#A63800', 'field': '', 'name':'avg # of workers', 'type': 'spline', 'dashStyle':'shortdot',
                tooltip: "Average number of workers. Click to select/deselect."}},
        'judgements': {
            'filteredWorkerunits': {'color': '#60D4AE', 'field': 'metrics.filteredWorkerunits.count', 'name':'# of low quality judgements', 'type': 'column',
                tooltip: "Number of low quality judgements. Click to select/deselect."},
            'workerunits': {'color': '#207F60', 'field': 'workerunitsCount', 'name':'# of high quality judgements', 'type': 'column',
                tooltip: "Number of high quality judgements. Click to select/deselect."},
            'avgWorkerunits': {'color': '#00AA72', 'field': '', 'name':'avg # of judgements', 'type': 'spline', 'dashStyle':'shortdot',
                tooltip: "Average number of judgements. Click to select/deselect."}},
        'units': {
            'filteredUnits': {'color': '#689CD2', 'field': 'metrics.filteredUnits.count', 'name':'# of unclear units', 'type': 'column',
                tooltip: "Number of unclear units. Unit Clarity: the value is defined as the maximum unit annotation score achieved on any annotation for that unit. High agreement over the annotations is represented by high cosine scores, indicating a clear unit. Click to select/deselect."},
            'units': {'color': '#26517C', 'field': 'unitsCount', 'name':'# of clear units', 'type': 'column',
                tooltip: "Number of clear units. Unit Clarity: the value is defined as the maximum unit annotation score achieved on any annotation for that unit. High agreement over the annotations is represented by high cosine scores, indicating a clear unit. Click to select/deselect."},
            'avgUnits': {'color': '#0D58A6', 'field': '', 'name':'avg # of units', 'type': 'spline', 'dashStyle':'shortdot',
                tooltip: "Average number of units. Click to select/deselect."}},
        'time': { 'job duration': {'color': '#FF9E00', 'field': 'runningTimeInSeconds', 'name':'job duration', 'type': 'spline', 'dashStyle':'LongDash',
            tooltip: "Amount of time the job has taken so far (in seconds). Click to select/deselect.",
            'tooltipSufix': { valueSuffix: ' secs' }}},
        'payment': { 'payment': {'color': '#E00000', 'field': 'projectedCost', 'name':'payment', 'type': 'spline','dashStyle':'LongDashDot', 'tooltipSufix': { valueSuffix: ' cents' },
            tooltip: "Amount paid so far - [# mTasks Complete Actual] * [Cost/mTask] (in cents). Click to select/deselect."}},
        'metrics': {
            'cosineSimilarity': {'color': '#00CED1', 'field': 'metrics.aggUnits.mean.max_relation_Cos', 'name':'avg unit clarity', 'type': 'spline', 'dashStyle':'Solid',
                tooltip: "CrowdTruth Average Unit Clarity: the value is defined as the maximum unit annotation score achieved on any annotation for that unit. High agreement over the annotations is represented by high cosine scores, indicating a clear unit. Click to select/deselect."},
            'workerAnnUnit': {'color': '#00FA9A', 'field': 'metrics.aggWorkers.mean.ann_per_unit', 'name':'avg # of annotations per unit', 'type': 'spline', 'dashStyle':'Solid',
                tooltip: "Average number of annotations per unit. Click to select/deselect."},
            'workerAnnUnit': {'color': '#00FA9C', 'field': 'metrics.aggWorkers.mean.no_of_units', 'name':'avg # of units per worker', 'type': 'spline', 'dashStyle':'Solid',
                tooltip: "Average number of units annotated by a worker. Click to select/deselect."},
            'workerAgreement': {'color': '#483D8B', 'field': 'metrics.aggWorkers.mean.avg_worker_agreement', 'name':'avg worker agreement', 'type': 'spline', 'dashStyle':'Solid',
                tooltip: "CrowdTruth Average Worker Agreement score. Higher scores indicate better quality workers. Click to select/deselect."},
            'workerCosine': {'color': '#6B8E23', 'field': 'metrics.aggWorkers.mean.worker_cosine', 'name':'avg worker cosine', 'type': 'spline', 'dashStyle':'Solid',
                tooltip: "CrowdTruth Average Cosine Similarity.  Lower Scores indicate better quality workers. Click to select/deselect."}}
    }

    var chartGeneralOptions = {
        credits: {
            enabled: false
        },
        chart: {
            resetZoomButton: {

                theme:{
                    fill: '#2aabd2',
                    style:{
                        color:'white'
                    }
                },
                position:{
                    x: 30,
                    y: -90
                }
            },
            backgroundColor: {
                linearGradient: [0, 0, 500, 500],
                stops: [
                    [0, 'rgb(255, 255, 255)'],
                    [1, 'rgb(240, 240, 255)'],
                ]
            },
            marginLeft: 180,
            marginRight: 180,
            zoomType: 'x',
            alignTicks: false,
            renderTo: 'generalBarChart_div',
            width: (($('.maincolumn').width())-0.05*($('.maincolumn').width())),
            height: 450,
            marginTop: 100,
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
                        selectedInfo[selectedUnits[index]]['tooltipLegend'] = {};
                        selectedInfo[selectedUnits[index]]['tooltipLegend']['Title'] = info[selectedUnits[index]]['title'];
                        selectedInfo[selectedUnits[index]]['tooltipLegend']['Type'] = info[selectedUnits[index]]['type'];
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
                                masterGraph.series[iterSeries].data[iterData].select(false,true);
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
        legend:{
            enabled: false
        },
        title: {
            text: 'Overview of jobs',
            style: {
                fontWeight: 'bold'
            }
        },
        subtitle: {
            text: 'Select area to zoom. Right click for table view. To see detailed information select individual jobs'
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
                        title = 'Overview of ' + interval.toFixed(0) + ' Jobs ';
                    } else {
                        if(barChart.series[0].data.length > 0) {
                            title = 'Overview of ' + interval.toFixed(0) + '/' + barChart.series[0].data.length + ' Jobs';
                        }else {
                            title = 'Overview of ' + interval.toFixed(0) + ' Jobs ';
                        }

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
            positioner: function (labelWidth, labelHeight, point) {
                var tooltipX, tooltipY;
                if (point.plotX + labelWidth > barChart.plotWidth) {
                    tooltipX = point.plotX + barChart.plotLeft - labelWidth - 20;
                } else {
                    tooltipX = point.plotX + barChart.plotLeft + 20;
                }
                tooltipY = point.plotY - labelHeight + barChart.plotTop + 10 ;
                return {
                    x: tooltipX,
                    y: tooltipY
                };
            },
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
                        contextmenu: function (e) {
                            anchorModal = $('<a class="testModal" id="' + this.category + '"' +
                                'data-modal-query="unit=' + this.category+
                                '" data-api-target="/api/analytics/unit?" ' +
                                'data-target="' + '#modalIndividualJob' + '" data-toggle="tooltip" data-placement="top" title="" ' +
                                'data-original-title="Click to see the individual worker page">6345558 </a>');
                            //$('body').append(anchorModal);
                            openModal(anchorModal, "#job_tab");
                        },
                        click: function () {
                            for (var iterSeries = 0; iterSeries < barChart.series.length; iterSeries++) {
                                barChart.series[iterSeries].data[this.x].select(null,true)
                                masterGraph.series[iterSeries].data[this.x].select(null,true)
                            }

                            if($.inArray(this.category, selectedUnits) > -1) {
                                selectedUnits.splice( $.inArray(this.category, selectedUnits), 1 );
                            } else {
                                selectedUnits.push(this.category)
                            }
                            updateSelection(this.category);

                            var buttonLength = barChart.exportSVGElements.length;
                            if(selectedUnits.length == 0) {
                                barChart.exportSVGElements[buttonLength - 2].hide();
                            } else {
                                barChart.exportSVGElements[buttonLength - 2].show();
                            }
                            var selectedInfo = {};
                            for (var index in selectedUnits) {
                                selectedInfo[selectedUnits[index]] = {};
                                selectedInfo[selectedUnits[index]]['tooltipLegend'] = {};
                                selectedInfo[selectedUnits[index]]['tooltipLegend']['Title'] = info[selectedUnits[index]]['title'];
                                selectedInfo[selectedUnits[index]]['tooltipLegend']['Type'] = info[selectedUnits[index]]['type'];
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
            var startIndex = Math.ceil(2* data["id"].length/5);
            var stopIndex = Math.ceil(3* data["id"].length/5);
            if (stopIndex - startIndex < 2) {
                startIndex = 0;
                stopIndex = 2;
            }
            if (stopIndex - startIndex > 100){
                stopIndex = startIndex + 100;
            }

            var offsetRight = 0;
            var offsetLeft = 0;

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
                var yAxisSettings = {
                    //gridLineWidth: 0,
                    showEmpty: false,
                    offset: 0,
                    labels: {
                        formatter: function () {
                            return this.value;
                        },
                        style: {
                            color: color
                        }
                    },
                    gridLineColor:  color,
                    startOnTick: false,
                    endOnTick: false,
                    min: 0,
                  //  max: 0,
                    title: {
                        text: key,
                        style: {
                            color: color
                        }
                    },
                    opposite: false
                };
                if(key == 'workers' || key =='units' || key == 'judgements'){
                    yAxisSettings.opposite = true;
                    yAxisSettings.offset = offsetLeft;
                    offsetLeft += 60;
                  //  yAxisSettings.max = getLimit(totalValue);

                } else {
                    yAxisSettings.offset = offsetRight;
                    offsetRight += 60;
                  //  yAxisSettings.max = getLimit(max);
                }
                if (key == 'judgements'){
                    var maxValueBothAxis = chartGeneralOptions.yAxis[ chartGeneralOptions.yAxis.length - 1].max;
                    if (totalValue > maxValueBothAxis) {
                        maxValueBothAxis = totalValue;
                    }
                 //   chartGeneralOptions.yAxis[ chartGeneralOptions.yAxis.length - 1].max = maxValueBothAxis;
                  //  yAxisSettings.max = maxValueBothAxis;
                }

                chartGeneralOptions.yAxis.push(yAxisSettings);
            }

            chartGeneralOptions.subtitle.text = subTitle + '<br/>' + 'Select an area to zoom. To see detailed information select individual jobs.Right click for table view.From legend select features. Adjust Y-Axis by dragging the labels(double click to return to default).';
            chartGeneralOptions.title.text = 'Overview of ' + data['id'].length + ' Jobs';
            chartGeneralOptions.xAxis.tickInterval = Math.ceil( data["id"].length/20);
            chartGeneralOptions.plotOptions.series.minPointLength = 2;
            // console.dir($scope.chartGeneralOptions);
            barChart = new Highcharts.Chart(chartGeneralOptions);
            masterGraph = createMaster(chartGeneralOptions.series, chartGeneralOptions['xAxis']['categories'], chartGeneralOptions.yAxis, 'generalBarChartMaster_div', barChart, startIndex, stopIndex);
            barChart.xAxis[0].setExtremes(startIndex, stopIndex);
            barChart.showResetZoom();

        });
    }
    // create the master chart
    function createMaster(seriesData, categories, yAxis, divName, chart, startIndex, stopIndex) {
        var series = []
        for (var iterAxis in yAxis) {
            yAxis[iterAxis]['gridLineWidth'] = 0;
        }
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
                backgroundColor: {
                    linearGradient: [0, 0, 500, 500],
                    stops: [
                        [0, 'rgb(255, 255, 255)'],
                        [1, 'rgb(240, 240, 255)']
                    ]
                },
                marginLeft: 180,
                marginRight: 180,
                borderWidth: 0,
                renderTo: divName,
                //backgroundColor: null,
                alignTicks: false,
                width: (($('.maincolumn').width())-0.05*($('.maincolumn').width())),
                height: 160,

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

                tickInterval: Math.ceil( categories.length/20),
                categories : categories,
                title :{
                    text: 'Job ID'
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
                        symbol: 'circle',
                        radius: 0.5
                        //enabled: false
                    },
                    shadow: false,
                    states: {
                        hover: {
                            lineWidth: 1
                        },
                        select: {
                            color: 'Blue',
                            radius: 0.5,
                            lineWidth: 4,
                            borderWidth: 30,
                            borderColor:'Blue'
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
         // return chart instance
    }

    var drawBarChart = function(matchStr,sortStr) {
        computeBarChartProjectData();
        getBarChartData(matchStr, sortStr);
    }

    this.createBarChart = function(matchStr, sortStr){
        matchStr = matchStr + '&';
        if(matchStr.indexOf("orderBy") > -1) {
            var secondHalf = matchStr.substring(matchStr.indexOf("orderBy")+8,matchStr.length);
            var sortCriteria = secondHalf.substring(0, secondHalf.indexOf(']'));
            var sortType = secondHalf.substring(0, secondHalf.indexOf('&')).indexOf('asc');
            if(sortCriteria == "") sortCriteria = 'created_at';
            if(sortType > 0){
                sortStr= '&sort[' + sortCriteria + ']=1';
            } else {
                sortStr= '&sort[' + sortCriteria + ']=-1';
            }
        } else {
            sortStr = '&sort[' + 'created_at' + ']=1'
        }
       // matchCriteria = 'match[type][]=job';
        drawBarChart(matchStr,sortStr);
    }

}

function unitsBarChartGraph(category, workerUpdateFunction, jobsUpdateFunction, annotationsUpdateFunction) {
    var unitsJobChart = "";
    var unitsWordCountChart = "";
    var selectedUnits = [];
    var projectCriteria = "";
    var matchCriteria = "";
    var specificInfo = {};
    var specificFields = {
        '#twrex-structured-sentence_tab':{ data : "words", info:['domain','format', 'relation', 'sentence' ],
            tooltip:"Number of words in the sentence. Click to select/deselect",
        labelsInfo:['domain','format', 'seed relation', 'sentence' ],
        name:'RelEx-structured sentences',
        query : '&project[words]=content.properties.sentenceWordCount' +'&project[domain]=domain' +'&project[format]=format'+
        '&project[sentence]=content.sentence.formatted&project[relation]=content.relation.noPrefix' +
        '&project[id]=_id&push[id]=id&push[domain]=domain&push[format]=format&push[words]=words&push[sentence]=sentence&push[relation]=relation'},
       '#fullvideo_tab':{ data : "keyframes", info:['domain','format', 'title', 'keyframes' ,'description'],
           tooltip:"Number of key frames in video. Click to select/deselect",
           labelsInfo:['domain','format', 'title', 'description'],
        name:'Video',
        query : '&project[keyframes]=keyframes.count' +'&project[domain]=domain' +'&project[format]=format'+
        '&project[title]=content.metadata.title&project[description]=content.metadata.description' +
        '&project[id]=_id&push[id]=id&push[title]=title&push[domain]=domain&push[format]=format&' +
            'push[description]=description&push[keyframes]=keyframes'}
    }

    var colors = ['#E35467', '#5467E3', '#E4D354' ,'#00CDCD', '#607B8B' ];

    var chartSeriesOptions = {
        'workers': {
            'potentialSpamWorkers': {'color': '#FF0000', 'field': 'cache.workers.potentialSpam', 'name':'# of potential low quality workers', 'type': 'column',
            tooltip: "Number of workers whose annotations, on a unit, were marked as low quality in some jobs and high quality in others. Click to select/deselect."},
            'spamWorkers': {'color': '#A80000', 'field': 'cache.workers.spam', 'name':'# of low quality workers', 'type': 'column',
                tooltip: "Number of low quality workers who annotated a unit. Click to select/deselect."},
            'workers': {'color': '#3D0000', 'field': 'cache.workers.nonSpam', 'name':'# of high quality workers', 'type': 'column',
                tooltip:  "Number of high quality workers who annotated a unit. Click to select/deselect."},
            'avgWorkers': {'color': '#A63800', 'field': '', 'name':'avg # of workers', 'type': 'spline', 'dashStyle':'shortdot',
                tooltip: "Average number workers who annotated a unit. Click to select/deselect."}},
        'annotations': {
            'spamAnnotations': {'color': '#60D4AE', 'field': 'cache.annotations.spam', 'name':'# of low quality annotations', 'type': 'column',
                tooltip: "Number of low quality annotations for a unit. Click to select/deselect."},
            'annotations': {'color': '#207F60', 'field': 'cache.annotations.nonSpam', 'name':'# of high quality annotations', 'type': 'column',
                tooltip: "Number of high quality annotations for a unit. Click to select/deselect."},
            'avgAnnotations': {'color': '#00AA72', 'field': '', 'name':'avg # of annotations', 'type': 'spline', 'dashStyle':'shortdot',
                tooltip: "Average number annotations for a unit. Click to select/deselect."}},
        'batches': { 'batches': {'color': '#FF9E00', 'field': 'cache.batches.count', 'name':'# of batches', 'type': 'spline', 'dashStyle':'LongDash',
            tooltip: "Number of batches the sentence was used in. Click to select/deselect."}},
        'metrics': {
            'avg_clarity': {'color': '#6B8E23', 'field': 'avg_clarity', 'name':'avg unit clarity', 'type': 'spline', 'dashStyle':'Solid',
                tooltip: "Average Unit Clarity: the value is defined as the maximum unit annotation score achieved on any annotation for that unit. High agreement over the annotations is represented by high cosine scores, indicating a clear unit. Click to select/deselect."}}
    }

    var chartGeneralOptions = {
        chart: {
            zoomType: 'x',
            alignTicks: false,
            spacingBottom: 70,
            renderTo: 'generalBarChart_div',
            marginBottom: 90,
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

                }
            }
        },
        title: {
            text: 'Overview of units used in jobs'
        },
        legend:{
            y: 70
        },
        subtitle: {
            text: 'Select area to zoom. To see detailed information select individual units'
        },
        credits: {
            enabled: false
        },
        xAxis: {
            title :{
                text: 'Unit ID'
            },
            events:{
                setExtremes :function (event) {
                    var graph = '';
                    if(this.chart.renderTo.id == 'generalBarChart_div' ) {
                        graph = unitsJobChart;
                    } else {
                        graph = unitsWordCountChart;
                    }
                    var min = 0;
                    if (event.min != undefined){
                        min = event.min;
                    }
                    var max = graph.series[0].data.length
                    if (event.max != undefined){
                        max = event.max;
                    }
                   // chart.yAxis[0].options.tickInterval
                    graph.xAxis[0].options.tickInterval = Math.ceil( (max-min)/20);
                },
                afterSetExtremes :function(event){
                    var graph = '';
                    var interval = (event.max - event.min + 1);
                    var title = ""
                    if(this.chart.renderTo.id == 'generalBarChart_div' ) {
                        title = 'Overview of Units used in Jobs ' + '(' + interval.toFixed(0) + ' out of total ' + unitsJobChart.series[0].data.length + ')';
                        unitsJobChart.setTitle({text: title});
                    } else {
                        title = 'Overview of Units not used in Jobs ' + '(' + interval.toFixed(0) + ' out of total ' + unitsWordCountChart.series[0].data.length + ')';
                        unitsWordCountChart.setTitle({text: title});
                    }
                }
            },
            labels: {
                formatter: function() {
                    var arrayID = this.value.split("/");
                    return arrayID[arrayID.length - 1];
                }
            }
        },
        tooltip: {
            hideDelay:10,
            useHTML : true,
            formatter: function() {
                var arrayID = this.x.split("/");
                var id =  arrayID[arrayID.length - 1];
                var s = '<div style="white-space:normal;"><b>' + specificFields[category]['name'] + ' </b>'+ id +'<br/>';
                for ( var indexField in specificFields[category]['info']) {
                    if(indexField == (specificFields[category]['info'].length - 1)) break;

                    var field = specificFields[category]['info'][indexField];
                    if (typeof specificInfo[this.x][field] === 'string') {
                        s +=  '<b>'+ specificFields[category]['labelsInfo'][indexField]  + ' : </b>' + specificInfo[this.x][field] + '<br/>';
                    } else {
                        for(var indexInfo in specificInfo[this.x][field]) {
                            s +=  '<b>' + field + ' (' + indexInfo + ') : </b>' + specificInfo[this.x][field][indexInfo] + '<br/>';
                        }
                    }
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
                    var yAxisValue = '<tr><td> </td><td style="text-align: left"><b>' + item +':</b></td>' + totalValue + '</tr>';

                    if(seriesOptions[item]['items'].length > 1) {
                        s += yAxisValue;
                    }
                    for(var li in seriesOptions[item]['items']) {
                        s += seriesOptions[item]['items'][li];
                    }

                }
                s += '</table>';

                var lastIndex = specificFields[category]['info'].length - 1;
                var field = specificFields[category]['info'][lastIndex];
                if (typeof specificInfo[this.x][field] === 'string') {
                    s +=  '<b>'+ specificFields[category]['labelsInfo'][lastIndex]  + ' : </b>' + specificInfo[this.x][field] + '<br/>';
                } else {
                    for(var indexInfo in specificInfo[this.x][field]) {
                        s +=  '<b>' + field + ' (' + indexInfo + ') : </b>' + specificInfo[this.x][field][indexInfo] + '<br/>';
                    }
                }
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
                            var selectedGraph = unitsWordCountChart;
                            var unSelectedGraph = unitsJobChart;

                            if(this.series.chart.renderTo.id == 'generalBarChart_div' ) {
                                selectedGraph = unitsJobChart;
                                unSelectedGraph = unitsWordCountChart;
                            }

                            for (var iterSeries = 0; iterSeries < selectedGraph.series.length; iterSeries++) {
                                selectedGraph.series[iterSeries].data[this.x].select(null,true)
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

    }

    var getBarChartData = function(newMatchCriteria, sortCriteria){
        chartGeneralOptions.series = [];
        chartGeneralOptions.yAxis = [];
        if(sortCriteria == ""){
            sortCriteria = '&sort[created_at]=1';
        }
        if(newMatchCriteria == ""){
            newMatchCriteria = matchCriteria;
        }


        var url = '/api/analytics/unitgraph/?' + '&match[cache.jobs.count][>]=0' +
                    newMatchCriteria +
                    sortCriteria +
            '&project[sentence]=content.sentence.formatted&project[relation]=content.relation.noPrefix' +
            '&push[words]=words&push[sentence]=sentence&push[relation]=relation' +
            '&project[title]=content.metadata.title&project[description]=content.metadata.description&' +
            '&project[domain]=domain' + '&project[format]=format' +
            '&push[title]=title&push[description]=description&push[format]=format&push[domain]=domain' +
                    projectCriteria;

        $.getJSON(url, function(data) {
            var subTitle = "Overview of " + specificFields[category]['name'];
            var selectionOptions = "";
            for (var option in data['query']) {
                //default query
                if( option != 'documentType') {
                    var columnName = $( 'th[data-query-key*="' + option + '"]').html();
                    selectionOptions += columnName + " ";
                    var connectionStr = " and ";
                    for (var key in data['query'][option]) {
                        if (key == 'like'){
                            selectionOptions += key + ' "' + data['query'][option][key] + '"' + connectionStr;
                            continue;
                        }

                        selectionOptions += key + " " + data['query'][option][key] + connectionStr;
                    }

                    selectionOptions = selectionOptions.substring(0, selectionOptions.length - connectionStr.length) +  ",";
                }
            }
            if (!(selectionOptions === "")) {
                subTitle += " having " + selectionOptions.substring(0, selectionOptions.length - 1);
            }


            chartGeneralOptions['xAxis']['categories'] = data["id"];

            //create the yAxis and series option fields
            chartGeneralOptions.yAxis = [];
            chartGeneralOptions.series = [];


            for (var indexData in data['id']) {
                var id = data['id'][indexData];
                specificInfo[id] = {};
                for ( var indexField in specificFields[category]['info']) {
                    var field = specificFields[category]['info'][indexField];
                    specificInfo[id][field] = data[field][indexData];
                }
            }

            for (var key in chartSeriesOptions) {
                var yAxisSeriesGroup = chartSeriesOptions[key];
                var color = 'black';
                var max = 0;
                for (var series in yAxisSeriesGroup) {
                    var newSeries = {
                        name: yAxisSeriesGroup[series]['name'],
                        color: yAxisSeriesGroup[series]['color'],
                        yAxis: chartGeneralOptions.yAxis.length,
                        type: yAxisSeriesGroup[series]['type'],
                        tooltipValue: yAxisSeriesGroup[series]['tooltip'],
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
                if(key == 'workers' || key =='job types' || key == 'annotations')
                    yAxisSettings.opposite = true;
                chartGeneralOptions.yAxis.push(yAxisSettings);
            }
            console.dir(chartGeneralOptions);
            chartGeneralOptions.xAxis.tickInterval = Math.ceil( data["id"].length/20);
            chartGeneralOptions.chart.renderTo = 'generalBarChart_div';
            chartGeneralOptions.title.text = 'Overview of Units used in Jobs (' + data["id"].length + ' out of total ' + data["id"].length+ ')';
            chartGeneralOptions.subtitle.text = subTitle + '<br/>'+ 'Select an area to zoom. To see detailed information select individual units.From legend select/deselect features.';
            chartGeneralOptions.plotOptions.series.pointPadding = 0.01;
            chartGeneralOptions.plotOptions.series.borderWidth = 0.01;
            chartGeneralOptions.legend.y = 70;
            console.dir(chartGeneralOptions);
            unitsJobChart = new Highcharts.Chart(chartGeneralOptions);
        });
    }
    var drawBarChart = function(matchStr,sortStr) {
        var url = '/api/analytics/jobtypes';
        //add the job type series to graph
        chartSeriesOptions['job types']={};
        $.getJSON(url, function(data) {
            $.each(data, function (key,value) {

                chartSeriesOptions['job types'][data[key]] = {'color': colors[key % colors.length],
                    'field': 'cache.jobs.types.' + data[key] + '.count', 'name':'# of ' + data[key] + ' jobs', 'type': 'column',
                    tooltip: 'Number of ' + data[key]  + ' jobs in which a unit was used. Click to select/deselect.'};
            });
            computeBarChartProjectData();
            getBarChartData(matchStr, sortStr);
        });
    }

    var drawSpecificBarChart = function(newMatchCriteria, sortCriteria){
        var newChartGeneralOptions = chartGeneralOptions;
        if(sortCriteria == ""){
            sortCriteria = '&sort[created_at]=1';
        }
        if(newMatchCriteria == ""){
            newMatchCriteria = matchCriteria;
        }
        //get the word count data
        var url = '/api/analytics/aggregate/?' +
            newMatchCriteria +
            '&match[cache.jobs.count][<]=1' +
            sortCriteria +
            specificFields[category]['query'];

        $.getJSON(url, function(data) {
            var subTitle = "Overview of " + specificFields[category]['name'];
            var selectionOptions = "";
            console.dir(data);
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

            if(data.length == 0)
                return;

            for (var indexData in data['id']) {
                var id = data['id'][indexData];
                specificInfo[id] = {};
                for ( var indexField in specificFields[category]['info']) {
                    var field = specificFields[category]['info'][indexField];
                    specificInfo[id][field] = data[field][indexData];
                }
            }

            newChartGeneralOptions['xAxis']['categories'] = data["id"];

            //create the yAxis and series option fields
            newChartGeneralOptions.yAxis = [];
            newChartGeneralOptions.series = [];
            var newSeries = {
                name: '# of ' + specificFields[category]['data'],
                color: '#6B8E23',
                yAxis: 0,
                type: 'column',
                tooltipValue: specificFields[category]['tooltip'],
                data: data[specificFields[category]['data']],
                visible: true
            };
            newChartGeneralOptions.series.push(newSeries);

            var yAxisSettings = {
                gridLineWidth: 0,
                labels: {
                    formatter: function () {
                        return this.value;
                    },
                    style: {
                        color: '#6B8E23'
                    }
                },
                min: 0,
                title: {
                    text: '# of ' + specificFields[category]['data'],
                    style: {
                        color: '#6B8E23'
                    }
                },
                opposite: false
            };

            newChartGeneralOptions.yAxis.push(yAxisSettings);
            newChartGeneralOptions.xAxis.tickInterval = Math.ceil( data["id"].length/20);
            newChartGeneralOptions.chart.renderTo = 'specificBarChart_div';
            newChartGeneralOptions.title.text = 'Overview of Units not used in Jobs (' + data["id"].length + ' out of total ' + data["id"].length+ ')';
            newChartGeneralOptions.subtitle.text = subTitle + '<br/>'+ 'Select area to zoom. To see detailed information select individual units.From legend select/deselect features';
            newChartGeneralOptions.plotOptions.series.pointPadding = 0;
            newChartGeneralOptions.plotOptions.series.minPointLength = 2;
            newChartGeneralOptions.plotOptions.series.borderWidth = 0;
            newChartGeneralOptions.legend.y = 70;

            unitsWordCountChart = new Highcharts.Chart(newChartGeneralOptions);
        });
    }

    this.createBarChart = function(matchStr){
        matchCriteria = 'match[documentType][]=twrex-structured-sentence';
        drawBarChart(matchStr,"");
        drawSpecificBarChart(matchStr,"");

    }


}
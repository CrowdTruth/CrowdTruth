function unitsWorkerDetails(category, categoryName, openModal) {
    var queryField = 'unit_id';
    if (category == '#job_tab'){
        queryField = 'job_id'
    }
    var urlBase = "/api/analytics/piegraph/?match[documentType][]=annotation&";
    var pieChartOptions = {};
    var workerInfo = {};
    var infoFields = [ {field:'softwareAgent_id', name:'platform'}, {field:'flagged', name:'flagged'} ,{field:'cfWorkerTrust', name:'platform worker trust'},
        {field:'avg_cosine', name:'avg worker cosine'}, {field:'avg_agreement', name:'avg worker agreement'} ];
    var currentSelection = [];
    var currentSelectionInfo = {};
    var spammers = [];
    var seriesBase = [];
    var pieChart = "";
    var barChart = "";

    var callback = function callback($this) {
        var img = $this.renderer.image('/assets/check_mark.png',$this.chartWidth-60,15,19,14);
        img.add();
        img.css({'cursor':'pointer'});
        img.attr({'title':'Pop out chart'});
        img.attr("data-toggle","tooltip");
        img.attr("title", "Click to see results without low quality annotations");
        img.on('click',function(){
            alert("under construction");
            // prcessing after image is clicked
        });
        var img = $this.renderer.image('/assets/cross.png',$this.chartWidth-90,16,19,12);
        img.add();
        img.css({'cursor':'pointer'});
        img.attr({'title':'Pop out chart'});
        img.attr("data-toggle","tooltip");
        img.attr("title", "Click to see results with low quality annotations");
        img.on('click',function(){
            alert("under construction");
            // prcessing after image is clicked
        });
    }
    var drawPieChart = function (platform, spam) {
        pieChart = new Highcharts.Chart({
            chart: {
                renderTo: 'workersPie_div',
                type: 'pie',
                width: (2*(($('.maincolumn').width() - 50)/5)),
                height: 430
            },
            title: {
                text: 'Quality of Workers of the ' + currentSelection.length +  ' selected ' + categoryName + '(s)'
            },
            subtitle: {
                text: 'Click a category to see the distribution of annotations per worker'
            },
            yAxis: {
                title: {
                    text: 'Number of workers per ' + categoryName
                }
            },
            dataLabels: {
                enabled: true
            },
            plotOptions: {
                pie: {

                    shadow: false,

                    allowPointSelect: true,
                    center: ['50%', '50%'],
                    point: {
                        events: {
                            click: function () {
                                searchSet = pieChartOptions[this.options.platform]['all'];

                                if ('spam' in this.options) {
                                    if(this.options.spam == 0) {
                                        searchSet = pieChartOptions[this.options.platform]['spam'];
                                    } else {
                                        if(this.options.spam == 1) {
                                            searchSet = pieChartOptions[this.options.platform]['potential'];
                                        } else {
                                            searchSet = pieChartOptions[this.options.platform]['nonSpam'];
                                        }
                                    }
                                }

                                for (var iterData = 0; iterData < barChart.series[0].data.length; iterData++) {
                                    seriesCategory = barChart.series[0].data[iterData].category;
                                    if($.inArray(seriesCategory, searchSet) > -1 && !this.selected ) {
                                        for (var iterSeries = 0; iterSeries < barChart.series.length; iterSeries++) {
                                            barChart.series[iterSeries].data[iterData].select(true,true);
                                        }

                                    } else {
                                        for (var iterSeries = 0; iterSeries < barChart.series.length; iterSeries++) {
                                            barChart.series[iterSeries].data[iterData].select(false,true);
                                        }
                                    }
                                }

                            }
                        }
                    }
                }
            },
            tooltip: {
                useHTML : true,
                formatter: function() {
                    var seriesValue = this.key;
                    if (this.key == 'potentially low quality') {
                        seriesValue += '(workers identified in both low/high quality categories for the current selection)'
                    }
                    return '<p><b>' + seriesValue + ' </b></br>' + this.series.name + ' : ' +
                        this.percentage.toFixed(2) + ' % ('  + this.y + '/' + this.total + ')' +
                        '</p>';
                },
                followPointer : false,
                hideDelay:10
            },
            credits: {
                enabled: false
            },
            series: [
                {
                    name: '# of workers',
                    data: platform,
                    size: '40%',
                    dataLabels: {
                        formatter: function () {
                            // display only if larger than 1
                            return this.point.name;
                        },
                        color: 'white',
                        distance: -30
                    }


                },
                {
                    name: '# of workers',
                    data: spam,
                    size: '60%',
                    innerSize: '40%',
                    dataLabels: {
                        formatter: function () {
                            // display only if larger than 1
                            return this.point.name;
                        },
                        color: 'black'

                    }

                }
            ]
        });
    }

    var drawBarChart = function (series, categories) {

        barChart = new Highcharts.Chart({
            chart: {
                zoomType: 'x',
                alignTicks: false,
                renderTo: 'workersBar_div',
                type: 'column',
                width: (3*(($('.maincolumn').width() - 50)/5)),
                height: 400,
                events: {
                    load: function () {
                        var chart = this,
                            legend = chart.legend;
                        for (var i = 0, len = legend.allItems.length; i < len; i++) {
                            var item = legend.allItems[i].legendItem;
                            var tooltipValue = "";
                            if (typeof currentSelectionInfo[legend.allItems[i].name]['tooltipLegend'] === 'string') {
                                var tooltipValue = currentSelectionInfo[legend.allItems[i].name]['tooltipLegend'];
                            } else {
                                for( var indexInfoKey in currentSelectionInfo[legend.allItems[i].name]['tooltipLegend']) {
                                    tooltipValue +=  currentSelectionInfo[legend.allItems[i].name]['tooltipLegend'][indexInfoKey] + ' (' + indexInfoKey + ')' + '<br/>';
                                }
                            }

                            item.attr("data-toggle","tooltip");
                            item.attr("title", tooltipValue);

                        }

                    }
                }
            },
            credits: {
                enabled: false
            },
            title: {
                text: 'Annotations of ' + categories.length + ' Worker(s) on ' + currentSelection.length + ' Selected ' +   categoryName + '(s)'
            },
            subtitle: {
                text: 'Select an area to zoom. To see detailed information select individual units.From legend select/deselect features.'
            },
            xAxis: {
                title :{
                    text: 'Worker ID (Red color : Workers identified at least ones on the platform as low quality)'
                },
                categories: categories,
                labels: {
                    formatter: function () {
                        var arrayUnit = this.value.split("/");
                        var value = arrayUnit[arrayUnit.length - 1];
                        if ($.inArray(this.value, spammers) > -1) {
                            return '<span style="fill: red;">' + value + '</span>';
                        } else {
                            return value;
                        }
                    },
                    rotation: -45,
                    align: 'right'
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
                        barChart.xAxis[0].options.tickInterval = Math.ceil( (max-min)/20);
                    },
                    afterSetExtremes :function(event){
                        var graph = '';
                        var interval = (event.max - event.min + 1);
                        if (interval == barChart.series[0].data.length) {
                            title = 'Annotations of ' + interval.toFixed(0) + ' Worker(s) of ' + currentSelection.length + ' Selected ' +   categoryName + '(s)'
                        } else {
                            title = 'Annotations of ' + interval.toFixed(0) + '/' +  barChart.series[0].data.length + ' Worker(s) of ' + currentSelection.length + ' Selected ' +   categoryName + '(s)'
                        }
                        barChart.setTitle({text: title});
                    }
                }

            },
            legend: {
                maxHeight: 100,
                labelFormatter: function() {
                    var arrayName = this.name.split("/");
                    var value = arrayName[arrayName.length - 1];
                    if(arrayName.length > 1) {
                        return  categoryName + ' ' + value  + ' # of mTasks ';
                    } else {
                        return categoryName + ' ' + value;
                    }

                }
            },
            yAxis: [{
                min: 0,
                title: {
                    text: '# micro tasks per worker'
                }
            }, {
                min: 0,
                opposite: true,
                title: {
                    text: 'metrics'
                }
            }],
            tooltip: {

                hideDelay:10,
                useHTML : true,
                formatter: function() {
                    var arrayID = this.x.split("/");
                    var id =  arrayID[arrayID.length - 1];
                    var s = '<div style="white-space:normal;"><b>Worker </b>'+ id +'<br/>';
                    for (var index in infoFields) {
                        var field = infoFields[index]['field'];
                        var pointValue =  workerInfo[this.x][field];
                        if (pointValue != undefined &&!(typeof pointValue === 'string') && !(pointValue % 1 === 0)){
                            pointValue = pointValue.toFixed(2);
                        }
                        s +=  '<b>' + infoFields[index]['name'] + ' : </b>' + pointValue + '<br/>';
                    }


                    var seriesOptions = {};
                    $.each(this.points, function(i, point) {
                        var pointValue = point.y
                        if (pointValue != undefined &&!(typeof pointValue === 'string') && !(pointValue % 1 === 0)){
                            pointValue = point.y.toFixed(2);
                        }
                        var id = point.series.options.categoryID;

                        var name = point.series.name;
                        var arrayName = id.split('/');
                        var shortName = arrayName[arrayName.length - 1];
                        if (point.series.name == id){
                            name =  '# of mTasks';
                        } else {
                            name = name.substr(shortName.length,name.length);
                        }

                        var line = '<tr><td></td><td style="color: ' + point.series.color + ';text-align: left">   ' + name +':</td>'+
                            '<td style="text-align: right">' + pointValue + '</td></tr>';
                        if(!(id in seriesOptions)){
                            seriesOptions[id] = [];
                        }
                        seriesOptions[id].push(line);
                    });

                    s += '<table calss="table table-condensed">';
                    for (var item in seriesOptions)
                    {
                        var arrayName = item.split('/');
                        var id = arrayName[arrayName.length - 1];
                        s += '<tr><td> </td><td style="text-align: left"><b>' + categoryName + ' ' +  id + ':</b></td></tr>';
                        if('tooltipChart' in currentSelectionInfo[item]){
                            for (var tooltipInfo in currentSelectionInfo[item]['tooltipChart']){
                                pointValue = currentSelectionInfo[item]['tooltipChart'][tooltipInfo];
                                if (pointValue != undefined &&!(typeof pointValue === 'string') && !(pointValue % 1 === 0)){
                                    pointValue = pointValue.toFixed(2);
                                }
                                s += '<tr><td></td><td style="text-align: left">   ' + tooltipInfo +':</td>'+
                                    '<td style="text-align: right">' + pointValue + '</td></tr>';
                            }
                        }

                        for(var li in seriesOptions[item]) {
                            s += seriesOptions[item][li];
                        }

                    }
                    s += '</table>';

                    return s;
                },
                shared: true,
                crosshairs: true
            },
            credits: {
                enabled: false
            },
            plotOptions: {
                series: {
                    minPointLength : 2
                },

                column: {

                    stacking: 'normal',
                    states: {

                        select: {
                            color: null,
                            borderWidth:3,
                            borderColor:'Blue'
                        }
                    },

                    point: {
                        events: {
                            click: function () {

                                urlBase = "";
                                for (var indexUnits in currentSelection) {
                                    urlBase += 'match['+ queryField + '][]=' + currentSelection[indexUnits] + '&';
                                }
                                anchorModal = $('<a class="testModal"' +
                                    'data-modal-query="agent=' + this.category + '&' + urlBase + '" data-api-target="/api/analytics/worker?" ' +
                                    'data-target="#modalIndividualWorker" data-toggle="tooltip" data-placement="top" title="" ' +
                                    'data-original-title="Click to see the individual worker page">6345558 </a>');
                                //$('body').append(anchorModal);
                                openModal(anchorModal, "#crowdagents_tab");


                            }
                        }
                    }
                }
            },
            series: series
        },callback);

    }


    var getWorkersData = function (url) {
        //make a check and see which units have workers?
        var colors =  Highcharts.getOptions().colors;
        var categories = [];
        var colorMaps = {};
        var seriesMaps = {};
        var series = seriesBase;

        workersURL = url + 'project[' + queryField + ']=' + queryField +
            '&group=crowdAgent_id&push[' + queryField + ']=' + queryField;
        for (var iterSeries in series) {
            series[iterSeries]['data'] = [];
        }
        //get the list of workers for this units
        $.getJSON(workersURL, function (data) {
            for (var iterData in data) {
                categories.push(data[iterData]['_id']);
                //colorMaps[data[iterData]['_id']] = colors[iterData%colors.length];
                //seriesMaps[data[iterData]['_id']] = series.length;

                for (var iterSeries in series) {
                    var unit_id = series[iterSeries]['name'];
                    var value = 0;
                    for (var iterUnits in data[iterData][queryField]) {
                        if (data[iterData][queryField][iterUnits] == unit_id) {
                            value++;
                        }
                    }

                    series[iterSeries]['data'].push(value);
                }
            }

            for (var iterSeries in series) {
                series[iterSeries]['color'] = Highcharts.Color(colors[iterSeries%(colors.length)]).get();
                series[iterSeries]['type'] = 'column';
                series[iterSeries]['categoryID'] = series[iterSeries]['name'];
                colorMaps[series[iterSeries]['name']] = colors[iterSeries%(colors.length)];

                seriesMaps[series[iterSeries]['name']] = iterSeries;
            }
            //get worker's info
            var urlWorkerInfo = '/api/analytics/metrics/?&&collection=crowdagents&'
            for (var indexUnits in currentSelection) {
                urlWorkerInfo += 'match['+ queryField + '][]=' + currentSelection[indexUnits] + '&';
            }
            urlWorkerInfo += 'match[documentType][]=annotation&project[crowdAgent_id]=crowdAgent_id&push[crowdAgent_id]=crowdAgent_id' +
                '&metrics[]=avg_agreement&metrics[]=avg_cosine'+
                '&metrics[]=flagged&metrics[]=cfWorkerTrust&metrics[]=softwareAgent_id';
            $.getJSON(urlWorkerInfo, function (data) {

                for(var iterData in data) {
                    workerInfo[data[iterData]['_id']] = data[iterData];
                }

                if (queryField == 'job_id') {
                    //get the metrics for jobs
                    var urlJobsInfo =  '/api/v1/?field[documentType]=job&only[]=metrics.workers.withFilter&';
                    for (var indexUnits in currentSelection) {
                        urlJobsInfo += 'field[_id][]=' + currentSelection[indexUnits] + '&';
                    }
                    $.getJSON(urlJobsInfo, function (data) {
                       for (var iterData in data) {
                           if (!('metrics' in data[iterData])) {continue;}
                           var metrics = data[iterData]['metrics']['workers']['withFilter'];
                           var job_id =  data[iterData]['_id'];
                           var arrayID = job_id.split("/");
                           var value = arrayID[arrayID.length - 1];
                           var avg_agreement = {'name': value + " avg agreement", data:[], categoryID:job_id, type: 'spline', color:Highcharts.Color( colorMaps[job_id]).brighten(0.3).get(), yAxis:1,'dashStyle':'shortdot'};
                               //linkedTo: seriesMaps[job_id], yAxis:1};
                           var avg_cosine = {'name': value + " avg cosine", data:[], categoryID:job_id, type: 'spline', color:Highcharts.Color( colorMaps[job_id]).brighten(0.1).get(), yAxis:1,'dashStyle':'LongDash'};
                              // linkedTo: seriesMaps[job_id], yAxis:1};
                           for (var agentIDIter in categories){
                               var agentID = categories[agentIDIter];
                               if ( agentID in metrics) {
                                   avg_agreement['data'].push(metrics[agentID]['avg_worker_agreement']['avg']);
                                   avg_cosine['data'].push(metrics[agentID]['worker_cosine']['avg']);
                               } else{
                                   avg_agreement['data'].push(0);
                                   avg_cosine['data'].push(0);
                               }

                           }
                           var position = 0;
                           for(var iterSeries in series){
                               if(series[iterSeries].name == job_id && series[iterSeries].type == 'column'){
                                   position = iterSeries;
                                   break;
                               }
                           }
                           currentSelectionInfo[value + " avg agreement"] = {}
                           currentSelectionInfo[value + " avg cosine"] = {}
                           /*currentSelectionInfo[value + " avg agreement"]['tooltipLegend'] = {}
                           currentSelectionInfo[value + " avg cosine"]['tooltipLegend'] = {}*/
                           currentSelectionInfo[value + " avg agreement"]['tooltipLegend'] = 'CrowdTruth Average Worker Agreement score.Higher scores indicate better quality workers. Click to select/deselect.'
                           currentSelectionInfo[value + " avg cosine"]['tooltipLegend'] = 'CrowdTruth Average Cosine Similarity.Higher Scores indicate better quality workers. Click to select/deselect.'
                           series.splice(position, 0, avg_agreement, avg_cosine);
                       }
                       drawBarChart(series, categories);
                    });

                } else {
                    drawBarChart(series, categories);
                }


            });



        });
    }

    this.update = function (selectedUnits, selectedInfo) {
        if(selectedUnits.length == 0){
            if ( $('#workersBar_div').highcharts() != undefined ) {
                $('#workersBar_div').highcharts().destroy();
                $('#workersPie_div').highcharts().destroy();
            }

            return;
        }
        currentSelection = selectedUnits;
        currentSelectionInfo = selectedInfo
        seriesBase = [];
        urlBase = "/api/analytics/piegraph/?match[documentType][]=annotation&";
        //create the series data
        for (var indexUnits in selectedUnits) {
            urlBase += 'match['+ queryField + '][]=' + selectedUnits[indexUnits] + '&';
            seriesBase.push({'name': selectedUnits[indexUnits], data: []});
        }

        getWorkersData(urlBase);
        //get the workers grouped by platform and spam, nonspam
        platformURL = urlBase + 'project[crowdAgent_id]=crowdAgent_id&group=softwareAgent_id&addToSet=crowdAgent_id';
        $.getJSON(platformURL, function (data) {
            var platformData = [];
            var spamData = [];
            var requests = [];
            var iterColors = 0;
            var colors = ['#FFC640', '#A69C00'];


            for (var platformIter in data) {
                var platformID = data[platformIter]['_id'];
                platformData.push({name: platformID, y: data[platformIter]['content'].length,
                    color: Highcharts.Color(colors[platformIter]).brighten(0.07).get(),
                    platform: platformID});
                pieChartOptions[platformID] ={};
                pieChartOptions[platformID]['all'] = data[platformIter]['content'];
                //get the spam, nonspam count
                requests.push($.get(urlBase + 'match[softwareAgent_id][]=' + data[platformIter]['_id'] + '&project[crowdAgent_id]=crowdAgent_id&group=spam&addToSet=crowdAgent_id'));

            }
            var defer = $.when.apply($, requests);
            defer.done(function () {

                $.each(arguments, function (index, responseData) {
                    // "responseData" will contain an array of response information for each specific request
                    if ($.isArray(responseData)) {
                        if (responseData[1] == 'success') {
                            responseData = responseData[0];
                        }

                        var commonWorkers = [];
                        responseData[0].content.forEach(function(key) {
                            if (!(-1 === responseData[1].content.indexOf(key))) {
                                commonWorkers.push(key);
                            }
                        }, this);

                        //get common workers
                        for (var iterObj in responseData) {
                            var content = []
                            responseData[iterObj].content.forEach(function(key) {
                                if (-1 === commonWorkers.indexOf(key)) {
                                    content.push(key);
                                }
                            }, this);

                            if (responseData[iterObj]['_id'] === true) {
                                spamData.push({name: 'low quality',
                                    spam: 0, //spammers
                                    y: content.length,
                                    color: Highcharts.Color(colors[index]).brighten(-0.05).get(),
                                    platform: data[index]['_id']});
                                pieChartOptions[data[index]['_id']]['spam'] = content;
                            } else {
                                spamData.push({name: 'high quality',
                                    spam: 3,//no spam
                                    y: content.length,
                                    color: colors[index],
                                    platform: data[index]['_id']});
                                pieChartOptions[data[index]['_id']]['nonSpam'] = content;
                            }
                        }
                        if(commonWorkers.length > 0) {
                            spamData.push({name: 'potentially low quality',
                                spam: 1,//possible spam
                                y: commonWorkers.length,
                                color: Highcharts.Color(colors[index]).brighten(-0.09).get(),
                                platform: data[index]['_id']});
                            pieChartOptions[data[index]['_id']]['potential'] = commonWorkers;
                        }
                    }
                });
                drawPieChart(platformData, spamData);
            });

        });
    }

    this.createUnitsWorkerDetails = function () {
        $.getJSON('/api/analytics/spammers', function (data) {
            spammers = data;
        });
    }

}
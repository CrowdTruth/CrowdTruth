function unitsJobDetails(category , categoryName, openModal) {
    var queryField = 'unit_id';
    var infoFields = [ {field:'type', name:'type'}, {field:'softwareAgent_id', name:'platform'} ];
    var querySettings = {};
    if (category == '#crowdagents_tab'){
        queryField = 'crowdAgent_id';
        infoFields.push({field:'avg_worker_agreement', name:'avg worker agreement'});
        infoFields.push({field:'worker_cosine', name:'avg worker cosine'});
        querySettings = {'metricCateg':'workers',metricFilter:'withFilter', aggName:'aggWorkers', metricFields:['avg_worker_agreement','worker_cosine'],
        metricName:['worker agreement','worker cosine']}
    } else {
        infoFields.push({field:'max_relation_Cos', name:'avg unit clarity'});
        infoFields.push({field:'magnitude', name:'magnitude'});
        querySettings = {'metricCateg':'units',metricFilter:'withSpam', aggName:"aggUnits", metricFields:['max_relation_Cos','magnitude'],
        metricName:['unit clarity','unit magnitude']}
    }

    var urlBase = "/api/analytics/piegraph/?match[documentType][]=annotation&";
    var currentSelection = [];
    var currentSelectionInfo = {};
    var pieChartOptions = {};
    var unitInfo = {};

    var spammers = [];
    var seriesBase = [];
    var pieChart = "";
    var barChart = "";
    var callback = function callback($this){
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

    var compare = function (a, b) {
        a_array = a._id.split("/");
        b_array = b._id.split("/");
        a_id = a_array[a_array.length - 1];
        b_id = b_array[b_array.length - 1];
        return a_id - b_id;
    };

    var getJobsData = function (url) {
        //make a check and see which units have workers?
        var categories = [];
        var colorMaps = {};
        var seriesMaps = {};
        var colors =  Highcharts.getOptions().colors;
        var series = seriesBase;
        jobsURL = url + 'project[' + queryField + ']=' + queryField +
            '&group=job_id&push[' + queryField + ']=' + queryField;
        for (var iterSeries in series) {
            series[iterSeries]['data'] = [];
        }

        //get the list of workers for this units
        $.getJSON(jobsURL, function (data) {
            data.sort(compare);
            var urlJobMatchStr = "";
            for (var iterData in data) {
                //urlJobMatchStr += "&match[_id][]=" + data[iterData]['_id'];
                categories.push(data[iterData]['_id']);
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

            var newSeries = {};
            var urlJobsInfo =  '/api/v1/?field[documentType]=job&';
            for (var iterCateg in categories) {
                urlJobsInfo += 'field[_id][]=' + categories[iterCateg] + '&';
            }

            for (var iterSeries in series) {
                var color = Highcharts.Color(colors[iterSeries%(colors.length)]).get();
                series[iterSeries]['color'] = color;
                series[iterSeries]['type'] = 'column';
                var categoryID =  series[iterSeries]['name'];
                series[iterSeries]['categoryID'] = categoryID;
                newSeries[categoryID] = {};

                for (var iterMetric in querySettings['metricFields']){
                    urlJobsInfo += '&only[]=metrics.' + querySettings['metricCateg'] + '.' +
                                    querySettings['metricFilter'] + '.' + categoryID + '.' + querySettings['metricFields'][iterMetric] + '.avg';
                    var dashStyle ='shortdot';
                    if (iterMetric % 2 == 0) { dashStyle = 'LongDash'; }
                    newSeries[categoryID][querySettings['metricFields'][iterMetric]] =
                                                                      {'name': querySettings['metricName'][iterMetric] ,
                                                                        data:[],
                                                                        categoryID: categoryID,
                                                                        type: 'spline', color:Highcharts.Color(color).brighten(0.3).get(),
                                                                        'dashStyle':dashStyle, linkedTo: iterSeries, yAxis:1};
                }
            }

            //get worker's info
            var urlUnitInfo = '/api/analytics/metrics/?&'
            for (var indexUnits in currentSelection) {
                urlUnitInfo += 'match['+ queryField + '][]=' + currentSelection[indexUnits] + '&';
            }
            urlUnitInfo += 'match[documentType][]=annotation&project[job_id]=job_id&push[job_id]=job_id' +
                '&metrics[]=type&metrics[]=softwareAgent_id&'
            for (var indexMetric in querySettings['metricFields']) {
                urlUnitInfo += 'metrics[]=metrics.' + querySettings['aggName'] + '.mean.' + querySettings['metricFields'][indexMetric] + '.avg&';
            }

            $.getJSON(urlUnitInfo, function (data) {
                for(var iterData in data) {
                    unitInfo[data[iterData]['_id']] = data[iterData];
                    for (var indexMetric in querySettings['metricFields']) {
                        var metricName = querySettings['metricFields'][indexMetric];
                        unitInfo[data[iterData]['_id']][metricName] = data[iterData]['metrics'][querySettings['aggName']]['mean'][metricName]['avg'];
                    }
                }

                $.getJSON(urlJobsInfo, function (data) {

                    data.sort(compare);
                    for (var iterData in data) {
                        var metrics = data[iterData]['metrics'][querySettings['metricCateg']][querySettings['metricFilter']];
                        for (var iterSeries in series) {
                            var id = series[iterSeries]['name'];
                            if (id in metrics){
                                for (var indexMetric in querySettings['metricFields']) {
                                    var metricName = querySettings['metricFields'][indexMetric];
                                    newSeries[id][metricName]['data'].push(metrics[id][metricName]['avg']);
                                }
                            } else {
                                for (var indexMetric in querySettings['metricFields']) {
                                    newSeries[id][querySettings['metricFields'][indexMetric]]['data'].push(0);
                                }
                            }
                        }
                    }
                    for( var iterIds in newSeries){
                        for (var indexMetric in querySettings['metricFields']) {
                            var metricName = querySettings['metricFields'][indexMetric];
                            series.push(newSeries[iterIds][metricName]);
                        }
                    }
                    drawBarChart(series, categories);
                });
            });
        });
    }

    var drawBarChart = function (series, categories) {
        //get the metrics for the units

        barChart = new Highcharts.Chart({
            chart: {
                zoomType: 'x',
                renderTo: 'jobsBar_div',
                type: 'column',
                width: (3*(($('.maincolumn').width() - 50)/5)),
                height: 430,
                events: {
                    load: function () {
                        var chart = this,
                            legend = chart.legend;
                        for (var i = 0, len = legend.allItems.length; i < len; i++) {
                            var item = legend.allItems[i].legendItem;
                            var tooltipValue = "";

                           /* if (typeof currentSelectionInfo[legend.allItems[i].name]['tooltipLegend'] === 'string') {
                                var tooltipValue = currentSelectionInfo[legend.allItems[i].name]['tooltipLegend'];
                            } else {
                                for( var indexInfoKey in currentSelectionInfo[legend.allItems[i].name]['tooltipLegend']) {
                                    tooltipValue +=  currentSelectionInfo[legend.allItems[i].name]['tooltipLegend'][indexInfoKey] + '(' + indexInfoKey + ')' + '<br/>';
                                }
                            }*/

                            item.attr("data-toggle","tooltip");
                            item.attr("title", tooltipValue);

                        }

                    }
                }
            },
            title: {
                text: 'Annotations of ' + categories.length +' Job(s) of ' + currentSelection.length + ' Selected ' +  categoryName + '(s)'
            },
            subtitle: {
                text: 'Select an area to zoom. To see detailed information select individual units.From legend select/deselect features.'
            },
            xAxis: {
                title :{
                    text: 'Job ID'
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
                            title = 'Annotations of ' + interval.toFixed(0) +' Job(s) of ' + currentSelection.length + ' Selected ' +  categoryName + '(s)'
                        } else {
                            title = 'Annotations of ' + interval.toFixed(0) + '/' + barChart.series[0].data.length +' Job(s) of ' + currentSelection.length + ' Selected ' +  categoryName + '(s)'
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
                    return categoryName + ' ' + value;
                }
            },
            yAxis: [{
                min: 0,
                title: {
                    text: '# micro tasks per job'
                }
            },
                {
                    min: 0,
                    title: {
                        text: 'unit clarity per job'
                    },
                    opposite:true
                }],
            tooltip: {

                hideDelay:10,
                useHTML : true,
                formatter: function() {
                    var arrayID = this.x.split("/");
                    var id =  arrayID[arrayID.length - 1];
                    var s = '<div style="white-space:normal;"><b>Job </b>'+ id +'<br/>';
                    for (var index in infoFields) {
                        var field = infoFields[index]['field'];
                        var pointValue =  unitInfo[this.x][field];
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
                            name = name;//.substr(shortName.length,name.length);
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
                    minPointLength : 2,

                    events: {
                        legendItemClick: function(event) {
                                var categoryID = this['options']['categoryID'];
                                for (var iterData = 0; iterData < barChart.series.length; iterData++) {
                                    if (barChart.series[iterData]['options']['categoryID'] == categoryID & barChart.series[iterData].type == 'spline') {
                                        barChart.series[iterData].visible ? barChart.series[iterData].hide() : barChart.series[iterData].show();
                                    }
                                }

                        }
                    }
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
                                    'data-modal-query="job=' + this.category + '&' + urlBase + '" data-api-target="/api/analytics/job?" ' +
                                    'data-target="#modalIndividualJob" data-toggle="tooltip" data-placement="top" title="" ' +
                                    'data-original-title="Click to see the individual worker page">6345558 </a>');
                                //$('body').append(anchorModal);
                                openModal(anchorModal, "#job_tab");



                            }
                        }
                    }
                }
            },
            series: series
        },callback);

    }

    var drawPieChart = function (platform, spam) {
        pieChart = new Highcharts.Chart({
            chart: {
                renderTo: 'jobsPie_div',
                type: 'pie',
                width: (2*(($('.maincolumn').width() - 50)/5)),
                height: 400
            },
            title: {
                text: 'Number of Jobs of the ' + currentSelection.length + ' selected ' + categoryName + '(s)'
            },
            subtitle: {
                text: 'Click a category to see the distribution of annotations per jobs'
            },
            yAxis: {
                title: {
                    text: 'Number of workers per unit'
                }
            },
            dataLabels: {
                enabled: true
            },
            credits: {
                enabled: false
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
                                if ('type' in this.options) {
                                    searchSet = pieChartOptions[this.options.platform][this.options.type];
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
                    return '<p><b>' + seriesValue + ' </b></br>' + this.series.name + ' : ' +
                        this.percentage.toFixed(2) + ' % ('  + this.y + '/' + this.total + ')' +
                        '</p>';
                },
                followPointer : false,
                hideDelay:10
            },

            series: [
                {
                    name: '# of jobs',
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
                    name: '# of jobs',
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


    this.update = function (selectedUnits, selectedInfo) {
        if(selectedUnits.length == 0){
            if ( $('#jobsBar_div').highcharts() != undefined ) {
                $('#jobsBar_div').highcharts().destroy();
                $('#jobsPie_div').highcharts().destroy();
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
            seriesBase.push({'name': selectedUnits[indexUnits], data: [],  yAxis: 0,
                type: 'column'});
        }

        getJobsData(urlBase);
        platformURL = urlBase + 'project[job_id]=job_id&group=softwareAgent_id&addToSet=job_id';
        $.getJSON(platformURL, function (data) {
            var platformData = [];
            var categoriesData = [];
            var requests = [];
            var iterColors = 0;
            var colors = ['#FFC640', '#A69C00'];


            for (var platformIter in data) {
                var platformID = data[platformIter]['_id'];
                platformData.push({name: platformID, y: data[platformIter]['content'].length,
                    color: Highcharts.Color(colors[platformIter]).brighten(0.07).get(),
                    platform: platformID});
                pieChartOptions[platformID] = {};
                pieChartOptions[platformID]['all'] = data[platformIter]['content'];
                //get the jobs by category
                var urlType = "/api/analytics/piegraph/?match[documentType][]=job&";
                for (var jobIter in data[platformIter]['content']) {
                    urlType += 'match[_id][]='+data[platformIter]['content'][jobIter] + '&';
                }
                urlType += '&project[_id]=_id&group=type&addToSet=_id';
                requests.push($.get(urlType));

            }
            var defer = $.when.apply($, requests);
            defer.done(function () {

                $.each(arguments, function (index, responseData) {
                    // "responseData" will contain an array of response information for each specific request
                    if ($.isArray(responseData)) {
                        if (responseData[1] == 'success') {
                            responseData = responseData[0];
                        }
                        for (var iterObj in responseData) {

                            categoriesData.push({name: responseData[iterObj]['_id'],
                                type: responseData[iterObj]['_id'],
                                y: responseData[iterObj].content.length,
                                color: Highcharts.Color(colors[index]).brighten(-0.01*iterObj).get(),
                                platform: data[index]['_id']});
                            pieChartOptions[data[index]['_id']][responseData[iterObj]['_id']] = responseData[iterObj].content;
                        }
                    }
                });
                drawPieChart(platformData, categoriesData);
            });

        });
       //get the list of jobs of the units grouped by platform units

       //get the set of job ids
       //get the types of the set of jobs
       //draw the
    }

    this.createUnitsJobDetails = function () {
    }

}
function unitsAnnotationDetails(category, categoryName, openModal) {
    var urlBase = "/api/analytics/piegraph/?match[documentType][]=annotation&";
    var queryField = 'unit_id';
    if (category == '#job_tab'){
        queryField = 'job_id'
    } else if (category == '#crowdagents_tab'){
        queryField = 'crowdAgent_id'
    }
    var currentSelection = [];
    var currentSelectionInfo = {};
    var unitsAnnotationInfo = {};
    var activeSelectedPlatform = "";
    var activeSelectedType = "";
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
    var drawBarChart = function (series, categories) {

        barChart = new Highcharts.Chart({
            chart: {
                zoomType: 'x',
                renderTo: 'annotationsBar_div',
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
                                    tooltipValue +=  currentSelectionInfo[legend.allItems[i].name]['tooltipLegend'][indexInfoKey] + '(' + indexInfoKey + ')' + '<br/>';
                                }
                            }

                            item.attr("data-toggle","tooltip");
                            item.attr("title", tooltipValue);

                        }

                    }
                }
            },
            title: {
                text: 'Aggregated view of ' + categories[0].length + ' annotations of ' +  currentSelection.length   +  ' Selected ' + categoryName + '(s)'
            },
            subtitle: {
                text: 'Select an area to zoom. To see detailed information select individual units.From legend select/deselect features.'
            },
            credits: {
                enabled: false
            },
            xAxis: [{
                categories: categories[0],
                title :{
                    text: 'Annotation name'
                },
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
                        var title = "";
                        if (interval ==  barChart.series[0].data.length) {
                            title = 'Aggregated view of ' + interval.toFixed(0) + ' annotations of ' +  currentSelection.length   +  ' Selected ' + categoryName + '(s)'
                        } else {
                            title = 'Aggregated view of ' + interval.toFixed(0) + '/' + barChart.series[0].data.length + ' annotations of ' +  currentSelection.length   +  ' Selected ' + categoryName + '(s)'
                        }
                        barChart.setTitle({text: title});
                    }
                }

            },{
                    categories: categories[1],
                    opposite:true,
                labels:
                {
                    enabled: false
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
                            var title = "";
                            if (interval ==  barChart.series[0].data.length) {
                                title = 'Aggregated view of ' + interval.toFixed(0) + ' annotations of ' +  currentSelection.length   +  ' Selected ' + categoryName + '(s)'
                            } else {
                                title = 'Aggregated view of ' + interval.toFixed(0) + '/' + barChart.series[0].data.length + ' annotations of ' +  currentSelection.length   +  ' Selected ' + categoryName + '(s)'
                            }
                            barChart.setTitle({text: title});
                        }
                    }

                }],
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
                    text: '# annotations'
                }
            },
                {
                    min: 0,
                    opposite: true,
                    title: {
                        text: 'metrics'
                    }
                }]
               ,
            tooltip: {

                hideDelay:10,
                useHTML : true,
                formatter: function() {
                    var arrayID = this.x.split("/");
                    var id =  arrayID[arrayID.length - 1];
                    var s = '<div style="white-space:normal;"><b>Annotation </b>'+ id +'<br/>';


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

                        if (point.series.name == id) {
                            name = '# of ann ';
                        }
                        if(point.series.options.manyAnnVectors){
                            var stackKey = point.series.stackKey;
                            var columnName = 'column';
                            name += '(' + stackKey.substr(columnName.length,stackKey.length) + ')';
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
            plotOptions: {
                series: {
                    minPointLength : 0,
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
                                urlBase += 'match[softwareAgent_id][]=' + activeSelectedPlatform ;
                                if (activeSelectedType != ""){
                                    urlBase += '&match[type][]=' + activeSelectedType;
                                }

                                anchorModal = $('<a class="testModal"' +
                                    'data-modal-query="' +  urlBase + '" data-api-target="/api/analytics/annotation?" ' +
                                    'data-target="#modalAnnotations" data-toggle="tooltip" data-placement="top" title="" ' +
                                    'data-original-title="Click to see the individual worker page">6345558 </a>');
                                //$('body').append(anchorModal);
                                openModal(anchorModal, '#twrex-structured-sentence_tab');
                            }
                        }
                    }
                }
            },
            series: series
        },callback);

    }

    var getBarChartData = function(platform, type) {
        //url to get the annotation

        var categories = [];
        var series = [];
        var annotationsURL = urlBase;
        activeSelectedPlatform = platform;
        activeSelectedType = type;
        annotationsURL += 'match[softwareAgent_id][]=' + platform ;
        if (type != ""){
            annotationsURL += '&match[type][]=' + type;
        }

        annotationsURL += '&project[dictionary]=dictionary&group=' + queryField + '&push[dictionary]=dictionary';

        //get the list of workers for this units
        $.getJSON(annotationsURL, function (data) {
            var colors =  Highcharts.getOptions().colors;

            var colorMaps = {};
            var seriesMaps = {};
            var microtasks = [];
            for (var iterData in data) {

                microtasks = [];
                var seriesData = {};
                for (var iterObject in data[iterData]['dictionary']) {
                    var object = data[iterData]['dictionary'][iterObject];
                    for (var microTaskKey in object) {
                        if (!(microTaskKey in seriesData)) {
                            microtasks.push(microTaskKey);
                            seriesData[microTaskKey] = object[microTaskKey];
                            continue;
                        }

                        for (var key in object[microTaskKey]){
                            if(key in seriesData[microTaskKey]){
                                seriesData[microTaskKey][key] += object[microTaskKey][key];
                            } else {
                                seriesData[microTaskKey][key] = object[microTaskKey][key];
                            }
                        }
                    }
                }

                if (categories.length == 0) {
                    for (var microTaskKeyIter in microtasks) {
                        for (var key in seriesData[microtasks[microTaskKeyIter]]) {
                            categories.push(key);
                        }
                        break
                    }
                }

                var first = true;
                var manyAnnVectors = microtasks.length > 1 ;

                for (var microTaskKey in microtasks) {

                    var newSeries = {'name':  data[iterData]['_id'], categoryID:data[iterData]['_id'], 'manyAnnVectors':manyAnnVectors, data:[], type: 'column', stack:microtasks[microTaskKey], color: colors[iterData%colors.length]};
                    colorMaps[data[iterData]['_id']] = colors[iterData%colors.length];
                    seriesMaps[data[iterData]['_id']] = series.length;
                    if(!(first == true)){
                        newSeries.linkedTo = ':previous';
                    } else {
                        first = false;
                    }
                    for(var iterCategories in categories) {
                        newSeries.data.push(seriesData[microtasks[microTaskKey]][categories[iterCategories]]);
                    }

                    series.push(newSeries);
                }

            }

            var additionalSeries = [];
            if (queryField == 'job_id') {

                var urlJobs = "/api/v1/?";
                for (var indexUnits in currentSelection) {
                    urlJobs += 'field[_id][]=' + currentSelection[indexUnits] + '&';
                }
                urlJobs += 'field[softwareAgent_id][]=' + activeSelectedPlatform ;
                if (activeSelectedType != ""){
                    urlJobs += '&field[type][]=' + activeSelectedType + '&';
                }
                urlJobs += 'only[]=metrics.annotations';

                $.getJSON(urlJobs, function (data) {

                    for (var iter in data){
                        //iterate job
                        var jobData = data[iter];
                        var ambiguity = {'name': "ambiguity", data:[], categoryID:data[iter]['_id'], type: 'spline', 'dashStyle':'shortdot',
                            color:Highcharts.Color( colorMaps[data[iter]['_id']]).brighten(0.3).get(), linkedTo: seriesMaps[data[iter]['_id']], xAxis:1, yAxis:1};
                        var clarity = {'name': "clarity", data:[], categoryID:data[iter]['_id'], type: 'spline','dashStyle':'LongDash',
                            color:Highcharts.Color( colorMaps[data[iter]['_id']]).brighten(0.1).get(),  linkedTo: seriesMaps[data[iter]['_id']], xAxis:1, yAxis:1};
                        for (var iterCateg in categories) {
                            for (var iterMicroTask in microtasks){
                                if (!('metrics' in data[iterData])) {continue;}
                                var value = data[iter]['metrics']['annotations']['withoutSpam'][microtasks[iterMicroTask]]['annot_ambiguity'][categories[iterCateg]];
                                ambiguity['data'].push(value);
                                var value = data[iter]['metrics']['annotations']['withoutSpam'][microtasks[iterMicroTask]]['annot_clarity'][categories[iterCateg]];
                                clarity['data'].push(value);
                            }

                        }
                        series.push(ambiguity);
                        series.push(clarity);
                    }
                    var categoriesJobs = [];
                    for (iter in categories) {
                        for(iterMicroTask in microtasks) {
                            categoriesJobs.push(categories[iter]);
                        }
                    }
                    drawBarChart(series, [categories, categoriesJobs]);
                });

            } else {
                drawBarChart(series, [categories,categories]);
            }
            //drawBarChart(series, [categories,categories]);
        });
        //group them
    }

    var drawPieChart = function (platform, spam) {
        pieChart = new Highcharts.Chart({
            chart: {
                renderTo: 'annotationsPie_div',
                type: 'pie',
                width: (2*(($('.maincolumn').width() - 50)/5)),
                height: 430
            },
            title: {
                text: 'Type of Annotations of the ' + currentSelection.length + ' selected '+ ' ' + categoryName + '(s)'
            },
            credits: {
                enabled: false
            },
            subtitle: {
                text: 'Click a category to see the distribution of annotations'
            },
            yAxis: {
                title: {
                    text: 'Number of workers per unit'
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
                                var platform = this.options.platform;
                                var type = "";
                                if ('type' in this.options) {
                                    type = this.options.type;
                                }
                                getBarChartData(platform, type);

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
                    name: '# of annotations',
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
                    name: '# of annotations',
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

            if ( $('#annotationsPie_div').highcharts() != undefined ) {

                $('#annotationsPie_div').highcharts().destroy();
            }
            if ( $('#annotationsPie_div').highcharts() != undefined ) {

                $('#annotationsPie_div').highcharts().destroy();
            }

            return;
        }
        currentSelection = selectedUnits;
        currentSelectionInfo = selectedInfo
        seriesBase = [];
        urlBase = "/api/analytics/piegraph/?match[documentType][]=annotation&";
        //create the series data
        for (var indexUnits in selectedUnits) {
            urlBase += 'match[' + queryField + '][]=' + selectedUnits[indexUnits] + '&';
            seriesBase.push({'name': selectedUnits[indexUnits], data: [],  yAxis: 0,
                type: 'column'});
        }


        platformURL = urlBase + '&project[_id]=_id&group=softwareAgent_id&push[id]=_id';
        $.getJSON(platformURL, function (data) {
            var platformData = [];
            var categoriesData = [];
            var requests = [];
            var iterColors = 0;
            var colors = ['#FFC640', '#A69C00'];


            for (var platformIter in data) {
                var platformID = data[platformIter]['_id'];
                platformData.push({name: platformID, y: data[platformIter]['id'].length,
                    color: Highcharts.Color(colors[platformIter]).brighten(0.07).get(),
                    platform: platformID});
                unitsAnnotationInfo[platformID] = {};
                unitsAnnotationInfo[platformID]['all'] = data[platformIter]['id'];
                //get the jobs by category
                requests.push($.get(urlBase + 'match[softwareAgent_id][]=' + data[platformIter]['_id'] + '&project[_id]=_id&group=type&addToSet=_id'));


            }
            var defer = $.when.apply($, requests);
            defer.done(function () {
                var platform = "";
                var type = "";

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
                            platform = data[index]['_id'];
                            type = responseData[iterObj]['_id']
                            unitsAnnotationInfo[platform][type] = responseData[iterObj].content;
                        }
                    }
                });
                drawPieChart(platformData, categoriesData);

            });

        });

    }

    this.createUnitsAnnotationDetails = function () {

    }

}
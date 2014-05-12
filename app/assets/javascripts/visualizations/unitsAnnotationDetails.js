function unitsAnnotationDetails(category, categoryName, openModal) {
    var urlBase = "/api/analytics/piegraph/?match[documentType][]=annotation&";
    var queryFields = {'#twrex-structured-sentence_tab': 'unit_id', '#fullvideo_tab': 'unit_id', '#job_tab': 'job_id', '#crowdagents_tab': 'crowdAgent_id'};
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
                        console.dir(currentSelectionInfo);
                        for (var i = 0, len = legend.allItems.length; i < len; i++) {
                            var item = legend.allItems[i].legendItem;
                            var tooltipValue = "";
                            if (typeof currentSelectionInfo[legend.allItems[i].name] === 'string') {
                                var tooltipValue = currentSelectionInfo[legend.allItems[i].name];
                            } else {
                                for( var indexInfoKey in currentSelectionInfo[legend.allItems[i].name]) {
                                    tooltipValue +=  currentSelectionInfo[legend.allItems[i].name][indexInfoKey] + '(' + indexInfoKey + ')' + '<br/>';
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
                    text: '# annotation per unit'
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
                shared: true,
                useHTML: true,
                headerFormat: '<b>Annotation {point.key}</b></br><p>(Click for details)</p><table>',
                pointFormat: '<tr><td style="color: {series.color};text-align: left">{series.name}: </td>' +
                    '<td style="text-align: right"><b>{point.y} annotations</b></td></tr>',
                footerFormat: '</table>',
                valueDecimals: 2

            },
           /* exporting: {
                buttons: {
                    customButton: {
                        x: -32,
                        onclick: function () {
                            alert('Clicked1');
                        },
                        theme: {
                            'stroke-width': 1,
                            stroke: 'silver',
                            fill: '#bada55',
                            height: 40,
                            width: 48,
                            symbolSize: 24,
                            symbolX: 23,
                            symbolY: 21,
                            symbolStrokeWidth: 2,
                            class: 'fa fa-circle-o fa-fw'

                        },
                        text: 'Low quality'

                    },
                    customButton2: {
                        x: -62,
                        onclick: function () {
                            alert('Clicked2');
                        },
                        height: 40,
                        width: 48,
                        symbolSize: 24,
                        symbolX: 23,
                        symbolY: 21,
                        symbolStrokeWidth: 2,
                        fill: '#bada55',
                        text: 'High quality'
                    }
                }
            },*/
            plotOptions: {
                series: {
                    minPointLength : 0
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
                                    urlBase += 'match['+ queryFields[category] + '][]=' + currentSelection[indexUnits] + '&';
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

        annotationsURL += '&project[dictionary]=dictionary&group=' + queryFields[category] + '&push[dictionary]=dictionary';

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
                console.dir('map');
                console.dir(seriesData);

                var first = true;
                for (var microTaskKey in microtasks) {
                    var newSeries = {'name':  data[iterData]['_id'], data:[], type: 'column', stack:microtasks[microTaskKey], color: colors[iterData%colors.length]};
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
                console.dir(iterData);
                console.dir(series);

            }
            console.dir("partial series");
            console.dir(series);
            console.dir(colorMaps);
            console.dir(seriesMaps);

            var additionalSeries = [];
            if (queryFields[category] == 'job_id') {

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
                        console.dir(data[iter]);
                        var ambiguity = {'name': "ambiguity", data:[], type: 'spline', color:Highcharts.Color( colorMaps[data[iter]['_id']]).brighten(0.3).get(), linkedTo: seriesMaps[data[iter]['_id']], xAxis:1, yAxis:1};
                        var clarity = {'name': "clarity", data:[], type: 'spline',color:Highcharts.Color( colorMaps[data[iter]['_id']]).brighten(0.1).get(),  linkedTo: seriesMaps[data[iter]['_id']], xAxis:1, yAxis:1};
                        for (var iterCateg in categories) {
                            console.dir(microtasks);
                            for (var iterMicroTask in microtasks){
                                console.dir(categories);
                                var value = data[iter]['metrics']['annotations']['withoutSpam'][microtasks[iterMicroTask]]['annot_ambiguity'][categories[iterCateg]];
                                ambiguity['data'].push(value);
                                var value = data[iter]['metrics']['annotations']['withoutSpam'][microtasks[iterMicroTask]]['annot_clarity'][categories[iterCateg]];
                                clarity['data'].push(value);
                            }

                        }
                        console.dir(ambiguity);
                        console.dir(clarity);
                        series.push(ambiguity);
                        series.push(clarity);
                    }
                    var categoriesJobs = [];
                    for (iter in categories) {
                        for(iterMicroTask in microtasks) {
                            categoriesJobs.push(categories[iter]);
                        }
                    }
                    console.dir(categories);
                    console.dir(categoriesJobs);

                    drawBarChart(series, [categories, categoriesJobs]);
                });

            } else {

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


                                /* console.dir(urlBase + this.options.match + '&');
                                 getWorkersData(urlBase + this.options.match + '&');
                                 ///pieChart.series[this.options.ser_nr].data[this.x].select(null,true);
                                 console.dir(pieChart.getSelectedPoints());*/
                                /*var elem = mapping[this.name];
                                 url = '/api/analytics/piegraph/?match[documentType][]=annotation' +
                                 '&match[unit_id][]='+elem['id']+
                                 '&match[spam]='+elem['spam']+
                                 '&group=crowdAgent_id';
                                 $.getJSON(url, function(data) {
                                 createUnitBarChart(data);
                                 });*/
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
        currentSelection = selectedUnits;
        currentSelectionInfo = selectedInfo
        seriesBase = [];
        urlBase = "/api/analytics/piegraph/?match[documentType][]=annotation&";
        //create the series data
        for (var indexUnits in selectedUnits) {
            urlBase += 'match[' + queryFields[category] + '][]=' + selectedUnits[indexUnits] + '&';
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
        //get the list of jobs of the units grouped by platform units

        //get the set of job ids
        //get the types of the set of jobs
        //draw the
    }

    this.createUnitsAnnotationDetails = function () {

    }

}
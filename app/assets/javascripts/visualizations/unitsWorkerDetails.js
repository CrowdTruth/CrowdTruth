function unitsWorkerDetails(category, categoryName, openModal) {
    var queryField = 'unit_id';
    if (category == '#job_tab'){
        queryField = 'job_id'
    }
    var urlBase = "/api/analytics/piegraph/?match[documentType][]=annotation&";
    var unitsWorkersInfo = {};
    var currentSelection = [];
    var currentSelectionInfo = {};
    var spammers = [];
    var seriesBase = [];
    var pieChart = "";
    var barChart = "";

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
                                searchSet = unitsWorkersInfo[this.options.platform]['all'];

                                if ('spam' in this.options) {
                                    if(this.options.spam == 0) {
                                        searchSet = unitsWorkersInfo[this.options.platform]['spam'];
                                    } else {
                                        if(this.options.spam == 1) {
                                            searchSet = unitsWorkersInfo[this.options.platform]['potential'];
                                        } else {
                                            searchSet = unitsWorkersInfo[this.options.platform]['nonSpam'];
                                        }
                                    }
                                }
                                console.dir(this);
                                console.dir(searchSet);

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
                    console.dir(this);
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
                renderTo: 'workersBar_div',
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
                                    tooltipValue +=  currentSelectionInfo[legend.allItems[i].name][indexInfoKey] + ' (' + indexInfoKey + ')' + '<br/>';
                                }
                            }

                            item.attr("data-toggle","tooltip");
                            item.attr("title", tooltipValue);

                        }

                    }
                }
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
                    return categoryName + ' ' + value;
                }
            },
            yAxis: {
                min: 0,
                title: {
                    text: '# annotation per unit'
                }
            },
            tooltip: {
                crosshairs: true,
                shared: true,
                useHTML: true,
                headerFormat: '<b>Worker {point.key}</b></br><p>(Click for details)</p><table>',
                pointFormat: '<tr><td style="color: {series.color};text-align: left">{series.name}: </td>' +
                    '<td style="text-align: right"><b>{point.y} annotations</b></td></tr>',
                footerFormat: '</table>',
                valueDecimals: 2

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

                                 //$('#6345558').click();

//                                console.dir('#'+ this.category);
//                                var arrayUnit = this.category.split("/");
//                                var value = arrayUnit[arrayUnit.length - 1];
//                                console.dir('#'+value);
//                                $('#'+value).click();


                            }
                        }
                    }
                }
            },
            series: series
        });

    }


    var getWorkersData = function (url) {
        //make a check and see which units have workers?

        var categories = [];
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
            drawBarChart(series, categories);
        });
    }

    this.update = function (selectedUnits, selectedInfo) {
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
                unitsWorkersInfo[platformID] ={};
                unitsWorkersInfo[platformID]['all'] = data[platformIter]['content'];
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
                                unitsWorkersInfo[data[index]['_id']]['spam'] = content;
                            } else {
                                spamData.push({name: 'high quality',
                                    spam: 3,//no spam
                                    y: content.length,
                                    color: colors[index],
                                    platform: data[index]['_id']});
                                unitsWorkersInfo[data[index]['_id']]['nonSpam'] = content;
                            }
                        }
                        if(commonWorkers.length > 0) {
                            spamData.push({name: 'potentially low quality',
                                spam: 1,//possible spam
                                y: commonWorkers.length,
                                color: Highcharts.Color(colors[index]).brighten(-0.09).get(),
                                platform: data[index]['_id']});
                            unitsWorkersInfo[data[index]['_id']]['potential'] = commonWorkers;
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
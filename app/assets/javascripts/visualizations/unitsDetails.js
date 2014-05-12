function unitsDetails(category, categoryName, openModal) {
    var urlBase = "/api/analytics/piegraph/?match[documentType][]=annotation&";
    var unitsWorkersInfo = {};
    var currentSelection = [];
    var currentSelectionInfo = {};
    var queryFields = {'#crowdagents_tab': 'crowdAgent_id', '#job_tab': 'job_id'};
    var spammers = [];
    var seriesBase = [];
    var pieChart = "";
    var barChart = "";

    var drawPieChart = function (platform, spam) {
        pieChart = new Highcharts.Chart({
            chart: {
                renderTo: 'unitsPie_div',
                type: 'pie',
                width: (2*(($('.maincolumn').width() - 50)/5)),
                height: 400
            },
            title: {
                text: 'Units of the selected ' + categoryName + 's (' + currentSelection.length + ')'
            },
            subtitle: {
                text: 'Click a category to see the distribution of annotations per unit'
            },
            credits: {
                enabled: false
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
                                    if(this.options.spam == true) {
                                        searchSet = unitsWorkersInfo[this.options.platform]['spam'];
                                    } else {
                                        searchSet = unitsWorkersInfo[this.options.platform]['nonSpam'];
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

                    return '<p><b>' + seriesValue + ' </b></br>' + this.series.name + ' : ' +
                        this.percentage.toFixed(2) + ' % ('  + this.y + '/' + this.total + ')' +
                        '</p>';
                },
                followPointer : false,
                hideDelay:10
            },

            series: [
                {
                    name: '# of units',
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
                    name: '# of units',
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
                renderTo: 'unitsBar_div',
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
                text: 'Annotations of Units on Selected ' +   categoryName + 's' + '(' + categories.length + ' out of total ' + categories.length + ')'
            },
            subtitle: {
                text: 'Select an area to zoom. To see detailed information select individual units.From legend select/deselect features.'
            },
            credits: {
                enabled: false
            },
            xAxis: {
                title :{
                    text: 'Unit ID'
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
                        var title = 'Annotations of Units on Selected ' +   categoryName + 's' + '(' + interval.toFixed(0) + ' out of total ' + barChart.series[0].data.length + ')';
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
                headerFormat: '<b>Unit {point.key}</b></br><p>(Click for details)</p><table>',
                pointFormat: '<tr><td style="color: {series.color};text-align: left">{series.name}: </td>' +
                    '<td style="text-align: right"><b>{point.y} annotations</b></td></tr>',
                footerFormat: '</table>',
                valueDecimals: 2

            },
            plotOptions: {
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
                                anchorModal = $('<a class="testModal"' +
                                    'data-modal-query="unit=' + this.category + '&' + urlBase + '" data-api-target="/api/analytics/unit?" ' +
                                    'data-target="#modalIndividualUnit" data-toggle="tooltip" data-placement="top" title="" ' +
                                    'data-original-title="Click to see the individual worker page">6345558 </a>');
                                //$('body').append(anchorModal);
                                openModal(anchorModal, '#twrex-structured-sentence_tab');


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
        workersURL = url + 'project[' + queryFields[category] + ']=' + queryFields[category] +
            '&group=unit_id&push[' + queryFields[category] + ']=' + queryFields[category];
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
                    for (var iterUnits in data[iterData][queryFields[category]]) {
                        if (data[iterData][queryFields[category]][iterUnits] == unit_id) {
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
            urlBase += 'match['+ queryFields[category] + '][]=' + selectedUnits[indexUnits] + '&';
            seriesBase.push({'name': selectedUnits[indexUnits], data: []});
        }

        getWorkersData(urlBase);
        //get the workers grouped by platform and spam, nonspam
        platformURL = urlBase + 'project[unit_id]=unit_id&group=softwareAgent_id&addToSet=unit_id';
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
          //      requests.push($.get(urlBase + 'match[softwareAgent_id][]=' + data[platformIter]['_id'] + '&project[crowdAgent_id]=crowdAgent_id&group=spam&addToSet=crowdAgent_id'));

            }
            drawPieChart(platformData, spamData);


        });
    }

    this.createUnitsDetails = function () {
       //get filtered units
    }

}
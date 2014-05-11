function unitsAnnotationDetails(category) {
    var urlBase = "/api/analytics/piegraph/?match[documentType][]=annotation&";
    var queryFields = {'#twrex-structured-sentence_tab': 'unit_id', '#fullvideo_tab': 'unit_id', '#job_tab': 'job_id', '#crowdagents_tab': 'crowdAgent_id'};
    var currentSelection = [];
    var unitsAnnotationInfo = {};
    var spammers = [];
    var seriesBase = [];
    var pieChart = "";
    var barChart = "";

    var drawBarChart = function (series, categories) {
        barChart = new Highcharts.Chart({
            chart: {
                zoomType: 'x',
                renderTo: 'annotationsBar_div',
                type: 'column',
                width: (3*(($('.maincolumn').width() - 50)/5)),
                height: 400
            },
            title: {
                text: 'Aggregated annotations'
            },
            credits: {
                enabled: false
            },
            xAxis: {
                categories: categories,
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
                }

            },
            legend: {
                maxHeight: 100
            },
            yAxis: {
                min: 0,
                title: {
                    text: '# annotation per unit'
                }
            }
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
                            }
                        }
                    }
                }
            },
            series: series
        });

    }

    var getBarChartData = function(platform, type) {
        //url to get the annotation

        var categories = [];
        var series = [];
        var annotationsURL = urlBase;
        annotationsURL += 'match[softwareAgent_id][]=' + platform ;
        if (type != ""){
            annotationsURL += '&match[type][]=' + type;
        }

        annotationsURL += '&project[dictionary]=dictionary&group=' + queryFields[category] + '&push[dictionary]=dictionary';

        //get the list of workers for this units
        $.getJSON(annotationsURL, function (data) {
            var colors = ['#528B8B', '#00688B', '#2F4F4F', '#66CCCC' ,'#00CDCD', '#607B8B' ];
            for (var iterData in data) {

                var seriesData = {};
                for (var iterObject in data[iterData]['dictionary']) {
                    var object = data[iterData]['dictionary'][iterObject];
                    for (var microTaskKey in object) {
                        if (!(microTaskKey in seriesData)) {
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
                    for (var microTaskKey in seriesData) {
                        for (var key in seriesData[microTaskKey]) {
                            categories.push(key);
                        }
                        break
                    }
                }

                var first = true;
                for (var microTaskKey in seriesData) {
                    var newSeries = {'name':  data[iterData]['_id'], data:[], type: 'column', stack:microTaskKey, color: colors[iterData%data.length]};
                    if(!(first == true)){
                        newSeries.linkedTo = ':previous';
                    } else {
                        first = false;
                    }
                    for(var iterCategories in categories) {
                        newSeries.data.push(seriesData[microTaskKey][categories[iterCategories]]);
                    }

                    series.push(newSeries);
                }

            }

            drawBarChart(series, categories);
        });
        //group them
    }

    var drawPieChart = function (platform, spam) {
        pieChart = new Highcharts.Chart({
            chart: {
                renderTo: 'annotationsPie_div',
                type: 'pie',
                width: (2*(($('.maincolumn').width() - 50)/5)),
                height: 400
            },
            title: {
                text: 'Annotations of the selected elements'
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
                valueSuffix: ''
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


    this.update = function (selectedUnits) {

        currentSelection = selectedUnits;
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
                getBarChartData(platform , type);
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
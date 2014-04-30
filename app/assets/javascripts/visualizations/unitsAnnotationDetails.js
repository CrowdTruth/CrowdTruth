function unitsAnnotationDetails() {
    var urlBase = "/api/analytics/piegraph/?match[documentType][]=annotation&";
    var currentSelection = [];
    var unitsAnnotationInfo = {};
    var spammers = [];
    var seriesBase = [];
    var pieChart = "";
    var barChart = "";




    var drawBarChart = function (series, categories) {
        //get the metrics for the units
        console.dir(series, categories);

        barChart = new Highcharts.Chart({
            chart: {
                renderTo: 'annotationsBar_div',
                type: 'column',
                width: (3*(($('.maincolumn').width() - 50)/5)),
                height: 400
            },
            title: {
                text: 'Units distributed across jobs'
            },

            xAxis: {
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
                pointFormat: '<tr><td style="color: {series.color}">{series.name}: </td>' +
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
                                console.dir(this);
                                getJobInfo(this.category);


                            }
                        }
                    }
                }
            },
            series: series
        });

    }

    var getBarChartData = function(searchSet) {
        //url to get the annotation

        var categories = [];
        var series = [];
        console.dir(categories);
        console.dir(series);
        var annotationsURL = urlBase;
        for (var iterAnn in searchSet){
            annotationsURL += 'match[_id][]=' + searchSet[iterAnn] + '&';
        }
        annotationsURL += 'group=unit_id&push[dictionary]=dictionary';

        //get the list of workers for this units
        $.getJSON(annotationsURL, function (data) {
            console.dir(data);

            for (var iterData in data) {
                var newSeries = {'name':  data[iterData]['_id'], data:[], type: 'column'};

                var seriesData = {};
                for (var iterAnnotation in data[iterData]['dictionary']) {
                    console.dir(data[iterData]['dictionary']);
                    var dictionary = data[iterData]['dictionary'][iterAnnotation];
                    for (var key in dictionary){
                        if(key in seriesData){
                            seriesData[key] += dictionary[key];
                        } else {
                            seriesData[key] = dictionary[key];
                        }
                    }
                }
                console.dir(categories);
                if (categories.length == 0) {
                    for(var key in seriesData) {
                        categories.push(key);
                    }
                }
                for(var iterCategories in categories) {
                    newSeries.data.push(seriesData[categories[iterCategories]]);
                }
                series.push(newSeries);
            }

            drawBarChart(series, categories);
        });
        //group them
    }

    var drawPieChart = function (platform, spam) {
        console.dir(platform);
        console.dir(spam);
        pieChart = new Highcharts.Chart({
            chart: {
                renderTo: 'annotationsPie_div',
                type: 'pie',
                width: (2*(($('.maincolumn').width() - 50)/5)),
                height: 400
            },
            title: {
                text: 'Annotation distribution across units'
            },
            subtitle: {
                text: 'Click to see the distribution of units per annotation'
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
                                searchSet = unitsAnnotationInfo[this.options.platform]['all'];
                                if ('type' in this.options) {
                                    searchSet = unitsAnnotationInfo[this.options.platform][this.options.type];
                                }
                                getBarChartData(searchSet);


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
        console.dir(selectedUnits);

        currentSelection = selectedUnits;
        seriesBase = [];
        urlBase = "/api/analytics/piegraph/?match[documentType][]=annotation&";
        //create the series data
        for (var indexUnits in selectedUnits) {
            urlBase += 'match[unit_id][]=' + selectedUnits[indexUnits] + '&';
            seriesBase.push({'name': selectedUnits[indexUnits], data: [],  yAxis: 0,
                type: 'column'});
        }


        platformURL = urlBase + 'group=softwareAgent_id&push[id]=_id';
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
                requests.push($.get(urlBase + 'match[softwareAgent_id][]=' + data[platformIter]['_id'] + '&group=type&addToSet=_id'));


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
                            console.dir(iterObj);
                            console.dir(responseData[iterObj])

                            categoriesData.push({name: responseData[iterObj]['_id'],
                                type: responseData[iterObj]['_id'],
                                y: responseData[iterObj].content.length,
                                color: Highcharts.Color(colors[index]).brighten(-0.01*iterObj).get(),
                                platform: data[index]['_id']});
                            unitsAnnotationInfo[data[index]['_id']][responseData[iterObj]['_id']] = responseData[iterObj].content;
                        }
                    }
                });
                drawPieChart(platformData, categoriesData);
                console.dir(unitsAnnotationInfo);
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
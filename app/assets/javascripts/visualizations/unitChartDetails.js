var getUnitPieData = function(selectedUnits){
    // Array of requests
    var requests = Array();
    var url = '/api/analytics/unitworkerpie?match[documentType][]=annotation&match[unit_id][]=';
    $.each(selectedUnits, function (key,value) {
        //console.dir(value);
        requests.push($.get(url+value));
    });

    var defer = $.when.apply($, requests);
    var results = Array();
    defer.done(function(){

        // This is executed only after every ajax request has been completed

        $.each(arguments, function(index, responseData){
            // "responseData" will contain an array of response information for each specific request

            results[selectedUnits[index]] = responseData[0];
        });

        //console.dir(results);
        unitWorkerPie(results);
    });

}

var unitWorkerPieBackup = function (data) {

    var colors = Highcharts.getOptions().colors;
    /*categories = ['Spam'];
     name = 'Browser brands';
     data = [{
     y: 55.11,
     color: colors[0],
     drilldown: {
     name: 'MSIE versions',
     categories: ['MSIE 6.0', 'MSIE 7.0', 'MSIE 8.0', 'MSIE 9.0'],
     data: [10.85, 7.35, 33.06, 2.81],
     color: colors[0]
     }
     }, {
     y: 21.63,
     color: colors[1],
     drilldown: {
     name: 'Firefox versions',
     categories: ['Firefox 2.0', 'Firefox 3.0', 'Firefox 3.5', 'Firefox 3.6', 'Firefox 4.0'],
     data: [0.20, 0.83, 1.58, 13.12, 5.43],
     color: colors[1]
     }
     }, {
     y: 11.94,
     color: colors[2],
     drilldown: {
     name: 'Chrome versions',
     categories: ['Chrome 5.0', 'Chrome 6.0', 'Chrome 7.0', 'Chrome 8.0', 'Chrome 9.0',
     'Chrome 10.0', 'Chrome 11.0', 'Chrome 12.0'],
     data: [0.12, 0.19, 0.12, 0.36, 0.32, 9.91, 0.50, 0.22],
     color: colors[2]
     }
     }, {
     y: 7.15,
     color: colors[3],
     drilldown: {
     name: 'Safari versions',
     categories: ['Safari 5.0', 'Safari 4.0', 'Safari Win 5.0', 'Safari 4.1', 'Safari/Maxthon',
     'Safari 3.1', 'Safari 4.1'],
     data: [4.55, 1.42, 0.23, 0.21, 0.20, 0.19, 0.14],
     color: colors[3]
     }
     }, {
     y: 2.14,
     color: colors[4],
     drilldown: {
     name: 'Opera versions',
     categories: ['Opera 9.x', 'Opera 10.x', 'Opera 11.x'],
     data: [ 0.12, 0.37, 1.65],
     color: colors[4]
     }
     }];*/


    // Build the data arrays
    var unitData = [];
    var platformData = [];
    var totalUnits = 0;
    var iterColors = 0;
    for(var keyData in data) {
        valueData = data[keyData];

        //get spam and nonspam data
        for(var keyUnit in valueData) {
            var valueUnit = valueData[keyUnit];
            var unitElement = Object();
            unitElement['y'] = valueUnit['content'].length;
            totalUnits +=  valueUnit['content'].length;
            unitElement['color'] = colors[iterColors];
            if(valueUnit['_id'] == true) {
                var arrayUnit = keyUnit.split("/");
                unitElement['name'] = 'Spam unit ' + arrayUnit[arrayUnit.length - 1];
                unitElement['color'] = Highcharts.Color(colors[iterColors]).brighten(-0.1).get();
            } else {
                var arrayUnit = keyUnit.split("/");
                unitElement['name'] = 'NonSpam unit ' + arrayUnit[arrayUnit.length - 1];
            }
            unitData.push(unitElement);

            var platforms = Array();
            //count platforms
            for (var iterWorkers = 0; iterWorkers < valueUnit.content.length; iterWorkers++) {
                key = valueUnit.content[iterWorkers]['platform'];
                if(key in platforms) {
                    platforms[key]++;
                } else {
                    platforms[key] = 1;
                }
            }

            var iterBrightness = 2;
            for(var keyPlatform in platforms) {
                valuePlatform = platforms[keyPlatform];
                var brightness = 0.2 - (iterBrightness / platforms.length) / 5 ;
                platformData.push({
                    name: keyPlatform,
                    y: valuePlatform,
                    color: Highcharts.Color(colors[iterColors]).brighten(0.1).get()
                });
                iterBrightness++;
            };


        };
        iterColors++;
    };


    for(var key in unitData) {
        value = unitData[key];
        value['y'] = Math.floor((value['y']/totalUnits)* 100);
    };

    for(var key in platformData) {
        value = platformData[key];
        value['y'] = Math.floor((value['y']/totalUnits)* 100);
    };

    // Create the chart
    $('#containerDetails').highcharts({
        chart: {
            type: 'pie',
            width: 600,
            height: 600
        },

        title: {
            text: 'Workers which annotated the units'
        },
        yAxis: {
            title: {
                text: 'Number of workers per unit'
            }
        },
        plotOptions: {
            pie: {
                showInLegend: true,
                shadow: false,
                allowPointSelect: true,
                center: ['50%', '50%']
            }
        },
        tooltip: {
            valueSuffix: '%'
        },
        dataLabels: {
            enabled: false
        },
        series: [{
            name: '# of workers',
            data: unitData,
            size: '40%'
           /* dataLabels: {
                formatter: function() {
                    return this.y > 5 ? this.point.name : null;
                },
                color: 'white',
                distance: -30
            }*/
        }, {
            name: '# of workers (per platforms)',
            data: platformData,
            size: '60%',
            innerSize: '40%',
            dataLabels: {
                formatter: function() {
                    // display only if larger than 1
                    return this.y > 1 ? '<b>'+ this.point.name +':</b> '+ (((this.y*totalUnits))/100).toFixed() + ' w' : null;
                }
            }
        }]
    });
};


var createUnitBarChart = function(data){
    series = {};
    dataBackup = [];
    dataBackupID = [];
    for(var s in platformWorkers ) {
        series[s] = Array();
    }
    for(var iterWorker in data) {
        worker = data[iterWorker]['_id'];
        for(var s in platformWorkers ) {
            if(platformWorkers[s].indexOf(worker)) {
                series[s].push( data[iterWorker]['count']);
            }
        }

        dataBackup.push(data[iterWorker]['count']);
        dataBackupID.push(data[iterWorker]['_id']);
    }
    dataGraph =[]
    for(var s in series){
        dataGraph.push({'name':s,'data':series[s]});
    }

    $('#barChartDetails').highcharts({
        chart: {
            type: 'column',
            width: 500,
            height: 500
        },
        title: {
            text: 'Number of annotation per worker'
        },
        xAxis: { categories:dataBackupID,labels: {
            rotation: -45,
            align: 'right'

        }
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Worker ids'
            }
        },
        tooltip: {
            headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
            pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                '<td style="padding:0"><b>{point.y:.1f} mm</b></td></tr>',
            footerFormat: '</table>',
            shared: true,
            useHTML: true
        },
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0
            }
        },
        series: [{'name': 'Workers', 'data':dataBackup}]
    });

}

var platformWorkers = {};

var unitWorkerPie = function (data) {

    var colors = Highcharts.getOptions().colors;
    var unitInfo = {};

    // Build the data arrays
    var unitData = [];
    var platformData = [];
    var totalUnits = 0;
    var iterColors = 0;
    var mapping = {};
    for(var keyData in data) {
        valueData = data[keyData];

        //get spam and nonspam data
        for(var keyUnit in valueData) {
            var valueUnit = valueData[keyUnit];
            var unitElement = Object();
            unitElement['y'] = valueUnit['content'].length;
            totalUnits +=  valueUnit['content'].length;
            unitElement['color'] = colors[iterColors];
            if(valueUnit['_id'] == true) {
                var arrayUnit = keyData.split("/");
                unitElement['name'] = 'Spam unit ' + arrayUnit[arrayUnit.length - 1];
                unitElement['color'] = Highcharts.Color(colors[iterColors]).brighten(-0.1).get();
            } else {
                var arrayUnit = keyData.split("/");
                unitElement['name'] = 'NonSpam unit ' + arrayUnit[arrayUnit.length - 1];
            }
            mapping[unitElement['name']] = {'id':keyData,'spam':valueUnit['_id']};
            unitData.push(unitElement);

            var platforms = Array();
            //count platforms
            for (var iterWorkers = 0; iterWorkers < valueUnit.content.length; iterWorkers++) {
                key = valueUnit.content[iterWorkers]['platform'];
                if(key in platforms) {
                    platforms[key]++;
                } else {
                    platforms[key] = 1;
                }
                value = valueUnit.content[iterWorkers]['crowdAgent_id'];
                if(key in platformWorkers) {
                    platformWorkers[key].push(value);
                } else {
                    platformWorkers[key] = Array();
                    platformWorkers[key].push(value);
                }

            }

            var iterBrightness = 2;
            for(var keyPlatform in platforms) {
                valuePlatform = platforms[keyPlatform];
                var brightness = 0.2 - (iterBrightness / platforms.length) / 5 ;
                platformData.push({
                    name: keyPlatform,
                    y: valuePlatform,
                    color: Highcharts.Color(colors[iterColors]).brighten(0.1).get()
                });
                iterBrightness++;
            };


        };
        iterColors++;
    };


    for(var key in unitData) {
        value = unitData[key];
        value['y'] = Math.floor((value['y']/totalUnits)* 100);
    };

    for(var key in platformData) {
        value = platformData[key];
        value['y'] = Math.floor((value['y']/totalUnits)* 100);
    };
    // Create the chart
    $('#containerDetails').highcharts({
        chart: {
            type: 'pie',
            width: 400,
            height: 400
        },
        title: {
            text: 'Workers which annotated the units'
        },
        yAxis: {
            title: {
                text: 'Number of workers per unit'
            }
        },
        plotOptions: {
            pie: {
                shadow: false,
                allowPointSelect: true,
                center: ['50%', '50%'],
                point: {
                    events: {
                        click: function () {
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
            valueSuffix: '%'
        },
        dataLabels: {
            enabled: false
        },
        series: [{
            name: '# of workers',
            data: unitData,
            size: '40%',
            dataLabels: {
                formatter: function() {
                    return this.y > 5 ? this.point.name : null;
                },
                color: 'white',
                distance: -30
            }
        }, {
            name: '# of workers (per platforms)',
            data: platformData,
            size: '60%',
            innerSize: '40%',
            dataLabels: {
                formatter: function() {
                    // display only if larger than 1
                    return this.y > 1 ? '<b>'+ this.point.name +':</b> '+ (((this.y*totalUnits))/100).toFixed() + ' w' : null;
                }
            }
        }]
    });
};


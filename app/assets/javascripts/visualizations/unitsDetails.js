function unitsDetails(category, categoryName, openModal, modalName) {
    var queryField = 'crowdAgent_id';
    var categoryPrefix = 'of';
    var unitMaps = {};
    if (category == '#job_tab') {
        queryField = 'job_id'
        categoryPrefix = 'in'
    }

    var urlBase = "/api/analytics/piegraph/?match[type][]=workerunit&";

    var infoFields = [
        {field: 'domain', name: 'domain'},
        {field: 'format', name: 'format'} ,
        {field: 'avg_clarity', name: 'avg clarity across jobs'}
    ]
    var currentSelection = [];
    var currentSelectionInfo = {};
    var unitSelection = [];
    var pieChartOptions = {};
    var unitInfo = {};
    var metrics_ids = [];
    var spam_ids = [];
    var seriesBase = [];
    var pieChart = "";
    var barChart = "";

    var createImage = function (chart, url, title, searchSet, w, h, x, y){
        var img = chart.renderer.image(url, w, h, x, y);
        img.add();
        img.css({'cursor': 'pointer'});
        img.attr({'title': 'Pop out chart'});
        img.attr("data-toggle", "tooltip");
        img.attr("style", "opacity:0.5");
        img.attr("title", title);
        img.on('click', function () {
            var hideIcon = true;
            for (var series in searchSet) {
                var series_id = searchSet[series];
                if (barChart.series[series_id].visible) {
                    hideIcon = true;
                    barChart.series[series_id].hide()
                    barChart.series[series_id].options.showInLegend = false;
                    barChart.series[series_id].legendItem = null;
                    barChart.legend.destroyItem(barChart.series[series_id]);
                    barChart.legend.render();
                } else {
                    hideIcon = false;
                    barChart.series[series_id].show();
                    barChart.series[series_id].options.showInLegend = true;
                    barChart.legend.renderItem(barChart.series[series_id]);
                    barChart.legend.render();
                }

            }
            if (hideIcon == true) {
                this.setAttribute("style", "opacity:0.5");
            } else {
                this.setAttribute("style", "opacity:1");
            }
        });
    }

    var callback = function callback($this) {

        createImage(this, '/assets/judgements.png', "Low quality judgements", spam_ids, $this.chartWidth-60,15,19,14);

        if (queryField != 'job_id')  return;

        createImage(this, '/assets/metrics.png',
            "Results of metrics before filtering the low quality annotations and workers",
            metrics_ids, $this.chartWidth-90, 16, 19, 12);

    }

    var drawPieChart = function (platform, spam, totalValue) {
        pieChart = new Highcharts.Chart({
            chart: {
                backgroundColor: {
                    linearGradient: [0, 0, 500, 500],
                    stops: [
                        [0, 'rgb(255, 255, 255)'],
                        [1, 'rgb(225, 225, 255)']
                    ]
                },
                renderTo: 'unitsPie_div',
                type: 'pie',
                width: (1.3*(($('.maincolumn').width() - 0.05*($('.maincolumn').width()))/5)),
                height: 430
            },
            title: {
                style: {
                    fontWeight: 'bold'
                },
                text: 'Platforms distribution for ' + totalValue + ' Unit(s) ' + categoryPrefix + ' the ' + currentSelection.length + ' selected ' + categoryName + '(s)'
            },
            subtitle: {
                text: 'Click a category to see the distribution of judgements per unit'
            },
            credits: {
                enabled: false
            },
            yAxis: {
                scalable:false,
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
                                searchSet = pieChartOptions[this.options.platform]['all'];
                                if ('spam' in this.options) {
                                    if (this.options.spam == true) {
                                        searchSet = pieChartOptions[this.options.platform]['spam'];
                                    } else {
                                        searchSet = pieChartOptions[this.options.platform]['nonSpam'];
                                    }
                                }

                                for (var iterData = 0; iterData < barChart.series[0].data.length; iterData++) {
                                    seriesCategory = barChart.series[0].data[iterData].category;
                                    if ($.inArray(seriesCategory, searchSet) > -1 && !this.selected) {
                                        for (var iterSeries = 0; iterSeries < barChart.series.length; iterSeries++) {
                                            barChart.series[iterSeries].data[iterData].select(true, true);
                                        }

                                    } else {
                                        for (var iterSeries = 0; iterSeries < barChart.series.length; iterSeries++) {
                                            barChart.series[iterSeries].data[iterData].select(false, true);
                                        }
                                    }
                                }


                            }
                        }
                    }
                }
            },
            tooltip: {
                useHTML: true,
                formatter: function () {
                    var seriesValue = this.key;

                    return '<p><b>' + seriesValue + ' </b></br>' + this.series.name + ' : ' +
                        this.percentage.toFixed(2) + ' % (' + this.y + '/' + this.total + ')' +
                        '</p>';
                },
                followPointer: false,
                hideDelay: 10
            },

            series: [
                {
                    name: '# of units',
                    data: platform,
                    size: '40%',
                    dataLabels: {
                        formatter: function () {
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
                        color: 'black',
                        distance: 3

                    }

                }
            ]
        });
    }

    var drawBarChart = function (series, categories) {

        var barChartOptions = {
            chart: {
                zoomType: 'x',
                renderTo: 'unitsBar_div',
                type: 'column',
                width: (3.7*(($('.maincolumn').width() - 0.05*($('.maincolumn').width()))/5)),
                height: 430,
                alignTicks: false,
                marginRight: 60,
                marginLeft: 60,
                resetZoomButton: {

                    theme:{
                        fill: '#2aabd2',
                        style:{
                            color:'white'
                        }
                    },
                    position:{
                        x: -50,
                        y: -50
                    }
                },
                backgroundColor: {
                    linearGradient: [0, 0, 500, 500],
                    stops: [
                        [0, 'rgb(235, 235, 255)'],
                        [1, 'rgb(255, 255, 255)']
                    ]
                },
                events: {
                    load: function () {
                        var chart = this,
                            legend = chart.legend;
                        for (var i = 0, len = legend.allItems.length; i < len; i++) {
                            var item = legend.allItems[i].legendItem;
                            var tooltipValue = "";
                            /*if (typeof currentSelectionInfo[legend.allItems[i].name]['tooltipLegend'] === 'string') {
                                var tooltipValue = currentSelectionInfo[legend.allItems[i].name]['tooltipLegend'];
                            } else {*/
                                for (var indexInfoKey in currentSelectionInfo[legend.allItems[i].name]['tooltipLegend']) {
                                    tooltipValue += " " + indexInfoKey + ": " +
                                        currentSelectionInfo[legend.allItems[i].name]['tooltipLegend'][indexInfoKey] + '<br/>';
                                }
                            //}

                            item.attr("data-toggle", "tooltip");
                            item.attr("title", tooltipValue);

                        }

                    }
                }
            },
            exporting: {
                buttons: {
                    resetButton: {
                        text: "View in media chart",
                        theme: {
                            fill: '#2aabd2',
                            id:"resetSelection",
                            style:{
                                color: 'white'
                            }
                        },
                        x: - (3.7*(($('.maincolumn').width() - 0.05*($('.maincolumn').width()))/5)) + 160,
                        y: 0,
                        onclick: function(e) {
                            localStorage.setItem("unitList", JSON.stringify(unitSelection));
                            $('#mediaTabOption')[0].children[0].click();
                        }
                    }
                }
            },
            title: {
                style: {
                    fontWeight: 'bold'
                },
                text: 'Judgements on ' + categories.length + ' Unit(s) ' + categoryPrefix + ' ' +
                    currentSelection.length + ' Selected ' + categoryName + '(s)'
            },
            subtitle: {
                text: 'Select an area to zoom. To see detailed information select individual units.From legend select/deselect features.'
            },
            credits: {
                enabled: false
            },
            xAxis: {
                tickInterval: Math.ceil( categories.length/35),
                title: {
                    text: 'Unit ID'
                },
                categories: categories,
                labels: {
                    formatter: function () {
                        var arrayUnit = this.value.split("/");
                        var value = arrayUnit[arrayUnit.length - 1];

                        return value;
                    },
                    rotation: -45,
                    align: 'right'
                },
                events: {
                    setExtremes: function (event) {
                        var min = 0;
                        if (event.min != undefined) {
                            min = event.min;
                        }
                        var max = barChart.series[0].data.length
                        if (event.max != undefined) {
                            max = event.max;
                        }
                        // chart.yAxis[0].options.tickInterval
                        barChart.xAxis[0].options.tickInterval = Math.ceil((max - min) / 20);
                    },
                    afterSetExtremes: function (event) {
                        var graph = '';
                        var interval = (event.max - event.min + 1);
                        if (interval == barChart.series[0].data.length) {
                            title = 'Judgements on ' + barChart.series[0].data.length + ' Unit(s) ' +
                                categoryPrefix + ' ' + currentSelection.length + ' Selected ' + categoryName + '(s)'
                        } else {
                            title = 'Judgements on ' + categoryPrefix + ' ' + interval.toFixed(0) +
                                '/' + barChart.series[0].data.length + ' Unit(s) ' +
                                categoryPrefix + ' ' + currentSelection.length + ' Selected ' + categoryName + '(s)'
                        }
                        barChart.setTitle({text: title});
                    }
                }
            },

            legend: {
                maxHeight: 100,

                labelFormatter: function () {

                    var arrayName = this.name.split("/");
                    var value = arrayName[arrayName.length - 1];
                    if (arrayName.length > 1) {
                        var indexHideStr = value.indexOf('_hide')
                        if (indexHideStr != -1) {
                            return  ' # of low quality judgements of ' + categoryName + ' ' + value.substring(0, indexHideStr);
                        } else {
                            return  ' # of high quality judgements of ' + categoryName + ' ' + value ;
                        }

                    } else {
                        return categoryName + ' ' + value;
                    }
                }
            },
            yAxis: [
                {
                    min: 0,
                    showEmpty: false,
                    offset:0,
                    labels: {
                        formatter: function () {
                            return this.value;
                        },
                        style: {
                            color: '#274B6D'
                        }
                    },
                    gridLineColor:  '#274B6D',
                    startOnTick: false,
                    endOnTick: false,
                    title: {
                        text: '# judgements per unit',
                        style: {
                            color: '#274B6D'
                        }
                    }
                }
            ],
            tooltip: {
                hideDelay: 10,
                useHTML: true,
                formatter: function () {
                    var arrayID = this.x.split("/");
                    var id = arrayID[arrayID.length - 1];
                    var s = '<div style="white-space:normal;"><b>Unit ' + id + '</b><br/>';
                    for (var index in infoFields) {
                        var field = infoFields[index]['field'];
                        var pointValue = unitInfo[this.x][field];
                        if (pointValue != undefined && !(typeof pointValue === 'string') && !(pointValue % 1 === 0)) {
                            pointValue = pointValue.toFixed(2);
                        }
                        s += '' + infoFields[index]['name'] + ' : ' + pointValue + '<br/>';
                    }


                    var seriesOptions = {};
                    $.each(this.points, function (i, point) {
                        var pointValue = point.y
                        if (pointValue != undefined && !(typeof pointValue === 'string') && !(pointValue % 1 === 0)) {
                            pointValue = point.y.toFixed(2);
                        }
                        var id = point.series.options.categoryID;

                        var name = point.series.name;
                        var arrayName = id.split('/');
                        var shortName = arrayName[arrayName.length - 1];
                        if (point.series.type == 'column') {
                            var indexHideStr = point.series.name.indexOf('_hide')
                            if (indexHideStr != -1) {
                                name = '# of low quality judgements on this unit';
                            } else {
                                name = '# of high quality judgements on this unit ';
                            }
                        } else {
                            name = name.substr(shortName.length + 1, name.length) + ' on this unit';
                        }

                        var line = '<tr><td></td><td style="color: ' + point.series.color +
                            ';text-align: left">&nbsp;&nbsp;' + name + ':</td>' +
                            '<td style="text-align: right">' + pointValue + '</td></tr>';

                        if (!(id in seriesOptions)) {
                            seriesOptions[id] = [];
                        }
                        seriesOptions[id].push(line);
                    });

                    s += '<div style="border:1px solid black;text-align: center"></div>'
                    //s += '<div style="text-align: center"><b>' + categoryName + '(s) of unit' + '</b></div>'
                    s += '<table calss="table table-condensed">';
                    for (var item in seriesOptions) {
                        if (unitMaps[this.x].indexOf(item) == -1) continue;
                        var arrayName = item.split('/');
                        var id = arrayName[arrayName.length - 1];
                        s += '<tr><td></td><td style="text-align: left"><b>' + categoryName + ' ' + id + ':</b></td></tr>';
                        if ('tooltipChart' in currentSelectionInfo[item]) {
                            for (var tooltipInfo in currentSelectionInfo[item]['tooltipChart']) {
                                pointValue = currentSelectionInfo[item]['tooltipChart'][tooltipInfo];
                                if (pointValue != undefined && !(typeof pointValue === 'string') && !(pointValue % 1 === 0)) {
                                    pointValue = pointValue.toFixed(2);
                                }
                                s += '<tr><td ></td><td style="text-align: left">&nbsp;&nbsp;' + tooltipInfo + ':</td>' +
                                    '<td style="text-align: right">' + pointValue + '</td></tr>';
                            }
                        }

                        for (var li in seriesOptions[item]) {
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
                    minPointLength: 2
                },
                column: {
                    stacking: 'normal',
                    states: {
                        select: {
                            color: null,
                            borderWidth: 3,
                            borderColor: 'Blue'
                        }
                    },

                    point: {
                        events: {
                            contextmenu: function (e) {
                                urlBase = "";

                                for (var indexUnits in currentSelection) {
                                    urlBase += 'match[' + queryField + '][]=' + currentSelection[indexUnits] + '&';
                                }
                                anchorModal = $('<a class="testModal"' +
                                    'data-modal-query="unit=' + this.category + '&' + urlBase +
                                    '" data-api-target="/api/analytics/unit?" ' +
                                    'data-target="' + '#modalIndividualUnit' + '" data-toggle="tooltip" data-placement="top" title="" ' +
                                    'data-original-title="Click to see the individual worker page">6345558 </a>');
                                //$('body').append(anchorModal);
                                openModal(anchorModal, category);

                            },
                            click: function () {
                                for (var iterSeries = 0; iterSeries < barChart.series.length; iterSeries++) {
                                    barChart.series[iterSeries].data[this.x].select(null,true);
                                }

                                if($.inArray(this.category, unitSelection) > -1) {
                                    unitSelection.splice( $.inArray(this.category, unitSelection), 1 );
                                } else {
                                    unitSelection.push(this.category)
                                }


                                var buttonLength = barChart.exportSVGElements.length;
                                if(unitSelection.length == 0) {
                                    barChart.exportSVGElements[buttonLength - 2].hide();
                                } else {
                                    barChart.exportSVGElements[buttonLength - 2].show();
                                }


                            }
                        }
                    }
                }
            },
            series: series
        };
        if (queryField == 'job_id') {
            barChartOptions.yAxis.push({
                min: 0,
                offset: 0,
                showEmpty: false,
                labels: {
                    formatter: function () {
                        return this.value;
                    },
                    style: {
                        color: '#4897F1'
                    }
                },
                gridLineColor:  '#4897F1',
                startOnTick: false,
                endOnTick: false,
                opposite: true,
                title: {
                    text: 'avg clarity',
                    style: {
                        color: '#4897F1'
                    }
                }})
        }
        barChart = new Highcharts.Chart(barChartOptions, callback);

    }

    var getWorkersData = function (url) {
        //make a check and see which units have workers?

        var categories = [];
        var colorMaps = {};
        var seriesMaps = {};
        var series = seriesBase;
        var colors = Highcharts.getOptions().colors;
        workersURL = url + 'project[' + queryField + ']=' + queryField +
            '&project[spam]=spam&group=unit_id&push[spam]=spam&push[' + queryField + ']=' + queryField;
        for (var iterSeries in series) {
            series[iterSeries]['data'] = [];
        }

        //get the list of workers for this units
        $.getJSON(workersURL, function (data) {
            for (var iterData in data) {
                categories.push(data[iterData]['_id']);
                unitMaps[data[iterData]['_id']] = []
                for (var iterSeries in series) {
                    if (iterSeries % 2 == 1)
                        continue
                    var unit_id = series[iterSeries]['name'];
                    var nonSpamValue = 0;
                    var spamValue = 0;
                    for (var iterUnits in data[iterData][queryField]) {
                        if (data[iterData][queryField][iterUnits] == unit_id) {
                            if (data[iterData]['spam'][iterUnits]) {
                                spamValue++
                            } else {
                                nonSpamValue++
                            }
                            unitMaps[data[iterData]['_id']].push(unit_id);
                        }
                    }

                    var nextSeries = parseInt(iterSeries) + 1
                    series[iterSeries]['data'].push(nonSpamValue);
                    series[nextSeries]['data'].push(spamValue);
                }
            }


            for (var iterSeries in series) {

                if (iterSeries % 2 == 1)
                    continue

                var nextSeries = parseInt(iterSeries) + 1
                series[iterSeries]['color'] = Highcharts.Color(colors[(iterSeries / 2) % (colors.length)]).get();
                series[iterSeries]['type'] = 'column';
                series[iterSeries]['categoryID'] = series[iterSeries]['name'];
                colorMaps[series[iterSeries]['name']] = series[iterSeries]['color'];
                seriesMaps[series[iterSeries]['name']] = iterSeries;

                series[nextSeries]['type'] = 'column';
                series[nextSeries]['color'] = Highcharts.Color(series[iterSeries]['color']).brighten(0.2).get();
                series[nextSeries]['categoryID'] = series[iterSeries]['name'];
                series[nextSeries]['borderWidth'] = 1;
                series[nextSeries]['showInLegend'] = false;
                series[nextSeries]['visible'] = false;
                series[nextSeries]['borderColor'] = 'red';
                currentSelectionInfo[series[iterSeries]['name'] + '_hide'] = currentSelectionInfo[series[iterSeries]['name']]
            }

            //get worker's info
            var urlUnitInfo = '/api/analytics/metrics/?&'
            for (var indexUnits in currentSelection) {
                urlUnitInfo += 'match[' + queryField + '][]=' + currentSelection[indexUnits] + '&';
            }
            urlUnitInfo += 'match[type][]=workerunit&project[unit_id]=unit_id&push[unit_id]=unit_id' +
                '&metrics[]=avg_clarity&metrics[]=domain&metrics[]=format';
            $.getJSON(urlUnitInfo, function (data) {
                for (var iterData in data) {
                    unitInfo[data[iterData]['_id']] = data[iterData];
                }

                if (queryField == 'job_id') {
                    //get the metrics for jobs
                    var urlJobsInfo = '/api/v1/?field[type]=job&only[]=metrics.units.withoutSpam&&only[]=metrics.units.withSpam&';

                    for (var indexUnits in currentSelection) {
                        urlJobsInfo += 'field[_id][]=' + currentSelection[indexUnits] + '&';
                    }

                    $.getJSON(urlJobsInfo, function (data) {
                        for (var iterData in data) {
                            if (!('metrics' in data[iterData])) {
                                continue;
                            }
                            var metrics_before_filter = data[iterData]['metrics']['units']['withSpam'];
                            var metrics_after_filter = data[iterData]['metrics']['units']['withoutSpam'];
                            var job_id = data[iterData]['_id'];
                            var arrayID = job_id.split("/");
                            var value = arrayID[arrayID.length - 1];

                            var avg_clarity  = {'name': value + " avg clarity after filter",
                                data: [],
                                categoryID: job_id,
                                type: 'spline',
                                lineWidth: 2,
                                visible: false,
                                color: Highcharts.Color(colorMaps[job_id]).brighten(0.3).get(),
                                yAxis: 1,
                                'dashStyle': 'shortdot'};

                            var avg_clarity_spam  = {'name': value + " avg clarity before filter",
                                data: [],
                                showInLegend : false,
                                categoryID: job_id,
                                type: 'spline',
                                lineWidth: 0.5,
                                visible: false,
                                color: Highcharts.Color(colorMaps[job_id]).brighten(0.3).get(),
                                yAxis: 1,
                                'dashStyle': 'shortdot'};


                            var position = 0;
                            for (var iterSeries in series) {
                                if (series[iterSeries].name == job_id && series[iterSeries].type == 'column') {
                                    position = iterSeries;
                                    break;
                                }
                            }

                            for (var agentIDIter in categories) {
                                var agentID = categories[agentIDIter];
                                if (agentID in metrics_before_filter) {
                                    avg_clarity_spam['data'].push(metrics_before_filter[agentID]['avg']['max_relation_Cos'])
                                    avg_clarity['data'].push(metrics_after_filter[agentID]['avg']['max_relation_Cos'])
                                } else {
                                    avg_clarity_spam['data'].push(0)
                                    avg_clarity['data'].push(0)
                                }

                            }
                            currentSelectionInfo[value + " avg clarity after filter"] = {}
                            currentSelectionInfo[value + " avg clarity after filter"]['tooltipLegend'] = {}
                            currentSelectionInfo[value + " avg clarity before filter"] = {}
                            currentSelectionInfo[value + " avg clarity before filter"]['tooltipLegend'] = {}
                            currentSelectionInfo[value + " avg clarity after filter"]['tooltipLegend']['CrowdTruth Average Unit Clarity'] = "" +
                                "the value is defined as the maximum unit annotation score achieved on any annotation for that unit. " +
                                "High agreement over the annotations is represented by high cosine scores, indicating a clear unit. " +
                                "Click to select/deselect."
                            currentSelectionInfo[value + " avg clarity before filter"]['tooltipLegend']['CrowdTruth Average Unit Clarity'] = "" +
                                "the value is defined as the maximum unit annotation score achieved on any annotation for that unit. " +
                                "High agreement over the annotations is represented by high cosine scores, indicating a clear unit. " +
                                "Click to select/deselect."


                            series.splice(position, 0, avg_clarity, avg_clarity_spam);
                        }

                        metrics_ids = [];
                        spam_ids = [];
                        for (var series_id in series) {
                            var series_name = series[series_id]['name'];
                            if (series_name.indexOf("_hide") != -1) {
                                spam_ids.push(series_id)
                            }
                            if (series_name.indexOf("before filter") != -1) {
                                metrics_ids.push(series_id)
                            }
                        }
                        drawBarChart(series, categories);
                        var buttonLength = barChart.exportSVGElements.length;
                        barChart.exportSVGElements[buttonLength - 2].hide();
                    });

                } else {
                    spam_ids = [];
                    for (var series_id in series) {
                        var series_name = series[series_id]['name'];
                        if (series_name.indexOf("_hide") != -1) {
                            spam_ids.push(series_id)
                        }
                    }
                    drawBarChart(series, categories);
                    var buttonLength = barChart.exportSVGElements.length;
                    barChart.exportSVGElements[buttonLength - 2].hide();
                }

            });
        });

    }

    this.update = function (selectedUnits, selectedInfo) {
        pieChartOptions = {};
        unitInfo = {};
        unitSelection = [];
        seriesBase = [];

        if (selectedUnits.length == 0) {
            $('#unitsBar_div').hide();
            $('#unitsPie_div').hide();
            return;
        } else {
            $('#unitsBar_div').show();
            $('#unitsPie_div').show();
        }
        currentSelection = selectedUnits;
        currentSelectionInfo = selectedInfo
        seriesBase = [];
        urlBase = "/api/analytics/piegraph/?match[type][]=workerunit&";
        //create the series data
        for (var indexUnits in selectedUnits) {
            urlBase += 'match[' + queryField + '][]=' + selectedUnits[indexUnits] + '&';
            seriesBase.push({'name': selectedUnits[indexUnits], data: []});
            seriesBase.push({'name': selectedUnits[indexUnits] + '_hide', data: []});
        }

        getWorkersData(urlBase);
        //get the workers grouped by platform and spam, nonspam
        platformURL = urlBase + 'project[unit_id]=unit_id&group=softwareAgent_id&addToSet=unit_id';
        $.getJSON(platformURL, function (data) {
            var platformData = [];
            var spamData = [];
            var requests = [];
            var iterColors = 0;
            var totalNumber = 0;
            var colors = ['#FFC640', '#A69C00'];


            for (var platformIter in data) {
                var platformID = data[platformIter]['_id'];
                platformData.push({name: platformID, y: data[platformIter]['content'].length,
                    color: Highcharts.Color(colors[platformIter]).brighten(0.07).get(),
                    platform: platformID});
                pieChartOptions[platformID] = {};
                pieChartOptions[platformID]['all'] = data[platformIter]['content'];
                totalNumber += data[platformIter]['content'].length
            }

            drawPieChart(platformData, spamData, totalNumber);


        });
    }

    this.createUnitsDetails = function () {
        //get filtered units
    }

}
function unitsWorkerDetails(category, openModal) {
    var urlBase = "/api/analytics/piegraph/?match[documentType][]=annotation&";
    var unitsWorkersInfo = {};
    var currentSelection = [];
    var queryFields = {'#twrex-structured-sentence_tab': 'unit_id', '#fullvideo_tab': 'unit_id', '#job_tab': 'job_id'};
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
                height: 400
            },
            title: {
                text: 'Workers of the selected elements'
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
                                    if(this.options.spam == true) {
                                        searchSet = unitsWorkersInfo[this.options.platform]['spam'];
                                    } else {
                                        searchSet = unitsWorkersInfo[this.options.platform]['nonSpam'];
                                    }
                                }

                                for (var iterData = 0; iterData < barChart.series[0].data.length; iterData++) {
                                    seriesCategory = barChart.series[0].data[iterData].category;
                                    if($.inArray(seriesCategory, searchSet) > -1) {
                                        for (var iterSeries = 0; iterSeries < barChart.series.length; iterSeries++) {
                                            barChart.series[iterSeries].data[iterData].select(null,true);
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
                valueSuffix: ''
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
                height: 400
            },
            title: {
                text: 'Annotations of workers'
            },

            xAxis: {
                title :{
                    text: 'Worker ID'
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

    var getWorkerInfo = function(crowdAgentID){
        var result = {'title':'','body':''};
        result['title'] = '<h4><b>CF worker: </b>' + crowdAgentID + ' - Annotation information</h4><h5><p>(internal worker quality:(TODO), platform worker quality:(TODO))</p></h5>';
        //get all the annotated sentences
        var url = urlBase;
        var urlJobSet = '/api/analytics/aggregate/?&match[documentType]=annotation&';

        for (var iterSelection in currentSelection) {
            url += 'match[unit_id][]='+currentSelection[iterSelection] + '&';
            urlJobSet += 'match[unit_id][]='+currentSelection[iterSelection] + '&';
        }
        url += 'match[crowdAgent_id]=' + crowdAgentID + '&';
        urlJobSet += 'match[crowdAgent_id]=' + crowdAgentID + '&';

        url += 'group=unit_id&push[jobs]=job_id&push[annotations]=content&push[spam]=spam';
        urlJobSet += 'sort[created_at]=1&project[jobs]=job_id&addToSet=jobs';
        $.getJSON(url, function (data) {
            var sentenceData = data;
            //get all the job ids

            $.getJSON(urlJobSet, function (data) {
                //get info about individual jobs
                var urlJobInfo = "/api/analytics/piegraph/?match[documentType][]=job&";
                var jobsDic = {};
                for( var iterJob in data['content']){
                    urlJobInfo += 'match[_id][]=' + data['content'][iterJob] + '&';
                }
                urlJobInfo += '&group=_id&push[type]=type&' +
                    'push[avgCosineScore]=metrics.aggWorker.mean.worker_cosine&' +
                    'push[avgAgreementScore]=metrics.aggWorker.mean.avg_worker_agreement&'+
                    'push[workerAgreementScore]=metrics.workers.withFilter.' + crowdAgentID + '.avg_worker_agreement&'+
                    'push[workerCosineScore]=metrics.workers.withFilter.' + crowdAgentID + '.worker_cosine';

                $.getJSON(urlJobInfo, function (data) {
                    for (var iterjobInfo in data) {
                        jobsDic[data[iterjobInfo]['_id']] = data[iterjobInfo];
                    }

                    result['body'] = '<dl class="dl-horizontal">';
                    for( var iterSent in sentenceData) {
                        result['body'] += '<ul><h4>'
                        result['body'] += 'Sentence: '+sentenceData[iterSent]['_id'] +'</h4>';
                        for (var iterAnn in sentenceData[iterSent]['annotations']) {
                            jobID = sentenceData[iterSent]['jobs'][iterAnn];
                            result['body'] += '<li>JobID: ' + jobID + '<dl class="dl-horizontal">';
                            result['body'] += '<dt>Job type: </dt><dd>' + jobsDic[jobID]['type'] + '</dd>';
                            result['body'] += '<dt>Avg job worker agreement:</dt><dd>' + jobsDic[jobID]['avgAgreementScore'][0] + '</dd>';
                            result['body'] += '<dt>Worker agreement score:</dt><dd>' + jobsDic[jobID]['workerAgreementScore'][0] + '</dd>';
                            result['body'] += '<dt>Avg job cosine value:</dt><dd>' + jobsDic[jobID]['avgCosineScore'][0] + '</dd>';
                            result['body'] += '<dt>Worker cosine score:</dt><dd>' + jobsDic[jobID]['workerCosineScore'][0] + '</dd>';
                            result['body'] += '<dt>Worker annotation:</dt><dd>' + sentenceData[iterSent]['annotations'][iterAnn]['direction']

                                + '</dd>';
                            result['body'] += '<dt>Marked as spam:</dt><dd>' + sentenceData[iterSent]['spam'][iterAnn] + '</dd>';

                            result['body'] += '</dl></li>';
                        }
                        result['body'] += '</ul>'
                    }
                    result['body'] += '</dl>';
                    $('#myModal .modal-title').html(result['title']);
                    $('#myModal .modal-body').html(result['body']);
                    $('#myModal').modal('show');

                });
            });

        });

        //get worker information

    }

    var getWorkersData = function (url) {
        //make a check and see which units have workers?

        var categories = [];
        var series = seriesBase;
        workersURL = url + 'project[' + queryFields[category] + ']=' + queryFields[category] +
            '&group=crowdAgent_id&push[' + queryFields[category] + ']=' + queryFields[category];
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

    this.update = function (selectedUnits) {
        currentSelection = selectedUnits;
        seriesBase = [];
        urlBase = "/api/analytics/piegraph/?match[documentType][]=annotation&";
        //create the series data
        for (var indexUnits in selectedUnits) {
            urlBase += 'match['+ queryFields[category] + '][]=' + selectedUnits[indexUnits] + '&';
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
                                    spam: true,
                                    y: content.length,
                                    color: Highcharts.Color(colors[index]).brighten(-0.05).get(),
                                    platform: data[index]['_id']});
                                unitsWorkersInfo[data[index]['_id']]['spam'] = content;
                            } else {
                                spamData.push({name: 'high quality',
                                    spam: false,
                                    y: content.length,
                                    color: colors[index],
                                    platform: data[index]['_id']});
                                unitsWorkersInfo[data[index]['_id']]['nonSpam'] = content;
                            }
                        }
                        if(commonWorkers.length > 0) {
                            spamData.push({name: 'potential spam',
                                spam: true,
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
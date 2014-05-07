function unitsJobDetails(category) {
    var urlBase = "/api/analytics/piegraph/?match[documentType][]=annotation&";
    var queryFields = {'#twrex-structured-sentence_tab': 'unit_id', '#fullvideo_tab': 'unit_id', '#crowdagents_tab': 'crowdAgent_id'};
    var currentSelection = [];
    var unitsJobsInfo = {};
    var spammers = [];
    var seriesBase = [];
    var pieChart = "";
    var barChart = "";

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
        var series = seriesBase;
        jobsURL = url + 'project[' + queryFields[category] + ']=' + queryFields[category] +
            '&group=job_id&push[' + queryFields[category] + ']=' + queryFields[category];
        for (var iterSeries in series) {
            series[iterSeries]['data'] = [];
        }

        //get the list of workers for this units
        $.getJSON(jobsURL, function (data) {
            data.sort(compare);
            var urlJobMatchStr = "";
            for (var iterData in data) {
                urlJobMatchStr += "&match[_id][]=" + data[iterData]['_id'];
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
            var requests = [];
            var seriesName = 'clarity_';
            if (queryFields[category] == 'crowdAgent_id') {
                seriesName = 'agreement_';
            }
            for (var iterSeries in series) {
                categories.push(seriesName + series[iterSeries]['name']);

                var specificQuery = '&project[metric]=metrics.units.withoutSpam.' + series[iterSeries]['name'] + '.max_relation_Cos.avg';
                if (queryFields[category] == 'crowdAgent_id') {
                    specificQuery = '&project[metric]=metrics.workers.withFilter.' + series[iterSeries]['name'] + '.avg_worker_agreement.avg';
                }

                var urlJobData = '/api/analytics/aggregate/?match[documentType]=job&sort[created_at]=1' + urlJobMatchStr +
                    specificQuery +
                    '&project[id]=_id' +
                    '&push[id]=id&push[metric]=metric';
                requests.push($.get(urlJobData));
            }
            var defer = $.when.apply($, requests);
            defer.done(function () {

                if (arguments[1] == 'success'){
                    series.push({'name': seriesName + series[0]['name'], data: arguments[0]['metric'],  yAxis: 1,
                        type: 'spline'});
                } else {
                    $.each(arguments, function (index, responseData) {
                        series.push({'name': seriesName + series[index]['name'], data: responseData[0]['metric'],  yAxis: 1,
                            type: 'spline'});
                    });
                }
                drawBarChart(series, categories);
            });
        });
    }

    var drawBarChart = function (series, categories) {
        //get the metrics for the units


        barChart = new Highcharts.Chart({
            chart: {
                zoomType: 'xy',
                renderTo: 'jobsBar_div',
                type: 'column',
                width: (3*(($('.maincolumn').width() - 50)/5)),
                height: 400
            },
            title: {
                text: 'Annotations of jobs'
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
            yAxis: [{
                min: 0,
                title: {
                    text: '# annotation per unit'
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
                crosshairs: true,
                shared: true,
                useHTML: true,
                headerFormat: '<b>Job {point.key}</b></br><p>(Click for details)</p><table>',
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
                                getJobInfo(this.category);


                            }
                        }
                    }
                }
            },
            series: series
        });

    }
    var getJobInfo = function(jobID){
        var result = {'title':'','body':''};
        result['title'] = '<h4><b>CF Job: </b>' + jobID + '</h4><h5></h5>';
        //get all the annotated sentences
        var url = urlBase;
        var urlJobSet = '/api/analytics/aggregate/?&match[documentType]=annotation&';

        for (var iterSelection in currentSelection) {
            url += 'match[unit_id][]='+currentSelection[iterSelection] + '&';
            urlJobSet += 'match[unit_id][]='+currentSelection[iterSelection] + '&';
        }
        url += 'match[job_id]=' + jobID + '&';
        urlJobSet += 'match[job_id]=' + jobID + '&';

        url += 'group=unit_id&push[agents]=crowdAgent_id&push[annotations]=content&push[spam]=spam';
        urlJobSet += 'sort[created_at]=1&project[jobs]=job_id&addToSet=jobs';

        $.getJSON(url, function (data) {
            var sentenceData = data;
            //get all the job ids

            $.getJSON(urlJobSet, function (data) {
                //get info about individual jobs

                result['body'] = '<dl class="dl-horizontal">';
                for( var iterSent in sentenceData) {
                    result['body'] += '<ul><h4>'
                    result['body'] += 'Sentence: '+sentenceData[iterSent]['_id'] +'</h4>';
                    for (var iterAnn in sentenceData[iterSent]['annotations']) {
                        result['body'] += '<li>Worker ID: ' + sentenceData[iterSent]['agents'][iterAnn] + '<dl class="dl-horizontal">';
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

        //get worker information

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
                text: 'Jobs of the selected elements'
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
            plotOptions: {
                pie: {

                    shadow: false,

                    allowPointSelect: true,
                    center: ['50%', '50%'],
                    point: {
                        events: {
                            click: function () {
                                searchSet = unitsJobsInfo[this.options.platform]['all'];
                                if ('type' in this.options) {
                                    searchSet = unitsJobsInfo[this.options.platform][this.options.type];
                                }

                                for (var iterData = 0; iterData < barChart.series[0].data.length; iterData++) {
                                    category = barChart.series[0].data[iterData].category;
                                    if($.inArray(category, searchSet) > -1) {
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


    this.update = function (selectedUnits) {

        currentSelection = selectedUnits;
        seriesBase = [];
        urlBase = "/api/analytics/piegraph/?match[documentType][]=annotation&";
        //create the series data
        for (var indexUnits in selectedUnits) {
            urlBase += 'match['+ queryFields[category] + '][]=' + selectedUnits[indexUnits] + '&';
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
                unitsJobsInfo[platformID] = {};
                unitsJobsInfo[platformID]['all'] = data[platformIter]['content'];
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
                            unitsJobsInfo[data[index]['_id']][responseData[iterObj]['_id']] = responseData[iterObj].content;
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
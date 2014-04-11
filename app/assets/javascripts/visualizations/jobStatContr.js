<<<<<<< HEAD
//write resource service class


//inject resourceSvc in this controller ? check if is the right definition for minimizations
angular.module("jobanalytics").controller("jobStatContr", function ($scope, $http, $templateCache, $q) {

    $scope.chartSeriesOptions = {
        'workers': {'legend': '#A63800',
            'avg': {'color': '#A63800',
                'field': 'workerCount',
                'value': 0},
            'noSpam': {'color': '#BF6030',
                'field': 'workerCount'},
            'spam': {'color': '#FF8040',
                'field': 'metrics.spammers.count'}
        },
        'units': {'legend': '#0D58A6',
            'avg': {'color': '#0D58A6',
                'field': 'unitsCount',
                'value': 0},
            'noSpam': {'color': '#26517C',
                'field': 'unitsCount'},
            'spam': {'color': '#689CD2',
                'field': 'metrics.filteredUnits.count'}
        },
        'annotations': {'legend': '#00AA72',
            'avg': {'color': '#00AA72',
                'field': 'annotationsCount',
                'value': 0},
            'noSpam': {'color': '#207F60',
                'field': 'annotationsCount'},
            'spam': {'color': '#60D4AE',
                'field': 'metrics.filteredUnits.count'}
        },
        'payment': {'legend': '#E00000 ', 'field': 'projectedCost', color: '#E00000 '}
    };

    $scope.chartGeneralOptions = {
        chart: {
            zoomType: 'xy',
            renderTo: 'statisticsChart',
            marginBottom: 70
        },
        title: {
            text: 'Comparison with jobs with the same template',

        },
        tooltip: {
            shared: true,
            useHTML: true,
            headerFormat: '<b>Job {point.key}</b><table>',
            pointFormat: '<tr><td style="color: {series.color}">{series.name}: </td>' +
                '<td style="text-align: right"><b>{point.y}</b></td></tr>',
            footerFormat: '</table>',
            valueDecimals: 2
        },

        plotOptions: {
            series: {
                stacking: 'normal',
                states: {
                    hover: {
                        enabled: true,
                        lineWidth: 5,
                        brightness: 0.4
                    }
                },
                cursor: 'pointer',
                point: {
                    events: {
                        click: function () {
                            $scope.selectedJob = this.category;
                            $('#selectJobID').click();
                        }
                    }
                }

            }
        }
    };

    $scope.hoverInChart = function (position) {
        for (var iterSeries = 0; iterSeries < $scope.chart.series.length; iterSeries++) {
            $scope.chart.series[iterSeries].data[position].setState('hover');
        }
    }

    $scope.hoverOutChart = function (position) {
        for (var iterSeries = 0; iterSeries < $scope.chart.series.length; iterSeries++) {
            $scope.chart.series[iterSeries].data[position].setState();
        }
    }

    $scope.clearAll = function () {
        for (var jobID in $scope.jobList) {
            $scope.jobList[jobID]['selected'] = false;
        }
    }

    $scope.analyze = function () {
        var selectedJobs = [];
        for (var jobID in $scope.jobList) {
            if ($scope.jobList[jobID]['selected'] == true)
                selectedJobs.push($scope.jobList[jobID]['name']);
        }

        if (selectedJobs.length == 0) {
            alert("Please select the jobs first");
        } else {
            window.location.href = '/analyze/view?jobs=' + selectedJobs.join();
        }
    }


    $scope.selectJobID = function () {
        $scope.jobList[$scope.selectedJob]['selected'] = true;
    }


    $scope.createStatChart = function () {

        var compare = function (a, b) {

            a_array = a._id.split("/");
            b_array = b._id.split("/");
            a_id = a_array[a_array.length - 1];
            b_id = b_array[b_array.length - 1];
            return a_id - b_id;
        };

        var updateChartOptions = function () {
            console.dir(Object.keys($scope.templates).join());
            $scope.chartGeneralOptions.subtitle = {
                text: Object.keys($scope.templates).join()

            };

            var xAxisValues = Object.keys($scope.jobList);
            console.dir(xAxisValues.sort());

            $scope.chartGeneralOptions.xAxis = {categories: [17, 18, 19, 20]};//xAxisValues.sort();

            //create the yAxis and series option fields
            $scope.chartGeneralOptions.yAxis = [];
            $scope.chartGeneralOptions.series = [];


            for (var key in $scope.chartSeriesOptions) {
                var yAxisSettings = {
                    gridLineWidth: 0,
                    labels: {
                        formatter: function () {
                            return this.value;
                        },
                        style: {
                            color: $scope.chartSeriesOptions[key]['legend']
                        }
                    },
                    title: {
                        text: key,
                        style: {
                            color: $scope.chartSeriesOptions[key]['legend']
                        }
                    },
                    opposite: true

                };
                if (key == 'payment') {
                    yAxisSettings['opposite'] = false;
                    var paymentSeries = {
                        name: key,
                        color: $scope.chartSeriesOptions[key]['color'],
                        yAxis: $scope.chartGeneralOptions.yAxis.length,
                        type: 'spline',
                        data: $scope.chartSeriesOptions[key]['data'],
                        tooltip: {
                            valueSuffix: ' cents'
                        }
                    }
                    $scope.chartGeneralOptions.yAxis.push(yAxisSettings);
                    $scope.chartGeneralOptions.series.push(paymentSeries);
                    continue;
                }

                //if not payment option
                //create an avg, spam and no spam series
                var spamSeries = {
                    name: '# spam ' + key,
                    color: $scope.chartSeriesOptions[key]['spam']['color'],
                    type: 'column',
                    yAxis: $scope.chartGeneralOptions.yAxis.length,
                    stack: key,
                    visible: false,
                    data: $scope.chartSeriesOptions[key]['spam']['data']
                }

                //create an avg, spam and no spam series
                var noSpamSeries = {
                    name: '# ' + key + '(no spam)',
                    color: $scope.chartSeriesOptions[key]['noSpam']['color'],
                    type: 'column',
                    yAxis: $scope.chartGeneralOptions.yAxis.length,
                    stack: key,
                    visible: false,
                    data: $scope.chartSeriesOptions[key]['noSpam']['data']
                }

                console.dir($scope.chartSeriesOptions[key]['avg']['value']);
                console.dir(xAxisValues.length);
                var avgValue = $scope.chartSeriesOptions[key]['avg']['value'] / xAxisValues.length;
                var avgSeries = {
                    name: 'avg # ' + key,
                    type: 'spline',
                    visible: false,
                    color: $scope.chartSeriesOptions[key]['avg']['color'],
                    yAxis: $scope.chartGeneralOptions.yAxis.length,
                    data: Array.apply(null, new Array(xAxisValues.length)).map(Number.prototype.valueOf, avgValue),
                    marker: {
                        enabled: false
                    },
                    dashStyle: 'shortdot'
                }

                $scope.chartGeneralOptions.yAxis.push(yAxisSettings);
                $scope.chartGeneralOptions.series.push(spamSeries);
                $scope.chartGeneralOptions.series.push(noSpamSeries);
                $scope.chartGeneralOptions.series.push(avgSeries);
            }

        }
        //create the url for http request
        // similar to:
        ////field[documentType]=job&field[domain]=medical&field[template][]=relation_direction/relation_direction_1&field[template][]=relation_direction/relation_direction_multiple&
        // only[]=metrics.spammers.count&only[]=metrics.filteredUnits.count&&only[]=annotationsCount&only[]=workerCount&only[]=unitsCount&only[]=projectedCost&only[]=template
        //TODO update with field[status]<>value
        var url = '/api/v1/?field[documentType]=job&field[status]=finished&field[domain]=' + $scope.domain + '&';
        //add template info
        for (var templateName in $scope.templates) {
            url = url + 'field[template][]=' + templateName + '&';
        }
        //retrieve ONLY specific fields
        for (var keyOption in $scope.chartSeriesOptions) {
            if (keyOption == 'payment') {
                url = url + 'only[]=' + $scope.chartSeriesOptions[keyOption]['field'] + '&';
            } else {
                url = url + 'only[]=' + $scope.chartSeriesOptions[keyOption]['noSpam']['field'] + '&';
                url = url + 'only[]=' + $scope.chartSeriesOptions[keyOption]['spam']['field'] + '&';
            }
        }

        $scope.jobList = {};

        $http.get(url).success(function (data, status) {
            data.sort(compare);
            //add data to the fields
            for (var jobInfoIter in data) {
                jobInfo = data[jobInfoIter];
                jobID = jobInfo['_id'];
                jobIDArray = jobID.split("/");
                $scope.jobList[jobIDArray[jobIDArray.length - 1]] = {'selected': false,
                    'name': jobID,
                    'position': jobInfoIter};


                for (var chartSeriesOptionKey in $scope.chartSeriesOptions) {
                    var option = $scope.chartSeriesOptions[chartSeriesOptionKey];

                    //if there is no key with spam, than it is a line chart
                    if (!('spam' in option)) {
                        if (!('data' in option)) {
                            option['data'] = [];
                        }

                        var yValue = 0;
                        if (option['field'] in jobInfo) {
                            yValue = jobInfo[option['field']];
                        }

                        if (jobID in $scope.analyzedJobIDs) {
                            yValue = {y: yValue, color: $scope.analyzedJobIDs[jobID]};
                        }

                        option['data'].push(yValue);

                    } else {
                        //get the number of spammers
                        var spamOption = option['spam'];
                        var noSpamOption = option['noSpam'];

                        if (!('data' in spamOption)) { //the data fields were not initialized
                            spamOption['data'] = [];
                            noSpamOption['data'] = [];
                        }

                        //once the database is stable maybe there will be no need for this try, catch
                        var yValue = 0;
                        if (noSpamOption['field'] in jobInfo) {
                            yValue = jobInfo[noSpamOption['field']];
                        }

                        //
                        var ySpamValue = 0;
                        try {
                            var pathArray = spamOption['field'].split('.');
                            ySpamValue = jobInfo;
                            for (iter = 0; iter < pathArray.length; iter++) {
                                ySpamValue = ySpamValue[pathArray[iter]];
                            }
                        }
                        catch (err) {
                            console.dir('the ' + chartSeriesOptionKey + ' spam value was set to zero for undefined');
                            ySpamValue = 0;
                        }

                        option['avg']['value'] += yValue;
                        var yNoSpamValue = yValue - ySpamValue;

                        if (jobID in $scope.analyzedJobIDs) {
                            ySpamValue = {y: ySpamValue, color: $scope.analyzedJobIDs[jobID]};
                            yNoSpamValue = {y: yNoSpamValue, color: $scope.analyzedJobIDs[jobID]};
                        }

                        spamOption['data'].push(ySpamValue);
                        noSpamOption['data'].push(yNoSpamValue);
                    }
                }
            }
            //update the options of the chart
            updateChartOptions();
            //generate the chart
            console.dir($scope.chartGeneralOptions);
            $scope.chart = new Highcharts.Chart($scope.chartGeneralOptions);

        });


    }


    $scope.initAnnotWorkerUnitInfo = function (){

        //get the information about jobs
        $scope.annotUnitWorker = [];

        //create the get requests
        var httpRequests = [];
        for (var jobID in $scope.analyzedJobIDs) {

            console.dir(jobID);
            var url = '/api/v1/?field[documentType]=annotation&field[job_id]=' +  jobID +
                '&only[]=job_id&only[]=crowdAgent_id&only[]=unit_id';

            var request = $http.get(url);

            httpRequests.push(request);
        }

        var chart = d3.parsets()
            .dimensions([ "job_id", "unit_id", "crowdAgent_id"]);

        var vis = d3.select("#visParasets").append("svg")
            .attr("width", chart.width())
            .attr("height", chart.height());


        $q.all(httpRequests).then(function(arrayOfResults) {
            console.dir(arrayOfResults);
            for (var iterArray in arrayOfResults) {
                $scope.annotUnitWorker = $scope.annotUnitWorker.concat(arrayOfResults[iterArray]['data']);
            }

            vis.datum($scope.annotUnitWorker).call(chart);
        });


    }


    $scope.init = function (data) {
        var arrayData = data.split(", ");
        var arrayDataLength = arrayData.length / 2;

        var httpRequests = [];

        //create a dictionary with colors of the analyzed jobs
        //create the http requests
        $scope.analyzedJobIDs = {};
        $scope.templates = {};

        for (var iterData = 0; iterData < arrayDataLength; iterData++) {
            var jobID = arrayData[iterData];
            var jobColor = arrayData[arrayDataLength + iterData];
            $scope.analyzedJobIDs[jobID] = jobColor;

            var url = '/api/v1/?field[_id]=' + jobID +
                '&only[]=template&only[]=domain';

            var request = $http.get(url);

            httpRequests.push(request);
        }

        //when the data is received generate the chart
        $q.all(httpRequests).then(function (arrayOfResults) {
            //console.log(arrayOfResults);
            var iterResults;
            for (iterResults in arrayOfResults) {
                template = arrayOfResults[iterResults].data[0]['template'];
                if (!(template in $scope.templates)) {
                    $scope.templates[template] = 0;
                }

            }
            //assuming that the check for a single domain has been done, get the last domain name
            $scope.domain = arrayOfResults[iterResults].data[0]['domain'];

            $scope.createStatChart();

            $scope.initAnnotWorkerUnitInfo();

        });
    };
=======
//write resource service class


//inject resourceSvc in this controller ? check if is the right definition for minimizations
angular.module("jobanalytics").controller("jobStatContr", function ($scope, $http, $templateCache, $q) {

    $scope.chartSeriesOptions = {
        'workers': {'legend': '#A63800',
            'avg': {'color': '#A63800',
                'field': 'workerCount',
                'value': 0},
            'noSpam': {'color': '#BF6030',
                'field': 'workerCount'},
            'spam': {'color': '#FF8040',
                'field': 'metrics.spammers.count'}
        },
        'units': {'legend': '#0D58A6',
            'avg': {'color': '#0D58A6',
                'field': 'unitsCount',
                'value': 0},
            'noSpam': {'color': '#26517C',
                'field': 'unitsCount'},
            'spam': {'color': '#689CD2',
                'field': 'metrics.filteredUnits.count'}
        },
        'annotations': {'legend': '#00AA72',
            'avg': {'color': '#00AA72',
                'field': 'annotationsCount',
                'value': 0},
            'noSpam': {'color': '#207F60',
                'field': 'annotationsCount'},
            'spam': {'color': '#60D4AE',
                'field': 'metrics.filteredUnits.count'}
        },
        'payment': {'legend': '#E00000 ', 'field': 'projectedCost', color: '#E00000 '}
    };

    $scope.chartGeneralOptions = {
        chart: {
            zoomType: 'xy',
            renderTo: 'statisticsChart',
            marginBottom: 70
        },
        title: {
            text: 'Comparison with jobs with the same template',

        },
        tooltip: {
            shared: true,
            useHTML: true,
            headerFormat: '<b>Job {point.key}</b><table>',
            pointFormat: '<tr><td style="color: {series.color}">{series.name}: </td>' +
                '<td style="text-align: right"><b>{point.y}</b></td></tr>',
            footerFormat: '</table>',
            valueDecimals: 2
        },

        plotOptions: {
            series: {
                stacking: 'normal',
                states: {
                    hover: {
                        enabled: true,
                        lineWidth: 5,
                        brightness: 0.4
                    }
                },
                cursor: 'pointer',
                point: {
                    events: {
                        click: function () {
                            $scope.selectedJob = this.category;
                            $('#selectJobID').click();
                        }
                    }
                }

            }
        }
    };

    $scope.hoverInChart = function (position) {
        for (var iterSeries = 0; iterSeries < $scope.chart.series.length; iterSeries++) {
            $scope.chart.series[iterSeries].data[position].setState('hover');
        }
    }

    $scope.hoverOutChart = function (position) {
        for (var iterSeries = 0; iterSeries < $scope.chart.series.length; iterSeries++) {
            $scope.chart.series[iterSeries].data[position].setState();
        }
    }

    $scope.clearAll = function () {
        for (var jobID in $scope.jobList) {
            $scope.jobList[jobID]['selected'] = false;
        }
    }

    $scope.analyze = function () {
        var selectedJobs = [];
        for (var jobID in $scope.jobList) {
            if ($scope.jobList[jobID]['selected'] == true)
                selectedJobs.push($scope.jobList[jobID]['name']);
        }

        if (selectedJobs.length == 0) {
            alert("Please select the jobs first");
        } else {
            window.location.href = '/analyze/view?jobs=' + selectedJobs.join();
        }
    }


    $scope.selectJobID = function () {
        $scope.jobList[$scope.selectedJob]['selected'] = true;
    }


    $scope.createStatChart = function () {

        var compare = function (a, b) {

            a_array = a._id.split("/");
            b_array = b._id.split("/");
            a_id = a_array[a_array.length - 1];
            b_id = b_array[b_array.length - 1];
            return a_id - b_id;
        };

        var updateChartOptions = function () {
            console.dir(Object.keys($scope.templates).join());
            $scope.chartGeneralOptions.subtitle = {
                text: Object.keys($scope.templates).join()

            };

            var xAxisValues = Object.keys($scope.jobList);
            console.dir(xAxisValues.sort());

            $scope.chartGeneralOptions.xAxis = {categories: [17, 18, 19, 20]};//xAxisValues.sort();

            //create the yAxis and series option fields
            $scope.chartGeneralOptions.yAxis = [];
            $scope.chartGeneralOptions.series = [];


            for (var key in $scope.chartSeriesOptions) {
                var yAxisSettings = {
                    gridLineWidth: 0,
                    labels: {
                        formatter: function () {
                            return this.value;
                        },
                        style: {
                            color: $scope.chartSeriesOptions[key]['legend']
                        }
                    },
                    title: {
                        text: key,
                        style: {
                            color: $scope.chartSeriesOptions[key]['legend']
                        }
                    },
                    opposite: true

                };
                if (key == 'payment') {
                    yAxisSettings['opposite'] = false;
                    var paymentSeries = {
                        name: key,
                        color: $scope.chartSeriesOptions[key]['color'],
                        yAxis: $scope.chartGeneralOptions.yAxis.length,
                        type: 'spline',
                        data: $scope.chartSeriesOptions[key]['data'],
                        tooltip: {
                            valueSuffix: ' cents'
                        }
                    }
                    $scope.chartGeneralOptions.yAxis.push(yAxisSettings);
                    $scope.chartGeneralOptions.series.push(paymentSeries);
                    continue;
                }

                //if not payment option
                //create an avg, spam and no spam series
                var spamSeries = {
                    name: '# spam ' + key,
                    color: $scope.chartSeriesOptions[key]['spam']['color'],
                    type: 'column',
                    yAxis: $scope.chartGeneralOptions.yAxis.length,
                    stack: key,
                    visible: false,
                    data: $scope.chartSeriesOptions[key]['spam']['data']
                }

                //create an avg, spam and no spam series
                var noSpamSeries = {
                    name: '# ' + key + '(no spam)',
                    color: $scope.chartSeriesOptions[key]['noSpam']['color'],
                    type: 'column',
                    yAxis: $scope.chartGeneralOptions.yAxis.length,
                    stack: key,
                    visible: false,
                    data: $scope.chartSeriesOptions[key]['noSpam']['data']
                }

                console.dir($scope.chartSeriesOptions[key]['avg']['value']);
                console.dir(xAxisValues.length);
                var avgValue = $scope.chartSeriesOptions[key]['avg']['value'] / xAxisValues.length;
                var avgSeries = {
                    name: 'avg # ' + key,
                    type: 'spline',
                    visible: false,
                    color: $scope.chartSeriesOptions[key]['avg']['color'],
                    yAxis: $scope.chartGeneralOptions.yAxis.length,
                    data: Array.apply(null, new Array(xAxisValues.length)).map(Number.prototype.valueOf, avgValue),
                    marker: {
                        enabled: false
                    },
                    dashStyle: 'shortdot'
                }

                $scope.chartGeneralOptions.yAxis.push(yAxisSettings);
                $scope.chartGeneralOptions.series.push(spamSeries);
                $scope.chartGeneralOptions.series.push(noSpamSeries);
                $scope.chartGeneralOptions.series.push(avgSeries);
            }

        }
        //create the url for http request
        // similar to:
        ////field[documentType]=job&field[domain]=medical&field[template][]=relation_direction/relation_direction_1&field[template][]=relation_direction/relation_direction_multiple&
        // only[]=metrics.spammers.count&only[]=metrics.filteredUnits.count&&only[]=annotationsCount&only[]=workerCount&only[]=unitsCount&only[]=projectedCost&only[]=template
        //TODO update with field[status]<>value
        var url = '/api/v1/?field[documentType]=job&field[status]=finished&field[domain]=' + $scope.domain + '&';
        //add template info
        for (var templateName in $scope.templates) {
            url = url + 'field[template][]=' + templateName + '&';
        }
        //retrieve ONLY specific fields
        for (var keyOption in $scope.chartSeriesOptions) {
            if (keyOption == 'payment') {
                url = url + 'only[]=' + $scope.chartSeriesOptions[keyOption]['field'] + '&';
            } else {
                url = url + 'only[]=' + $scope.chartSeriesOptions[keyOption]['noSpam']['field'] + '&';
                url = url + 'only[]=' + $scope.chartSeriesOptions[keyOption]['spam']['field'] + '&';
            }
        }

        $scope.jobList = {};

        $http.get(url).success(function (data, status) {
            data.sort(compare);
            //add data to the fields
            for (var jobInfoIter in data) {
                jobInfo = data[jobInfoIter];
                jobID = jobInfo['_id'];
                jobIDArray = jobID.split("/");
                $scope.jobList[jobIDArray[jobIDArray.length - 1]] = {'selected': false,
                    'name': jobID,
                    'position': jobInfoIter};


                for (var chartSeriesOptionKey in $scope.chartSeriesOptions) {
                    var option = $scope.chartSeriesOptions[chartSeriesOptionKey];

                    //if there is no key with spam, than it is a line chart
                    if (!('spam' in option)) {
                        if (!('data' in option)) {
                            option['data'] = [];
                        }

                        var yValue = 0;
                        if (option['field'] in jobInfo) {
                            yValue = jobInfo[option['field']];
                        }

                        if (jobID in $scope.analyzedJobIDs) {
                            yValue = {y: yValue, color: $scope.analyzedJobIDs[jobID]};
                        }

                        option['data'].push(yValue);

                    } else {
                        //get the number of spammers
                        var spamOption = option['spam'];
                        var noSpamOption = option['noSpam'];

                        if (!('data' in spamOption)) { //the data fields were not initialized
                            spamOption['data'] = [];
                            noSpamOption['data'] = [];
                        }

                        //once the database is stable maybe there will be no need for this try, catch
                        var yValue = 0;
                        if (noSpamOption['field'] in jobInfo) {
                            yValue = jobInfo[noSpamOption['field']];
                        }

                        //
                        var ySpamValue = 0;
                        try {
                            var pathArray = spamOption['field'].split('.');
                            ySpamValue = jobInfo;
                            for (iter = 0; iter < pathArray.length; iter++) {
                                ySpamValue = ySpamValue[pathArray[iter]];
                            }
                        }
                        catch (err) {
                            console.dir('the ' + chartSeriesOptionKey + ' spam value was set to zero for undefined');
                            ySpamValue = 0;
                        }

                        option['avg']['value'] += yValue;
                        var yNoSpamValue = yValue - ySpamValue;

                        if (jobID in $scope.analyzedJobIDs) {
                            ySpamValue = {y: ySpamValue, color: $scope.analyzedJobIDs[jobID]};
                            yNoSpamValue = {y: yNoSpamValue, color: $scope.analyzedJobIDs[jobID]};
                        }

                        spamOption['data'].push(ySpamValue);
                        noSpamOption['data'].push(yNoSpamValue);
                    }
                }
            }
            //update the options of the chart
            updateChartOptions();
            //generate the chart
            console.dir($scope.chartGeneralOptions);
            $scope.chart = new Highcharts.Chart($scope.chartGeneralOptions);

        });


    }


    $scope.initAnnotWorkerUnitInfo = function (){

        //get the information about jobs
        $scope.annotUnitWorker = [];

        //create the get requests
        var httpRequests = [];
        for (var jobID in $scope.analyzedJobIDs) {

            console.dir(jobID);
            var url = '/api/v1/?field[documentType]=annotation&field[job_id]=' +  jobID +
                '&only[]=job_id&only[]=crowdAgent_id&only[]=unit_id';

            var request = $http.get(url);

            httpRequests.push(request);
        }

        var chart = d3.parsets()
            .dimensions([ "job_id", "unit_id", "crowdAgent_id"]);

        var vis = d3.select("#visParasets").append("svg")
            .attr("width", chart.width())
            .attr("height", chart.height());


        $q.all(httpRequests).then(function(arrayOfResults) {
            console.dir(arrayOfResults);
            for (var iterArray in arrayOfResults) {
                $scope.annotUnitWorker = $scope.annotUnitWorker.concat(arrayOfResults[iterArray]['data']);
            }

            vis.datum($scope.annotUnitWorker).call(chart);
        });


    }


    $scope.init = function (data) {
        var arrayData = data.split(", ");
        var arrayDataLength = arrayData.length / 2;

        var httpRequests = [];

        //create a dictionary with colors of the analyzed jobs
        //create the http requests
        $scope.analyzedJobIDs = {};
        $scope.templates = {};

        for (var iterData = 0; iterData < arrayDataLength; iterData++) {
            var jobID = arrayData[iterData];
            var jobColor = arrayData[arrayDataLength + iterData];
            $scope.analyzedJobIDs[jobID] = jobColor;

            var url = '/api/v1/?field[_id]=' + jobID +
                '&only[]=template&only[]=domain';

            var request = $http.get(url);

            httpRequests.push(request);
        }

        //when the data is received generate the chart
        $q.all(httpRequests).then(function (arrayOfResults) {
            //console.log(arrayOfResults);
            var iterResults;
            for (iterResults in arrayOfResults) {
                template = arrayOfResults[iterResults].data[0]['template'];
                if (!(template in $scope.templates)) {
                    $scope.templates[template] = 0;
                }

            }
            //assuming that the check for a single domain has been done, get the last domain name
            $scope.domain = arrayOfResults[iterResults].data[0]['domain'];

            $scope.createStatChart();

            $scope.initAnnotWorkerUnitInfo();

        });
    };
>>>>>>> 373a996f28c09ac33cb9afd1d59c621e5cca2fd5
});
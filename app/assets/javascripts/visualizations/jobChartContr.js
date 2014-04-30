//write resource service class


//inject resourceSvc in this controller ? check if is the right definition for minimizations
angular.module("dataRetrieval").controller("jobChartContr", function ($scope, $http, $templateCache, $q) {

    $scope.chartSeriesOptions = {
        'workers': {
            'potentialSpamWorkers': {'color': '#FF0000', 'field': '', 'name':'# of potential spam workers', 'type': 'column'},
            'spamWorkers': {'color': '#A80000', 'field': 'metrics.spammers.count', 'name':'# of spam workers', 'type': 'column'},
            'avgWorkers': {'color': '#A63800', 'field': '', 'name':'avg # of workers', 'type': 'spline', 'dashStyle':'shortdot'},
            'workers': {'color': '#BF6030', 'field': 'workerCount', 'name':'# of workers', 'type': 'column'}},
        'units': {
            'filteredUnits': {'color': '#689CD2', 'field': 'metrics.filteredUnits.count', 'name':'# of filtered units', 'type': 'column'},
            'units': {'color': '#26517C', 'field': 'unitsCount', 'name':'# of units', 'type': 'column'},
            'avgUnits': {'color': '#0D58A6', 'field': '', 'name':'avg # of units', 'type': 'spline', 'dashStyle':'shortdot'}},
        'annotations': {
            'spamAnnotations': {'color': '#60D4AE', 'field': 'metrics.filteredUnits.count', 'name':'# of spam annotations', 'type': 'column'},
            'annotations': {'color': '#207F60', 'field': 'annotationsCount', 'name':'# of annotations', 'type': 'column'},
            'avgAnnotations': {'color': '#00AA72', 'field': '', 'name':'avg # of annotations', 'type': 'spline', 'dashStyle':'shortdot'}},
        'time': { 'time': {'color': '#FF9E00', 'field': '', 'name':'job duration', 'type': 'spline', 'dashStyle':'LongDash',  'tooltip': { valueSuffix: ' secs' }}},
        'payment': { 'payment': {'color': '#E00000', 'field': 'projectedCost', 'name':'payment', 'type': 'spline','dashStyle':'LongDashDot', 'tooltip': { valueSuffix: ' cents' }}},
        'metrics': {
            'cosineSimilarity': {'color': '#00CED1', 'field': 'metrics.aggUnits.mean.max_relation_Cos', 'name':'avg unit clarity', 'type': 'spline', 'dashStyle':'Solid'},
            'magnitude': {'color': '#00FA9A', 'field': 'metrics.aggUnits.mean.magnitude', 'name':'avg unit magnitude', 'type': 'spline', 'dashStyle':'Solid'},
            'workerAgreement': {'color': '#483D8B', 'field': 'metrics.aggWorker.mean.avg_worker_agreement', 'name':'avg worker agreement', 'type': 'spline', 'dashStyle':'Solid'},
            'workerCosine': {'color': '#6B8E23', 'field': 'metrics.aggWorker.mean.worker_cosine', 'name':'avg worker cosine', 'type': 'spline', 'dashStyle':'Solid'}}
    }
    $scope.selected = [];

    $scope.chartGeneralOptions = {
        chart: {
            zoomType: 'xy',
            renderTo: 'jobsChart',
            marginBottom: 70
        },
        legend: {
            layout: 'horizontal',
            align: 'left',
            verticalAlign: 'bottom',
            floating: true,
            backgroundColor: '#FFFFFF'
        },
        title: {
            text: 'Job overview'
        },
        xAxis: {
            events:{
                setExtremes :function (event) {
                    console.dir(event.min + " " + event.max);
                    console.dir(unitsChart.xAxis);
                    var min = 0;
                    if (event.min != undefined){
                        min = event.min;
                    }
                    var max = unitsChart.series[0].data.length
                    if (event.max != undefined){
                        max = event.max;
                    }
                    // chart.yAxis[0].options.tickInterval
                    console.dir(min + " " + max);
                    unitsChart.xAxis[0].options.tickInterval = Math.ceil( (max-min)/20);
                }
            },
            labels: {
                formatter: function() {
                    var arrayJob = this.value.split("/");
                    return arrayJob[arrayJob.length - 1];
                }
            }
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
                //allowPointSelect: true,
                states: {

                    select: {
                        color: null,
                        borderWidth:2,
                        borderColor:'Blue'
                    }
                },
                //cursor: 'pointer',
                point: {
                    events: {
                        click: function () {
                            /*if ($scope.selected.indexOf(this.x)){

                            } else {
                                $scope.selected(push())
                            }*/


                            //console.dir(this);

                            for (var iterSeries = 0; iterSeries < $scope.barChart.series.length; iterSeries++) {
                              /*  if($scope.barChart.series[iterSeries].data[this.x].selected == true) {
                                    $scope.barChart.series[iterSeries].data[this.x].select(false,true);
                                } else {*/
                                    $scope.barChart.series[iterSeries].data[this.x].select(null,true)
                                //console.dir($scope.barChart.series[iterSeries].data[this.x]);
                               // }
                            }
                           // console.dir($scope.barChart.getSelectedPoints());

                        }
                    }
                }
                /*dataLabels: {
                    inside: true,
                    enabled: true,
                    useHTML: true,
                    formatter: function () {
                        console.log(this.point.series.yAxis.axisTitle.textStr);
                        return this.point.series.yAxis.axisTitle.textStr[0].toUpperCase();
                    }
                }*/

            }
        }
    };




    $scope.computeBarChartProjectData = function(){

        $scope.projectCriteria = "";
        for (var key in $scope.chartSeriesOptions) {
            yAxisSeries = $scope.chartSeriesOptions[key];
            for (var key in yAxisSeries) {
                if(yAxisSeries[key]['field']!= ""){
                    $scope.projectCriteria += "&project[" + key + "]=" + yAxisSeries[key]['field'];
                }
            }
        }
        //console.log($scope.projectCriteria);

    }

    $scope.drawBarChartData = function(matchCriteria, sortCriteria){

        if(sortCriteria == ""){
            sortCriteria = '&sort[created_at]=1';
        }

        var url = '/api/analytics/jobgraph/?' +
                    $scope.matchCriteria + matchCriteria +
                    sortCriteria +
                    $scope.projectCriteria;

        $http.get(url).success(function (data, status) {

            $scope.chartGeneralOptions['xAxis']['categories'] = data["id"];

            //create the yAxis and series option fields
            $scope.chartGeneralOptions.yAxis = [];
            $scope.chartGeneralOptions.series = [];


            for (var key in $scope.chartSeriesOptions) {
                var yAxisSeriesGroup = $scope.chartSeriesOptions[key];
                var color = 'black';
                for (var series in yAxisSeriesGroup) {
                    var newSeries = {
                        name: yAxisSeriesGroup[series]['name'],
                        color: yAxisSeriesGroup[series]['color'],
                        yAxis: $scope.chartGeneralOptions.yAxis.length,
                        type: yAxisSeriesGroup[series]['type'],
                        data: data[series],
                        visible: false
                    }
                    if ("tooltip" in yAxisSeriesGroup[series]) {
                        newSeries['tooltip'] = yAxisSeriesGroup[series]['tooltip'];
                    }
                    if(yAxisSeriesGroup[series]['type'] == 'column') {
                        newSeries['stack'] =  key;
                        newSeries['visible'] = true;
                    } else {
                        newSeries['dashStyle'] =  yAxisSeriesGroup[series]['dashStyle'];
                    }
                    $scope.chartGeneralOptions.series.push(newSeries);
                    color = yAxisSeriesGroup[series]['color'];

                }

                var yAxisSettings = {
                    gridLineWidth: 0,
                    labels: {
                        formatter: function () {
                            return this.value;
                        },
                        style: {
                            color: color
                        }
                    },
                    title: {
                        text: key,
                        style: {
                            color: color
                        }
                    },
                    opposite: false
                };
                if(key == 'workers' || key =='units' || key == 'annotations')
                    yAxisSettings.opposite = true;
                //console.dir($scope.chartGeneralOptions.yAxis);
                $scope.chartGeneralOptions.yAxis.push(yAxisSettings);
             //   console.dir(key);
             //   console.dir($scope.chartGeneralOptions.yAxis);
            }

           // console.dir($scope.chartGeneralOptions);
            $scope.barChart = new Highcharts.Chart($scope.chartGeneralOptions);

        });
    }

    $scope.createBarChart = function(){
        $scope.matchCriteria = 'match[documentType][]=job';
        $scope.computeBarChartProjectData();
        $scope.drawBarChartData("","");
    }

    $scope.createBarChart();

});
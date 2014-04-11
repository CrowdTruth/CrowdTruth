//inject resourceSvc in this controller ? check if is the right definition for minimizations
angular.module("jobanalytics").controller("jobMetricsContr", function ($scope, $http, $templateCache) {


    $scope.xFunction = function () {
        return function (d) {
            return d[0];
        };
    }

    $scope.xAxisTickFormatFunction = function () {
        return function (d) {
            return d;
        }
    }

    $scope.yFunction = function () {
        return function (d) {
            return d[1];
        };
    }

    $scope.toolTipContentFunction2 = function () {

        return function (key, x, y, e, graph) {
            sentID = $scope.sentsMap[x];
            return  '<div > <p>' + key + '</p>' +
                '<p><b>Sentence:</b>' + sentID + '</p>' +
                '<p> <b>First term:</b>' + $scope.sentInfo[sentID]['terms']['first']['text'] + '</p>' +
                '<p> <b>Second term:</b>' + $scope.sentInfo[sentID]['terms']['second']['text'] + '</p>' +
                '<p> <b>Content:</b>' + $scope.sentInfo[sentID]['sentence']['text'] + '</p></div>';
        }
    }

    $scope.toggleAnnDist = function (annDistSpam) {

        if (annDistSpam == false) {
            $scope.annDistSpam = true;
            $scope.annDistStatus = "With";
            $scope.annotationDistributionData = $scope.chartDataNS;

        } else if (annDistSpam == true) {
            $scope.annDistSpam = false;
            $scope.annDistStatus = "Without";
            $scope.annotationDistributionData = $scope.chartDataWS;
        }

    }


    $scope.getAnnotDistChartData = function (data) {
        $scope.status = status;
        var responseWS = data['results']['withSpam'];
        var responseNS = data['results']['withoutSpam'];
        $scope.sents = Object.keys(responseWS);
        $scope.sentsMap = {};
        $scope.annKeys = Object.keys(responseWS[$scope.sents[0]]);
        $scope.annDistributionWS = {};
        $scope.annDistributionNS = {};

        //create annotation_key:[sentid:nb_annotators,]
        for (var iterSent in $scope.sents) {
            var sentID = $scope.sents[iterSent];
            var splitArray = sentID.split('/');
            var sentKey = splitArray[splitArray.length - 1];
            $scope.sentsMap[sentKey] = sentID;

            for (var iterAnnKey in $scope.annKeys) {
                annKey = $scope.annKeys[iterAnnKey];
                if (annKey in $scope.annDistributionWS) {
                    $scope.annDistributionWS[annKey].push([sentKey, responseWS[sentID][annKey]]);
                    $scope.annDistributionNS[annKey].push([sentKey, responseNS[sentID][annKey]]);
                } else {
                    $scope.annDistributionWS[annKey] = [
                        [sentKey, responseWS[sentID][annKey]]
                    ];
                    $scope.annDistributionNS[annKey] = [
                        [sentKey, responseNS[sentID][annKey]]
                    ];
                }
            }
        }

        $scope.chartDataWS = [];
        $scope.chartDataNS = [];
        for (var ann_key in $scope.annDistributionWS) {
            var annDictWS = {};
            var annDictNS = {};
            annDictWS['key'] = annDictNS['key'] = ann_key;
            annDictWS['values'] = $scope.annDistributionWS[ann_key];
            annDictNS['values'] = $scope.annDistributionNS[ann_key];
            $scope.chartDataWS.push(annDictWS);
            $scope.chartDataNS.push(annDictNS);

        }

        $scope.sentInfo = new Array();

        for (var iterSent in $scope.sents) {
            var sentID = $scope.sents[iterSent];
            var url = '/api/v1/?field[_id]='.concat(sentID).concat('&only[]=content');
            $http({method: $scope.method, url: url, cache: $templateCache}).
                success(function (data, status) {
                    $scope.sentInfo[data[0]['_id']] = data[0]['content'];
                }).
                error(function (data, status) {
                    $scope.data = data || "Request failed";
                    $scope.status = status;
                });
        }

        $scope.annotationDistributionData = $scope.chartDataWS;
        $scope.annDistStatus = "Without";
        console.dir($scope.sentInfo);

    }


    $scope.getData = function () {

        $scope.method = 'GET';
        //make a request for all the results and process the results
        if ($scope.jobIDs.length == 1) {
            var base_url = '/api/v1/?field[_id]=';
            $scope.url = base_url.concat($scope.jobIDs);
        } else {
            var base_url = '/api/v1/?createMetrics=';
            $scope.url = base_url.concat($scope.jobIDs);
        }
        $http({method: $scope.method, url: $scope.url, cache: $templateCache}).
            success(function (data, status) {
                console.dir(data);
                if ($scope.jobIDs.length == 1) {
                    $scope.data = data[0];
                    $scope.getAnnotDistChartData(data[0]);
                } else {
                    $scope.data = data;
                    $scope.getAnnotDistChartData(data);
                }
                $scope.createWorkerMetrics();
                $scope.createUnitMetrics();
            }).
            error(function (data, status) {
                $scope.data = data || "Request failed";
                $scope.status = status;
            });

        //for the sentence ids get the sentence information
    }

    $scope.createWorkerMetrics = function () {
        console.dir($scope.data['metrics']['workers']);

        var color = function (d) {
            console.dir(d['id']);
            if (d['id'] === 'mean') {
                console.dir("dada");
                return "#a00";
            }
            else {
                return "#010"
            }
        };

        var parcoords = d3.parcoords()("#workerMetric")
            .alpha(0.4);

        $scope.workerMetrics = {};
        $scope.workerMetrics['withFilter'] = [];
        $scope.workerMetrics['withoutFilter'] = [];

        metrics = $scope.data['metrics']['workers'];
        for (var metricKey in metrics['withFilter']) {

            var dict = metrics['withFilter'][metricKey];
            dict['id'] = metricKey;
            console.dir(metricKey);
            console.dir(dict);
            $scope.workerMetrics['withFilter'].push(dict);
            dict = metrics['withoutFilter'][metricKey];
            dict['id'] = metricKey;
            $scope.workerMetrics['withoutFilter'].push(dict);
        }
        meanDic = $scope.data['metrics']['aggWorker']['mean'];
        meanDic['id'] = 'mean';
        $scope.workerMetrics['withFilter'].push(meanDic)

        console.dir($scope.workerMetrics['withFilter']);
        parcoords
            .data($scope.workerMetrics['withFilter'])
            .color(color)
            .alpha(0.2)
            .render()
            // .reorderable()
            .brushable();  // enable brushing


        d3.select('#btnReset').on('click', function () {
            parcoords.brushReset();
        })
    }
    $scope.createUnitMetrics = function () {

        console.dir($scope.data['metrics']['units']);

        var color = function (d) {
            console.dir(d['id']);
            if (d['id'] === 'mean') {
                console.dir("dada");
                return "#a00";
            }
            else {
                return "#010"
            }
        };

        var parcoords = d3.parcoords()("#unitMetric")
            .alpha(0.4);

        $scope.unitsMetrics = {};
        $scope.unitsMetrics['withSpam'] = [];
        $scope.unitsMetrics['withoutSpam'] = [];

        metrics = $scope.data['metrics']['units'];

        for (var metricKey in metrics['withSpam']) {

            var dict = metrics['withSpam'][metricKey];
            dict['id'] = metricKey;
            console.dir(metricKey);
            console.dir(dict);
            $scope.unitsMetrics['withSpam'].push(dict);
            dict = metrics['withoutSpam'][metricKey];
            dict['id'] = metricKey;
            $scope.unitsMetrics['withoutSpam'].push(dict);
        }
        meanDic = $scope.data['metrics']['aggUnits']['mean'];
        meanDic['id'] = 'mean';
        $scope.unitsMetrics['withSpam'].push(meanDic)

        console.dir($scope.unitsMetrics['withSpam']);
        parcoords
            .data($scope.unitsMetrics['withSpam'])
            .color(color)
            .alpha(0.2)
            .render()
            // .reorderable()
            .brushable();  // enable brushing


        d3.select('#btnReset').on('click', function () {
            parcoords.brushReset();
        })

    }

    $scope.init = function (ids) {
        $scope.jobIDs = ids.split(",");
        $scope.getData();
    };
});




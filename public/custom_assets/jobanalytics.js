var app = angular.module("jobanalytics", ['nvd3ChartDirectives', 'ngResource']);

//write resource service class


//inject resourceSvc in this controller ? check if is the right definition for minimizations
app.controller("jobanalyticsCtrl", function ($scope, $http, $templateCache) {
    // On init fetches first pagination object


    $scope.xFunction = function () {
        return function (d) {
           // console.dir(d);
            return d[0];
        };
    }

    $scope.xAxisTickFormatFunction = function () {
        return function (d) {
            //splitArray = d.split('/');
            //splitArray[splitArray.length - 1];
            return d;
        }
    }

    $scope.yFunction = function () {
        return function (d) {
            return d[1];
        };
    }

    var colorArray = ['#FF0000', '#0000FF', '#FFFF00', '#00FFFF'];
    $scope.colorFunction = function () {
        return function (d, i) {
            return colorArray[i];
        };
    }

    $scope.toolTipContentFunction = function () {

        return function (key, x, y, e, graph) {
            sentID = $scope.sentsMap[x];
            return  '<p>' + key + '</p>' +
                '<p><b>Sentence:</b>' + sentID + '</p>' +
                '<p> <b>First term:</b>' + $scope.sentInfo[sentID]['terms']['first']['text'] + '</p>' +
                '<p> <b>Second term:</b>' + $scope.sentInfo[sentID]['terms']['second']['text'] + '</p>';
                //'<p>' + $scope.sentsMap[x] + '</p>'

               // '<p>' + y + ' at ' + $scope.sentsMap[x] + '</p>'
        }
    }


    // helper for formatting date
    var humanReadableDate = function (d) {
        return d.getUTCMonth() + '/' + d.getUTCDate();
    };


    $scope.method = 'GET';
    $scope.url = 'http://10.11.12.13/api/v2/?';

    $scope.fetch = function () {
        $scope.code = null;
        $scope.response = null;

        $http({method: $scope.method, url: $scope.url, cache: $templateCache}).
            success(function (data, status) {
                $scope.status = status;
                $scope.data = data;
            }).
            error(function (data, status) {
                $scope.data = data || "Request failed";
                $scope.status = status;
            });
    };

    $scope.updateModel = function (method, url) {
        $scope.method = method;
        $scope.url = url;
    };

    $scope.getChartData = function(data) {
        $scope.status = status;
        var response = data[0]['results'];
        $scope.sents = Object.keys(response);
        $scope.sentsMap = {};
        $scope.annKeys = Object.keys(response[$scope.sents[0]]);
        $scope.annDistribution = {};

        //create annotation_key:[sentid:nb_annotators,]
        for (var iterSent in $scope.sents) {
            var sentID = $scope.sents[iterSent];
            var splitArray = sentID.split('/');
            var sentKey = splitArray[splitArray.length - 1];
            $scope.sentsMap[sentKey] = sentID;
            for(var iterAnnKey in $scope.annKeys){
                annKey = $scope.annKeys[iterAnnKey];
                if (annKey in $scope.annDistribution) {
                    $scope.annDistribution[annKey].push([sentKey, response[sentID][annKey]]);
                } else {
                    $scope.annDistribution[annKey] = [[sentKey, response[sentID][annKey]]];
                }
            }
        }

        $scope.chartData = [];
        for (var ann_key in $scope.annDistribution) {
            var annDict = {};
            annDict['key'] = ann_key;
            annDict['values'] = $scope.annDistribution[ann_key];
            $scope.chartData.push(annDict);
        }

        $scope.sentInfo = new Array();

        for (var iterSent in $scope.sents) {
            var sentID = $scope.sents[iterSent];
            url = '/api/v2/?field[_id]='.concat(sentID).concat('&only[]=content');
            $http({method: $scope.method, url: url, cache: $templateCache}).
                success(function (data, status) {
                    $scope.sentInfo[data[0]['_id']] = data[0]['content'];
                }).
                error(function (data, status) {
                    $scope.data = data || "Request failed";
                    $scope.status = status;
                });
        }

        console.dir($scope.sentInfo);
    }

    $scope.getData= function() {

        //make a request for all the results and process the results
        if ($scope.jobIDs.length == 1) {
            var base_url = '/api/v2/?field[_id]=';
            $scope.url = base_url.concat($scope.jobIDs).concat('&only[]=results');
        }

        $http({method: $scope.method, url: $scope.url, cache: $templateCache}).
            success(function (data, status) {
                $scope.getChartData(data);

            }).
            error(function (data, status) {
                $scope.data = data || "Request failed";
                $scope.status = status;
            });

        //for the sentence ids get the sentence information
    }

    $scope.init = function (ids) {
        $scope.jobIDs = ids.split(",");
        $scope.getData();
    };
    // $scope.results = getResource($resource,$scope.jobIDs);
    //console.dir($scope.results);

});



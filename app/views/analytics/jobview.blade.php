@extends('layouts.default')

@section('head')
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.2.13/angular.min.js"
        xmlns="http://www.w3.org/1999/html"></script>
<script src="//cdn.jsdelivr.net/lodash/2.4.1/lodash.underscore.min.js"></script>
<script type="text/javascript" src="http://code.angularjs.org/1.2.9/angular-resource.min.js"></script>
<script type="text/javascript" src="/custom_assets/angular-moment.js"></script>
<script type="text/javascript" src="/custom_assets/moment.js"></script>
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/angular-ui-bootstrap/0.10.0/ui-bootstrap-tpls.min.js"></script>

<script type="text/javascript" src="/custom_assets/crowdwatson.js"></script>
<link rel="stylesheet" type="text/css" href="/custom_assets/analytics/job/d3.parcoords.css">
<link rel="stylesheet" type="text/css" href="/custom_assets/analytics/job/d3.parsets.css">
<link rel="stylesheet" href="/custom_assets/analytics/job/jquery.mediaTable.css"/>
@stop

@section('content')

<div class="maincolumn CW_box_style" ng-app="jobanalytics">
    <container>
        <div class="page-header text-center"><h3>Jobs</h3></div>
        <div class="content">
            <table class="mediaTable table table-responsive table-hover">
                <thead>
                <tr>
                    <th class="essential persist">JobId</th>
                    <th style="display: none;">Media type</th>
                    <th class="optional">Domain</th>
                    <th>Template</th>
                    <th style="display: none;">Number of Units</th>
                    <th style="display: none;">Number of Annotations</th>
                    <th style="display: none;">Number of Workers</th>
                    <th>Platform</th>
                    <th>Status</th>
                    <th style="display: none;">Cost</th>
                </tr>
                </thead>

                <tbody>
                @foreach ($jobConfigurations as $job)
                <tr style='background-color:{{$job->color}}'>
                    <td>{{$job->id}}</td>
                    <td style="display: none;">{{$job->format}}</td>
                    <td>{{$job->domain}}</td>
                    <td>{{$job->template}}</td>
                    <td style="display: none;">{{$job->unitsCount}}</td>
                    <td style="display: none;">{{$job->annotationsCount}}</td>
                    <td style="display: none;">{{$job->workerCount}}</td>
                    <td>{{$job->softwareAgent_id}}</td>
                    <td>{{$job->status}} (Completion {{number_format($job->completion,2)*100}})</td>
                    <td style="display: none;">{{$job->projectedCost}}</td>
                </tr>
                @endforeach
                </tbody>
            </table>
            <div ng-controller="jobStatContr"
                 data-ng-init='init("{{implode(", ",$jobIDs)}}, {{implode(", ",$jobColors)}}")'>
                <div class="row ">
                    <div class="col-xs-12 col-md-8">
                        <ul class="list-unstyled">

                        </ul>
                    </div>
                </div>

                <div class="row container">
                    <div id="statisticsChart" style="min-width: 400px; height: 450px; margin: 0 auto"></div>
                </div>
                <div class="row container">
                    <div class="hidden">
                        <button id="selectJobID" type="button" class="btn btn-success" ng-click="selectJobID()"
                                href=""></button>
                    </div>
                    <ul>
                        <li class="checkbox-inline" data-ng-repeat="job in jobList"
                            ng-mouseover="hoverInChart(job.position)"
                            ng-mouseleave="hoverOutChart(job.position)">
                            <input type="checkbox" value="key" id="job.position" ng-change="chartJobSettingEvt($event)"
                                   ng-model="job.selected">
                            <a class="hover">@{{job.name}} </a>
                        </li>
                    </ul>
                    <ul>
                        <button type="button" ng-click="clearAll()" class="btn btn-default">ClearAll</button>
                        <button type="button" ng-click="analyze()" class="btn btn-default">Analyze</button>
                    </ul>
                </div>
                <br/>
                <div class="row container">
                    <h4 class="text-center"> Relation between jobs-workers-units</h4>
                    <div class="center-block" id="visParasets"></div>
                </div>

            </div>

            <div ng-controller="jobMetricsContr" data-ng-init='init("{{implode(", ",$jobIDs)}}")'>
                <div class="row container">
                    <h4 class="text-center"> Annotation Distribution</h4>
                    <br/>
                </div>
                <div class="row container">
                    <div class="col-xs-12 col-md-8">
                        <nvd3-multi-bar-chart
                        data="annotationDistributionData"
                        id="annotationDistribution"
                        width="750"
                        height="300"
                        showXAxis="true"
                        showYAxis="true"
                        xAxisTickFormat="xAxisTickFormatFunction()"
                        showLegend="true"
                        noData="No Data Available"
                        delay="2400"
                        stacked="true"
                        y="yFunction()"
                        interactive="true"
                        tooltips="true"
                        tooltipcontent="toolTipContentFunction2()"
                        x="xFunction()">
                        <svg></svg>
                    </nvd3-multi-bar-chart>
                    </div>
                    <div class="col-xs-6 col-md-4">
                        <br/>
                        <br/>
                        <button type="button" class="btn btn-success" ng-click="toggleAnnDist(annDistSpam)" ng-init="annDistSpam=false" href="">@{{annDistStatus}} spam?</button>

                    </div>
                </div>
                <div class="row container">
                    <h4 class="text-center"> Unit Metrics</h4>
                    <br/>
                    <div id="unitMetric" class="parcoords" style="width:960px;height:200px;"></div>
                    <br/>
                </div>
                <div class="row container">
                    <h4 class="text-center"> Worker Metrics</h4>
                    <br/>
                    <div id="workerMetric" class="parcoords" style="width:960px;height:200px;"></div>
                    <br/>
                </div>

            </div>
        </div>
    </container>
</div>

@stop

@section('end_javascript')
{{ javascript_include_tag('visualizations/d3.min.js')}}
{{ javascript_include_tag('visualizations/nv.d3.js')}}
{{ javascript_include_tag('visualizations/angularjs-nvd3-directives.js')}}
{{ javascript_include_tag('visualizations/jquery.mediaTable.js') }}
{{ javascript_include_tag('visualizations/moduleJobVis.js') }}
{{ javascript_include_tag('visualizations/jobStatContr.js') }}
{{ javascript_include_tag('visualizations/d3Parasets.js')}}
{{ javascript_include_tag('visualizations/jobMetricsContr.js')}}
{{ javascript_include_tag('visualizations/d3.parcoords.js')}}
{{ javascript_include_tag('highcharts.js') }}
{{ javascript_include_tag('modules/exporting.js') }}


<script>
    $('document').ready(function () {
        console.dir($('.mediaTable'));
        $('.mediaTable').mediaTable();
    });
</script>
@stop


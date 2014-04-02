@extends('layouts.default')

@section('head')
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.2.13/angular.min.js"></script>
<script src="//cdn.jsdelivr.net/lodash/2.4.1/lodash.underscore.min.js"></script>
<script type="text/javascript" src="http://code.angularjs.org/1.2.9/angular-resource.min.js"></script>
<script type="text/javascript" src="/custom_assets/angularjs-nvd3-directives.js"></script>
<script type="text/javascript" src="/custom_assets/d3.js"></script>
<script type="text/javascript" src="/custom_assets/nv.d3.js"></script>

<script type="text/javascript" src="/custom_assets/jobanalytics.js"></script>
<script type="text/javascript" src="/custom_assets/angular-moment.js"></script>
<script type="text/javascript" src="/custom_assets/moment.js"></script>
@stop


@section('content')
<div  class="maincolumn CW_box_style" style="..." ng-app="jobanalytics" ng-controller="jobanalyticsCtrl" data-ng-init="init('{{$jobConfigurations}}')">
    <div class = "page-header text-center"><h4>{{$jobConfigurations}}</h4></div>
    <div select="refresh('id1')">
        <nvd3-multi-bar-chart
            data="chartData"
            id="showLegendExample"
            width="750"
            height="300"
            showXAxis="true"
            showYAxis="true"
            xAxisTickFormat="xAxisTickFormatFunction()"
            showLegend="true"
            noData="No Data Available"
            delay="2400"
            showControls="true"
            y="yFunction()"
            interactive="true"
            tooltips="true"
            tooltipcontent="toolTipContentFunction()"
            x="xFunction()">
            <svg></svg>
        </nvd3-multi-bar-chart>
    </div>
    <h4> @{{data}}</h4>
</div>
@stop

@section('end_javascript')
	<script>
				
	</script>
@stop
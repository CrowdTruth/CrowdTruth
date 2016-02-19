@extends('layouts.default_new')

@section('container', 'full-container')

@section('head')
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css" rel="stylesheet">
@stop

@section('content')
  <div ng-app="myapp">
    
    <div ng-controller="shankey">
      <form class="form-inline">
        <button ng-click='createShankey()' class="btn btn-default">Create Shankey</button>
        <input ng-model="user" class='form-control' placeholder='user'>
      </form>
      
      <div class='row'>

    
        
        <div id='shankey'></div>
 
      </div>
    </div>
  </div>
  <style>
    .wrapper{
      border: 1px #e4e4e4 solid;
    }

    .node rect {
      cursor: move;
      fill-opacity: .9;
      shape-rendering: crispEdges;
    }
    .node text {
      pointer-events: none;
      text-shadow: 0 1px 0 #fff;
    }
    .link {
      fill: none;
      stroke: #000;
      stroke-opacity: .2;
    }
    .link:hover {
      stroke-opacity: .5;
    }

    .box{
      height: 500px;
        overflow: scroll;
    }
  </style>
@stop

@section('end_javascript')
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
  {{ javascript_include_tag('stavros-viz/angular.min.js') }}
  {{ javascript_include_tag('stavros-viz/highcharts-ng.js') }}
  {{ javascript_include_tag('stavros-viz/d3.v3.min.js') }}
  {{ javascript_include_tag('stavros-viz/shankey.js') }}
  {{ javascript_include_tag('stavros-viz/shankey-app.js') }}
@stop
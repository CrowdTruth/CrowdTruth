@extends('layouts.default_new')

@section('container', 'full-container')

@section('head')
  {{ stylesheet_link_tag('stavros-viz/projects.css') }}
  <script src='http://d3js.org/d3.v2.min.js'></script>
  <link rel="stylesheet" media="all" href="http://cdnjs.cloudflare.com/ajax/libs/codemirror/3.16.0/codemirror.css" />
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
@stop

@section('content')
  <div id="app"></div>
@stop

@section('end_javascript')
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
  <script src='http://d3js.org/d3.v2.min.js'></script>
  <link rel="stylesheet" media="all" href="http://cdnjs.cloudflare.com/ajax/libs/codemirror/3.16.0/codemirror.css" />
  {{ javascript_include_tag('stavros-viz/d3tip.js') }}
  {{ javascript_include_tag('stavros-viz/build.min.js') }}
  <!-- {{ javascript_include_tag('project-viz/project-app.js') }} -->
@stop
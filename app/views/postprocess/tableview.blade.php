@extends('layouts.default')

@section('content')
	<div class="container" ng-app="DataRetrieval">
		<div ng-controller="ResultCtrl">
			[[totalJobs]]
		</div>						
	</div>

@section('end_javascript')

<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.2.13/angular.min.js"></script>


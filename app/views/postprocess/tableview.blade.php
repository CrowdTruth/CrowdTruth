@extends('layouts.default')

@section('content')
	<div class="container" ng-app="DataRetrieval">
		<div class"row" ng-controller="ResultCtrl">
			<div class="col-md-10">
				<div>
					This will be the filter and sort box
					[[totalJobs]]
				</div>
				<div>
					And this is where the results will go
					[[totalJobs]]
				</div>
			</div>
		</div>
	</div>

@section('end_javascript')
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.2.13/angular.min.js">
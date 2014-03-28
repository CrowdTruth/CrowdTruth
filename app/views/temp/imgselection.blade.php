@extends('layouts.default')

@section('head')
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.2.13/angular.min.js"></script>
<script src="//cdn.jsdelivr.net/lodash/2.4.1/lodash.underscore.min.js"></script>
<script type="text/javascript" src="http://code.angularjs.org/1.2.9/angular-resource.min.js"></script>
<script type="text/javascript" src="/custom_assets/imageselection.js"></script>	
<link rel="stylesheet" type="text/css" href="/custom_assets/custom.css"></link>
@stop

@section('content')
<div class="mainContainer" style="height: 800px;" ng-app="imageSelection" ng-controller="imgCtrl">
	<div ng-show="loading" class="loading"><img class="loading-img" src="/loading.gif"><div>Loading..</div></div>
	<div ng-show="loading == false" class="image-box pull-left space-left" ng-repeat="image in pictures">
		<div class="image-selectable" ng-class="{selected: image.checked}" style="background-image: url(/image.png);">
			<div class="image-checkbox">
				<input type="checkbox" ng-model="image.checked">
			</div>
		</div>
		<div class="image-metadata">
				@{{image.name}}
		</div>
	</div>
	<div ng-show="loading == false" class="space-left" style="margin-bottom: 30px;">
		<button ng-click="executeScript()" class="btn btn-primary">Execute script!</button>
	</div>
</div>
@stop

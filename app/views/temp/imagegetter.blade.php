@extends('layouts.default')

@section('head')
<script type="text/javascript" src="/custom_assets/crowdwatson.js"></script>
<script type="text/javascript" src="/custom_assets/angular-moment.js"></script>
<script type="text/javascript" src="/custom_assets/moment.js"></script>
@stop

@section('content')
<div class="mainContainer" ng-controller="imgCtrl">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4><i class="fa fa-upload fa-fw"></i>Image getter</h4>
		</div>
		<div class="panel-body" ng-show="!imageGetting">							
			<div class="form-horizontal">
				<div class="form-group">
					<label for="domain_type" class="col-sm-3 control-label">Domain</label>
					<div class="col-sm-5">
						<select name="domain_type" class="form-control" id="domain_type" ng-model="domain">
							<option value="">--</option>
							<option value="art" class="file_format_image">Art</option>
						</select>
					</div>
				</div>

				<div class="form-group">
					<label for="document_type" class="col-sm-3 control-label">Type of Document</label>
					<div class="col-sm-5">
						<select name="document_type" class="form-control" id="document_type" ng-model="type">
							<option value="">--</option>
							<option value="painting" class="art">Painting</option>
							<option value="drawing" class="art">Drawing</option>
							<option value="picture" class="art">Picture</option>
						</select>
					</div>
				</div>

				<div class="form-group">
					<label for="category" class="col-sm-3 control-label">Number of images:</label>
					<div class="col-sm-6">
						<input type="number" id="category" ng-model="numImg"/>
					</div>
				</div>
				<div class="form-group">
					<label for="category" class="col-sm-3 control-label">Keyphrase:</label>
					<div class="col-sm-6">
						<input type="text" id="category" ng-model="keyphrase"/>
					</div>
				</div>

				<div class="form-group">
					<div class="col-sm-offset-3 col-sm-5">
						<button class="btn btn-primary" ng-click="next();">Start</button>
					</div>
				</div>
				<!-- @{{domain}} @{{type}} @{{numImg}} @{{keyphrase}}<br>@{{pictures}}<br>@{{status}} -->
			</div>
		</div>

		<div class="panelbody" ng-show="imageGetting" style="height: 600px; overflow-y: scroll;">
			<div ng-show="loading" class="loading"><img class="loading-img" src="/loading.gif"><div>Loading..</div></div>
			<div ng-show="empty" style="margin-left: 20px;"><h3> No images found in this query </h3><a ng-click="emptyArray()"> click here to go back</a> </div>
			<div class="image-box pull-left space-left" style="margin-top: 30px;" ng-show="!loading && !empty" ng-repeat="image in pictures">
				<div class="image-selectable" style="background-image: url(@{{image.url}}); background-size: 100%">
					<div class="image-hover" ng-class="{overlay: image.checked}">
						<div class="image-checkbox">
							<input type="checkbox" ng-model="image.checked">
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="panelfooter" ng-show="imageGetting">
			<div class="space-left" style="margin-bottom: 30px;">
				<button ng-click="executeScript()" ng-show="!loading && !empty" class="btn btn-primary">Execute script!</button>
			</div>
		</div>
	
	</div>


</div>
@stop

@section('end_javascript')
	{{ javascript_include_tag('jquery.chained.min.js') }}

	<script type="text/javascript">
		$(document).ready(function () {
			$("#domain_type").chainedTo("#file_format");
			$("#document_type").chainedTo("#domain_type");
		});
	</script>
@stop

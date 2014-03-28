@extends('layouts.default')

@section('head')
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.2.13/angular.min.js"></script>
<script src="//cdn.jsdelivr.net/lodash/2.4.1/lodash.underscore.min.js"></script>
<script type="text/javascript" src="http://code.angularjs.org/1.2.9/angular-resource.min.js"></script>
<script type="text/javascript" src="/custom_assets/imageselection.js"></script>	
<link rel="stylesheet" type="text/css" href="/custom_assets/custom.css"></link>
@stop

@section('content')
<div class="mainContainer" ng-app="imageSelection" ng-controller="imgCtrl">

	<div class="panel panel-default">
		<div class="panel-heading">
			<h4><i class="fa fa-upload fa-fw"></i>Image getter</h4>
		</div>
		<div class="panel-body">							
			<div class="form-horizontal">
				<div class="form-group">
					<label for="domain_type" class="col-sm-3 control-label">Domain</label>
					<div class="col-sm-5">
						<select name="domain_type" class="form-control" id="domain_type">
							<option value="">--</option>
							<option value="domain_type_art" class="file_format_image">Art</option>
						</select>
					</div>
				</div>

				<div class="form-group">
					<label for="document_type" class="col-sm-3 control-label">Type of Document</label>
					<div class="col-sm-5">
						<select name="document_type" class="form-control" id="document_type">
							<option value="">--</option>
							<option value="document_type_painting" class="domain_type_art">Painting</option>
							<option value="document_type_drawing" class="domain_type_art">Drawing</option>
							<option value="document_type_picture" class="domain_type_art">Picture</option>
						</select>
					</div>
				</div>

				<div class="form-group">
					<label for="category" class="col-sm-3 control-label">Number of images:</label>
					<div class="col-sm-6">
						<input type="number" id="category"/>
					</div>
				</div>
				<div class="form-group">
					<label for="category" class="col-sm-3 control-label">Keyphrase:</label>
					<div class="col-sm-6">
						<input type="text" id="category"/>
					</div>
				</div>

				<div class="form-group">
					<div class="col-sm-offset-3 col-sm-5">
						<button class="btn btn-primary" ng-click="next()">Start</button>
					</div>
				</div>
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

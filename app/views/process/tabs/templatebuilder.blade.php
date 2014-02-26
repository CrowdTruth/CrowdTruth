@extends('layouts.default')
@section('head')
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.2.13/angular.min.js"></script>
<!-- <script type="text/javascript" src="http://code.angularjs.org/1.2.9/angular.js"></script>
 --><script type="text/javascript" src="/custom_assets/angular-form-builder/angular-form-builder.js"></script>
<script type="text/javascript" src="/custom_assets/angular-form-builder/angular-form-builder-components.js"></script>
<script type="text/javascript" src="/custom_assets/angular-form-builder/angular-validator.min.js"></script>
<!-- basic rules (not required) -->
<script type="text/javascript" src="/custom_assets/angular-form-builder/angular-validator-rules.js"></script>
<link type="text/css" rel="stylesheet" href="/custom_assets/angular-form-builder/angular-form-builder.css"/>
<script type="text/javascript" src="/custom_assets/angular-form-builder/demo.js"></script>
@stop

@section('content')
<div ng-app="templatebuilder">

		<div class="row maincolumn CW_box_style" ng-controller="templatebuilderCtrl" style="padding:20px">
		<div class="row">
			<h1>angular-form-builder</h1>
			<hr/>

			<div class="col-md-6">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title">Builder</h3>
					</div>
					<div fb-builder="default" style="min-height:100px; padding:10px"></div>
					<div class="panel-footer">
						<div class="checkbox">
							<label><input type="checkbox" ng-model="isShowScope" />
								Show scope
							</label>
						</div>
						<pre ng-if="isShowScope">@{{form}}</pre>
					</div>
				</div>
			</div>

			<div class="col-md-6">
				<div fb-components></div>
			</div>
		
		</div>
		<div class="row" style="padding:20px">
			<h2>Form</h2>
			<hr/>
			<form class="form-horizontal">
				<div ng-model="input" fb-form="default" fb-default="defaultValue"></div>
				<div class="form-group">
					<div class="col-md-8 col-md-offset-4">
						<input type="submit" ng-click="submit()" class="btn btn-default"/>
					</div>
				</div>
			</form>
			<div class="checkbox">
				<label><input type="checkbox" ng-model="isShowScope" ng-init="isShowScope=true" />
					Show scope
				</label>
			</div>
			<pre ng-if="isShowScope">@{{input}}</pre>
		</div>
	</div>


@stop

@section('end_javascript')
<script>

</script>

@stop

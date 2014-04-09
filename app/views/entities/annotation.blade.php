@extends('layouts.default')

@section('head')
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.2.13/angular.min.js"></script>
<script src="//cdn.jsdelivr.net/lodash/2.4.1/lodash.underscore.min.js"></script>
<script type="text/javascript" src="http://code.angularjs.org/1.2.9/angular-resource.min.js"></script>
<script type="text/javascript" src="/custom_assets/entityretrieval.js"></script>	
<script type="text/javascript" src="/custom_assets/angular-moment.js"></script>
<script type="text/javascript" src="/custom_assets/moment.js"></script>
<link rel="stylesheet" type="text/css" href="/custom_assets/custom.css"></link>
@stop

@section('content')
<div ng-app="entityRetrieval" ng-controller="annotationByIdCtrl">
	<!-- Main column with results -->
	@include('layouts.flashdata')
 	<div class="row">
		<div class="ng-scope disabled pull-left">
			<ul style="margin-left: 20px;" class="pagination ng-isolate-scope">
				<li><a ng-click="previousId()">Previous</a></li> 
				<li><a ng-click="nextId()">Next</a></li> 
			</ul>
		</div>
	</div>

	<div class="mainContainer">
		<div class="bordered">
			<div class="row">
           		<div class="col-md-8">
           			<h2>@{{annotation._id}}</h2>
           			<div><h4 style="font-style: italic;"><i class="fa fa-user"></i><a ng-click="gotoWorker(annotation.crowdAgent_id)"> @{{annotation.crowdAgent_id}}</a></h4></div>
           			<div class="wp-from">Part of @{{annotation.job_id}}, created during @{{annotation.activity_id}}</div>
           		</div>
           		<div class="col-md-2 box-icon">
           			<strong style="font-size: 12pt;"><i class="fa fa-users fa-2x"></i><br>@{{annotation.softwareAgent_id}}</strong> 
           		</div>
          		<div class="col-md-2 box-icon right-end">
           			<strong style="font-size: 12pt; "><i class="fa fa-clock-o fa-2x"></i><br><span am-time-ago="annotation.created_at"></span></strong>
           		</div>	
           	</div>
		</div>
		<div class="bordered">
			<div class="row">
				<div class="col-md-8">
					<label class="ann-label">Accepted at: </label>@{{annotation.acceptTime}}<br>
					<label class="ann-label">Submitted at: </label>@{{annotation.submitTime}}<br>
					<label class="ann-label">Platform ID: </label>@{{annotation.platformAnnotationId}}<br>
				</div>
				<div class="col-md-4" ng-show="annotation.softwareAgent_id == 'cf'">
					<label class="ann-label">Cf-trust:  </label>@{{annotation.cfTrust | number: 2}} <br>
					<label class="ann-label">Cf-channel: </label>@{{annotation.cfChannel}}<br>
				</div>
			</div>
			<div>
				<pre>@{{annotation.content | json}}</pre>
			</div>
		</div>
		<div class="bordered">
			<div class="row">
				<div class="col-md-8">
					<h4>Unit</h4>
					<label class="ann-label">Title: </label><a ng-click="gotoUnit(unit._id)">@{{unit.title}}</a><br>
					<label class="ann-label">Document type: </label>@{{unit.documentType}}<br>
				</div>
				<div class="col-md-4">
					<br><br><label class="ann-label">Created at:  </label>@{{unit.created_at}} <br>
				</div>
			</div>
			<div>
				<pre>@{{unit.content | json}}</pre>
			</div>
		</div>
	</div>
</div>
@stop

@section('end_javascript')
	<script>
				
	</script>
@stop
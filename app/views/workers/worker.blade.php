@extends('layouts.default')

@section('head')
<script type="text/javascript" src="/custom_assets/angular-moment.js"></script>
<script type="text/javascript" src="/custom_assets/moment.js"></script>
<script type="text/javascript" src="/custom_assets/crowdwatson.js"></script>
@stop

@section('content')
<div ng-app="workerRetrieval" ng-controller="workerByIdCtrl">
	<!-- Main column with results -->
	@include('layouts.flashdata')
	<div class="row">
		
			<div class="ng-scope disabled pull-left">
				<ul style="margin-left: 20px;" class="pagination ng-isolate-scope">
					<li><a ng-click="gotoOverview()">Back to overview</a></li>
				</ul>
			</div>
			<div class="ng-scope space-left pull-left"> 
				<ul class="pagination ng-isolate-scope">
	          		<li><a class="ng-binding" ng-click="flagWorker()">Flag worker</a></li>
	          		<li><a class="ng-binding" ng-click="openMessage(worker._id)">Message worker</a></li>
	          	</ul>
			</div>
	</div>

	<div class="mainContainer">

		<div class="bordered">
			<div class="row">
           		<div class="col-md-8">
           			<h2>@{{worker.softwareAgent_id}}  @{{worker.platformAgentId}}</h2>
           			<div class="wp-from"><span ng-show="worker.city">@{{worker.city}},</span><span ng-show="worker.region"> @{{worker.region}},</span> @{{worker.country}}</div>
           			<div class="wp-created">First seen: @{{worker.created_at}}</div>
           		</div>
           		<div class="col-md-2 box-icon">
           			<strong style="font-size: 12pt;"><i class="fa fa-file fa-2x"></i><br>Favourite task</strong> 
           		</div>
          		<div class="col-md-2 box-icon right-end">
           			<strong style="font-size: 12pt; "><i class="fa fa-clock-o fa-2x"></i><br><span am-time-ago="worker.updated_at"></span></strong>
           		</div>	
           	</div>
		</div>

		<div class="bordered" ng-init="active.tab = 'jobs'">
			<ul class="nav nav-tabs">
			  <li ng-class="{active: active.tab == 'jobs'}"><a ng-click='active.tab = "jobs"'><h4><i class="fa fa-file"></i> Jobs <span class="badge badgecount">@{{jobs.length}}</span></h4></a></li>
			  <li ng-class="{active: active.tab == 'units'}"><a ng-click='active.tab = "units"'><h4><i class="fa fa-bars"></i> Units <span class="badge badgecount">@{{units.length}}</span></h4></a></li>
			  <li ng-class="{active: active.tab == 'ann'}"><a ng-click='active.tab = "ann"'><h4><i class="fa fa-gavel"></i> Annotations <span class="badge badgecount">@{{annotations.length}}</span></h4></a></li>
			</ul>

			<div ng-switch on='active.tab'>
				<div ng-switch-when="jobs">
					@include('workers.subview.jobs')
				</div>
				<div ng-switch-when="units">
					@include('workers.subview.units')
				</div>
				<div ng-switch-when="ann">
					@include('workers.subview.annotation')
				</div>								
			</div>
		</div>

	</div>

</div>
@stop

@section('end_javascript')
	<script>
				
	</script>
@stop
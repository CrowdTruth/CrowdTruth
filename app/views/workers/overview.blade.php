@extends('layouts.default')

@section('head')
<script type="text/javascript" src="/custom_assets/crowdwatson.js"></script>	
<script type="text/javascript" src="/custom_assets/angular-moment.js"></script>
<script type="text/javascript" src="/custom_assets/moment.js"></script>
<link rel="stylesheet" type="text/css" href="/custom_assets/custom.css"></link>
<script type="text/javascript" src="/custom_assets/angular-ui.js"></script>
@stop


@section('content')
	
<div ng-controller="workerCtrl">
	<div  id="filtercolumn" class="col-md-3 ">
        <div class="panel panel-default" style="margin-top: 10px;">
			<div class="panel-heading">
				<h3 class="panel-title">Sort by
					<span class="fa pull-right ng-class: {'fa-caret-down': !sortVisible, 'fa-caret-up': sortVisible }" ng-click="setSortVisible()" ng-init="sortVisible = true"></span>
				 </h3>
			</div>
			<div ng-show="sortVisible">
				<div id="created_at" class="panel-body panel-nav-bar ng-class: { 'panel-nav-bar-active': selectedIndex == 'created_at' };" style="border-bottom: 1px solid #eee">
					<i class="fa fa-check-circle"></i> Last seen<div class="pull-right"> <i ng-click="setSortDesc('created_at')" class="fa fa-caret-down"></i>
								<i ng-click="setSortAsc('created_at')" class="fa fa-caret-up"></i></div>
				</div>
				<div id="projectedCost" class="panel-body panel-nav-bar ng-class: { 'panel-nav-bar-active': selectedIndex == 'projectedCost' }" style="border-bottom: 1px solid #eee">
					<i class="fa fa-dollar"></i> Annotations<div class="pull-right"> <i ng-click="setSortDesc('projectedCost')" class="fa fa-caret-down"></i>
								<i ng-click="setSortAsc('projectedCost')" class="fa fa-caret-up"></i></div>
				</div>
				<div id="created_at" class="panel-body panel-nav-bar ng-class: { 'panel-nav-bar-active': selectedIndex == 'created_at'}" style="border-bottom: 1px solid #eee">
					<i class="fa fa-clock-o"></i> Jobs<div class="pull-right"> <i ng-click="setSortDesc('created_at')" class="fa fa-caret-down"></i>
								<i ng-click="setSortAsc('created_at')" class="fa fa-caret-up"></i> </div>
				</div>
				<div id="id" class="panel-body panel-nav-bar ng-class: { 'panel-nav-bar-active': selectedIndex == '_id'}" style="border-bottom: 1px solid #eee">
					<i class="fa fa-clock-o"></i> Worker ID<div class="pull-right"> <i ng-click="setSortDesc('_id')" class="fa fa-caret-down"></i>
								<i ng-click="setSortAsc('_id')" class="fa fa-caret-up"></i> </div>
				</div>
			</div>
		</div>
	
	<!-- Left column for filters -->
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Filter
				<span class="fa pull-right ng-class: {'fa-caret-down': !filterVisible, 'fa-caret-up': filterVisible }" ng-click="setFilterVisible()" ng-init="filterVisible = true"></span>
				</h3>
			</div>
			<div ng-show="filterVisible">
				<!-- <div class="panel-body" style="border-bottom: 1px solid #eee">
					<i class="fa fa-fighter-jet"></i> Domain:
					<input type="text" class="pull-right" ng-keyup="setFilter()" ng-model="filter.domain">
				</div>
				<div class="panel-body" style="border-bottom: 1px solid #eee">
					<i class="fa fa-envelope-o"></i> Format:
					<input type="text" class="pull-right" ng-keyup="setFilter()" ng-model="filter.format">
				</div> -->
				<div class="panel-body" style="border-bottom: 1px solid #eee">
					<i class="fa fa-users"></i> Platform:
					<input type="text" class="pull-right" ng-keyup="setFilter()" ng-model="filter.softwareAgent_id">
				</div>
				<div class="panel-body" style="border-bottom: 1px solid #eee">
					<i class="fa fa-user"></i> Worker ID:
					<input type="text" class="pull-right" ng-keyup="setFilter()" ng-model="filter._id">
				</div>
			</div>
		</div>
	<!-- END OF LEFT COLUMN HERE -->
	</div>

	<!-- Main column with results -->
	<div id="results" class="col-md-9">
		@include('layouts.flashdata')
		

		<div class="pull-left">
			<div class="ng-scope disabled pull-left" style="margin-top: 25px;"><label for="page">Page :  </label> <input id="page" type="text" style="width: 25px;" ng-model="pageNr"> / @{{numPages()}}</div>
				<ul style="margin-left: 20px;" class="pagination ng-isolate-scope">
					<li><a ng-click="selectPage('first')" class="ng-binding">First</a></li>
					<li><a ng-click="selectPage('previous')" class="ng-binding">Previous</a></li>
					<li><a ng-click="selectPage('next')" class="ng-binding">Next</a></li>
					<li><a ng-click="selectPage('last')" class="ng-binding">Last</a></li>
				</ul>
			</div>
			<div class="ng-scope space-left pull-left"> 
				<ul class="pagination ng-isolate-scope">
	          		<li><a class="ng-binding" ng-show="selection" ng-click="analyze()">Analyse</a></li>
	          		<li><a class="ng-binding" ng-show="selection" ng-click="openMessage()">Message workers</a></li>
	          	</ul>
			</div>
			<div class="row" style="margin-left:auto; margin-right:auto; width:100%; text-align: center;">
				<div class="pull-right" style="margin-top: 23px; margin-bottom: 20px;">
					<select ng-model="itemsPerPage" ng-change="setPerPage()" ng-options="options.value for options in optionsPerPage">
						<option value="">--# per page--</option>
					</select>
				</div>
			</div>	

			<div class="mainContainer">
				<div class="bordered bgwhite" ng-repeat="result in results.data">
					<div class="row">
						<div class="col-md-1">
							<button ng-show="result.checked != true" ng-click="addWorker(result._id); result.checked = true;">Add</button>
							<button ng-show="result.checked == true" ng-click="removeWorker(result._id); result.checked = false;">Remove</button>
						</div>
		           		<div class="col-md-5">
		           			<a class="workerid" ng-click="gotoWorker(result._id)"><strong class="fat">@{{result.softwareAgent_id}}</strong> @{{result.platformAgentId}}</a>
		           			<div style="color: grey; font-size: 9pt; font-style: italic;" class="subscript"><span ng-show="result.city">@{{result.city}},</span><span ng-show="result.region"> @{{result.region}},</span> @{{result.country}}</div>
		           			<div style="color: grey; font-size: 9pt; font-style: italic;" class="subscript">First seen: <span am-time-ago="result.created_at"></span></div>
		           		</div>
		           		<div class="col-md-2" style="text-align: center;">
		           			<strong style="font-size: 12pt;"><i class="fa fa-file fa-2x"></i><br>Favourite task</strong> 
		           		</div>
		           		<div class="col-md-1" style="text-align: center;">
		           			<strong style="font-size: 12pt;"><i class="fa fa-list fa-2x"></i><br>12</strong> 
		           		</div>
		           		<div class="col-md-1" style="text-align: center;">
		           			<strong style="font-size: 12pt;"><i class="fa fa-gavel fa-2x"></i><br>64</strong> 
		           		</div>
		          		<div class="col-md-2" style="text-align: center;">
		           			<strong style="font-size: 12pt; "><i class="fa fa-clock-o fa-2x"></i><br><span am-time-ago="result.updated_at"></span></strong>
		           		</div>	
		           	</div>
				</div>
			</div>
		</div>
	</div>
	
</div>

@stop

@section('end_javascript')
	





@stop
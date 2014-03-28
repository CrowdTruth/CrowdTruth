@extends('layouts.default')

@section('head')
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.2.13/angular.min.js"></script>
<script src="//cdn.jsdelivr.net/lodash/2.4.1/lodash.underscore.min.js"></script>
<script type="text/javascript" src="http://code.angularjs.org/1.2.9/angular-resource.min.js"></script>
<script type="text/javascript" src="/custom_assets/workerretrieval.js"></script>	
<script type="text/javascript" src="/custom_assets/angular-moment.js"></script>
<script type="text/javascript" src="/custom_assets/moment.js"></script>
<link rel="stylesheet" type="text/css" href="/custom_assets/custom.css"></link>
@stop

@section('modal')
<div ng-app="workerRetrieval" ng-controller="messageCtrl" ng-init="init()">
	<div class="modal fade" id="messageModal" tabindex="-1" role="dialog" aria-labelledby="messageModal" aria-hidden="true">
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	        <h4 class="modal-title" id="myModalLabel">Message</h4>
	      </div>
		  <div class="modal-body">
	 		<label class="ann-label">To:</label> @{{recipient}}<br>
	 		<label for="subject" class="ann-label">Subject: </label><input id="subject" type="text"><br>
	 		<input type="textarea" class="messagecontent">
		  </div>
	      <div class="modal-footer">
    	     <button type="button" class="btn btn-primary" ng-click="sendMessage()">Send message</button>
	      </div>
	    </div>
	  </div>
  </div>
</div> 
@stop

@section('content')
<div ng-app="workerRetrieval" ng-controller="workerCtrl">

	<div  id="filtercolumn" class="col-md-3 ">
        <div class="panel panel-default" style="margin-top: 10px;">
			<div class="panel-heading">
				<h3 class="panel-title">Sort by
					<span class="fa pull-right ng-class: {'fa-caret-down': !sortVisible, 'fa-caret-up': sortVisible }" ng-click="setSortVisible()"></span>
				 </h3>
			</div>
			<div ng-show="sortVisible">
				<div id="completion" class="panel-body panel-nav-bar ng-class: { 'panel-nav-bar-active': selectedIndex == 'completion' };" style="border-bottom: 1px solid #eee">
					<i class="fa fa-check-circle"></i> Last seen<div class="pull-right"> <i ng-click="setSortDesc('completion')" class="fa fa-caret-down"></i>
								<i ng-click="setSortAsc('completion')" class="fa fa-caret-up"></i></div>
				</div>
				<div id="projectedCost" class="panel-body panel-nav-bar ng-class: { 'panel-nav-bar-active': selectedIndex == 'projectedCost' }" style="border-bottom: 1px solid #eee">
					<i class="fa fa-dollar"></i> Annotations<div class="pull-right"> <i ng-click="setSortDesc('projectedCost')" class="fa fa-caret-down"></i>
								<i ng-click="setSortAsc('projectedCost')" class="fa fa-caret-up"></i></div>
				</div>
				<div id="created_at" class="panel-body panel-nav-bar ng-class: { 'panel-nav-bar-active': selectedIndex == 'created_at'}" style="border-bottom: 1px solid #eee">
					<i class="fa fa-clock-o"></i> Jobs<div class="pull-right"> <i ng-click="setSortDesc('created_at')" class="fa fa-caret-down"></i>
								<i ng-click="setSortAsc('created_at')" class="fa fa-caret-up"></i> </div>
				</div>
			</div>
		</div>
	
	<!-- Left column for filters -->
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Filter
				<span class="fa pull-right ng-class: {'fa-caret-down': !filterVisible, 'fa-caret-up': filterVisible }" ng-click="setFilterVisible()"></span>
				</h3>
			</div>
			<div ng-show="filterVisible">
				<div class="panel-body" style="border-bottom: 1px solid #eee">
					<i class="fa fa-fighter-jet"></i> Domain:
					<input type="text" class="pull-right" ng-keyup="setFilter()" ng-model="filter.domain">
				</div>
				<div class="panel-body" style="border-bottom: 1px solid #eee">
					<i class="fa fa-envelope-o"></i> Format:
					<input type="text" class="pull-right" ng-keyup="setFilter()" ng-model="filter.format">
				</div>
				<div class="panel-body" style="border-bottom: 1px solid #eee">
					<i class="fa fa-file"></i> Template:
					<input type="text" class="pull-right" ng-keyup="setFilter()" ng-model="filter.hasConfiguration.type">
				</div>
				<div class="panel-body" style="border-bottom: 1px solid #eee">
					<i class="fa fa-users"></i> Platform:
					<input type="text" class="pull-right" ng-keyup="setFilter()" ng-model="filter.softwareAgent_id">
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
	          		<li><a class="ng-binding" ng-show="selection" data-toggle="modal" ng-click="gotoMessage()" data-target="#messageModal">Message workers</a></li>
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
					<div class="checkbox">
						<input type="checkbox" ng-model="result.checked">
					</div>
					<div class="row">
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
	<script>
				
	</script>
@stop
@extends('layouts.default')

@section('head')
<script type="text/javascript" src="/custom_assets/angular-moment.js"></script>
<script type="text/javascript" src="/custom_assets/moment.js"></script>
<script type="text/javascript" src="/custom_assets/crowdwatson.js"></script>
<link rel="stylesheet" type="text/css" href="/custom_assets/custom.css"></link>
@stop

@section('modal')
@stop

@section('content')
<div ng-controller="resourceCtrl">
	
			<div  id="filtercolumn" class="col-md-3 ">
			<!-- Left column for sorting -->
				<div style="margin-top: 20px;">
	               	<a href="/process"><button class="btn btn-success btn-lg" style="width: 100%; margin-bottom:10px;">Create Job</button></a>
	            </div>    
				
				<div class="panel panel-default" style="margin-top: 10px;">
					<div class="panel-heading">
						<h3 class="panel-title">Sort by
							<span class="fa pull-right ng-class: {'fa-caret-down': !sortVisible, 'fa-caret-up': sortVisible }" ng-click="sortVisible = !sortVisible" ng-init="sortVisible = true"></span>
						 </h3>
					</div>
					<div ng-show="sortVisible">
						<div id="completion" class="panel-body panel-nav-bar bordered ng-class: { 'panel-nav-bar-active': selectedIndex == 'completion' };">
							<i class="fa fa-check-circle"></i> Completion <div class="pull-right"> <i ng-click="setSort('completion', 'desc')" class="fa fa-caret-down"></i>
										<i ng-click="setSort('completion', 'asc')" class="fa fa-caret-up"></i></div>
						</div>
						<div id="projectedCost" class="panel-body panel-nav-bar bordered ng-class: { 'panel-nav-bar-active': selectedIndex == 'projectedCost' }">
							<i class="fa fa-dollar"></i> Projected cost <div class="pull-right"> <i ng-click="setSort('projectedCost', 'desc')" class="fa fa-caret-down"></i>
										<i ng-click="setSort('projectedCost', 'asc')" class="fa fa-caret-up"></i></div>
						</div>
						<div id="created_at" class="panel-body panel-nav-bar bordered ng-class: { 'panel-nav-bar-active': selectedIndex == 'created_at'}">
							<i class="fa fa-clock-o"></i> Created at <div class="pull-right"> <i ng-click="setSort('created_at', 'desc')" class="fa fa-caret-down"></i>
										<i ng-click="setSort('created_at', 'asc')" class="fa fa-caret-up"></i> </div>
						</div>
						<div id="flaggedWorkers" class="panel-body panel-nav-bar bordered ng-class: { 'panel-nav-bar-active': selectedIndex == 'flaggedWorkers' }">
							<i class="fa fa-flag"></i> Flagged workers <div class="pull-right"> <i ng-click="setSort('flaggedWorkers', 'desc')" class="fa fa-caret-down"></i>
										<i ng-click="setSort('flaggedWorkers', 'asc')" class="fa fa-caret-up"></i></div>
						</div>
						<div id="unitsCount" class="panel-body panel-nav-bar bordered ng-class: { 'panel-nav-bar-active': selectedIndex == 'unitsCount' }">
							<i class="fa fa-gavel"></i> Job size <div class="pull-right"> <i ng-click="setSortDesc('unitsCount', 'desc')" class="fa fa-caret-down"></i>
										<i ng-click="setSort('unitsCount', 'asc')" class="fa fa-caret-up"></i></div>
						</div>
						<div class="panel-body panel-nav-bar bordered ng-class: { 'panel-nav-bar-active': selectedIndex == 'user_id' }" id="user_id">
							<i class="fa fa-user"></i> Created by <div class="pull-right"> <i ng-click="setSort('user_id', 'desc')" class="fa fa-caret-down"></i>
										<i ng-click="setSort('user_id', 'asc')" class="fa fa-caret-up"></i></div>
						</div>
						<div class="panel-body panel-nav-bar bordered ng-class: { 'panel-nav-bar-active': selectedIndex == 'domain' }" id="">
							<i class="fa fa-fighter-jet"></i> Domain <div class="pull-right"> <i ng-click="setSort('domain', 'desc')" class="fa fa-caret-down"></i>
										<i ng-click="setSort('domain', 'asc')" class="fa fa-caret-up"></i></div>
						</div>
						<div class="panel-body panel-nav-bar bordered ng-class: { 'panel-nav-bar-active': selectedIndex == 'format' }" id="">
							<i class="fa fa-envelope-o"></i> Type <div class="pull-right"> <i ng-click="setSort('format', 'desc')" class="fa fa-caret-down"></i>
										<i ng-click="setSort('format', 'asc')" class="fa fa-caret-up"></i></div>
						</div>
						<div class="panel-body panel-nav-bar bordered ng-class: { 'panel-nav-bar-active': selectedIndex == 'hasConfiguration.type' }" id="">
							<i class="fa fa-file"></i> Task <div class="pull-right"> <i ng-click="setSort('hasConfiguration.type', 'desc')" class="fa fa-caret-down"></i>
										<i ng-click="setSort('hasConfiguration.type', 'asc')" class="fa fa-caret-up"></i></div>
						</div>
					</div>
				</div>
			
			<!-- Left column for filters -->
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title">Filter
						<span class="fa pull-right ng-class: {'fa-caret-down': !filterVisible, 'fa-caret-up': filterVisible }" ng-click="filterVisible = !filterVisible" ng-init="filterVisible = true"></span>
						</h3>
					</div>
					<div ng-show="filterVisible">
						<div class="panel-body bordered">
							<i class="fa fa-user"></i> Agent:
							<input type="text" class="pull-right" ng-keyup="setFilter()" ng-model="filter.username">
						</div>
						<div class="panel-body bordered">
							<i class="fa fa-fighter-jet"></i> Domain:
							<input type="text" class="pull-right" ng-keyup="setFilter()" ng-model="filter.domain">
						</div>
						<div class="panel-body bordered">
							<i class="fa fa-envelope-o"></i> Format:
							<input type="text" class="pull-right" ng-keyup="setFilter()" ng-model="filter.format">
						</div>
						<div class="panel-body bordered">
							<i class="fa fa-file"></i> Template:
							<input type="text" class="pull-right" ng-keyup="setFilter()" ng-model="filter.hasConfiguration.type">
						</div>
						<div class="panel-body bordered" >
							<i class="fa fa-users"></i> Platform:
							<input type="text" class="pull-right" ng-keyup="setFilter()" ng-model="filter.softwareAgent_id">
						</div>
						<div class="panel-body bordered" >
							<i class="fa fa-check"></i> Status:
							<input type="text" class="pull-right" ng-keyup="setFilter()" ng-model="filter.status">
						</div>
					</div>
				</div>
			<!-- END OF LEFT COLUMN HERE -->
			</div>

			<!-- Main column with results -->
			<div id="results" class="col-md-9">
				@include('layouts.flashdata')
				<div class="row" style="margin-left:auto; margin-right:auto; width:100%; text-align: center;">
					<div class="pull-left">
						<div class="ng-scope disabled pull-left nav-buttons"><label for="page">Page :  </label> <input id="page" type="text" style="width: 25px;" ng-model="pageNr"> / @{{results.numPages}}</div>
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
					<div class="pull-right" style="margin-top: 23px; margin-bottom: 20px;">
						<select ng-model="itemsPerPage" ng-change="setPerPage()" ng-options="options.value for options in optionsPerPage">
							<option value="">--# per page--</option>
						</select>
					</div>
				</div>	
				<!-- Top row is panel heading with creation date and creator -->
				<div class="panel panel-default" ng-repeat="result in results.data">
					<div class="panel-heading clearfix">
						<div style="width: 3%; float:left;">
							<input type="checkbox" ng-model="result.checked"></a>
						</div>
              			<div style="float:left;">
              				Created on @{{result.created_at}} by @{{result.user_id}}
	              		</div>
      		     		<div class="pull-right" style="width: 33%;">
		           			<div class="progress" style="margin-bottom: 0px;">	
		           				<div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="@{{result.completion * 100}}" aria-valuemin="0" aria-valuemax="100" style="width: @{{result.completion * 100}}% ;">
	   								<span class="sr-only">@{{result.completion * 100}}% Complete</span>
	  							</div>
	              			</div>
	              		</div>
	              		<div class="pull-right" style="width: 10%; padding-right: 10px;">
	              			<strong>@{{result.status}}</strong>
	              		</div>
               		</div>
               		<!-- First content row with title and description of the ct and the elapsed time -->
               		<div class="panel-body" style="padding-top: 0px; padding-bottom: 0px;">
	               		<div class="row" style="border-bottom: 1px solid #eee;">
		               		<div class="col-md-10" style="border-right: 1px solid #eee;">
		               			<h4>@{{result.hasConfiguration.content.title}}</h4>
		               			<p>@{{result.hasConfiguration.content.description }}</p>
		               			<strong style="font-size: 18px;"><i class="fa fa-file"></i> @{{result.type}}</strong> 
		               		</div>
		               		<div class="col-md-2" style="text-align: center; padding-top: 15px;">
		               			<strong style="font-size: 20px; "><i class="fa fa-clock-o fa-2x"></i><br> <span am-time-ago="result.created_at"></span></strong></p>
		               		</div>	
		               	</div>
		               	<div class="row" style="height: 90px;">
		               		<!-- This row has the following content: #sentences, judgments/unit and template info; block with worker info; block of costs, block of completion percentage; -->
		               		<div class="col-md-2" style="border-right: 1px solid #eee; height: 100%; text-align: center; display: table-cell; vertical-align: middle; padding-top: 10px;">
		               			<strong style="font-size: 24px;"><i class="fa fa-bars"></i> @{{result.hasConfiguration.content.annotationsPerUnit}}</strong><br>
		               			<strong style="font-size: 24px;"><i class="fa fa-gavel"></i> @{{result.hasConfiguration.content.unitsPerTask}}</strong><br>
		               		</div>
		               		<div class="col-md-4" style="border-right: 1px solid #eee; height: 100%; text-align: center; display: table-cell; padding-top: 10px; vertical-align: middle;"> 
		               			<h2><i class="fa fa-users"></i> @{{result.softwareAgent_id}} </h2>
		               		</div>
		               		<div class="col-md-2" style="border-right: 1px solid #eee; height:100%; text-align: center; display: table-cell; padding-top: 5px; font-size: 26px; vertical-align: middle;"> 
		                   		<i class="fa fa-flag"></i> <br>0%</strong>
		                   	</div>
						    <div class="col-md-2" style="border-right: 1px solid #eee; height: 100%; text-align: center; display: table-cell; padding-top: 10px; vertical-align: middle;">
						    	<i class="fa fa-dollar"></i><strong> /</strong> <i class="fa fa-gavel"></i> <strong> @{{result.hasConfiguration.content.reward | number: 2}}</strong>
						       	<h2><i class="fa fa-dollar"></i> @{{result.projectedCost | number: 2}}</h2>
						    </div>
						    <div class="col-md-2" style="text-align: center; height: 100%; display: table-cell; vertical-align: middle; padding-top: 10px;">
						    	<strong> <i class="fa fa-gavel"></i> @{{result.completion * result.unitsCount | number: 0}} / @{{result.unitsCount}} </strong>
						    	<h3><i class="fa fa-check-circle"></i> @{{result.completion * 100 | number: 0}} %</h3>
		               		</div>
						</div>
					<!-- Here starts hidden details -->
	          			<div class="row ng-hide" ng-show="result.detailchecked">
	          				<div style="padding-left: 10px;">
			     				Tags: @{{result.hasConfiguration.tags}}<br>
      		     				Keywords: @{{result.hasConfiguration.content.keywords}}<br>
      		     				Hit lifetime in minutes: @{{result.hasConfiguration.content.hitLifetimeInMinutes}}<br>
      		     				Expiration in minutes: @{{result.hasConfiguration.content.expirationInMinutes}}<br>
      		     				Autoapproval delay in minutes: @{{result.hasConfiguration.content.autoApprovalDelayInMinutes}}<br>
      		     				Qualification Requirement: @{{result.hasConfiguration.content.qualificationRequirement}}<br>
      		     				Assignment Review Policy: @{{result.hasConfiguration.assignmentReviewPolicy}}<br>
      		     				Answer Fields: @{{result.hasConfiguration.answerfields}}<br>
      		     				Requester Annotation: @{{result.hasConfiguration.content.requesterAnnotation}}<br>
      		     				Instructions: @{{result.hasConfiguration.content.instructions}}<br>
      		     				Notification e-mail: @{{result.hasConfiguration.content.notificationEmail}}
  		     				</div>
	          			</div>
          			</div>
          			
					<!-- Here starts the panel footer -->
               		<div class="panel-footer">
               			<div class="row">
               				<div style="float:left; padding: 3px; padding-left:8px;">	 	
			  			 		<label class="btn btn-primary">
			  			 			<input type="checkbox" id="details" style="display:none" ng-model="result.detailchecked">Details
			  			 		</label>
							</div>
					  			 
							<div class="btn-group" style="float: left; padding: 3px;">
								<button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user fa-fw"></i>Actions
					    			<span class="caret"></span>
				   				</button>
					 				 <ul class="dropdown-menu" role="menu">
					       				<li><a ng-click="perform(result, 'pause')" ng-show="result.status == 'running'"><i class="fa fa-folder-open fa-fw"></i>Pause Job</a></li>
					       				<li><a ng-click="perform(result, 'resume')" ng-show="result.status == 'paused'"><i class="fa fa-folder-open fa-fw"></i>Resume Job</a></li>
					       				<li><a ng-click="perform(result, 'order')" ng-show="result.status == 'unordered'"><i class="fa fa-folder-open fa-fw"></i>Order Job</a></li>
					       				<li><a ng-click="perform(result, 'cancel')" ng-show="result.status != 'cancelled' "><i class="fa fa-sign-out fa-fw"></i>Cancel Job</a></li>
					       				<li class="divider"></li>
					       				<li><a ng-href="/process/duplicate/@{{result._id}}"><i class="fa fa-sign-out fa-fw"></i>Duplicate Job</a></li>
					       				<li><a ng-click="perform(result, 'delete')"><i class="fa fa-sign-out fa-fw"></i>Delete Job</a></li>
					   				</ul>
								</div>
						</div>
					</div>								
				<!--End of panel  -->
				</div>	
			<!-- Close results column -->
			</div>
</div>
@stop

@extends('layouts.default')

@section('head')
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.2.13/angular.min.js"></script>
<script src="//cdn.jsdelivr.net/lodash/2.4.1/lodash.underscore.min.js"></script>
<script type="text/javascript" src="http://code.angularjs.org/1.2.9/angular-resource.min.js"></script>
<script type="text/javascript" src="/custom_assets/dataretrieval.js"></script>	
<script type="text/javascript" src="/custom_assets/angular-moment.js"></script>
<script type="text/javascript" src="/custom_assets/moment.js"></script>
@stop


@section('content')
<div ng-app="dataRetrieval" ng-controller="resourceCtrl">

			<div  id="filtercolumn" class="col-md-3 ">
			<!-- Left column for sorting -->
				<div>
	               	<a href="/process"><button class="btn btn-success btn-lg" style="width: 100%; margin-bottom:10px;">Create Job</button></a>
	            </div>

				<div>
	               	<button class="btn btn-primary btn-lg" style="width: 100%; margin-bottom:10px;" ng-click="analyze()">Analyse</button>
	            </div>
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title">Sort by:</h3>
					</div>
					<div class="panel-body" id="annotationsPerUnit" style="border-bottom: 1px solid #eee">
						<i class="fa fa-check-circle"></i> Completion <div class="pull-right"> <i ng-click="setSortDesc('completion')" class="fa fa-caret-down"></i>
									<i ng-click="setSortAsc('completion')" class="fa fa-caret-up"></i></div><br>
					</div>
					<div class="panel-body" id="totalCost" style="border-bottom: 1px solid #eee">
						<i class="fa fa-dollar"></i> Projected cost <div class="pull-right"> <i ng-click="setSortDesc('projectedCost')" class="fa fa-caret-down"></i>
									<i ng-click="setSortAsc('projectedCost')" class="fa fa-caret-up"></i></div><br>
					</div>
					<div class="panel-body" id="created_at" style="border-bottom: 1px solid #eee">
						<i class="fa fa-clock-o"></i> Created at <div class="pull-right"> <i ng-click="setSortDesc('created_at')" class="fa fa-caret-down"></i>
									<i ng-click="setSortAsc('created_at')" class="fa fa-caret-up"></i> </div><br>
					</div>
					<div class="panel-body" id="flaggedWorkers" style="border-bottom: 1px solid #eee">
						<i class="fa fa-flag"></i> Flagged workers <div class="pull-right"> <i ng-click="setSortDesc('flaggedWorkers')" class="fa fa-caret-down"></i>
									<i ng-click="setSortAsc('flaggedWorkers')" class="fa fa-caret-up"></i></div><br>
					</div>
					<div class="panel-body" id="jobSize">
						<i class="fa fa-gavel"></i> Job size <div class="pull-right"> <i ng-click="setSortDesc('unitsCount')" class="fa fa-caret-down"></i>
									<i ng-click="setSortAsc('unitsCount')" class="fa fa-caret-up"></i></div><br>
					</div>
					<div class="panel-body" id="jobSize">
						<i class="fa fa-user"></i> Created by <div class="pull-right"> <i ng-click="setSortDesc('user_id')" class="fa fa-caret-down"></i>
									<i ng-click="setSortAsc('user_id')" class="fa fa-caret-up"></i></div><br>
					</div>
				</div>

			
			<!-- Left column for filters -->
				<!-- <div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title">Apply filter:</h3>
					</div>
					<div class="panel-body" style="border-bottom: 1px solid #eee">
						<i class="fa fa-user"></i> {{Form::label('createdBy', 'Created by:')}}<br>
						{{Form::input('createdBy','createdBy')}}
					</div>
					<div class="panel-body" style="border-bottom: 1px solid #eee">
						<i class="fa fa-users"></i> {{Form::label('user', 'Platform:')}}<br>
						{{Form::checkbox('')}} CrowdFlower<br>
						{{Form::checkbox('')}} Amazon MTurk
					</div>
					<seperator/>
					<div class="panel-body" style="border-bottom: 1px solid #eee">
						<i class="fa fa-file"></i> {{Form::label('user', 'Template:')}}<br>
						{{Form::checkbox('')}} Relation Direction<br>
						{{Form::checkbox('')}} Relation Extraction<br>
						{{Form::checkbox('')}} Factor Span
					</div>
					<div class="panel-body">
						Domain, Type, Status (Running, Completed)
					</div>
				</div> -->
			<!-- END OF LEFT COLUMN HERE -->
			</div>

			<!-- Main column with results -->
			<div id="results" class="col-md-9">
				@include('layouts.flashdata')	
						<div class="panel panel-default" ng-repeat="result in results.data">
						<!-- Top row is panel heading with creation date and creator -->
						<div class="panel-heading clearfix">
							<div style="width: 5%; float:left;">
								<input type="checkbox" ng-model="result.checked"></a>
							</div>
	              			<div style="float:left;">
	              				Created on @{{result.created_at}} by @{{result.user_id}}
		              		</div>
			           		<div class="pull-right" style="width: 33%;">
			           			<div class="progress" style="margin-bottom: 0px;">	
			           				<div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="@{{result.completion}}%" aria-valuemin="0" aria-valuemax="100" style="width: @{{result.completion}} %;">
		   								<span class="sr-only">@{{result.completion}}% Complete</span>
		  							</div>
		              			</div>
		              		</div>
	               		</div>
	               		<!-- First content row with title and description of the ct and the elapsed time -->
	               		<div class="panel-body" style="padding-top: 0px; padding-bottom: 0px;">
		               		<div class="row" style="border-bottom: 1px solid #eee;">
			               		<div class="col-md-10" style="border-right: 1px solid #eee;">
			               			<h4>@{{result.hasConfiguration.content.title}}</h4>
			               			<p>@{{result.hasConfiguration.content.description }}</p>
			               			<strong style="font-size: 18px;"><i class="fa fa-file"></i> @{{result.hasConfiguration.type}}</strong> 
			               		</div>
			               		<div class="col-md-2" style="text-align: center; padding-top: 15px;">
			               			<strong style="font-size: 20px; "><i class="fa fa-clock-o fa-2x"></i><br> <span am-time-ago="result.created_at"></span></strong></p>
			               		</div>	
			               	</div>
			               	<div class="row" style="height: 90px;">
			               		<!-- This row has the following content: #sentences, judgments/unit and template info; block with worker info; block of costs, block of completion percentage; -->
			               		<div class="col-md-2" style="border-right: 1px solid #eee; height: 100%; text-align: center; display: table-cell; vertical-align: middle; padding-top: 10px;">
			               			<strong style="font-size: 24px;"><i class="fa fa-bars"></i> @{{result.hasConfiguration.content.judgmentsPerUnit}}</strong><br>
			               			<strong style="font-size: 24px;"><i class="fa fa-gavel"></i> @{{result.hasConfiguration.content.unitsPerTask}}</strong><br>
			               		</div>
			               		<div class="col-md-4" style="border-right: 1px solid #eee; height: 100%; text-align: center; display: table-cell; padding-top: 10px; vertical-align: middle;"> 
			               			<h2><i class="fa fa-users"></i> @{{result.hasConfiguration.content.platform}} </h2>
			               		</div>
			               		<div class="col-md-2" style="border-right: 1px solid #eee; height:100%; text-align: center; display: table-cell; padding-top: 5px; font-size: 26px; vertical-align: middle;"> 
			                   		<i class="fa fa-flag"></i> <br> %</strong>
			                   	</div>
							    <div class="col-md-2" style="border-right: 1px solid #eee; height: 100%; text-align: center; display: table-cell; padding-top: 10px; vertical-align: middle;">
							    	<i class="fa fa-dollar"></i><strong> /</strong> <i class="fa fa-gavel"></i> <strong> @{{result.hasConfiguration.content.reward}}</strong>
							       	<h2><i class="fa fa-dollar"></i>@{{result.projectedCost}}</h2>
							    </div>
							    <div class="col-md-2" style="text-align: center; height: 100%; display: table-cell; vertical-align: middle; padding-top: 10px;">
							    	<strong> <i class="fa fa-gavel"></i> / @{{result.unitsCount}} </strong>
							    	<h2><i class="fa fa-check-circle"></i> %</h2>
			               		</div>
							</div>
							 <!-- Here starts the hidden details field, see js at bottom of page -->
							<div id="" class="row" style="display: none;">
					            <table class="table table-striped">
					           	
					 	    	</table>
	          				</div>
	          			</div>
						<!-- Here starts the panel footer -->
	               		<div class="panel-footer">
	               			<div class="row">
	               				<div style="float:left; padding: 3px; padding-left:8px;">
						  			<input class="btn btn-primary" type="button" id="" ng-click="showDetail(result)" value="Details">
								</div>
								<!-- <div class="btn-group" style="float: left; padding: 3px;">
									<button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user fa-fw"></i>Actions
						    			<span class="caret"></span>
					   				</button>
					 				<ul class="dropdown-menu" role="menu">
					       				<li><a href="#"><i class="fa fa-folder-open fa-fw"></i>Pause Job</a></li>
					       				<li><a href=""><i class="fa fa-sign-out fa-fw"></i>Cancel Job</a></li>
					       				<li class="divider"></li>
					       				<li><a href=""><i class="fa fa-sign-out fa-fw"></i>Duplicate Job</a></li>
					       				<li><a href=""><i class="fa fa-sign-out fa-fw"></i>Delete Job</a></li>
					   				</ul>
								</div>
 -->							</div>
						</div>								
					<!--End of panel  -->
					</div>	
			<!-- Close results column -->
			</div>
</div>
@stop

@section('end_javascript')
	<script>
				
	</script>
@stop
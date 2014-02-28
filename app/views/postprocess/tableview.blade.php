@extends('layouts.default')

@section('container', 'full-container')
@section('head')
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.2.13/angular.min.js"></script>
<script src="//cdn.jsdelivr.net/lodash/2.4.1/lodash.underscore.min.js"></script>
<script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/restangular/1.3.1/restangular.min.js"></script>
<script type="text/javascript" src="http://code.angularjs.org/1.2.9/angular-resource.min.js"></script>
<script data-require="ng-table@*" data-semver="0.3.0" src="http://bazalt-cms.com/assets/ng-table/0.3.0/ng-table.js"></script>


@stop

@section('content')
			<div class="maincolumn CW_box_style" style="width:auto;" ng-app="dataRetrieval" ng-controller="resourceCtrl">
				<div>
					<table class="table table-striped table-condensed" style="widht: auto;">
						<thead>
							<tr>
								<th><i class="fa fa-check-square-o"></i></th>
								<th><i ng-click="setSortDesc('created_at')" class="fa fa-caret-down"><i ng-click="setSortAsc('created_at')" class="fa fa-caret-up"></i> Created at</th>
								<th><i ng-click="setSortDesc('user_id')" class="fa fa-caret-down"><i ng-click="setSortAsc('user_id')" class="fa fa-caret-up"></i> Creator</th>
								<th><i ng-click="setSortDesc('domain')" class="fa fa-caret-down"><i ng-click="setSortAsc('domain')" class="fa fa-caret-up"></i> Domain</th>
								<th><i ng-click="setSortDesc('format')" class="fa fa-caret-down"><i ng-click="setSortAsc('format')" class="fa fa-caret-up"></i> Format</th>
								<th><i ng-click="setSortDesc('type')" class="fa fa-caret-down"><i ng-click="setSortAsc('type')" class="fa fa-caret-up"></i> Template</th>
								<th><i ng-click="setSortDesc('has_configuration.content.unitsPerTask')" class="fa fa-caret-down"><i ng-click="setSortAsc('has_configuration.content.unitsPerTask')" class="fa fa-caret-up"></i> Units</th>
								<th><i ng-click="setSortDesc('hasGold')" class="fa fa-caret-down"><i ng-click="setSortAsc('hasGold')" class="fa fa-caret-up"></i> Has Gold</th>
								<th><i ng-click="setSortDesc('has_configuration.platform')" class="fa fa-caret-down"><i ng-click="setSortAsc('has_configuration.platform')" class="fa fa-caret-up"></i> Platform</th>
								<th><i ng-click="setSortDesc('has_configuration.content.reward')" class="fa fa-caret-down"><i ng-click="setSortAsc('has_configuration.content.reward')" class="fa fa-caret-up"></i> Reward</th>
								<th><i ng-click="setSortDesc('has_configuration.content.rewardPerHour')" class="fa fa-caret-down"><i ng-click="setSortAsc('has_configuration.content.rewardPerHour')" class="fa fa-caret-up"></i> Reward hourly</th>
								<th><i ng-click="setSortDesc('has_configuration.content.totalCost')" class="fa fa-caret-down"><i ng-click="setSortAsc('has_configuration.content.totalCost')" class="fa fa-caret-up"></i> Total Cost</th>
								<th><i ng-click="setSortDesc('flaggedPercentage')" class="fa fa-caret-down"><i ng-click="setSortAsc('flaggedPercentage')" class="fa fa-caret-up"></i> Flagged %</th>
								<th><i ng-click="setSortDesc('flaggedWorkers')" class="fa fa-caret-down"><i ng-click="setSortAsc('flaggedWorkers')" class="fa fa-caret-up"></i> Flagged abs</th>
								<th><i ng-click="setSortDesc('has_configuration.content.judgmentsPerUnit')" class="fa fa-caret-down"><i ng-click="setSortAsc('has_configuration.content.judgmentsPerUnit')" class="fa fa-caret-up"></i> Judgments</th>
								<th><i ng-click="setSortDesc('totalJudgments')" class="fa fa-caret-down"><i ng-click="setSortAsc('totalJudgments')" class="fa fa-caret-up"></i> Total Judgments</th>
								<th><i ng-click="setSortDesc('status')" class="fa fa-caret-down"><i ng-click="setSortAsc('status')" class="fa fa-caret-up"></i> Status</th>
								<th><i ng-click="setSortDesc('completion')" class="fa fa-caret-down"><i ng-click="setSortAsc('completion')" class="fa fa-caret-up"></i> Completion</th>
							</tr>
							<tr>
								<td></td>
								<td><input type="datetime" ng-model="filter.created_at"></td>
								<td><input type="text" ng-model="filter.user_id"></td>
								<td><input type="text" ng-model="filter.domain"></td>
								<td><input type="text"></td>
								<td><input type="text"></td>
								<td><input type="number"></td>
								<td><input type="checkbox"></td>
								<td><input type="text"></td>
								<td><input type="number"></td>
								<td><input type="number"></td>
								<td><input type="number"></td>
								<td><input type="number"></td>
								<td><input type="number"></td>
								<td><input type="number"></td>
								<td><input type="number"></td>
								<td><input type="text"></td>
								<td><input type="number"></td>
							</tr>
						</thead>
						<tbody>
							<tr ng-repeat="result in results">
						        <td>{{Form::checkbox('')}}</td>
						        <td>@{{result.created_at}}</td>
						        <td>@{{result.user_id}}</td>
						        <td>@{{result.domain}}</td>
						        <td>@{{result.format}}</td>
						        <td>@{{result.has_configuration.type}}</td>
						        <td>@{{result.has_configuration.content.unitsPerTask}}</td>
						        <td>@{{hasGold}}</td>
						        <td>@{{result.has_configuration.content.platform}}</td>
						        <td>@{{result.has_configuration.content.reward}}</td>
						        <td>@{{result.has_configuration.content.rewardPerHour}}</td>
						        <td>@{{result.has_configuration.content.totalCost}}</td>
						        <td>@{{result.flaggedPercentage}}</td>
						        <td>@{{result.flaggedWorkers}}</td>
						        <td>@{{result.has_configuration.content.judgmentsPerUnit}}</td>
						        <td>@{{result.totalJudgments}}</td>
						        <td>@{{result.status}}</td>
						        <td>@{{result.completion}}</td>
						   	</tr>
    					</tbody>
					</table>
					@{{filter}}
					@{{result}}
				</div>
		
@stop

@section('end_javascript')
<script>

function sortModel(method){
			// checks whether current method is on descend or ascend and stores it in variable
			var sort = 'none';
			if($("#"+method).hasClass("panel-nav-bar-ascending"))
				{sort = 'desc';}
			if($('#'+method).hasClass("panel-nav-bar-descending"))
				{sort = 'asc';}
			// ajax request with the method of sorting and the ascend/descend option, response is the result view
			$.ajax({
				url: 'sort/' + method + "/" + sort,
				type: 'GET'
			}).done(function( data ) {
				// put the response in the results div
               	$("#results").html(data);
               	// reset the styling of the sorting panel
            	$(".panel-nav-bar").removeClass("panel-nav-bar-ascending panel-nav-bar-descending");
            	// set the styling on the sorting panel and remember whether it is descending or ascending
            	if (sort == "desc")
					{$("#"+method).addClass("panel-nav-bar-descending");}				
	       		else {$("#"+method).addClass("panel-nav-bar-ascending");}
        	});
		}

</script>

@stop


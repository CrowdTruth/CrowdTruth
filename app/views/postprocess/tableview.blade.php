@extends('layouts.default')

@section('container', 'full-container')

@section('head')
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.2.13/angular.min.js"></script>
<script src="//cdn.jsdelivr.net/lodash/2.4.1/lodash.underscore.min.js"></script>
<script type="text/javascript" src="http://code.angularjs.org/1.2.9/angular-resource.min.js"></script>
<script type="text/javascript" src="/custom_assets/dataretrieval.js"></script>
@stop

@section('content')
			<div class="maincolumn CW_box_style" style="width:auto;" ng-app="dataRetrieval" ng-controller="resourceCtrl">
				<div>
				<button class="btn btn-primary" ng-click="analyze()">Analyze job(s)</button>
					<table class="table table-striped table-condensed" style="width: auto;">
						<thead>
							<tr>
								<th><i class="fa fa-check-square-o"></i></th>
								<th>
									<i ng-click="setSortDesc('created_at')" class="fa fa-caret-down"></i>
									<i ng-click="setSortAsc('created_at')" class="fa fa-caret-up"></i> Created at 
								</th>
								<th>
									<i ng-click="setSortDesc('wasAttributedToUserAgent.username')" class="fa fa-caret-down"></i>
									<i ng-click="setSortAsc('wasAttributedToUserAgent.username')" class="fa fa-caret-up"></i> Creator
								</th>
								<th>
									<i ng-click="setSortDesc('domain')" class="fa fa-caret-down"></i>
									<i ng-click="setSortAsc('domain')" class="fa fa-caret-up"></i> Domain
								</th>
								<th>
									<i ng-click="setSortDesc('format')" class="fa fa-caret-down"></i>
									<i ng-click="setSortAsc('format')" class="fa fa-caret-up"></i> Format
								</th>
								<th>
									<i ng-click="setSortDesc('type')" class="fa fa-caret-down"></i>
									<i ng-click="setSortAsc('type')" class="fa fa-caret-up"></i> Template
								</th>
								<th>
									<i ng-click="setSortDesc('hasConfiguration.content.unitsPerTask')" class="fa fa-caret-down"></i>
									<i ng-click="setSortAsc('hasConfiguration.content.unitsPerTask')" class="fa fa-caret-up"></i> Units
								</th>
								<th>
									<i ng-click="setSortDesc('hasGold')" class="fa fa-caret-down"></i>
									<i ng-click="setSortAsc('hasGold')" class="fa fa-caret-up"></i> Has Gold
								</th>
								<th>
									<i ng-click="setSortDesc('hasConfiguration.platform')" class="fa fa-caret-down"></i>
									<i ng-click="setSortAsc('hasConfiguration.platform')" class="fa fa-caret-up"></i> Platform
								</th>
								<th>
									<i ng-click="setSortDesc('hasConfiguration.content.reward')" class="fa fa-caret-down"></i>
									<i ng-click="setSortAsc('hasConfiguration.content.reward')" class="fa fa-caret-up"></i> Reward
								</th>
								<th>
									<i ng-click="setSortDesc('hasConfiguration.content.rewardPerHour')" class="fa fa-caret-down"></i>
									<i ng-click="setSortAsc('hasConfiguration.content.rewardPerHour')" class="fa fa-caret-up"></i> Reward hourly
								</th>
								<th>
									<i ng-click="setSortDesc('hasConfiguration.content.projectedCost')" class="fa fa-caret-down"></i>
									<i ng-click="setSortAsc('hasConfiguration.content.projectedCost')" class="fa fa-caret-up"></i> Projected Cost
								</th>
								<th>
									<i ng-click="setSortDesc('flaggedPercentage')" class="fa fa-caret-down"></i>
									<i ng-click="setSortAsc('flaggedPercentage')" class="fa fa-caret-up"></i> Flagged %
								</th>
								<th>
									<i ng-click="setSortDesc('flaggedWorkers')" class="fa fa-caret-down"></i>
									<i ng-click="setSortAsc('flaggedWorkers')" class="fa fa-caret-up"></i> Flagged abs
								</th>
								<th>
									<i ng-click="setSortDesc('hasConfiguration.content.judgmentsPerUnit')" class="fa fa-caret-down"></i>
									<i ng-click="setSortAsc('hasConfiguration.content.judgmentsPerUnit')" class="fa fa-caret-up"></i> Judgments
								</th>
								<th>
									<i ng-click="setSortDesc('totalJudgments')" class="fa fa-caret-down"></i>
									<i ng-click="setSortAsc('totalJudgments')" class="fa fa-caret-up"></i> Total Judgments
								</th>
								<th>
									<i ng-click="setSortDesc('status')" class="fa fa-caret-down"></i>
									<i ng-click="setSortAsc('status')" class="fa fa-caret-up"></i> Status
								</th>
								<th>
									<i ng-click="setSortDesc('completion')" class="fa fa-caret-down"></i>
									<i ng-click="setSortAsc('completion')" class="fa fa-caret-up"></i> Completion
								</th>
							</tr>
							<tr>
								<td></td>
								<td><input type="datetime" ng-keyup="setFilter('created_at', filter.created_at)" ng-model="filter.created_at"></td>
								<td><input type="text" ng-keyup="setFilter('wasAttributedToUserAgent.username', filter.wasAttributedToUserAgent.username)" ng-model="filter.wasAttributedToUserAgent.username"></td>
								<td><input type="text" ng-keyup="setFilter('domain', filter.domain)" ng-model="filter.domain"></td>
								<td><input type="text" ng-keyup="setFilter('format', filter.format)" ng-model="filter.format"></td>
								<td><input type="text" ng-keyup="setFilter('hasConfiguration.type', filter.hasConfiguration.type)" ng-model="filter.hasConfiguration.type"></td>
								<td><input type="number" ng-keyup="setFilter('hasConfiguration.content.unitsPerTask', filter.hasConfiguration.content.unitsPerTask)" ng-model="filter.hasConfiguration.content.unitsPerTask"></td>
								<td><input type="checkbox" ng-keyup="setFilter('hasGold', filter.hasGold)" ng-model="filter.hasGold"></td>
								<td><input type="text" ng-keyup="setFilter('hasConfiguration.platform', filter.hasConfiguration.platform)" ng-model="filter.hasConfiguration.platform"></td>
								<td><input type="number" ng-keyup="setFilter('hasConfiguration.content.reward', filter.hasConfiguration.content.reward)" ng-model="filter.hasConfiguration.content.reward"></td>
								<td><input type="number" ng-keyup="setFilter('hasConfiguration.content.rewardPerHour', filter.hasConfiguration.content.rewardPerHour)" ng-model="filter.hasConfiguration.content.rewardPerHour"></td>
								<td><input type="number" ng-keyup="setFilter('hasConfiguration.content.projectedCost', filter.hasConfiguration.content.projectedCost)" ng-model="filter.hasConfiguration.content.projectedCost"></td>
								<td><input type="number" ng-keyup="setFilter('flaggedPercentage', filter.flaggedPercentage)" ng-model="filter.flaggedPercentage"></td>
								<td><input type="number" ng-keyup="setFilter('flaggedWorkers', filter.flaggedWorkers)" ng-model="filter.flaggedWorkers"></td>
								<td><input type="number" ng-keyup="setFilter('hasConfiguration.content.judgmentsPerUnit', filter.hasConfiguration.content.judgmentsPerUnit)" ng-model="filter.hasConfiguration.content.judgmentsPerUnit"></td>
								<td><input type="number" ng-keyup="setFilter('unitsCount', filter.unitsCount)" ng-model="filter.unitsCount"></td>
								<td><input type="text" ng-keyup="setFilter('status', filter.status)" ng-model="filter.status"></td>
								<td><input type="number" ng-keyup="setFilter('completion', filter.completion)" ng-model="filter.completion"></td>
							</tr>
						</thead>
						<tbody>
							<tr ng-repeat="result in results.data">
						        <td><input type="checkbox" ng-model="result.checked"></td>
						        <td>@{{result.created_at}}</td>
						        <td>@{{result.wasAttributedToUserAgent.username}}</td>
						        <td>@{{result.domain}}</td>
						        <td>@{{result.format}}</td>
						        <td>@{{result.hasConfiguration.type}}</td>
						        <td>@{{result.hasConfiguration.content.unitsPerTask}}</td>
						        <td>@{{result.hasGold}}</td>
						        <td>@{{result.hasConfiguration.content.platform}}</td>
						        <td>@{{result.hasConfiguration.content.reward}}</td>
						        <td>@{{result.hasConfiguration.content.rewardPerHour}}</td>
						        <td>@{{result.hasConfiguration.content.totalCost}}</td>
						        <td>@{{result.flaggedPercentage}}</td>
						        <td>@{{result.flaggedWorkers}}</td>
						        <td>@{{result.hasConfiguration.content.judgmentsPerUnit}}</td>
						        <td>@{{result.totalJudgments}}</td>
						        <td>@{{result.status}}</td>
						        <td>@{{result.completion}}</td>
						   	</tr>
    					</tbody>
					</table>
									
				</div>			
		
@stop

@section('end_javascript')
<script>

</script>

@stop


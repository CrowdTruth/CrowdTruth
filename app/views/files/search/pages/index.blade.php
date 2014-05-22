@extends('layouts.default')

@section('container', 'full-container')

@section('head')
		{{ javascript_include_tag('angular.min.js') }}
		<script src='//cdn.jsdelivr.net/restangular/1.3.1/restangular.min.js'></script>
		<script src="//cdn.jsdelivr.net/lodash/2.4.1/lodash.underscore.min.js"></script>
@stop

@section('content')
				<!-- START search_content --> 
				<div class="col-xs-12" ng-app="facetedSearchApp">
					<div class='maincolumn CW_box_style'>
@include('layouts.flashdata')						
@include('files.layouts.nav')
						<div class='tab' ng-controller="facetedSearchController">
							<div class='row'>

								<div class='col-xs-3'>

									<div class="form-group">
										<label for="format"> Formats </label>
										<select name="field[format]" id="format" class="form-control" ng-model="formData.format" ng-change="changeFormat()">
											<option ng-repeat="format in formats" value="@{{ format }}">@{{ format }}</option>
											<option value="Images">Images</option>
											<option value="Videos">Videos</option>
										</select>
									</div>

									<div class="form-group" ng-show="showDomains">
										<label for="domain">Domains</label>						
										<div class="checkbox" ng-repeat="domain in domains">
											<label>
												<input name="domains[]" type="checkbox" value="@{{ domain }}" ng-checked="selectedDomains.indexOf(domain) > -1" ng-click="changeDomain(domain)">@{{ domain }}
											</label>
										</div>
									</div>

									<div class="form-group" ng-show="showDocumentTypes" ng-model="formData.documentType">
										<label for="documentType">Document-types</label>						
										<div class="checkbox" ng-repeat="documentType in documentTypes">
											<label>
												<input name="field[documentType]" type="checkbox" value="@{{ documentType }}" ng-click="changeDocumentType(documentType)">@{{ documentType }}
											</label>
										</div>
									</div>									

									<div class="form-group" ng-show="showUserAgents">
										<label for="userAgents">User Agents</label>						
										<div class="checkbox" ng-repeat="userAgent in userAgents">
											<label>
												<input name="field[userAgent]" type="checkbox" value="@{{ userAgent }}" ng-checked="selectedDomains.indexOf(userAgent) > -1" ng-click="changeUserAgent(userAgent)">@{{ userAgent.firstname }} @{{ userAgent.lastname }}
											</label>
										</div>
									</div>
								</div>								
    							<div class='col-xs-9'>


<ul class="nav nav-pills">
	<li class="active"><a href="#all" data-toggle="tab">All</a></li>
	<li ng-repeat="selectedDocumentType in selectedDocumentTypes">
		<a href="#@{{selectedDocumentType}}" data-toggle="tab">@{{selectedDocumentType}}</a>
	</li>
</ul>


<div class="tab-content">
	<div class="tab-pane active" id="all">

									<div class='table-responsive'>
										<table class='table table-striped'>
											<thead>
												<tr>
													<th>ID</th>
													<th>Title</th>
													<th>Document-Type</th>
												</tr>
											</thead>
											<tbody>
										        <tr ng-repeat="row in results" on-finish-render="test">
										            <td>@{{row._id}}</td>
										            <td>@{{row.title}}</td>
										            <td>@{{row.documentType}}</td>
										        </tr>
										    </tbody>
							        	</table>
	    							</div>
	</div>
	<div ng-repeat="selectedDocumentType in selectedDocumentTypes" class="tab-pane" id="@{{selectedDocumentType}}" ng-switch on="selectedDocumentType">

		<div class='table-responsive' ng-switch-when="relex-structured-sentence">
			<table class='table table-striped'>
				<thead>
					<tr>
						<th>Relation</th>
						<th>First Term</th>
						<th>Second Term</th>
						<th>Sentence</th>
						<th>Word Count</th>
					</tr>
				</thead>
				<tbody>
			        <tr ng-repeat="row in results | filter:{documentType: selectedDocumentType}:true">
			            <td>@{{row.content.relation.original}}</td>
			            <td>@{{row.content.terms.first.text}}</td>
			            <td>@{{row.content.terms.second.text}}</td>
			            <td>sentence</td>
			            <td>@{{row.content.properties.sentenceWordCount}}</td>
			        </tr>
			    </tbody>
			</table>
		</div>

		<div class='table-responsive' ng-switch-default>
			<table class='table table-striped'>
				<thead>
					<tr>
						<th>ID</th>
						<th>Title</th>
						<th>Document-Type</th>
					</tr>
				</thead>
				<tbody>
			        <tr ng-repeat="row in results | filter:{documentType: selectedDocumentType}:true">
			            <td>@{{row._id}}</td>
			            <td>@{{row.title}}</td>
			            <td>@{{row.documentType}}</td>
			        </tr>
			    </tbody>
			</table>
		</div>


	</div>
</div>	    							
<!--     								<pre>

@{{formData|json}}
@{{formats|json}}
@{{selectedDomains|json}}
@{{documentTypes|json}}
@{{userAgents|json}}
@{{results|json}}

    								</pre> -->
    							</div>
    						</div>
						</div>
					</div>
				</div>
				<!-- STOP search_content --> 				
@stop

@section('end_javascript')
	{{ javascript_include_tag('jquery.tablesorter.min.js') }}
	{{ javascript_include_tag('jquery.tablesorter.widgets.min.js') }}
<script>


function enableTable(){
	$('.table').tablesorter({
			theme : 'bootstrap',
			stringTo: "max",

			// initialize zebra and filter widgets
			widgets: ["filter"],

			widgetOptions: {
			// include child row content while filtering, if true
			filter_childRows  : false,
			// class name applied to filter row and each input
			filter_cssFilter  : 'tablesorter-filter',
			// search from beginning
			filter_startsWith : false,
			// Set this option to false to make the searches case sensitive 
			filter_ignoreCase : true
		}
	}).trigger("update");
}



var app = angular.module('facetedSearchApp', ['restangular']);

app.config(function(RestangularProvider) {
    RestangularProvider.setBaseUrl('{{ URL::to('api/v2/') }}');
});

function facetedSearchController($scope, Restangular){

    $scope.$on('test', function(ngRepeatFinishedEvent) {
        enableTable();
    });

	$scope.selectedDomains = [];
	$scope.selectedDocumentTypes = [];
	$scope.selectedUserAgents = [];

	$scope.changeFormat = function() {
		getDomains($scope, Restangular);
	};

	$scope.changeDomain = function(domain) {
		updateSelection($scope.selectedDomains, domain);
		getDocumentTypes($scope, Restangular);
	};

	$scope.changeDocumentType = function(documentType) {
		updateSelection($scope.selectedDocumentTypes, documentType);
		getResults($scope, Restangular);
	};

	Restangular.all('distinct/?collection=users').getList().then(function(data) {
		console.log(data);
		$scope.userAgents = data;
		$scope.showUserAgents = true;
	});	

	Restangular.all('distinct/format').getList().then(function(data) {
		console.log(data);
		$scope.formats = data;
	});
}

function getDomains($scope, Restangular){
	Restangular.all('distinct/domain').getList().then(function(data) {
		console.log(data);
		$scope.domains = data;
		$scope.showDomains = true;
	});

}

function getDocumentTypes($scope, Restangular){
	var query = buildQuery($scope.selectedDomains, "domain");

	if (jQuery.isEmptyObject(query))
	{
		$scope.showDocumentTypes = false;
	} else {
		Restangular.all('distinct/documentType').getList(query).then(function(data) {
			console.log(data);
			$scope.documentTypes = data;
			$scope.showDocumentTypes = true;
		});
	}

	getResults($scope, Restangular);
}

function updateSelection(list, selected){
	var idx = list.indexOf(selected);
	if (idx > -1) {
		list.splice(idx, 1);
	} else {
		list.push(selected);
	}
}

function buildQuery(list, fieldKey){
	var query = {};

	for (var i in list) {
	    query['field[' + fieldKey + '][' + i + ']'] = list[i];
	}

	return query;
}

function getResults($scope, Restangular){
	var domainsQuery = buildQuery($scope.selectedDomains, "domain");
	var documentTypesQuery = buildQuery($scope.selectedDocumentTypes, "documentType");

	Restangular.all('?collection=entity').getList($.extend(domainsQuery, documentTypesQuery)).then(function(data) {
		console.log(data);
		// alert('done');
		$scope.results = data;
	});
}

app.directive('onFinishRender', function ($timeout) {
    return {
        restrict: 'A',
        link: function (scope, element, attr) {
            if (scope.$last === true) {
                $timeout(function () {
                    scope.$emit(attr.onFinishRender);
                });
            }
        }
    }
});

</script>

@stop
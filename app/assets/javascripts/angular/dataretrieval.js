var app = angular.module("dataRetrieval", [ 'ngResource']);

	//write resource service class


//inject resourceSvc in this controller
app.controller("resourceCtrl", function($scope, $resource) {
	// On init fetches first pagination object
	$scope.results = getResource($resource,"","","","");

	// resourceSvc.get({}, 
	// 	function(data){$scope.results = data.data;}	
	// );

	// //call resource service after setting sort
 // 	$scope.setSortAsc = function( column ){
 // 		var sort = "sortBy=" + column + "&order=asc";

 // 		resourceSvc.get({sort: 'sort'},
 // 			function(data){
 // 				$scope.results = data.data;
 // 			},
 // 			function(data){
 // 				$scope.results = "Didn't fetch a thing bro, " + data;
 // 			}
 // 		);
 // 	} 

 // 	$scope.setSortDesc = function( column ){
 // 		var sort = "sortBy=" + column + "&sort=desc";
 // 		alert(sort);
 // 	} 

 // 	//call resource service after appending filter
 // 	if($scope.filter != ''){

 // 	}

});

var getResource = function($resource, page, perPage, sort, filter){
		alert('this is the api-call' + page + perPage + sort + filter);
		return $resource('/api/v3/?page=:page&perpage=:perPage&sort=:sort&filter=:filter', 
			{page: '@page', perPage: '@perPage', sort: '@sort', filter: '@filter'})
			.get({},
				{function(data){$scope.results = data.data;},
				{function(data){$scope.results="Too bad, no data for you";}
				);
		
	};


// var getResource = app.factory('resourceSvc', ['$resource', 
// 	function($resource, page, perPage, sort, filter){
// 		alert('this is the api-call' + page + perPage + sort + filter);
// 		return $resource('/api/v3/?page=:page&perpage=:perPage&sort=:sort&filter=:filter', {page: '@page', perPage: '@perPage', sort: '@sort', filter: '@filter'});
// 	}]
// );

	// $scope.filter = {created_at: created_at, user_id: user_id, domain: domain};


// platform
// TotalCost
// WorkerReward = hourly rate
// TotalJudgements = per job
// FlaggedWorkers → #
// FlaggedWorkers → %
// Status → running, paused, cancelled (final state), not ordered, finished (final state), toReview, reviewed
// for toReview → show the time left to review
	// Status → the actual progress of the job (in %)
// Contains gold                   



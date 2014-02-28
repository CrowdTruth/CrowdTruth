var app = angular.module("dataRetrieval", [ 'ngResource']);

app.controller("resourceCtrl", function($scope, $resource) {
	var Result = $resource('/api/v3/?currentPage=:currentPage&perpage=:perPage&sort=:sort&filter=:filter', {perPage: '15', sort: '@sort'});
 	
	Result.get(
		{},
		function(data){
 			$scope.results = data.data;
  		},
 		function(data){
 			$scope.results = "I didn't get your results";
 		}
 	);

 	$scope.setSortAsc = function( column ){
 		var sort = "sortBy=" + column + "&order=asc";
 		alert(sort);
 	} 

 	$scope.setSortDesc = function( column ){
 		var sort = "sortBy=" + column + "&sort=desc";
 		alert(sort);
 	} 
 	

 

});

	$scope.filter = {created_at: created_at, user_id: user_id, domain: domain};


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



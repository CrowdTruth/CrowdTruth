var app = angular.module("dataRetrieval", [ 'ngResource']);

	//write resource service class


//inject resourceSvc in this controller
app.controller("resourceCtrl", function($scope, $resource) {
	// On init fetches first pagination object
	var page = 1;
	var perPage = 15;
	var sort = "";
	var filter = "";
	$scope.results = getResource($resource,page,perPage,sort,filter);
	
	// //call getResource after setting sort
 	$scope.setSortAsc = function( column ){
 		var sort = "sortBy=" + column + "&order=asc";
 		$scope.$watch('sort', function(n,o){
 			$scope.results = getResource($resource, "", "", sort, filter );
 		}); 
 	} 

 	$scope.setSortDesc = function( column ){
 		var sort = "sortBy=" + column + "&order=des";
 		$scope.$watch('sort', function(n,o){
 			$scope.results = getResource($resource, "", "", sort, filter );
 		});
 	} 

  // 	$scope.$watch('filter', function(n,o){

 	// })
 	

});

var getResource = function($resource, page, perPage, sort, filter){
		return Result = $resource('/api/v3/?:page:perPage:sort:filter', 
			{page: '@page', perPage: '@perPage', sort: '@sort', filter: '@filter'})
			.get({page: page, perPage: perPage, sort: sort, filter: filter},
				
//PARENTHESIS ERROR HERE
				function(data, $scope){$scope.results = data.data;}
	};

// getResource as a factory; for later implementation (passing parameters into factory proven more difficult than using simple var-like function call)
// var getResource = app.factory('resourceSvc', ['$resource', 
// 	function($resource, page, perPage, sort, filter){
// 		alert('this is the api-call' + page + perPage + sort + filter);
// 		return $resource('/api/v3/?page=:page&perpage=:perPage&sort=:sort&filter=:filter', {page: '@page', perPage: '@perPage', sort: '@sort', filter: '@filter'});
// 	}]
// );

	// $scope.filter = {created_at: created_at, user_id: user_id, domain: domain};


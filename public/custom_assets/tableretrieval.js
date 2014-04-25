var app = angular.module("tableRetrieval", [ 'ngResource']);

	//write resource service class


//inject resourceSvc in this controller
app.controller("resourceCtrl", function($scope, $resource, filterFilter) {
	// On init fetches first pagination object
	var pageNr = 1;
	var page = "page=" + pageNr;
	var itemsPerPage = 15;
	var perPage = "&perPage=" + itemsPerPage;
	var sort = "";
	var filter = "";

	$scope.results = getResource($resource,page,perPage,sort,filter);
	
	// //call getResource after setting sort
 	$scope.setSortAsc = function( column ){
 		sort = "&sortBy=" + column + "&order=asc";
 		$scope.$watch('sort', function(n,o){
 			$scope.results = getResource($resource, page, perPage, sort, filter );
 		}); 
 	} 

 	$scope.setSortDesc = function( column ){
 		sort = "&sortBy=" + column + "&order=des";
 		$scope.$watch('sort', function(n,o){
 			$scope.results = getResource($resource, page, perPage, sort, filter );
 		});
 	} 

 	$scope.setFilter = function(){
		//  set standard filter string
  		var filter = "";
  				
		angular.forEach($scope.filter, function(value, key){
			//append each field to string
			if(value != "")
			{
				var addfilter = "&filter[" + key + "][like]=" + value;
				filter = filter.concat(addfilter);
			}	
		});

  		$scope.$watch('filter', function(n,o){
  		 	$scope.results = getResource($resource, page, perPage, sort, filter);
  		})
  	}  	

 	//The following part concerns selection of jobs for analysis
 	$scope.selection = [];

 	$scope.$watch('results.data|filter:{checked:true}', function(n,o){
 		if(n != undefined)
 			$scope.selection = n.map(function (result){
				return result._id;
 			});
 	}, true);
 	
 	$scope.analyze = function(){
 		if($scope.selection[0] == null ){
 			alert('Select a job first.')
 		}else{
 		alert('Redirect to analyze: ' + $scope.selection);
 		}
 	}

 	
});


var getResource = function($resource, page, perPage, sort, filter){
		return Result = $resource('/api/v3/?:page:perPage:sort:filter', 
			{page: '@page', perPage: '@perPage', sort: '@sort', filter: '@filter'})
			.get({page: page, perPage: perPage, sort: sort, filter: filter},
				function(data, $scope){$scope.results = data.data;}
				);
	}

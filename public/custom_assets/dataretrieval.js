var app = angular.module("dataRetrieval", [ 'ngResource', 'angularMoment', 'ui.bootstrap']);

	//write resource service class


//inject resourceSvc in this controller
app.controller("resourceCtrl", function($scope, $resource, filterFilter) {
	
	$scope.optionsPerPage = [
	    {value: 5},
	    {value: 10 },
	    {value: 20 },
	    {value: 50 },
	    ];

	//First fetch of results with default settings (empty filter&sort, pageNr=1 and perPage=20)
  	$scope.itemsPerPage = $scope.optionsPerPage[0].value;
  	console.log($scope.itemsPerPage); 
	

	page = "page=" + $scope.pageNr;
	perPage = "&perPage=" + $scope.itemsPerPage;
	sort = "";
	filter = "";

	$scope.results = getResource($resource,page,perPage,sort,filter);
	
	$scope.setPerPage = function(){
		perPage = "&perPage=" + $scope.itemsPerPage.value;
		$scope.results = getResource($resource, page, perPage, sort, filter);
	}
	
	//The current set page *lit up
  	
  	//Max nr of pages shown on pagination-slider
	$scope.maxSize = 12;
  
  	$scope.selectPage = function (pageNo) {
		console.log(pageNo);
		page = "page=" + pageNo;
	
		$scope.results = getResource($resource, page, perPage, sort, filter);
		
		console.log("Page set, sir! " );
    		
		$scope.pageNr = pageNo;
    };
	
  	// Call getResource after setting sort
  	$scope.setSortVisible = function(){
  		if($scope.sortVisible == true ){
  			$scope.sortVisible = false;
  		} else {
  			$scope.sortVisible = true;
  		}
  	}

 	$scope.setSortAsc = function( column ){
 		sort = "&sortBy=" + column + "&order=asc";
 		$scope.$watch('sort', function(n,o){
 			$scope.results = getResource($resource, page, perPage, sort, filter );
 		}); 
 		$scope.selectedIndex = column;
	} 

 	$scope.setSortDesc = function( column ){
 		sort = "&sortBy=" + column + "&order=des";
 		$scope.$watch('sort', function(n,o){
 			$scope.results = getResource($resource, page, perPage, sort, filter );
 		});
 		$scope.selectedIndex = column;
	} 

	$scope.setFilterVisible = function(){
  		if($scope.filterVisible == true ){
  			$scope.filterVisible = false;
  		} else {
  			$scope.filterVisible = true;
  		}
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
 		} else
 		{
 			window.location = '/analyze/view?field[_id][]=' + $scope.selection;
 		}
 	}
 	// Buttons for each job
 	$scope.pauseJob = function(){

 	}

 	$scope.startJob = function(){
 		
 	}

 	$scope.cancelJob = function(){
 		
 	}

 	$scope.duplicateJob = function(){
 		
 	}

 	$scope.deleteJob = function(){
 		
 	}


});

var getResource = function($resource, page, perPage, sort, filter){
		return Result = $resource('/api/v3/?:page:perPage:sort:filter', 
			{page: '@page', perPage: '@perPage', sort: '@sort', filter: '@filter'})
			.get({page: page, perPage: perPage, sort: sort, filter: filter},
				function(data, $scope){$scope.results = data.data;}
				);
	}


var app = angular.module("workerRetrieval", [ 'ngResource', 'angularMoment' ]);

	//write resource service class


//inject resourceSvc in this controller
app.controller("workerCtrl", function($scope, $resource, filterFilter) {
	
	$scope.optionsPerPage = [
	    {value: 5},
	    {value: 10 },
	    {value: 20 },
	    {value: 50 },
	    ];

	// Pagination directive


	//First fetch of results with default settings (empty filter&sort, pageNr=1 and perPage=20)
  	$scope.itemsPerPage = $scope.optionsPerPage[0].value;
  		
	$scope.pageNr = 1;
	page = "page=" + $scope.pageNr;
	perPage = "&perPage=" + $scope.itemsPerPage;
	sort = "";
	filter = "";

	$scope.results = getResource($resource,page,perPage,sort,filter);
	
	$scope.setPerPage = function(){
		if( $scope.itemsPerPage.value < $scope.results.total ){
			perPage = "&perPage=" + $scope.itemsPerPage.value;
		}else {
			alert("There are not that many jobs to show!");
		}
	}
		
	$scope.selectPage = function(page){
		switch (page){
			case 'first':
			$scope.pageNr = 1;
			break;
			case 'previous':
			$scope.pageNr = $scope.pageNr -1;
			break;
			case 'next':
			$scope.pageNr = $scope.pageNr +1;
			break;
			case 'last':
			$scope.pageNr = $scope.numPages;
			break;
			default: 
			$scope.pageNr;
		}
	}
	  	
	$scope.$watch('pageNr + itemsPerPage', function(n,o) {
  		if ($scope.pageNr != ""){
  			page = "page=" + $scope.pageNr;

  			$scope.numPages = function(){
				var pages = Math.ceil($scope.results.total/$scope.itemsPerPage);
				if(pages == 1){
					console.log("I evaluate true says if in numPages == 1");
					return pages = 1;
				}
			console.log("numPages calculated");
			return pages;
			}

  			$scope.results = getResource($resource, page, perPage, sort, filter);
  			
  			// if ($scope.results.data == null){
  			// 	$scope.pageNr = 1;
  			// }
  		}
   	});



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
});

var getResource = function($resource, page, perPage, sort, filter){
		return Result = $resource('/api/v3/?:page:perPage:sort:filter', 
			{page: '@page', perPage: '@perPage', sort: '@sort', filter: '@filter'})
			.get({page: page, perPage: perPage, sort: sort, filter: filter},
				function(data, $scope){$scope.results = data;}
				);
	}


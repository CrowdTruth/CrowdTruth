var app = angular.module("crowdWatson", [ 'ngResource', 'angularMoment', 'ui.bootstrap']);

	//write resource service class

app.service('workerService', function() {
  
  var workerList = [];

  this.addWorker = function(workerId) {
  		workerList.push(workerId);
  		console.log(workerList);
  };

  this.removeWorker = function(id) {
  	var index = workerList.indexOf(id);
  	if (index > -1) {
	    workerList.splice(index, 1);
	}
  };

  this.getWorkers = function(){
  	console.log("This is the getWorkers method");
  		return workerList;
  };
  
});

function sendMessage($resource, subject, messageText, workerID){
	return callback = $resource('/api/actions/message').post()
	}

app.controller("workerByIdCtrl", function($scope, $resource, workerService, redirectionService){
	// Get the id from the URL and make API-call to get worker info
	var url = window.location.pathname.split("/");
	var _id = url[3] + "/" + url[4] + "/" + url[5];
	
	worker = getWorker($resource, _id);
	
	worker.$promise.then( function(data){
		$scope.worker = data[0];
		
		if(worker.length == 0){
			window.location = '/workers';
			alert("This worker does not exist");
		}
		
		$scope.annotations = $scope.worker.hasGeneratedAnnotations;
		$scope.jobs = $scope.worker.jobs;
		$scope.units = $scope.worker.units;

	});


	// $scope.currentPage = 1;
 //  	$scope.numPerPage = 1;
 //  	$scope.maxSize = 5;

	
	// $scope.numPages = function () {
 //    	return Math.ceil($scope.annotations.length / $scope.numPerPage);
 //  	};
  	
 //  	$scope.$watch('currentPage + numPerPage', function() {
 //    	var begin = (($scope.currentPage - 1) * $scope.numPerPage);
 //    	var end = begin + $scope.numPerPage;
    	
 //    	$scope.annotations = $scope.annotations.slice(begin, end);
 //  	});

	$scope.openMessage = function(){
		
	}

	$scope.gotoOverview = function(){
		window.location = '/workers';
	}
	
	$scope.flagWorker = function(){
		redirectionService.redirectToFlag();
	}

	$scope.gotoAnnotation = function(id){
		redirectionService.redirectToAnnotation(id);
	}

	$scope.gotoUnit = function(id){
		redirectionService.redirectToUnit(id);
	}
})

	
//inject resourceSvc in this controller
app.controller("workerCtrl", function($scope, $resource, filterFilter, workerService, redirectionService, $modal) {
	
	$scope.optionsPerPage = [
	    {value: 5},
	    {value: 10 },
	    {value: 20 },
	    {value: 50 },
	    ];

	//First fetch of results with default settings (empty filter&sort, pageNr=1 and perPage=20)
  	$scope.itemsPerPage = $scope.optionsPerPage[1].value;
	$scope.pageNr = 1;
	page = "page=" + $scope.pageNr;
	perPage = "&perPage=" + $scope.itemsPerPage;
	sort = "";
	filter = "";

	$scope.results = getResource($resource,page,perPage,sort,filter);
  	console.log($scope.results);	
	
	$scope.setPerPage = function(){
		perPage = "&perPage=" + $scope.itemsPerPage.value;
	}
		
	$scope.selectPage = function(page){
		switch (page){
			case 'first':
			$scope.pageNr = 1;
			break;
			case 'previous':
			if($scope.pageNr > 1) { $scope.pageNr = $scope.pageNr -1; console.log('-1') }
			break;
			case 'next':
			if($scope.pageNr < $scope.numPages) { $scope.pageNr = $scope.pageNr +1;}
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

 	$scope.addWorker = function(result){
 		workerService.addWorker(result);
 		
 	}
	
	$scope.removeWorker = function(result){
		workerService.removeWorker(result);
		console.log("This is removeWorker");
	}
 	
 	$scope.analyze = function(){
 		if($scope.selection[0] == null ){
 			alert('Select a job first.')
 		} else if($scope.selection.length == 1) {
 			window.location = '/workers/worker/' + $scope.selection._id;
 		} else
 		{
 			window.location = '/analyze/view?field[_id][]=' + $scope.selection;
 		}
 	}

 	$scope.gotoWorker = function(id){
 		redirectionService.redirectToWorker(id);
 	}


 	$scope.openMessage = function () {
		selection = workerService.getWorkers();
 		window.location = '/workers/message/?selection=' + selection;
   	}
})

app.controller('messageCtrl', function($scope){
	$scope.showPrevious = function(){
		// window.history.back();
		// In case of browser incompatibility there is also:
		// var oldURL = document.referrer;
		// window.location = oldUrl;
	}

	function getParameterByName(name) {
	    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
	    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
        results = regex.exec(location.search);
    	return results == null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
	}

	$scope.selection = getIds('selection');

	function getIds(name){
		res = getParameterByName(name);
		selection = res.split(',');
		console.log(selection);
	}
	
	console.log($scope.selection);

	$scope.sendMessage = function($resource){
		alert("Message send.");
		}

	$scope.flagWorker = function($resource){
		alert("User flagged!");
	}

	$scope.gotoOverview = function(){
		window.location = '/workers';
	}

	$scope.messagetemplates = [
		{'title': 'AMT: Welcome to Crowd-Watson', 'content':' BLATIEBLATIEBLA', 'subject':'Welcome to Crowd-Watson!'},
		{'title':'AMT: Thanks for completing the job!', 'content':'You did AWESOME!', 'subject':'Thank you for Turking!'}
		];

	$scope.flagtemplates = [
		{'title': 'AMT: Performance under par', 'content':' YOU\'RE FIRED!', 'subject':'Bad performance on our tasks'},
		];	
})


app.service('redirectionService', function(){
	this.redirectToUnit = function (id){
		url = id.split("/");
		window.location = '/entities/unit/' + url[1] + "/" + url[2] + "/" + url[3] + "/" + url[4];
	}

	this.redirectToAnnotation = function(id){
		url = id.split("/");
		window.location = '/entities/annotation/' + url[1] + "/" + url[2] + "/" + url[4];
	}

	this.redirectToWorker = function(id){
		window.location = 'workers/worker/' + id;
	}

	this.redirectToMessage = function (){
		window.location = 'workers/message';
	}

	this.redirectToFlag = function(){
		window.location = 'workers/flag';
	}

});

var getResource = function($resource, page, perPage, sort, filter){
		return Result = $resource('/api/v4/?:page:perPage:sort:filter', 
			{page: '@page', perPage: '@perPage', sort: '@sort', filter: '@filter'})
			.get({page: page, perPage: perPage, sort: sort, filter: filter},
				function(data, $scope){$scope.results = data;}
				);
	}

var getWorker = function($resource, id){
	return worker = $resource('/api/v4/?id=:id', {id: '@id'}, {'get': {method: 'GET', isArray:true }})
		.get({id: id}, 
			function(data, $scope){$scope.worker = data;});
	}

app.controller("resourceCtrl", function($scope, $resource, filterFilter, $http) {
	
	$scope.optionsPerPage = [
	    {value: 5},
	    {value: 10 },
	    {value: 20 },
	    {value: 50 }
	    ];

	// Pagination directive


	//First fetch of results with default settings (empty filter&sort, pageNr=1 and perPage=20)
  	$scope.itemsPerPage = $scope.optionsPerPage[0].value;
  		
	$scope.pageNr = 1;
	page = "page=" + $scope.pageNr;
	perPage = "&perPage=" + $scope.itemsPerPage;
	sort = "";
	filter = "";

	$scope.results = getJobs($resource,page,perPage,sort,filter);
	
	$scope.setPerPage = function(){
		perPage = "&perPage=" + $scope.itemsPerPage.value;
	}
		
	$scope.selectPage = function(page){
		switch (page){
			case 'first':
			$scope.pageNr = 1;
			break;
			case 'previous':
			if($scope.pageNr > 1) { $scope.pageNr = $scope.pageNr -1; }
			break;
			case 'next':
			console.log($scope.pageNr + '-' +  $scope.results.numPages);
			if($scope.pageNr < $scope.results.numPages) { $scope.pageNr = $scope.pageNr +1; }
			break;
			case 'last':
			$scope.pageNr = $scope.results.numPages;
			break;
			default: 
			$scope.pageNr;
		}
	}
	  	
	$scope.$watch('pageNr + itemsPerPage', function(n,o) {
  		if ($scope.pageNr != "" && $scope.pageNr > 0 && $scope.pageNr <= $scope.results.numPages && n!=o){
  			page = "page=" + $scope.pageNr;
			$scope.results = getJobs($resource, page, perPage, sort, filter);
  		}
   	});


 	$scope.setSort = function( column, ascdesc ){
 		sort = "&sortBy=" + column + "&order=" + ascdesc;
 		$scope.$watch('sort', function(n,o){
 			$scope.results = getJobs($resource, page, perPage, sort, filter );
 		}); 
 		$scope.selectedIndex = column;
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
  		 	$scope.results = getJobs($resource, page, perPage, sort, filter);
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
 	
 	$scope.gotoMessage = function(){
 		$scope.$emit('sendMessage', $scope.selection);
 		console.log("Broadcasted message" + $scope.selection);
 	}

 	$scope.analyze = function(){
        if($scope.selection[0] == null ){
            alert('Select a job first.')
        } else
        {
            var redirect_url = '/analyze/view?'
            for (var item in $scope.selection) {
                redirect_url += 'field[_id][]=' + $scope.selection[item] + '&';
            }
            //window.location = redirect_url.substring(0, redirect_url.length - 1)
            window.location = '/analyze/view?jobs=' + $scope.selection;
        }
 	}


 	$scope.perform = function(job, action){
 		var newstatus = '';
 		if(action == 'pause') newstatus = 'paused';
 		else if(action == 'order' || action == 'resume') newstatus = 'running';
 		else if(action == 'cancel') newstatus = 'canceled';
 		else if(action == 'delete') {alert('Deletion is not yet implemented.'); return;}
 		else return;
console.log(action);
 		$http({method: 'GET', url: '/api/actions/'+job._id+'/'+action}).
		    success(function(data, status, headers, config) {
		      	if(data.status == 'ok'){
 					job.status = newstatus;
 				} else {
 					alert(data.message);
 				}	
		     }).
		    error(function(data, status, headers, config) {
		      console.log(status);
		     // alert(status);
		});
 	}
 	
});





var getJobs = function($resource, page, perPage, sort, filter){
		return $resource('/api/v3/?:page:perPage:sort:filter', 
			{page: '@page', perPage: '@perPage', sort: '@sort', filter: '@filter'})
			.get({page: page, perPage: perPage, sort: sort, filter: filter},
				function(data, $scope, perPage){
					data.numPages = Math.ceil(data.total/data.perPage);
					return data;
				}
				);
	}


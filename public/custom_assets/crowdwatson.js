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

app.controller("workerByIdCtrl", function($scope, $resource, workerService, redirectionService, $timeout){
	// Get the id from the URL and make API-call to get worker info
	var url = window.location.pathname.split("/");
	var _id = url[3] + "/" + url[4] + "/" + url[5];
	
	worker = getWorker($resource, _id);
	
	worker.$promise.then( function(data){
		$scope.worker = data[0];

		/* THIS IS TO PREVENT WRONG URLs */
		if(data.length == 0){
			$timeout(
				function(){
					alert("timeout")
					window.location = '/workers';
				},500);
			
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
		window.location = '/workers/message/?selection=' + $scope.worker._id;
		
	}

	$scope.gotoOverview = function(){
		window.location = '/workers';
	}
	
	$scope.flagWorker = function(){
		redirectionService.redirectToFlag($scope.worker._id);
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
		if( $scope.itemsPerPage.value < $scope.results.total ){
			perPage = "&perPage=" + $scope.itemsPerPage.value;
		}else {
			alert("There are not that many items to show!");
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

app.controller('messageCtrl', function($scope, $http, $resource){
	$scope.showPrevious = function(){
		// window.history.back();
		// In case of browser incompatibility there is also:
		// var oldURL = document.referrer;
		// window.location = oldUrl;
	}
	/* THIS METHOD GRABS THE WORKER IDs FROM THE URL */
	function getParameterByName(name) {
	    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
	    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
        results = regex.exec(location.search);
    	return results == null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
	}

	/* THIS METHOD PUTS THE IDs BACK IN AN ARRAY */
	function getIds(name){
		res = getParameterByName(name);
		selection = res.split(',');
		return selection;
	}

	function getFlagId(name){
		return getParameterByName(name);
		
	}

	$scope.selection = getIds('selection');

	$scope.flagselected = getFlagId('selection');

	$scope.sendMessage = function(){
		data = [];
		data.push($scope.message, $scope.selection);
		
		$http.post("/api/actions/message", data).success(function (data,status, headers){
			console.log(data);
		})
		}

	$scope.flagWorker = function(){
		data = [];
		data.push($scope.message, $scope.selection);
		
		$http.post("/api/actions/flag", data).success(function (data,status, headers){
			console.log(data);
		})
		
	}

	$scope.gotoOverview = function(){
		window.location = '/workers';
	}

	$scope.messagetemplates = [
		{'title': 'AMT: Welcome to Crowd-Watson', 'content':' Thank you for joining our community. We hope to see you more often. Find more information on the jobs that you\'re doing for us on: www.demo.com. Have a great day! Regards, the Crowd-Watson team', 'subject':'Welcome to Crowd-Watson!'},
		{'title':'AMT: Thanks for completing the job!', 'content':'You did AWESOME! Keep going like this to work your way towards a bonus! We keep close track of our turkers and will reward our best turkers! Regards, the Crowd-Watson team', 'subject':'Thank you for Turking!'}
		];

	$scope.flagtemplates = [
		{'title': 'AMT: Performance under par', 'content':' Sorry, but according to our stats we can not allow more tasks from you. We advise you to get your performance up to the standard with other contributors. Contact us on demo@example.com if you do not agree with our decision. Regards, the Crowd-Watson team' , 'subject':'Bad performance on our tasks'},
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

	this.redirectToMessage = function (selection){
		window.location = '/workers/message' + selection;
	}

	this.redirectToFlag = function(id){
		window.location = '/workers/flag/?selection=' + id;
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

app.controller("resourceCtrl", function($scope, $resource, filterFilter) {
	
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

	$scope.results = getJobs($resource,page,perPage,sort,filter);
	
	$scope.setPerPage = function(){
		if( $scope.itemsPerPage.value < $scope.results.total ){
			perPage = "&perPage=" + $scope.itemsPerPage.value;
		}else {
			alert("There are not that many items to show!");
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
			return pages;
			}

  			$scope.results = getJobs($resource, page, perPage, sort, filter);
  			
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
 			$scope.results = getJobs($resource, page, perPage, sort, filter );
 		}); 
 		$scope.selectedIndex = column;
	} 

 	$scope.setSortDesc = function( column ){
 		sort = "&sortBy=" + column + "&order=des";
 		$scope.$watch('sort', function(n,o){
 			$scope.results = getJobs($resource, page, perPage, sort, filter );
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
 	
});


var getJobs = function($resource, page, perPage, sort, filter){
		return Result = $resource('/api/v3/?:page:perPage:sort:filter', 
			{page: '@page', perPage: '@perPage', sort: '@sort', filter: '@filter'})
			.get({page: page, perPage: perPage, sort: sort, filter: filter},
				function(data, $scope){$scope.results = data;}
				);
	}

app.controller("imgCtrl", function($scope, $http, filterFilter){
	
	$scope.pictures = [];

 	$scope.next = function (){
		$scope.loading = true;
		$scope.imageGetting = true;
		domain = $scope.domain.toLowerCase();
		type = $scope.type.toLowerCase();
		numImg = $scope.numImg.toString();
		keyphrase = $scope.keyphrase.toLowerCase();
		$scope.empty = false;

		var url = '/api/actions/image/' + domain + '/' + type + '/' + numImg + '/' + keyphrase + '';
		
		$http.get(url)
		.success(function (data, status){
			$scope.status = status;
			// JSON.parse first time for removing ""
			withoutslashesdata = JSON.parse(data);
			// JSON.parse second time to form array
			data = JSON.parse(withoutslashesdata);
			
			$scope.pictures = [];
			angular.forEach(data, function(key, value){
				image = {};
				image.url = key;
				image.title = "Image #" + value;
				$scope.pictures.push(image);
			})
			if($scope.pictures.length == 0){
				$scope.empty = true;
			}
			console.log("Get request success! Pictures array:");
			console.log($scope.pictures);
			$scope.loading = false;
			
		})
		.error(function(data, status){
			$scope.images = data || "Request failed! :(";
			$scope.status = status;
			console.log("Get request failed!");
			console.log(status + data);
			$scope.loading = false;
		});


    }

    $scope.emptyArray = function(){
    	$scope.imageGetting = false;
    }

	$scope.selection = [];

 	$scope.$watch('pictures | filter:{checked:true}', function(n,o){
 		if(n != undefined){
 			$scope.selection = n.map(function (image){
 				return image.url;
 			});
 		}
 	}, true);

 	$scope.executeScript = function(){
 		if($scope.selection[0] == null ){
 			alert('Select an image first.')
 		}else{
 			domain = $scope.domain.toLowerCase();
			type = $scope.type.toLowerCase();
			angular.forEach($scope.selection, function(value, key){
					arr = [];
					arr.push(value);
					arr.push(domain);
					arr.push(type);
					console.log(arr); 
					url = '/api/actions/features';
					console.log("This is the url: " + url);
					alert("Your images are being processed and stored in the database. This may take a while (~20 sec per image). Please continue browsing.")
					$http.post(url, arr)
					.success(function (data, status){
						console.log("Succesful callback" + data);
					})
 					.error( function(data, status){
 						console.log("Script went bad" + status)
					});
 			})
			
 		}
 	}

});

app.controller("annotationByIdCtrl", function($scope, $resource){
	// Get the id from the URL and make API-call to get JSON
	// url i.e.: entities/annotation/text/medical/42
	var url = window.location.pathname.split("/");
	var _id = "entity/" + url[3] + "/" + url[4] + "/annotation/" + url[5];

	annotation = getEntity($resource, _id);

	annotation.$promise.then( function(data){
		$scope.annotation = data[0];
		$scope.unit = $scope.annotation.hasUnit;

		if(annotation.length == 0){
			window.location = '/';
			alert('This is not an entity.');
		}
	});

	$scope.gotoUnit = function(id){
		redirectToUnit(id);
	}

	$scope.gotoWorker = function(id){
		redirectToWorker(id);
	}

	$scope.previousId = function(){
		entityScroller("previous", url, 5);
	}

	$scope.nextId = function(){
		entityScroller("next", url, 5);
	}

})

app.controller("unitByIdCtrl", function($scope, $resource){
	// Get the id from the URL and make API-call to get JSON

	// url i.e.: entities/unit/twrex-structured-sentence/
	var url = window.location.pathname.split("/");
	
	// _id of unit: entity/text/medical/twrex-structured-sentence/142
	var _id = "entity/" + url[3] + "/" + url[4] + "/" + url[5] + "/" + url[6];
	
	unit = getEntity($resource, _id);

	unit.$promise.then( function(data){
		$scope.unit = data[0];
		$scope.annotations = $scope.unit.hasAnnotations;
		if(unit.length == 0){
			window.location = '/';
			alert('This is not an entity.');
		}
	});

	$scope.annotationsVisible = false;

	$scope.setAnnotationsVisible = function(){
		$scope.annotationsVisible == true ? $scope.annotationsVisible = false : $scope.annotationsVisible = true;
		
	}

	$scope.gotoAnnotation = function(id){
		redirectToAnnotation(id);
	}

	$scope.gotoWorker = function(id){
		redirectToWorker(id);
	}

	$scope.previousId = function(){
		entityScroller("previous", url, 6);
	}

	$scope.nextId = function(){
		entityScroller("next", url, 6);
	}
	
})

function entityScroller(direction, url, index){
	if (direction == "next"){
		url[index] = (parseInt(url[index]) + 1).toString();
	} else {
		url[index] = url[index] - 1;
		if(url[index] == -1){
			alert("This is the very first entity of this class.")
		}
	}
	url = url.join('/');
	window.location = url;
}


function getEntity($resource, id){
	return entity = $resource('/api/v3/?id=:id', {id: '@id'}, {'get': {method: 'GET', isArray:true }})
		.get({id: id}, 
			function(data, $scope){$scope.entity = data;});
}

//PUT THESE FUNCTIONS IN A REDIRECTIONSERVICE
function redirectToUnit(id){
	url = id.split("/");
	window.location = '/entities/unit/' + url[1] + "/" + url[2] + "/" + url[3] + "/" + url[4];
}

function redirectToAnnotation(id){
	url = id.split("/");
	window.location = '/entities/annotation/' + url[1] + "/" + url[2] + "/" + url[4];
}

function redirectToWorker(id){
	window.location = '/workers/worker/' + id;
}
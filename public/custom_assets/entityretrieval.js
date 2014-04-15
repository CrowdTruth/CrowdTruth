var app = angular.module("entityRetrieval", [ 'ngResource', 'angularMoment']);

	//write resource service class

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
		if($scope.annotationsVisible == true){
			$scope.annotationsVisible = false;
		} else {
			$scope.annotationsVisible = true;
		}
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
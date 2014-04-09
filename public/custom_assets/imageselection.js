var app = angular.module("imageSelection", ['ngResource']);

app.controller("imgCtrl", function($scope, $http, filterFilter){
	
	$scope.pictures = [];

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
 		alert('Run script on:' + $scope.selection);
 		}
 	}

 	// Watch database for image job to complete, then change $scope.loading boolean to false to show images
 	$scope.loading = true;


	$scope.next = function (){
		console.log("Starting next function.")
		$scope.loading = true;
		domain = $scope.domain.toLowerCase();
		type = $scope.type.toLowerCase();
		numImg = $scope.numImg.toString();
		keyphrase = $scope.keyphrase.toLowerCase();

		var url = '/api/actions/image/' + domain + '/' + type + '/' + numImg + '/' + keyphrase + '';
		
		console.log(url);

		$http({method: 'GET', url: url }).success(function(data, status) {
		      $scope.status = status;
		      $scope.data = data;
		      $scope.loading = false;
		    }).
		    error(function(data, status) {
		      $scope.data = data || "Request failed";
		      $scope.status = status;
		  });


 		
 	}

 	
});




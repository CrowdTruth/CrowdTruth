var app = angular.module("imageSelection", ['ngResource']);

app.controller("imgCtrl", function($scope, filterFilter, $resource ){
	
	$scope.pictures = [
	// {'name': 'picture1', 'url': 'image.png'}, 
	// {'name' : 'picture2', 'url':'image.png'}, 
	// {'name' : 'z5', 'url':'/image.png'}
	];

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


	$scope.next = function ($resource){
		$scope.loading = true;
 		window.location = '/temp/imgselection';

 		var url = "/api/actions/image/" + $scope.domain + "/" + $scope.type + "/" + $scope.numImg + "/" + $scope.keyphrase;
 		console.log(url);
		
 		//MAKE THIS A RESOURCE METHOD
		$http({method: 'GET', url: url}).
			success(function(data, status){
				console.log('Image being loaded');
				$scope.status = status;
				$scope.pictures = data;
				$scope.loading = false;
			})
 		 		
 	}
});
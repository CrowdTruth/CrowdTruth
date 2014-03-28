var app = angular.module("imageSelection", []);

app.controller("imgCtrl", function($scope, filterFilter){
	
	$scope.pictures = [
	{'name': 'picture1', 'url': 'image.png'}, 
	{'name' : 'picture2', 'url':'image.png'}, 
	{'name' : 'z5', 'url':'/image.png'}
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


	$scope.next = function (){
		alert('To the batmobile!!');
 		$scope.loading = true;
 		window.location = '/temp/imgselection';
 	}
});
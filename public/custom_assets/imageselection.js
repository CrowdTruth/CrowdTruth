var app = angular.module("imageSelection", []);

app.controller("imgCtrl", function($scope, $http, filterFilter){
	
	$scope.pictures = [];

 	$scope.next = function (){
		$scope.scriptLoading = true;
		domain = $scope.domain.toLowerCase();
		type = $scope.type.toLowerCase();
		numImg = $scope.numImg.toString();
		keyphrase = $scope.keyphrase.toLowerCase();

		var url = '/api/actions/image/' + domain + '/' + type + '/' + numImg + '/' + keyphrase + '';
		
		console.log(url);

		$http.get(url).success(function (data, status){
			$scope.status = status;
			$scope.pictures = data;
			$scope.scriptLoading = false;
			console.log($scope.pictures);
		})
		.error(function(data, status){
			$scope.images = data || "Request failed! :(";
			$scope.status = status;
		});
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
 		alert('Run script on:' + $scope.selection);
 		}
 	}

});




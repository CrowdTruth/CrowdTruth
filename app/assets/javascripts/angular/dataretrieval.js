	var app = angular.module("dataRetrieval", ["restangular"]);



	app.controller("restangularCtrl", [ "Restangular", "$scope", function (Restangular, $scope){
		Restangular.setBaseUrl('api/v3');
		var resource = Restangular.all('?limit=10&field[documentType]=job');
		
		$scope.resource = resource;
		resource.getList().then(function(job) {
  			$scope.job = job;
		});
	}])

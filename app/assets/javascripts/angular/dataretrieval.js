	var app = angular.module("dataRetrieval", ["restangular"]);

	app.controller("restangularCtrl", [ "Restangular", "$scope", function (Restangular, $scope){
		var resource = Restangular.all('api/v2/?limit=10&field[documentType]=job');
		$scope.jobconfig = "yeaaaaaaah1";
		console.info(resource);
		$scope.resource = resource;
		resource.getList().then(function(job) {
  			$scope.job = job;
		});
	}])

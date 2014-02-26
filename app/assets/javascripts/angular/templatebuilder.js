	var app = angular.module("templatebuilder", ['builder', 'builder.components']);

	app.controller("templatebuilderCtrl", ["$scope", function ($scope){
  		$scope.input = 'ok';
	}])

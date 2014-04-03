app.controller("messageCtrl", function($scope, $resource){
	
	$scope.init = function(){
		$scope.$on('sendMessage', function(event, id){
		$scope.recipient = id;
		console.log("This is messageService: " + id);
		})
	}

	$scope.sendMessage = function(){
		console.log("Send message to " + $scope.recipient);
	}
	
});
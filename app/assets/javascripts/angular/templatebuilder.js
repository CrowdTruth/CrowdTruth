	var app = angular.module("templatebuilder", ['builder', 'builder.components', 'builder.controller', 'ui.bootstrap', 'ui.ace']);

	app.controller("templatebuilderCtrl", ["$scope", "$sce", "$timeout", function ($scope, $sce, $timeout){

		$scope.uncleancss = "input{width:100px}"; //h2{color:red;} 
		//$scope.html = $sce.trustAsHtml("<script>" + $scope.css + "</script>bla");

		// Get 'form' in our own scope.
		$scope.formoldlength = -1;
 		$scope.$on('formchanged', function(event, f) { // We added this to angular-form-builder.js
			if(f.length != $scope.formoldlength){
				$.scoped(); // (re)initialize the html5 scoped fallback
				$scope.formoldlength = f.length;
			}
 			$scope.form = f;
 		});

 		$scope.$on('testchanged', function(event, t) { // We added this to angular-form-builder.js
			//$scope.html = $sce.trustAsHtml(t);
			console.log(t);
 		});

		$scope.changeTab=function(tab){
			$scope.tab=tab;
			// H4X! Prevent popover from showing when clicking label in preview.
			if(tab=='components'){
				$(".popover").hide();
				$("#popoverhack").remove();
			} else {
				$("head").append("<style id='popoverhack'>.popover { display:none !important }</style>");
			}
		};

		  $scope.cssLoaded = function(_editor) {
		  	var promise;
		  	 _editor.getSession().on("change", function(){ 
			
				if(promise) $timeout.cancel(promise);
				promise = $timeout(function() {
					console.log('timeout');
					$scope.css = $sce.trustAsHtml(_editor.getSession().getValue());
					//$scope.html = $sce.trustAsHtml("<style>" + _editor.getSession().getValue() + "</style>"
					//	+ $scope.test);
					//$.scoped();
					//$timeout(function() {$.scoped();}, 1000); // (re)initialize the html5 scoped fallback
				}, 1000);
				  	 	
		  	 });

		  	$scope.$watch('tab', function(){
				if($scope.tab=='css') _editor.focus();
		  	});
		   };

		  $scope.jsLoaded = function(_editor) {
		  	$scope.$watch('tab', function(){
				if($scope.tab=='js') _editor.focus();
		  	});
		   };


	}]);

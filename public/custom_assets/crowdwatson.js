var app = angular.module("crowdWatson", []);

app.controller("imgCtrl", function($scope, $http, filterFilter){
	$scope.imageGetting = false;
	$scope.pictures = [];
	$scope.domain = 'art';

	$scope.previous = function(){
		window.history.back();
	}

 	$scope.next = function (){
		$scope.loading = true;
		$scope.imageGetting = true;
		domain = $scope.domain.toLowerCase();
		type = $scope.type.toLowerCase();
		numImg = $scope.numImg.toString();
		keyphrase = $scope.keyphrase.toLowerCase();
		$scope.empty = false;

		var url = '/api/actions/image/' + domain + '/' + type + '/' + numImg + '/' + keyphrase + '';
		
		$http.get(url)
		.success(function (data, status){
			$scope.status = status;
			// JSON.parse first time for removing ""
			withoutslashesdata = JSON.parse(data);
			// JSON.parse second time to form array
			data = JSON.parse(withoutslashesdata);
			// console.log(data);
			$scope.pictures = [];
			angular.forEach(data, function(key, value){
				image = {};
				//image.url = key;
				image.url = key.URL;
				image.title= key.title;
				image.author = key.author;
				image.description = key.description; 
				image.width = key.width; 
				image.height = key.height; 

				$scope.pictures.push(image);
			})
			if($scope.pictures.length == 0){
				$scope.empty = true;
			}
			$scope.loading = false;
			
		})
		.error(function(data, status){
			$scope.images = data || "Request failed! :(";
			$scope.status = status;
			$scope.loading = false;
		});


    }

    $scope.emptyArray = function(){
    	$scope.imageGetting = false;
    }

	$scope.selection = [];

 	$scope.$watch('pictures | filter:{checked:true}', function(n,o){
 		if(n != undefined){
 			$scope.selection = n.map(function (image){
 				return image;
 			});
 		}
 	}, true);

 	$scope.setModel = function(m){
 		$scope.numImg = 16;
 		$scope.keyphrase = m;
 		$scope.type = 'drawing'
 	}

 	$scope.selectAll = function(){
 		angular.forEach($scope.pictures, function(pic){
 			pic.checked = true;
 		})
 	}

 	$scope.deselectAll = function(){
 		angular.forEach($scope.pictures, function(pic){
 			pic.checked = false;
 		})
 	}

 	$scope.executeScript = function(){
 		if ($scope.selection[0] == null ){
 			alert('Select an image first.')
 		} else {
 			domain = $scope.domain.toLowerCase();
			type = $scope.type.toLowerCase();
			arr = [];
			arr.push($scope.selection);
			arr.push(domain);
			arr.push(type);
				
			url = '/api/actions/features';
				
			alert("Your images are being processed and stored in the database. This may take a while (~20 sec per image). Please continue browsing.");
	
			$http.post(url, arr)
				.success(function (data, status){
					if(data.status == 'ok'){
						console.log(data.message);
					} else {
						console.log(data.error);
					}
				})
				.error( function(data, status){
					console.log("Script went bad " + status)
				});
 		} 		
 	}
});


/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var aplApp = angular.module('panelyApp');

aplApp.controller('detailController', function ($scope, $routeParams,$http,$timeout) {
    
    $scope.places;
    
    $scope.getPlaces = function(){
	    $http.get('./getPlaces.php'
		    )
		    .then(function (response) {
			$scope.places = response.data.places;
			$scope.panels = response.data.panels;
		    });
    }

    // init
    $scope.getPlaces();
});



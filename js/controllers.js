/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var aplApp = angular.module('aplApp');

aplApp.controller('dauftrController',function($scope){
    $scope.cislo=1;
});


aplApp.controller('dbehexportController',function($scope,$http){
    $scope.verpmenge=80;
    $scope.stk=0;
    $scope.countPalVoll = Math.floor($scope.stk/$scope.verpmenge);
    $scope.verpRest = $scope.stk % $scope.verpmenge;
    $scope.restPal = $scope.verpRest>0?1:0;
    $scope.sumPal = $scope.countPalVoll + $scope.restPal;
    
    $scope.disabled = undefined;

    $scope.enable = function() {
	$scope.disabled = false;
    };

    $scope.disable = function() {
	$scope.disabled = true;
    };

    $scope.clear = function() {
	$scope.teil1.selected = undefined;
    };
    
    $scope.teil1 = {};
    $scope.refreshTeile = function(t) {
    var params = {t: t, sensor: false};
    return $http.post(
      './getTeile.php',
      {params: params}
    ).then(function(response) {
	    $scope.teile = response.data.results
	});
    };
    $scope.updatePalCount = function(){
	$scope.countPalVoll = Math.floor($scope.stk/$scope.verpmenge);
	$scope.verpRest = $scope.stk % $scope.verpmenge;
	$scope.restPal = $scope.verpRest>0?1:0;
	$scope.sumPal = $scope.countPalVoll + $scope.restPal;
    }
});

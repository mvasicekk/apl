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
    $scope.verpmenge=0;
    $scope.stk=0;
//    $scope.countPalVoll = Math.floor($scope.stk/$scope.verpmenge);
//    $scope.verpRest = $scope.stk % $scope.verpmenge;
//    $scope.restPal = $scope.verpRest>0?1:0;
//    $scope.sumPal = $scope.countPalVoll + $scope.restPal;


    $scope.behData = null;
    $scope.disabled = undefined;

    $scope.enable = function() {
	$scope.disabled = false;
    };

    $scope.disable = function() {
	$scope.disabled = true;
    };

    $scope.clear = function() {
	$scope.teil1.selected = undefined;
	$scope.export.selected = undefined;
    };
    
    $scope.teil1 = {};
    $scope.export = {};
    
    $scope.refreshExporte = function(e) {
    var params = {e: e};
    return $http.get(
      './getExporte.php',
      {params: params}
    ).then(function(response) {
	    $scope.exporte = response.data.exporteArray;
	});
    };
    
    
    $scope.getBehData = function(){
    var params = {t: $scope.teil1.selected.teil};
    return $http.get(
      './getBehData.php',
      {params: params}
    ).then(function(response) {
	    $scope.behData = response.data.behArray;
	});
    }
    
    
    $scope.exportSelected = function(){
	$scope.teile =undefined;
	$scope.teil1.selected = undefined;
    }
    
    $scope.teilSelected = function(){
	$scope.updatePalCount();
	$scope.getBehData();
    }
    
    $scope.refreshTeile = function(t) {
	if($scope.export.selected===undefined){
	    $scope.teile=null;
	    return;
	}
    var params = {t: t, kunde: $scope.export.selected.kunde};
    return $http.get(
      './getTeile.php',
      {params: params}
    ).then(function(response) {
	    $scope.teile = response.data.teileArray;
	    $scope.searchT = response.data.t;
	});
    }
    
    
    
    $scope.updatePalCount = function(){
	if($scope.teil1.selected!==undefined){
	    $scope.verpmenge=$scope.teil1.selected.verpackungmenge;
	    if($scope.verpmenge!=0){
		$scope.countPalVoll = Math.floor($scope.stk/$scope.verpmenge);
		$scope.verpRest = $scope.stk % $scope.verpmenge;
		$scope.restPal = $scope.verpRest>0?1:0;
		$scope.sumPal = $scope.countPalVoll + $scope.restPal;
	    }
	    else{
		$scope.countPalVoll = 0;
		$scope.verpRest = 0;
		$scope.restPal = 0;
		$scope.sumPal = 0;
	    }
	}
    }
    
    
});

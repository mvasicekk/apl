/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var aplApp = angular.module('dbehexportApp');

aplApp.directive("enterfocus", function () {
        return {
            restrict: "A",
            link: function ($scope, elem, attrs) {
                var focusables = $(":focusable");
                elem.bind("keydown", function (e) {
                    var code = e.keyCode || e.which;
                    if (code === 13) {
                        var current = focusables.index(this);
                        var next = focusables.eq(current + 1).length ? focusables.eq(current + 1) : focusables.eq(0);
			console.log('current='+current+' next=');
			console.log(next);
                        next.focus();
			next.select();
                        e.preventDefault();
                    }
                });
            }
        }
});

aplApp.controller('dbehexportController',function($scope,$http){
    $scope.verpmenge=0;
    $scope.stk=0;
    $scope.mitPal=false;
    $scope.a5=0;
    $scope.pal2x = 0;
    $scope.nochNichtGedruckt=0;
    
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
    
    
    
    $scope.getBehArray = function(){
		
    params = {t: $scope.teil1.selected.teil,ex:$scope.export.selected.ex};
    return $http.post(
      './getBehArray.php',
      {params: params}
    ).then(function(response) {
	    $scope.behArray = response.data.behArray;
	});
    }
    
    $scope.getBehData = function(){
		
    params = {t: $scope.teil1.selected.teil,ex:$scope.export.selected.ex};
    return $http.get(
      './getBehData.php',
      {params: params}
    ).then(function(response) {
	    $scope.behArray = response.data.behArray;
	    $scope.zeilenArray = response.data.zeilenArray;
	    $scope.zeilenDArray = response.data.zeilenDArray;
	    $scope.zeilenDAArray = response.data.zeilenDAArray;
	    $scope.abgnrKeysArray = response.data.abgnrKeysArray;
	    $scope.aartKeysArray = response.data.aartKeysArray;
	    $scope.nochNichtGedruckt = response.data.nochNichtGedruckt;
	});
    }

    /**
     * 
     * @returns {undefined}
     */
    $scope.zettelDruckClick = function(){
	console.log('zettelDruckClick');
	$scope.nochNichtGedruckt = 0;
	$scope.getBehArray();
    }
    
    /**
     * 
     * @returns {unresolved}
     */
    $scope.saveZettel = function(){
	params = {
	    t: $scope.teil1.selected.teil,
	    ex:$scope.export.selected.ex,
	    palCount: $scope.sumPal,
	    verpMenge:$scope.verpmenge,
	    verpRest:$scope.verpRest
	};
	return $http.post(
		'./saveZettel.php',
		{params: params}
	).then(function (response) {
	    console.log(response.data);
	    $scope.behArray = response.data.behArray;
	    $scope.nochNichtGedruckt = response.data.nochNichtGedruckt;
	});
    }
    /**
     * 
     * @param {type} pal
     * @returns {unresolved}
     */
    $scope.nichtInExportChanged = function (pal) {
	console.log(pal);
	params = {
	    field: 'nicht_in_export',
	    value: pal.nicht_in_export,
	    id: pal.id
	};
	return $http.post(
		'./updateBehData.php',
		{params: params}
	).then(function (response) {
	    console.log(response.data);
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

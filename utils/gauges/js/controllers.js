/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var aplApp = angular.module('gaugesApp');


/**
 * 
 */
aplApp.controller('vzkdController', function ($scope, $http,$timeout,$window,$location,$interval) {

    // promenne ----------------------------------------------------------------
    $scope.dt = new Date();
    var vzkdAktual = {};
    
    $scope.vzkd = {
	vzkdAktual:0,
	vzkdPlan:0,
	vzkdSchatzung:0,
	vzkdStat:{}
    };
    
    $scope.statArray = [
	{
	    statnr:'S0011',
	    plan:0,
	    schatzung:0,
	    aktual:0
	},
	{
	    statnr:'S0041',
	    plan:0,
	    schatzung:0,
	    aktual:0
	},
	{
	    statnr:'S0051',
	    plan:0,
	    schatzung:0,
	    aktual:0
	},
	{
	    statnr:'S0061',
	    plan:0,
	    schatzung:0,
	    aktual:0
	},
	{
	    statnr:'S0081',
	    plan:0,
	    schatzung:0,
	    aktual:0
	}
    ];
    
    // funkce ==================================================================
    
    $scope.getStatArray = function(){
	return $http.post(
		'../getStatNrPlan.php',
		{kdvon:111,kdbis:195} // nevyuziva se, vybiram podle produktgruppe = 1
		).then(function (response) {
		    $scope.statNrArray = response.data.statArray;
		    var vzkdPlan = 0;
		    if($scope.statNrArray!==null){
			$scope.statNrArray.forEach(function(v){
			    if(v.statnr!='S0091'){
				$scope.statArray.forEach(function(v1){
				if(v1.statnr==v.statnr){
				    v1.plan = parseFloat(v.vzkd);
				}
			    });
			    vzkdPlan += parseFloat(v.vzkd);
			    }
			    
			});
			$scope.vzkd.vzkdPlan = vzkdPlan;
		    }
		});
    }
    
    $scope.getVzkdArray = function(){
	return $http.post('../getVzKdAktual.php',{kdvon:111,kdbis:195})
		.then(function (response) {
		    $scope.vzkdArray = response.data.vzkdArray;
		    var vzkdSum = 0;
		    if($scope.vzkdArray!==null){
			$scope.vzkdArray.forEach(function(v){
			    if(v.statnr!='X'){
				vzkdAktual[v.statnr] = parseFloat(v.vzkd);
				vzkdSum += parseFloat(v.vzkd);
			    }
			});
			$scope.vzkd.vzkdAktual = vzkdSum;
			$scope.vzkd.vzkdStat = vzkdAktual;
		    }
		});
    }
    
    
    
    
    $scope.getStatArray();
    $scope.getVzkdArray();
    
    $interval($scope.getVzkdArray,60000);
    $interval($scope.getStatArray,60000);
    
    $interval(function(){
	$scope.dt = new Date();
    },1000);
    
});

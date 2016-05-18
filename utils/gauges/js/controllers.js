/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var aplApp = angular.module('gaugesApp');

aplApp.directive('aplHelp', function () {
	    
	    return {
		scope:{
		    el:'=',
		    showhelp:'=',
		    showadmininfo:'='
		},
		restrict: 'E',
		templateUrl: './templates/aplhelp.html',
		link: function (scope, element, attrs) {
		}
	    }
	});

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
			//console.log('current='+current+' next=');
			//console.log(next);
                        next.focus();
			next.select();
                        e.preventDefault();
                    }
                });
            }
        }
});

aplApp.controller('gaugesController', function ($scope, $http,$timeout,$window,$location) {

    var statArrayOptions = [
	    {
		id:"lS0011",
		unitString:'VzKd %',
		titleString:'S0011',
		threshold: 50
	    },
	    {
		id:"lS0041",
		unitString:'VzKd %',
		titleString:'S0041',
		threshold: 50
	    },
	    {
		id:"lS0051",
		unitString:'VzKd %',
		titleString:'S0051',
		threshold: 50
	    },
	    {
		id:"lS0061",
		unitString:'VzKd %',
		titleString:'S0061',
		threshold: 50
	    }
	    
    ];
    var gaugesArray = [];
    
    var linearS00XX = document.getElementById('linearS00XX');
    
    statArrayOptions.forEach(function(v){
	var linear1 = new steelseries.Linear(v.id, {
			    width: linearS00XX.offsetWidth,
                            height: 140,
                            titleString: v.titleString,
                            unitString: v.unitString,
                            threshold: v.threshold,
                            lcdVisible: true
                            });
	linear1.setFrameDesign(steelseries.FrameDesign.METAL);			    
	linear1.setBackgroundColor(steelseries.BackgroundColor.WHITE);
	linear1.setValueColor(steelseries.ColorDef.RED);
	linear1.setLcdColor(steelseries.LcdColor.BEIGE);
	linear1.setLedVisible(false);
	gaugesArray.push(linear1);
    });
    
    //LCD hodiny + datum
    var lcdHodiny = new steelseries.DisplaySingle('lcdHodiny', {
                            width: document.getElementById('lcdHodinyContainer').offsetWidth,
                            height: 100,
			    valuesNumeric: false
                            });
    lcdHodiny.setLcdColor(steelseries.LcdColor.BEIGE);			    
    setInterval(function(){ 
	var today = Date();
	lcdHodiny.setValue(Date().toLocaleString());
    }, 500);			    
    
});

/**
 * 
 */
aplApp.controller('vzkdController', function ($scope, $http,$timeout,$window,$location,$interval) {

    $scope.dt = new Date();
    
    $scope.getStatArray = function(){
	return $http.post(
		'../getStatNrPlan.php',
		{kdvon:111,kdbis:195}
		).then(function (response) {
		    $scope.statNrArray = response.data.statArray;
		    console.log('getStatArray');
		    console.log($scope.statNrArray);
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
		    console.log('getVzkdArray');
		    console.log($scope.vzkdArray);
		    var vzkdSum = 0;
		    if($scope.vzkdArray!==null){
			$scope.vzkdArray.forEach(function(v){
			    if(v.statnr!='X'){
				$scope.vzkdAktual[v.statnr] = parseFloat(v.vzkd);
				vzkdSum += parseFloat(v.vzkd);
			    }
			});
			$scope.vzkd.vzkdAktual = vzkdSum;
		    }
		});
    }
    
    
    $scope.vzkdAktual = {};
    
    $scope.vzkd = {
	vzkdAktual:45000,
	vzkdPlan:55000,
	vzkdSchatzung:44000
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
    
    $scope.getStatArray();
    $scope.getVzkdArray();
    
    $interval($scope.getVzkdArray,15000);
    $interval(function(){
	$scope.dt = new Date();
    },1000);
    
});

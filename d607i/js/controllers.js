/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var aplApp = angular.module('d607iApp');

aplApp.controller('d607iController', function ($scope, $http,$timeout) {
    $scope.terminMatch = '';
    $scope.importMatch = '';
    $scope.teilMatch = '';
    
    var d607it;
    
    $scope.$on('$viewContentLoaded', function(event) {
	    d607it = $('#d607it');
    });
    
    $scope.showPrintDialog = function(){
	d607it.floatThead('destroy');
	window.onafterprint = function(){
	    console.log("Printing completed...");
	    d607it.floatThead();
	}
	window.print();
    };
    $scope.getZeilen = function(e){
	console.log('getZeilen event.keyCode='+e.which);
	if (($scope.terminMatch.length >= 3)&&(e.which==13)) {
	    $http.get('./getD607i.php?termin=' + $scope.terminMatch
		    +'&import='+$scope.importMatch
		    +'&teil='+$scope.teilMatch
		    )
		    .success(function (data) {
			$scope.zeilen = data.zeilen;
			$scope.zeilenD = data.zeilenD;
			$scope.zeilenDA = data.zeilenDA;
			$scope.dZeilen = [].concat($scope.zeilen);
			$scope.abgnrKeysArray = data.abgnrKeysArray;
			$scope.aartKeysArray = data.aartKeysArray;
			$scope.terminKeysArray = data.terminKeysArray;
			$timeout(function(){
			    d607it.floatThead('destroy');
			    d607it.floatThead();
			    d607it.floatThead('reflow');
			},100);
		    });
	}
    };
});



/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var aplApp = angular.module('d607iApp');

aplApp.controller('d607iController', function ($scope, $http,$timeout) {
//    $scope.terminMatch = '';
    $scope.terminMatchVon = '';
    $scope.terminMatchBis = '';
    $scope.importMatch = '';
    $scope.teilMatch = '';
    $scope.mitPaletten = false;
    $scope.mitReklamation = false;
    $scope.minutenOption = 'vzaby';
    
    var d607it;
    
    $scope.$on('$viewContentLoaded', function(event) {
	    d607it = $('#d607it');
	    $('#spinner').hide();
    });
    
    $scope.showPrintDialog = function(){
	d607it.floatThead('destroy');
	window.onafterprint = function(){
	    console.log("Printing completed...");
	    d607it.floatThead();
	}
	window.print();
    };
    
    /**
     * 
     * @param {type} e
     * @returns {undefined}
     */
    $scope.getSumMinuten = function(palInfo,minutenOption){
	var index = 'sum_'+$scope.minutenOption;
	return palInfo[index];
    }
    
    /**
     * 
     * @param {type} e
     * @returns {undefined}
     */
    $scope.getZeilen = function(e){
	console.log('getZeilen event.keyCode='+e.which);
	if (($scope.terminMatchVon.length >= 3)&&($scope.terminMatchBis.length >= 3)&&(e.which==13)) {
	    $('#spinner').show();
	    $http.get('./getD607i.php?terminvon=' + $scope.terminMatchVon
		    +'&terminbis='+$scope.terminMatchBis
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
			$scope.terminArray = data.terminArray;
			$scope.teileArray = data.teileArray;
			$timeout(function(){
			    d607it.floatThead('destroy');
			    d607it.floatThead();
			    d607it.floatThead('reflow');
			    $('#spinner').hide();
			},100);
		    });
	}
    };
});



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
    $scope.kundeMatch = '';
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
    
    $scope.dauftrRowChange = function(r,field){
	console.log(r);
	$http.post('./saveDauftr.php', {r: r,field:field}).then(function (response) {
		
	    });
    }
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
    $scope.getCompleteProzent = function(palInfoSoll,palInfoIst,minutenOption){
	var retValue = 0;
	var soll = $scope.getSumMinuten(palInfoSoll,minutenOption);
	var ist = $scope.getSumMinuten(palInfoIst,minutenOption);
	if(soll!=0){
	    retValue = ist/soll*100;
	    if(retValue>100){
		// hodnoty na 100 procenr ometim na 100
		retValue=100;
	    }
	}
	return retValue;
    }
    /**
     * 
     * @param {type} e
     * @returns {undefined}
     */
    $scope.getZeilen = function(e){
	console.log('getZeilen event.keyCode='+e.which);
	if (((($scope.terminMatchVon.length >= 3)&&($scope.terminMatchBis.length >= 3))||($scope.kundeMatch.length==3))&&(e.which==13)) {
	    $('#spinner').show();
	    $http.get('./getD607i.php?terminvon=' + $scope.terminMatchVon
		    +'&terminbis='+$scope.terminMatchBis
		    +'&import='+$scope.importMatch
		    +'&teil='+$scope.teilMatch
		    +'&kunde='+$scope.kundeMatch
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
			$scope.sumReport = data.sumReport;
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



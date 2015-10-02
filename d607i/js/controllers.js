/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var aplApp = angular.module('d607iApp');

aplApp.controller('d607iController', function ($scope, $http,$timeout) {
//    $scope.terminMatch = '';
    $scope.terminMatchVon = '';
    $scope.terminVon = null;
    $scope.terminBis = null;
    $scope.terminMatchBis = '';
    $scope.importMatch = '';
    $scope.teilMatch = '';
    $scope.kundeMatch = '';
    $scope.mitPaletten = false;
    $scope.mitReklamation = false;
    $scope.mitMinuten = false;
    $scope.gt_editable = false;
    $scope.bemerkung_editable = false;
    
    $scope.minutenOption = 'vzaby';
    $scope.securityInfo = undefined;
    
    var d607it;
    
    $scope.dateOptions = {
		dateFormat: 'dd.mm.yy',
		firstDay: 1
    };
	    
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
    
    $scope.updateFloatHead = function(){
	$timeout(function(){
			    d607it.floatThead('destroy');
			    d607it.floatThead();
			    d607it.floatThead('reflow');
			    $('#spinner').hide();
			},100);
    }
    
    /**
     * 
     * @returns {undefined}
     */
    $scope.makeEditable_gt = function(v){
	console.log('makeEditable_gt'+v);
	$scope.gt_editable=v;
    }
    
    /**
     * 
     * @returns {undefined}
     */
    $scope.makeEditable_bemerkung = function(v){
	console.log('makeEditable_bemerkung'+v);
	$scope.bemerkung_editable=v;
    }
    /**
     * 
     * @param {type} e
     * @returns {undefined}
     */
    $scope.getZeilen = function(e){
	console.log('getZeilen event.keyCode='+e.which);
	console.log($scope.terminVon);
	if (
		(
		(($scope.terminMatchVon.length >= 3)&&($scope.terminMatchBis.length >= 3))
		||($scope.kundeMatch.length==3)
//		||(($scope.terminVon!==null)&&($scope.terminBis!==null)&&($scope.kundeMatch.length==3))
		||(($scope.terminVon!==null)&&($scope.terminBis!==null))
		)
		&&
		(e.which==13)
	    ) {
	    $('#spinner').show();
	    if(($scope.terminVon!==null)&&($scope.terminBis!==null)){
		var v = $scope.terminVon.getTime();
		var b = $scope.terminBis.getTime();
	    }
	    else{
		var v = null;
		var b = null;
	    }
	    $http.get('./getD607i.php?terminvon=' + $scope.terminMatchVon
		    +'&terminbis='+$scope.terminMatchBis
		    +'&import='+$scope.importMatch
		    +'&teil='+$scope.teilMatch
		    +'&kunde='+$scope.kundeMatch
		    +'&von='+v
		    +'&bis='+b
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
			
			$scope.updateFloatHead();
		    });
	}
    };
    
    $scope.initSecurity = function(){
	var p={
	    form_id:'d607i'
	};
	return $http.post('./getSecurityInfo.php',p).then(
		    function(response){
			$scope.securityInfo = response.data.securityInfo;
		    }
		);
    }
    
    
    // inicializace controlleru
    $scope.initSecurity();
});



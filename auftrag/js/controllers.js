/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var aplApp = angular.module('auftragApp');

aplApp.controller('detailController', function ($scope, $routeParams,$http,$timeout) {
    
    var auftragTable;
    
    $scope.auftragsnr = $routeParams.auftragsnr;
    $scope.auftragInfo = undefined;
    $scope.showAlleTat = false;
    $scope.auftrag = {};
    $scope.auftrag.selected = {};
    
    $scope.enable = function () {
	$scope.disabled = false;
    };

    $scope.disable = function () {
	$scope.disabled = true;
    };
    
    $scope.dateOptions = {
		dateFormat: 'dd.mm.yy',
		firstDay: 1
	    };
    
    $scope.$on('$viewContentLoaded', function(event) {
	auftragTable = $('#dauftr');
    });
    
    
    $scope.auftragOnSelect = function($item, $model){
	    console.log($item);
	    $scope.auftragsnr = $item.auftragsnr;
	    $routeParams.auftragsnr=$scope.auftragsnr;
	    $scope.getAuftragInfo();
    }
	
    $scope.refreshAuftragsnr = function (e) {
	    var params = {e: e};
	    return $http.get(
		    './getAuftragsnr.php',
		    {params: params}
	    ).then(function (response) {
		$scope.auftragsnrArray = response.data.auftragsnrArray;
	    });
    };
    /**
     * 
     * @param {type} e
     * @returns {undefined}
     */
    $scope.getAuftragInfo = function(e){
//	console.log('getZeilen event.keyCode='+e.which);
	    $('#spinner').show();
	    $http.get('./getAuftragInfo.php?auftragsnr=' + $scope.auftragsnr
		    )
		    .then(function (response) {
			$scope.auftragInfo = response.data.auftragInfo;
			$scope.dauftrPos = response.data.dauftrPos;
			$scope.auftrag.selected.auftragsnr = response.data.auftragInfo.auftragsnr;
			$scope.displayDauftrPos = [].concat($scope.dauftrPos);
			$timeout(function(){
			    auftragTable.floatThead('destroy');
			    auftragTable.floatThead();
			    auftragTable.floatThead('reflow');
			    $('#spinner').hide();
			},100);
		    });
    };
    
    // init
    $scope.getAuftragInfo();
    
    $scope.showPrintDialog = function(){
	auftragTable.floatThead('destroy');
	window.onafterprint = function(){
	    console.log("Printing completed...");
	    auftragTable.floatThead();
	}
	window.print();
    };
    
    /**
     * 
     * @param {type} e
     * @returns {undefined}
     */
//    $scope.getSumMinuten = function(palInfo,minutenOption){
//	var index = 'sum_'+$scope.minutenOption;
//	return palInfo[index];
//    }
    
    /**
     * 
     * @param {type} e
     * @returns {undefined}
     */
    $scope.getZeilen = function(e){
	console.log('getZeilen event.keyCode='+e.which);
	if (($scope.terminMatchVon.length >= 3)&&($scope.terminMatchBis.length >= 3)&&(e.which==13)) {
	    //$('#spinner').show();
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



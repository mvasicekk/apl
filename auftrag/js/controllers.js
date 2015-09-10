/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var aplApp = angular.module('auftragApp');

aplApp.controller('detailController', function ($scope, $routeParams,$http,$timeout) {
    
    var auftragTable;
    
    $scope.formDataChanged = false;
    $scope.auftragsnr = $routeParams.auftragsnr;
    $scope.auftragInfo = undefined;
    $scope.showAlleTat = false;
    $scope.auftrag = {};
    $scope.auftrag.selected = {};
    
    $scope.zielort = {};
    $scope.zielort.selected = {};
    
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
    
    var convertMysql2Date = function(dt){
	var t = dt.split(/[- :]/);
	// Apply each element to the Date function
	var d = new Date(t[0], t[1]-1, t[2], t[3], t[4], t[5]);
	return d;
    }
    
    $scope.testFormChanges = function(){
	console.log('testing form changes');
	var d = JSON.stringify($scope.auftragInfo);
	if($scope.auftragInfoOriginal!==d){
	    $scope.formDataChanged = true;
	    //TODO zavolat ukladaci funkci, po vyrizeni ukladani nastavit 
	    //formDataChanged na false a auftragInfoOriginal nastavit na
	    //aktualni auftraginfo
	    $scope.auftragInfoOriginal = d;
	}
	else{
	    $scope.formDataChanged = false;
	}
    }
    
    $scope.auftragOnSelect = function($item, $model){
	    console.log($item);
	    $scope.auftragsnr = $item.auftragsnr;
	    $routeParams.auftragsnr=$scope.auftragsnr;
	    $scope.getAuftragInfo();
    }
    
    $scope.zielortOnSelect = function($item, $model){
	    console.log($item);
	    $scope.auftragInfo.zielort_id = $item.id;
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
    
    var parseTime = function(time){
	console.log(time);
	if(time.length>3){
	    //return String.sub
	}
    }
    
    $scope.parseImSollTime = function(){
	$scope.auftragInfo.imsolluhr1 = parseTime($scope.auftragInfo.imsolluhr1);
    }
    
    $scope.refreshZielort = function (e) {
	if ($scope.auftragInfo !== undefined) {
	    var params = {e: e, k: $scope.auftragInfo.kunde};
	    return $http.get(
		    './getZielorte.php',
		    {params: params}
	    ).then(function (response) {
		$scope.zielortArray = response.data.zielortArray;
		// pokud nastavuju obsah selectu podle zielort_id, je e undefined
		if(e===undefined){
		    console.log('e je undefined');
		    //projit zielortArray a do selected priradi objekt se shodou v id
			for(i=0;i<$scope.zielortArray.length;i++){
			    if($scope.zielortArray[i].id==$scope.auftragInfo.zielort_id){
				$scope.zielort.selected = $scope.zielortArray[i];
				break;
			    }
			}
		}
	    });
	}
	else {
	    return;
	}
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
//			$scope.auftragInfo.exsolluhr1 = convertMysql2Date($scope.auftragInfo.ex_soll_datetime);
			$scope.auftragInfo.exsolldat1 = convertMysql2Date($scope.auftragInfo.ex_soll_datetime);
//			$scope.auftragInfo.imsolluhr1 = convertMysql2Date($scope.auftragInfo.im_soll_datetime);
			$scope.auftragInfo.imsolldat1 = convertMysql2Date($scope.auftragInfo.im_soll_datetime);
//			$scope.auftragInfo.aufuhr1 = convertMysql2Date($scope.auftragInfo.aufdat_raw);
			$scope.auftragInfo.aufdat1 = convertMysql2Date($scope.auftragInfo.aufdat_raw);
//			$scope.auftragInfo.auslieferuhr1 = convertMysql2Date($scope.auftragInfo.ausliefer_raw);
			$scope.auftragInfo.auslieferdat1 = convertMysql2Date($scope.auftragInfo.ausliefer_raw);
			
			// ulozit originalni stav dat, abych mohl porovnat zda jsou ve formulari zmeny
			$scope.auftragInfoOriginal = JSON.stringify($scope.auftragInfo);
			$scope.dauftrPos = response.data.dauftrPos;
			$scope.auftrag.selected.auftragsnr = response.data.auftragInfo.auftragsnr;
			$scope.displayDauftrPos = [].concat($scope.dauftrPos);
			$scope.refreshZielort();
			//nastavit zielort do select boxu podle zielort_id v auftragInfo
			
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



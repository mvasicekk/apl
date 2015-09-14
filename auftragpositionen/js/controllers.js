/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var aplApp = angular.module('auftragposApp');

aplApp.controller('detailController', function ($filter,$scope, $routeParams,$http,$timeout) {
    
    var auftragTable;
    
    $scope.formDataChanged = false;
    $scope.auftragsnr = $routeParams.auftragsnr;
    $scope.teilInfo = undefined;
    $scope.showAlleTat = false;
    $scope.dpos = undefined;
    $scope.minpreis;
    $scope.runden = 4;
    
    $scope.positionInfo = {
	palstk:0,
	stkpropal:1,
	firstpal:10,
	increment:10,
	fremdauftr:'',
	fremdpos:'',
	gt:'',
	fremdausauftrag:'',
	explanmit:''
    };
    
    $scope.teil = {};
    $scope.teil.selected = {};
    
    
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
//	auftragTable = $('#dauftr');
    });
    
    var convertMysql2Date = function(dt){
	if(dt===null){
	    return null;
	}
	var t = dt.split(/[- :]/);
	// Apply each element to the Date function
	var d = new Date(t[0], t[1]-1, t[2], t[3], t[4], t[5]);
	return d;
    }
    
    $scope.teilOnSelect = function($item, $model){
	    console.log($item);
	    $scope.getTeilInfo();
    }
    
    $scope.refreshTeil = function (e) {
	    var params = {e: e,auftrag:$scope.auftragsnr};
	    return $http.get(
		    './getTeil.php',
		    {params: params}
	    ).then(function (response) {
		$scope.teilArray = response.data.teilArray;
	    });
    };
    
    $scope.dposRowClicked = function(r){
	console.log(r);
	if(r.kz_druck==0){
	    r.kz_druck=1;
	}
	else{
	    r.kz_druck=0;
	}
    }

    /**
     * 
     * @param {type} r
     * @returns {undefined}
     */
    $scope.vzChanged = function(r,c){
	console.log('vzChanged: ' + c);
	var vzValue = numeral().unformat(r[c].replace(',','.'));
	if(c=='vzkd'){
	    r.preis = vzValue*$scope.minpreis;
	}
	r[c] = numeral(vzValue).format('0.0000');
	console.log(vzValue);
    }
    /**
     * 
     * @param {type} e
     * @returns {undefined}
     */
    $scope.getTeilInfo = function(e){
	    var params = {teil: $scope.teil.selected.teil,auftrag:$scope.auftragsnr};
	    $http.get('./getTeilInfo.php',{params:params}
		    )
		    .then(function (response) {
			$scope.minpreis = response.data.minpreis;
			$scope.runden = response.data.runden;
		
			$scope.teilInfo = response.data.teilInfo;
			$scope.dpos = $scope.teilInfo.dpos;
			$scope.positionInfo.fremdauftr = response.data.fremdauftr;
			$scope.positionInfo.fremdpos = response.data.fremdpos;
			$scope.positionInfo.fremdausauftrag = response.data.fremdauftrausauftrag;
			$scope.positionInfo.explanmit = response.data.explanmit;
			$scope.displaydpos = [].concat($scope.dpos);
		    });
    }
    
    // init
    
    $scope.showPrintDialog = function(){
	window.onafterprint = function(){
	    console.log("Printing completed...");
	}
	window.print();
    };
});



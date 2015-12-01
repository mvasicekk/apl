/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var aplApp = angular.module('auftragposApp');

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
	
aplApp.directive('palcanexistValidator', function($http, $q) {
    return {
        require: 'ngModel',
        link: function(scope, element, attrs, ngModel) {
            ngModel.$asyncValidators.palexists = function(modelValue, viewValue) {
                return $http.post('./palExists.php', {firstPal: viewValue,attrs:attrs}).then(
                    function(response) {
                        if (response.data.palCanExist) {
                            return $q.reject(response.data.errorMessage);
                        }
                        return true;
                    }
                );
            };
        }
    };
});

aplApp.controller('detailController', function (setfocus,$filter,$scope, $routeParams,$http,$timeout,$window) {
    
    var auftragTable;
    
    $scope.formDataChanged = false;
    $scope.auftragsnr = $routeParams.auftragsnr;
    $scope.teilInfo = undefined;
    $scope.showAlleTat = false;
    $scope.dpos = undefined;
    $scope.minpreis;
    $scope.runden = 4;
    $scope.allowErfassen;
    
    $scope.positionInfo = {
	palstk:0,
	stkpropal:1,
	firstpal:10,
	increment:10,
	fremdauftr:'',
	fremdpos:'',
	gt:'',
	fremdausauftrag:'',
	explanmit:'',
	bemerkung:''
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
	    $('#gew').focus();
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
			$scope.allowErfassen = true;
			$scope.teilInfo = response.data.teilInfo;
			$scope.dpos = $scope.teilInfo.dpos;
			$scope.positionInfo.fremdauftr = response.data.fremdauftr;
			$scope.positionInfo.fremdpos = response.data.fremdpos;
			$scope.positionInfo.fremdausauftrag = response.data.fremdauftrausauftrag;
			$scope.positionInfo.explanmit = response.data.explanmit;
			$scope.displaydpos = [].concat($scope.dpos);
			if($scope.teilInfo.teil.status=='ALT'||$scope.teilInfo.teil.status=='GSP'||$scope.teilInfo.teil.status=='ZT'){
			    $scope.allowErfassen = false;
			}
			$timeout(function() {
			    var element = $window.document.getElementById('gew');
			    if(element){
				element.focus();
				element.select();
			    }
			});
		    });
    }
    
    // init
    
    $scope.showPrintDialog = function(){
	window.onafterprint = function(){
	    console.log("Printing completed...");
	}
	window.print();
    };
    
    /**
     * 
     * @param {type} info
     * @returns {undefined}
     */
    $scope.posErstellen = function(info){
	    $scope.allowErfassen = false;
	    var params = {teil: $scope.teil.selected.teil,auftrag:$scope.auftragsnr,positionInfo:$scope.positionInfo,dpos:$scope.dpos,teilInfo:$scope.teilInfo};
	    $http.post('./posErstellen.php',{params:params}
		    )
		    .then(function (response) {
			//presunout se na spravu zakazky
			$window.location.href = '../auftrag/auftrag.php#/det/'+response.data.auftrag;
//			$window.location.href = '../dauftr/dauftr.php?auftragsnr='+response.data.auftrag;
		    });
    };
    
    $scope.endeClick = function(){
	$window.location.href = '../auftrag/auftrag.php#/det/'+$scope.auftragsnr;
//	$window.location.href = '../dauftr/dauftr.php?auftragsnr='+response.data.auftrag;
    };
    
    $scope.updateKzGut = function(r){
	//povolim je jedno G u operaci
	console.log('updateKzGut');
	if(r.kzgut=='G'||r.kzgut=='g'){
	    console.log('zadal G');
	    r.kzgut = 'G';
	    var abgnr = r.abgnr;
	    for(i=0;i<$scope.dpos.length;i++){
		if($scope.dpos[i].abgnr!=abgnr){
		    $scope.dpos[i].kzgut='';
		}
	    }
	}
    }
    
    $scope.explanmitChange = function(){
	// pokud je prazdny tak nedelam nic
	if($scope.positionInfo.explanmit==''){
	    return;
	}
	//test jestli je na zacatku P
	
	$scope.positionInfo.explanmit = $scope.positionInfo.explanmit.toUpperCase();
	console.log($scope.positionInfo.explanmit.indexOf('P'));
	if($scope.positionInfo.explanmit.indexOf('P')===0){
	    console.log('mam p na zacatku, nedelam nic');
	    // P mam na zacatku nedelam nic
	}
	else{
	    // na zacatek pridam P
	    console.log('pridavam P na zacatek');
	    $scope.positionInfo.explanmit = 'P'+$scope.positionInfo.explanmit;
	}
    }
});



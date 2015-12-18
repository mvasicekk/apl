/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var aplApp = angular.module('d550iApp');

aplApp.controller('d550iController', function ($scope, $http,$timeout) {
    $scope.kundeVon = "";
    $scope.kundeBis = "";
    $scope.datumVon;
    $scope.datumBis;
    $scope.teilMatch="";
    $scope.exMatch="";
    $scope.mitImportDetail=false;
    $scope.stkOption = 'ba';
    $scope.securityInfo = undefined;
    $scope.columns=10;
    
    var d550it;
    
    $scope.dateOptions = {
		dateFormat: 'dd.mm.yy',
		firstDay: 1
    };
	    
    $scope.$on('$viewContentLoaded', function(event) {
	    d550it = $('#d550it');
	    $('#spinner').hide();
    });
    
    $scope.initSecurity = function(){
	var p={
	    form_id:'d550i'
	};
	return $http.post('./getSecurityInfo.php',p).then(
		    function(response){
			$scope.securityInfo = response.data.securityInfo;
			$scope.columns = $scope.securityInfo.showArray.vzkd_column?10:9;     
		    }
		);
    }
    
    $scope.showPrintDialog = function(){
	d550it.floatThead('destroy');
	window.onafterprint = function(){
	    console.log("Printing completed...");
	    d550it.floatThead();
	}
	window.print();
    };
    
    $scope.toggleShowEditBemerkung = function(r){
//	console.log('toggle edit Bemerkung')
//	console.log(r);
	if(r.dmaRow.showEditBemerkung==undefined || r.dmaRow.showEditBemerkung===false){
	    r.dmaRow.showEditBemerkung=true;
	}
	else{
	    r.dmaRow.showEditBemerkung=false;
	}
    }
    
    $scope.bemerkungChanged = function(r,field){
//	console.log(e);
	$http.post('./updateDMAField.php', {r: r,field:field}).then(function (response) {
		
	    });
    }
    /**
     * 
     * @param {type} e
     * @returns {undefined}
     */
    $scope.getZeilen = function(e){
	console.log('getZeilen event.keyCode='+e.which);
	if (
		(
		(($scope.kundeVon.length==3)&&($scope.kundeBis.length==3))
		||(($scope.datumVon!==null)&&($scope.datumBis!==null))
		||(($scope.teilMatch.length>0))
		)
		&&
		(e.which==13)
	    ) {
	    console.log('splnen if');
	    //$('#spinner').show();
	    if(($scope.datumVon)&&($scope.datumBis)){
		var v = $scope.datumVon.getTime();
		var b = $scope.datumBis.getTime();
	    }
	    else{
		var v = 0;
		var b = 0;
	    }
	    console.log('posilam get pozadavek');
	    $('#spinner').show();
	    $http.get('./getD550i.php?kundevon=' + $scope.kundeVon
		    +'&kundebis='+$scope.kundeBis
		    +'&von='+v
		    +'&bis='+b
		    +'&teil='+$scope.teilMatch
		    +'&export='+$scope.exMatch
		    )
		    .success(function (data) {
			$scope.zeilen = data.zeilen;
			$scope.dZeilen = [].concat($scope.zeilen);
			$scope.teileKeysArray = data.teileKeysArray;
			$timeout(function(){
			    d550it.floatThead('destroy');
			    d550it.floatThead();
			    d550it.floatThead('reflow');
			    $('#spinner').hide();
			},100);
		    });
	}
    };
    
    $scope.initSecurity();
});



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
    $scope.mitImportDetail=false;
    $scope.stkOption = 'ba';
    
    var d550it;
    
    $scope.dateOptions = {
		dateFormat: 'dd.mm.yy',
		firstDay: 1
    };
	    
    $scope.$on('$viewContentLoaded', function(event) {
	    d550it = $('#d550it');
	    $('#spinner').hide();
    });
    
    $scope.showPrintDialog = function(){
	d550it.floatThead('destroy');
	window.onafterprint = function(){
	    console.log("Printing completed...");
	    d550it.floatThead();
	}
	window.print();
    };
    
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
});



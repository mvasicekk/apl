/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var aplApp = angular.module('persstatApp');

aplApp.controller('persstatController', function ($scope, $http,$timeout) {
    $scope.persVon = "";
    $scope.persBis = "";
    $scope.datumVon;
    $scope.datumBis;
    $scope.showGroups = {};
    
    var d550it;
    
    $scope.dateOptions = {
		dateFormat: 'dd.mm.yy',
		firstDay: 1
    };
	    
    $scope.$on('$viewContentLoaded', function(event) {
	    d550it = $('#d550it');
	    $('#spinner').hide();
    });
    
    $scope.monthValueClicked = function(e,r){
	var eId = e.target.id;
	//zlikvidovat popovery
	$('div[id^=popover]').popover('destroy');
	$http.post('./getDetailContent.php', {r: r,eId:eId}).then(function (response) {
	    var content = response.data.content;
	    var popOptions = {
		container:'body',
		content:response.data.content,
		html:true,
		placement:'top',
		title:'Detail',
		trigger:'manual',
	    };
	    $('#'+eId).popover(popOptions);
	    $('#'+eId).popover('show');
	});
	console.log(r);
    }
    
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
		(($scope.persVon.length>0)&&($scope.persBis.length>0))
		&&(($scope.datumVon!==null)&&($scope.datumBis!==null))
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
	    $http.get('./getPersStat.php?persvon=' + $scope.persVon
		    +'&persbis='+$scope.persBis
		    +'&von='+v
		    +'&bis='+b
		    )
		    .success(function (data) {
			$scope.zeilen = data.zeilen;
			$scope.groups = data.groups;
			$scope.monthsArray = data.monthsArray;
			$scope.dZeilen = [].concat($scope.zeilen);
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



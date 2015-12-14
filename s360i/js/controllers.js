/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var aplApp = angular.module('s360iApp');

aplApp.controller('s360iController', function ($scope, $http,$timeout) {
    $scope.kundeVon = '111';
    $scope.kundeBis = '999';
    $scope.reklVon;
    $scope.reklBis;
    $scope.reklnr='';
    $scope.wahrung='EUR';
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
	if($('div[id^=popover]').length>0){
	    $('div[id^=popover]').popover('destroy');
	    return;
	}
	
	$http.post('./getDetailContent.php', {r: r,eId:eId}).then(function (response) {
	    var content = response.data.content;
	    var popOptions = {
		container:'body',
		content:response.data.content,
		html:true,
		placement:'bottom',
		title:response.data.title,
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
	console.log('kundeVon='+$scope.kundeVon+' kundeVon.length='+$scope.kundeVon.length);
	console.log('kundeBis='+$scope.kundeBis+' kundeBis.length='+$scope.kundeBis.length);
	if (
		(
		    ($scope.kundeVon.length>0)&&($scope.kundeBis.length>0)
		)
		&&
		(e.which==13)
	    ) {
	    console.log('splnen if');
	    //$('#spinner').show();
	    if(($scope.reklVon)&&($scope.reklBis)){
		var v = $scope.reklVon.getTime();
		var b = $scope.reklBis.getTime();
	    }
	    else{
		var v = 0;
		var b = 0;
	    }
	    console.log('posilam get pozadavek');
	    if($('div[id^=popover]').length>0){
		$('div[id^=popover]').popover('destroy');
	    }
	    $('#spinner').show();
	    $http.get('./getS360i.php?kundevon=' + $scope.kundeVon
		    +'&kundebis='+$scope.kundeBis
		    +'&von='+v
		    +'&bis='+b
		    +'&reklnr='+$scope.reklnr
		    )
		    .success(function (data) {
			$scope.zeilen = data.zeilen;
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



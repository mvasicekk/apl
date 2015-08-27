/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var aplApp = angular.module('abmahnunggenApp');

aplApp.controller('formController', function ($scope, $http,$timeout,$routeParams) {
    $scope.abmahnungid = $routeParams.abmahnungid;
    $scope.abmahnungInfo = undefined;
    $scope.pdfReady = false;
    
    /**
     * 
     * @returns {undefined}
     */
    $scope.abmahnungGenerieren = function () {
	    console.log('abmahnung generieren');
	    var params = {
		texte: $scope.textArray,
		abmahnungInfo:$scope.abmahnungInfo,
		persInfo:$scope.persInfo,
		persDetailInfo:$scope.persDetailInfo,
	    };
	    $http.post('../Reports/Abmahnung_pdf.php', params).then(function (response) {
		console.log('abmahnung generiert ' + response.data);
		$scope.filename = response.data.filename;
		$scope.pdfPath = response.data.pdfPath;
		$scope.pdfReady = true;
	    });
    }
    
    /**
     * 
     * @returns {undefined}
     */
    $scope.getAbmahnungInfo = function getAbmahnungInfo(){
	console.log('getAbmahnungInfo '+$scope.abmahnungid);
	if($scope.abmahnungid>0){
	    var req = {
		method:'POST',
		url:'./getAbmahnungInfo.php',
		data:{
		    abmahnungid:$scope.abmahnungid
		}
	    };
	    $http(req)
		    .then(function(response){
//			alert('then');
			$scope.abmahnungInfo = response.data.abmahnungInfo;
			$scope.abmahnungGrundId = response.data.abmahnungGrundId;
			$scope.persInfo = response.data.persInfo;
			$scope.aktualDatum = response.data.aktualDatum;
			$scope.persDetailInfo = response.data.persDetailInfo;
			$scope.textArray = response.data.textArray;
			$scope.textArray.text20 = $scope.textArray.text20 + ', '+$scope.aktualDatum;
			$scope.textArray.textOsC = 'os.č. '+$scope.abmahnungInfo.persnr;
		    });
	}
    };
    
    $scope.getAbmahnungInfo();
    
//    $scope.terminMatch = '';
//    $scope.terminMatchVon = '';
//    $scope.terminMatchBis = '';
//    $scope.importMatch = '';
//    $scope.teilMatch = '';
//    $scope.mitPaletten = false;
//    $scope.mitReklamation = false;
//    
//    var d607it;
//    
//    $scope.$on('$viewContentLoaded', function(event) {
//	    d607it = $('#d607it');
//	    $('#spinner').hide();
//    });
//    
//    $scope.showPrintDialog = function(){
//	d607it.floatThead('destroy');
//	window.onafterprint = function(){
//	    console.log("Printing completed...");
//	    d607it.floatThead();
//	}
//	window.print();
//    };
//    $scope.getZeilen = function(e){
//	console.log('getZeilen event.keyCode='+e.which);
//	if (($scope.terminMatchVon.length >= 3)&&($scope.terminMatchBis.length >= 3)&&(e.which==13)) {
//	    $('#spinner').show();
//	    $http.get('./getD607i.php?terminvon=' + $scope.terminMatchVon
//		    +'&terminbis='+$scope.terminMatchBis
//		    +'&import='+$scope.importMatch
//		    +'&teil='+$scope.teilMatch
//		    )
//		    .success(function (data) {
//			$scope.zeilen = data.zeilen;
//			$scope.zeilenD = data.zeilenD;
//			$scope.zeilenDA = data.zeilenDA;
//			$scope.dZeilen = [].concat($scope.zeilen);
//			$scope.abgnrKeysArray = data.abgnrKeysArray;
//			$scope.aartKeysArray = data.aartKeysArray;
//			$scope.terminKeysArray = data.terminKeysArray;
//			$scope.terminArray = data.terminArray;
//			$scope.teileArray = data.teileArray;
//			$timeout(function(){
//			    d607it.floatThead('destroy');
//			    d607it.floatThead();
//			    d607it.floatThead('reflow');
//			    $('#spinner').hide();
//			},100);
//		    });
//	}
//    };
});



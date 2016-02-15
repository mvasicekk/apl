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
    $scope.showQR = false;
    $scope.BewertungKriteria = undefined;
    $scope.bewertungForDetails = [
	"A6:a6_prozent:q_auss",
	"rekl:sum_bewertung_E:q_reklamationen",
	"rekl:sum_bewertung_I:q_reklamationen",
	"HF_repkosten:faktor:q_reparaturen"
    ];

        /**
     * 
     * @param {type} kunde
     * @param {type} bereich
     * @param {type} interval
     * @param {type} grenze
     * @returns {undefined}
     */
    function updateBewertungen(kunde, bereich, interval, grenze) {
	// podle bereichu si najdu radky, kde budu updatovat
	var rowsToUpdate = $scope.bewertungForDetails.filter(function (cv, index) {
	    var a = cv.split(':');
	    if (a.length === 3) {
		if (a[2] == bereich) {
		    return true;
		}
		else {
		    return false;
		}
	    }
	});

	if (rowsToUpdate.length > 0) {
	    if ($scope.zeilen !== undefined && $scope.zeilen.length > 0) {
		rowsToUpdate.forEach(function (rtu) {
		    var a = rtu.split(':');
		    var group = a[0];
		    var groupDetail = a[1];
		    var bewertungDetail = 'bewertung_js';
		    //najit vsechny radky, ktere budu updatovat
		    $scope.zeilen.forEach(function (zeileValue, index) {
			var zeileGroup = zeileValue.group;
			var zeileGroupDetail = zeileValue.groupDetail;
			if (zeileGroup == group && zeileGroupDetail == groupDetail) {
			    currentValue = $scope.zeilen[index];
			    cellToUpdate = $scope.zeilen[index + 1];
			    //jednotlive mesice
			    $scope.monthsArray.forEach(function (m) {
				v1 = currentValue.monthValues[m];
				if (v1 !== null && v1 !== undefined && v1 !== '' && v1 !== ' ') {
				    v2 = v1.toString().replace(',', '.');
				}
				else {
				    v2 = 0;
				}
				v3 = parseFloat(v2);
				v = v3;
				if ((hodnota = $scope.getBewertungKriterium(v, 100, bereich, 1)) !== null) {
				    $scope.zeilen[index + 1].monthValues[m] = hodnota.bewertung;
				}
				else {
				    $scope.zeilen[index + 1].monthValues[m] = 6;
				}
			    });
			    //sumy pro mesice
			    m = 'sum';
			    v1 = currentValue.monthValues[m];
			    if (v1 !== null && v1 !== undefined && v1 !== '' && v1 !== ' ') {
				v2 = v1.toString().replace(',', '.');
			    }
			    else {
				v2 = 0;
			    }
			    v3 = parseFloat(v2);
			    v = v3;
			    if ((hodnota = $scope.getBewertungKriterium(v, 100, bereich, 12)) !== null) {
				$scope.zeilen[index + 1].monthValues[m] = hodnota.bewertung;
			    }
			    else {
				$scope.zeilen[index + 1].monthValues[m] = 6;
			    }
			}
		    });
		});
	    }
	}
    }

    var d550it;
    
    $scope.dateOptions = {
		dateFormat: 'dd.mm.yy',
		firstDay: 1
    };
	    
    $scope.$on('$viewContentLoaded', function(event) {
	    d550it = $('#d550it');
	    $('#spinner').hide();
    });
    
    $scope.getBewertungKriteria = function(kunde,bereich){
	$http.post('./getBewertungKriteria.php', {kunde:kunde,bereich:bereich}).then(function (response) {
	    $scope.BewertungKriteria = response.data.bewertungKriteriaRows;});
    }
    
    $scope.getBewertungKriterium = function(v,kunde,bereich,interval){
	if($scope.BewertungKriteria!==undefined){
	    var kriteriumsArray = $scope.BewertungKriteria.filter(function(item){
		//console.log(item);
		if(item.kunde==kunde && item.bereich==bereich && item.interval_monate==interval && parseFloat(item.grenze)>=parseFloat(v)){
		    return true;
		}
		else{
		    return false;
		}
	    });
	    if(kriteriumsArray.length>0){
		//seradit vzestupne podle grenze
		kriteriumsArray.sort(function(a,b){
		    if(a.grenze<b.grenze){
			return -1;
		    }
		    if(a.grenze>b.grenze){
			return 1;
		    }
		    return 0;
		});
		return kriteriumsArray[0];
	    }
	    else{
		return null;
	    }
	}
    }
    
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
    
    $scope.kriteriaChanged = function(kriteria,v){
//	console.log(kriteria);
//	console.log(v);
	updateBewertungen(kriteria.kunde,kriteria.bereich,kriteria.interval_monate,v)
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
	    if($('div[id^=popover]').length>0){
		$('div[id^=popover]').popover('destroy');
	    }
	    $('#spinner').show();
	    $http.get('./getPersStat.php?persvon=' + $scope.persVon
		    +'&persbis='+$scope.persBis
		    +'&von='+v
		    +'&bis='+b
		    )
		    .success(function (data) {
			$scope.zeilen = data.zeilen;
			//projdu vsechny zeilen a pridam bewertung pomoci javascriptu
			for(index=0;index<$scope.zeilen.length;index++){
			    currentValue = $scope.zeilen[index];
    			    //projdu pole s pozadovanymi bewertung
			    $scope.bewertungForDetails.forEach(function(cV){
				var groupDetail = cV.split(':');
				//console.log(groupDetail);
				if(groupDetail.length==3){
				    if(groupDetail[0]==currentValue.group && groupDetail[1]==currentValue.groupDetail){
					console.log(groupDetail);
					// pro tuto kombinaci chci spocitat bewertung
					// do $scope.zeilen pridam radek
					
					var zeileToInsert = {
					    group:currentValue.group,
					    groupDetail:'bewertung_js',
					    name:currentValue.name,
					    persnr:currentValue.persnr,
					    section:currentValue.section,
					    monthValues:[]
					}
					//jednotlive mesice
					data.monthsArray.forEach(function(m){
					    v1 = currentValue.monthValues[m];
					    console.log('v1='+v1);
					    if(v1!==null && v1!==undefined && v1!=='' && v1!==' '){
						v2 = v1.toString().replace(',','.');
					    }
					    else{
						v2 = 0;
					    }
					    //console.log('v2='+v2);
					    
					    v3 = parseFloat(v2);
					    //console.log('v3='+v3);
					    
					    v = v3;
					    //console.log('v='+v);
					    if((hodnota=$scope.getBewertungKriterium(v,100,groupDetail[2],1))!==null){
						zeileToInsert.monthValues[m] = hodnota.bewertung;
					    }
					    else{
						zeileToInsert.monthValues[m] = 6;
					    }
					});
    					//sumy pro mesice
					m = 'sum';
					v1 = currentValue.monthValues[m];
					//console.log('v1='+v1);
					   if(v1!==null && v1!==undefined && v1!=='' && v1!==' '){
						v2 = v1.toString().replace(',','.');
					    }
					    else{
						v2 = 0;
					    }
					    //console.log('v2='+v2);
					    
					    v3 = parseFloat(v2);
					    //console.log('v3='+v3);
					    
					    v = v3;
					    //console.log('v='+v);
					    if((hodnota=$scope.getBewertungKriterium(v,100,groupDetail[2],12))!==null){
						zeileToInsert.monthValues[m] = hodnota.bewertung;
					    }
					    else{
						zeileToInsert.monthValues[m] = 6;
					    }
					//czk
					
					$scope.zeilen.splice(index+1,0,zeileToInsert);
					//console.log(currentValue);
				    }
				}
			    });

			}
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
    
    
    // init
    $scope.getBewertungKriteria(100,'');
});



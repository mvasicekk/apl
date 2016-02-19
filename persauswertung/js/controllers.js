/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var aplApp = angular.module('persstatApp');

aplApp.controller('persstatController', function ($scope, $http,$timeout) {
    $scope.persVon = "";
    $scope.persBis = "";
    $scope.stammOE = "*";
    $scope.datumVon;
    $scope.datumBis;
    $scope.showGroups = {};
    $scope.showQR = false;
    $scope.BewertungKriteria = undefined;
    $scope.bewertungForDetails = [
	"A6:a6_prozent:q_auss",
	"nacharbeit:faktor:q_nacharbeit",
	"rekl:sum_bewertung_E:q_reklamationen_E",
	"rekl:sum_bewertung_I:q_reklamationen_I",
	"HF_repkosten:faktor:q_reparaturen"
    ];
    $scope.betragSumme = {};
    $scope.personalSumme = {};

    /**
     * 
     * @param {type} zeilen
     * @param {type} betragZeile
     * @returns {undefined}
     */
    function makePersonalSummen(zeilen,betragZeile){
	var summenArray = [];
	var sumObj = {};
	
	summenArray = zeilen.filter(function(i){
	    if(i.section=='groupdetail'){
		//console.log(i);
		if((i.groupDetail.indexOf(betragZeile)>-1)){
		    return true;
		}
	    }
	    return false;
	});
	summenArray.forEach(function(i){
	    if(!sumObj.hasOwnProperty(i.persnr)){
		sumObj[i.persnr] = {
		    count:1,
		    monthValues: {}
		};
		for(mY in i.monthValues){
		    sumObj[i.persnr].monthValues[mY] = i.monthValues[mY]==''?0:parseFloat(i.monthValues[mY]);
		}
	    }
	    else{
		// uz jsem tuto property mel, inkrementuju citac a prictu hodnoty monthValues
		sumObj[i.persnr].count++;
		for(monthYear in sumObj[i.persnr].monthValues){
		    //console.log('monthYear='+monthYear);
		    var val = i.monthValues[monthYear]==''?0:parseFloat(i.monthValues[monthYear]);
		    sumObj[i.persnr].monthValues[monthYear] = sumObj[i.persnr].monthValues[monthYear] + val;
		}
	    }
	});
	return sumObj;
    }
    /**
     * 
     * @param {type} zeilen
     * @param {type} forDetails
     * @param {type} bereich
     * @returns {controllers_L9.makeGroupSummen.sumObj}
     */
    function makeGroupSummen(zeilen,forDetails,bereich){
	var summenArray = [];
	var sumObj = {};
	//pripravit si pole
	var groupsArray = forDetails.map(function(i){
	    var a = i.split(':');
	    return a[0];
	});
	//console.log(groupsArray);
	
	var groupsDetailArray = forDetails.map(function(i){
	    var a = i.split(':');
	    return a[1];
	});
	//console.log(groupsDetailArray);
	
	summenArray = zeilen.filter(function(i){
	    if(i.section=='groupdetail'){
		//console.log(i);
		if((groupsArray.indexOf(i.group)>-1) && (i.groupDetail.indexOf('bewertung_betrag')>-1)){
		    return true;
		}
	    }
	    return false;
	});
	summenArray.forEach(function(i){
	    if(!sumObj.hasOwnProperty(i.group)){
		sumObj[i.group] = {
		    count:1,
		    monthValues: {}
		};
		for(mY in i.monthValues){
		    sumObj[i.group].monthValues[mY] = i.monthValues[mY]==''?0:parseFloat(i.monthValues[mY]);
		}
	    }
	    else{
		// uz jsem tuto property mel, inkrementuju citac a prictu hodnoty monthValues
		sumObj[i.group].count++;
		for(monthYear in sumObj[i.group].monthValues){
		    //console.log('monthYear='+monthYear);
		    var val = i.monthValues[monthYear]==''?0:parseFloat(i.monthValues[monthYear]);
		    sumObj[i.group].monthValues[monthYear] = sumObj[i.group].monthValues[monthYear] + val;
		}
	    }
	});
	return sumObj;
    }
    /**
     * 
     * @param {type} kunde
     * @param {type} bereich
     * @param {type} interval
     * @param {type} grenze
     * @returns {undefined}
     */
    function updateBetragSummen(kunde, bereich, interval, grenze) {
	var summenObject = makeGroupSummen($scope.zeilen, $scope.bewertungForDetails,bereich);
	$scope.betragSumme = summenObject;
	//console.log(summenObject);
	//a pridat radky na zacatek tabulky
	for (gr in summenObject) {
	    //najit odpovidajici radek a updatnout hodnoty monthValues
	    // group == gr, droupDetail = 'betrag'
	    for(index=0;index<$scope.zeilen.length;index++){
		var val = $scope.zeilen[index];
		if(val.section=='summebetrag' && val.group==gr && val.groupDetail=='betrag'){
		    //console.log(val);
		    for(mY in val.monthValues){
			$scope.zeilen[index].monthValues[mY] = summenObject[gr].monthValues[mY];
		    }
		    break;
		}
	    };
	}
    }
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
				    v2 = '';
				}
				v3 = parseFloat(v2);
				v = v3;
				if ((hodnota = $scope.getBewertungKriterium(v, 100, bereich, 1,currentValue.regeloe)) !== null) {
				    $scope.zeilen[index + 1].monthValues[m] = hodnota.bewertung;
				    $scope.zeilen[index + 2].monthValues[m] = hodnota.betrag;
				}
				else {
				    $scope.zeilen[index + 1].monthValues[m] = '';
				    $scope.zeilen[index + 2].monthValues[m] = '';
				}
			    });
			    //sumy pro mesice
			    m = 'sum';
			    v1 = currentValue.monthValues[m];
			    if (v1 !== null && v1 !== undefined && v1 !== '' && v1 !== ' ') {
				v2 = v1.toString().replace(',', '.');
			    }
			    else {
				v2 = '';
			    }
			    v3 = parseFloat(v2);
			    v = v3;
			    if ((hodnota = $scope.getBewertungKriterium(v, 100, bereich, 12,currentValue.regeloe)) !== null) {
				$scope.zeilen[index + 1].monthValues[m] = hodnota.bewertung;
				$scope.zeilen[index + 2].monthValues[m] = hodnota.betrag;
			    }
			    else {
				$scope.zeilen[index + 1].monthValues[m] = '';
				$scope.zeilen[index + 2].monthValues[m] = '';
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
    
    /**
     * 
     * @param {type} v
     * @param {type} kunde
     * @param {type} bereich
     * @param {type} interval
     * @returns {controllers_L9.$scope.getBewertungKriterium.kriteriumsArray}
     */
    $scope.getBewertungKriterium = function(v,kunde,bereich,interval,oe){
	if(isNaN(v)){
	    return null;
	}
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
		// mam radek
		var krit = kriteriumsArray[0];
		//console.log(krit.oe);
		var re = new RegExp(krit.oe,"gi");
		//console.log(re);
		if(oe.match(re)!==null){
		    return kriteriumsArray[0];
		}
		else{
		    return null;
		}
		//return kriteriumsArray[0];
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
	//console.log(r);
    }
    
    $scope.showPrintDialog = function(){
	d550it.floatThead('destroy');
	window.onafterprint = function(){
	    //console.log("Printing completed...");
	    d550it.floatThead();
	}
	window.print();
    };
    
    $scope.kriteriaChanged = function (kriteria, v, kriterium) {
	$http.post('./saveKriteria.php', {kriteria: kriteria, v: v, kriterium: kriterium}).then(function (response) {

	});
	updateBewertungen(kriteria.kunde, kriteria.bereich, kriteria.interval_monate, v);
	updateBetragSummen(kriteria.kunde, kriteria.bereich, kriteria.interval_monate, v);
	var persSummenObject = makePersonalSummen($scope.zeilen, 'bewertung_betrag');
	$scope.personalSumme = persSummenObject;
	//console.log(persSummenObject);
	//projit radky se zahlavim pro persnr a priradit hodnoty z persSummenObject
	$scope.zeilen.filter(function (z1) {
	    if (z1.section == 'persheader') {
		return true;
	    }
	    else {
		return false
	    }
	}).forEach(function (z2) {
	    z2.monthValues = $scope.personalSumme[z2.persnr].monthValues;
	});
    }
    
    
    /**
     * 
     * @param {type} e
     * @returns {undefined}
     */
    $scope.getZeilen = function(e){
	//console.log('getZeilen event.keyCode='+e.which);
	if (
		(
		(($scope.persVon.length>0)&&($scope.persBis.length>0))
		&&(($scope.datumVon!==null)&&($scope.datumBis!==null))
		&&(($scope.stammOE.length>0))
		)
		&&
		(e.which==13)
	    ) {
	    //console.log('splnen if');
	    //$('#spinner').show();
	    if(($scope.datumVon)&&($scope.datumBis)){
		var v = $scope.datumVon.getTime();
		var b = $scope.datumBis.getTime();
	    }
	    else{
		var v = 0;
		var b = 0;
	    }
	    //console.log('posilam get pozadavek');
	    if($('div[id^=popover]').length>0){
		$('div[id^=popover]').popover('destroy');
	    }
	    $('#spinner').show();
	    $http.get('./getPersStat.php?persvon=' + $scope.persVon
		    +'&persbis='+$scope.persBis
		    +'&stammoe='+$scope.stammOE
		    +'&von='+v
		    +'&bis='+b
		    )
		    .success(function (data) {
			$scope.zeilen = data.zeilen;
			
			var betragSummen = {};
			
			//projdu vsechny zeilen a pridam bewertung pomoci javascriptu
			for(index=0;index<$scope.zeilen.length;index++){
			    currentValue = $scope.zeilen[index];
    			    //projdu pole s pozadovanymi bewertung
			    $scope.bewertungForDetails.forEach(function(cV){
				var groupDetail = cV.split(':');
				//console.log(groupDetail);
				if(groupDetail.length==3){
				    if(groupDetail[0]==currentValue.group && groupDetail[1]==currentValue.groupDetail){
					//console.log(groupDetail);
					// pro tuto kombinaci chci spocitat bewertung
					// do $scope.zeilen pridam radek
					betragSummen[currentValue.group] = {monthValues:[]};
					var zeileToInsert = {
					    group:currentValue.group,
					    groupDetail:'bewertung_js',
					    name:currentValue.name,
					    persnr:currentValue.persnr,
					    section:currentValue.section,
					    monthValues:[]
					}
					var zeileToInsertBetrag = {
					    group:currentValue.group,
					    groupDetail:'bewertung_betrag',
					    name:currentValue.name,
					    persnr:currentValue.persnr,
					    section:currentValue.section,
					    monthValues:[]
					}
					//jednotlive mesice
					var sumMonths = {};
					data.monthsArray.forEach(function(m){
					    v1 = currentValue.monthValues[m];
					    //console.log('v1='+v1);
					    if(v1!==null && v1!==undefined && v1!=='' && v1!==' '){
						v2 = v1.toString().replace(',','.');
					    }
					    else{
						v2 = '';
					    }
					    v3 = parseFloat(v2);
					    v = v3;
					    if((hodnota=$scope.getBewertungKriterium(v,100,groupDetail[2],1,currentValue.regeloe))!==null){
						zeileToInsert.monthValues[m] = hodnota.bewertung;
						zeileToInsertBetrag.monthValues[m] = hodnota.betrag;
						if(sumMonths[m]===undefined){
						    sumMonths[m] = 0;
						}
						sumMonths[m] += parseFloat(hodnota.betrag);
					    }
					    else{
						zeileToInsert.monthValues[m] = '';
						zeileToInsertBetrag.monthValues[m] = '';
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
						v2 = '';
					    }
					    v3 = parseFloat(v2);
					    v = v3;
					    if((hodnota=$scope.getBewertungKriterium(v,100,groupDetail[2],12,currentValue.regeloe))!==null){
						zeileToInsert.monthValues[m] = hodnota.bewertung;
						zeileToInsertBetrag.monthValues[m] = hodnota.betrag;
						if(sumMonths[m]===undefined){
						    sumMonths[m] = 0;
						}
						sumMonths[m] += parseFloat(hodnota.betrag);
					    }
					    else{
						zeileToInsert.monthValues[m] = '';
						zeileToInsertBetrag.monthValues[m] = '';
					    }
					//console.log(sumMonths);
					//bewertung
					$scope.zeilen.splice(index+1,0,zeileToInsert);
					//betrag
					$scope.zeilen.splice(index+2,0,zeileToInsertBetrag);
					//console.log(currentValue);
				    }
				}
			    });
			}
			//console.log(betragSummen);
			var summenObject = makeGroupSummen($scope.zeilen,$scope.bewertungForDetails);
			$scope.betragSumme = summenObject;
			
			var persSummenObject = makePersonalSummen($scope.zeilen,'bewertung_betrag');
			$scope.personalSumme = persSummenObject;
			console.log(persSummenObject);
			//projit radky se zahlavim pro persnr a priradit hodnoty z persSummenObject
			$scope.zeilen.filter(function(z1){
			    if(z1.section=='persheader'){
				return true;
			    }
			    else{
				return false
			    }
			}).forEach(function(z2){
			    z2.monthValues = $scope.personalSumme[z2.persnr].monthValues;
			});
			console.log($scope.zeilen);
			//a pridat radky na zacatek tabulky
			for(gr in summenObject){
			    var zeileToInsert = {
					    group:gr,
					    groupDetail:'betrag',
					    section:'summebetrag',
					    monthValues:summenObject[gr].monthValues
					};
			    $scope.zeilen.splice($scope.zeilen.length,0,zeileToInsert);
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



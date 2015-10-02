/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var aplApp = angular.module('auftragApp');

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

aplApp.controller('detailController', function ($scope, $routeParams,$http,$timeout,$window,$location) {
    
    var auftragTable;

    $scope.securityInfo = undefined;
    // pro uchovani hodnot pred editaci radku
    $scope.dauftragOriginalArray = [];
    $scope.preisupdate = false;
    $scope.username;
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
	if(dt===null){
	    return null;
	}
	var t = dt.split(/[- :]/);
	// Apply each element to the Date function
	var d = new Date(t[0], t[1]-1, t[2], t[3], t[4], t[5]);
	return d;
    }
    
    /**
     * 
     * @returns {undefined}
     */
    var saveFormChanges = function(){
	var propChanges = [];
	// projdu vsechny vlastnosti objektu auftragInfo a porovnam s puvodni hodnotou
	//vytvorim si pole zmenenych vlastnosti
	var keys = Object.keys($scope.auftragInfo);
	var original = JSON.parse($scope.auftragInfoOriginal);
	
	for (i=0;i<keys.length;i++) {
	    var pr = keys[i];
	    if ($scope.auftragInfo.hasOwnProperty(pr)) {
		//date objekty musim porovnavat specialne
		if ($scope.auftragInfo[pr] instanceof Date){
		    vAktual = $scope.auftragInfo[pr].toISOString();
		    vOriginal = original[pr]==''?'':new Date(original[pr]).toISOString();
		}
		else{
		    vAktual = $scope.auftragInfo[pr]===null?'':$scope.auftragInfo[pr].toString();
		    vOriginal = original[pr]===null?'':original[pr].toString();
		}
		if(vOriginal!=vAktual){
		    propChanges.push({prop:pr,oldVal:original[pr],newVal:$scope.auftragInfo[pr]});
		}
	    }
	}
	// pokud bude mit pole nejake polozky, ulozit zmeny do db
	if(propChanges.length>0){
	    //ulozit zmeny do DB
	    console.log('ukladam zmeny do DB');
	    
	    $http.post('./saveDaufkopfChanges.php', {auftragsnr:$scope.auftragInfo.auftragsnr,propChanges: propChanges}).then(function (response) {
		console.log(response.data);
		$scope.formDataChanged = false;
	    });
	}
	
    }
    
    /**
     * 
     */
    $scope.testFormChanges = function(){
	console.log('testing form changes');
	var d = JSON.stringify($scope.auftragInfo);
	if($scope.auftragInfoOriginal!==d){
	    $scope.formDataChanged = true;
	    //TODO zavolat ukladaci funkci, po vyrizeni ukladani nastavit 
	    //formDataChanged na false a auftragInfoOriginal nastavit na
	    //aktualni auftraginfo
	    //funkce by mela vratit promise (asynchronni volani) protoze muze trvat dlouho
	    saveFormChanges();
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
	    //prenastavit url
	    console.log($location.url());
	    $location.url('/det/'+$scope.auftragsnr);
	    
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
		    if($scope.zielortArray!==null){
			for(i=0;i<$scope.zielortArray.length;i++){
			    if($scope.zielortArray[i].id==$scope.auftragInfo.zielort_id){
				$scope.zielort.selected = $scope.zielortArray[i];
				break;
			    }
			}
		    }
		    else{
			$scope.zielort.selected = undefined;
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
			$scope.auftragInfo.exsolldat1 = convertMysql2Date($scope.auftragInfo.ex_soll_datetime);
			$scope.auftragInfo.imsolldat1 = convertMysql2Date($scope.auftragInfo.im_soll_datetime);
			$scope.auftragInfo.aufdat1 = convertMysql2Date($scope.auftragInfo.aufdat_raw);
			$scope.auftragInfo.auslieferdat1 = convertMysql2Date($scope.auftragInfo.ausliefer_raw);
			
			// ulozit originalni stav dat, abych mohl porovnat zda jsou ve formulari zmeny
			$scope.auftragInfoOriginal = JSON.stringify($scope.auftragInfo);
			$scope.dauftrPos = response.data.dauftrPos;
			$scope.auftrag.selected.auftragsnr = response.data.auftragInfo.auftragsnr;
			$scope.displayDauftrPos = [].concat($scope.dauftrPos);
			
			$scope.preisupdate = response.data.preisupdate;
			$scope.username = response.data.user;
			
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
    
    $scope.initSecurity = function(){
	var p={
	    form_id:'auftrag'
	};
	return $http.post('./getSecurityInfo.php',p).then(
		    function(response){
			$scope.securityInfo = response.data.securityInfo;
		    }
		);
    }
    
    // init
    $scope.initSecurity();
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
    
    $scope.makeEditable = function(r){
	r.edit=1;
	console.log(r);
	// schovam si puvodni hodnoty pro pripad cancelEditDposRow
	$scope.dauftragOriginalArray.push(JSON.parse(JSON.stringify(r)));
	console.log($scope.dauftragOriginalArray);
	//+ zmenit na tlacitko pro ulozeni radku
    }
    $scope.saveDposRow = function(r){
	// pomoct http.post ulozit radek a pote nastevit edit=0
	r.edit=0;
	//odstranit polozku z dauftragOriginalArray
	for(i=0;i<$scope.dauftragOriginalArray.length;i++){
	    if(r.id_dauftr==$scope.dauftragOriginalArray[i].id_dauftr){
		//odstranit polozku z pole
		$scope.dauftragOriginalArray.splice(i,1);
		break;
	    }
	}
	console.log($scope.dauftragOriginalArray);
	// a vlastni ulozeni
	var params = {r: r};
	    return $http.post(
		    './saveDposRow.php',
		    {params: params}
	    ).then(function (response) {
		console.log(response.data);
		$scope.dauftrPos = response.data.dauftragPositionen;
	    });
    }

    $scope.preisUpdate = function(r){
	var href = '../dauftr/preisupdateformular.php?id_dauftr='+r.id_dauftr+'&level='+'0';
	if(($scope.preisupdate==true)&&(r.hatrechnung==0)){
	    $window.location.href = href;
	}
    }

    $scope.deleteRechnung = function(ai){
	console.log('deleteRechnung ');
	console.log(ai);
	var d = $window.confirm('Rechnung wirklich loeschen / opravdu smazat fakturu ?');
	if(d){
	    console.log('mazu');
	    // a vlastni smazani
		var params = {r: ai};
		return $http.post(
		    './deleteRechnung.php',
		    {params: params}
		).then(function (response) {
		    $scope.getAuftragInfo();
		});
	}
    }
    
    /**
     * 
     * @param {type} auftragInfo
     * @returns {undefined}
     */
    $scope.auftragsMengeSpeichern = function(auftragInfo){
	var d = $window.confirm('Importmenge speichern ?');
	if (d) {
	    	// a vlastni smazani
		var params = {r: $scope.auftragInfo};
		return $http.post(
		    './imStkSpeichern.php',
		    {params: params}
		).then(function (response) {
		    console.log(response.data);
		    $scope.getAuftragInfo();
		});
	}
    }
    
    $scope.deleteDposRow = function(r){
	if(r.KzGut=='G'){
	    var text = "Loeschen ganze Palette ? / smazat celou paletu ?";
	}
	else{
	    var text = "Loeschen Position ? / smazat pozici ?";
	}
	var d = $window.confirm(text);
	if (d) {
	    	// a vlastni smazani
		var params = {r: r};
		return $http.post(
		    './deleteDposRow.php',
		    {params: params}
		).then(function (response) {
		    console.log(response.data);
		    $scope.dauftrPos = response.data.dauftragPositionen;
		});
	}
    }
    
    $scope.cancelEditDposRow = function(r){
	//r.edit=0;
	original = undefined;
	//najit polozku v dauftragOriginalArray,vratit puvodni stav a odstranit z pole
	for(i=0;i<$scope.dauftragOriginalArray.length;i++){
	    if(r.id_dauftr==$scope.dauftragOriginalArray[i].id_dauftr){
		original = JSON.parse(JSON.stringify($scope.dauftragOriginalArray[i]));
		//odstranit polozku z pole
		$scope.dauftragOriginalArray.splice(i,1);
		break;
	    }
	}
	if(original!==undefined){
	    for(i=0;i<$scope.dauftrPos.length;i++){
		if(original.id_dauftr==$scope.dauftrPos[i].id_dauftr){
		    for(p in original){
			if(original.hasOwnProperty(p)){
			    $scope.dauftrPos[i][p] = original[p];
			}
		    }
		    $scope.dauftrPos[i].edit=0;
		    break;
		}
	    }
	}
	console.log($scope.dauftragOriginalArray);
    }
    
//        $scope.openBrana = function(cislobrany){
//	var params = {brana:cislobrany,user:$scope.username};
//		return $http.post(
//		    'http://172.16.1.236/brany/open',
//		    {params: params}
//		).then(function (response) {
//		    console.log(response.data);
//		});
//    }

});


//listController -----------------------------------------------------------------
aplApp.controller('listController', function ($scope, $routeParams,$http,$timeout,$window,$location) {
    $scope.auftragsnr = "";
    $scope.auftrags = [];
    $scope.createNew = false;
    $scope.createNewKundeError = '';
    
    $scope.getAuftragsMatch = function () {
	var params = {a: $scope.auftragsnr};
	return $http.post(
		'./getAuftragsMatch.php',
		{params: params}
	).then(function (response) {
	    console.log(response.data);
	    $scope.auftrags = response.data.auftrags;
	    if(($scope.auftrags==null) && ($scope.auftragsnr.length==8)){
		$scope.createNew = true;
		var prvniTriCisla = $scope.auftragsnr.substring(0,3);
		cislo = parseInt(prvniTriCisla);
		//var cislo = parseInt(kw.value)/1000;
		// test jestli je cislo delitelne 10
		// upravu budu provadet u zakaznika 140
		zbytek=cislo%10;
		if((zbytek>0)&&((cislo-zbytek)==140))
		{
			$scope.newkunde=cislo-zbytek;
		}
		else
		{
                    // pomoc pri zadavani cisla zakaznika
                    if(cislo==358)  cislo=355;
                    if(cislo==350)  cislo=355;
		    if(cislo==352)  cislo=355;
                    if(cislo==106)  cislo=107;
                    if(cislo==131)  cislo=130;
		    if(cislo==132)  cislo=130;
		    if(cislo==112)  cislo=111;
		    if(cislo==113)  cislo=111;
		    if(cislo==198)  cislo=195;
                    $scope.newkunde = cislo;
		}
		$timeout(function() {
		    var element = $window.document.getElementById('newkunde');
		    if(element){
			element.focus();
			element.select();
		    }
		});
	    }
	    else{
		$scope.createNew = false;
	    }
	});
    }
    
    $scope.listRowClicked = function(auftrag){
	console.log(auftrag);
	$location.path('det/'+auftrag.auftragsnr);
	//$window.location.href = './det/'+auftrag.auftragsnr;
    }
    
    $scope.goToAufrag = function(e){
	console.log('goToAuftrag event.keyCode='+e.which);
	if($scope.auftrags!==null){
	    if (($scope.auftrags.length>=1)&&(e.which==13)) {
		$location.path('det/'+$scope.auftrags[0].auftragsnr);
	    }
	}
    }
    
    $scope.createNewAuftrag = function(){
	console.log('create new auftrag');
	console.log($scope.newkunde);
	console.log($scope.auftragsnr);
	var params = {kunde:$scope.newkunde,auftragsnr:$scope.auftragsnr};
		return $http.post(
		    './createNewAuftrag.php',
		    {params: params}
		).then(function (response) {
		    console.log(response.data);
		    $scope.createNewKundeError = response.data.createError;
		    if(response.data.created){
			$location.path('det/'+$scope.auftragsnr);
		    }
		});
    }
});


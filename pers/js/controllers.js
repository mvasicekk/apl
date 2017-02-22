/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var convertMysql2Date = function (dt) {
    if (dt === null) {
	return null;
    }
    var t = dt.split(/[- :]/);
    // Apply each element to the Date function
    var d = new Date(t[0], t[1] - 1, t[2], t[3], t[4], t[5]);
    return d;
}

var aplApp = angular.module('persApp');

aplApp.directive("enterfocus", function () {
    return {
	restrict: "A",
	link: function ($scope, elem, attrs) {
	    var focusables = $(":tabbable");
	    elem.bind("keydown", function (e) {
		var code = e.keyCode || e.which;
		if (code === 13) {
		    var current = focusables.index(this);
		    var next = focusables.eq(current + 1).length ? focusables.eq(current + 1) : focusables.eq(0);
		    console.log('current=' + current + ' next=');
		    //console.log(next);
		    next.focus();
		    next.select();
		    e.preventDefault();
		}
	    });
	}
    }
});

aplApp.directive("formOnChange", function($parse){
  return {
    require: "form",
    link: function(scope, element, attrs){
       var cb = $parse(attrs.formOnChange);
       element.on("change", function(){
          cb(scope);
       });
    }
  }
});


aplApp.controller('persController', function ($scope, $routeParams, $http, $timeout, $window, $location, $sanitize) {
    $scope.isEditor = false;	//urcuje zda muze uzivatel editovat helptext
    $scope.tinyMceOptions = {
	inline: true,
	menubar: false
    };
    $scope.tinymceModel = "tady se da psat pomoci zabudovaneho editoru, zkus to !";
    $scope.dateOptions = {
	dateFormat: 'dd.mm.yy',
	firstDay: 1
    };
    $scope.showHelp = false;
    $scope.datePickerFormat = 'dd.MM.yyyy';
    $scope.securityInfo = undefined;

    $scope.osoby = [];
    $scope.datum = new Date();
    $scope.jenma = true;
    $scope.austritt60 = false;
    $scope.ma = {
	selectedIndex: -1,
	maInfo: null,
	oeInfo: null
    };
    $scope.oes = {
	oeArray: null,
	oeSelected: '*'
    };
    $scope.fahtypen = {
	fahtypenArray: null,
	fahtypidSelected: 0
    };

    $scope.fahigkeiten = {
	fahigkeitenArray: null,
	fahigkeitenidSelected: 0
    };

    var curdate = new Date();

    $scope.hfPremieVon = new Date(curdate.getFullYear(), curdate.getMonth() - 2, 1);
    $scope.hfPremieBis = new Date(curdate.getFullYear(), curdate.getMonth() + 1, 0);
    $scope.kvalifikaceGiltAb = new Date();

    $scope.osobniHodnoceniVon = new Date(curdate.getFullYear(), curdate.getMonth() - 2, 1);
    $scope.osobniHodnoceniBis = new Date(curdate.getFullYear(), curdate.getMonth() + 1, 0);

    $scope.hfPremieArray = null;
    $scope.osobniHodnoceniArray = null;


    $scope.showHromadneOperace = false;
    $scope.hfPremieJahr = curdate.getFullYear();
    $scope.curJahr = curdate.getFullYear();
    $scope.hfPremieMonat = curdate.getMonth();
    $scope.fillHFButtonDisabled = false;
    $scope.lockHFButtonDisabled = false;

    $scope.osobniHodnoceniJahr = curdate.getFullYear();
    $scope.osobniHodnoceniMonat = curdate.getMonth();
    $scope.fillOHButtonDisabled = false;
    $scope.lockOHButtonDisabled = false;


    $scope.inventar = {};
    $scope.inventarArray = [];

    $scope.persInventarArray = [];
    $scope.addparents = false;

    $scope.persKvalifikaceArray = [];
    
    $scope.showPanel = {
	kvalifikace:false,
	inventar:false,
	hfpremie:false,
	osobnihodnoceni:false
    };
    
    $scope.bemerkung = {};
    
    $scope.filt = {
	dstatus : ['MA'],
	oearray : ['*']
    };

    $scope.dpersstatuses = [];
    $scope.status_fur_aby = [];
    
    $scope.hodnoceniArray = ["1","2","3","4","5","6","7","8","9","10"];
    
    $scope.bewFahigkeiten = [];


//    NgMap.getMap().then(function(map) {
//    console.log(map.getCenter());
//    console.log('markers', map.markers);
//    console.log('shapes', map.shapes);
//    });
  
    $scope.addresses = [];
    $scope.refreshAddresses = function(address) {
	console.log(address);
    var params = {address: address, sensor: false};
    if(address.length>0){
	return $http.get('https://maps.googleapis.com/maps/api/geocode/json', {params: params})
      .then(function(response) {
	  if(response.data.status=='OK'){
	      $scope.addresses = response.data.results
	  }
      });
    }
    
    };
    
    
/**
 * 
 * @returns {undefined}
 */
    $scope.dpersstatusChanged = function(){
	console.log('dstatus Changed');
	console.log($scope.filt.dstatus);
	$scope.osobaChanged();
    }
    
    /**
     * 
     * @returns {undefined}
     */
    $scope.isDstatusOnlyMA = function () {
	    if ($scope.filt.dstatus.length == 1) {
		if ($scope.filt.dstatus[0] == 'MA') {
		    return true;
		}
		else {
		    return false;
		}
	    }
	    else {
		return false;
	    }
    }
    /**
     * 
     */
    $scope.deleteDpersInventar = function (pa) {
	$scope.addInventar(null, pa);
    }

    $scope.deleteDpersKvalifikace = function (k) {
	$scope.addKvalifikace(k);
    }
    /**
     * 
     * @returns {undefined}
     */
    $scope.formSubmitted = function () {
	console.log('submit');
    }

    $scope.getShowPanel = function(panelid){
	return showPanel[panelid];
    }
    
    /**
     * 
     * @param {type} field
     * @returns {undefined}
     */
    $scope.bewerberFieldChanged = function (field) {
	console.log('bewerberFieldChanged: ' + field);
	if ($scope.ma.maInfo !== null) {
	    return	$http.post(
		    './updateBewerberField.php',
		    {
			persnr: $scope.ma.maInfo.PersNr,
			value: $scope.ma.bewerberInfo[field],
			field: field
		    }
	    ).then(function (response) {
	    });
	}

    }
    /**
     * 
     * @param {type} pa
     * @param {type} field
     * @returns {undefined}
     */
    $scope.dperskvalifikaceChanged = function (pa, field) {
	console.log(field);
	console.log(pa);
	return	$http.post(
		'./updateDpersKvalifikace.php',
		{
		    pi: pa,
		    field: field
		}
	).then(function (response) {
	});
    }
    /**
     * 
     * @param {type} pa
     * @param {type} field
     * @returns {undefined}
     */
    $scope.dpersinventarChanged = function (pa, field) {
	console.log(field);
	console.log(pa);
	return	$http.post(
		'./updateDpersInventar.php',
		{
		    pi: pa,
		    field: field
		}
	).then(function (response) {
	});
    }
    /**
     * 
     * @param {type} i
     * @returns {undefined}
     */
    $scope.addInventar = function (i, pa) {
	console.log(i);
	return	$http.post(
		'./addInventar.php',
		{
		    i: i,
		    persnr: $scope.ma.maInfo.PersNr,
		    addparents: $scope.addparents,
		    pa: pa
		}
	).then(function (response) {
	    if (response.data.insertId > 0 || response.data.delRows > 0) {
		//neco vlozeno , aktualizuju pole
		getPersInventar();
		$scope.addparents = false;
	    }

	});
    }

    /**
     * 
     * @param {type} i
     * @param {type} pa
     * @returns {unresolved}
     */
    $scope.addKvalifikace = function (k) {
	return	$http.post(
		'./addKvalifikace.php',
		{
		    k: k,
		    oekvalifikace: $scope.oekvalifikace,
		    hodnoceni: $scope.kvalifikacebewertung,
		    giltab:$scope.kvalifikaceGiltAb,
		    persnr: $scope.ma.maInfo.PersNr,
		}
	).then(function (response) {
	    if (response.data.insertId > 0 || response.data.delRows > 0) {
		//neco vlozeno , aktualizuju pole
		getPersKvalifikace();
	    }

	});
    }
    /**
     * 
     * @returns {unresolved}
     */
    $scope.fillOsobniHodnoceni = function () {
	console.log('fillOsobniHodnoceni');
	$scope.fillOHButtonDisabled = true;
	$scope.lockOHButtonDisabled = true;
	return	$http.post(
		'./fillOsobniHodnoceni.php',
		{
		    jenma: $scope.jenma,
		    austritt60: $scope.austritt60,
		    oeselected: $scope.oes.oeSelected,
		    jahr: $scope.osobniHodnoceniJahr,
		    monat: $scope.osobniHodnoceniMonat
		}
	).then(function (response) {
	    getMAInfo($scope.ma.maInfo.PersNr);
	    $scope.fillOHButtonDisabled = false;
	    $scope.lockOHButtonDisabled = false;
	});
    }
    /**
     * 
     * @returns {undefined}
     */
    $scope.fillHFPremie = function () {
	console.log('fillHFPremie');
	$scope.fillHFButtonDisabled = true;
	$scope.lockHFButtonDisabled = true;
	return	$http.post(
		'./fillHFPremie.php',
		{
		    jenma: $scope.jenma,
		    austritt60: $scope.austritt60,
		    oeselected: $scope.oes.oeSelected,
		    jahr: $scope.hfPremieJahr,
		    monat: $scope.hfPremieMonat
		}
	).then(function (response) {
	    getMAInfo($scope.ma.maInfo.PersNr);
	    $scope.fillHFButtonDisabled = false;
	    $scope.lockHFButtonDisabled = false;
	});
    }

    $scope.lockHFPremie = function (lockvalue) {
	console.log('fillHFPremie');
	$scope.lockHFButtonDisabled = true;
	$scope.fillHFButtonDisabled = true;
	return	$http.post(
		'./fillHFPremie.php',
		{
		    jenma: $scope.jenma,
		    austritt60: $scope.austritt60,
		    oeselected: $scope.oes.oeSelected,
		    jahr: $scope.hfPremieJahr,
		    monat: $scope.hfPremieMonat,
		    lock: true,
		    lockvalue: lockvalue
		}
	).then(function (response) {
	    getMAInfo($scope.ma.maInfo.PersNr);
	    $scope.lockHFButtonDisabled = false;
	    $scope.fillHFButtonDisabled = false;
	});
    }

    /**
     * 
     * @param {type} lockvalue
     * @returns {undefined}
     */
    $scope.lockOsobniHodnoceniAll = function (lockvalue) {
	console.log('fillOsobniHodnoceni');
	$scope.fillOHButtonDisabled = true;
	$scope.lockOHButtonDisabled = true;
	return	$http.post(
		'./fillOsobniHodnoceni.php',
		{
		    jenma: $scope.jenma,
		    austritt60: $scope.austritt60,
		    oeselected: $scope.oes.oeSelected,
		    jahr: $scope.osobniHodnoceniJahr,
		    monat: $scope.osobniHodnoceniMonat,
		    lock: true,
		    lockvalue: lockvalue
		}
	).then(function (response) {
	    getMAInfo($scope.ma.maInfo.PersNr);
	    $scope.fillOHButtonDisabled = false;
	    $scope.lockOHButtonDisabled = false;
	});
    }
    /**
     * 
     * @param {type} p
     * @returns {undefined}
     */
    $scope.unlockHfPremie = function (p, monat) {
	console.log(p);
	p.locked = false;
	premie = p;
	return	$http.post(
		'./updateSkutPremie.php',
		{
		    persnr: $scope.ma.maInfo.PersNr,
		    premie: premie,
		    jm: monat,
		    lockchanged: true
		}
	).then(function (response) {
	    if (response.data.insertid > 0) {
		//upravit id z 0 na skutecne id pro dany mesic a persnr
		$scope.hfPremieArray[$scope.ma.maInfo.PersNr].monate[monat].skutId = response.data.insertid;
	    }
	    $scope.hfPremieArray[$scope.ma.maInfo.PersNr].monate[monat].last_edit = response.data.u;
	});
    }
    /**
     * 
     * @param {type} p
     * @returns {undefined}
     */
    $scope.lockHfPremie = function (p, monat) {
	console.log(p);
	p.locked = true;
	premie = p;
	return	$http.post(
		'./updateSkutPremie.php',
		{
		    persnr: $scope.ma.maInfo.PersNr,
		    premie: premie,
		    jm: monat,
		    lockchanged: true
		}
	).then(function (response) {
	    if (response.data.insertid > 0) {
		//upravit id z 0 na skutecne id pro dany mesic a persnr
		$scope.hfPremieArray[$scope.ma.maInfo.PersNr].monate[monat].skutId = response.data.insertid;
	    }
	    $scope.hfPremieArray[$scope.ma.maInfo.PersNr].monate[monat].last_edit = response.data.u;
	});
    }

    /**
     * 
     * @param {type} p
     * @param {type} monat
     * @returns {unresolved}
     */
    $scope.lockOsobniHodnoceni = function (oh, monat) {
	console.log(oh);
	oh.locked = true;
	return	$http.post(
		'./updateOsobniHodnoceni.php',
		{
		    oh: oh,
		    jm: monat,
		    lockchanged: true
		}
	).then(function (response) {
	    $scope.osobniHodnoceniArray.hodnoceni[response.data.oh.id_faktor][response.data.jm].last_edit = response.data.u;
	});
    }

/**
 * 
 * @param {type} field
 * @returns {suma|Number}
 */
    $scope.sumHFPremieRow = function (pole,field) {
	suma = 0;
	//console.log('sumHFPremieRow: field:'+field);
	//console.log(pole);
	if(pole!==undefined){
	    for(p in pole){
		suma += parseFloat(pole[p][field]);
		//console.log(pole[p]);
	    }
	}
	//console.log($scope.hfPremieArray);
//	console.log($scope.ma);
//	if ($scope.hfPremieArray !== null && $scope.ma.maInfo!==null) {
//	    for (p in $scope.hfPremieArray[$scope.ma.maInfo.PersNr].monate) {
//		suma += parseInt(p[field]);
//	    }
//	}

	return suma;
    }
    /**
     * 
     * @param {type} prop
     * @param {type} jm
     * @returns {undefined}
     */
    $scope.sumaOsobniHodnoceniMonat = function (p, jm) {
	var suma = 0;
	for (pr in $scope.osobniHodnoceniArray.hodnoceni) {
	    //console.log(pr);
	    if (pr !== "osobniFaktory") {
		//console.log($scope.osobniHodnoceniArray.hodnoceni[pr][jm].hodnoceni_osobni[p]);
		suma += parseFloat($scope.osobniHodnoceniArray.hodnoceni[pr][jm].hodnoceni_osobni[p]);
	    }
	}
	return suma;
    }
    /**
     * 
     * @param {type} oh
     * @param {type} monat
     * @returns {unresolved}
     */
    $scope.unlockOsobniHodnoceni = function (oh, monat) {
	console.log(oh);
	oh.locked = false;
	return	$http.post(
		'./updateOsobniHodnoceni.php',
		{
		    oh: oh,
		    jm: monat,
		    lockchanged: true
		}
	).then(function (response) {
	    $scope.osobniHodnoceniArray.hodnoceni[response.data.oh.id_faktor][response.data.jm].last_edit = response.data.u;
	});
    }

    /**
     * 
     * @returns {undefined}
     */
    $scope.toggleHromadneOperace = function () {
	$scope.showHromadneOperace = $scope.showHromadneOperace == true ? false : true;
    }

    /**
     * 
     * @param {type} premie
     * @param {type} monat
     * @returns {unresolved}
     */
    $scope.skutPremieChanged = function (premie, monat) {
	console.log('skutPremieChanged');
	console.log(premie);
	console.log(monat);
	console.log($scope.ma.maInfo.PersNr);
	return	$http.post(
		'./updateSkutPremie.php',
		{
		    persnr: $scope.ma.maInfo.PersNr,
		    premie: premie,
		    jm: monat
		}
	).then(function (response) {
	    if (response.data.insertid > 0) {
		//upravit id z 0 na skutecne id pro dany mesic a persnr
		$scope.hfPremieArray[$scope.ma.maInfo.PersNr].monate[monat].skutId = response.data.insertid;
	    }
	    $scope.hfPremieArray[$scope.ma.maInfo.PersNr].monate[monat].last_edit = response.data.u;
	});
    }

/**
 * 
 * @param {type} e
 * @param {type} p
 * @returns {undefined}
 */
$scope.commentClicked = function(e,p){
    console.log('comment clicked ');
    var eId = e.target.id;
    console.log('eId='+eId);
	//zlikvidovat popovery
	if($('div[id^=popover]').length>0){
	    $('div[id^=popover]').popover('destroy');
	    return;
	}
	var content = '<p>'+p.bemerkung+'</p>';
	    var popOptions = {
		container:'body',
		content:content,
		html:true,
		placement:'bottom',
		title:'poznámka',
		trigger:'manual',
	    };
	    $('#'+eId).popover(popOptions);
	    $('#'+eId).popover('show');
}
/**
 * 
 * @param {type} e
 * @param {type} r
 * @returns {undefined}
 */
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
/**
 * 
 * @param {type} $event
 * @returns {undefined}
 */
    $scope.formKeyDown = function($event){
	console.log($event);
    }
    /**
     * 
     * @param {type} panelid
     * @returns {undefined}
     */
    $scope.panelSwitch = function (panelid) {
	console.log(panelid);
	for (var prop in $scope.showPanel) {
	    if( $scope.showPanel.hasOwnProperty( prop ) ) {
		$scope.showPanel[prop] = false;
	    } 
	}
	$scope.showPanel[panelid] = true;
    }
    /**
     * 
     * @param {type} oh
     * @returns {undefined}
     */
    $scope.osobniHodnoceniChanged = function (oh) {
	console.log(oh);
	return	$http.post(
		'./updateOsobniHodnoceni.php',
		{
		    oh: oh,
		}
	).then(function (response) {
	    if (response.data.ar > 0) {
		// podarilo se updatovat
		$scope.osobniHodnoceniArray.hodnoceni[response.data.oh.id_faktor][response.data.jm].hodnoceni_osobni.castka = response.data.castka;
	    }
	    //$scope.hfPremieArray[$scope.ma.maInfo.PersNr].monate[monat].last_edit = response.data.u;
	});
    }

    /**
     * 
     * @param {type} grenze
     * @returns {undefined}
     */
    $scope.oshodDatumChanged = function (grenze) {
	if (grenze == 'von') {
	    //nastavim na prvni den mesice
	    $scope.osobniHodnoceniVon = new Date($scope.osobniHodnoceniVon.getFullYear(), $scope.osobniHodnoceniVon.getMonth(), 1);
	}
	if (grenze == 'bis') {
	    //nastavim na posledni den mesice
	    $scope.osobniHodnoceniBis = new Date($scope.osobniHodnoceniBis.getFullYear(), $scope.osobniHodnoceniBis.getMonth() + 1, 0);
	}
	getOsobniHodnoceni();
    }
    
    /**
     * 
     * @param {type} t
     * @param {type} rid
     * @returns {unresolved}
     */
    $scope.bemerkungChanged = function (t, rid,p) {
	console.log('bemerkung Changed t:' + t + ',rid:' + rid);
	p.bemerkung = $scope.bemerkung[t][rid].poznamka;
	if (rid > 0) {
	    return	$http.post(
		    './bemerkungChanged.php',
		    {
			t: t,
			rid: rid,
			value: $scope.bemerkung[t][rid].poznamka
		    }
	    ).then(function (response) {
		//$scope.hfPremieArray = response.data.hfpremiearray;
	    });
	}
    }
    /**
     * 
     * @param {type} table
     * @param {type} rowid
     * @returns {undefined}
     */
    $scope.updateBemerkung = function (t, rid) {

	if (parseInt(rid) > 0) {
	    //console.log("table:"+t+", rowid:"+rid);
	    //console.log('show = '+$scope.bemerkung[table][rowid].show);
	    if (!$scope.bemerkung.hasOwnProperty(t)) {
		console.log('nema ownproperty table ' + t);
		$scope.bemerkung[t] = {};
		$scope.bemerkung[t][rid] = {};
	    }
	    else {
		if (!$scope.bemerkung[t].hasOwnProperty(rid)) {
		    console.log('nema ownproperty rowid ' + rid);
		    $scope.bemerkung[t][rid] = {};
		}
	    }

	    if ($scope.bemerkung[t][rid].show !== true) {
		console.log('nastavuji show true');
		//zkusit natahnout obsah z db
		$http.post(
			'./getBemerkungValue.php',
			{
			    t: t,
			    rid: rid
			}
		).then(function (response) {
		    $scope.bemerkung[t][rid].poznamka = response.data.poznamka;
		    $scope.bemerkung[t][rid].show = true;
		});
	    }
	    else {
		console.log('nastavuji show false');
		$scope.bemerkung[t][rid].show = false;
	    }
	}
	else {
	    console.log('bunka jeste nema svuj radek v tabulce');
	    console.log('nejprve je potreba insert hodnoty');
	}
    }
    
    /**
     * 
     * @param {type} grenze
     * @returns {undefined}
     */
    $scope.premieDatumChanged = function (grenze) {
	if (grenze == 'von') {
	    //nastavim na prvni den mesice
	    $scope.hfPremieVon = new Date($scope.hfPremieVon.getFullYear(), $scope.hfPremieVon.getMonth(), 1);
	}
	if (grenze == 'bis') {
	    //nastavim na posledni den mesice
	    $scope.hfPremieBis = new Date($scope.hfPremieBis.getFullYear(), $scope.hfPremieBis.getMonth() + 1, 0);
	}
	getHFPremie();

    }
    /**
     * 
     * @returns {undefined}
     */
    function getHFPremie() {
	//hf premie ----------------------------------------------------
	if ($scope.ma.maInfo !== null) {
	    return	$http.post(
		    './getHFPremie.php',
		    {
			persnr: $scope.ma.maInfo.PersNr,
			von: $scope.hfPremieVon,
			bis: $scope.hfPremieBis
		    }
	    ).then(function (response) {
		$scope.hfPremieArray = response.data.hfpremiearray;
	    });
	}

    }


    /**
     * 
     * @returns {unresolved}
     */
    function getPersKvalifikace() {
	if ($scope.ma.maInfo !== null) {
	    return	$http.post(
		    './getPersKvalifikace.php',
		    {
			persnr: $scope.ma.maInfo.PersNr
		    }
	    ).then(function (response) {
		$scope.persKvalifikaceArray = response.data.persKvalifikaceArray;
		$scope.oekvalifikace = $scope.ma.oeInfo.oe;//$scope.oes.oeArray[0];
		$scope.kvalifikacebewertung = 6;
	    });
	}

    }
    /**
     * 
     * @returns {unresolved}
     */
    function getPersInventar() {
	if ($scope.ma.maInfo !== null) {
	    return	$http.post(
		    './getPersInventar.php',
		    {
			persnr: $scope.ma.maInfo.PersNr
		    }
	    ).then(function (response) {
		if (response.data.persInventarArray !== null && response.data.persInventarArray.length > 0) {
		    for (var i = 0; i < response.data.persInventarArray.length; i++) {
			response.data.persInventarArray[i]['vydej_datum1'] = convertMysql2Date(response.data.persInventarArray[i]['vydej_datum']);
			response.data.persInventarArray[i]['vraceno_datum1'] = convertMysql2Date(response.data.persInventarArray[i]['vraceno_datum']);
		    }
		}

		$scope.persInventarArray = response.data.persInventarArray;

	    });
	}

    }

    /**
     * 
     */
    function getOsobniHodnoceni() {
	//hf premie ----------------------------------------------------
	if ($scope.ma.maInfo !== null) {
	    return	$http.post(
		    './getOsobniHodnoceni.php',
		    {
			persnr: $scope.ma.maInfo.PersNr,
			von: $scope.osobniHodnoceniVon,
			bis: $scope.osobniHodnoceniBis
		    }
	    ).then(function (response) {
		$scope.osobniHodnoceniArray = response.data.osobniHodnoceniArray;
	    });
	}

    }
    /**
     * 
     * @param {type} persnr
     * @returns {undefined}
     */
    function getMAInfo(persnr, direction) {
	if ($scope.oes.oeSelected === null) {
	    $scope.oes.oeSelected = '*';
	}

	// zakladni informace
	$http.post(
		'./getMAInfo.php',
		{
		    persnr: persnr,
		    direction: direction,
		    jenma: $scope.jenma,
		    austritt60: $scope.austritt60,
		    oeselected: $scope.oes.oeSelected,
		    statusarray:    $scope.filt.dstatus,
		    oearray:    $scope.filt.oearray
		}
	).then(function (response) {
	    if (response.data.ma !== null) {
		$scope.ma.maInfo = response.data.ma[0];
		$scope.ma.bewerberInfo = response.data.bewerber[0];
		$scope.ma.oeInfo = response.data.oeinfo;
		// dodatecne informace
		getHFPremie();
		getOsobniHodnoceni();
		getPersInventar();
		getPersKvalifikace();
		
		//zrusit popovery
		if($('div[id^=popover]').length>0){
		    $('div[id^=popover]').popover('destroy');
		}
		
		//vynulovat poznamky
		$scope.bemerkung = {};
	    }
	});


    }

    /**
     * 
     * @param {type} direction
     * @returns {undefined}
     */
    $scope.moveMA = function (direction) {
	$scope.ma.selectedIndex = 0;
	getMAInfo($scope.ma.maInfo.PersNr, direction);
    }
    /**
     * 
     * @returns {undefined}
     */
    $scope.getfirstActiveMA = function () {
	$scope.ma.selectedIndex = 0;
	getMAInfo(0, 0);
    }

    /**
     * 
     * @param {type} i
     * @returns {unresolved}
     */
    $scope.listRowClicked = function (i) {
	console.log('listRowClicked ' + i);
	$scope.ma.selectedIndex = i;
	getMAInfo($scope.osoby[i].persnr, 0);
    }

    /**
     * 
     * @returns {undefined}
     */
    $scope.austritt60Changed = function () {
	$scope.osobaChanged();
    }

    /**
     * 
     * @returns {undefined}
     */
    $scope.jenMAChanged = function () {
	$scope.osobaChanged();
    }
    /**
     * 
     * @returns {unresolved}
     */

    $scope.oeChanged = function () {
	if ($scope.ma.selectedIndex < 0) {
	    $scope.osobaChanged();
	}
    }
    
    $scope.oeArrayChanged = function(){
	if ($scope.ma.selectedIndex < 0) {
	    $scope.osobaChanged();
	}
    }

    /**
     * 
     * @returns {unresolved}
     */
    $scope.osobaChanged = function () {
	console.log('osobaChanged');
	$scope.ma.selectedIndex = -1;
	return $http.post(
		'./getPersInfo.php',
		{
		    osoba: $scope.osoba,
		    jenma: $scope.jenma,
		    austritt60: $scope.austritt60,
		    oeselected: $scope.oes.oeSelected,
		    statusarray:    $scope.filt.dstatus,
		    oearray:    $scope.filt.oearray,
		}
	).then(function (response) {
	    $scope.osoby = response.data.osoby;
	});
    }

    /**
     * 
     * @param {type} e
     * @returns {unresolved}
     */
    $scope.refreshInventar = function (e) {
	var params = {e: e};
	return $http.get(
		'./getInventar.php',
		{params: params}
	).then(function (response) {
	    $scope.inventarArray = response.data.inventarArray;
	});
    };

    /**
     * 
     * @returns {unresolved}
     */
    $scope.initSecurity = function () {
	var p = {
	    form_id: 'persjs'
	};
	return $http.post('../getSecurityInfo.php', p).then(
		function (response) {
		    $scope.securityInfo = response.data.securityInfo;
		    //zkusim najit roli helptexteditor
		    $scope.securityInfo.roles.forEach(function (v) {
			if (v.rolename == 'helptexteditor') {
			    $scope.isEditor = true;
			    console.log('is helptexteditor');
			}
			if (v.rolename == 'admin') {
			    $scope.isAdmin = true;
			    console.log('is admin');
			}
		    });
		}
	);
    }

    /**
     * 
     * @returns {unresolved}
     */
    $scope.initHelp = function () {
	var p = {
	    form_id: 'persjs'
	};
	return $http.post('../getHelpInfo.php', p).then(
		function (response) {
		    $scope.helpText = response.data.help.helpText;
		    $scope.hIArray = response.data.help.hiArray;
		}
	);
    }
    /**
     * 
     */
    $scope.initLists = function () {
	return $http.post(
		'./getLists.php',
		{}
	).then(function (response) {
	    //console.log(response.data);
	    $scope.bewFahigkeiten = response.data.bewFahigkeiten;
	    $scope.infoVomArray = response.data.infoVomArray;
	    $scope.dpersstatuses = response.data.dpersstatuses;
	    $scope.status_fur_aby = response.data.status_fur_aby;
	    $scope.oes.oeArray = response.data.oeArray;
	    $scope.oes.oeSelected = response.data.oeSelected;
	    $scope.fahtypen.fahtypenArray = response.data.fahtypenArray;
	    $scope.fahtypen.fahtypidSelected = response.data.fahtypidSelected;
	    $scope.fahigkeiten.fahigkeitenArray = response.data.fahigkeitenArray;
	    if ($scope.fahigkeiten.fahigkeitenArray !== null) {
		$scope.fahigkeiten.fahigkeitenidSelected = $scope.fahigkeiten.fahigkeitenArray[0].id;
	    }
	});
    }
    // init
    $scope.initSecurity();
    $scope.initLists();
    $scope.initHelp();
    $scope.getfirstActiveMA();


    var such = $window.document.getElementById('osoba');
    if (such) {
	such.focus();
	such.select();
    }

});

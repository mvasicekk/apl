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


aplApp.controller('persController', function ($scope, $routeParams, $http, $timeout, $window, $location, $sanitize,Upload) {
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

    $scope.lohnArray = null;
    var curdate = new Date();

    $scope.hfPremieVon = new Date(curdate.getFullYear(), 0, 1);
    $scope.hfPremieBis = new Date(curdate.getFullYear(), curdate.getMonth() + 1, 0);
    $scope.kvalifikaceGiltAb = new Date();

    $scope.osobniHodnoceniVon = new Date(curdate.getFullYear(), 0, 1);
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
    
    $scope.lohnJahr = parseInt(curdate.getFullYear());
    $scope.lohnMonat = parseInt(curdate.getMonth()+1);
    
    $scope.fillOHButtonDisabled = false;
    $scope.lockOHButtonDisabled = false;
    $scope.persnrExists = false;

    $scope.inventar = {};
    $scope.inventarArray = [];

    $scope.persInventarArray = [];
    $scope.addparents = false;

    $scope.persKvalifikaceArray = [];
    $scope.persIdentifikatoryArray = [];
    $scope.identifikatorvydano = curdate;
    
    $scope.showPanel = {
	grundinfo:true,
	kvalifikace:false,
	inventar:false,
	hfpremie:false,
	osobnihodnoceni:false,
	lohnberechnung:false,
	identifikatory:false,
	lohninfo:false,
	dokumenty:false
    };
    
    $scope.bemerkung = {};
    
    $scope.filt = {
	dstatus : ["MA","DOHODA"],
	oearray : ["*"]
    };

    $scope.dpersstatuses = [];
    $scope.status_fur_aby = [];
    
    $scope.hodnoceniArray = ["1","2","3","4","5","6","7","8","9","10"];
    
    $scope.bewFahigkeiten = [];
    
    $scope.dokumentyArray = [];


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
     * test zda muze bzt zadani osobnim cislem
     * @param {type} osoba
     * @returns {Boolean}
     */
    $scope.isPersNr = function(osoba){
	if(parseInt(osoba)>0){
	    return true;
	}
	else{
	    return false;
	}
    }
    
    /**
     * 
     * @param {type} $item
     * @param {type} $model
     * @returns {undefined}
     */
    $scope.statusSelectAction = function($item,$model){
	//console.log($item);
	//console.log($model);
	$scope.filt.dstatus.push($item);
    }
    
    /**
     * 
     * @param {type} $item
     * @param {type} $model
     * @returns {undefined}
     */
    $scope.hodnoceniOESelectAction = function($item,$model,jm){
	console.log($item);
	console.log($model);
	console.log(jm);
	$http.post(
		    './createOsobniHodnoceni.php',
		    {
			persnr: $scope.ma.maInfo.PersNr,
			jm: jm,
			oe: $model
		    }
	    ).then(function (response) {
		getOsobniHodnoceni();
	    });
    }
    /**
     * 
     * @param {type} files
     * @param {type} errFiles
     * @param {type} b
     * @returns {undefined}
     */
    $scope.uploadFiles1 = function(files, errFiles,b) {
        $scope.files = files;
        $scope.errFiles = errFiles;
        angular.forEach(files, function(file) {
            file.upload = Upload.upload({
                url: './upload.php',
		data: {file: file, att:'foto',persnr:$scope.ma.maInfo.PersNr}
            });

            file.upload.then(function (response) {
                $timeout(function () {
                    file.result = response.data;
                });
            }, function (response) {
                if (response.status > 0)
                    $scope.errorMsg = response.status + ': ' + response.data;
            }, function (evt) {
                file.progress = Math.min(100, parseInt(100.0 * 
                                         evt.loaded / evt.total));
		//pokud bude progress = 100, odstranim file ze seznamu files
		if(file.progress==100){
		    var ind = $scope.files.findIndex(function(v){v.name==file.name});
		    $scope.files.splice(ind,1);
		    //pokud bude pole nulove, obnovim prehled souboru
		    if($scope.files.length==0){
			//$scope.anlagenButtonClicked(b);
		    }
		}
            });
        });
    }
    
    $scope.uploadMAFiles = function(files, errFiles,b) {
        $scope.files = files;
        $scope.errFiles = errFiles;
        angular.forEach(files, function(file) {
            file.upload = Upload.upload({
                url: './upload.php',
		data: {file: file, att:'dokument',persnr:$scope.ma.maInfo.PersNr}
            });

            file.upload.then(function (response) {
                $timeout(function () {
                    file.result = response.data;
                });
            }, function (response) {
                if (response.status > 0)
                    $scope.errorMsg = response.status + ': ' + response.data;
            }, function (evt) {
                file.progress = Math.min(100, parseInt(100.0 * 
                                         evt.loaded / evt.total));
		//pokud bude progress = 100, odstranim file ze seznamu files
		if(file.progress==100){
		    var ind = $scope.files.findIndex(function(v){v.name==file.name});
		    $scope.files.splice(ind,1);
		    //pokud bude pole nulove, obnovim prehled souboru
		    if($scope.files.length==0){
			refreshMAFiles();
		    }
		}
            });
        });
    }
    
    
    /**
     * pro prepocteni mzdy pri zmene MA,monat nebo jahr
     * 
     * vola privatni funkci = do budoucna, kdyby potreboval ridit parametrem
     * 
     * @returns {undefined}
     * 
     */
    $scope.updateLohn = function(){
	console.log($scope.lohnMonat);
	console.log($scope.lohnJahr);
	getPersLohn();
    }
    /**
     * 
     * @param {type} persnr
     * @returns {undefined}
     */
    $scope.addNewBewerber = function(persnr){
	$http.post(
		    './createNewMA.php',
		    {
			persnr: persnr
		    }
	    ).then(function (response) {
		if(response.data.persnr!==null){
		    //byl vytvoren novy MA, vytahnu informace
		    //jeste vhodne nastavit filtry
		    $scope.jenma = false;
		    $scope.austritt60 = false;
		    $scope.oes.oeSelected = "*";
		    $scope.filt.dstatus = ["BEWERBER"];
		    $scope.filt.oearray = ["*"];
		    $scope.ma.selectedIndex = 1;
		    $scope.panelSwitch('grundinfo');
		    getMAInfo(response.data.persnr,0);
		}
	    });
    }
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
	
	    if ($scope.filt.dstatus.length == 2) {
		if (
			(($scope.filt.dstatus[0] == 'MA') && ($scope.filt.dstatus[1] == 'DOHODA') )
			||
			(($scope.filt.dstatus[1] == 'MA') && ($scope.filt.dstatus[0] == 'DOHODA') )
		    ) {
			return true;
			}
		else{
		    return false;
		}
	    }
	    else if($scope.filt.dstatus.length == 1){
		    if (
			(($scope.filt.dstatus[0] == 'MA') || ($scope.filt.dstatus[0] == 'DOHODA') )
		    ) {
		    return true;
		}
		else{
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
    $scope.dpersFieldChanged = function(field){
	console.log('dpersFieldChanged: ' + field);
	
	if ($scope.ma.maInfo !== null) {
	    return	$http.post(
		    './updateDpersField.php',
		    {
			persnr: $scope.ma.maInfo.PersNr,
			value: $scope.ma.maInfo[field],
			field: field
		    }
	    ).then(function (response) {
		// pokud vyhodim a premii, nastavim automaticky a_praemie na 0
		if(field=='a_praemie'){
		    if($scope.ma.maInfo[field]=='0'){
			$scope.ma.maInfo['a_praemie_st'] = '0';
			$scope.dpersFieldChanged('a_praemie_st');
		    }
		}
	    });
	}
    }
    
    /**
     * 
     * @param {type} field
     * @returns {unresolved}
     */
    $scope.dpersdetailFieldChanged = function(field){
	console.log('dpersdetailFieldChanged: ' + field);
	if ($scope.ma.maInfo !== null) {
	    return	$http.post(
		    './updateDpersDetailField.php',
		    {
			persnr: $scope.ma.maInfo.PersNr,
			value: $scope.ma.dpersDetail[field],
			field: field
		    }
	    ).then(function (response) {
	    });
	}
    }
    
    $scope.bewerberFieldOnSelect = function(i,m,field,isArray=false){
	console.log('bewerberFieldOnSelect');
	console.log('item');
	console.log(i);
	console.log('model');
	console.log(m);
	console.log('field = '+field);
	if(isArray===true){
	    if($scope.ma.bewerberInfo[field]===null){
		$scope.ma.bewerberInfo[field] = [];
	    }
	    $scope.ma.bewerberInfo[field].push(m);
	}
	else{
	    $scope.ma.bewerberInfo[field] = m;
	}
	
	console.log($scope.ma.bewerberInfo[field]);
	$scope.bewerberFieldChanged(field);
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
    
    $scope.dpersidentifikatoryChanged = function (pa, field) {
	console.log(field);
	console.log(pa);
	return	$http.post(
		'./updateDpersIdentifikatory.php',
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
 * @param {type} k
 * @returns {unresolved}
 */
    $scope.addIdentifikator = function (k) {
	return	$http.post(
		'./addIdentifikator.php',
		{
		    k: k,
		    oe: $scope.oeidentifikator,
		    kunde: $scope.kundeidentifikator,
		    identifikator: $scope.identifikator,
		    vydano:$scope.identifikatorvydano,
		    poznamka:$scope.identifikatorpoznamka,
		    persnr: $scope.ma.maInfo.PersNr,
		}
	).then(function (response) {
	    if (response.data.insertId > 0 || response.data.delRows > 0) {
		//neco vlozeno , aktualizuju pole
		getPersIdentifikatory();
		$scope.updateIdentArray('kunde');
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
		    poznamka: $scope.kvalifikacepoznamka,
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
	if ($scope.osobniHodnoceniArray !== null) {
	    for (pr in $scope.osobniHodnoceniArray.hodnoceni) {
		//console.log(pr);
		if (pr !== "osobniFaktory") {
		    //console.log($scope.osobniHodnoceniArray.hodnoceni[pr][jm].hodnoceni_osobni[p]);
		    if($scope.osobniHodnoceniArray.hodnoceni[pr][jm].hodnoceni_osobni.rowexists===true){
			suma += parseFloat($scope.osobniHodnoceniArray.hodnoceni[pr][jm].hodnoceni_osobni[p]);
		    }
		}
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
    
    $scope.checkExtraPassword = function(){
	console.log('checkExtraPassword, kontroluji panel '+$scope.checkedPanelId + ', resourceid=' + $scope.checkedResourceId);
	$http.post(
		'./resourceSecurity.php',
		{
		    panelid: $scope.checkedPanelId,
		    resourceid:$scope.checkedResourceId,
		    pass:$scope.extraPassword
		}
	    ).then(function (response) {
		if(response.data.allow===true){
		    // muzu prepnout panel, uz nebudu kontrolovat heslo, dam resourceid = 0 ( bez parametru )
		    $scope.panelSwitch(response.data.panelid);
		}
	    });
	$('#extra_password_modal').modal('hide');
    }
    /**
     * 
     * @param {type} panelid
     * @returns {undefined}
     */
    $scope.panelSwitch = function (panelid,resourceid=0) {
	console.log(panelid);
	
	for (var prop in $scope.showPanel) {
	    if( $scope.showPanel.hasOwnProperty( prop ) ) {
		if($scope.showPanel[prop]==true){
		    //zapamatovat puvodni
		    oldpanel = prop;
		}
		$scope.showPanel[prop] = false;
	    } 
	}
	
	//kontrola extra security
	if(resourceid>0){
	    $scope.checkedPanelId = panelid;
	    $scope.checkedResourceId = resourceid;
	    $scope.extraPassword='';
	    
	    // zobrazit modal dialog s vyzvou k zadani dodatecneho hesla
	    $('#extra_password_modal').modal();
	    console.log('ukoncen modal');
	    $scope.showPanel[oldpanel] = true;
	}
	else{
	    $scope.showPanel[panelid] = true;
	}
	
	
	//pri zvoleni lohnberechnung nacist pole s udaji
	if(panelid=='lohnberechnung'){
	    console.log('volam getPersLohn');
	    getPersLohn();
	}
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
	    if($scope.osobniHodnoceniVon!=undefined){
		$scope.osobniHodnoceniVon = new Date($scope.osobniHodnoceniVon.getFullYear(), $scope.osobniHodnoceniVon.getMonth(), 1);
	    }
	}
	if (grenze == 'bis') {
	    //nastavim na posledni den mesice
	    if($scope.osobniHodnoceniBis!=undefined){
		$scope.osobniHodnoceniBis = new Date($scope.osobniHodnoceniBis.getFullYear(), $scope.osobniHodnoceniBis.getMonth() + 1, 0);
	    }
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
    function getPersLohn(){
	$scope.lohnArray = null;
	if ($scope.ma.maInfo !== null) {
	    return $http.post(
		    '../utils/getlohnJson.php',
		    {
			persvon: $scope.ma.maInfo.PersNr,
			persbis: $scope.ma.maInfo.PersNr,
			jahr: $scope.lohnJahr,
			monat: $scope.lohnMonat
		    }
	    ).then(function (response) {
		$scope.lohnArray = response.data.personen[$scope.ma.maInfo.PersNr];
		$scope.lohnParams = response.data.params;
		//$scope.showPanel['lohnberechnung'] = true;
	    });
	}
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
    
    function getPersIdentifikatory() {
	if ($scope.ma.maInfo !== null) {
	    return	$http.post(
		    './getPersIdentifikatory.php',
		    {
			persnr: $scope.ma.maInfo.PersNr
		    }
	    ).then(function (response) {
		$scope.persIdentifikatoryArray = response.data.persIdentifikatoryArray;
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
		$scope.hasOEHodnoceni = response.data.hasOEHodnoceni;
		$scope.osobniHodnoceniArray = response.data.osobniHodnoceniArray;
		$scope.osobniHodnoceniJMArray = response.data.jahrmonatarray;
		$scope.osobniHodnoceniKoeficientArray = response.data.osobniHodnoceniKoeficientArray;
		$scope.osobniHodnoceniOeSelectArray = response.data.oeSelectArray;
	    });
	}

    }
    
    /**
     * 
     * @returns {undefined}
     */
    function getTemplateVariables(){
	$http.post(
		'./getTemplateVariables.php',
		{
		    persnr: $scope.ma.maInfo.PersNr,
		}
	).then(function (response) {
	    $scope.templateVariables = response.data.variables;
	});
    }
    
    /**
     * 
     * @returns {undefined}
     */
    function refreshMAFiles(){
	$http.post(
		'./getMAInfo.php',
		{
		    persnr: $scope.ma.maInfo.PersNr,
		    //direction: direction,
		    jenma: $scope.jenma,
		    austritt60: $scope.austritt60,
		    oeselected: $scope.oes.oeSelected,
		    statusarray:    $scope.filt.dstatus,
		    oearray:    $scope.filt.oearray
		}
	).then(function (response) {
	    if (response.data.ma !== null) {
		
		$scope.maDocPath = response.data.maDocPath;
		$scope.sablonyPath = response.data.sablonyPath;

		if(response.data.dokumentyArray!==null){
		    $scope.dokumentyArray = response.data.dokumentyArray;
		}
		else{
		    $scope.dokumentyArray = [];
		}
	    }
	});
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
		if(response.data.dpersdetail!==null){
		    $scope.ma.dpersDetail = response.data.dpersdetail[0];
		}
		
		$scope.maDocPath = response.data.maDocPath;
		$scope.sablonyPath = response.data.sablonyPath;

		if(response.data.dokumentyArray!==null){
		    $scope.dokumentyArray = response.data.dokumentyArray;
		}
		else{
		    $scope.dokumentyArray = [];
		}
		
		if(response.data.sablonyArray!==null){
		    $scope.sablonyArray = response.data.sablonyArray;
		}
		else{
		    $scope.sablonyArray = [];
		}
		
		if(response.data.attArray.docsArray!==null){
		    $scope.ma.maFotoUrl = response.data.attArray.docsArray[0].thumburl;
		}
		else{
		    $scope.ma.maFotoUrl = null;
		}
		console.log($scope.ma.maFotoUrl);
		$scope.ma.oeInfo = response.data.oeinfo;
		// dodatecne informace
		getHFPremie();
		getOsobniHodnoceni();
		getPersInventar();
		getPersKvalifikace();
		getPersIdentifikatory();
		getTemplateVariables();

		//jen kdyz je panel zobrazen, protoze jinak to moc dlouho trva
		if($scope.showPanel.lohnberechnung===true){
		    getPersLohn();
		}
		//zrusit popovery
		if($('div[id^=popover]').length>0){
		    $('div[id^=popover]').popover('destroy');
		}
		
		//vynulovat poznamky
		$scope.bemerkung = {};
	    }
	});


    }

    $scope.sablona2Doku = function () {
	selectedDocs = $scope.sablonyArray.filter(function (item) {
	    return item.selected;
	});
	if (selectedDocs.length > 0) {
	    return $http.post(
		    './sablona2Doku.php',
		    {
			docs: selectedDocs,
			persnr: $scope.ma.maInfo.PersNr
		    }
	    ).then(function (response) {
		if (response.data.dokumentyArray !== null) {
		    $scope.dokumentyArray = response.data.dokumentyArray;
		} else {
		    $scope.dokumentyArray = [];
		}
	    });
	}
    };
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
	    $scope.persnrExists = response.data.persnrExists;
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
     * @returns {undefined}
     */
    $scope.updateIdentArray = function(contrl){
	console.log('updateIdentArray');
	return $http.post(
		'./updateIdentArray.php',
		{
		    oe:$scope.oeidentifikator,
		    kunde:$scope.kundeidentifikator,
		    ctrl:contrl
		}
	).then(function (response) {
	    console.log(response.data);
	    if(response.data.ctrl=='oe'){
		$scope.kundeIdentArray = response.data.kundeIdentArray;
		$scope.kundeIdentSelected = response.data.kundeIdentSelected;
		$scope.kundeidentifikator = $scope.kundeIdentSelected;
		$scope.identifikatorArray = response.data.identifikatorArray;
		$scope.identifikatorSelected = response.data.identifikatorSelected;
		$scope.identifikator = $scope.identifikatorSelected;
	    }
	    if(response.data.ctrl=='kunde'){
		$scope.identifikatorArray = response.data.identifikatorArray;
		$scope.identifikatorSelected = response.data.identifikatorSelected;
		$scope.identifikator = $scope.identifikatorSelected;
	    }
	});
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
	    $scope.staaten = response.data.staaten;
	    $scope.staats_gruppen = response.data.staats_gruppen;
	    $scope.bewFahigkeiten = response.data.bewFahigkeiten;
	    $scope.infoVomArray = response.data.infoVomArray;
	    $scope.dpersstatuses = response.data.dpersstatuses;
	    $scope.status_fur_aby = response.data.status_fur_aby;
	    $scope.oes.oeIdentArray = response.data.oeIdentArray;
	    $scope.oes.oeIdentSelected = response.data.oeIdentSelected;
	    $scope.oeidentifikator = $scope.oes.oeIdentSelected;
	    $scope.kundeIdentArray = response.data.kundeIdentArray;
	    $scope.kundeIdentSelected = response.data.kundeIdentSelected;
	    $scope.kundeidentifikator = $scope.kundeIdentSelected;
	    $scope.identifikatorArray = response.data.identifikatorArray;
	    $scope.identifikatorSelected = response.data.identifikatorSelected;
	    $scope.identifikator = $scope.identifikatorSelected;
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

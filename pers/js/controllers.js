/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

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
			//console.log('current='+current+' next=');
			//console.log(next);
                        next.focus();
			next.select();
                        e.preventDefault();
                    }
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
    $scope.ma = {
	selectedIndex:-1,
	maInfo:null,
	oeInfo:null
    };
    $scope.oes = {
	oeArray:null,
	oeSelected:'*'
    };

    var curdate = new Date();

    $scope.hfPremieVon = new Date(curdate.getFullYear(), curdate.getMonth(), 1);
    $scope.hfPremieBis = new Date(curdate.getFullYear(), curdate.getMonth() + 1, 0);
    
    $scope.osobniHodnoceniVon = new Date(curdate.getFullYear(), curdate.getMonth(), 1);
    $scope.osobniHodnoceniBis = new Date(curdate.getFullYear(), curdate.getMonth() + 1, 0);
    
    $scope.hfPremieArray = null;
    $scope.osobniHodnoceniArray = null;
    
    
    $scope.showHromadneOperace = false;
    $scope.hfPremieJahr = curdate.getFullYear();
    $scope.curJahr = curdate.getFullYear();
    $scope.hfPremieMonat = curdate.getMonth();
    $scope.fillHFButtonDisabled=false;
    $scope.lockHFButtonDisabled=false;
    
    $scope.osobniHodnoceniJahr = curdate.getFullYear();
    $scope.osobniHodnoceniMonat = curdate.getMonth();
    $scope.fillOHButtonDisabled=false;
    $scope.lockOHButtonDisabled=false;
    
    
    /**
     * 
     * @returns {unresolved}
     */
    $scope.fillOsobniHodnoceni = function(){
	console.log('fillOsobniHodnoceni');
	$scope.fillOHButtonDisabled = true;
	$scope.lockOHButtonDisabled = true;
	return	$http.post(
			'./fillOsobniHodnoceni.php',
			{
			    jenma:$scope.jenma,
			    oeselected:$scope.oes.oeSelected,
			    jahr:$scope.osobniHodnoceniJahr,
			    monat:$scope.osobniHodnoceniMonat
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
    $scope.fillHFPremie = function(){
	console.log('fillHFPremie');
	$scope.fillHFButtonDisabled = true;
	$scope.lockHFButtonDisabled = true;
	return	$http.post(
			'./fillHFPremie.php',
			{
			    jenma:$scope.jenma,
			    oeselected:$scope.oes.oeSelected,
			    jahr:$scope.hfPremieJahr,
			    monat:$scope.hfPremieMonat
			}
		).then(function (response) {
		    getMAInfo($scope.ma.maInfo.PersNr);
		    $scope.fillHFButtonDisabled = false;
		    $scope.lockHFButtonDisabled = false;
		});
    }
    
    $scope.lockHFPremie = function(lockvalue){
	console.log('fillHFPremie');
	$scope.lockHFButtonDisabled = true;
	$scope.fillHFButtonDisabled = true;
	return	$http.post(
			'./fillHFPremie.php',
			{
			    jenma:$scope.jenma,
			    oeselected:$scope.oes.oeSelected,
			    jahr:$scope.hfPremieJahr,
			    monat:$scope.hfPremieMonat,
			    lock:true,
			    lockvalue:lockvalue
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
    $scope.lockOsobniHodnoceniAll = function(lockvalue){
	console.log('fillOsobniHodnoceni');
	$scope.fillOHButtonDisabled = true;
	$scope.lockOHButtonDisabled = true;
	return	$http.post(
			'./fillOsobniHodnoceni.php',
			{
			    jenma:$scope.jenma,
			    oeselected:$scope.oes.oeSelected,
			    jahr:$scope.osobniHodnoceniJahr,
			    monat:$scope.osobniHodnoceniMonat,
			    lock:true,
			    lockvalue:lockvalue
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
    $scope.unlockHfPremie = function(p,monat){
	console.log(p);
	p.locked=false;
	premie=p;
	return	$http.post(
			'./updateSkutPremie.php',
			{
			    persnr: $scope.ma.maInfo.PersNr,
			    premie: premie,
			    jm: monat,
			    lockchanged:true
			}
		).then(function (response) {
		    if(response.data.insertid>0){
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
    $scope.lockHfPremie = function(p,monat){
	console.log(p);
	p.locked=true;
	premie=p;
	return	$http.post(
			'./updateSkutPremie.php',
			{
			    persnr: $scope.ma.maInfo.PersNr,
			    premie: premie,
			    jm: monat,
			    lockchanged:true
			}
		).then(function (response) {
		    if(response.data.insertid>0){
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
    $scope.lockOsobniHodnoceni = function(oh,monat){
	console.log(oh);
	oh.locked=true;
	return	$http.post(
			'./updateOsobniHodnoceni.php',
			{
			    oh: oh,
			    jm: monat,
			    lockchanged:true
			}
		).then(function (response) {
		    $scope.osobniHodnoceniArray.hodnoceni[response.data.oh.id_faktor][response.data.jm].last_edit = response.data.u;
		});
    }
    
    /**
     * 
     * @param {type} prop
     * @param {type} jm
     * @returns {undefined}
     */
    $scope.sumaOsobniHodnoceniMonat = function(p,jm){
	var suma =0;
	for(pr in $scope.osobniHodnoceniArray.hodnoceni){
	    //console.log(pr);
	    if(pr!=="osobniFaktory"){
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
    $scope.unlockOsobniHodnoceni = function(oh,monat){
	console.log(oh);
	oh.locked=false;
	return	$http.post(
			'./updateOsobniHodnoceni.php',
			{
			    oh: oh,
			    jm: monat,
			    lockchanged:true
			}
		).then(function (response) {
		    $scope.osobniHodnoceniArray.hodnoceni[response.data.oh.id_faktor][response.data.jm].last_edit = response.data.u;
		});
    }
    
    /**
     * 
     * @returns {undefined}
     */
    $scope.toggleHromadneOperace = function(){
	$scope.showHromadneOperace = $scope.showHromadneOperace==true?false:true;
    }
    
    /**
     * 
     * @param {type} premie
     * @param {type} monat
     * @returns {unresolved}
     */
    $scope.skutPremieChanged = function(premie,monat){
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
		    if(response.data.insertid>0){
			//upravit id z 0 na skutecne id pro dany mesic a persnr
			$scope.hfPremieArray[$scope.ma.maInfo.PersNr].monate[monat].skutId = response.data.insertid;
		    }
		    $scope.hfPremieArray[$scope.ma.maInfo.PersNr].monate[monat].last_edit = response.data.u;
		});
    }
    
    /**
     * 
     * @param {type} oh
     * @returns {undefined}
     */
    $scope.osobniHodnoceniChanged = function(oh){
	console.log(oh);
	return	$http.post(
			'./updateOsobniHodnoceni.php',
			{
			    oh: oh,
			}
		).then(function (response) {
		    if(response.data.ar>0){
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
    $scope.oshodDatumChanged = function(grenze){
	if(grenze=='von'){
	    //nastavim na prvni den mesice
	    $scope.osobniHodnoceniVon = new Date($scope.osobniHodnoceniVon.getFullYear(), $scope.osobniHodnoceniVon.getMonth(), 1);
	}
	if(grenze=='bis'){
	    //nastavim na posledni den mesice
	    $scope.osobniHodnoceniBis = new Date($scope.osobniHodnoceniBis.getFullYear(), $scope.osobniHodnoceniBis.getMonth() + 1, 0);
	}
	getOsobniHodnoceni();
    }
    /**
     * 
     * @param {type} grenze
     * @returns {undefined}
     */
    $scope.premieDatumChanged = function(grenze){
	if(grenze=='von'){
	    //nastavim na prvni den mesice
	    $scope.hfPremieVon = new Date($scope.hfPremieVon.getFullYear(), $scope.hfPremieVon.getMonth(), 1);
	}
	if(grenze=='bis'){
	    //nastavim na posledni den mesice
	    $scope.hfPremieBis = new Date($scope.hfPremieBis.getFullYear(), $scope.hfPremieBis.getMonth() + 1, 0);
	}
	getHFPremie();
    }
    /**
     * 
     * @returns {undefined}
     */
    function getHFPremie(){
	//hf premie ----------------------------------------------------
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
    
    /**
     * 
     */
    function getOsobniHodnoceni(){
	//hf premie ----------------------------------------------------
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
		    oeselected: $scope.oes.oeSelected
		}
	).then(function (response) {
	    if (response.data.ma !== null) {
		$scope.ma.maInfo = response.data.ma[0];
		$scope.ma.oeInfo = response.data.oeinfo;
		// dodatecne informace
		getHFPremie();
		getOsobniHodnoceni();
	    }
	});


    }
    
    /**
     * 
     * @param {type} direction
     * @returns {undefined}
     */
    $scope.moveMA = function(direction){
	$scope.ma.selectedIndex = 0;
	getMAInfo($scope.ma.maInfo.PersNr,direction);
    }
    /**
     * 
     * @returns {undefined}
     */
    $scope.getfirstActiveMA = function(){
	$scope.ma.selectedIndex = 0;
	getMAInfo(0,0);
    }
    
    /**
     * 
     * @param {type} i
     * @returns {unresolved}
     */
    $scope.listRowClicked = function(i){
	console.log('listRowClicked '+i);
	$scope.ma.selectedIndex = i;
	getMAInfo($scope.osoby[i].persnr,0);
    }
    /**
     * 
     * @returns {undefined}
     */
    $scope.jenMAChanged = function(){
	$scope.osobaChanged();
    }
    /**
     * 
     * @returns {unresolved}
     */
    
    $scope.oeChanged = function () {
	if($scope.ma.selectedIndex<0){
	    $scope.osobaChanged();
	}
    }
    
    $scope.osobaChanged = function () {
	console.log('osobaChanged');
	$scope.ma.selectedIndex = -1;
	return $http.post(
		'./getPersInfo.php',
		{
		    osoba: $scope.osoba,
		    jenma:$scope.jenma,
		    oeselected:$scope.oes.oeSelected
		}
	).then(function (response) {
	    $scope.osoby = response.data.osoby;
	});
    }

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
	    $scope.oes.oeArray = response.data.oeArray;
	    $scope.oes.oeSelected = response.data.oeSelected;
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

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var aplApp = angular.module('dambewApp');

aplApp.directive("enterfocus", function () {
        return {
            restrict: "A",
            link: function ($scope, elem, attrs) {
                var focusables = $(":tabbable");
		//jeste musim nektere objekty vyradit, napr. odkazy
		//console.log(focusables);
		//vyradim ty, ktere maji tabindex = -1
//		focusables = focusables.filter(function(v){
//		    console.log(v);
//		    if(v.tabIndex=='-1'){
//			return false;
//		    }
//		    else{
//			return true;
//		    }
//		});
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


aplApp.controller('kartyController', function ($scope, $routeParams,$http,$timeout,$window,$location,$sanitize) {
    $scope.isEditor = false;	//urcuje zda muze uzivatel editovat helptext
    $scope.tinyMceOptions = {
	inline:true,
	menubar:false
    };
    $scope.tinymceModel = "tady se da psat pomoci zabudovaneho editoru, zkus to !";
    $scope.dateOptions = {
		dateFormat: 'dd.mm.yy',
		firstDay: 1
    };
    $scope.showHelp = false;
    $scope.datePickerFormat = 'dd.MM.yyyy';
    $scope.securityInfo = undefined;
    
    $scope.amnrChanged = function(){
	console.log($scope.karta);
	return $http.post(
		'./getAmnrMatch.php',
		{suchen: $scope.karta}
	).then(function (response) {
	    //console.log(response.data);
	    $scope.kartyRows = response.data.karty;
	});
    }
    
    $scope.initHelp = function(){
	var p={
	    form_id:'dambew_karty'
	};
	return $http.post('../getHelpInfo.php',p).then(
		    function(response){
			$scope.helpText = response.data.help.helpText;
			$scope.hIArray = response.data.help.hiArray;
		    }
		);
    }
    
    $scope.initSecurity = function(){
	var p={
	    form_id:'dambew_karty'
	};
	return $http.post('../getSecurityInfo.php',p).then(
		    function(response){
			$scope.securityInfo = response.data.securityInfo;
			//zkusim najit roli helptexteditor
			$scope.securityInfo.roles.forEach(function(v){
			    if(v.rolename=='helptexteditor'){
				$scope.isEditor = true;
				console.log('is helptexteditor');
			    }
			});
		    }
		);
    }
    
    $scope.initSecurity();
    $scope.initHelp();
});

aplApp.controller('dambewController', function ($scope, $routeParams,$http,$timeout,$window,$location,$sanitize) {
    $scope.isEditor = false;	//urcuje zda muze uzivatel editovat helptext
    $scope.tinyMceOptions = {
	inline:true,
	menubar:false
    };
    $scope.tinymceModel = "tady se da psat pomoci zabudovaneho editoru, zkus to !";
    $scope.dateOptions = {
		dateFormat: 'dd.mm.yy',
		firstDay: 1
    };
    $scope.showHelp = false;
    $scope.datePickerFormat = 'dd.MM.yyyy';
    $scope.securityInfo = undefined;
    
    $scope.oe = {};
    $scope.amnr = '';
    $scope.ausgabe = 1;
    $scope.ruckgabe = 0;
    $scope.persSklady = [];
    $scope.amnrSklady = [];
    $scope.sklad = {};
    $scope.datum = new Date();
    $scope.insertedRows = [];

$scope.submitForm = function(){
    console.log('formsubmit');
    	return $http.post(
		'./saveDambew.php',
		{
		    datum:$scope.datum,
		    persnr:$scope.persnr,
		    oe:$scope.oe,
		    amnr:$scope.amnr,
		    sklad:$scope.sklad,
		    ausgabe:$scope.ausgabe,
		    ruckgabe:$scope.ruckgabe,
		    bemerkung:$scope.bemerkung
		}
	).then(function (response) {
	    console.log(response.data);
	    if(response.data.insertId>0){
		//rozsirim pole vlozenych zaznamu
		var insertItem = {
		    datum : response.data.datum,
		    persnr : response.data.persnr,
		    oe : response.data.oe,
		    amnr : response.data.amnr,
		    sklad : response.data.sklad,
		    ausgabe : response.data.ausgabe,
		    ruckgabe : response.data.ruckgabe,
		    bemerkung : response.data.bemerkung,
		    u : response.data.u
		};
		
		
		$scope.insertedRows.unshift(insertItem);
	    }
	    
    		// pripravit na dalsi zadani
		$scope.persinfo = {};
		$scope.persnr = '';
		$scope.oe.tat='';
		$scope.amnr = '';
		$scope.amnrinfo = {};
		$scope.amnrSklady = [];
		$scope.skladyArray = $scope.skladyArrayAll;
		$scope.sklad.cislo = $scope.skladyArray[0].cislo;
		$scope.ausgabe = 1;
		$scope.ruckgabe = 0;
		$scope.bemerkung = '';
		// a focus na osobni cislo
		var such = $window.document.getElementById('persnr');
		if (such) {
		    such.focus();
		    such.select();
		}


	});
}

    
    $scope.initSecurity = function(){
	var p={
	    form_id:'dambew'
	};
	return $http.post('../getSecurityInfo.php',p).then(
		    function(response){
			$scope.securityInfo = response.data.securityInfo;
			//zkusim najit roli helptexteditor
			$scope.securityInfo.roles.forEach(function(v){
			    if(v.rolename=='helptexteditor'){
				$scope.isEditor = true;
				console.log('is helptexteditor');
			    }
			});
		    }
		);
    }
    
    $scope.initHelp = function(){
	var p={
	    form_id:'dambew'
	};
	return $http.post('../getHelpInfo.php',p).then(
		    function(response){
			$scope.helpText = response.data.help.helpText;
			$scope.hIArray = response.data.help.hiArray;
		    }
		);
    }

    


    /**
     * 
     */
//    $scope.getTeilMatch = function () {
//	var params = {a: $scope.teil_search};
//	$scope.importe = null;
//	$scope.teilAktual = null;
//	$scope.importAktual = null;
//	return $http.post(
//		'../dkopf/getTeilMatch.php',
//		{params: params}
//	).then(function (response) {
//	    //console.log(response.data);
//	    $scope.teile = response.data.teile;
//	    
//	    if(($scope.teile===null) && ($scope.teil_search.length===10)){
//		$scope.createNew = true;
//	    }
//	    else{
//		$scope.createNew = false;
//		if(($scope.teile!==null) && ($scope.teile.length===1)){
//		    // pokud mi vyhovuje jen jeden dil, tak ho rovnou nastavim jako aktualni
//		    $scope.listRowClicked(0);
//		}
//	    }
//	});
//    }
    
    
    
    /**
     * 
     */
    $scope.initLists = function(){
	return $http.post(
		'./getLists.php',
		{}
	).then(function (response) {
	    //console.log(response.data);
	    $scope.oeArray = response.data.oeArray;
	    $scope.skladyArrayAll = response.data.skladyArray;
	    $scope.skladyArray = response.data.skladyArray;
	    if($scope.skladyArray.length>0){
		$scope.sklad.cislo = $scope.skladyArray[0].cislo;
	    }
	});
    }
    // init
    
    $scope.initSecurity();
    
    $scope.initLists();
    
    $scope.initHelp();
    
    /*
    $scope.$watch('hIArray',function(newValue,oldValue){
	if(oldValue!==undefined){
//	    console.log('hiarray changed');
//	    console.log(newValue);
//	    console.log(oldValue);
	    // projdu vsechny atributy a zjistim, kde se zmenil help text
	    for(p in newValue){
		//console.log(p);
		if(newValue[p][0].help_text!=oldValue[p][0].help_text){
		    //zde byla zmena v help textu
//		    console.log(newValue[p][0].id);
//		    console.log(newValue[p][0].help_text);
		    // a updatnout v DB
		    $http.post(
			'./updateHelpText.php',
			{id:newValue[p][0].id,helptext:newValue[p][0].help_text}
		    ).then(function (response) {
//			console.log(response.data);
		    });
		}
	    }
	}
    },
    true);
    */
    
    var such = $window.document.getElementById('persnr');
    if (such) {
	such.focus();
	such.select();
    }

    /**
     * 
     * @returns {undefined}
     */
    $scope.isFormValid = function(){
	if($scope.sklad.cislo!=undefined){
	    var cisloSkladu = $scope.sklad.cislo.length;
	}
	else{
	    var cisloSkladu = 0;
	}
	
	var valid = ($scope.persnr>0)
		&&($scope.amnr.length>0)
		&&(cisloSkladu>0)
		&&($scope.ausgabe!==null)
		&&($scope.ruckgabe!==null)
		&&(toString($scope.ausgabe).length>0)
		&&(toString($scope.ruckgabe).length>0);
	//console.log(valid);
	return valid;
	
    }
    
    /**
     * 
     * @returns {unresolved}
     */
    $scope.persnrChanged = function(){
	console.log('persnrChanged');
	return $http.post(
		'./getPersInfo.php',
		{persnr:$scope.persnr}
	).then(function (response) {
	    $scope.persinfo = response.data.persinfo;
	    $scope.persSklady = response.data.persSklady;
    
	    if($scope.persinfo===null){
		$scope.persnr = '';
	    }
	    else{
//		nastavim oe
		$scope.oe.tat=$scope.persinfo.regeloe;
	    }
	});
    }
    
    $scope.amnrChanged = function () {
	console.log('amnrChanged');
	return $http.post(
		'./getAmnrInfo.php',
		{amnr: $scope.amnr}
	).then(function (response) {
	    $scope.amnrinfo = response.data.amnrinfo;
	    $scope.amnrSklady = response.data.amnrSklady;
	    if ($scope.amnrinfo === null) {
		$scope.amnr = '';
		$scope.skladyArray = $scope.skladyArrayAll;
	    }
	    else {
//		upravit seznam skladu $scope.skladyArray, aby obsahoval jen sklady, ktere jsou obsazeny i v $scope.amnrSklady
		$scope.skladyArray = $scope.skladyArrayAll.filter(function (v) {
		    var sklad = v.cislo;
		    if ($scope.amnrSklady !== null) {
			for (i = 0; i < $scope.amnrSklady.length; i++) {
			    if ($scope.amnrSklady[i].sklad == sklad) {
				return true;
			    }
			}
		    }
		    return false;
		});
		if($scope.skladyArray.length==0){
		    $scope.sklad.cislo='';
		}
		else{
		    $scope.sklad.cislo = $scope.skladyArray[0].cislo;
		}
		//$scope.oe.tat=$scope.persinfo.regeloe;
	    }
	});
    }
    
});

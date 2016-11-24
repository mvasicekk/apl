/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var aplApp = angular.module('stApp');

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
aplApp.controller('f355Controller', function ($scope, $routeParams,$http,$timeout,$window,$location,$sanitize) {
    $scope.tinyMceOptions = {
	inline:true,
	menubar:false
    };
    $scope.tinymceModel = "tady se da psat pomoci zabudovaneho editoru, zkus to !";
    $scope.showHelp = false;
    $scope.datePickerFormat = 'dd.MM.yyyy';
    $scope.securityInfo = undefined;
    $scope.isEditor = false;
    $scope.fehlerArray = [
	{
	    druh:'Sandstellen / písek',
	    popis:'',
	    ks:''
	    ,ks_kemper:''
	    ,ks_nacharbeit:''
	},
	{
	    druh:'Schlackestellen / okuje',
	    popis:'',
	    ks:''
	    ,ks_kemper:''
	    ,ks_nacharbeit:''
	},
	{
	    druh:'Versatz / přesazení',
	    popis:'',
	    ks:''
	    ,ks_kemper:''
	    ,ks_nacharbeit:''
	},
	{
	    druh:'Blattrippen / špony',
	    popis:'',
	    ks:''
	    ,ks_kemper:''
	    ,ks_nacharbeit:''
	},
	{
	    druh:'angebrannt / spálené',
	    popis:'',
	    ks:''
	    ,ks_kemper:''
	    ,ks_nacharbeit:''
	},
	{
	    druh:'Kaltlauf / studený vtok',
	    popis:'',
	    ks:''
	    ,ks_kemper:''
	    ,ks_nacharbeit:''
	},
	{
	    druh:'Lufteinschlūsse / bubliny',
	    popis:'',
	    ks:''
	    ,ks_kemper:''
	    ,ks_nacharbeit:''
	},
	{
	    druh:'Kernbruch / prasklé jádro',
	    popis:'',
	    ks:''
	    ,ks_kemper:''
	    ,ks_nacharbeit:''
	},
	{
	    druh:'Lunker / díra',
	    popis:'',
	    ks:''
	    ,ks_kemper:''
	    ,ks_nacharbeit:''
	},
	{
	    druh:'sonstige Fehler/ ostatní chyby',
	    popis:'',
	    ks:''
	    ,ks_kemper:''
	    ,ks_nacharbeit:''
	}
    ];
    
    $scope.teilAktual = null;
    $scope.importAktual = null;


/**
 * 
 * @param {type} teil
 * @returns {unresolved}
 */
    function getImporte(teil){
	return $http.post('./getImporte.php',{teil:teil}).then(
		    function(response){
			console.log(response.data);
			$scope.importe = response.data.importe;
		    }
		);
    }
    /**
     * 
     * @param {type} i
     * @returns {undefined}
     */
    $scope.listRowClicked = function(i){
	console.log(i);
	$scope.teilAktual = $scope.teile[i];
	console.log($scope.teilAktual);
	$scope.teile=null;
	$scope.teil_search=$scope.teilAktual.teillang;
	
	//stahnout importy k vybranemu dilu
	getImporte($scope.teilAktual.Teil);
    }
    
    /**
     * 
     * @param {type} i
     * @returns {undefined}
     */
        $scope.listImportRowClicked = function(i){
	console.log(i);
	$scope.importAktual = $scope.importe[i];
	$scope.importe=null;
    }


/**
 * 
 * @returns {undefined}
 */
$scope.getStkSumme = function(){
    //console.log('getStkSumme');
    var suma = 0;
    $scope.fehlerArray.forEach(function(v){
	var cislaArray = v.ks.split(/\s*[,\/]/gi);
	//console.log(cislaArray);
	cislaArray.forEach(function(c){
	    var cislo = parseFloat(c);
	    if(!isNaN(cislo)){
		suma+=cislo;
	    }
	});
    });
    return suma;
}
/**
 * 
 * @returns {undefined}
 */
    $scope.addFehler = function(){
	var newFehler = {
	    druh:'sonstige Fehler/ ostatní chyby',
	    popis:'',
	    ks:''
	    ,ks_kemper:''
	    ,ks_nacharbeit:''
	};
	$scope.fehlerArray.push(newFehler);
    }
    /**
     * 
     * @returns {unresolved}
     */
    $scope.initSecurity = function () {
	var p = {
	    form_id: 'f355_mangelbericht'
	};
//	return $http.post('./getSecurityInfo.php',p).then(
//		    function(response){
//			$scope.securityInfo = response.data.securityInfo;
//		    }
//		);
	return $http.post('../getSecurityInfo.php', p).then(
		function (response) {
		    $scope.securityInfo = response.data.securityInfo;
		    //zkusim najit roli helptexteditor
		    $scope.securityInfo.roles.forEach(function (v) {
			if (v.rolename == 'helptexteditor') {
			    $scope.isEditor = true;
			    console.log('is helptexteditor');
			}
		    });
		}
	);
    }

    
    $scope.initHelp = function(){
	var p={
	    form_id:'f355_mangelbericht'
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
 * @returns {undefined}
 */
    $scope.createPdf = function(){
    	    console.log('createPdf');
	    var params = {
		importAktual:$scope.importAktual,
		teilAktual: $scope.teilAktual,
		fehlerArray:$scope.fehlerArray,
		sumaKs: $scope.getStkSumme()
	    };
	    $http.post('../Reports/F355_pdf.php', params).then(function (response) {
		console.log('pdf generiert ' + response.data);
		$scope.filename = response.data.filename;
		$scope.pdfPath = response.data.pdfPath;
		$scope.pdfReady = true;
	    });
	
    }


    /**
     * 
     */
    $scope.getTeilMatch = function () {
	var params = {a: $scope.teil_search};
	$scope.importe = null;
	$scope.teilAktual = null;
	$scope.importAktual = null;
	return $http.post(
		'../dkopf/getTeilMatch.php',
		{params: params}
	).then(function (response) {
	    //console.log(response.data);
	    $scope.teile = response.data.teile;
	    
	    if(($scope.teile===null) && ($scope.teil_search.length===10)){
		$scope.createNew = true;
	    }
	    else{
		$scope.createNew = false;
		if(($scope.teile!==null) && ($scope.teile.length===1)){
		    // pokud mi vyhovuje jen jeden dil, tak ho rovnou nastavim jako aktualni
		    $scope.listRowClicked(0);
		}
	    }
	});
    }
    // init
    
    $scope.initSecurity();
    /*
    $scope.initLists();
    */
    $scope.initHelp();
    
    var such = $window.document.getElementById('teil_search');
    if (such) {
	such.focus();
	such.select();
    }
});
aplApp.controller('f450Controller', function ($scope, $routeParams,$http,$timeout,$window,$location,$sanitize) {
    $scope.tinyMceOptions = {
	inline:true,
	menubar:false
    };
    $scope.tinymceModel = "tady se da psat pomoci zabudovaneho editoru, zkus to !";
    $scope.showHelp = false;
    $scope.datePickerFormat = 'dd.MM.yyyy';
    $scope.securityInfo = undefined;
    $scope.isEditor = false;
    $scope.teile = [];
    $scope.teilAktual = null;
    $scope.ab = null;
    $scope.now = new Date();
    
    

    $scope.initHelp = function(){
	var p={
	    form_id:'f450_sklkarta'
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
	    form_id:'f450_sklkarta'
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

    /**
     * 
     * @param {type} i
     * @returns {undefined}
     */
        $scope.listRowClicked = function(i){
	console.log(i);
	$scope.teil_search = $scope.teile[i].amnr;
	$scope.getPolMatch();
    }
    
    /**
     * 
     */
    $scope.getPolMatch = function () {
	$scope.teilAktual = null;
	
	if($scope.teil_search.length>0){
	    return $http.post(
		'../dambew/getAmnrMatch.php',
		{suchen: $scope.teil_search}
	).then(function (response) {
	    $scope.teile = response.data.karty;
	});
	}
	else{
	    $scope.teile = [];
	}
	
    }
    
    $scope.createPdf = function(){
    	    console.log('createPdf');
	    //pred odeslanim prefiltruju pole teile poslu jen polozky s vyplnenym ab>=0
	    teileFiltered = $scope.teile.filter(function(item){
		if(parseInt(item.ab)>=0){
		    return true;
		}
		else{
		    return false;
		}
	    });
	    
	    var params = {
		teile:teileFiltered,ab:$scope.ab
	    };
	    if(teileFiltered.length>0||$scope.teile.length==0){
		$scope.noFilteredTeile = false;
		$http.post('../Reports/F450_pdf.php', params).then(function (response) {
		console.log('pdf generiert ' + response.data);
		$scope.filename = response.data.filename;
		$scope.pdfPath = response.data.pdfPath;
		$scope.pdfReady = true;
		
	    });
	    }
	    else{
		$scope.noFilteredTeile = true;
	    }
	    
	
    }
    // init

    $scope.initSecurity();
    $scope.initHelp();

    var such = $window.document.getElementById('ab');
    if (such) {
	such.focus();
	such.select();
    }
});
aplApp.controller('f1810Controller', function ($scope, $routeParams,$http,$timeout,$window,$location,$sanitize) {
    $scope.tinyMceOptions = {
	inline:true,
	menubar:false
    };
    $scope.tinymceModel = "tady se da psat pomoci zabudovaneho editoru, zkus to !";
    $scope.showHelp = false;
    $scope.datePickerFormat = 'dd.MM.yyyy';
    $scope.securityInfo = undefined;
    $scope.isEditor = false;
    $scope.ma = [];
    $scope.maAktual = null;
    $scope.now = new Date();
    $scope.dateOptions = {
	dateFormat: 'dd.mm.yy',
	firstDay: 1
    };
    $scope.ma_search = '';
    

    $scope.initHelp = function(){
	var p={
	    form_id:'f1810_urlaubantrag'
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
	    form_id:'f1810_urlaubantrag'
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

    /**
     * 
     * @param {type} i
     * @returns {undefined}
     */
        $scope.listRowClicked = function(i){
	console.log(i);
	$scope.ma_search = $scope.ma[i].persnr;// + ' - ' + $scope.ma[i].vorname + ' ' +  $scope.ma[i].name;
	$scope.maAktual = $scope.ma[i];
	$scope.maAktual.tat = "dp";
	
	    return $http.post(
		'../pers/getPersUrlaubInfo.php',
		{
		    persnr:$scope.maAktual.persnr
		}
	).then(function (response) {
	    $scope.urlaubInfo = response.data.urlaubInfo;
	});
    }
    
    /**
     * 
     */
    $scope.getMaMatch = function () {
	$scope.maAktual = null;
	$scope.urlaubInfo = null;
	$scope.ma = [];
	
	
	if($scope.ma_search.length>0){
	    return $http.post(
		'../pers/getPersInfo.php',
		{
		    osoba: $scope.ma_search,
		    jenma:true,
		    oeselected:'*'
		}
	).then(function (response) {
	    $scope.ma = response.data.osoby;
	    if($scope.ma!==null){
		if($scope.ma.length==1){
		$scope.listRowClicked(0);
	    }
	    }
	    
	});
	}
	else{
	    $scope.ma = [];
	}
	
    }
    
    /**
     * 
     * @returns {undefined}
     */
    $scope.createPdf = function(){
    	    console.log('createPdf');
	    var params = {
		ma:$scope.maAktual,
		urlaubinfo:$scope.urlaubInfo
	    };
		$scope.noFilteredTeile = false;
		$http.post('../Reports/F1810_pdf.php', params).then(function (response) {
		console.log('pdf generiert ' + response.data);
		$scope.filename = response.data.filename;
		$scope.pdfPath = response.data.pdfPath;
		$scope.pdfReady = true;
		
	    });
    }
    // init

    $scope.initSecurity();
    $scope.initHelp();

    var such = $window.document.getElementById('ma_search');
    if (such) {
	such.focus();
	such.select();
    }
});

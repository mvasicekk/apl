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

aplApp.controller('f355Controller', function ($scope, $routeParams,$http,$timeout,$window,$location,$sanitize,Upload) {
    $scope.tinyMceOptions = {
	inline:true,
	menubar:false
    };
    $scope.tinymceModel = "tady se da psat pomoci zabudovaneho editoru, zkus to !";
    $scope.showHelp = false;
    $scope.datePickerFormat = 'dd.MM.yyyy';
    $scope.securityInfo = undefined;
    $scope.fehlerArray = [
	{
	    druh:'Sandstellen / písek',
	    popis:'',
	    ks:''
	},
	{
	    druh:'Schlackestellen / okuje',
	    popis:'',
	    ks:''
	},
	{
	    druh:'Versatz / přesazení',
	    popis:'',
	    ks:''
	},
	{
	    druh:'Blattripen / špony',
	    popis:'',
	    ks:''
	},
	{
	    druh:'angebrannt / spálené',
	    popis:'',
	    ks:''
	},
	{
	    druh:'Kaltlauf / studený vtok',
	    popis:'',
	    ks:''
	},
	{
	    druh:'Lufteinschlūsse / bubliny',
	    popis:'',
	    ks:''
	},
	{
	    druh:'Kernbruch / prasklé jádro',
	    popis:'',
	    ks:''
	},
	{
	    druh:'Lunker / díra',
	    popis:'',
	    ks:''
	},
	{
	    druh:'sonstige Fehler/ ostatní chyby',
	    popis:'',
	    ks:''
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
	};
	$scope.fehlerArray.push(newFehler);
    }
    /**
     * 
     * @returns {unresolved}
     */
    $scope.initSecurity = function(){
	var p={
	    form_id:'f355_mangelbericht'
	};
	return $http.post('./getSecurityInfo.php',p).then(
		    function(response){
			$scope.securityInfo = response.data.securityInfo;
		    }
		);
    }

    
    $scope.initHelp = function(){
	var p={
	    form_id:'f355_mangelbericht'
	};
	return $http.post('./getHelpInfo.php',p).then(
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
    /*
    $scope.initSecurity();
    $scope.initLists();
    $scope.initHelp();
    */
    var such = $window.document.getElementById('teil_search');
    if (such) {
	such.focus();
	such.select();
    }
});

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var aplApp = angular.module('dkopfApp');

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

aplApp.controller('detailController', function ($scope, $routeParams,$http,$timeout,$window,$location) {
    
    $scope.securityInfo = undefined;
    $scope.teil = $routeParams.teil;
    $scope.werkstoffe = [];
    $scope.lager = [];
    $scope.aktualJahr;
    $scope.dposOriginalArray = [];
    $scope.mittelList = [];
    $scope.selectedMittel = {};

    /**
    * inicializuje staticke sez;namy pro selecty atd., napr.seznam werkstoffu
    * @returns {undefined}
    */
    $scope.initLists = function(){
	$scope.aktualJahr = new Date().getFullYear();
	var p={
	    form_id:'dkopf'
	};
	return $http.post('./getLists.php',p).then(
		    function(response){
			$scope.werkstoffe = response.data.werkstoffe;
			$scope.lager = response.data.lager;
			$scope.mittelList = response.data.mittelList;
		    }
		);
    }
    /**
     * 
     * @returns {unresolved}
     */
    $scope.initSecurity = function(){
	var p={
	    form_id:'dkopf'
	};
	return $http.post('./getSecurityInfo.php',p).then(
		    function(response){
			$scope.securityInfo = response.data.securityInfo;
		    }
		);
    }

    


    /**
     * 
     * @returns {Number}
     */
    $scope.dposActive = function(){
	//console.log(m);
	if($scope.dpos!==undefined && $scope.dpos!==null){
	    if($scope.dpos.length>0){
		return $scope.dpos
		.filter(function(v,i){
		    if(v['kz-druck']!=='0'){
			return true;
		    }
		    else{
			return false;
		    }
		}).length;
	    }
	    else return 0;
	}
	else return 0;
	}
    /**
     * 
     * @returns {undefined}
     */
    $scope.getDposSumme = function(m){
	//console.log(m);
	if($scope.dpos!==undefined && $scope.dpos!==null){
	    if($scope.dpos.length>0){
		return $scope.dpos
		.filter(function(v,i){
		    if(v['kz-druck']!=='0'){
			return true;
		    }
		    else{
			return false;
		    }
		})
		.reduce(function(prev,v){
		    return prev+parseFloat(v[m]);
		},0);
	    }
	}
    }
    
    /**
     * 
     * @param {type} r
     * @returns {undefined}
     */
    $scope.cancelEditDposRow = function(r){
	//r.edit=0;
	original = undefined;
	//najit polozku v dauftragOriginalArray,vratit puvodni stav a odstranit z pole
	for(i=0;i<$scope.dposOriginalArray.length;i++){
	    if(r.dpos_id==$scope.dposOriginalArray[i].dpos_id){
		original = JSON.parse(JSON.stringify($scope.dposOriginalArray[i]));
		//odstranit polozku z pole
		$scope.dposOriginalArray.splice(i,1);
		break;
	    }
	}
	if(original!==undefined){
	    for(i=0;i<$scope.dpos.length;i++){
		if(original.dpos_id==$scope.dpos[i].dpos_id){
		    for(p in original){
			if(original.hasOwnProperty(p)){
			    $scope.dpos[i][p] = original[p];
			}
		    }
		    $scope.dpos[i].edit=0;
		    break;
		}
	    }
	}
	//console.log($scope.dauftragOriginalArray);
    }
    /**
     * 
     * @param {type} r
     * @returns {unresolved}
     */
    $scope.saveDposRow = function(r){
	// pomoct http.post ulozit radek a pote nastevit edit=0
	r.edit=0;
	//odstranit polozku z dauftragOriginalArray
	for(i=0;i<$scope.dposOriginalArray.length;i++){
	    if(r.dpos_id==$scope.dposOriginalArray[i].dpos_id){
		//odstranit polozku z pole
		$scope.dposOriginalArray.splice(i,1);
		break;
	    }
	}
	//console.log($scope.dauftragOriginalArray);
	// a vlastni ulozeni
	var params = {r: r};
	    return $http.post(
		    './saveDposRow.php',
		    {params: params}
	    ).then(function (response) {
		console.log(response.data);
		if(response.data.insertId>0){
		    $scope.dpos = response.data.dpos;
		    $scope.dpos.forEach(function(v){v.edit=0;});
		}
		if(response.data.ar>0){
		    //vymenim upraveny radek
		    //najit index podle dpos_id
		    var i = $scope.dpos.findIndex(function(e){
			if(e.dpos_id==response.data.dpos_id){
			    return true;
			}
		    });
		    console.log('nalezeny index = '+i);
		    if(i>0){
			$scope.dpos.splice(i,0,response.data.updatedRow);
			$scope.dpos[i].lager_von = {lager:response.data.updatedRow.lager_von};
			$scope.dpos[i].lager_nach = {lager:response.data.updatedRow.lager_nach};
			$scope.dpos.splice(i+1,1);
		    }
		}
	    });
    }
    /**
     * 
     * @param {type} r
     * @returns {unresolved}
     */
    $scope.deleteDposRow = function (r) {
	var text = "Loeschen Position ? / smazat pozici ?";
	var d = $window.confirm(text);
	if (d) {
	    // na klientovi
	    for (i = 0; i < $scope.dpos.length; i++) {
		if (r.dpos_id == $scope.dpos[i].dpos_id) {
		    //odstranit polozku z pole
		    $scope.dpos.splice(i, 1);
		    break;
		}
	    }
	    // a vlastni smazani na serveru
	    var params = {r: r};
	    return $http.post(
		    './deleteDposRow.php',
		    {params: params}
	    ).then(function (response) {
		//console.log(response.data);
//		$scope.dpos = response.data.dpos;
//		$scope.dpos.forEach(function (v) {
//		    v.edit = 0;
//		});
	    });
	}
    }
    /**
     * 
     * @param {type} m
     * @returns {undefined}
     */
    $scope.delMittel = function (m) {
	console.log(m);
	var text = "smazat " + m.nazev + ' (' + m.poznamka + ') ?';
	var d = $window.confirm(text);
	if (d) {
	    var params = {
		teil: m.teil,
		oper: 'del',
		m: m
	    };
	    return $http.post(
		    './updateMittel.php',
		    {params: params}
	    ).then(function (response) {
		console.log(response.data);
		if (response.data.ar > 0) {
		    $scope.mittel = response.data.mittel;
		}
	    });
	}
    }
    /**
     * 
     * @param {type} p
     * @returns {undefined}
     */
    $scope.addMittel = function(p){
	console.log(p);
	console.log($scope.selectedMittel[p['dpos_id']]);
	var params = {
	    teil:p.Teil,
	    oper: 'add',
	    mittel_id: $scope.selectedMittel[p['dpos_id']].id,
	    abgnr:p['TaetNr-Aby']
	};
	return $http.post(
		'./updateMittel.php',
		{params: params}
	).then(function (response) {
	    console.log(response.data);
	    if(response.data.ar>0){
		$scope.mittel = response.data.mittel;
	    }
	});

    }
    /**
     * 
     * @param {type} r
     * @returns {undefined}
     */
    $scope.makeEditable = function(r){
	r.edit=1;
	//console.log(r);
	// schovam si puvodni hodnoty pro pripad cancelEditDposRow
	$scope.dposOriginalArray.push(JSON.parse(JSON.stringify(r)));
	//console.log($scope.dposOriginalArray);
	//+ zmenit na tlacitko pro ulozeni radku
    }
    
    /**
     * 
     * @param {type} p
     * @returns {undefined}
     */
    $scope.newAbgnrChanged = function(p){
	var params = {
	    p:p,
	    abgnr:p['TaetNr-Aby']
	};
	return $http.post(
		'./getNewAbgnrInfo.php',
		params
	).then(function (response) {
	    console.log(response.data);
	    if(response.data.abgnrInfo!==null){
			p['TaetBez-Aby-D'] = response.data.abgnrInfo[0]['oper_D'];
			p['TaetBez-Aby-T'] = response.data.abgnrInfo[0]['oper_CZ'];
			// pridat navrh casu podle puvodniho formulare
			p['VZ-min-kunde'] = response.data.vzkd;
			p['vz-min-aby'] = response.data.vzaby;
	    }
	    else{
		p['TaetNr-Aby'] = '';
		p['TaetBez-Aby-D'] = '';
		p['TaetBez-Aby-T'] = '';
		p['VZ-min-kunde'] = 0;
		p['vz-min-aby'] = 0;
	    }
	});
    }
    /**
     * 
     * @param {type} abgnr
     * @returns {undefined}
     */
    $scope.getMittelForAbgNr = function(p){
	if($scope.mittel===null){
	    $scope.mittel=[];
	}
	return $scope.mittel.filter(function(v){
	    if(v.abgnr==p['TaetNr-Aby'] && p['dpos_id']!='0'){
		return true;
	    }
	    else{
		return false;
	    }
	});
    }
    /**
     * 
     * @param {type} teil
     * @returns {unresolved}
     */
    function getDpos(teil){
	return $http.post('./getDpos.php',{teil:teil}).then(
		    function(response){
			$scope.dpos = response.data.dpos;
			$scope.mittel = response.data.mittel;
			if($scope.dpos!==null){
			    $scope.dpos.forEach(function(v){
				v.edit=0;
				v.lager_von = {lager:v.lager_von};
				v.lager_nach = {lager:v.lager_nach};
				$scope.selectedMittel[v.dpos_id]={id:$scope.mittelList[0].id,abgnr:v['TaetNr-Aby']};
			    });
			}
		    }
		);
    }
    
    /**
     * 
     * @returns {undefined}
     */
    $scope.addDpos = function(){
	var pos = 
	    {
		"dpos_id":"0",
		"Teil":$scope.teilaktual.Teil,
		"KzGut":"",
		"TaetNr-Aby":"3",
		"TaetBez-Aby-D":"Kommentar",
		"TaetBez-Aby-T":"",
		"VZ-min-kunde":"0",
		"vz-min-aby":"0",
		"kz-druck":"0",
		"lager_von":{"lager":""},
		"lager_nach":{"lager":""},
		"bedarf_typ":"",
		"edit":1
	    };
	    
	    $scope.dpos.unshift(pos);
    }
    /**
     * 
     * @param {type} e
     * @returns {undefined}
     */
    $scope.setFocusedElement = function(e){
	//console.log(e);
	$scope.focusedElement = e;
    }
    /**
     * 
     * @param {type} e
     * @returns {undefined}
     */
    $scope.teilaktualChanged = function(field){
	return $http.post('./updateTeilAktual.php',{field:field,teilaktual:$scope.teilaktual}).then(
		    function(response){
			field = response.data.field;
			newValue = response.data.newValue;
			console.log('ar='+response.data.ar);
			console.log('field='+field);
			console.log('newValue='+newValue);
			if(response.data.ar>0){
			    $scope.teilaktual[response.data.field] = response.data.newValue;
			}
			
		    }
		);
    }
    /**
     * 
     * @param {type} p
     * @returns {undefined}
     */
    $scope.kzdruckClicked = function(p){
	if(p['kz-druck']=='0'){
	    p['kz-druck'] = '1';
	}
	else{
	    p['kz-druck'] = '0';
	}
    }
    /**
     * 
     * @param {type} e
     * @returns {undefined}
     */
    $scope.setTeilAktual = function(e){
	//console.log('setTeilAktual event.keyCode='+e.which);
	if($scope.teile!==null && $scope.teile!==undefined){
	    if (($scope.teile.length>=1)&&(e.which==13)) {
		$scope.listRowClicked(0);
	    }
	}
    }
    
     $scope.listRowClicked = function(i){
	//console.log(i);
	$scope.teilaktual = $scope.teile[i];
	
	 //upravit nektere parametry pro pouziti se selecty
	$scope.teilaktual.Wst = {id:$scope.teilaktual.Wst};
	
	$scope.teile=null;
	$scope.teil_search=$scope.teilaktual.Teil;
	getDpos($scope.teilaktual.Teil);
    }
    
    $scope.getTeilMatch = function () {
	var params = {a: $scope.teil_search};
	return $http.post(
		'./getTeilMatch.php',
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
    $scope.initLists();
    if($routeParams.teil_search!='0'){
	$scope.teil_search = $routeParams.teil_search;
	$scope.getTeilMatch();
    }
    var such = $window.document.getElementById('teil_search');
    if (such) {
	such.focus();
	such.select();
    }
});

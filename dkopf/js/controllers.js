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

aplApp.controller('detailController', function ($scope, $routeParams,$http,$timeout,$window,$location,Upload) {
    $scope.datePickerFormat = 'dd.MM.yyyy';
    $scope.dateOptions = {
	startingDay:1
    };
    $scope.anlagenButtons = [
	{
	    name:'Muster',
	    selected:false,
	    att:'muster'
	},
	{
	    name:'EMPB',
	    selected:false,
	    att:'empb'
	},
	{
	    name:'PPA',
	    selected:false,
	    att:'ppa'
	},
	{
	    name:'GPA',
	    selected:false,
	    att:'gpa'
	},
	{
	    name:'VPA',
	    selected:false,
	    att:'vpa'
	},
	{
	    name:'Q-Anforderungen',
	    selected:false,
	    att:'qanf'
	},
    ];
    $scope.anlagenArray = [];
    $scope.securityInfo = undefined;
    $scope.teil = $routeParams.teil;
    $scope.werkstoffe = [];
    $scope.lager = [];
    $scope.aktualJahr;
    $scope.dposOriginalArray = [];
    $scope.mittelList = [];
    $scope.dokumenttyp = [];
    $scope.selectedMittel = {};
    $scope.showWaitWheel = false;
    $scope.selectedButton;


    $scope.uploadFiles1 = function(files, errFiles,b) {
        $scope.files = files;
        $scope.errFiles = errFiles;
        angular.forEach(files, function(file) {
            file.upload = Upload.upload({
                url: './upload.php',
		data: {file: file, att:$scope.selectedAtt,'teil':$scope.teilaktual.Teil}
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
			$scope.anlagenButtonClicked(b);
		    }
		}
            });
        });
    }
    
    $scope.getDokuBeschreibung = function(dokunr){
	//console.log(dokunr);
	var i = $scope.dokumenttyp.findIndex(function(e){
			//console.log('e.doku_nr='+e.doku_nr);
			if(e.doku_nr==dokunr){
			    return true;
			}
		    });
	//console.log(i);
	if(i>=0){
	    return $scope.dokumenttyp[i].doku_beschreibung;
	}
	else{
	    return '';
	}
    }

/**
 * 
 * @param {type} $item
 * @param {type} $model
 * @param {type} $label
 * @param {type} $event
 * @returns {undefined}
 */
    $scope.selectedItem = function($item, $model, $label, $event,p,element){
	$scope.teilDokuRowChanged(p,element);
    }
    
    /**
     * 
     * @param {type} b
     * @returns {undefined}
     */
    $scope.anlagenButtonClicked = function(b){
	console.log(b);
	//vymazat selected vsem buttonum
	$scope.anlagenButtons.forEach(function(v){v.selected=false;});
	b.selected = true;
	$scope.selectedButtonName = b.name;
	$scope.selectedButton = b;
	$scope.selectedAtt = b.att;
	var params = {teil:$scope.teilaktual.Teil,att:b.att};
	$scope.showWaitWheel = true;
	return $http.post(
		'./getAnlagenArray.php',
		params
		).then(function (response) {
		    $scope.anlagenArray = response.data.docsArray;
		    if($scope.anlagenArray!==null){
			$scope.anlagenArray.forEach(function(v){
			    if(v.ext=="JPG"){
				v.filetype='image';
			    }
			    else{
				v.filetype='';
			    }
			});
		    }
		    $scope.anlagenDir = response.data.dir;
		    $scope.showWaitWheel = false;
		});
	}
    /**
     * 
     * @param {type} term
     * @returns {undefined}
     */
    $scope.getFreigabeVom = function(term){
	var p={
	    term:term
	};
	return $http.get('./getFreigabeV.php?term='+term).then(
		    function(response){
			return response.data.freigabevom;
		    }
		);
    }
/**
 * 
 * @param {type} d
 * @returns {undefined}
 */
    $scope.openEinlagPopup = function(d){
	d.einlag_datumPopup.opened = true;
    }
    /**
     * 
     * @param {type} d
     * @returns {undefined}
     */
    $scope.openFreigabePopup = function(d){
	d.freigabe_amPopup.opened = true;
    }
    
    /**
     * 
     * @param {type} d
     * @param {type} element
     * @returns {undefined}
     */
    $scope.teilDokuRowChanged = function(d,element){
	console.log(d);
	console.log(element);
	// v db menim jen uz stavajiciho radku tj. id>0
	if(parseInt(d.id)>0){
	    console.log('update v DB');
	    var params = {d: d,update:'update'};
	    return $http.post(
		'./saveTeilDoku.php',
		params
		).then(function (response) {
		    if(response.data.ar>0){
			getTeilDokuArray($scope.teilaktual.Teil);
		    }
		});
	}
    }
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
			$scope.dokumenttyp = response.data.dokumenttyp;
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
     * @param {type} d
     * @returns {undefined}
     */
    $scope.deleteTeilDokuRow = function (r) {
	var text = "Loeschen Position ? / smazat pozici ?";
	var d = $window.confirm(text);
	if (d) {
	    // na klientovi
	    for (i = 0; i < $scope.teildokuarray.length; i++) {
		if (r.id == $scope.teildokuarray[i].id) {
		    //odstranit polozku z pole
		    $scope.teildokuarray.splice(i, 1);
		    break;
		}
	    }

	    // a pokud uz to byl radek ulozeny v DB (id>0), tak smazu i v db

	    // a vlastni smazani na serveru
	    if (r.id > 0) {
		var params = {r: r};
		return $http.post(
			'./deleteTeilDokuRow.php',
			{params: params}
		).then(function (response) {
		});
	    }
	}
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
     * @returns {undefined}
     */
    function getTeilDokuArray(teil){
	return $http.post('./getDpos.php',{teil:teil}).then(
		    function(response){
			$scope.teildokuarray = response.data.teildokuarray;
			if($scope.teildokuarray!==null){
			    $scope.teildokuarray.forEach(function(v){
				var t = v.einlag_datum.split('.');
				v.einlag_datum = t.length<3?null:new Date(t[2],t[1]-1,t[0]);
				v.einlag_datumPopup = {opened:false};
				var t = v.freigabe_am.split('.');
				v.freigabe_am = t.length<3?null:new Date(t[2],t[1]-1,t[0]);
				v.freigabe_amPopup = {opened:false};
				v.edit=0;
			    });
			}
		    }
		);
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
			
			$scope.teildokuarray = response.data.teildokuarray;
			if($scope.teildokuarray!==null){
			    $scope.teildokuarray.forEach(function(v){
				var t = v.einlag_datum.split('.');
				v.einlag_datum = t.length<3?null:new Date(t[2],t[1]-1,t[0]);
				v.einlag_datumPopup = {opened:false};
				var t = v.freigabe_am.split('.');
				v.freigabe_am = t.length<3?null:new Date(t[2],t[1]-1,t[0]);
				v.freigabe_amPopup = {opened:false};
				v.edit=0;
			    });
			}
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
    $scope.addTeilDoku = function(){
	
	var pos = 
	    {
		"id":"0",
		"doku_nr":"99",
		"teil":$scope.teilaktual.Teil,
		"einlag_datum":new Date(),
		"freigabe_am":null,
		"freigabe_vom":"",
		"musterplatz":"",
		einlag_datumPopup : {opened:false},
		freigabe_amPopup : {opened:false}
	    };
	    
	    if($scope.teildokuarray === null){
		$scope.teildokuarray = [];
	    }
	    $scope.teildokuarray.unshift(pos);
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
	$scope.anlagenButtons.forEach(function(v){v.selected=false;});
	$scope.anlagenArray = [];
	$scope.anlagenDir = '';
	getDpos($scope.teilaktual.Teil);
    }
    
    /**
     * 
     * @param {type} d
     * @returns {undefined}
     */
    $scope.saveTeilDoku = function(d){
	console.log(d);
	var params = {d: d};
	return $http.post(
		'./saveTeilDoku.php',
		params
	).then(function (response) {
	    getTeilDokuArray($scope.teilaktual.Teil);
	});
    }
     
    /**
     * 
     */
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

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

var aplApp = angular.module('ftabloApp');

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



aplApp.controller('ftabloController', function ($scope, $routeParams, $http, $timeout, $window, $location, $sanitize) {
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
    $scope.securityInfo = undefined;
    $scope.kunde = $routeParams.kunde;
    $scope.termin = new Date();
    $scope.teile = [];
    $scope.teiletermin = [];
    $scope.teileHeadersObj = {};
    $scope.teileHeaders = [];
    $scope.showPalTermin = false;
    $scope.palOld = '';
    
    $scope.sortableOptions = {
	stop: function(e, ui) {
	    console.log('sortable stop');
	    console.log($scope.teileHeaders);
	    $http.post(
			'./updateFTabloSort.php',
			{
			    th: $scope.teileHeaders,
			    termin: $scope.termin
			}
		).then(function (response) {

		});
	}
    };
    
    /**
     * 
     * @returns {Array}
     */
    function updateTeileTermin(){
	console.log('updateTeileTermin');
	$scope.teileHeadersObj = {};
	$scope.teileHeaders = [];
	if($scope.teiletermin.length>0){
	    for(i = 0;i<$scope.teiletermin.length;i++){
		t = $scope.teiletermin[i];
		if($scope.teileHeadersObj[t.teil]){
		    //teileHeaders[t.teil].pocet++;
		    //teileHeaders[t.teil].sumvzaby += t.im_stk*t.vzaby;
		}
		else{
		    $scope.teileHeadersObj[t.teil] = {};
		    $scope.teileHeadersObj[t.teil].pocet = 1;
		    $scope.teileHeadersObj[t.teil].sumvzaby = t.im_stk*t.vzaby;
		    $scope.teileHeaders.push({teil:t.teil,kunde:t.kunde});
		}
	    }
	}
    }
    
    
    /**
     * 
     * @param {type} teil
     * @returns {Number}
     */
    $scope.getAbgnrBarvaTerminedTeil = function(teil){
	var abgnrBarva = '';
	// pro kazdej dil a auftragsnr a paletu nascitat kusy jen jednou
	var teilArray = $scope.teiletermin.filter(function(it){
	    if(it.teil==teil){
		return true;
	    }
	    else{
		return false;
	    }
	});
	var abgnrOld=0;
	
	for(i = 0;i<teilArray.length;i++){
	    itm = teilArray[i];
	    if((itm.abgnr!==abgnrOld) && (itm.abgnr>=1100) && (itm.abgnr<=1299)){
		abgnrBarva += itm.abgnr;//+' / ' + numeral(itm.vzaby).format('0,0.00') + ' min';
		abgnrOld = itm.abgnr;
	    }
	}
	return abgnrBarva;
    }
    /**
     * 
     * @param {type} teil
     * @returns {Number}
     */
    $scope.getStkTerminedTeil = function(teil){
	var sumStk = 0;
	// pro kazdej dil a auftragsnr a paletu nascitat kusy jen jednou
	var teilArray = $scope.teiletermin.filter(function(it){
	    if(it.teil==teil){
		return true;
	    }
	    else{
		return false;
	    }
	});
	
	var auftrOld=0;
	var palOld=0;
	
	for(i = 0;i<teilArray.length;i++){
	    itm = teilArray[i];
	    if(itm.auftragsnr!==auftrOld || itm.pal!==palOld){
		sumStk += parseInt(itm.im_stk);
		auftrOld = itm.auftragsnr;
		palOld = itm.pal;
	    }
	}
	return sumStk;
    }
    /**
     * 
     * @returns {undefined}
     */
    $scope.getVzAbyTermined = function(){
	var sumVzAby = 0;
	for(i = 0;i<$scope.teiletermin.length;i++){
	    itm = $scope.teiletermin[i];
	    sumVzAby += itm.im_stk * itm.vzaby;
	}
	return sumVzAby;
    }
    /**
     * 
     * @param {type} t
     * @param {type} i
     * @returns {undefined}
     */
    $scope.teilTerminRowClicked = function (t, i) {

	var teil = t.teil;
	var pal = t.pal;
	var auftragsnr = t.auftragsnr;

	var counter = $scope.teiletermin.length;

	while (counter--) {
	    itm = $scope.teiletermin[counter];
	    if ((itm.teil == teil) && (itm.pal == pal) && (itm.auftragsnr == auftragsnr)) {
		//ulozit info do dauftr
		$http.post(
			'./updateFTabloTermin.php',
			{
			    t: itm,
			    termin: null
			}
		).then(function (response) {

		});
		$scope.teiletermin.splice(counter, 1);
	    }
	}
	updateTeileTermin();
    }
    
    /**
     * 
     * @param {type} t
     * @param {type} i
     * @returns {undefined}
     */
    $scope.teilRowClicked = function (t, i) {

	var teil = t.teil;
	var pal = t.pal;
	var auftragsnr = t.auftragsnr;

	var counter = $scope.teile.length;
	while (counter--) {
	    itm = $scope.teile[counter];
	    if ((itm.teil == teil) && (itm.pal == pal) && (itm.auftragsnr == auftragsnr)) {
		if((itm.statnr=='S0061' || itm.statnr=='S0062')){
		    //do terminu presunu jen "barevne" operace
		    //ulozit info do dauftr
		    $http.post(
			'./updateFTabloTermin.php',
			{
			    t: itm,
			    termin: $scope.termin
			}
		    ).then(function (response) {

		    });
		    $scope.teiletermin.push(itm);
		}
		
		$scope.teile.splice(counter, 1);
	    }
	}
	updateTeileTermin();
    }
    
    /**
     * 
     * @param {type} pal
     * @returns {undefined}
     */
    $scope.isNewPal = function(pal){
	if(pal!==$scope.palOld){
	    $scope.palOld=pal;
	    return true;
	}
	else{
	    return false;
	}
    }
    /**
     * 
     * @returns {unresolved}
     */
    $scope.teilsuchenChanged = function () {
	console.log('teilsuchenChanged');
	$scope.palOld='';
	return $http.post(
		'./getTeile.php',
		{
		    termin:0,
		    kunde: $scope.kunde,
		    teil: $scope.teilsuchen
		}
	).then(function (response) {
	    if(response.data.teile!==null){
		$scope.teile = response.data.teile;
	    }
	    else{
		$scope.teile = [];
	    }
	});
    }
    
    /**
     * 
     * @returns {unresolved}
     */
    function getTeileTermined() {
	console.log('getTeileTermined');

	return $http.post(
		'./getTeile.php',
		{
		    termin: $scope.termin,
		    kunde: $scope.kunde,
		    teil: ""
		}
	).then(function (response) {
	    $scope.teileHeadersObj = {};
	    $scope.teileHeaders = [];
	    if(response.data.teile!==null){
		$scope.teiletermin = response.data.teile;
		updateTeileTermin();
	    }
	    else{
		$scope.teiletermin = [];
	    }
	});
	
    }


    $scope.getRowCountForTeil = function(t){
	return $scope.teiletermin.filter(function(item){
	    if(item.teil==t){
		return true;
	    }
	    else{
		return false;
	    }
	}).length;
    }
    /**
     * 
     * @returns {undefined}
     */
    $scope.terminUpdated = function(){
	console.log('terminUpdated');
	getTeileTermined();
    }
    
    /**
     * 
     * @returns {unresolved}
     */
    $scope.initSecurity = function () {
	var p = {
	    form_id: 'ftablo'
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
	    form_id: 'ftablo'
	};
	return $http.post('../getHelpInfo.php', p).then(
		function (response) {
		    $scope.helpText = response.data.help.helpText;
		    $scope.hIArray = response.data.help.hiArray;
		}
	);
    }
    
    // init
    $scope.initSecurity();
    $scope.initHelp();
    //$scope.initLists();
    
    //$scope.getfirstActiveMA();
    //$scope.getTeileTermined();
    //console.log($routeParams);


    var such = $window.document.getElementById('teilsuchen');
    if (such) {
	such.focus();
	such.select();
    }

});

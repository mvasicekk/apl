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
    teileHeaders = {};
    
    /**
     * 
     * @returns {Array}
     */
    $scope.getTeileTermin = function(){
	
	if($scope.teiletermin.length>0){
	    for(i = 0;i<$scope.teiletermin.length;i++){
		t = $scope.teiletermin[i];
		if(teileHeaders[t.teil]){
		    //teileHeaders[t.teil].pocet++;
		    //teileHeaders[t.teil].sumvzaby += t.im_stk*t.vzaby;
		}
		else{
		    teileHeaders[t.teil] = {};
		    teileHeaders[t.teil].pocet = 1;
		    teileHeaders[t.teil].sumvzaby = t.im_stk*t.vzaby;
		}
	    }
	}
	
	return teileHeaders;
    }
    
    /**
     * 
     * @param {type} t
     * @param {type} i
     * @returns {undefined}
     */
    $scope.teilTerminRowClicked = function (t, i) {
	for (i = 0; i < $scope.teiletermin.length; i++) {
	    //najit polozku, kterou chci smazat podle auftragsnr,teil,pal
	    var p = $scope.teiletermin[i];
	    if (p.auftragsnr == t.auftragsnr && p.teil == t.teil && p.pal == t.pal) {
		$scope.teiletermin.splice(i, 1);
		return $http.post(
			'./updateFTabloTermin.php',
			{
			    t: t,
			    termin: null
			}
		).then(function (response) {
		});
		break;
	    }
	}
	//$scope.teiletermin.push(t);
    }
    
    /**
     * 
     * @param {type} t
     * @param {type} i
     * @returns {undefined}
     */
    $scope.teilRowClicked = function(t,i){
	
	var teil = t.teil;
	var pal = t.pal;
	var auftragsnr = t.auftragsnr;
	
	var counter=$scope.teile.length;
	console.log('counter='+counter);
	while(counter--){
	    itm = $scope.teile[counter];
	    console.log('itm:');
	    console.log(itm);
	    
	    if((itm.teil==teil) && (itm.pal==pal) && (itm.auftragsnr==auftragsnr)){
		console.log('shoda');
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
		    $scope.teile.splice(counter,1);
	    }
	    console.log('counter='+counter);
	}
    }
    
    /**
     * 
     * @returns {unresolved}
     */
    $scope.teilsuchenChanged = function () {
	console.log('teilsuchenChanged');
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
	    teileHeaders = {};
	    if(response.data.teile!==null){
		$scope.teiletermin = response.data.teile;
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

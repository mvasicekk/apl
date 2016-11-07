/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var aplApp = angular.module('hodnoceniApp');

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



aplApp.controller('hodnocenifiremniController', function ($scope, $routeParams, $http, $timeout, $window, $location, $sanitize) {
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

    $scope.datum = new Date();
    var curdate = new Date();

    $scope.von = new Date(curdate.getFullYear(), curdate.getMonth(), 1);
    $scope.bis = new Date(curdate.getFullYear(), curdate.getMonth() + 1, 0);
    $scope.hodnoceniJahr = curdate.getFullYear();
    $scope.curJahr = curdate.getFullYear();
    $scope.hodnoceniMonat = curdate.getMonth();
    $scope.firmaFaktorMonat = {};


    $scope.initSecurity = function () {
	var p = {
	    form_id: 'hodnoceni_firemni'
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
	    form_id: 'hodnoceni_firemni'
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
		{
		    von:$scope.von,
		    bis:$scope.bis
		}
	).then(function (response) {
	    //console.log(response.data);
	    $scope.firemniFaktory = response.data.firemniFaktory;
	    $scope.jmArray = response.data.jmArray;
	});
    }

    /**
     * 
     * @param {type} p
     * @param {type} jm
     * @returns {Number}
     */
    $scope.sumaMonat = function (p, jm) {
	var suma = 0;
	for (pr in $scope.firmaFaktorMonat) {
	    if (typeof ($scope.firmaFaktorMonat[pr][jm]) !== 'undefined') {
		value = parseFloat($scope.firmaFaktorMonat[pr][jm][p]);
		if (!isNaN(value)) {
		    suma += value;
		}
	    }

	}
	return suma;
    }
    /**
     * 
     * @returns {unresolved}
     */
    $scope.getjmArray = function () {
	return $http.post(
		'./getJmArray.php',
		{
		    von:$scope.von,
		    bis:$scope.bis
		}
	).then(function (response) {
	    $scope.jmArray = response.data.jmArray;
	});
    }
    
    $scope.getFirmaFaktorMonat = function () {
	return $http.post(
		'./getFirmaFaktorMonat.php',
		{
		    von:$scope.von,
		    bis:$scope.bis
		}
	).then(function (response) {
	    $scope.firmaFaktorMonat = response.data.firmaFaktorMonat;
	});
    }
    
    /**
     * 
     * @param {type} v
     * @returns {undefined}
     */
    $scope.datumChanged = function(v){
	if(v=='von'){
	    $scope.von = new Date($scope.von.getFullYear(), $scope.von.getMonth(), 1);
	}
	if(v=='bis'){
	    $scope.bis = new Date($scope.bis.getFullYear(), $scope.bis.getMonth() + 1, 0);
	}
	$scope.getjmArray();
	$scope.getFirmaFaktorMonat();
    }
    
    /**
     * 
     * @param {type} firmaFaktorMonat
     * @returns {unresolved}
     */
    $scope.updateHodnoceniFirma = function(firmaFaktorMonat){
	return $http.post(
		'./updateFirmaFaktorMonat.php',
		{
		    ffM:firmaFaktorMonat
		}
	).then(function (response) {
	    //$scope.firmaFaktorMonat = response.data.firmaFaktorMonat;
	    if(response.data.ar>0){
		//povedl se update
	    }
	    else{
		//update se po validaci nepovedl, nastvim hodnotu na 0
		//$scope.firmaFaktorMonat[firmaFaktorMonat.id][]
	    }
	});
    }
    
    // init
    $scope.initSecurity();
    $scope.initLists();
    $scope.initHelp();
    $scope.getFirmaFaktorMonat();
    
});

aplApp.controller('hodnocenifaktoryoeController', function ($scope, $routeParams, $http, $timeout, $window, $location, $sanitize) {
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

    $scope.datum = new Date();
    var curdate = new Date();

    $scope.von = new Date(curdate.getFullYear(), curdate.getMonth(), 1);
    $scope.bis = new Date(curdate.getFullYear(), curdate.getMonth() + 1, 0);
    $scope.hodnoceniJahr = curdate.getFullYear();
    $scope.curJahr = curdate.getFullYear();
    $scope.hodnoceniMonat = curdate.getMonth();
    $scope.faktoryOE = {};
    $scope.oefilter = {oe:'G'};


    $scope.initSecurity = function () {
	var p = {
	    form_id: 'hodnoceni_faktory_oe'
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
	    form_id: 'hodnoceni_faktory_oe'
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
		'./getListsFaktoryOE.php',
		{
		}
	).then(function (response) {
	    //console.log(response.data);
	    $scope.osobniFaktory = response.data.osobniFaktory;
	    $scope.firemniFaktory = response.data.firemniFaktory;
	    $scope.oeArray = response.data.oeArray;
	});
    }

    /**
     * 
     * @returns {unresolved}
     */
    $scope.getFaktoryOE = function () {
	return $http.post(
		'./getFaktoryOE.php',
		{
		}
	).then(function (response) {
	    $scope.faktoryOE = response.data.faktoryOE;
	});
    }
    
    /**
     * 
     * @param {type} foe
     * @param {type} id
     * @param {type} oe
     * @returns {unresolved}
     */
    $scope.updateFaktoryOE = function(foe,id,oe){
	return $http.post(
		'./updateFaktoryOE.php',
		{
		    foe:foe,
		    id:id,
		    oe:oe
		}
	).then(function (response) {
	    $scope.faktoryOE[response.data.id][response.data.oe].id_hodnoceni_faktory_oe = response.data.id_hodnoceni_faktory_oe;
	});
    }
    
    /**
     * 
     * @param {type} f
     * @returns {undefined}
     */
    $scope.updateFiremniFaktor = function(f){
	console.log(f)
	return $http.post(
		'./updateFiremniFaktor.php',
		{
		    f:f
		}
	).then(function (response) {
	    //$scope.osobniFaktory[response.data.f.id].id_firma_faktor = response.data.v;
	});
    }
    // init
    $scope.initSecurity();
    $scope.initLists();
    $scope.initHelp();
    $scope.getFaktoryOE();
});

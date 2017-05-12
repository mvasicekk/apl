/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var aplApp = angular.module('panelyApp');

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



aplApp.controller('panelyController', function ($scope, $routeParams, $http, $timeout, $window, $location, $sanitize) {
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
    $scope.places = [];
    $scope.panels = [];
    $scope.activePlace = 0;



    $scope.initSecurity = function () {
	var p = {
	    form_id: 'panely'
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
	    form_id: 'panely'
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
 * @returns {unresolved}
 */
    $scope.getPlacesAndPanels = function(placeid=0){
	return $http.post(
		'./getPlaces.php',
		{
		    placeid:placeid
		}
	).then(function (response) {
	    //console.log(response.data);
	    $scope.activePlace = placeid;
	    
	    if(placeid>0){
		console.log('nactene panely');
		$scope.panels = response.data.panels;
	    }
	    else{
		console.log('nactene mista');
		$scope.places = response.data.places;
	    }
	    
	});
    }
    
    /**
     * 
     * @param {type} panel
     * @param {type} t
     * @returns {undefined}
     */
    $scope.textChanged = function(panel,t){
	console.log('textChanged');
	console.log(panel);
	console.log(t);
	return $http.post(
		'./updatePanelTable.php',
		{
		    panel:panel,
		    t:t
		}
	).then(function (response) {
	    //console.log(response.data);
	    //$scope.places = response.data.places;
	    //$scope.panels = response.data.panels;
	    //$scope.activePlace = placeid;
	});
    }
    /**
     * 
     * @param {type} placeid
     * @returns {undefined}
     */
    $scope.setActivePlace = function(placeid){
	$scope.activePlace = placeid;
    }
    
    // init
    $scope.initSecurity();
    //$scope.initLists();
    $scope.initHelp();
    $scope.getPlacesAndPanels(0);
});



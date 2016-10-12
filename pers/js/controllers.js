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



//aplApp.controller('kartyController', function ($scope, $routeParams,$http,$timeout,$window,$location,$sanitize) {
//    $scope.isEditor = false;	//urcuje zda muze uzivatel editovat helptext
//    $scope.tinyMceOptions = {
//	inline:true,
//	menubar:false
//    };
//    $scope.tinymceModel = "tady se da psat pomoci zabudovaneho editoru, zkus to !";
//    $scope.dateOptions = {
//		dateFormat: 'dd.mm.yy',
//		firstDay: 1
//    };
//    $scope.showHelp = false;
//    $scope.datePickerFormat = 'dd.MM.yyyy';
//    $scope.securityInfo = undefined;
//    
//    $scope.amnrChanged = function(){
//	console.log($scope.karta);
//	return $http.post(
//		'./getAmnrMatch.php',
//		{suchen: $scope.karta}
//	).then(function (response) {
//	    //console.log(response.data);
//	    $scope.kartyRows = response.data.karty;
//	});
//    }
//    
//    $scope.initHelp = function(){
//	var p={
//	    form_id:'dambew_karty'
//	};
//	return $http.post('../getHelpInfo.php',p).then(
//		    function(response){
//			$scope.helpText = response.data.help.helpText;
//			$scope.hIArray = response.data.help.hiArray;
//		    }
//		);
//    }
//    
//    $scope.initSecurity = function(){
//	var p={
//	    form_id:'dambew_karty'
//	};
//	return $http.post('../getSecurityInfo.php',p).then(
//		    function(response){
//			$scope.securityInfo = response.data.securityInfo;
//			//zkusim najit roli helptexteditor
//			$scope.securityInfo.roles.forEach(function(v){
//			    if(v.rolename=='helptexteditor'){
//				$scope.isEditor = true;
//				console.log('is helptexteditor');
//			    }
//			});
//		    }
//		);
//    }
//    
//    $scope.initSecurity();
//    $scope.initHelp();
//});


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
    $scope.osobaChanged = function () {
	console.log('osobaChanged');
	return $http.post(
		'./getPersInfo.php',
		{osoba: $scope.osoba,jenma:$scope.jenma}
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
	    $scope.oeArray = response.data.oeArray;
	    $scope.skladyArrayAll = response.data.skladyArray;
	    $scope.skladyArray = response.data.skladyArray;
	    if ($scope.skladyArray.length > 0) {
		$scope.sklad.cislo = $scope.skladyArray[0].cislo;
	    }
	});
    }
    // init
    $scope.initSecurity();
//    $scope.initLists();
    $scope.initHelp();

    var such = $window.document.getElementById('osoba');
    if (such) {
	such.focus();
	such.select();
    }

});

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

var aplApp = angular.module('rechnungApp');

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



aplApp.controller('rechnungController', function ($scope, $routeParams, $http, $timeout, $window, $location, $sanitize) {
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
    $scope.showRechnungTeilenForm = false;
    $scope.securityInfo = undefined;
    $scope.auftragsnr = $routeParams.auftragsnr;
    $scope.curDate = new Date();
    $scope.dauftrRows = [];
    $scope.maradio = {
	druckMA:'voll'
    };

/**
 * 
 * @returns {undefined}
 */
$scope.toggleRechnungTeilenForm  = function(){
    $scope.showRechnungTeilenForm = !$scope.showRechnungTeilenForm;
}

    /**
     * 
     * @returns {unresolved}
     */
    $scope.initSecurity = function () {
	var p = {
	    form_id: 'rechnung'
	};
	return $http.post('../../getSecurityInfo.php', p).then(
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
	    form_id: 'rechnung'
	};
	return $http.post('../../getHelpInfo.php', p).then(
		function (response) {
		    $scope.helpText = response.data.help.helpText;
		    $scope.hIArray = response.data.help.hiArray;
		}
	);
    }

    $scope.initRechnung = function () {
	return $http.post(
		'./getRechnungInit.php',
		{auftragsnr:$scope.auftragsnr}
	).then(function (response) {
	    console.log(response.data);
	    $scope.rechnungInfo = response.data;
	    // TODO, pro testovani, smazat !!!
	    //$scope.rechnungInfo.hatMARechnung = true;
	    $scope.maradio.druckMA='voll';
	    if($scope.rechnungInfo!==null){
		
	    }
	    $scope.dauftrRows = response.data.dauftrRows;
	});
    }
    
    // init
    $scope.initSecurity();
    $scope.initHelp();
    $scope.initRechnung();
});

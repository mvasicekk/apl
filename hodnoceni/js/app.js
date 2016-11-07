/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var aplApp = angular.module('hodnoceniApp', ['ngNumeraljs', 'ui.date', 'ui.tinymce', 'ngSanitize', 'ui.bootstrap', 'ngRoute']);

aplApp.config(['$numeraljsConfigProvider', function ($numeraljsConfigProvider) {
	var language = {
	    delimiters: {
		thousands: ' ',
		decimal: ','
	    },
	    abbreviations: {
		thousand: 'k',
		million: 'm',
		billion: 'b',
		trillion: 't'
	    },
	    ordinal: function (number) {
		return '.';
	    },
	    currency: {
		symbol: '€'
	    }
	};
	$numeraljsConfigProvider.setLanguage('de', language);
	$numeraljsConfigProvider.setCurrentLanguage('de');
    }]);

aplApp.config(['$routeProvider', '$sceProvider',
    function ($routeProvider, $sceProvider) {
	$sceProvider.enabled(false);
	$routeProvider.
		when('/hodnoceni_firemni/', {
		    templateUrl: function (p) {
			console.log(p);
			return 'templates/hodnoceni_firemni.html';
		    }
		    //controller:'eformController'
		})
		.when('/hodnoceni_faktory_oe/', {
		    templateUrl: function (p) {
			console.log(p);
			return 'templates/hodnoceni_faktory_oe.html';
		    }
		    //controller:'eformController'
		})
		.otherwise({redirectTo: '/hodnoceni_firemni/'});
    }]);


aplApp.config(['$compileProvider', function ($compileProvider) {
	//$compileProvider.debugInfoEnabled(false);
    }]);
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var aplApp = angular.module('dambewApp',['ngNumeraljs','ui.date','ui.tinymce','ngSanitize','ui.bootstrap','ngRoute']);

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

aplApp.config(['$routeProvider','$sceProvider',
    function($routeProvider,$sceProvider){
	$sceProvider.enabled(false);
	$routeProvider.
		when('/bew/',{
		    templateUrl:function(p){
			console.log(p);
			return 'templates/dambew.html';
		    }
		    //controller:'eformController'
		})
		.when('/karty/',{
		    templateUrl:function(p){
			console.log(p);
			return 'templates/karty.html';
		    }
		    //controller:'eformController'
		})
		.otherwise({redirectTo:'/bew/'});
    }]);


aplApp.config(['$compileProvider', function ($compileProvider) {
  //$compileProvider.debugInfoEnabled(false);
}]);
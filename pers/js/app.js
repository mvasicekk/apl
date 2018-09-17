/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//var aplApp = angular.module('persApp',['ngNumeraljs','ngFileUpload','ui.date','ui.scrollpoint','ui.select','ui.tinymce','ngSanitize','ui.bootstrap','ngRoute']);
var aplApp = angular.module('persApp',['ngNumeraljs','ngFileUpload','ui.date','ui.select','ui.tinymce','ngSanitize','ui.bootstrap','ngRoute']);
var persPlanApp = angular.module('persPlanApp',['ngNumeraljs','ngFileUpload','ui.date','ui.select','ui.tinymce','ngSanitize','ui.bootstrap','ngRoute']);


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
		when('/ma/',{
		    templateUrl:function(p){
			//console.log(p);
			return 'templates/ma.html';
		    }
		    //controller:'eformController'
		})
		.when('/showplan/:oe/:datum',{
		    templateUrl:function(p){
			//console.log(p);
			return 'templates/showplan.html';
		    }
		    //controller:'eformController'
		})
		.when('/showplan/',{
		    templateUrl:function(p){
			//console.log(p);
			return 'templates/showplan.html';
		    }
		    //controller:'eformController'
		})
		.otherwise({redirectTo:'/ma/'});
    }]);


aplApp.config(['$compileProvider', function ($compileProvider) {
  //$compileProvider.debugInfoEnabled(false);
}]);


//------------------------------------------------------------------------------
//persplanApp
persPlanApp.config(['$numeraljsConfigProvider', function ($numeraljsConfigProvider) {
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

persPlanApp.config(['$routeProvider','$sceProvider',
    function($routeProvider,$sceProvider){
	$sceProvider.enabled(false);
	$routeProvider.
		when('/maplan/',{
		    templateUrl:function(p){
			//console.log(p);
			return 'templates/maplan.html';
		    }
		    //controller:'eformController'
		})
		.otherwise({redirectTo:'/maplan/'});
    }]);


persPlanApp.config(['$compileProvider', function ($compileProvider) {
  //$compileProvider.debugInfoEnabled(false);
}]);
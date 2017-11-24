/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//var aplApp = angular.module('stApp',['ui.tinymce','ngSanitize','ngFileUpload','angular-thumbnails','ui.bootstrap','ngRoute']);
var aplApp = angular.module('stApp',['ngNumeraljs','ui.date','ui.tinymce','ngSanitize','ui.select','ui.bootstrap','ngRoute']);

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

aplApp.config(['$routeProvider',
    function($routeProvider){
	$routeProvider.
		when('/eform/:eformid',{
		    templateUrl:function(p){
			console.log(p);
			return 'templates/eform/'+p.eformid+'.html';
		    }
		    //controller:'eformController'
		})
		.when('/sonstform/:eformid',{
		    templateUrl:function(p){
			console.log(p);
			return 'templates/sonstform/'+p.eformid+'.html';
		    }
		    //controller:'eformController'
		})
		.otherwise({redirectTo:'.'});
    }]);


aplApp.config(['$compileProvider', function ($compileProvider) {
  //$compileProvider.debugInfoEnabled(false);
}]);
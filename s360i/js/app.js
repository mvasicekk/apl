//globalni nastaveni pro aplikaci s360i


var aplApp = angular.module('s360iApp',['ngNumeraljs','ngSanitize','ui.select','ui.date','ngRoute','smart-table','aplFilters','ui.bootstrap']);

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
		when('/list',{
		    templateUrl:'templates/list.html',
		    controller:'s360iController'
		}).
		otherwise({redirectTo:'/list'});
    }]);


aplApp.config(['$compileProvider', function ($compileProvider) {
  $compileProvider.debugInfoEnabled(false);
}]);
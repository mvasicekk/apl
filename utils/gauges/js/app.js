/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var aplApp = angular.module('gaugesApp',[]);

numeral.language('cs', {
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
    ordinal : function (number) {
        return number === 1 ? 'er' : 'ème';
    },
    currency: {
        symbol: '€'
    }
});
// switch between languages
numeral.language('cs');
//aplApp.config(['$routeProvider',
//    function($routeProvider){
//	$routeProvider.
//		when('/eform/:eformid',{
//		    templateUrl:function(p){
//			console.log(p);
//			return 'templates/eform/'+p.eformid+'.html';
//		    }
//		    //controller:'eformController'
//		})
//		.otherwise({redirectTo:'.'});
//    }]);


aplApp.config(['$compileProvider', function ($compileProvider) {
  //$compileProvider.debugInfoEnabled(false);
}]);
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var aplApp = angular.module('dkopfApp',['ui.bootstrap','ngRoute']);

aplApp.config(['$routeProvider',
    function($routeProvider){
	$routeProvider.
		when('/det/:teil_search',{
		    templateUrl:'templates/detail.html',
		    controller:'detailController'
		}).
		otherwise({redirectTo:'/det/0'});
    }]);


aplApp.config(['$compileProvider', function ($compileProvider) {
  //$compileProvider.debugInfoEnabled(false);
}]);
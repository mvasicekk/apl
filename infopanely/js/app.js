/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//var aplApp = angular.module('panelyApp',['ngSanitize','ui.select','ui.date','ui.bootstrap','ngRoute','smart-table','aplFilters']);
var aplApp = angular.module('panelyApp',['ngSanitize','ui.select','ui.date','ngRoute','aplFilters']);

aplApp.config(['$routeProvider',
    function($routeProvider){
	$routeProvider.
		when('/det',{
		    templateUrl:'templates/detail.html',
		    controller:'detailController'
		}).
		otherwise({redirectTo:'/det'});
    }]);


aplApp.config(['$compileProvider', function ($compileProvider) {
  //$compileProvider.debugInfoEnabled(false);
}]);
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var aplApp = angular.module('persstatApp',['ngSanitize','ui.select','ui.date','ngRoute','smart-table','aplFilters','ui.bootstrap']);

aplApp.config(['$routeProvider',
    function($routeProvider){
	$routeProvider.
		when('/list',{
		    templateUrl:'templates/list.html',
		    controller:'persstatController'
		}).
		otherwise({redirectTo:'/list'});
    }]);


aplApp.config(['$compileProvider', function ($compileProvider) {
  $compileProvider.debugInfoEnabled(false);
}]);
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var aplApp = angular.module('reklApp',['ngNumeraljs','ngSanitize','ui.select','ui.date','ngRoute','smart-table']);

aplApp.config(['$routeProvider',
    function($routeProvider){
	$routeProvider.
		when('/list',{
		    templateUrl:'templates/list.html',
		    controller:'reklController'
		}).
		when('/detail/:reklid',{
		    templateUrl:'templates/detail.html',
		    controller:'detailController'
		}).
		when('/det/:reklid',{
		    templateUrl:'templates/detail.html',
		    controller:'detailController'
		}).
		otherwise({redirectTo:'/list'});
    }]);

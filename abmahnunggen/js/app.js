/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var aplApp = angular.module('abmahnunggenApp',['ngSanitize','ui.select','ui.date','ngRoute','smart-table','aplFilters']);

aplApp.config(['$routeProvider',
    function($routeProvider){
	$routeProvider.
		when('/form/:abmahnungid',{
		    templateUrl:'templates/form.html',
		    controller:'formController'
		}).
//		when('/detail/:reklid',{
//		    templateUrl:'templates/detail.html',
//		    controller:'detailController'
//		}).
//		when('/det/:reklid',{
//		    templateUrl:'templates/detail.html',
//		    controller:'detailController'
//		}).
		otherwise({redirectTo:'/form/0'});
    }]);


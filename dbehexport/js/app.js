/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var aplApp = angular.module('dbehexportApp',['ngSanitize','ui.select','ui.date','ui.bootstrap','ngRoute','smart-table','aplFilters']);

//aplApp.config(['$routeProvider',
//    function($routeProvider){
//	$routeProvider.
//		when('/list',{
//		    templateUrl:'templates/list.html',
//		    controller:'listController'
//		}).
//		when('/det/:auftragsnr',{
//		    templateUrl:'templates/detail.html',
//		    controller:'detailController'
//		}).
//		otherwise({redirectTo:'/det/0'});
//    }]);


aplApp.config(['$compileProvider', function ($compileProvider) {
  //$compileProvider.debugInfoEnabled(false);
}]);
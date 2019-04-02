/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//var aplApp = angular.module('persApp',['ngNumeraljs','ngFileUpload','ui.date','ui.scrollpoint','ui.select','ui.tinymce','ngSanitize','ui.bootstrap','ngRoute']);
var mdApp = angular.module('mdApp',['ngMaterial','ngRoute','ngSanitize']);

mdApp.config(['$routeProvider','$sceProvider',
    function($routeProvider,$sceProvider){
	$sceProvider.enabled(false);
	$routeProvider.
		when('/statory/',{
		    templateUrl:function(p){
			//console.log(p);
			return 'templates/statory.html';
		    },
		    controller:'statoryController'
		})
//		.when('/showplan/:oe/:datum',{
//		    templateUrl:function(p){
//			//console.log(p);
//			return 'templates/showplan.html';
//		    }
//		    //controller:'eformController'
//		})
//		.when('/showplan/:oe',{
//		    templateUrl:function(p){
//			//console.log(p);
//			return 'templates/showplan.html';
//		    }
//		    //controller:'eformController'
//		})
//		.when('/showplan/',{
//		    templateUrl:function(p){
//			//console.log(p);
//			return 'templates/showplan.html';
//		    }
//		    //controller:'eformController'
//		})
		.otherwise({redirectTo:'/statory/'});
    }]);


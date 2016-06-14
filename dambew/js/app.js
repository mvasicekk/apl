/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var aplApp = angular.module('dambewApp',['ui.date','ui.tinymce','ngSanitize','ui.bootstrap','ngRoute']);

aplApp.config(['$routeProvider','$sceProvider',
    function($routeProvider,$sceProvider){
	$sceProvider.enabled(false);
	$routeProvider.
		when('/bew/',{
		    templateUrl:function(p){
			console.log(p);
			return 'templates/dambew.html';
		    }
		    //controller:'eformController'
		})
		.otherwise({redirectTo:'/bew/'});
    }]);


aplApp.config(['$compileProvider', function ($compileProvider) {
  //$compileProvider.debugInfoEnabled(false);
}]);
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

angular.module('stApp')
	.directive('aplHelp', function () {
	    
	    return {
		scope:{
		    el:'=',
		    showhelp:'=',
		    showadmininfo:'='
		},
		restrict: 'E',
		templateUrl: './templates/aplhelp.html',
		link: function (scope, element, attrs) {
		}
	    }
	});


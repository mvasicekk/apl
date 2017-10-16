/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

angular.module('rechnungApp')
	.directive('aplHelp', function ($http) {

	    return {
		scope: {
		    el: '=',
		    showhelp: '=',
		    showadmininfo: '=',
		    editable: '='
		},
		restrict: 'E',
		templateUrl: './templates/aplhelp.html',
		link: function (scope, element, attrs) {
		    scope.$watch('el', function (newVal, oldVal) {
			if (oldVal !== undefined) {
			    if (newVal[0].help_text != oldVal[0].help_text) {
				//zde byla zmena v help textu
				// a updatnout v DB
				$http.post(
					//ulozeni noveho helptextu
					attrs.helptextupdater,
					{id: newVal[0].id, helptext: newVal[0].help_text}
				).then(function (response) {
				});
			    }
			}
		    }, true);
		}
	    }
	});


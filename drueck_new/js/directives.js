/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

angular.module('dambewApp')
	.directive('aplHelp', function ($http) {
	    
	    return {
		scope:{
		    el:'=',
		    showhelp:'=',
		    showadmininfo:'=',
		    editable:'='
		},
		restrict: 'E',
		templateUrl: './templates/aplhelp.html',
		link: function (scope, element, attrs) {
		    scope.$watch('el',function(newVal,oldVal){
			if(oldVal!==undefined){
			    //console.log('directive el changed');
			    //console.log(newVal);
			    //console.log(oldVal);
			    //for(p in newVal){
				if(newVal[0].help_text!=oldVal[0].help_text){
				    //zde byla zmena v help textu
				    //console.log(newVal[0].id);
				    //console.log(newVal[0].help_text);
				    //console.log(attrs),
				    // a updatnout v DB
				    
				    $http.post(
					//'./updateHelpText.php',
					attrs.helptextupdater,
					{id:newVal[0].id,helptext:newVal[0].help_text}
				    ).then(function (response) {
					//console.log(response.data);
				    });
				}
			    //}
			}
		    },true);
		}
	    }
	});


/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

angular.module('fApp')
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

                        if(newVal[0].help_text!=oldVal[0].help_text){
                            $http.post(
                                //'./updateHelpText.php',
                                //ulozeni noveho helptextu
                                attrs.helptextupdater,
                                {id:newVal[0].id,helptext:newVal[0].help_text}
                            ).then(function (response) {
                                //console.log(response.data);
                            });
                        }

                    }
                },true);
            }
        }
    });


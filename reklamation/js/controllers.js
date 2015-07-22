/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var aplApp = angular.module('reklApp');

aplApp.controller('reklController',function($scope,$http){
    $http.get('./getReklamationen.php').success(function(data){
	$scope.reklamationen = data.reklamationen;
	$scope.dReklamationen = [].concat($scope.reklamationen);
    });
});


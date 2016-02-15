/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var aplApp = angular.module('dkopfApp');

aplApp.directive("enterfocus", function () {
        return {
            restrict: "A",
            link: function ($scope, elem, attrs) {
                var focusables = $(":focusable");
                elem.bind("keydown", function (e) {
                    var code = e.keyCode || e.which;
                    if (code === 13) {
                        var current = focusables.index(this);
                        var next = focusables.eq(current + 1).length ? focusables.eq(current + 1) : focusables.eq(0);
			console.log('current='+current+' next=');
			console.log(next);
                        next.focus();
			next.select();
                        e.preventDefault();
                    }
                });
            }
        }
});

aplApp.controller('detailController', function ($scope, $routeParams,$http,$timeout,$window,$location) {
    
    $scope.securityInfo = undefined;
    $scope.teil = $routeParams.teil;

    $scope.initSecurity = function(){
	var p={
	    form_id:'dkopf'
	};
	return $http.post('./getSecurityInfo.php',p).then(
		    function(response){
			$scope.securityInfo = response.data.securityInfo;
		    }
		);
    }
    
    // init
    $scope.initSecurity();
    
    
});

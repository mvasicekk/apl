var mdApp = angular.module('mdApp');
mdApp.controller('statoryController', function ($scope, $routeParams, $http, $timeout, $window, $location) {
    
    $scope.drueckInfo = null;
    
    $scope.statory = [];
    
    
    /**
     * 
     * @param {type} searchStator
     * @returns {undefined}
     */
    $scope.querySearchStatory = function(searchStator){
	return $http.post(
		'./querySearchStatory.php',
		{ 
		    stator:searchStator,
		    drueck_id:$scope.sys
		}
	    ).then(function (response) {
		    if(response.data.statory!==null){
			return response.data.statory;
		    }
	    	    else{
			return false;
		    }
	    });
    }
    /**
     * 
     * @param {type} chip
     * @returns {undefined}
     */
    $scope.transformChip = function (chip) {
	// If it is an object, it's already a known chip
	console.log(chip);
	if (angular.isObject(chip)) {
	    $http.post(
		'./addDrueckRow.php',
		{ 
		    sys:$scope.sys,
		    et_invnummer: chip.stator,
		}
	    ).then(function (response) {
	    	    
	    });
	    return {et_invnummer: chip.stator, id: 'nový'};;
	}
	else if(chip.length>0){
	    // Otherwise, create a new one
	    $http.post(
		'./addDrueckRow.php',
		{ 
		    sys:$scope.sys,
		    et_invnummer: chip,
		}
	    ).then(function (response) {
	    	    
	    });
	    return {et_invnummer: chip.stator, id: 'nový'};
	}
	else{
	    //chip nepridam
	    return null;
	}
	
    }
    
    /**
     * 
     * @param {type} chip
     * @param {type} index
     * @param {type} event
     * @returns {undefined}
     */
    $scope.removeChip = function(chip,index,event){
//	console.log('chip removed');
//	console.log(chip);
//	console.log('index='+index);
//	console.log(event);
	$http.post(
		'./delDrueckRow.php',
		{ 
		    id:chip.id
		}
	    ).then(function (response) {
	    	    
	    });
    }
    
    /**
     * 
     * @returns {undefined}
     */
    $scope.sysChanged = function(){
	console.log($scope.sys);
	$http.post(
		'./getDrueckRow.php',
		{ 
		    sys:$scope.sys
		}
	).then(function (response) {
	    if(response.data.rows!==null){
		$scope.drueckInfo = response.data.rows[0];
		if(response.data.rowsPlus!==null){
		    $scope.statory = [];
		    for(i = 0;i<response.data.rowsPlus.length;i++){
			$scope.statory.push(response.data.rowsPlus[i]);
		    }
		}
	    }
	    else{
		$scope.statory = [];
		$scope.drueckInfo = null;
	    }
	});
    }
});


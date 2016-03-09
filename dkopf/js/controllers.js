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
			//console.log('current='+current+' next=');
			//console.log(next);
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
    $scope.werkstoffe = [];
    $scope.aktualJahr;

    /**
    * inicializuje staticke seznamy pro selecty atd., napr.seznam werkstoffu
    * @returns {undefined}
    */
    $scope.initLists = function(){
	$scope.aktualJahr = new Date().getFullYear();
	var p={
	    form_id:'dkopf'
	};
	return $http.post('./getLists.php',p).then(
		    function(response){
			$scope.werkstoffe = response.data.werkstoffe;
		    }
		);
    }
    /**
     * 
     * @returns {unresolved}
     */
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

    var such = $window.document.getElementById('teil_search');
    if (such) {
	such.focus();
	such.select();
    }
    
    /**
     * 
     * @param {type} teil
     * @returns {unresolved}
     */
    function getDpos(teil){
	return $http.post('./getDpos.php',{teil:teil}).then(
		    function(response){
			$scope.dpos = response.data.dpos;
		    }
		);
    }
    /**
     * 
     * @param {type} e
     * @returns {undefined}
     */
    $scope.setFocusedElement = function(e){
	//console.log(e);
	$scope.focusedElement = e;
    }
    /**
     * 
     * @param {type} e
     * @returns {undefined}
     */
    $scope.teilaktualChanged = function(field){
	return $http.post('./updateTeilAktual.php',{field:field,teilaktual:$scope.teilaktual}).then(
		    function(response){
			field = response.data.field;
			newValue = response.data.newValue;
			console.log('ar='+response.data.ar);
			console.log('field='+field);
			console.log('newValue='+newValue);
			if(response.data.ar>0){
			    $scope.teilaktual[response.data.field] = response.data.newValue;
			}
			
		    }
		);
    }
    /**
     * 
     * @param {type} p
     * @returns {undefined}
     */
    $scope.kzdruckClicked = function(p){
	if(p['kz-druck']=='0'){
	    p['kz-druck'] = '1';
	}
	else{
	    p['kz-druck'] = '0';
	}
    }
    /**
     * 
     * @param {type} e
     * @returns {undefined}
     */
    $scope.setTeilAktual = function(e){
	//console.log('setTeilAktual event.keyCode='+e.which);
	if($scope.teile!==null && $scope.teile!==undefined){
	    if (($scope.teile.length>=1)&&(e.which==13)) {
		$scope.listRowClicked(0);
	    }
	}
    }
    
     $scope.listRowClicked = function(i){
	//console.log(i);
	$scope.teilaktual = $scope.teile[i];
	
	 //upravit nektere parametry pro pouziti se selecty
	$scope.teilaktual.Wst = {id:$scope.teilaktual.Wst};
	
	$scope.teile=null;
	$scope.teil_search=$scope.teilaktual.Teil;
	getDpos($scope.teilaktual.Teil);
    }
    
    $scope.getTeilMatch = function () {
	var params = {a: $scope.teil_search};
	return $http.post(
		'./getTeilMatch.php',
		{params: params}
	).then(function (response) {
	    //console.log(response.data);
	    $scope.teile = response.data.teile;
	    
	    if(($scope.teile===null) && ($scope.teil_search.length===10)){
		$scope.createNew = true;
	    }
	    else{
		$scope.createNew = false;
		if(($scope.teile!==null) && ($scope.teile.length===1)){
		    // pokud mi vyhovuje jen jeden dil, tak ho rovnou nastavim jako aktualni
		    $scope.listRowClicked(0);
		}
	    }
	});
    }
    // init
    $scope.initSecurity();
    $scope.initLists();
    
    
});

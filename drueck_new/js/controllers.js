/**
 * Created by Miroslav on 16.06.2016.
 */

var aplApp = angular.module('auftragApp');


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
    };
});

aplApp.controller('rc', function ($scope,$http,$filter) {
    

    
    $scope.palette = '0';
    $scope.datum = $filter('date')(new Date(), 'dd.MM.yyyy');
    $scope.mehr = '0';
    
    $("#datum").css('background-color', 'rgb(179, 255, 70)');
    //****************************************\\
    $scope.auftrCheck = function(){
	return $http.post(
            './auftrag_validate.php',
            {auftragsnr:$scope.auftragsnr}).
                then( function(response){
                   $scope.auftragsnr = response.data.auftragsnr;
                    //alert($scope.auftragsnr);
                    
                    if(response.data.auftragsnr === "ERROR-NOAUFTRAGSNR"){
                        $("#auftragsnr").css('background-color', 'rgb(255, 124, 124)');
                    }else{
                        $("#auftragsnr").css('background-color', 'rgb(179, 255, 70)');
                    }
        });
    };
    
    $scope.palCheck = function(){
        return $http.post(
            './pal_validate.php',
            {palette:$scope.palette,auftragsnr:$scope.auftragsnr}).
                then( function(response){
                    $scope.palette = response.data.palette;
                    $scope.teilnr = response.data.teil;
                    $scope.tatnrarray = response.data.teilbez;
                    $scope.export = response.data.export;
                    $scope.teilB = response.data.teilB;
                     if(response.data.palette === "ERROR-NOPAL"){
                        $("#pal").css('background-color', 'rgb(255, 124, 124)');
                    }else{
                        $("#pal").css('background-color', 'rgb(179, 255, 70)');
                    }
                    
                    
        });
    };

    $scope.dateCheck = function(){
        return $http.post(
            './datum_validate.php',
            {datum:$scope.datum}).
                then( function(response){
                $scope.datum = response.data.datum;               
                if(response.data.datum === "ERROR"){
                     $("#datum").css('background-color', 'rgb(255, 124, 124)');
                }else{
                     $("#datum").css('background-color', 'rgb(179, 255, 70)');
                    }
        });
    };

    $scope.mehrCheck = function(){
        return $http.post(
                './mehr_validate.php',
                {mehr:$scope.mehr,palette:$scope.palette,auftragsnr:$scope.auftragsnr}).
                then( function(response){
                    $scope.tatnrarray = response.data.teilb;
                    //alert(response.data.teilb);
                if($scope.mehr >= "0"){
                     $("#mehr").css('background-color', 'rgb(179, 255, 70)');
                }else{
                     $("#mehr").css('background-color', 'rgb(255, 124, 124)');
                    }                  
                });
    };
    

   $scope.myVar = false;
   $scope.toggleAPLinfo = function() {
   $scope.myVar = !$scope.myVar;
   };
   
   $scope.get_data = function(){
    $http.get('./showAPLinfo.php').success(function(data){

        $scope.fields = data;
        return $scope;
    });
};

   $scope.get_last_records = function(){
    $http.get('./lastRecord.php').success(function(data){
        $scope.fiel = data;
        return $scope;
    });
};

   $scope.tatX  = function(){
	return $http.post(
            './abgnr_validate.php',
            {tat1:$scope.tat1,mehr:$scope.mehr,palette:$scope.palette,auftragsnr:$scope.auftragsnr,teilnr:$scope.teilnr}).
                then( function(response){
                 $scope.tat1 = [];
                 $scope.tat1 = response.data.abgnr;
            
                 alert(response.data.abgnr);
        });
    };
    
    
    
    
    
    
    });








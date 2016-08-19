/**
 * Created by Miroslav on 16.06.2016.
 */
var aplApp = angular.module('auftragApp',['ngRoute']);

aplApp.config(['$routeProvider',
    function($routeProvider){
        $routeProvider.
            when('/',{
            templateUrl:'templates/detail.html'
        })}]);
aplApp.config(['$compileProvider', function ($compileProvider) {
    //$compileProvider.debugInfoEnabled(false);
}]);



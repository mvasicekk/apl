/**
 * Created by Miroslav on 16.06.2016.
 */
 var aplApp = angular.module('statApp',['ui.sortable','ui.select','ui.tinymce','ui.bootstrap','ngRoute','ui.date']);


aplApp.config(['$routeProvider',
    function($routeProvider){
        $routeProvider.
            when('/',{
            templateUrl:'templates/index.html'
        })}]);
aplApp.config(['$compileProvider', function ($compileProvider) {
    //$compileProvider.debugInfoEnabled(false);
}]);

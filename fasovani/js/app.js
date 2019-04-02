
 var aplApp = angular.module('fApp',['ui.sortable','ui.select','ngSanitize','ui.tinymce','ui.bootstrap','ngRoute']);


aplApp.config(['$routeProvider',
    function($routeProvider){
        $routeProvider.
            when('/',{
            templateUrl:'templates/index.html'
        })}]);
aplApp.config(['$compileProvider', function ($compileProvider) {
    //$compileProvider.debugInfoEnabled(false);
}]);

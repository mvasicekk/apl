<!DOCTYPE html>
<html>
    <head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<title>
	    Arbeitsplan pflegen / sprava pracovniho planu
	</title>

<!--jQuery dependencies-->
    <script src="./bower_components/jquery/dist/jquery.min.js" type="text/javascript"></script>

{*	Bootstrap*}
    <link href="./bower_components/bootstrap/dist/css/bootstrap.css" rel="stylesheet">
    <script src="./bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

    <script src="./bower_components/angular/angular.min.js"></script>
    <script src="./bower_components/angular-route/angular-route.min.js"></script>
    <script src="./bower_components/angular-bootstrap/ui-bootstrap-tpls.min.js"></script>
    
    <script src="../libs/bower_components/angular-thumbnails/dist/angular-thumbnails.min.js"></script>
    
    <script src="./js/app.js"></script>
    <script src="./js/controllers.js"></script>
    <script src="../js/filters.js"></script>
    
    <link href="./css/style.css" rel="stylesheet">

</head>

<body ng-app="dkopfApp">
{include file='../../templates/headingBS.tpl'}
{literal}
<div ng-view></div>
{/literal}
</body>


</html>
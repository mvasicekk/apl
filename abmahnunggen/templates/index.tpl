<!DOCTYPE html>
<html>
    <head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<title>
	    Abmahnung generator
	</title>

<!--jQuery dependencies-->
{*	<link rel="stylesheet" href="../js/jquery-ui-1.10.4/themes/base/jquery-ui.css" />*}
    <script src="./bower_components/jquery/dist/jquery.min.js" type="text/javascript"></script>
    <script src="./bower_components/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
    <link href="./bower_components/jquery-ui/themes/smoothness/jquery-ui.min.css" rel="stylesheet">

{*	Bootstrap*}
    <link href="./bower_components/bootstrap/dist/css/bootstrap.css" rel="stylesheet">
    <script src="./bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
{*	<script src="../js/jquery-ui-1.11.0/jquery-ui.min.js" type="text/javascript"></script>*}

    <script src="./bower_components/angular/angular.min.js"></script>
    <script src="./bower_components/angular-route/angular-route.min.js"></script>
    <script src="./bower_components/angular-ui-date/src/date.js"></script>
    
    <script src="./bower_components/angular-smart-table/dist/smart-table.min.js"></script>
    
    <script type="text/javascript" src="../plupload/js/plupload.full.js"></script>
    
    <script src="./bower_components/angular-sanitize/angular-sanitize.min.js"></script>
    <script src="./bower_components/angular-ui-select/dist/select.min.js"></script>
    <link href="./bower_components/angular-ui-select/dist/select.min.css" rel="stylesheet">

    
    <script src="./bower_components/floatThead/dist/jquery.floatThead.min.js"></script>
    <script src="./bower_components/numeral/numeral.js"></script>
    
    <script src="./js/app.js"></script>
    <script src="./js/controllers.js"></script>
    <script src="../js/filters.js"></script>
    
    
    <link href="./css/style.css" rel="stylesheet">
    <link href="./css/style_det.css" rel="stylesheet">
</head>

<body ng-app="abmahnunggenApp">
{include file='../../templates/headingBS.tpl'}
{literal}
<div ng-view></div>
{/literal}
</body>


</html>
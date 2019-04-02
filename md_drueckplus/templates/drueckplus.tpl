<!DOCTYPE html>
<html>
    <head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<title>
	    DrueckPlus
	</title>
{*	Bootstrap*}
	<link href="../libs/bootstrap/css/bootstrap.min.css" rel="stylesheet">
	
	<!--jQuery dependencies-->
	
	{*	<link rel="stylesheet" href="../js/jquery-ui-1.10.4/themes/base/jquery-ui.css" />*}
{*	<script src="../libs/bower_components/jquery/dist/jquery.min.js" type="text/javascript"></script>
	<script src="../libs/bower_components/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
	<link href="../libs/bower_components/jquery-ui/themes/smoothness/jquery-ui.min.css" rel="stylesheet">
*}
{*	<link rel="stylesheet" href="../js/jquery-ui-1.10.4/themes/base/jquery-ui.css" />*}
{*	<script src="../js/jquery-1.11.0.min.js" type="text/javascript"></script>
	<script src="../js/jquery-ui-1.11.0/jquery-ui.min.js" type="text/javascript"></script>
	<link href="../js/jquery-ui-1.11.0/jquery-ui.theme.min.css" rel="stylesheet">
*}{*	Bootstrap*}
{*	<script src="../libs/bootstrap/js/bootstrap.min.js"></script>*}
	
	<link href='./node_modules/angular-material/angular-material.min.css' rel="stylesheet">
	<script src='./node_modules/angular/angular.min.js'></script>
	<script src='./node_modules/angular-route/angular-route.min.js'></script>
	<script src='./node_modules/angular-animate/angular-animate.min.js'></script>
	<script src='./node_modules/angular-aria/angular-aria.min.js'></script>
	<script src='./node_modules/angular-material/angular-material.min.js'></script>
	<script src='./node_modules/angular-messages/angular-messages.min.js'></script>
	<script src='./node_modules/angular-sanitize/angular-sanitize.min.js'></script>
	
{*	<script src="../libs/bower_components/angular/angular.min.js"></script>
	<script src="../libs/bower_components/angular-route/angular-route.min.js"></script>
*}{*	<script src="../libs/bower_components/angular-ui-scrollpoint/dist/scrollpoint.min.js"></script>*}
{*	<script src="../libs/bower_components/ng-file-upload/ng-file-upload.min.js"></script>
	
	<script src="../libs/bower_components/angular-sanitize/angular-sanitize.min.js"></script>
	<script src="../libs/bower_components/angular-bootstrap/ui-bootstrap-tpls.min.js"></script>
    
	<script src="../libs/bower_components/angular-ui-date/dist/date.js"></script>
	<script src="../libs/bower_components/tinymce-dist/tinymce.min.js"></script>
	<script src="../libs/bower_components/angular-ui-tinymce/dist/tinymce.min.js"></script>

	<script src="../libs/bower_components/numeral/min/numeral.min.js"></script>
	<script src="../libs/bower_components/angular-numeraljs/dist/angular-numeraljs.min.js"></script>
	
	<script src="./bower_components/angular-ui-select/dist/select.min.js"></script>
	<link href="./bower_components/angular-ui-select/dist/select.min.css" rel="stylesheet">
*}	
{*	<script src="../libs/bower_components/ngmap/build/scripts/ng-map.min.js"></script>*}
{*	<script src="https://maps.google.com/maps/api/js?key=AIzaSyDLu4nP4fAKwfBje213A8-d_xf3OI2usmU"></script>*}
	
{*	<script src="../libs/bower_components/angular-google-maps/dist/angular-google-maps.min.js"></script>*}
	
	
{*	<script src="../libs/bower_components/angular-google-maps/dist/angular-google-maps-street-view.min.js"></script>
	<script src="../libs/bower_components/angular-google-maps/dist/angular-google-maps-street-view_dev_mapped.min.js"></script>
	<script src="../libs/bower_components/angular-google-maps/dist/angular-google-maps_dev_mapped.min.js"></script>
*}	
	


	
	<script src="./js/app.js"></script>
	<script src="./js/controllers.js"></script>
{*	<script src="./js/directives.js"></script>*}
{*	<script src="../js/filters.js"></script>*}
    
{*	<link href="./css/style.css" rel="stylesheet">*}
    </head>
	
    <body ng-app="mdApp">
	{include file='../../templates/headingBS.tpl'}

	{literal}
	<div ng-view></div>
	{/literal}
    </body>
</html>
    
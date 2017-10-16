<!DOCTYPE html>
<html>
    <head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<title>
	    Exportrechnung erstellen / vytvoreni faktury podle exportu
	</title>
{*	Bootstrap*}
	<link href="../../libs/bootstrap/css/bootstrap.min.css" rel="stylesheet">
	
	<!--jQuery dependencies-->
	
	{*	<link rel="stylesheet" href="../js/jquery-ui-1.10.4/themes/base/jquery-ui.css" />*}
	<script src="../../libs/bower_components/jquery/dist/jquery.min.js" type="text/javascript"></script>
	<script src="../../libs/bower_components/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
	<link href="../../libs/bower_components/jquery-ui/themes/smoothness/jquery-ui.min.css" rel="stylesheet">

	
	
{*	Bootstrap*}
	<script src="../../libs/bootstrap/js/bootstrap.min.js"></script>
	
	
	<script src="../../libs/bower_components/angular/angular.min.js"></script>
	<script src="../../libs/bower_components/angular-route/angular-route.min.js"></script>
	
	<script src="../../libs/bower_components/angular-sanitize/angular-sanitize.min.js"></script>
	<script src="../../libs/bower_components/angular-bootstrap/ui-bootstrap-tpls.min.js"></script>
    
	<script src="../../libs/bower_components/angular-ui-date/dist/date.js"></script>
	<script src="../../libs/bower_components/tinymce-dist/tinymce.min.js"></script>
	<script src="../../libs/bower_components/angular-ui-tinymce/dist/tinymce.min.js"></script>

	<script src="../../libs/bower_components/numeral/min/numeral.min.js"></script>
	<script src="../../libs/bower_components/angular-numeraljs/dist/angular-numeraljs.min.js"></script>
	
	<script src="./bower_components/angular-ui-select/dist/select.min.js"></script>
	<link href="./bower_components/angular-ui-select/dist/select.min.css" rel="stylesheet">
	
	<script src="../../libs/bower_components/angular-ui-sortable/sortable.min.js" type="text/javascript"></script>


	
	<script src="./js/app.js"></script>
	<script src="./js/controllers.js"></script>
	<script src="./js/directives.js"></script>
	<script src="../../js/filters.js"></script>
    
	<link href="./css/style.css" rel="stylesheet">
    </head>
	
    <body ng-app="rechnungApp">
	{include file='../../../templates/headingBS.tpl'}

	{literal}
	<div ng-view></div>
	{/literal}
    </body>
</html>
    
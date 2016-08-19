<!doctype html>
<html lang="cs">
<head>
	<meta charset="utf-8">
	<title>
        Rückmeldungen / zadání výkonu
    </title>
    <!-- Latest compiled and minified CSS -->
    <link href="../libs/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!--jQuery dependencies-->

    {*	<link rel="stylesheet" href="../js/jquery-ui-1.10.4/themes/base/jquery-ui.css" />*}
    <script src="../libs/bower_components/jquery/dist/jquery.min.js" type="text/javascript"></script>
    <script src="../libs/bower_components/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
    <link href="../libs/bower_components/jquery-ui/themes/smoothness/jquery-ui.min.css" rel="stylesheet">

    {*	<link rel="stylesheet" href="../js/jquery-ui-1.10.4/themes/base/jquery-ui.css" />*}
    {*	<script src="../js/jquery-1.11.0.min.js" type="text/javascript"></script>
        <script src="../js/jquery-ui-1.11.0/jquery-ui.min.js" type="text/javascript"></script>
        <link href="../js/jquery-ui-1.11.0/jquery-ui.theme.min.css" rel="stylesheet">
    *}{*	Bootstrap*}
    <script src="../libs/bootstrap/js/bootstrap.min.js"></script>


    <script src="../libs/bower_components/angular/angular.min.js"></script>
    <script src="../libs/bower_components/angular-route/angular-route.min.js"></script>

    <script src="../libs/bower_components/angular-sanitize/angular-sanitize.min.js"></script>
    <script src="../libs/bower_components/angular-bootstrap/ui-bootstrap-tpls.min.js"></script>

    <script src="../libs/bower_components/angular-ui-date/dist/date.js"></script>
    <script src="../libs/bower_components/tinymce-dist/tinymce.min.js"></script>
    <script src="../libs/bower_components/angular-ui-tinymce/dist/tinymce.min.js"></script>
    <link rel="stylesheet" href="css/style.css">
    <script src="js/app.js" type="text/javascript"></script>
    <script src="js/controllers.js" type="text/javascript"></script>
   

</head>
<body ng-app="auftragApp">
{include file='../../templates/headingBS.tpl'}

{literal}
    <div ng-view></div>
{/literal}

</body>
</html>
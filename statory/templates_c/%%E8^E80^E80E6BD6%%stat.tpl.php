<?php /* Smarty version 2.6.14, created on 2017-04-06 09:02:55
         compiled from stat.tpl */ ?>
<!doctype html>
<html lang="cs">
<head>
	<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
	<meta content="utf-8" http-equiv="encoding">

	<title>
      Statory
    </title>
    <!-- Latest compiled and minified CSS -->
    <link href="../libs/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!--jQuery dependencies-->

        <script src="../libs/bower_components/jquery/dist/jquery.min.js" type="text/javascript"></script>
    <script src="../libs/bower_components/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
    <link href="../libs/bower_components/jquery-ui/themes/smoothness/jquery-ui.min.css" rel="stylesheet">


            <script src="../libs/bootstrap/js/bootstrap.min.js"></script>


    <script src="../libs/bower_components/angular/angular.min.js"></script>
    <script src="../libs/bower_components/angular-route/angular-route.min.js"></script>

    <script src="../libs/bower_components/angular-sanitize/angular-sanitize.min.js"></script>
    <script src="../libs/bower_components/angular-bootstrap/ui-bootstrap-tpls.min.js"></script>

    <script src="../libs/bower_components/angular-ui-date/dist/date.js"></script>
    <script src="../libs/bower_components/tinymce-dist/tinymce.min.js"></script>
    <script src="../libs/bower_components/angular-ui-tinymce/dist/tinymce.min.js"></script>

		<script src="../libs/bower_components/angular-ui-select/dist/select.min.js"></script>
		<link href="../libs/bower_components/angular-ui-select/dist/select.min.css" rel="stylesheet">
	<script src="../libs/bower_components/angular-bootstrap/ui-bootstrap-tpls.min.js"></script>
		<script src="../libs/bower_components/angular-ui-sortable/sortable.min.js" type="text/javascript"></script>


    <link rel="stylesheet" href="css/style.css">
    <script src="js/app.js" type="text/javascript"></script>
    <script src="js/controllers.js" type="text/javascript"></script>



</head>
<body ng-app="statApp">
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => '../../templates/headingBS.tpl', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php echo '
    <div ng-view></div>
'; ?>


</body>
</html>
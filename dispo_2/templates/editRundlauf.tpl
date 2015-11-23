<!DOCTYPE html>
<html>

    <head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1" />          

	<!--jQuery dependencies-->
	<link rel="stylesheet" href="../js/jquery-ui-1.10.4/themes/base/jquery-ui.css" />
	<script src="../js/jquery-1.11.0.min.js" type="text/javascript"></script>
	<script src="../js/jquery-ui-1.11.0/jquery-ui.min.js" type="text/javascript"></script>

	<link href="../brany/bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
	<script src="../brany/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
	{*	<link rel="stylesheet" href="../styl_common.css" type="text/css">*}

	{*	Handsontable files*}
	<script src="../libs/moment/moment.js"></script>
	<script src="../libs/pikaday/pikaday.js"></script>
	<link rel="stylesheet" media="screen" href="../libs/pikaday/css/pikaday.css">
	<script src="../libs/handsontable/handsontable.full.js"></script>
	<link rel="stylesheet" media="screen" href="../libs/handsontable/handsontable.full.css">

	<script type = "text/javascript" src = "./editRundlauf.js"></script>
	<link rel="stylesheet" href="./editRundlauf.css" type="text/css">

	<title>
	    APL Abydos - Edit Rundlauf
	</title>    
    </head>
    <body>
	{include file='../../templates/headingBS.tpl'}
	{*	{include file='../../templates/heading.tpl'}*}
	<div class="container-fluid">
	    <div class="page-header">
		<div class="row">
		    <div class="col-sm-12">
			<h4 class="text-center">
			    <span class="glyphicon glyphicon-road"></span>
			    Edit Rundlauf
			</h4>
		    </div>
		</div>
	    </div>


	    <div class="row">
		<div class="col-sm-12 form-inline">
		    <fieldset>
			<legend>Filter</legend>
			<div class="form-group">
			    <label for="datumVon">Datum von</label>
			    <input class="form-control" type="date" accesskey="d" id="datumVon" value="" />
			</div>
			<div class="form-group">
			    <label for="datumBon">Datum bis</label>
			    <input class="form-control" type="date" accesskey="d" id="datumBis" value="" />
			</div>
			<div class="form-group">
			    <label for="spediteur">Spediteur</label>
			    {html_options class=form-control id=spediteur name=spediteur options=$spedOptions selected=$spedSelected}
			</div>
		    </fieldset>
		</div>
	    </div>
	    <div class="row">
		<div class="col-sm-12">
		    <div id="exceltable" style="margin:auto;margin-top:5px;"></div>
		</div>
	    </div>
	</div>
    </body>
</html>

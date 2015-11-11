<!DOCTYPE html>
<html>

    <head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1" />          
        
{*	<link href="../brany/bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
	<script src="../brany/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
*}	<link rel="stylesheet" href="../styl_common.css" type="text/css">
	

	<!--jQuery dependencies-->
	<link rel="stylesheet" href="../js/jquery-ui-1.10.4/themes/base/jquery-ui.css" />
	<script src="../js/jquery-1.11.0.min.js" type="text/javascript"></script>
	<script src="../js/jquery-ui-1.11.0/jquery-ui.min.js" type="text/javascript"></script>
	
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
	{include file='../../templates/heading.tpl'}
	<div id="sizeinfo"></div>
	<div id="mainform">
	<fieldset>
	    <legend>Main filter</legend>
	    <label for="datumVon">Datum von</label>
	    <input type="date" accesskey="d" id="datumVon" value="" />
    	    <label for="datumBon">Datum bis</label>
	    <input type="date" accesskey="d" id="datumBis" value="" />
	    <label for="spediteur">Spediteur</label>
	    {html_options id=spediteur name=spediteur options=$spedOptions selected=$spedSelected}
	</fieldset>
	
	<div id="exceltable" style="margin:auto;"></div>
	</div>
    </body>
</html>

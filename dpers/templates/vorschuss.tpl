<!DOCTYPE html>
<html>

    <head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1" />          
        <link rel="stylesheet" href="../styl_common.css" type="text/css">
	<link rel="stylesheet" href="./vorschuss.css" type="text/css">

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

	<script type = "text/javascript" src = "./vorschuss.js"></script>

	<title>
	    APL Abydos - Edit Vorschuss
	</title>    
    </head>
    <body>
	{include file='../../templates/heading.tpl'}
	<div id="sizeinfo"></div>
	<div id="mainform">
	<fieldset>
	    <legend>Main filter</legend>
	    <label for="persnr">PersNr</label>
	    <input type="number" accesskey="p" id="persnr" value="0" autofocus />
	    <label for="datum">Datum</label>
	    <input type="date" accesskey="d" id="datum" value="" />
	</fieldset>
	
	<div id="exceltable" style="margin:auto;"></div>
	</div>
    </body>
</html>

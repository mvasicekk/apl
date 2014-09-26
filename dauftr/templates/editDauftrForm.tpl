<!DOCTYPE html>
<html>

    <head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1" />          
        <link rel="stylesheet" href="../styl_common.css" type="text/css">
	<link rel="stylesheet" href="./editDauftrForm.css" type="text/css">

	<!--jQuery dependencies-->
	<link rel="stylesheet" href="../js/jquery-ui-1.10.4/themes/base/jquery-ui.css" />
	<script src="../js/jquery-1.11.0.min.js" type="text/javascript"></script>
	<script src="../js/jquery-ui-1.11.0/jquery-ui.min.js" type="text/javascript"></script>
	<!--PQ Grid files-->
	<link href="../libs/pqgrid/css/pqgrid.min.css" rel="stylesheet" type="text/css"/>
	<script src="../libs/pqgrid/js/pqgrid.min.js" type="text/javascript"></script>
	<!--PQ Grid Office theme-->
	<link rel="stylesheet" href="../libs/pqgrid/themes/office/pqgrid.css" />

	<script type = "text/javascript" src = "./editDauftrForm.js"></script>

	<title>
	    APL Abydos - Edit Dauftr
	</title>    
    </head>
    <body>
	{include file='../../templates/heading.tpl'}
	<div id="sizeinfo"></div>
	<div id="mainform">
	<fieldset>
	    <legend>Main filter</legend>
	    <label for="import">Import</label>
	    <input type="number" accesskey="i" id="import" value="{$import}" autofocus />
	    <label for="teil">Teil</label>
	    <input type="text" accesskey="t" id="teil" value="*" />
	    <label for="teil">Plan</label>
	    <input type="number" accesskey="p" id="plan" value="" />
	</fieldset>
	
	<div id="dauftrgrid" style="margin:auto;"></div>
	    
	<div id="map-container">
	    
	</div>
	</div>
    </body>
</html>

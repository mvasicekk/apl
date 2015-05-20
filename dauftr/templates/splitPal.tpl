<!DOCTYPE html>
<html>

    <head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1" />          
        <link rel="stylesheet" href="../styl_common.css" type="text/css">
	<link rel="stylesheet" href="./splitPal.css" type="text/css">

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

	<script type = "text/javascript" src = "./splitPal.js"></script>

	<title>
	    APL Abydos - Split Pal
	</title>    
    </head>
    <body>
	{include file='../../templates/heading.tpl'}
	<div id="sizeinfo"></div>
	<div id="mainform">
	<fieldset style="border: none;margin-top: 10px;border-bottom: 1px solid;margin-bottom: 10px;">
	    <legend>Von :</legend>
	    <label for="import">Import</label>
	    <input type="number" accesskey="i" id="import" value="{$import}" autofocus />
	    <label for="pal1">Pal von</label>
	    <input type="number" accesskey="p" id="pal1" value="" />
	    <span id="exInfo">schon Exportiert</span>
	</fieldset>

	<div style="border-bottom: 1px solid black;margin-bottom: 10px;">
    	<fieldset style="float: left;border: none;">
	    <legend>Import</legend>
	    <div id="dauftrtable" style="margin:auto;"></div>
	</fieldset>

	    <fieldset style="border:none;">
	    <legend>Rueckmeldungen</legend>
	    <div id="ruecktable" style="margin:auto;"></div>
	</fieldset>
		<hr style="visibility: hidden;clear: both;">
	</div>    
	
	
	<fieldset style="border:none;">
	    <legend>Nach :</legend>
	    <label for="import">Import</label>
	    <input type="number" disabled="disabled" readonly accesskey="i" id="import1" value="{$import}"/>
	    <label for="pal2">Pal nach</label>
	    <input type="number" accesskey="p" id="pal2" value="" />
	    <span id="pal2ExistsInfo">pal existiert !!!</span>
	    <label for="pal2stk">Stk</label>
	    <input type="number" id="pal2stk" value="0" />
	    <label for="persnr">Persnr</label>
	    <input type="number" id="persnr" value="0" />
	    <input type="button" id="gosplit" value="Split !" />
	</fieldset>
	    
	    
	    <div id="dauftrLog">
		
	    </div>
	</div>
    </body>
</html>

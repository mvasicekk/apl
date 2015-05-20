<!DOCTYPE html>
<html>

    <head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1" />          
        <link rel="stylesheet" href="../styl_common.css" type="text/css">
	<link rel="stylesheet" href="./showquery1.css" type="text/css">

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

	<script type = "text/javascript" src = "./showquery1.js"></script>

	<title>
	    Showquery1
	</title>    
    </head>
    <body>
	{include file='../../templates/heading.tpl'}
	<div id="formular_header">
	    {$label}
	    &nbsp;&nbsp; Suchen:&nbsp;<input type="text" id="searchField" name="searchField" />
	</div>
	
	<div id="mainform">
	    {if strlen(trim($filter))>0}
	    <div id="filterDiv">
		{$filter}
	    </div>
	    {/if}
	    {if strlen(trim($par))>0}
	    <div id="filterDiv">
		{$par}
	    </div>
	    {/if}
	    <input type="hidden" id="sqlField" value="{$sql}" />
{*	<fieldset>
	    <legend>Info</legend>
	    {$sql}
	    
	</fieldset>
	
	<fieldset>
	    <legend>Table</legend>
	    
*}	
	
	<div id="querytable" style="margin:auto;"></div>
{*	</fieldset>*}
	</div>
    </body>
</html>

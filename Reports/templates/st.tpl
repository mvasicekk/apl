<!DOCTYPE html>
<html>
    <head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<title>
	    Schluesseltabellen / číselníky
	</title>
	<link rel="stylesheet" href="../styl_common.css" type="text/css">
	<link rel="stylesheet" href="./styl.css" type="text/css">
    </head>
	
    <body>
	{include file='../../templates/heading.tpl'}
	<div id="formular_header">
	    Schluesseltabellen / číselníky
	</div>

{*	<div>
	    <pre>
		{$display_sec}
	    </pre>
	</div>
*}	{if $prihlasen}
	    <div id="formular_telo">
		<div id='buttoncontainer'>
		    <div id='st_S100' style="display:{$display_sec.st_S100}">
			{foreach from=$querys item=q key=k}
			    {if $q.showButton}
{*				<input id="{$k}" onClick="location.href='./showquery1.php?label={$q.buttonName}&sql={$q.sql}&filter={$q.filter}';" class='reportbutton' type="button"  name="{$k}" value="{$q.buttonName}"/>*}
				<input id="{$k}" onClick="location.href='../get_st_parameters.php?popisky={$q.popisky}&promenne={$q.promenne}&values={$q.values}&query={$k}&label={$q.buttonName}&sql={$q.sql}&filter={$q.filter}';" class='reportbutton' type="button"  name="{$k}" value="{$q.buttonName}"/>
			    {/if}
			{/foreach}
		    </div>
		</div>
	    </div>
	{/if}
    
    </body>
</html>
    
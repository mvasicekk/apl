<!DOCTYPE html>
<html>
    <head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<title>
	    Schluesseltabellen / číselníky
	</title>
{*	<link rel="stylesheet" href="../styl_common.css" type="text/css">*}
{*	<link rel="stylesheet" href="./styl.css" type="text/css">*}
	
{*	Bootstrap*}
	<link href="../libs/bootstrap/css/bootstrap.min.css" rel="stylesheet">
	
	<!--jQuery dependencies-->
{*	<link rel="stylesheet" href="../js/jquery-ui-1.10.4/themes/base/jquery-ui.css" />*}
	<script src="../js/jquery-1.11.0.min.js" type="text/javascript"></script>
{*	<script src="../js/jquery-ui-1.11.0/jquery-ui.min.js" type="text/javascript"></script>*}

{*	Bootstrap*}
	<script src="../libs/bootstrap/js/bootstrap.min.js"></script>
    </head>
	
    <body>
	{include file='../../templates/headingBS.tpl'}
{*	<div>
	    <pre>
		{$display_sec}
	    </pre>
	</div>
*}	{if $prihlasen}
	    <div class='container-fluid' id="formular_telo">
		<div class='panel panel-primary' id='buttoncontainer'>
		    <div class='panel-heading'>
			<h3 class="panel-title">
			    Schluesseltabellen / číselníky
			</h3>
		    </div>
		    <div class='panel-body'>
			<div id='st_S100' style="display:{$display_sec.st_S100}">
			    <div class='row'>
				{foreach from=$querys item=q key=k}
				    {if $q.showButton}
					<div class='col-md-3 col-sm-4'>
					    <button style='text-align:left;' class='btn btn-lg btn-default btn-block' id="{$k}" onClick="location.href='../get_st_parameters.php?popisky={$q.popisky}&promenne={$q.promenne}&values={$q.values}&query={$k}&label={$q.buttonName}&sql={$q.sql}&filter={$q.filter}&tabid={$k}';">
{*					    <input class='btn btn-lg btn-default btn-block' id="{$k}" onClick="location.href='../get_st_parameters.php?popisky={$q.popisky}&promenne={$q.promenne}&values={$q.values}&query={$k}&label={$q.buttonName}&sql={$q.sql}&filter={$q.filter}';" class='reportbutton' type="button"  name="{$k}" value="{$q.buttonName}">*}
						<span class="glyphicon {$q.icon}" aria-hidden="true"></span> {$q.buttonName}
					    </button>
					</div>
				    {/if}
				{/foreach}
			    </div>
			</div>
		    </div>
		</div>
	    </div>
	{/if}
    
    </body>
</html>
    
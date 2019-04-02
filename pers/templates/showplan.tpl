<!DOCTYPE html>
<html>
    <head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<title>
	    Personal Plan
	</title>
	{*	Bootstrap*}
	<link href="../libs/bootstrap/css/bootstrap.min.css" rel="stylesheet">

	<!--jQuery dependencies-->

	{*	<link rel="stylesheet" href="../js/jquery-ui-1.10.4/themes/base/jquery-ui.css" />*}
	<script src="../libs/bower_components/jquery/dist/jquery.min.js" type="text/javascript"></script>
	<script src="../libs/bower_components/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
	<link href="../libs/bower_components/jquery-ui/themes/smoothness/jquery-ui.min.css" rel="stylesheet">

	<script src="../libs/bootstrap/js/bootstrap.min.js"></script>
	<link href="./css/style.css" rel="stylesheet">
    </head>

    <body>
	{include file='../../templates/headingBS.tpl'}

	<div class="container-fluid">
    <div class="page-header">
	<div class="row">
	    <div class="col-sm-12">
		<h2>Personal Plan - den / Tag / day <span class="label label-danger">{$datum} ( {$jmenoDneCZ}, {$jmenoDneE} )</span> <span class="label label-info">OE: {$oe}</span></h2>
	    </div>
	</div>
    </div>
  

{if count($osoby)>0}
    <div class="row">
	{foreach from=$osoby item=m}
	<div class="col-lg-3 col-md-4 col-sm-6">
	    <!--vychytavka - cervene lidi, kteri nejsou podle dochazky :-)-->
	    <div class="panel {$m.panelclass}">
		<div class="panel-heading">
		    <h4>{$m.persnr} 
			{if $m.edata_min!=NULL}<span class='edatabadge badge badge-important'>(od:{$m.edata_min})</span>{/if}
			{if $m.edata_max!=NULL && $m.edata_max!=$m.edata_min}<span class='edatabadge badge badge-important'>(do:{$m.edata_max})</span>{/if}
		    </h4>
		    <h5>{$m.vorname} {$m.name} <span class="label label-primary">{$m.oe} ({$m.stunden})</span></h5>
		</div>
	    </div>
	</div>
	{/foreach}
    </div>
{/if}		

		
</div>
</body>
</html>
    
<!DOCTYPE html>
<html>
    <head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<title>
	    Schluesseltabellen / číselníky
	</title>
{*	Bootstrap*}
	<link href="../libs/bootstrap/css/bootstrap.min.css" rel="stylesheet">
	
	<!--jQuery dependencies-->
{*	<link rel="stylesheet" href="../js/jquery-ui-1.10.4/themes/base/jquery-ui.css" />*}
	<script src="../js/jquery-1.11.0.min.js" type="text/javascript"></script>
{*	Bootstrap*}
	<script src="../libs/bootstrap/js/bootstrap.min.js"></script>
	
	
	<script src="../libs/bower_components/angular/angular.min.js"></script>
	<script src="../libs/bower_components/angular-route/angular-route.min.js"></script>
	
	<script src="../libs/bower_components/angular-sanitize/angular-sanitize.min.js"></script>
	<script src="../libs/bower_components/angular-bootstrap/ui-bootstrap-tpls.min.js"></script>
    
	<script src="../libs/bower_components/angular-thumbnails/dist/angular-thumbnails.min.js"></script>
	<script src="../libs/bower_components/ng-file-upload/ng-file-upload.min.js"></script>

	<script src="../libs/bower_components/tinymce-dist/tinymce.min.js"></script>
	<script src="../libs/bower_components/angular-ui-tinymce/dist/tinymce.min.js"></script>
    
	<script src="./js/app.js"></script>
	<script src="./js/controllers.js"></script>
	<script src="./js/directives.js"></script>
	<script src="../js/filters.js"></script>
    
	<link href="./css/style.css" rel="stylesheet">
    </head>
	
    <body ng-app="stApp">
	{include file='../../templates/headingBS.tpl'}

	{if $prihlasen}
	    <div class='container-fluid' id="formular_telo" >
		{if $typcountarray.schltabelle>0}
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
				    {if $q.showButton && $q.form_typ=='schltabelle'}
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
		{/if}	    
		{if $typcountarray.eform>0}
		<div class='panel panel-success' id='buttoncontainer'>
		    <div class='panel-heading'>
			<h3 class="panel-title">
			    e-forms
			</h3>
		    </div>
		    <div class='panel-body'>
			<div id='st_Sefom'>
			    <div class='row'>
				<div class="col-sm-3">
				    {foreach from=$querys item=q key=k}
				    {if $q.showButton && $q.form_typ=='eform'}
					    <a style='text-align:left;' class='btn btn-sm btn-default btn-block' id="{$k}" href="#/{$q.form_typ}/{$q.tabid}">
						<span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span> {$q.buttonName}
					    </a>
				    {/if}
				{/foreach}
				</div>
				{literal}
				<div ng-view class="col-sm-9">
				</div>
				{/literal}
			    </div>
			</div>
		    </div>
		</div>
		{/if}
	    </div>
			    
{*        {literal}
	<div ng-view></div>
	{/literal}
*}
	
	{/if}
    
    </body>
</html>
    
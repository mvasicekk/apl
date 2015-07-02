<html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
      Dbehexport
    </title>

<link rel="stylesheet" href="../libs/bower_components/bootstrap/dist/css/bootstrap.min.css" type="text/css">    


<script type="text/javascript" src="../libs/bower_components/jquery/dist/jquery.js"></script>
{*<script type="text/javascript" src="../libs/bower_components/angular/angular.min.js"></script>*}
<script type="text/javascript" src="../libs/bower_components/angular/angular.js"></script>
{*<script type="text/javascript" src="../libs/bower_components/angular-sanitize/angular-sanitize.min.js"></script>*}
<script type="text/javascript" src="../libs/bower_components/angular-sanitize/angular-sanitize.js"></script>
{*<script type="text/javascript" src="../libs/bower_components/angular-ui-select/dist/select.min.js"></script>*}
<script type="text/javascript" src="../libs/bower_components/angular-ui-select/dist/select.js"></script>
{*<link rel="stylesheet" href="../libs/bower_components/angular-ui-select/dist/select.min.css" type="text/css">    *}
<link rel="stylesheet" href="../libs/bower_components/angular-ui-select/dist/select.css" type="text/css">    
{*<script type="text/javascript" src="../libs/bower_components/angular-ui-grid/ui-grid.js"></script>
<link rel="stylesheet" href="../libs/bower_components/angular-ui-grid/ui-grid.css" type="text/css">    
*}
<script type="text/javascript" src="../libs/bower_components/numeral/numeral.js"></script>

<link rel="stylesheet" href="./styl.css" type="text/css">
<link rel="stylesheet" href="../styldesign.css" type="text/css">

<script type="text/javascript" src="../js/app.js"></script>
<script type="text/javascript" src="../js/controllers.js"></script>
<script type="text/javascript" src="../js/filters.js"></script>

</head>

<body class="container-fluid" ng-app="aplApp" ng-controller="dbehexportController">
    {literal}
	<div class="row">
	    <div class="col-md-12">
		<form class="form-inline">
		    <div class="row">
			<div class="col-md-3">
    			    <div class="form-group">
				<label class="sr-only" for="export">Export</label>
				<ui-select
				    ng-model="export.selected" 
				    ng-disabled="disabled" 
				    reset-search-input="true" 
				    on-select="exportSelected()"
				    theme="bootstrap">
				    <ui-select-match maxlength="8" placeholder="Export">{{$select.selected.ex}}</ui-select-match>
				    <ui-select-choices repeat="e in exporte track by $index"
					refresh="refreshExporte($select.search)"
					refresh-delay="0">
					<div ng-bind-html="e.formattedExport | highlight: $select.search"></div>
				    </ui-select-choices>
				</ui-select>
			    </div>
			</div>
			
			<div class="col-md-3">
			    <div class="form-group">
				<label class="sr-only" for="teil1">teil1</label>
				<ui-select
				    ng-model="teil1.selected" 
				    ng-disabled="disabled" 
				    reset-search-input="true" 
				    theme="bootstrap"
				    on-select="teilSelected()"
				    >
				    <ui-select-match placeholder="Teilnr">{{$select.selected.teil}}</ui-select-match>
				    <ui-select-choices repeat="t in teile track by $index"
					refresh="refreshTeile($select.search)"
					refresh-delay="0">
					<div ng-bind-html="t.formattedTeil | highlight: $select.search"></div>
				    </ui-select-choices>
				</ui-select>
			    </div>
			</div>

			<div class="col-md-3">
			    <div class="form-group">
				<label class="sr-only" for="stk">Stk</label>
				<input type="number" ng-model="stk" ng-change="updatePalCount();" class="form-control" id="stk" placeholder="Stk">
			    </div>
			</div>
		    </div>
		</form>
	    </div>
	</div>
	
	<div class="row">
	    <div class="col-md-3">
		VerpMenge: {{teil1.selected.verpackungmenge}} Stk
	    </div>
	    <div class="col-md-4">
		->{{stk}} Stk = {{countPalVoll}} x {{verpmenge}} Stk + {{restPal}} x {{verpRest}} Stk= {{sumPal}} Pal
	    </div>
	</div>
	
	<div class="row">
	    <div class="col-md-12">
		<div class="behTable">
		    <table class="table table-condensed table-striped">
			<thead>
			    
			</thead>
			<tbody>
			    <tr ng-repeat="beh in behData">
				<td ng-repeat="(k,v) in beh">{{v}}</td>
			    </tr>
			</tbody>
		    </table>
		</div>
	    </div>
	</div>
    {/literal}
</body>
</html>

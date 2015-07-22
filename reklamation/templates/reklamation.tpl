<!DOCTYPE html>
<html>
    <head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<title>
	    Reklamation
	</title>

<!--jQuery dependencies-->
{*	<link rel="stylesheet" href="../js/jquery-ui-1.10.4/themes/base/jquery-ui.css" />*}
    <script src="./bower_components/jquery/dist/jquery.min.js" type="text/javascript"></script>

{*	Bootstrap*}
    <link href="./bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="./bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
{*	<script src="../js/jquery-ui-1.11.0/jquery-ui.min.js" type="text/javascript"></script>*}

    <script src="./bower_components/angular/angular.min.js"></script>
    <script src="./bower_components/angular-smart-table/dist/smart-table.min.js"></script>
    <script src="./js/app.js"></script>
    <script src="./js/controllers.js"></script>
</head>
	
<body ng-app="reklApp" ng-controller="reklController">
    {include file='../../templates/headingBS.tpl'}
    {literal}
	
	<div class="container-fluid">
	    <table st-table="dReklamationen" st-safe-src="reklamationen" class="table table-bordered table-striped table-condensed table-hover">
		<thead>
		    <tr>
			<th colspan="11"><input st-search="" class="form-control" placeholder="global search ..." type="text"/></th>
		    </tr>
		    <tr>
			<th>Kunde</th>
			<th>ReklNr</th>
			<th>Kd ReklNr</th>
			<th>Kd Kd ReklNr</th>
			<th>IM</th>
			<th>EX</th>
			<th st-sort="rekl_datum">Festgelegt am</th>
			<th>TeileNr</th>
			<th>reklamierte Menge</th>
			<th>Beschreibung der Abweichung</th>
			<th>Bemerkung</th>
		    </tr>
		</thead>
		<tbody>
		    <tr ng-repeat="r in dReklamationen">
			<td>{{r.kunde}}</td>
			<td>{{r.rekl_nr}}</td>
			<td>{{r.kd_rekl_nr}}</td>
			<td>{{r.kd_kd_rekl_nr}}</td>
			<td>{{r.import}}</td>
			<td>{{r.export}}</td>
			<td>{{r.rekl_datum}}</td>
			<td>{{r.teil}}</td>
			<td>{{r.stk_reklammiert}}</td>
			<td>{{r.beschr_abweichung}}</td>
			<td>{{r.bemerkung}}</td>
		    </tr>
		</tbody>
		<tfoot>
		    <tr>
			<td colspan="11" class="text-center">
			    <div st-pagination="true" st-items-by-page="50" st-displayed-pages="10"></div>
			</td>
		    </tr>
		</tfoot>
	    </table>
	</div>
    {/literal}
</body>
</html>
    
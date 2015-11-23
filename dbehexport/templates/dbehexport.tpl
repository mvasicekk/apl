<html>
    <head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>
	    Dbehexport
	</title>

	<!--jQuery dependencies-->
	{*	<link rel="stylesheet" href="../js/jquery-ui-1.10.4/themes/base/jquery-ui.css" />*}
	<script src="./bower_components/jquery/dist/jquery.min.js" type="text/javascript"></script>
	<script src="./bower_components/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
	<link href="./bower_components/jquery-ui/themes/smoothness/jquery-ui.min.css" rel="stylesheet">

	{*	Bootstrap*}
	<link href="./bower_components/bootstrap/dist/css/bootstrap.css" rel="stylesheet">
	<script src="./bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
	{*	<script src="../js/jquery-ui-1.11.0/jquery-ui.min.js" type="text/javascript"></script>*}

	<script src="./bower_components/angular/angular.min.js"></script>
	<script src="./bower_components/angular-route/angular-route.min.js"></script>
	<script src="./bower_components/angular-ui-date/src/date.js"></script>

	<script src="./bower_components/angular-smart-table/dist/smart-table.min.js"></script>

	<script type="text/javascript" src="../plupload/js/plupload.full.js"></script>

	<script src="./bower_components/angular-sanitize/angular-sanitize.min.js"></script>
	<script src="./bower_components/angular-ui-select/dist/select.min.js"></script>
	<link href="./bower_components/angular-ui-select/dist/select.min.css" rel="stylesheet">

	<script src="./bower_components/angular-bootstrap/ui-bootstrap-tpls.min.js"></script>

	<script src="./bower_components/floatThead/dist/jquery.floatThead.min.js"></script>
	<script src="./bower_components/numeral/numeral.js"></script>

	<link href="./css/style.css" rel="stylesheet">
	{*<link href="./css/style_det.css" rel="stylesheet">
	<link rel="stylesheet" href="./styl.css" type="text/css">
	<link rel="stylesheet" href="../styldesign.css" type="text/css">
	*}
	<script type="text/javascript" src="./js/app.js"></script>
	<script type="text/javascript" src="./js/controllers.js"></script>
	<script type="text/javascript" src="../js/filters.js"></script>

    </head>

    <body ng-app="dbehexportApp" ng-controller="dbehexportController">
	{include file='../../templates/headingBS.tpl'}
	{literal}
	    <div class="container-fluid">
		<div class="page-header">
		    <h4>Dbehexport</h4>
		</div>
		<div class="row">
		    <div class="col-sm-6">
			<div class="row">
			    <div class="col-sm-4">
				<div class="form-group">
				    <label for="export">Export</label>
				    <ui-select
					ng-model="export.selected" 
					ng-disabled="disabled" 
					reset-search-input="false" 
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
			    <div class="col-sm-4">
				<div class="form-group">
				    <label for="teil1">Teil</label>
				    <ui-select
					ng-model="teil1.selected" 
					ng-disabled="disabled" 
					reset-search-input="false" 
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
			    <div class="col-sm-4">
				<div class="form-group">
				    <label for="stk">Stk</label>
				    <input type="number" ng-model="stk" ng-change="updatePalCount()" class="form-control" id="stk" placeholder="Stk">
				</div>
			    </div>
			</div>
		    </div>
		    <div class="col-sm-6">
			<p>
			    VerpMenge: {{teil1.selected.verpackungmenge}} Stk
			</p>
			<p>
			    {{stk}} Stk = {{countPalVoll}} x {{verpmenge}} Stk + {{restPal}} x {{verpRest}} Stk= {{sumPal}} Pal
			</p>   
			<p ng-show="sumPal>0">
			    <button class='btn btn-default no-print' ng-click='saveZettel()'>Freigabezettel speichern</button>
			</p>
			<div ng-show="nochNichtGedruckt>0" class="no-print">
			    <a ng-click="zettelDruckClick()" class='btn btn-default no-print' target='_blank' ng-href="../Reports/D64Z_pdf.php?export={{export.selected.ex}}&a5={{a5}}&pal2x={{pal2x}}">Zettel drucken</a>
			    <div class="checkbox">
				<label>
				    <input type="checkbox" ng-true-value="'1'" ng-false-value="'0'" ng-model="pal2x"> jeder Behaelter 2x
				</label>
			    </div>
			    <div class="checkbox">
				<label>
				    <input type="checkbox" ng-true-value="'1'" ng-false-value="'0'" ng-model="a5"> auf A5 drucken
				</label>
			    </div>
			</div>
		    </div>
		</div>


		<div class="row">
		    <div class="col-md-12">
			<div class="behTable">
			    <table ng-show="zeilenArray.length>0" class="table table-condensed table-striped table-bordered">
				<caption>Was habe ich ?</caption>
				<thead>
				    <tr>
					<th ng-show="mitPal" colspan="5">
					    Auftraege ( ohne Export )
					    <div class="checkbox-inline">
						<label class="checkbox-inline">
						    <input ng-model="mitPal" type="checkbox">Pal zeigen
						</label>
					    </div>
					</th>
					<th ng-show="!mitPal" colspan="2">
					    Auftraege ( ohne Export )
					    <div class="checkbox-inline">
						<label class="checkbox-inline">
						    <input ng-model="mitPal" type="checkbox">Pal zeigen
						</label>
					    </div>
					</th>
					<th class="drueck" colspan="{{1 + abgnrKeysArray.length + aartKeysArray.length}}">
					    Drueck
					</th>
				    </tr>
				    <tr>
					<th>
					    Teil
					</th>
					<th ng-show="mitPal">
					    Import
					</th>
					<th  ng-show="mitPal" class="text-right">
					    ImPal
					</th>
					<th class="text-right">
					    Im Stk
					</th>
					<th  ng-show="mitPal">
					    Plan
					</th>
					<th class="text-right drueck">
					    G Stk
					</th>
					<th style="vertical-align: middle;" class="text-right drueck" ng-repeat="tat in abgnrKeysArray">{{tat}}</th>
					<th style="vertical-align: middle;" class="text-right drueck" ng-repeat="auss in aartKeysArray">A {{auss}}</th>
				    </tr>
				</thead>
				<tbody>
				    <tr class="{{beh.section}}" ng-repeat="beh in zeilenArray" ng-init="zeilenIndex = $index">
					<td ng-show="beh.section=='detail' && mitPal">{{beh.teil}}</td>
					<td ng-show="beh.section=='detail' && mitPal">{{beh.import}} ({{beh.import_datum}})</td>
					<td ng-show="beh.section=='detail' && mitPal" class="text-right">{{beh.pal}}</td>
					<td ng-show="beh.section=='detail' && mitPal" class="text-right">{{beh.palInfo.sum_im_stk}}</td>
					<td ng-show="beh.section=='detail' && mitPal">{{beh.termin}}</td>
					<td ng-show="beh.section=='detail' && mitPal" class="text-right">{{zeilenDArray[zeilenIndex].palInfo.sum_G_stk}}</td>
					<td ng-class="beh.section" ng-if="beh.section=='detail' && mitPal" class="text-info text-right" ng-repeat="tat in abgnrKeysArray">
					    <span ng-if="beh.palInfo.hasOwnProperty(tat)" title="abgnr:{{tat}}">
						{{zeilenDArray[zeilenIndex].palInfo[tat].sum_gut_stk}}
					    </span>
					</td>
					<td ng-class="{leftBorderDetail:$first,rightBorderDetail:$last}" ng-if="beh.section=='detail' && mitPal" class="text-info text-right {{beh.section}}" ng-repeat="auss in aartKeysArray">
					    {{zeilenDAArray[zeilenIndex].palInfo[auss].sum_auss_stk}}
					</td>
					
					<td ng-show="beh.section=='sumteil'">Summe {{beh.teil}}</td>
					<td ng-show="beh.section=='sumteil' && mitPal"></td>
					<td ng-show="beh.section=='sumteil' && mitPal"></td>
					<td ng-show="beh.section=='sumteil'" class="text-right">{{beh.palInfo.sum_im_stk}}</td>
					<td ng-show="beh.section=='sumteil' && mitPal"></td>
					<td ng-show="beh.section=='sumteil'" class="text-right">{{zeilenDArray[zeilenIndex].palInfo.sum_G_stk}}</td>
					<td ng-class="beh.section" ng-if="beh.section=='sumteil'" class="text-info text-right" ng-repeat="tat in abgnrKeysArray">
					    <span ng-if="beh.palInfo.hasOwnProperty(tat)" title="abgnr:{{tat}}">
						{{zeilenDArray[zeilenIndex].palInfo[tat].sum_gut_stk}}
					    </span>
					</td>
					<td ng-class="{leftBorderDetail:$first,rightBorderDetail:$last}" ng-if="beh.section=='sumteil'" class="text-info text-right {{beh.section}}" ng-repeat="auss in aartKeysArray">
					    {{zeilenDAArray[zeilenIndex].palInfo[auss].sum_auss_stk}}
					</td>
				    </tr>
				</tbody>
			    </table>
			</div>
		    </div>
		</div>
		
		<div class="row">
		    <div class="col-sm-6">
			<table  ng-show="behArray.length>0" class="table table-condensed table-striped table-bordered">
			    <caption>Freigabezettel fuer Paletten in Export/Teil</caption>
			    <thead>
				<tr>
				    <th>Teil</th>
				    <th class="text-right">Ex Pal</th>
				    <th  class="text-right">Ex Stk</th>
				    <th  class="text-right">von Import</th>
				    <th  class="text-right">Im Pal</th>
				    <th class="text-center">nicht in Export</th>
				    <th>gedruck am</th>
				</tr>
			    </thead>
			    <tbody>
				<tr ng-class="{not_in_ex:pal.nicht_in_export=='1'}" ng-repeat="pal in behArray">
				    <td>{{pal.teil}}</td>
				    <td class="text-right">{{pal.ex_pal}}</td>
				    <td class="text-right">{{pal.ex_stk_gut}}</td>
				    <td class="text-right">
					<input type="text" ng-change="palBehChanged(pal,'import')" ng-model="pal.import" maxlength="8" size="8"/>
				    </td>
				    <td class="text-right">
					<input type="text" ng-change="palBehChanged(pal,'im_pal')" ng-model="pal.im_pal" maxlength="255" size="10"/>
				    </td>
				    <td class="text-center">
					<input type="checkbox" ng-model="pal.nicht_in_export" ng-true-value="'1'" ng-false-value="'0'" ng-change="nichtInExportChanged(pal)"/>
				    </td>
				    <td>{{pal.gedruckt_am|date}}</td>
				</tr>
			    </tbody>
			</table>
		    </div>
		</div>
	    </div>
	{/literal}
    </body>
</html>

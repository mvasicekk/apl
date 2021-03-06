<div class="container-fluid">
    <table st-table="dReklamationen" st-safe-src="reklamationen" class="table table-bordered table-striped table-condensed table-hover">
	<thead>
	    <tr>
		<th colspan="10"><input st-search="" class="form-control" placeholder="global suchen / hledat ..." type="text"/></th>
		<th colspan="1">zeige: <span class="badge">{{dReklamationen.length}}</span> / gesamt: <span class="badge">{{reklamationen.length}}</span></th>
	    </tr>
	    <tr>
		<th st-sort="kunde">Kunde</th>
		<th st-sort="rekl_nr">ReklNr</th>
		<th>Kd ReklNr</th>
		<th>Kd Kd ReklNr</th>
		<th>IM</th>
		<th>EX</th>
		<th st-sort="rekl_datum">Festgelegt am</th>
		<th st-sort="teil">TeileNr</th>
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

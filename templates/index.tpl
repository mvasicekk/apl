<!DOCTYPE html>
<html lang="cs">
    <head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<title>
	    APL Abydos
	</title>    
	<script type="text/javascript" src="./js/jquery-1.11.0.min.js"></script>
	
	<script type="text/javascript" src="./js/jquery.jqplot.min.js"></script>
	<link rel="stylesheet" type="text/css" href="./js/jquery.jqplot.min.css"/>
	
	<script type="text/javascript" src="./js/plugins/jqplot.barRenderer.min.js"></script>
	<script type="text/javascript" src="./js/plugins/jqplot.categoryAxisRenderer.min.js"></script>
	<script type="text/javascript" src="./js/plugins/jqplot.canvasTextRenderer.min.js"></script>
	<script type="text/javascript" src="./js/plugins/jqplot.canvasAxisLabelRenderer.min.js"></script>
	<script type="text/javascript" src="./js/plugins/jqplot.canvasOverlay.min.js"></script>
	<script type="text/javascript" src="./js/plugins/jqplot.highlighter.min.js"></script>
	<script type="text/javascript" src="./js/plugins/jqplot.cursor.min.js"></script>
	
{*	<script src="./brany/bower_components/jquery/dist/jquery.min.js"></script>*}
<!--{*	Bootstrap*}-->
    <link href="./brany/bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="./brany/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
    
    <script src="./brany/js/brany.js"></script>
{*    <link href="./brany/css/brany.css" rel="stylesheet">*}
    
	<link rel="stylesheet" href="./styl_common.css" type="text/css">
	<link rel="stylesheet" href="./styl.css" type="text/css">
	
	<script type="text/javascript" src="./apl.js"></script>
	{literal}
	<style>
	    input.abyStartButton{
/*		width: 10em;*/
	    }
	</style>
	{/literal}
    </head>
    <body>
	{include file='heading.tpl'}
	{if $prihlasen}
	    <div id="tlacitka">
		<fieldset class='buttonsection personal'>
		    <legend>Personal</legend>
		    <input style="display:{$display_sec.dzeitedata};" type="button" value="Anwesenheitserfassung lt. Leistung" id="dzeitedata" class="abyStartButton"  onClick="location.href='./personal/doc_root/index.php?action=edataAnw&presenter=DpersAnwesenheit'" />
		    <input style="display:{$display_sec.dpers};" type="button" value="Personal Pflegen" id="dpers" class="abyStartButton"  onClick="okno=window.open();okno.location.href='./personal/doc_root/index.php?presenter=Persinfo'" />
		    <input style="display:{$display_sec.dzeit};" type="button" value="Anwesenheitserfassung" id="dzeit" class="abyStartButton"  onClick="location.href='./dzeit/dzeit.php'" />
		    <input style="display:{$display_sec.anwesenheitplan};" type="button" value="Anwesenheitplanung" id="anwesenheitplan" class="abyStartButton"  onClick="location.href='./personal/doc_root/index.php?action=planAnwesenheit&presenter=DpersAnwesenheit'" />
		    <input style="display:{$display_sec.vorschuss};" type="button" value="Vorschuss" id="vorschuss" class="abyStartButton"  onClick="location.href='./dpers/vorschuss.php'" />
		</fieldset>

		<fieldset class='buttonsection kunden'>
		    <legend>Kunden / zakaznici</legend>
		    <input style="display:{$display_sec.kundepflegen};" type="button" value="Kunden Pflegen" id="kundepflegen" class="abyStartButton"  onClick="okno=window.open();okno.location.href='./dksd/kundesuchen.php'" />
		    <input style="display:{$display_sec.telbuch};" type="button" value="Telefonbuch" id="telbuch" class="abyStartButton"  onClick="okno=window.open();okno.location.href='./telbuch/telbuch.php'" />
		</fieldset>

		<fieldset class='buttonsection auftraege'>
		    <legend>Auftraege / zakazky</legend>
{*		    <input style="display:{$display_sec.daufkopf};" type="button" value="Aufträge Pflegen" id="daufkopf" class="abyStartButton" onClick="okno=window.open();okno.location.href='./dauftr/auftragsuchen.php'" />*}
		    <input style="display:{$display_sec.daufkopf};" type="button" value="Aufträge Pflegen" id="daufkopf" class="abyStartButton" onClick="okno=window.open();okno.location.href='./auftrag/auftrag.php#/list'" />
{*		    <input style="" type="button" value="Aufträge Pflegen" id="daufkopf" class="abyStartButton" onClick="okno=window.open();okno.location.href='./dauftr/auftragsuchen.php'" />*}
		    <input style="display:{$display_sec.dkopf};" type="button" value="Arbeitsplan Pflegen" id="dkopf" class="abyStartButton" onClick="okno=window.open();okno.location.href='./dkopf/teilsuchen.php'" />
		    <input style="display:{$display_sec.umtermin};" type="button" value="Umterminieren" id="umtermin" class="abyStartButton"  onClick="okno=window.open();okno.location.href='./dauftr/umterminieren/umterminieren.php'" />
	<!--	    <input style="display:{$display_sec.cmr};" type="button" value="CMR" id="cmr" class="abyStartButton" onClick="location.href=''" />-->
{*		    <input style="display:{$display_sec.rundlauf};" type="button" value="Rundlauf" id="rundlauf"  class="abyStartButton" onClick="okno=window.open();okno.location.href='./napl/www/?action=rundlauf&presenter=Dispo'" />*}
		    <input style="display:{$display_sec.dispo};" type="button" value="Dispo" id="dispo"  class="abyStartButton" onClick="okno=window.open();okno.location.href='./dispo_2/dispo.php'" />
{*		    <input style="display:{$display_sec.reklamation};" type="button" value="Reklamation" id="reklamation"  class="abyStartButton" onClick="okno=window.open();okno.location.href='./napl/www/?action=reklamation&presenter=Reklamation&uziv={$user}'" />*}
		    <input style="display:{$display_sec.reklamation};" type="button" value="Reklamation" id="reklamation"  class="abyStartButton" onClick="okno=window.open();okno.location.href='./reklamation/reklamation.php'" />
		    <input style="display:{$display_sec.infopanely};" type="button" value="Infopanely" id="infopanely" class="abyStartButton"  onClick="okno=window.open();okno.location.href='./infopanely/places.php'" />
		</fieldset>

		<fieldset class='buttonsection rm'>
		    <legend>Rueckmeldungen / hlaseni vykonu</legend>
		    <input style="display:{$display_sec.drueck};" type="button" value="Rückmeldung Leist" id="drueck" class="abyStartButton" onClick="okno=window.open();okno.location.href='./drueck/drueck.php'" />
		</fieldset>

		<fieldset class='buttonsection lager'>
		    <legend>Laeger / sklady</legend>
		    <input style="display:{$display_sec.lagerbew};" type="button" value="Lagerumbuchung / pridani do skladu" id="lagerbew" class="abyStartButton" onClick="location.href='./dlager/umbuchung.php'" />
		    <input style="display:{$display_sec.lagerstk};" type="button" value="Lager Inventur" id="lagerstk" class="abyStartButton" onClick="location.href='./dlagstk/dlagstk.php'" />
{*		    <input style="display:{$display_sec.behlagerbew};" type="button" value="Behaelterbewegung / palety pohyby" id="behlagerbew" class="abyStartButton" onClick="location.href='./dbehaelter/bewegung.php'" />*}
		    <input style="display:{$display_sec.behlagerinv};" type="button" value="Behaelterinventur / palety inventura" id="behlagerinv" class="abyStartButton" onClick="location.href='./dbehaelter/inventur.php'" />
		</fieldset>

		<fieldset class='buttonsection bericht'>
		    <legend>Berichte / sestavy</legend>
		    <input style="display:{$display_sec.berichte};" type="button" value="Berichte Drucken" id="berichte" class="abyStartButton" onClick="okno=window.open();okno.location.href='./Reports/reports.php'" />
		    <input style="display:{$display_sec.phpexcel};" type="button" value="PHPExcel - Exporte" id="phpexcel" class="abyStartButton" onClick="okno=window.open();okno.location.href='./Reports/excelreports.php'" />
		    <input style="display:{$display_sec.showquery};" type="button" value="Schlüsseltabellen zeigen" id="showquery" class="abyStartButton" onClick="okno=window.open();okno.location.href='./Reports/st.php'" />
{*		    <input style="" type="button" value="Berichte Drucken" id="berichte" class="abyStartButton" onClick="okno=window.open();okno.location.href='./Reports/reports.php'" />*}
{*		    <input style="" type="button" value="PHPExcel - Exporte" id="phpexcel" class="abyStartButton" onClick="okno=window.open();okno.location.href='./Reports/excelreports.php'" />*}
{*		    <input style="display:{$display_sec.showquery};" type="button" value="Schlüsseltabellen zeigen" id="showquery" class="abyStartButton" onClick="okno=window.open();okno.location.href='./Reports/querys.php'" />*}

		</fieldset>

		<fieldset class='buttonsection reparatur'>
		    <legend>Reparaturen</legend>
		    <input style="display:{$display_sec.repeingabe};" type="button" value="Reparaturen" id="repeingabe" class="abyStartButton" onClick="okno=window.open();okno.location.href='./reparaturen/repeingabe.php'" />
		</fieldset>
		<fieldset class='buttonsection einkauf'>
		    <legend>Einkauf / nákup</legend>
		    <input style="display:{$display_sec.eink_anforderung};" type="button" value="Anforderung/požadavek" id="eink_anforderung" class="abyStartButton" onClick="okno=window.open();okno.location.href='./einkauf/anforderung.php'" />
		</fieldset>
	    </div>
	{else}
	    <div id='logindiv'>
		<form action="index.php" method="post" id="loginform" class="loginform">
		    <table class="logintable" border=0>
			<tr class="login_username">
			    <td>
				<label for="username">
				    Benutzername / uzivatelske jmeno
				</label>
			    </td>
			    <td>
				<input class='paraminput' type="text" name="username" id="username">
			    </td>
			</tr>
			<tr class="login_password">
			    <td>
				<label for="password">
				    Kennwort / heslo
				</label>
			    </td>
			    <td>
				<input class='paraminput' type="password" name="password" id="password">
			    </td>
			</tr>
			<tr>
			    <td align="center" colspan="2">
				<input class='abyStartButton' type="submit" name="loginbutton" id="loginbutton" value="Anmelden/prihlasit">
			    </td>
			</tr>
		    </table>
		</form>
	    </div>
	{/if}

	{if $prihlasen}
	    <div id="leistungtabelle">
		<div id="tvfiles" style="width:100%;">
		    <div class="panel panel-info">
		    <a role="button" data-toggle="collapse" href="#tvinfo" aria-expanded="true" aria-controls="tvinfo">
			<div class="panel-heading">
			    <h4 class="panel-title panel-info">
				<span class="glyphicon glyphicon-film" aria-hidden="true"></span>
				&nbsp;TV
			    </h4>
			</div>
		    </a>
		    <div id="tvinfo" class="panel-collapse collapse">
			<div class="panel-body">
			    <table class="table">
				{foreach key=poradi item=file from=$tvFiles}
				    <tr>
					<td>
					    <a target='_blank' href="{$file.url}">{$file.filename}</a>
					</td>
				    </tr>
				{/foreach}
			    </table>
			</div>
		    </div>
		    </div>
		</div>
		
		<div style="display:{$display_sec.branydiv};width:100%" id="branydiv">
		    <input type="hidden" id="userinfo" value="{$user}" />
		    <div class="container-fluid">
			{*<div class="row">
			    <div class="col-xs-12 text-center">
				<span id="sock_status" class="badge">not connected</span>
			    </div>
			</div>*}
			 <div class="row">
				<div class="col-md-4 text-center">
				    <input value="" type="password" maxlength="4" class="form-control text-center" id="branaPin" placeholder="PIN" />
				    <button title="načte aktuální obraz z kamer" class="btn btn-block" onclick="window.location.reload();" id="branaRefreshButton">Camera refresh</button>
				</div>
				<div class="col-md-4 text-center">
{*				    <button class="btn btn-block" id="brana1Button">horní brana</button>*}
				    <button title="horní brána" class="btn btn-block" id="brana1Button">
				    <img id='brana1img' width="100%" src="http://a:a@172.16.1.102/Streaming/channels/801/picture" title="horní brána">
{*				    <hr>horní brána*}
				    </button>
				</div>
				<div class="col-md-4 text-center">
{*				    <button class="btn btn-block" id="brana2Button">dolní brana</button>*}
				    <button title="dolní brána" class="btn btn-block" id="brana2Button">
				    <img id='brana2img' width="100%" src="http://a:a@172.16.1.102/Streaming/channels/401/picture" title="dolní brána">
{*				    <hr>dolní brána*}
				    </button>
				</div>
			</div>
			<!--	    
			<div class="row">
			    <div class="col-md-4 text-center">
				<input value="" type="password" maxlength="4" class="form-control text-center" id="branaPin" placeholder="PIN" />
			    </div>
			    <div class="col-md-4 text-center">
				<button class="btn btn-block btn-default" id="brana1Button">horní brana</button>
			    </div>
			    <div class="col-md-4 text-center">
				<button class="btn btn-block btn-default" id="brana2Button">dolní brana</button>
			    </div>
			</div>
			-->
		    </div>
		</div>
<!--		<a title="kundenminuten aktuell" href="graph/leistung_akt_monat_gross.php" ><img border=0 src="graph/leistung_akt_monat.php"></a>-->
		<table class="monatleistungtable" border="0">

		    <tr>
			<td class="progresspopis">datum</td>
			<td colspan="2" class="progresspopis">Guss / % Ziel</td>
{*			<td class="progresspopis">PG3</td>*}
			<td colspan="2" class="progresspopis">NE / % Ziel</td>
{*			<td class="progresspopis">PG9</td>*}
			<td colspan="2" class="progresspopis">Summe / % Ziel</td>
		    </tr>

		    <tr>
			<td class="ganzmonat">akt. Monat</td>
			<td colspan="2" class="ganzmonat">{$sum_pg1|string_format:"%d"}</td>
{*			<td class="ganzmonat">{$sum_pg3|string_format:"%d"}</td>*}
			<td colspan="2" class="ganzmonat">{$sum_pg4|string_format:"%d"}</td>
{*			<td class="ganzmonat">{$sum_pg9|string_format:"%d"}</td>*}
			<td colspan="2" class="ganzmonat">{$sum_celkem|string_format:"%d"}</td>
		    </tr>

		    {foreach key=poradi item=polozka from=$pole}
			<tr>
			    <td class='progresspolozka'>{$polozka.datum}</td>
			    <td class='progresspolozka'>{$polozka.pg1|string_format:"%d"}</td>
			    <td class='progresspolozka'>{$polozka.ziel_pg1|string_format:"%d"}%</td>
{*			    <td class='progresspolozka'>{$polozka.pg3|string_format:"%d"}</td>*}
			    <td class='progresspolozka'>{$polozka.pg4|string_format:"%d"}</td>
			    <td class='progresspolozka'>{$polozka.ziel_pg4|string_format:"%d"}%</td>
{*			    <td class='progresspolozka'>{$polozka.pg9|string_format:"%d"}</td>*}
			    <td class='progresspolozka'>{$polozka.celkem|string_format:"%d"}</td>
			    <td class='progresspolozka'>{$polozka.ziel_sum|string_format:"%d"}%</td>
			</tr>
		    {/foreach}
		</table>
		<div id="myChart">
		</div>
	    </div>
	{/if}
    </body>
</html>

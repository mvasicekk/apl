<!DOCTYPE html>
<html>
    <head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<title>
	    APL Abydos
	</title>    
	<link rel="stylesheet" href="./styl.css" type="text/css">
	<script type="text/javascript" src="./js/jquery-1.11.0.min.js"></script>
	<script type="text/javascript" src="./apl.js"></script>
    </head>
    <body>
	{include file='heading.tpl'}

	{if $prihlasen}
	    <div align="center" id="tlacitka">
		<fieldset class='buttonsection personal'>
		    <legend>Personal</legend>
		    <input style="display:{$display_sec.dzeitedata};" type="button" value="Anwesenheitserfassung lt. Leistung" id="dzeitedata" class="abyStartButton"  onClick="location.href='./personal/doc_root/index.php?action=edataAnw&presenter=DpersAnwesenheit'" />
		    <input style="display:{$display_sec.dpers};" type="button" value="Personal Pflegen" id="dpers" class="abyStartButton"  onClick="okno=window.open();okno.location.href='./personal/doc_root/index.php?presenter=Persinfo'" />
		    <input style="display:{$display_sec.dzeit};" type="button" value="Anwesenheitserfassung" id="dzeit" class="abyStartButton"  onClick="location.href='./dzeit/dzeit.php'" />
		    <input style="display:{$display_sec.anwesenheitplan};" type="button" value="Anwesenheitplanung" id="anwesenheitplan" class="abyStartButton"  onClick="location.href='./personal/doc_root/index.php?action=planAnwesenheit&presenter=DpersAnwesenheit'" />
		</fieldset>

		<fieldset class='buttonsection kunden'>
		    <legend>Kunden / zakaznici</legend>
		    <input style="display:{$display_sec.kundepflegen};" type="button" value="Kunden Pflegen" id="kundepflegen" class="abyStartButton"  onClick="okno=window.open();okno.location.href='./dksd/kundesuchen.php'" />
		    <input style="display:{$display_sec.telbuch};" type="button" value="Telefonbuch" id="telbuch" class="abyStartButton"  onClick="okno=window.open();okno.location.href='./telbuch/telbuch.php'" />
		</fieldset>

		<fieldset class='buttonsection auftraege'>
		    <legend>Auftraege / zakazky</legend>
		    <input style="display:{$display_sec.daufkopf};" type="button" value="Aufträge Pflegen" id="daufkopf" class="abyStartButton" onClick="okno=window.open();okno.location.href='./dauftr/auftragsuchen.php'" />
		    <input style="display:{$display_sec.dkopf};" type="button" value="Arbeitsplan Pflegen" id="dkopf" class="abyStartButton" onClick="okno=window.open();okno.location.href='./dkopf/teilsuchen.php'" />
		    <input style="display:{$display_sec.umtermin};" type="button" value="Umterminieren" id="umtermin" class="abyStartButton"  onClick="okno=window.open();okno.location.href='./dauftr/umterminieren/umterminieren.php'" />
	<!--	    <input style="display:{$display_sec.cmr};" type="button" value="CMR" id="cmr" class="abyStartButton" onClick="location.href=''" />-->
		    <input style="display:{$display_sec.rundlauf};" type="button" value="Rundlauf" id="rundlauf"  class="abyStartButton" onClick="okno=window.open();okno.location.href='./napl/www/?action=rundlauf&presenter=Dispo'" />
		    <input style="display:{$display_sec.reklamation};" type="button" value="Reklamation" id="reklamation"  class="abyStartButton" onClick="okno=window.open();okno.location.href='./napl/www/?action=reklamation&presenter=Reklamation&uziv={$user}'" />
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
		    <input style="display:{$display_sec.behlagerbew};" type="button" value="Behaelterbewegung / palety pohyby" id="behlagerbew" class="abyStartButton" onClick="location.href='./dbehaelter/bewegung.php'" />
		    <input style="display:{$display_sec.behlagerinv};" type="button" value="Behaelterinventur / palety inventura" id="behlagerinv" class="abyStartButton" onClick="location.href='./dbehaelter/inventur.php'" />
		</fieldset>

		<fieldset class='buttonsection bericht'>
		    <legend>Berichte / sestavy</legend>
		    <input style="display:{$display_sec.berichte};" type="button" value="Berichte Drucken" id="berichte" class="abyStartButton" onClick="okno=window.open();okno.location.href='./Reports/reports.php'" />
		    <input style="display:{$display_sec.phpexcel};" type="button" value="PHPExcel - Exporte" id="phpexcel" class="abyStartButton" onClick="okno=window.open();okno.location.href='./Reports/excelreports.php'" />
		    <input style="display:{$display_sec.showquery};" type="button" value="Schlüsseltabellen zeigen" id="showquery" class="abyStartButton" onClick="okno=window.open();okno.location.href='./Reports/querys.php'" />
		</fieldset>

		<fieldset class='buttonsection reparatur'>
		    <legend>Reparaturen</legend>
		    <input style="display:{$display_sec.repeingabe};" type="button" value="Reparaturen" id="repeingabe" class="abyStartButton" onClick="okno=window.open();okno.location.href='./reparaturen/repeingabe.php'" />
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
	    <div align="center" id="leistungtabelle">

		<!--	<a title="kundenminuten aktuell" href="graph/leistung_akt_monat_gross.php" ><img border=0 src="graph/leistung_akt_monat.php"></a>-->
		<table class="monatleistungtable" border="0">

		    <tr>
			<td class="progresspopis">datum</td>
			<td class="progresspopis">PG1</td>
			<td class="progresspopis">PG3</td>
			<td class="progresspopis">PG4</td>
			<td class="progresspopis">PG9</td>
			<td class="progresspopis">Summe</td>
		    </tr>

		    <tr>
			<td class="ganzmonat">akt. Monat</td>
			<td class="ganzmonat">{$sum_pg1|string_format:"%d"}</td>
			<td class="ganzmonat">{$sum_pg3|string_format:"%d"}</td>
			<td class="ganzmonat">{$sum_pg4|string_format:"%d"}</td>
			<td class="ganzmonat">{$sum_pg9|string_format:"%d"}</td>
			<td class="ganzmonat">{$sum_celkem|string_format:"%d"}</td>
		    </tr>

		    {foreach key=poradi item=polozka from=$pole}
			<tr>
			    <td class='progresspolozka'>{$polozka.datum}</td>
			    <td class='progresspolozka'>{$polozka.pg1|string_format:"%d"}</td>
			    <td class='progresspolozka'>{$polozka.pg3|string_format:"%d"}</td>
			    <td class='progresspolozka'>{$polozka.pg4|string_format:"%d"}</td>
			    <td class='progresspolozka'>{$polozka.pg9|string_format:"%d"}</td>
			    <td class='progresspolozka'>{$polozka.celkem|string_format:"%d"}</td>
			</tr>
		    {/foreach}
		</table>

		<table class="monatleistungtable" border="0">
		    <tr>
			<td colspan="5" class="progresspopis">Importe - Heute</td>
		    </tr>
		    <tr>
			<td class="ganzmonat">Kunde</td>
			<td class="ganzmonat">auftragsnr</td>
			<td class="ganzmonat">aufdat</td>
			<td class="ganzmonat">ausliefdat</td>
			<td class="ganzmonat">rechdatum</td>
		    </tr>
		    {foreach item=polozka from=$zakazkyIM}
			<tr>
			    <td class='progresspolozka'>{$polozka.kunde}</td>
			    <td class='progresspolozka'>{$polozka.auftragsnr}</td>
			    <td class='progresspolozka'>{$polozka.aufdat}</td>
			    <td class='progresspolozka'>{$polozka.ausliefer_datum}</td>
			    <td class='progresspolozka'>{$polozka.fertig}</td>
			</tr>
		    {/foreach}
		</table>

		<table class="monatleistungtable" border="0">
		    <tr>
			<td colspan="5" class="progresspopis">Exporte - Heute</td>
		    </tr>
		    <tr>
			<td class="ganzmonat">Kunde</td>
			<td class="ganzmonat">auftragsnr</td>
			<td class="ganzmonat">aufdat</td>
			<td class="ganzmonat">ausliefdat</td>
			<td class="ganzmonat">rechdatum</td>
		    </tr>
		    {foreach item=polozka from=$zakazkyEX}
			<tr>
			    <td class='progresspolozka'>{$polozka.kunde}</td>
			    <td class='progresspolozka'>{$polozka.auftragsnr}</td>
			    <td class='progresspolozka'>{$polozka.aufdat}</td>
			    <td class='progresspolozka'>{$polozka.ausliefer_datum}</td>
			    <td class='progresspolozka'>{$polozka.fertig}</td>
			</tr>
		    {/foreach}
		</table>

	    </div>
	{/if}

    </body>
</html>

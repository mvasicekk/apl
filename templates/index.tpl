<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
     
    <title>
      APL Abydos
    </title>    
    <link rel="stylesheet" href="./styl.css" type="text/css">    
	
	<script type="text/javascript" src="./js/init_form.js"></script>

{literal}	
<script type="text/javascript">

function zavri(){
window.close();
}

function sendlogin()
{
	//alert('posilam login');
	document.getElementById('loginform').submit();
}

function logout()
{
}

</script>
{/literal}

</head>
<body onload="init_level({$level})">
{popup_init src="./js/overlib.js"}
<div id="header">
<h3 align="center">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Auftragsabwicklung Produktionssteuerung</h3>
</div>

<div align="center" id="podheader">
{if $prihlasen}
	{$user}  level={$level}<a href="index.php?akce=logout">abmelden/odhlasit</a>
{else}
	Benutzer nicht angemeldet/neprihlaseny uzivatel
{/if}

</div>

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
                <td class="ganzmonat">auslieferdatum</td>
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
                <td class="ganzmonat">auslieferdatum</td>
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

<!-- seznam vsech tlacitek -->
<!-- plot (t, x) -->
<div align="center" id="tlacitka">
{if $prihlasen}
	<table class="formulartable" border="0" cellspacing="0">
	<tr >
		<td class="personalpopis" colspan="2">
			Personal
		</td>
	</tr>
	<tr>
<!--            <td><input {popup text="Sprava personalu .."} type="button" value="Personal Pflegen" id="dpers" class="abyStartButton"  onClick="okno=window.open();okno.location.href='http://mailserver/apl/index.php?presenter=Persinfo'" /></td>-->
            <td><input {popup text="Sprava personalu .."} type="button" value="Personal Pflegen" id="dpers" class="abyStartButton"  onClick="okno=window.open();okno.location.href='./personal/doc_root/index.php?presenter=Persinfo'" /></td>
            <td><input disabled {popup text="Zadavani dochazky .."} type="button" value="Anwesenheitserfassung lt. Leistung" id="dzeitedata" class="abyStartButton"  onClick="location.href='./personal/doc_root/index.php?action=edataAnw&presenter=DpersAnwesenheit'" /></td>
	</tr>
        <tr>
            <td>&nbsp;</td>
            <td><input disabled {popup text="Zadavani dochazky .."} type="button" value="Anwesenheitserfassung" id="dzeit" class="abyStartButton"  onClick="location.href='./dzeit/dzeit.php'" /></td>
        </tr>	
	<tr>
		<td><input  disabled {popup text="Osobni karta .."} type="button" value="Personal Karte" id="perskarte" class="abyStartButton"  onClick="location.href=''" /></td>
<!--		<td><input disabled {popup text="Planovani dochazky .."} type="button" value="Anwesenheitplanung" id="anwesenheitplan" class="abyStartButton"  onClick="location.href='http://mailserver/apl/index.php?action=planAnwesenheit&presenter=DpersAnwesenheit'" /></td>-->
                <td><input disabled {popup text="Planovani dochazky .."} type="button" value="Anwesenheitplanung" id="anwesenheitplan" class="abyStartButton"  onClick="location.href='./personal/doc_root/index.php?action=planAnwesenheit&presenter=DpersAnwesenheit'" /></td>
	</tr>
	<tr>
		<td class="kundenpopis" colspan="2">
			Kunden / zakaznici
		</td>
	</tr>
	<tr>
		<td><input {popup text="Sprava zakazniku .."} type="button" value="Kunden Pflegen" id="dksd" class="abyStartButton" style="display:{$display_sec.kundepflegen};" onClick="okno=window.open();okno.location.href='./dksd/kundesuchen.php'" /></td>
		<td><input {popup text="Telefonbuch .."} type="button" value="Telefonbuch" id="telbuch" class="abyStartButton" style="display:{$display_sec.telbuch};" onClick="okno=window.open();okno.location.href='./telbuch/telbuch.php'" /></td>
	</tr>
	<tr>
		<td class="auftragpopis" colspan="2">
			Auftraege / zakazky
		</td>
	</tr>
	<tr>
		<td><input disabled {popup text="Sprava zakazek .."} type="button" value="Aufträge Pflegen" id="daufkopf" class="abyStartButton" onClick="okno=window.open();okno.location.href='./dauftr/auftragsuchen.php'" /></td>
		<td><input disabled {popup text="Pracovni plan zmenit .."} type="button" value="Arbeitsplan Pflegen" id="dkopf" class="abyStartButton" onClick="okno=window.open();okno.location.href='./dkopf/teilsuchen.php'" /></td>
	</tr>
	<tr>
		<td><input {popup text="Umterminieren .."} type="button" value="Umterminieren" id="umterminieren" class="abyStartButton" style="display:{$display_sec.umtermin};" onClick="okno=window.open();okno.location.href='./dauftr/umterminieren/umterminieren.php'" /></td>
		<td><input disabled type="button" value="CMR" id="cmr" class="abyStartButton" disabled='disabled' onClick="location.href=''" /></td>
	</tr>
	<tr>
		<td><input type="button" value="Rundlauf" id="rundlauf" style="display:{$display_sec.rundlauf};" class="abyStartButton" onClick="okno=window.open();okno.location.href='./napl/www/?action=rundlauf&presenter=Dispo'" /></td>
		<td><input type="button" value="Reklamation" id="reklamation" style="display:{$display_sec.reklamation};" class="abyStartButton" onClick="okno=window.open();okno.location.href='./napl/www/?action=reklamation&presenter=Reklamation&uziv={$user}'" /></td>
	</tr>
	<tr>
		<td class="hlasenipopis" colspan="2">
			Rueckmeldungen / hlaseni vykonu
		</td>
	</tr>
	<tr>
		<td><input disabled {popup text="Zadavani zpetnych hlaseni .."} type="button" value="Rückmeldung Leist" id="drueck" class="abyStartButton" onClick="okno=window.open();okno.location.href='./drueck/drueck.php'" /></td>
		<td></td>
	</tr>

	<tr>
		<td class="hlasenipopis" colspan="2">
			Laeger / sklady
		</td>
	</tr>
	<tr>
		<td><input disabled {popup text="lagerumbuchung"} type="button" value="Lagerumbuchung / pridani do skladu" id="lagerbew" class="abyStartButton" onClick="location.href='./dlager/umbuchung.php'" /></td>
		<td><input disabled {popup text="Sprava skladu .."} type="button" value="Lager Inventur" id="lagerstk" class="abyStartButton" onClick="location.href='./dlagstk/dlagstk.php'" /></td>
	</tr>
	<tr>
		<td><input disabled {popup text="Behaelter Bewegung"} type="button" value="Behaelterbewegung / palety pohyby" id="behlagerbew" class="abyStartButton" onClick="location.href='./dbehaelter/bewegung.php'" /></td>
		<td><input disabled {popup text="Behaelter Inventur"} type="button" value="Behaelterinventur / palety inventura" id="behlagerinv" class="abyStartButton" onClick="location.href='./dbehaelter/inventur.php'" /></td>
	</tr>
	
	<tr>
		<td class="sestavypopis" colspan="2">
			Berichte / sestavy
		</td>
	</tr>
	<tr>
		<td><input disabled type="button" value="Berichte Drucken" id="berichte" class="abyStartButton" onClick="okno=window.open();okno.location.href='./Reports/reports.php'" /></td>
		<td><input disabled type="button" value="GF Berichte" id="gfberichte" class="abyStartButton" onClick="okno=window.open();okno.location.href='./Reports/gfreports.php'" /></td>
	</tr>
	<tr>
		<td><input disabled type="button" value="Schlüsseltabellen zeigen" id="showquery" class="abyStartButton" onClick="okno=window.open();okno.location.href='./Reports/querys.php'" /></td>
		<td><input disabled type="button" value="PHPExcel - Exporte" id="phpexcel" class="abyStartButton" onClick="okno=window.open();okno.location.href='./Reports/excelreports.php'" /></td>
	</tr>

       	<tr>
		<td class="sestavypopis" colspan="2">
			Reparaturen
		</td>
	</tr>
	<tr>
		<td><input disabled type="button" value="Reparaturen" id="repeingabe" class="abyStartButton" onClick="okno=window.open();okno.location.href='./reparaturen/repeingabe.php'" /></td>
		<td></td>
	</tr>

      
      <!-- <input type="button" value="Telefon Buch" id="telBuch" class="abyStartButton" disabled='disabled' onClick="location.href='./telbuch/telbuch.php?telId=1&amp;action=first'" />
      <input type="button" value="Telefon Buch Class" id="telBuchClass" class="abyStartButton" onClick="location.href='./telbuch/telBuchClass.php?telId=1&amp;action=first'" />
      <input type="button" value="Teil Lager" id="teilLager" class="abyStartButton" onClick="location.href='./lagrvisacky/vysacky.php?kunde=none'" /> -->
	  </table>
{else}
	<table class="logintable" border=0>
	<form action="index.php" method="post" id="loginform" class="loginform">
		<tr class="login_username">
		<td>
		<label for="username">
        Benutzername / uzivatelske jmeno
		</label>
		</td>
		<td>
		<input type="text" name="username" id="username">
		</td>
		</tr>
		<tr class="login_password">
		<td>
		<label for="password">
        Kennwort / heslo
		</label>
		</td>
		<td>
		<input type="password" name="password" id="password">
		</td>
		</tr>
		<tr>
		<td align="center" colspan="2">
		<input onclick="sendlogin();" type="button" name="loginbutton" id="loginbutton" value="Anmelden/prihlasit">
		</td>
		</tr>
	</form>
	</table>
{/if}
    <input class='formularEndbutton' type="button" value="Ende/konec" id="ende" style="margin-top: 15px;"  onClick="zavri();">
</div>
</body>
</html>

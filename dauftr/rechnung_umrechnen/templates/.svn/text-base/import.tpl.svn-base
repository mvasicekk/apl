<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta name="generator" content="Bluefish 1.0.5">
    <title>
      Rechnungen umrechnen
    </title>

<link rel="stylesheet" href="./styl.css" type="text/css">
<link rel="stylesheet" href="../../styldesign.css" type="text/css">

<script type="text/javascript" src="./js_functions.js"></script>

<!-- YUI stuff -->
<script type="text/javascript" src="../../js/yahoo-dom-event/yahoo-dom-event.js"></script>
<script type="text/javascript" src="../../js/connection/connection-min.js"></script>

</head>

<body>
{popup_init src="../js/overlib.js"}

<div id="header">
<h3 align="center">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Auftragsabwicklung Produktionssteuerung</h3>
</div>


<div align="center" id="podheader">
{if $prihlasen}
	{$user}  level={$level}<a href="../index.php?akce=logout">abmelden/odhlasit</a>
{else}
	Benutzer nicht angemeldet/neprihlaseny uzivatel
{/if}
</div>

<div id="formular_header">
Rechnung umrechnen / přepočet faktury
</div>

<div id="formular_telo">

	<div id='rechnungstable'>
		<div id='scroll_apl'>	
			<table class='dauftr_table' border='0' id='dautfrtabulka'>
				<tr class='dauftr_table_header'>
					<td>Rechnung</td>
					<td>Rechnungsdatum</td>
					<td>Auslieferdatum</td>
					<td>wartet / fertig</td>
				</tr>
				{foreach from=$rechnungen item=polozka}
				{if $polozka.wartet_fertig eq "W"}
				<tr id='tr{$polozka.AuftragrNr}' class='Wrow' onclick='handleonclick(this);' onmouseover='handleOnMouseOver(this);' onmouseout='handleOnMouseOut(this);'>
				{else}
				<tr id='tr{$polozka.AuftragrNr}' bgcolor='white' onclick='handleonclick(this);' onmouseover='handleOnMouseOver(this);' onmouseout='handleOnMouseOut(this);'>
				{/if}
				
					<td>{$polozka.AuftragsNr}</td>
				<td>	{$polozka.rechnungsdatum}</td>
						<td>{$polozka.lieferdatum}</td>
					<td>{$polozka.wartet_fertig}</td>
				</tr>
				{/foreach}
			</table>
		</div>
	</div>
	
	<div id='umrechformular'>
	
	<div id='umrechformulardetail'>
		<form>
			<label>Rechnung</label>
			<input id='rechnung' type="text" size="6" disabled="true" />
			<label>Rechnungsdatum</label>
			<input id='rechnungsdatum' type="text" size="10" onblur="YAHOO.util.Connect.asyncRequest('GET','./validate_datum.php?what=datum&value='+this.value+'&controlid='+this.id, refreshdatum);" />
			<label>Lieferdatum</label>
			<input id='lieferdatum' type="text" size="10" onblur="YAHOO.util.Connect.asyncRequest('GET','./validate_datum.php?what=datum&value='+this.value+'&controlid='+this.id, refreshdatum);" />
			<hr/>
			<label>Vom</label>
			<input id='vom' type="text" size="10" onblur="YAHOO.util.Connect.asyncRequest('GET','./validate_vom.php?vom='+document.getElementById('vom').value+'&an='+document.getElementById('an').value, refreshvom);" />
			<label>An</label>
			<input id='an' type="text" size="10" onblur="YAHOO.util.Connect.asyncRequest('GET','./validate_an.php?vom='+document.getElementById('vom').value+'&an='+document.getElementById('an').value, refreshan);"	/>
			<hr/>
			<input id='berechnenbutton' type="button" value='berechnen' onclick="YAHOO.util.Connect.asyncRequest('GET','./rechumrech.php?rechnung='+document.getElementById('rechnung').value+'&rechdatum='+document.getElementById('rechnungsdatum').value+'&liefdatum='+document.getElementById('lieferdatum').value+'&vom='+document.getElementById('vom').value+'&an='+document.getElementById('an').value+'&delold=0', rechumrech);"/>
		
		</form>
	</div>
	</div>
<div id='dauftr_form_footer'>
<table width='100%' border='0' cellspacing='0'>

<tr>
	<td>
		<input onClick="location.href='../get_parameters.php?popisky=AuftragsNr;Pal von;Pal bis;A->Z(0) Z->A(1)&promenne=auftragsnr;palvon;palbis;order&values={$auftragsnr_value};0;9999;0&report=D210'" class='formularbutton' type="button" id="D231" name="D210" value="D210 - Arbeitspapiere / Rückseite"/>
	</td>
	
		<td>
			
		</td>

		<td>
			<input id="D700" disabled='disabled' onClick="location.href='../get_parameters.php?popisky=Export;Termin&promenne=export;termin&values={$auftragsnr_value};{$now}&report=D700'" class='formularbutton' type="button"  value="D700 - Lieferungsübersicht"/>
		</td>
		
		<td>
			<input class='formularEndbutton' type='button' value='Ende / konec' onclick="document.location.href='../index.php';"/>
		</td>

</tr>

</div>


</div>

</body>
</html>

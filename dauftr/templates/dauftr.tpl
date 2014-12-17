<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta name="generator" content="Bluefish 1.0.5">
    <title>
      Auftraege pflegen / zadani zakazky
    </title>

<link rel="stylesheet" href="./styl.css" type="text/css">
<link rel="stylesheet" href="../styldesign.css" type="text/css">
<link rel='stylesheet' href='./print.css' type='text/css' media="print"/>
<link rel="stylesheet" href="../css/ui-lightness/jquery-ui-1.8.14.custom.css" type="text/css">

<script type="text/javascript" src="../js/detect.js"></script>
<script type="text/javascript" src="../js/eventutil.js"></script>

<!-- YUI stuff -->
<script type="text/javascript" src="../js/yahoo-dom-event/yahoo-dom-event.js"></script>
<script type="text/javascript" src="../js/connection/connection-min.js"></script>

<script charset="windows-1250" src="js_functions.js" type="text/javascript"></script>
<script type = "text/javascript" src = "../js/ajaxgold.js"></script>

<script type = "text/javascript" src = "../js/jquery-1.5.1.min.js"></script>
<script type = "text/javascript" src = "../js/jquery-ui-1.8.14.custom.min.js"></script>
<script type = "text/javascript" src = "../js/jquery.ui.datepicker-cs.js"></script>
<!--<script type="text/javascript" src="../js/jquery.js"></script>-->
<script type="text/javascript" src="./dauftr.js"></script>
<script>

var promenne = new Array(	"teil",
							"pos_pal_nr",
							"stk",
							"preis",
							"mehrarb_kz",
							"abgnr",
							"KzGut",
							"termin",
							"auftragsnr_exp",
							"pos_pal_nr_exp",
							"stk_exp",
							"fremdauftr",
							"fremdpos");

var onblur_function = new Array("savevalue(this);",
								"savevalue(this);",
								"savevalue(this);",
								"js_validate_float(this);savevalue(this);",
								"getDataReturnXml('./validate_mehrarb_kz.php?id='+this.id, validate_mehrarb_kz);savevalue(this);",
								"getDataReturnXml('./validate_abgnr.php?id='+this.id, validate_abgnr);savevalue(this);",
								"savevalue(this);",
								"savevalue(this);",
								"savevalue(this);",
								"savevalue(this);",
								"savevalue(this);",
								"savevalue(this);",
								"savevalue(this);");

var editovat = new Array(0,0,1,0,0,0,1,1,1,1,1,1,1);

</script>

<script type = "text/javascript" src = "js_tablegrid.js"></script>



</head>

<body onLoad="init_dauftr_form('show');">
{popup_init src="../js/overlib.js"}

<!--
<div id='souradnice'>
sql={$sqldauftr}<br>
error={$sqlerror}<br>
</div>
-->


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
Aufträge pflegen / zadání zakázky
</div>

<div id="formular_telo">
<table cellpadding="10px" class="formulartable" border="1">
	<form method="post" action='' name="auftragsuchen_formular" onsubmit="">
    <tr>
		<td>
		    <table border="0">
			<tr>
			    <td>
				<label for="auftragsnr">Auftragsnr</label>
			    </td>
			    <td>
				<input class='disabled_bold' disabled readonly  maxlength='8' size="8" type="text" id="auftragsnr" name="auftragsnr" value="{$auftragsnr_value}"/>
			    </td>
			    <td>
				<label for="bestellnr">Bestellnr</label>
			    </td>
			    <td>
				<input maxlength='30' size="6" type="text" id="bestellnr" name="bestellnr" value="{$bestellnr_value}"/>
			    </td>
			    <td>
				<label for="aufdat">Auftragseingang / datum zakázky</label>
			    </td>
			    <td>
				<input onblur="getDataReturnText('./validate_datum.php?what=datum&value='+this.value+'&controlid='+this.id, refreshdatum);" maxlength='10' size="10" type="text" id="aufdat" name="aufdat" value="{$aufdat_value}"/>
			    </td>
			</tr>
			<tr>
			    <td>
				<label for="ex_datum_soll">Ex Soll</label>
			    </td>
			    <td>
				<input  acturl='./updateSollEx.php?auftragsnr={$auftragsnr_value}' class='datepicker' maxlength='10' size="10" type="text" id="ex_datum_soll" name="ex_datum_soll" value="{$ex_datum_soll_value}"/>
				<input  acturl='./updateSollEx.php?auftragsnr={$auftragsnr_value}' maxlength='5' size="5" type="text" id="ex_zeit_soll" name="ex_zeit_soll" value="{$ex_zeit_soll_value}"/>
			    </td>
			    <td colspan="2">
				<label for="zielort">Zielort</label>
			    </td>
			    <td>
				<input acturl='./zielortChange.php?auftragsnr={$auftragsnr_value}' value='{$zielort_value}' type="text" id="zielort" name="zielort" value="{$zielort}"/>
				<input type="hidden" id="ziel_value" />
			    </td>
			</tr>
		    </table>
		</td>
		<td width='150px'>
			<input class='formularbutton' accesskey='h' title='Alt+h' type='button' value='suchen / hledat' onclick="document.location.href='auftragsuchen.php';"/>
			<input class='formularbutton' accesskey='s' title='Alt+s' type='button' value='Änderungen speichern'
		onclick="getDataReturnXml('./save_dauftr.php?auftragsnr='+encodeControlValue('auftragsnr')
												+'&bestellnr='+encodeControlValue('bestellnr')
												+'&aufdat='+encodeControlValue('aufdat')
												+'&ex_datum_soll='+encodeControlValue('ex_datum_soll')
												, saverefresh);"/>
                        <input type="button" class='formularbutton' accesskey='b' title='Alt+b' value='Behaelter bew.' onclick="document.location.href='../dbehaelter/beheingabe.php?auftrag={$auftragsnr_value}';"/>
			<input type="button" class='formularbutton' accesskey='b' title='Alt+e' value='edit Auftrpositionen' onclick="document.location.href='./editDauftrForm.php?import={$auftragsnr_value}';"/>
		</td>
	</tr>
	<tr>
		<td colspan='2'>
			<label for="fertig">Rechnung am / faktura dne</label>
			<!-- po dvojkliku dovolim smazat fakturu -->
			<input ondblclick="YAHOO.util.Connect.asyncRequest('GET','./delrechnung.php?auftragsnr={$auftragsnr_value}&run=1', delrechnung);" readonly maxlength='10' size="10" type="text" id="fertig" name="fertig" value="{$fertig_value}"/>

			<label for="ausliefer_datum">ausgeliefert am</label>
			<input class='disabled_bold' disabled readonly maxlength='10' size="10" type="text" id="ausliefer_datum" name="ausliefer_datum" value="{$ausliefer_datum_value}"/>

                        <label for="bemerkung">Bemerkung</label>
			<input acturl="./saveBemerkung.php" maxlength='255' size="20" type="text" id="bemerkung" name="bemerkung" value="{$bemerkung}"/>

		</td>
	</tr>

	<tr>
		<td colspan='2'>
			<!-- tabulka s informacema o zakaznikovi -->
			<table class="kunde_info" border='0'>
			<tr>
				<td>
					KundeNR :<b>{$kunde_value}</b>
					<input type="hidden" id="kundenr" value="{$kunde_value}" />
				</td>
				<td>
					Preis/Minute :{$minpreis_value|string_format:"%.4f"}
				</td>
				<td>
					Preis Vorgabestunde :{$preis_vzh|string_format:"%.2f"}&nbsp;{$waehr_kz}
				</td>

			</tr>
			<tr>
				<td>
					{$name1}
				</td>
				<td>
					{$name2}
				</td>
				<td>
					Ort :{$ort}
				</td>
			</tr>

			</table>
		</td>
	</tr>
   </form>
	</table>
</div>


<div id='dauftr_table'>
	<div id='scroll_apl'>
		<table class='dauftr_table' border='0' id='dautfrtabulka'>
		<tr class='dauftr_table_header'>
			<td>Teil</td>
			<td>Pal</td>
			<td>Stk</td>
			<td>Preis</td>
			<td>Kennz</td>
			<td>TaetNr</td>
			<td>G</td>
			<td>Termin</td>
			<td>AuftrEx</td>
			<td>PalEx</td>
			<td>StkEx</td>
			<td>FremdAuftr</td>
			<td>FremdPos</td>
			<td width='60'>&nbsp;</td>
		</tr>
		{assign var="old_pal" value="0"}
		{foreach from=$dauftr item=polozka}
		{if $old_pal != $polozka.pos_pal_nr}
        <tr class='oddel_paletu'><td align='center' colspan='14'>&nbsp;</td><tr>
		{assign var="old_pal" value=$polozka.pos_pal_nr}
		{/if}

		{if $polozka.KzGut eq "G"}
		<tr id='tr{$polozka.id_dauftr}' class='Grow'>
		{else}
		<tr id='tr{$polozka.id_dauftr}' bgcolor='white' >
		{/if}
			<td id='td_select_teil{$polozka.id_dauftr}'>{$polozka.Teil}</td>
			<td title='{$polozka.bemerkung}' id='td_pal{$polozka.id_dauftr}' acturl='./updatePalBemerkung.php?id={$polozka.id_dauftr}' align='right'>{$polozka.pos_pal_nr}</td>
			<td align='right'>{$polozka.stk}</td>
			<td {if $canpreisupdate}{if !$polozka.hasrechnung}ondblclick="YAHOO.util.Connect.asyncRequest('GET','./preisupdate.php?auftragsnr={$auftragsnr_value}&id_dauftr={$polozka.id_dauftr}&level={$level}', preisupdate);"{/if}{/if} align='right'>{$polozka.Preis|string_format:"%.4f"}</td>
			<td id='td_select_mehrarb_kz{$polozka.id_dauftr}'>{$polozka.mehrarb_kz}</td>
			<td id='td_select_abgnr{$polozka.id_dauftr}' align='right'>{$polozka.abgnr}</td>
			<td>{$polozka.KzGut}</td>
			<td>{$polozka.Termin}</td>
			<td>{$polozka.auftragsnr_exp}</td>
			<td align='right'>{$polozka.pos_pal_nr_exp}</td>
			<td align='right'>{$polozka.stk_exp}</td>
			<td>{$polozka.fremdauftr}</td>
			<td>{$polozka.fremdpos}</td>
			<td onmouseover="this.style.cursor='pointer';" id='tdedit{$polozka.id_dauftr}'>
				{if $polozka.hasrechnung}
				Rechnung
				{else}
				<a {if $polozka.KzGut eq 'G'}
						{popup text="<p>pokud <strong>editujete operaci G</strong>, potom se ImportStk,Termin,AuftrEx,PalEx,FremdAuftr a FremdPos rozkopíruje do všech pozic na vybrané paletě.</p><p>Pokud <strong>mažete u operace G</strong>, smaže se celá paleta (tj. všechny operace na paletě)</p>" }
					{/if}
					id='edit{$polozka.id_dauftr}'
					onclick="getDataReturnXml('./edit_dauftr_row.php?dauftr_id={$polozka.id_dauftr}', edit);"
					href='#'>edit</a>
				{/if}
			</td>
		</tr>
		{/foreach}
		</table>
	</div>
</div>


<div id='dauftr_form_footer'>
<table width='100%' border='0' cellspacing='0'>

<tr>
	<td>
		<input class='formularbutton'  type='button' value='Position erstellen' onclick="document.location.href='./dauftr_schnell_erfassen/dauftr_schnell_erfassen.php?kunde={$kunde_value}&auftragsnr={$auftragsnr_value}&minpreis={$minpreis_value}';"/>
	</td>
	<td>
		<input onClick="location.href='../get_parameters.php?popisky=Import&promenne=auftragsnr&values={$auftragsnr_value}&report=D605'" class='formularbutton' type="button" id="D605" name="D605" value="D605 Import"/>
	</td>
	<td>
		<input class='formularbutton' type='button' value='Export/Plan füllen' onclick="document.location.href='./export_fullen/export_fuellen.php?auftragsnr={$auftragsnr_value}';"/>

	</td>
	<td>
		<input class='formularbutton' type='button' value='Rechnung' onclick="document.location.href='./rechnung/rechnung_berechnen.php?auftragsnr={$auftragsnr_value}';"/>
	</td>
</tr>

<tr>
	<td>
                <input onClick="location.href='../get_parameters.php?popisky=AuftragsNr;Pal von;Pal bis&promenne=auftragsnr;palvon;palbis&values={$auftragsnr_value};0;9999&report=D235'" class='formularbutton' type="button" id="D235" name="D230" value="D230 - Arbeitspapiere/DUPLEX"/>
	</td>

	<td>
		<input onClick="location.href='../get_parameters.php?popisky=Export&promenne=export&values={$auftragsnr_value}&report=D606'" class='formularbutton' type="button" id="D606" name="D606" value="D606 Export"/>
	</td>
	<td>
		<input {if $hasrechnung} disabled='disabled' {/if} class='formularbutton' type='button' value='Export löschen' onclick="document.location.href='./export_fullen/export_loeschen.php?auftragsnr={$auftragsnr_value}';"/>
	</td>
	<td>
		<input {if !$hasrechnung} disabled='disabled' {/if} class='formularbutton' type='button' value='Rechnung exportieren' onclick="YAHOO.util.Connect.asyncRequest('GET','./exportdrech.php?auftragsnr={$auftragsnr_value}&run=1', exportdrech);"/>
	</td>

</tr>

<tr>
		<td>
{*			<input onClick="location.href='../get_parameters.php?popisky=AuftragsNr;Pal von;Pal bis&promenne=auftragsnr;palvon;palbis&values={$auftragsnr_value};0;9999&report=D230'" class='formularbutton' type="button" id="D230" name="D230" value="D230 - Arbeitspapiere"/>	*}
		</td>

		<td>
			<input id="D607" onClick="location.href='../get_parameters.php?popisky=Geplant mit von;Geplant mit bis;Neue Seite nach Teil;Teil;Reporttyp,*RA&promenne=von;bis;teilpager;teil;reporttyp&values={$auftragsnr_value};{$auftragsnr_value};0;*;Detail,Detail mit Summen,nur Summen,Info an Kunden&report=D607'" class='formularbutton' type="button"  name="D607" value="D607 Plan"/>
		</td>

		<td>
			<input id="D710" onClick="location.href='../get_parameters.php?popisky=Export;Termin,*DATE&promenne=export;termin&values={$auftragsnr_value};{$ausliefer_datum_value}&report=D710'" class='formularbutton' type="button"  name="D710" value="D710 - Lieferungsübersicht"/>
		</td>
		<td>
			<input id="importrechnung" onClick="location.href='./rechnung_umrechnen/import.php'" class='formularbutton' type="button"  name="" value="Rechnung importieren"/>
		</td>
</tr>

<tr>
	<td>
{*		<input onClick="location.href='../get_parameters.php?popisky=AuftragsNr;Pal von;Pal bis;A->Z(0) Z->A(1)&promenne=auftragsnr;palvon;palbis;order&values={$auftragsnr_value};0;9999;0&report=D210'" class='formularbutton' type="button" id="D231" name="D210" value="D210 - Arbeitspapiere / Rückseite"/>*}
	</td>

		<td>
                    <input id="exporttable" onClick="location.href='./exporttablo.php?export={$auftragsnr_value}'" class='formularbutton' type="button"  value="ExportTablo"/>
		</td>

		<td>
			<input id="D64X" onClick="location.href='../get_parameters.php?popisky=Export (leer = Blanko );Teil;Termin;Text,*RA;Wassermarke,*RA;Format,*RA&promenne=export;teil;termin;popisek;watermark;format&values={$auftragsnr_value};*;{$ausliefer_datum_value};FREIGABE,ZWEIFLER;nein,ja;6x auf A4,2x auf A4&report=D64X'" class='formularbutton' type="button"  value="D6XX - Freigabezettel"/>
		</td>

		<td>
			<input class='formularEndbutton' type='button' value='Ende / konec' onclick="document.location.href='../index.php';"/>
		</td>

</tr>

</div>

</body>
</html>

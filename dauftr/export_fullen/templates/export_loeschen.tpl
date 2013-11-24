<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta name="generator" content="Bluefish 1.0.5">
    <title>
      Exportfullen
    </title>

<link rel="stylesheet" href="./styl.css" type="text/css">

<script type="text/javascript" src="../../js/detect.js"></script>
<script type="text/javascript" src="../../js/eventutil.js"></script>

<script charset="windows-1250" src="js_functions.js" type="text/javascript"></script>
<script type = "text/javascript" src = "../../js/ajaxgold.js"></script>

</head>

<body onload="rebuildpage();">
{popup_init src="../js/overlib.js"}

<div id="formular_header">
Exportloeschen / mazani Exportu
</div>

<div align="center" id="podheader">
{if $prihlasen}
	{$user}  level={$level}<a href="../index.php?akce=logout">abmelden/odhlasit</a>
{else}
	Benutzer nicht angemeldet/neprihlaseny uzivatel
{/if}
</div>

{if $hatRechnung==0}

<div id="formular_telo">


	<div id='import_header'>
		Exportierte Positionen in: {$auftragsnr}
		
	</div>

	<div id='export_header'>
	Positionen zum loeschen:
	</div>


	<div id='import_table'>
		<div id='scroll_import'>
		<table id='imtable' class='import_table'>
			<tr>
				<td>auftragsnr</td>
				<td>pal</td>
				<td>teil</td>
				<td>exp stk</td>
			</tr>
		
			{foreach from=$loeschen item=polozka}
			<tr onclick="export2null(this);" id='im{$polozka.id}' class='even'>
				<td class='right'>{$polozka.AuftragsNr}</td>
				<td class='right'><b>{$polozka.pal}</b></td>
				<td class='left'>{$polozka.Teil}</td>
				<td class='left'>{$polozka.gut_stk}</td>
			</tr>
			{/foreach}
		</table>
		</div>
	</div>

<div id='export_table'>
	<div id='scroll_export'>
		<table id='extable' class='export_table'>
			<tr>
				<td>auftragsnr</td>
				<td>pal</td>
				<td>teil</td>
				<td>exp stk</td>
			</tr>
		</table>
	</div>
</div>


</div>

{else}
<div id="formular_telo">
	<div id='fehlermeldung'>
 		<strong>{$auftragsnr} hat schon Rechnung !!</strong><br/>
 		<strong>{$auftragsnr} je vyfakturovana !!</strong>
 		<p>
 			Exporte loeschen ist nicht moeglich !!
 		</p>
 		<p>
 			mazani exportu neni mozne !!
 		</p>
 	</div>
</div>
{/if}

<div id='export_fullen_form_footer'>
<table width='100%' border='0' cellspacing='0'>
<tr>
	<td>
		<input class='formularbutton' 
			{if $hatRechnung==1} disabled='disabled'{/if} type='button' value='alles loeschen' onclick="loeschenAll();"/>
	</td>
	<td>
		<input class='formularbutton' id='teil_delete' disabled type='button' value='' onclick=""/>
	</td>
	<td>
		<input class='formularbutton' id='teil_neu' disabled type='button' value='' onclick=""/>
	</td>
	<td>
		<input class='formularbutton' id='teil_edit' disabled type='button' value='' onclick=""/>
	</td>
</tr>
<tr>
	<td>
		<!-- getDataReturnXml('./loeschen.php?list='+encodeControlValue('idlist')+'&export='+document.getElementById('export').value,fullenRefresh);" --> 
		<input {if $hatRechnung==1} disabled='disabled'{/if} class='formularbutton' id='info_D510' type='button' value='LOESCHEN' onclick="fillIdListLoeschen();getDataReturnXml('./loeschen.php?list='+encodeControlValue('idlist'),loeschenRefresh);" />

		<input type='hidden' name='idlist' id='idlist'/>
	</td>
	<td>
		<input id='teil_save' class='formularbutton' type='button' value='' onclick=""/>
	</td>
	<td>
		<input id='lager_zugang' class='formularbutton' disabled type='button' value='' onclick=""/>
	</td>
	<td>
		<input class='formularEndbutton' type='button' value='Ende / konec' onclick="document.location.href='../dauftr.php?auftragsnr={$auftragsnr}';"/>
	</td>

</tr>
</div>

</body>
</html>

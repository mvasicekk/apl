<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta name="generator" content="Bluefish 1.0.5">
    <title>
      Exportfullen
    </title>
<link rel="stylesheet" href="./styl.css" type="text/css">
<link rel="stylesheet" href="../../styldesign.css" type="text/css">
<script type="text/javascript" src="../../js/detect.js"></script>
<script type="text/javascript" src="../../js/eventutil.js"></script>

<script src="js_functions.js" type="text/javascript"></script>
<script type = "text/javascript" src = "../../js/ajaxgold.js"></script>

<script type = "text/javascript" src = "../../js/jquery-1.5.1.min.js"></script>
<script type = "text/javascript" src = "../../js/jquery-ui-1.8.14.custom.min.js"></script>
<script type = "text/javascript" src = "../../js/jquery.ui.datepicker-cs.js"></script>

<!--<script type="text/javascript" src="../../js/jquery.js"></script>-->
<script type="text/javascript" src="../dauftr.js"></script>
</head>

<body onload="rebuildpage();">
{popup_init src="../../js/overlib.js"}
  
<div align="center" id="podheader">
{if $prihlasen}
	{$user}  level={$level}<a href="../index.php?akce=logout">abmelden/odhlasit</a>
{else}
	Benutzer nicht angemeldet/neprihlaseny uzivatel
{/if}
</div>

<div id="formular_header">
Exportfullen / zadani Exportu
</div>

<div id="formular_telo">

<div id='import_header'>
<input type="hidden" id="auftragsnr" value="{$auftragsnr}"/>
    {if strlen($teil)>0}
    TEIL: {$teil}
{else}
    IMPORT: {$auftragsnr}
{/if}
</div>

<div id='export_header'>
EXPORT / GEPLANNT:
<input type='text' {popup text="zde zadejte cislo exportu, se kterym maji vybrane palety odejit.."} class='input_exportnummer' id='export' name='export' size='10' maxlength='6' />
</div>


<div id='import_table'>
	<div id='scroll_import'>
		<table id='imtable' class='import_table'>
			<tr>
				<td>auftragsnr</td>
				<td>pal</td>
				<td>teil</td>
<!-- pridat moznost zobrazeni poctu kusu importovanych ( jga ) -->
                                <td>IM-Stk</td>
				<td>tatkz</td>
				<td>abgnr</td>
				<td>gut_stk</td>
				<td>A2</td>
				<td>A4</td>
				<td>A6</td>
				<td>G</td>
				<td>Termin</td>
			</tr>

            {assign var="oldpal" value="-1"}
			{foreach from=$fullen item=polozka}
            {if $oldpal != $polozka.pal}
                <tr class='oddel_paletu'>
                    <td align='center' colspan='12'>&nbsp;</td>
                </tr>
                {assign var="oldpal" value=$polozka.pal}
            {/if}
			{if $polozka.kzgut eq 'G'}
			<tr onclick="import2export(this);" id='im{$polozka.id}' class='highlightrow'>
			{else}
			<tr id='im{$polozka.id}' class='even'>
			{/if}
				<td class='right'>{$polozka.AuftragsNr}</td>
				<td class='right'><b>{$polozka.pal}</b></td>
				<td class='left'>{$polozka.Teil}</td>
                                <td class='right'>{$polozka.im_stk}</td>
				<td class='left'>{$polozka.tatkz}</td>
				<td class='right'>{$polozka.abgnr}</td>
                                <td class='right'>{$polozka.gut_stk}</td>
				<td class='right'>{$polozka.auss2}</td>
				<td class='right'>{$polozka.auss4}</td>
				<td class='right'>{$polozka.auss6}</td>
				<td class='left'>{$polozka.kzgut}</td>
				<td class='left'>{$polozka.termin}</td>
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
                                <td>IM-Stk</td>
				<td>tatkz</td>
				<td>abgnr</td>
				<td>gut_stk</td>
				<td>auss2</td>
				<td>auss4</td>
				<td>auss6</td>
			</tr>
		</table>
	</div>
</div>


</div>

<div id='export_fullen_form_footer'>
<table width='100%' border='0' cellspacing='0'>
<tr>
	<td {popup text="klavesova zkratka Alt+f"}> 
		<input accesskey='f'  class='formularbutton' id='exportfullen1' type='button' value='Export fullen' onclick="fillIdlist();getDataReturnXml('./fullen.php?list='+encodeControlValue('idlist')+'&export='+document.getElementById('export').value+'&import={$auftragsnr}',fullenRefresh);" />
<!--		<input class='formularbutton' id='info_D510' type='button' value='FULLEN' onclick="document.location.href='./fullen.php?list='+encodeControlValue('idlist');" /> -->

		<input type='hidden' name='idlist' id='idlist'/>
	</td>
	<td>
		<input accesskey='p'  class='formularbutton' id='planfullen' type='button' value='Plan fullen' onclick="fillIdlist();getDataReturnXml('./planfullen.php?list='+encodeControlValue('idlist')+'&export='+document.getElementById('export').value+'&import={$auftragsnr}',fullenRefresh);" />
	</td>
	<td>
		<input class='formularbutton' id='teil_neu' disabled type='button' value='' onclick=""/>
	</td>
	<td>
		<input {popup text="vyexportuje vsechny zbyvajici palety z vybrane zakazky"} class='formularbutton' type='button' value='Rest exportieren' onclick="exportAll();"/>
	</td>
</tr>
<tr>
	<td>
		<input class='formularbutton' id='teilfilter' type='button' value='Teil waehlen'  />
<!--		<input class='formularbutton' id='info_D510' type='button' value='FULLEN' onclick="document.location.href='./fullen.php?list='+encodeControlValue('idlist');" /> -->

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
</table>
</div>

</body>
</html>

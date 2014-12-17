<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta name="generator" content="Bluefish 1.0.5">
    <title>
      Exportrechnung erstellen / vytvoreni faktury podle exportu
    </title>

<link rel="stylesheet" href="./styl.css" type="text/css">
<link rel="stylesheet" href="../../styldesign.css" type="text/css">
<script type="text/javascript" src="../../js/detect.js"></script>
<script type="text/javascript" src="../../js/eventutil.js"></script>

<!-- YUI stuff -->
<script type="text/javascript" src="../../js/yahoo-dom-event/yahoo-dom-event.js"></script>
<script type="text/javascript" src="../../js/connection/connection-min.js"></script>

<script charset="windows-1250" src="js_functions.js" type="text/javascript"></script>
<script type = "text/javascript" src = "../../js/ajaxgold.js"></script>

</head>

<body onLoad="init_form('show');">
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
umgerechnete Rechnung pflegen 
</div>

<div id="formular_telo">
	<table cellpadding="5px" class="formulartable" border="1">
	    	<tr>
			<td>
			<h2>Rechnung: {$rechnung}</h2> 
			</td>
			<td>
			<h2>Auslieferdatum: {$auslieferdatum}</h2>
			</td>
		</tr>
		<tr>
			<td colspan='2'>
				<div id="scroll">
				<table id='rechnung_table'>
				<tr id='rechnung_table_hlavicka'>
					<td align='left'>Auftragsnr</td>
					<td align='left'>Teil</td>
					<td align='right'>Pal</td>
					<td align='right'>Ausschuss</td>
					<td align='left'>Tat</td>
					<td align='right'>Stk</td>
					<td align='right'>Preis</td>
					<td align='left'>Taetigkeit</td>
					<td align='right'>Vom</td>
					<td align='right'>An</td>
					<td align='right'>Preis Gesamt</td>
					<td align='right'>Waehrung</td>
					<td align='left'>fremdauftr</td>
					<td align='left'>fremdpos</td>
					<td align='left'>oper</td>
				</tr>

				{assign var="citacradku" value="0"}
				{assign var="oldpal" value="-1"}
				{foreach from=$rows item=polozka}
   					{if ($oldpal!=$polozka.pal)}
                        <tr class="oddel_paletu"><td colspan="15">&nbsp;</td></tr>
					{assign var="oldpal" value=$polozka.pal}
					{/if}

					<tr id="radek_{$polozka.id}" {if $citacradku%2}class='rechnung_table_position_even'{else}class='rechnung_table_position_odd'{/if}>
					{assign var="citacradku" value=$citacradku+1}
						<td align='left'><input onblur="YAHOO.util.Connect.asyncRequest('GET','./columnupdate.php?column='+this.id+'&value='+this.value, columnupdate);" class="auftragsnr" id="auftragsnr_{$polozka.id}" name="auftragsnr{$polozka.id}" value="{$polozka.auftragsnr}"/></td>
						<td class="teil" >{$polozka.teil}</td>
						<td align='right'>{$polozka.pal}</td>
						<td align='right'><input onblur="YAHOO.util.Connect.asyncRequest('GET','./columnupdate.php?column='+this.id+'&value='+this.value, columnupdate);" class="ausschuss" id="ausschuss_{$polozka.id}" name="ausschuss{$polozka.id}" value="{$polozka.ausschuss}"/></td>
						<td align='left'>{$polozka.tat} ({$polozka.abgnr})<input type="hidden" class="stk" id="abgnr_{$polozka.id}" name="abgnr{$polozka.id}" value="{$polozka.abgnr}"/></td>
						<td align='right'><input onblur="YAHOO.util.Connect.asyncRequest('GET','./columnupdate.php?column='+this.id+'&value='+this.value, columnupdate);" class="stk" id="stk_{$polozka.id}" name="stk{$polozka.id}" value="{$polozka.stk}"/></td>
						<td align='right'><input onblur="js_validate_float(this);YAHOO.util.Connect.asyncRequest('GET','./columnupdate.php?column='+this.id+'&value='+this.value, columnupdate);" class="stk" id="preis_{$polozka.id}" name="preis{$polozka.id}" value="{$polozka.preis|number_format:4:".":" "}"/></td>
						<td align='left'><input onblur="YAHOO.util.Connect.asyncRequest('GET','./columnupdate.php?column='+this.id+'&value='+this.value, columnupdate);" class="text1" id="text1_{$polozka.id}" name="text1{$polozka.id}" value="{$polozka.text1}"/></td>
						<td align='right'>{$polozka.vom}</td>
						<td align='right'>{$polozka.an}</td>
						<td align='right'>{$polozka.preisges}</td>
						<td align='right'>{$polozka.waehrung}</td>
						<td align='left'><input onblur="YAHOO.util.Connect.asyncRequest('GET','./columnupdate.php?column='+this.id+'&value='+this.value, columnupdate);" class="fremdauftr" id="fremdauftr_{$polozka.id}" name="fremdauftr{$polozka.id}" value="{$polozka.fremdauftr}"/></td>
						<td align='left'><input onblur="YAHOO.util.Connect.asyncRequest('GET','./columnupdate.php?column='+this.id+'&value='+this.value, columnupdate);" class="fremdpos" id="fremdpos_{$polozka.id}" name="fremdpos{$polozka.id}" value="{$polozka.fremdpos}"/></td>
						<td><input onclick="delRechnungUmrechPosition(this);" id="del_{$polozka.id}" name="del{$polozka.id}" type='button' value='del'/></td>
					</tr>
				{/foreach}
				</table>
				</div>
			</td>
		</tr>
	</table>
</div>

<div id='rechnungteilenform'>
<form action="">
	<table>
		<tr>
			<td>
				<label for="rechnrneu">RechnunrNr.:</label>
			</td>
			<td>
				<input onblur="" maxlength='8' size="8" type="text" id="rechnrneu" name="rechnrneu" value=""/>
				<input type='text' class='hidden' id='newtatnr_failed' value='tatnrfehler' />
			</td>
		</tr>
		<tr>
			<td>
				<label for="taetbedingung">TaetNr ></label>
			</td>
			<td>
				<input onblur="" maxlength='4' size="4" type="text" id="taetnrbedingung" name="taetnrbedingung" value=""/>
				<input type='text' class='hidden' id='newbez_d_failed' value='tatnrfehler' />
			</td>
		</tr>
		<tr>
			<td>
				<input type='button' value='markieren' onclick='rechnungNeuMarkieren();'/>
			</td>
			<td>
				<input type='button' value='abbrechen / zrusit' onclick='rechnungNeuMarkierenCancel();' />
			</td>
		</tr>
		<tr>
			<td colspan="2" style="text-align:center;">
				<input style="background-color:red;font-weight:bold;color:white;margin-top:10px;" type='button' value='Teilen !!!' onclick='rechnungNeuTeilen();'/>
			</td>
		</tr>

	</table>
</form>
</div>

<div id='rechumrech_footer'>
<!-- {$sql} -->
<div id='tlacitka_reporty'>
	<input class='' type='button' value='D741 kurz' onclick="location.href='../../get_parameters.php?popisky=Rechnung Nr.&promenne=auftragsnr&values={$rechnung}&report=D741'" id='D741'/>
	<input class='' type='button' value='D751 normal' onclick="location.href='../../get_parameters.php?popisky=Rechnung Nr.&promenne=auftragsnr&values={$rechnung}&report=D751'" id='D751'/>
	<input class='' type='button' value='D761 Summe Teil' onclick="location.href='../../get_parameters.php?popisky=Rechnung Nr.&promenne=auftragsnr&values={$rechnung}&report=D761'" id='D761'/>
	
	<input class='' type='button' value='D743 kurz' onclick="location.href='../../get_parameters.php?popisky=Rechnung Nr.&promenne=auftragsnr&values={$rechnung}&report=D743'" id='D743'/>
	<input class='' type='button' value='D753 normal' onclick="location.href='../../get_parameters.php?popisky=Rechnung Nr.&promenne=auftragsnr&values={$rechnung}&report=D753'" id='D753'/>
	<input class='' type='button' value='D763 Summe Teil' onclick="location.href='../../get_parameters.php?popisky=Rechnung Nr.&promenne=auftragsnr&values={$rechnung}&report=D763'" id='D763'/>

    <input class='' type='button' value='Rechnung wählen' onclick="location.href='./rechumrechwdh.php'" id='rechumrechwdh'/>
    <input class='' type='button' value='Rechnung teilen' onclick="rechnungteilen('{$letzterechnung_sonst}');" id='rechnungteilen'/>
	<input class='formularendbutton' type='button' value='Ende' onclick="document.location.href='../dauftr.php?auftragsnr={$rechnung}';"/>
</div>


</div>
</body>
</html>

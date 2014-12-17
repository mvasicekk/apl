<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta name="generator" content="Bluefish 1.0.5">
    <title>
      DKsd
    </title>

<link rel="stylesheet" href="./styl.css" type="text/css">
<link rel="stylesheet" href="../styldesign.css" type="text/css">

<script type="text/javascript" src="./js/init_controls.js"></script>
<script type="text/javascript" src="../js/detect.js"></script>
<script type="text/javascript" src="../js/eventutil.js"></script>

<!-- YUI stuff -->
<script type="text/javascript" src="../js/yahoo-dom-event/yahoo-dom-event.js"></script>
<script type="text/javascript" src="../js/connection/connection-min.js"></script>

<script src="js_functions.js" type="text/javascript"></script>
<script type = "text/javascript" src = "../js/ajaxgold.js"></script>
</head>

<body onload="">
{popup_init src="../js/overlib.js"}

<!-- 
<div id='souradnice'>
souradnice
</div>
-->

<div align="center" id="podheader">
{if $prihlasen}
	{$user}  level={$level}<a href="../index.php?akce=logout">abmelden/odhlasit</a>
{else}
	Benutzer nicht angemeldet/neprihlaseny uzivatel
{/if}
</div>

<div id="formular_header">
Kundenpaare pflegen / Sprava dvojic zakazniku
</div>

<div id="formular_telo">
{if (strlen($error)>0)}
<div style='background-color:white;'>
<h3>SQL - ERROR</h3>
sql = {$sql}<br>
error = {$error}<br>
</div>
{/if}

<!-- tabulka se seznamem paru zakazniku -->
<br>
<table class="apl_table">
<tr class="apl_table_header">
	<td>vom</td>
	<td>an</td>
	<td>letzte rechnung</td>
	<td>minpreis</td>
	<td>operation</td>
</tr>
{foreach from=$kndpaare item=paar}
<tr bgcolor="{if $paar.id%2 eq 0}white{else}lightgrey{/if}">
	<td>{$paar.vom}</td>
	<td>{$paar.an}</td>
	<td>{$paar.letzterechnung}</td>
	<td>{$paar.minpreis}</td>
	<td><a href="./kndumrech.php?vom={$paar.vom}&an={$paar.an}">edit</a></td>
</tr>
{/foreach}

 
</table>

{if $haspaarinfo==1}
	<!-- vypisu formular s informacem o paru zakazniku -->
	<form id='kndumrechform' action='kndumrech.php' method='post'>
		<dl>
			<dt>&nbsp;</dt>
			<dd>
			<fieldset>
			<legend>Kundenpaar</legend>
			<dl>
				<dt>vom</dt>
				<dd><input onblur="YAHOO.util.Connect.asyncRequest('GET','./validatekndnummer.php?value='+this.value+'&id='+this.id, validatekndnummer);" size="3" maxlength="3" type='text' id='vom' name='vom' value='{$paarinfo.vom}' />&nbsp;<span id='vomname'>{$paarinfo.vomname}</span></dd>
				<dt>an</dt>
				<dd><input onblur="YAHOO.util.Connect.asyncRequest('GET','./validatekndnummer.php?value='+this.value+'&id='+this.id, validatekndnummer);" size="3" maxlength="3" type='text' id='an' name='an' value='{$paarinfo.an}' />&nbsp;<span id='anname'>{$paarinfo.anname}</span></dd>
				<dt>letzte Rechnung</dt>
				<dd><input size="8" maxlength="8" type='text' id='letzterechnung' name='letzterechnung' value='{$paarinfo.letzterechnung}' /></dd>

				<dt>letzte sonstRechnung</dt>
				<dd><input size="8" maxlength="8" type='text' id='letzterechnung_sonst' name='letzterechnung_sonst' value='{$paarinfo.letzterechnung_sonst}' /></dd>
			</dl>
			</fieldset>
			</dd>
			
						<dt>&nbsp;</dt>
			<dd>
			<fieldset>
			<legend>Informationen</legend>
			<dl>
				<dt>minutenpreis</dt>
				<dd><input onblur="js_validate_float(this);" size="6" maxlength="6" type='text' id='minpreis' name='minpreis' value='{$paarinfo.minpreis}' /> {$paarinfo.wahr}/min</dd>
				<dt>frachtkosten</dt>
				<dd><input size="10" maxlength="10" type='text' id='fracht' name='fracht' value='{$paarinfo.fracht}' /> {$paarinfo.wahr}</dd>
				<dt>zoll</dt>
				<dd><input size="10" maxlength="10" type='text' id='zoll' name='zoll' value='{$paarinfo.zoll}' /> {$paarinfo.wahr}</dd>
				<dt>sonst</dt>
				<dd><input size="10" maxlength="10" type='text' id='sonst' name='sonst' value='{$paarinfo.sonst}' /> {$paarinfo.wahr}</dd>
				<dt>waehrung</dt>
				<dd><input size="4" maxlength="4" type='text' id='wahr' name='wahr' value='{$paarinfo.wahr}' /></dd>
				<dt>mwst</dt>
				<dd><input size="3" maxlength="3" type='text' id='mwst' name='mwst' value='{$paarinfo.mwst}' /> %</dd>
				<dt>zahlungsziel</dt>
				<dd><input size="3" maxlength="3" type='text' id='zahlungsziel' name='zahlungsziel' value='{$paarinfo.zahlungsziel}' /> Tage</dd>
				<dt>rechnungstext</dt>
				<dd><input size="60" maxlength="255" type='text' id='rechtext' name='rechtext' value='{$paarinfo.rechtext}' /></dd>
				<dt>kontotext</dt>
				<dd><input size="60" maxlength="255" type='text' id='kontotext' name='kontotext' value='{$paarinfo.kontotext}' /></dd>
				<dt>zweck</dt>
				<dd><input size="60" maxlength="255" type='text' id='verzweck' name='verzweck' value='{$paarinfo.verzweck}' /></dd>
				<dt>Fusszeile 1</dt>
				<dd><input size="60" maxlength="500" type='text' id='fusszeile1' name='fusszeile1' value='{$paarinfo.fusszeile1}' /></dd>
				<dt>Fusszeile 2</dt>
				<dd><input size="60" maxlength="500" type='text' id='fusszeile2' name='fusszeile2' value='{$paarinfo.fusszeile2}' /></dd>
				<dt>Fusszeile 3</dt>
				<dd><input size="60" maxlength="500" type='text' id='fusszeile3' name='fusszeile3' value='{$paarinfo.fusszeile3}' /></dd>

			</dl>
			</fieldset>
			
			<fieldset>
			<dl>
				<dt>&nbsp;</dt>
				<dt>
					<input type="hidden" id='save' name='save' value='1'/>
					<input type='submit' value='speichern'/>
				</dt>
			</dl>
			</fieldset>
			</dd>
			
		</dl>
	</form>
{else}
{/if}
</div>

</body>
</html>

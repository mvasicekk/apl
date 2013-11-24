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
<script type="text/javascript" src="../js/detect.js"></script>
<script type="text/javascript" src="../js/eventutil.js"></script>

<!-- YUI stuff -->
<script type="text/javascript" src="../js/yahoo-dom-event/yahoo-dom-event.js"></script>
<script type="text/javascript" src="../js/connection/connection-min.js"></script>

<script charset="windows-1250" src="js_functions.js" type="text/javascript"></script>
<script type = "text/javascript" src = "../js/ajaxgold.js"></script>
</head>

<body onload="document.getElementById('preis').focus();document.getElementById('preis').select();">
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
Preisaenderung / zmena ceny
</div>

<div id="formular_telo">

<input type="hidden" id="id_dauftr" value="{$id_dauftr}"/><br/>
<input type="hidden" id="pozicdauftr" value="{$pozicdauftr}"/><br/>
<input type="hidden" id="pozicdauftrall" value="{$pozicdauftrall}"/><br/>
<input type="hidden" id="pozicdrueck" value="{$pozicdrueck}"/><br/>
<input type="hidden" id="pozicdrueckall" value="{$pozicdrueckall}"/><br/>
<input type="hidden" id="auftragsnr" value="{$auftragsnr}"/><br/>
<input type="hidden" id="minpreis" value="{$minpreis}"/><br/>
<input type="hidden" id="kunde" value="{$kunde}"/><br/>

<table width="100%">
<tr class='dauftr_table_header'>
<td>Auftrag</td>
<td>teil</td>
<td>pal</td>
<td>taetnr</td>
<td>preis</td>
<td>vzkd</td>
<td>vzaby</td>
</tr>
<tr bgcolor='white'>
<td>{$auftragsnr}</td>
<td>{$teil}</td>
<td>{$pal}</td>
<td>{$abgnr}</td>

<!-- onblur="YAHOO.util.Connect.asyncRequest('GET','./vzkdfrompreis.php?auftragsnr={$auftragsnr_value}&run=1', delrechnung);"  -->

<td><input  id="preis" onblur="prepoctiPreis2VzKd();" type="text" size="8" value="{$preis}"/></td>


<!-- <td id="vzkd">{$vzkd}</td> -->

<td>
	<input id="vzkd" onblur="prepoctiVzKd2Preis();" type="text" size="8" value="{$vzkd}"/>
</td>

<td>
	<input id="vzaby" onblur="js_validate_float(this);" type="text" size="8" value="{$vzaby}"/>
</td>
</tr>
</table>
<p>
<input id="vsechnypalety" onclick="vsechnypalety_onclick();" type="checkbox" /> pro vsechny palety pro dany dil
</p>
<div id='pocetpozic'>
upravim:<br>
<input id="pocetdauftr" size="4" readonly="readonly" value="{$pozicdauftr}"/> pozic v zakazce (DAUFTR).<br>
<input id="pocetdrueck" size="4" readonly="readonly" value="{$pozicdrueck}"/> pozic v DRUECK.<br>
</div>
<p>
<input id='aplsave' type="checkbox" value=""/> nove hodnoty vzkd a vzaby ulozit do pracovniho planu
</p>
</div>


<div id='dauftr_form_footer'>
<table width='100%' border='0' cellspacing='0'>

<tr>
	<td>
		<input class='formularbutton'  type='button' value='preis aendern' onclick="YAHOO.util.Connect.asyncRequest('GET','./gopreisupdate.php?id_dauftr={$id_dauftr}'
																													+'&vsechnypalety='+document.getElementById('vsechnypalety').checked
																													+'&aplsave='+document.getElementById('aplsave').checked
																													+'&preis='+document.getElementById('preis').value
																													+'&vzkd='+document.getElementById('vzkd').value
																													+'&vzaby='+document.getElementById('vzaby').value,
		 																											gopreisupdate);"
		/>
	</td>
	<td>
		<input onClick="" class='formularbutton' type="button" id="D605" name="D605" value=""/>
	</td>
	<td>
		<input onClick="" class='formularbutton' type="button" id="D605" name="D605" value=""/>
	</td>
	<td>
		<input class='formularEndbutton' type='button' value='Ende / konec' onclick="history.back();"/>		
	</td>
</tr>
</div>

<!-- konec formulare -->

</body>
</html>

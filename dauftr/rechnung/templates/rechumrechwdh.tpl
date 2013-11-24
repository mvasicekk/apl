<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta name="generator" content="Bluefish 1.0.5">
    <title>
      Umgerechnete Rechnungen pflegen
    </title>

<link rel="stylesheet" href="./styl.css" type="text/css">
<link rel="stylesheet" href="../styldesign.css" type="text/css">
<script type="text/javascript" src="../../js/detect.js"></script>
<script type="text/javascript" src="../../js/eventutil.js"></script>

<!-- YUI stuff -->
<script type="text/javascript" src="../../js/yahoo-dom-event/yahoo-dom-event.js"></script>
<script type="text/javascript" src="../../js/connection/connection-min.js"></script>

<script charset="windows-1250" src="js_functions.js" type="text/javascript"></script>
<script type = "text/javascript" src = "../../js/ajaxgold.js"></script>
</head>

<body onLoad="document.auftragsuchen_formular.auftragsnr.focus();">
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
Umgerechnete Rechnungen
</div>

<div id="formular_telo">
<table cellpadding="10px" class="formulartable" border="0">
	<form method="post" action='' name="auftragsuchen_formular" onsubmit="">
    <tr>
	<td>
	<span id="content" onclick="hideSuggestions();">
	        <label>
        	Rechnung suchen
	        </label>
	        <input type="text" name="auftragsnr" id="auftragsnr" onfocus="this.select();" onkeyup="YAHOO.util.Connect.asyncRequest('GET','./suggest.php?keyword='+this.value, suggestrechnung);" maxlength="10" size="10">
	</span>
    </td>
	</tr>

	<tr>
	<td>
	
		<div id="scroll">
			<div id="suggest">
			</div>
		</div>
	
    </td>
	</tr>
	
    </form>
	</table>
</div>



</body>
</html>

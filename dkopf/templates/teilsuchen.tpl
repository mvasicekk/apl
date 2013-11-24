<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta name="generator" content="Bluefish 1.0.5">
    <title>
      DLager
    </title>

<link rel="stylesheet" href="./styl.css" type="text/css">
<script type="text/javascript" src="../js/detect.js"></script>
<script type="text/javascript" src="../js/eventutil.js"></script>
<script src="js_functions.js" type="text/javascript"></script>
<script type = "text/javascript" src = "../js/ajaxgold.js"></script>
</head>

<body onLoad="document.teilsuchen_formular.teil.focus();">
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
Arbeitsplan pflegen / Sprava pracovniho planu
</div>

<div id="formular_telo">
<form name="teilsuchen_formular">
<table cellpadding="10px" class="formulartable" border="0">
	
    <tr>
	<td>
	<span id="content">
	        <label>
        	Teil suchen
	        </label>
	        <input type="text" name="teil" id="teil" onfocus="this.select();" onkeyup="getDataReturnXml('./suggest.php?keyword='+this.value, pissuggest);" maxlength="10" size="10">
	        <input type='button' id='teilneubutton' onclick="getDataReturnXml('./new_teil.php?teilneu='+document.getElementById('teil').value, new_teil);" disabled='disabled' class='formularbutton' value='Teil NEU'>
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
	</table>
	</form>
</div>



</body>
</html>

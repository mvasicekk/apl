<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta name="generator" content="eclipse">
    <title>
      DKsd
    </title>

<link rel="stylesheet" href="./styl.css" type="text/css">
<script type="text/javascript" src="../js/detect.js"></script>
<script type="text/javascript" src="../js/eventutil.js"></script>
<script src="js_functions.js" type="text/javascript"></script>
<script type = "text/javascript" src = "../js/ajaxgold.js"></script>
</head>

<body onLoad="document.getElementById('kunde').focus();">
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
Kunden pflegen / Sprava zakazniku
</div>

<div id="formular_telo">
<table cellpadding="10px" class="formulartable" border="0">
	<form method="post" action='' id='kundensuchenFormular' name="kundensuchenFormular" onsubmit="">
    <tr>
	<td>
	<span id="content" onclick="hideSuggestions();">
	        <label>
        	Kunde suchen
	        </label>
	        <input type="text" name="kunde" maxlength='3' id="kunde" onfocus="this.select();" onkeyup="getDataReturnXml('./suggest.php?keyword='+this.value, pissuggest);" maxlength="10" size="10">
	        <input class='hidden' type="button" name="kundeneu" id="kundeneu" onclick="getDataReturnXml('./new_kunde.php?kunde='+document.getElementById('kunde').value, new_kunde);" value='NEU Kunde / novy zakaznik'>
	</span>
    </td>
    <td>
    	<input type="button" id="kndumrech" value="Kundenpaare bearbeiten" onClick="location.href='./kndumrech.php'" />
    </td>
	</tr>

	<tr>
	<td colspan="2">
	
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

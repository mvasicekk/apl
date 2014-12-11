<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=windows-1250">
    <meta name="generator" content="Bluefish 1.0.5">
    <title>
      DLager
    </title>

<link rel="stylesheet" href="./styl.css" type="text/css">
<script type="text/javascript" src="../js/detect.js"></script>
<script type="text/javascript" src="../js/eventutil.js"></script>
<script charset="windows-1250" src="js_functions.js" type="text/javascript"></script>
<script type = "text/javascript" src = "../js/ajaxgold.js"></script>
</head>

<body onLoad="document.formdlager_zugang.auftragsnr.focus();">
{popup_init src="../js/overlib.js"}
  
<div id="header">
<h3 align="center">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Auftragsabwicklung Produktionssteuerung</h3>
</div>



<div align="center" id="podheader">
{if $prihlasen}
	{$user}  level={$level}<a href="index.php?akce=logout">abmelden/odhlasit</a>
{else}
	Benutzer nicht angemeldet/neprihlaseny uzivatel
{/if}
</div>

<div id="formular_header">
Lagerzugang / pridani do skladu
</div>

<div id="formular_telo">
<table cellpadding="10px" class="formulartable" border="1">
	<form method="post" action='save.php' name="formdlager_zugang" onsubmit="beforeSubmit();">
    <tr>
	<td>
		<label>
        Import
        </label>
        <input type="text" name="auftragsnr" id="auftragsnr" maxlength="7" size="7" onblur="">
<!--
        <label>
        Kundenteil
        </label>
        <input type="text" name="teillang" id="teillang" size="15" onblur="getDataReturnText('./matchteillang.php?teillang='+this.value, refreshteil);">
-->
	<span id="content" onclick="hideSuggestions();">
	        <label>
        	Teil
	        </label>
	        <input type="text" name="teil" id="teil" onfocus="this.select();" onkeyup="getDataReturnXml('./suggest.php?keyword='+this.value, pissuggest);" maxlength="10" size="10">
		<div id="scroll">
			<div id="suggest">
			</div>
		</div>
	</span>
        <label>
	Teilbezeichnung
	</label>
        <input type="text" name="teilbez" id="teilbez" maxlength="30" size="20" readonly>
		<input type="text" name="kunde" id="kunde" maxlength="3" size="3" readonly>

    </td>
	</tr>
	<tr>
	<td>
        <label>
        Pal
        </label>
        <input type="text" name="pal" id="pal" onfocus="this.select();" onblur=""  value="0" size="10">
        
		<label>
        Behaelter
		</label>
        <input type="text" name="beh" id="beh" size="4" Value="" onfocus="this.select();" onblur="">
    </td>
	</tr>
	<tr>
	<td>
    <label>
    Stk
    </label>
    <input type="text" name="stk" id="stk" size="6" Value="0" onfocus="this.select();" onblur="">
    <label >
    nach Lager
    </label>
    <select name="nachlager" id="nachlager">
	{html_options values=$lagervalue output=$lageroutput selected="8V"}
    </select>
    
	
    <input type="submit" value="Weiter/Dalsi" id="weiter">
    <input type="button" value="Ende/Konec" id="konec"  onClick="location.href='../index.php'">
	</td>
	</tr>
      </form>
	  </table>
</div>

	<div id="form_footer_tlacitka_reporty">
	</div>
</body>
</html>

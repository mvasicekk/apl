<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta name="generator" content="Bluefish 1.0.5">
    <title>
      Auftraege pflegen
    </title>

<link rel="stylesheet" href="./styl.css" type="text/css">
<link rel="stylesheet" href="../styldesign.css" type="text/css">
<script type="text/javascript" src="../js/detect.js"></script>
<script type="text/javascript" src="../js/eventutil.js"></script>
<script charset="windows-1250" src="js_functions.js" type="text/javascript"></script>
<script type="text/javascript" src="../js/jquery.js"></script>
<script type = "text/javascript" src = "./auftragsuchen.js"></script>
<script type = "text/javascript" src = "../js/ajaxgold.js"></script>

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
Aufträge pflegen / zadani zakazky
</div>

<div id="formular_telo">
<table cellpadding="10px" class="formulartable" border="0">
	<form method="post" action='' name="auftragsuchen_formular" onsubmit="">
    <tr>
	<td>
	<span id="content" onclick="hideSuggestions();">
	        <label>
        	Auftrag suchen / hledat zakázku
	        </label>
	        <input type="text" name="auftragsnr" id="auftragsnr" onfocus="this.select();" onkeyup="getDataReturnXml('./suggest.php?keyword='+this.value, pissuggest);" maxlength="8" size="8">
	        <input class='hidden' type="text" name="kunde" id="kunde" maxlength="3" size="3">
		<input class='hidden' type="button" name="auftragsnrneu" id="auftragsnrneu" onclick="getDataReturnXml('./new_auftrag.php?auftragsnr='+document.getElementById('auftragsnr').value+'&kunde='+document.getElementById('kunde').value, new_auftrag);" value='NEU Auftrag / nova zakazka'>
	</span>
        </td>
        <td>
            <input type='button' id='b_infopanely1' value='Infopanel Halle B1N'/>
	    <input type='button' id='b_infopanely2' value='Infopanel Halle B1SO'/>
	    <input type='button' id='b_infopanely3' value='Infopanel Halle B1W'/>
        </td>
    </tr>

    <tr>
	<td colspan='2'>
	
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

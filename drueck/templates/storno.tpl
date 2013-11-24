<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta name="generator" content="Bluefish 1.0.5">
    <title>
      STORNO Rueckmeldungen / stornovani vykonu
    </title>

<link rel="stylesheet" href="./styl.css" type="text/css">
<script type="text/javascript" src="../js/detect.js"></script>
<script type="text/javascript" src="../js/eventutil.js"></script>
<script src="js_functions.js" type="text/javascript"></script>
<script type = "text/javascript" src = "../js/ajaxgold.js"></script>
<script>


</script>

</head>

<body onLoad="document.getElementById('auftragsnr').focus();rebuildpage();">
{popup_init src="../js/overlib.js"}
  
<!--   
<div id="header">
<h3 align="center">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Auftragsabwicklung Produktionssteuerung</h3>
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
STORNO Rueckmeldungen / stornovani vykonu
</div>

{if $prihlasen}
<div id="filtr">
<table class='dauftr_table'>
	<tr class='dauftr_table_header'>
			<td><u>A</u>uftragsnr</td>
			<td><u>T</u>eil</td>
			<td><u>P</u>al</td>
			<td>Ta<u>e</u>tNr</td>
			<td><u>D</u>atum</td>
			<td>Pe<u>r</u>sNr</td>
			<td></td>
	</tr>
	<tr>
			<td><input id='auftragsnr'  accesskey='a' title='Alt+a' {popup text="muzete stisknout Alt+a pro presun do tohoto policka"} onfocus='this.select();' type='text' name='auftragsnr' size='15' /></td>
			<td><input id='teil'  accesskey='t' title='Alt+t' onfocus='this.select();' type='text' name='teil' size='15' /></td>
			<td><input id='pal' accesskey='p' title='Alt+p' onfocus='this.select();' type='text' name='palette' size='15' /></td>
			<td><input id='taetnr' accesskey='e' title='Alt+e' onfocus='this.select();' type='text' name='taetnr' size='15' /></td>
			<td><input id='datum' accesskey='d' title='Alt+d' onfocus='this.select();' onblur="getDataReturnText('./validate_datum.php?what=datum&allownull=1&value='+this.value+'&controlid='+this.id, refreshdatum);" type='text' name='datum' size='15' /></td>
			<td><input id='persnr' accesskey='r' title='Alt+r' onfocus='this.select();' type='text' name='persnr' size='15' /></td>
			<td>
				<input id='filtruj' accesskey='f' {popup text="muzete stisknout Alt+f misto klikani mysi na toto tlacitko"} onclick="makeButtonBusy(this);savefilterparam();getDataReturnXml('./refreshwhere.php?filterparam='+document.getElementById('filterparam').value,refreshwhere);" value='filtr' type='button' title='Alt+f'/>			
			</td>
			
			<input type='hidden' id='filterparam' name='filterparam'/>
	</tr>
</table>
</div>

<div id='drueck_table'>
	<div id='scroll_apl'>
	<table class='dauftr_table' id='druecktab'>
		<tr class='dauftr_table_header'>
			<td>Auftragsnr</td>
			<td>Teil</td>
			<td>Pal</td>
			<td>TaetNr</td>
			<td>Stk</td>
			<td>Auss</td>
			<td>AArt</td>
			<td>ATyp</td>
			<td>VzKd</td>
			<td>VzAby</td>
			<td>Datum</td>
			<td>PersNr</td>
			<td>von</td>
			<td>bis</td>
			<td>verb</td>
			<td>Pause</td>
			<td>OE</td>
			<td>auft</td>
			<td>user</td>
			<td>stamp</td>
			<td></td>
		</tr>
		{foreach from=$stornorows item=polozka}
		<tr id='tr{$polozka.drueck_id}' class='{cycle values="lichy,sudy"}'>
			<td>{$polozka.auftragsnr}</td>
			<td>{$polozka.teil}</td>
			<td>{$polozka.pal}</td>
			<td align='right'>{$polozka.taetnr}</td>
			<td align='right'>{$polozka.stk}</td>
			<td align='right'>{$polozka.aussstk}</td>
			<td align='right'>{$polozka.aart}</td>
			<td align='right'>{$polozka.atyp}</td>
			<td align='right'>{$polozka.vzkd|string_format:"%.4f"}</td>
			<td align='right'>{$polozka.vzaby|string_format:"%.2f"}</td>
			<td>{$polozka.datum}</td>
			<td align='right'>{$polozka.persnr}</td>
			<td>{$polozka.von}</td>
			<td>{$polozka.bis}</td>
			<td align='right'>{$polozka.verb}</td>
			<td align='right'>{$polozka.pause}</td>
			<td align='right'>{$polozka.oe}</td>
			<td>{$polozka.aufteilung}</td>
			<td>{$polozka.user}</td>
			<td>{$polozka.stamp}</td>
			<td>
				<input {popup text="kliknutim vytvorite pozici s minusovym poctem dobrych kusu, zmetku a spotrebovaneho casu."} onclick="getDataReturnXml('./stornorow.php?id='+this.id,removerow);" type='button' class='stornobutton' value='stor' id='{$polozka.drueck_id}'/>
				<input onclick="window.location.href='./editrow.php?id='+this.id;" type='button' class='editbutton' value='edit' id='{$polozka.drueck_id}'/>
			</td>
		</tr>
		{/foreach}
		</table>
		
	</div>
	
</div>

<div id='storno_form_footer'>
<table width='100%' border='0' cellspacing='0' cellpadding='1'>
<tr>
	<td>
	
	</td>
	<td>
		&nbsp;
	</td>
	<td>
		&nbsp;
	</td>
	<td>
		<input class='formularEndbutton' type='button' value='Ende / konec' onclick="history.back();"/>
	</td>

</tr>
</div>

{/if}


</body>
</html>

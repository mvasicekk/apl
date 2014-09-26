<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta name="generator" content="eclipse">
    <title>
      Schlüsseltabellen / tabulky s klíči
    </title>
<link rel="stylesheet" href="../styl_common.css" type="text/css">
<link rel="stylesheet" href="./styl.css" type="text/css">
<link rel="stylesheet" href="../styldesign.css" type="text/css">
<script type="text/javascript" src="../js/detect.js"></script>
<script type="text/javascript" src="../js/eventutil.js"></script>
<script type = "text/javascript" src = "../js/ajaxgold.js"></script>

<!-- YUI stuff -->
<script type="text/javascript" src="../js/yahoo-dom-event/yahoo-dom-event.js"></script>

<script type = "text/javascript" src = "./js_functions.js"></script>
<script>


</script>

</head>

<body onload="rebuildpage();">
{popup_init src="../js/overlib.js"}
<!-- 
<div id='souradnice'>
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
Schlüsseltabellen anzeigen / zobrazení tabulek
</div>

{if $prihlasen}
<div id="formular_telo">
	<h5>SQL: {$sql}, REC.: {$numrows}</h5>
	<p>orderby = {$order}</p>
	<p>ordersloupec = {$ordersloupec}</p>
	<p>razeni = {$razeni}</p>
	
	<div id='scroll'>
	<table class='dauftr_table' border='0'>
	<tr class='dauftr_table_header'>
	{assign var="citac" value="0"}
	{foreach from=$sloupce item=sloupec}
		<td class='{$typsloupce[$citac]}'>{$sloupec}</td>
		{assign var="citac" value=$citac+1}
	{/foreach}
	</tr>
	{assign var="citacradku" value="0"}
	{foreach from=$radky item=polozka}
		<tr {if $citacradku%2}bgcolor='lightgrey'{else}bgcolor='white'{/if}>
		{assign var="citacradku" value=$citacradku+1}
		{assign var="citac" value="0"}
		{foreach from=$polozka item=bunka}
			<td class='{$typsloupce[$citac]}'>{$bunka}</td>
			{assign var="citac" value=$citac+1}
		{/foreach}
		</tr>	
	{/foreach}
	</table>
	</div>

</div>




<div id='dauftr_form_footer'>
<table width='100%' border='0' cellspacing='0' cellpadding='1'>
<tr>
	<td>
		<input class='formularbutton' type='button' value='' onclick="document.location.href='';"/>
	</td>
	<td>
		<input class='formularbutton' type='button' value='' onclick="document.location.href='';"/>	</td>
	<td>
		<input class='formularbutton' type='button' value='' onclick="document.location.href='';"/>
	</td>
	<td>
		<input class='formularEndbutton' type='button' value='Ende / konec' onclick="history.back();"/>
	</td>

</tr>
</table>
</div>

{/if}

</body>
</html>

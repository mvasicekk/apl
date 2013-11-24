<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta name="generator" content="Bluefish 1.0.5">
    <title>
      Dkopf - teilnr aendern
    </title>

<link rel="stylesheet" href="./styl.css" type="text/css">

<script type="text/javascript" src="../js/detect.js"></script>
<script type="text/javascript" src="../js/eventutil.js"></script>
<script type = "text/javascript" src = "../js/ajaxgold.js"></script>



</head>

<body>
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

{if $prihlasen}
<div id="formular_header">
Arbeitsplan pflegen / Sprava pracovniho planu
</div>
<div id="formular_telo">
    <form action="teilnraendern.php" method="POST">
        <input type="hidden" value="1" name="go" id="go"/>
        <label for="teilOld">TeilNr Alt:</label><input type="text" id="teilOld" name="teilOld" value="{$teilOld}"/><br>
        <label for="teilNew">TeilNr Neu:</label><input type="text" id="teilNew" name="teilNew" value="{$teilNew}"/><br>
        <input type="submit" value="aendern"/>
    </form>

    <div id="zpravy">
    {$zprava}
    
</div>
</div>

<div id='dkopf_form_footer'>
<form action="">
<table width='100%' border='0' cellspacing='0'>
    <tr>
	<td>
		<input class='formularEndbutton' type='button' value='Ende / konec' onclick="document.location.href='../index.php';"/>
	</td>

</tr>
</table>
</form>
</div>

{else}
    prihlaste se !
{/if}

</body>
</html>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
     
    <title>
      APL Abydos
    </title>    
    <link rel="stylesheet" href="./styl.css" type="text/css">
    <link rel="stylesheet" href="../../styldesign.css" type="text/css">
    <script type = "text/javascript" src = "../../js/jquery.js"></script>
    <script  type="text/javascript" src="./js_functions.js"></script>
</head>
<body>
<!-- pomocna procedura k vyzkouseni -->

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

<div align="center" id="parametry">

    <table width="800px" id="fz_teile" style="padding: 5px;" border="1">
    <tr>
        <td colspan="3">Export: <input type="text" id="export" size="6" acturl="validateExport.php"/></td>
        <td colspan="3">Am: <input type="text" id="export_datum" size="10"/></td>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td>TeilNr:</td>
        <td><input type="text" id="teil" size="10" acturl="validateTeil.php"/></td>
        <td>Stk/Beh.:</td>
        <td style="text-align:right;"><input type="text" id="verpackungmenge" size="5"/></td>
        <td>Anz. Behaelter:</td>
        <td style="text-align:right;"><input type="text" id="anzpal" size="5"/></td>

        <td><input type="button" id="addteil" value="+"/></td>
    </tr>

    <tr style="background-color:yellow;">
        <th  style="text-align:left;" colspan="2">Teil</th>
        <th  style="text-align:left;" colspan="2">Stk/Beh.</th>
        <th  style="text-align:left;" colspan="2">Anz.Behaelter</th>
        <th>&nbsp;</th>
    </tr>
</table>

<input type="button" id="fz_drucken" value="Freigabezettel drucken" acturl="fzDrucken.php"/>
<!-- volba pro prvni paletu -->
&nbsp;erster Behaelter:&nbsp;<input type="text" id="erstpal" size="5"/>&nbsp;
<!-- volba pro tisk palety 2x na jeden papir -->
jeder Behaelter 2x <input type="checkbox" id="pal2x" checked="checked"/>
</div>

</body>
</html>

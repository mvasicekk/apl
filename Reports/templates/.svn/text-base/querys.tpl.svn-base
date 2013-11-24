<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=windows-1250">
    <meta name="generator" content="Bluefish 1.0.5">
    <title>
      Berichtswesen / vystupni sestavy
    </title>

<link rel="stylesheet" href="./styl.css" type="text/css">
<link rel="stylesheet" href="../styldesign.css" type="text/css">
<script type="text/javascript" src="../js/detect.js"></script>
<script type="text/javascript" src="../js/eventutil.js"></script>
<script type = "text/javascript" src = "../js/ajaxgold.js"></script>
<script>


</script>

</head>

<body>
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
Schlüsseltabellen anzeigen / zobrazení tabulek
</div>

{if $prihlasen}
<div id="formular_telo">
	<form method="post" action='' name="" onsubmit="">
    <div id='D1XX'>
			<input id="dpers" onClick="location.href='./showquery.php?sql=select persnr,name,vorname,schicht from dpers order by persnr'" class='reportbutton' type="button"  name="dpers" value="DPERS"/>
			<input id="dschicht" onClick="location.href='./showquery.php?sql=select * from dschicht order by schichtnr asc'" class='reportbutton' type="button"  name="dschicht" value="DSCHICHT"/>
			<input id="dtaetigkeiten" onClick="location.href='./showquery.php?sql=select stat_nr,dtaetkz,`abg-nr`,name,oper_cz,oper_d,bemerk,stamp from `dtaetkz-abg` order by `abg-nr`'" class='reportbutton' type="button"  name="dtat" value="DTAETKZ-ABG"/>
			<input id="" value="Auss-art" onClick="location.href='./showquery.php?sql=select * from `auss-art`'" class='reportbutton' type="button"  name="" />
			<input id="" value="Auss-typen" onClick="location.href='./showquery.php?sql=select * from `auss_typen`'" class='reportbutton' type="button"  name="" />
			<input id="" value="Dlager" onClick="location.href='./showquery.php?sql=select * from `dlager`'" class='reportbutton' type="button"  name="" />
			<input id="" value="dproduktgruppen" onClick="location.href='./showquery.php?sql=select * from `dproduktgruppen`'" class='reportbutton' type="button"  name="" />
			<input id="" value="drechtext" onClick="location.href='./showquery.php?sql=select * from `drechtext`'" class='reportbutton' type="button"  name="" />
			<input id="" value="dschichtgruppen" onClick="location.href='./showquery.php?sql=select * from `dschichtgruppen`'" class='reportbutton' type="button"  name="" />
			<input id="" value="dstat" onClick="location.href='./showquery.php?sql=select * from `dstat`'" class='reportbutton' type="button"  name="" />
			<input id="" value="dtaetkz" onClick="location.href='./showquery.php?sql=select * from `dtaetkz`'" class='reportbutton' type="button"  name="" />
			<input id="" value="" onClick="location.href='./showquery.php?sql=select * from `auss-art`'" class='reportbutton' type="button"  name="" />
	</div>

	
   </form>

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
		<input class='formularEndbutton' type='button' value='Ende / konec' onclick="document.location.href='../index.php';"/>
	</td>

</tr>
</table>
</div>

{/if}

</body>
</html>

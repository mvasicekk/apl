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
PHPExcel Exporte / exporty dat do Excelu
</div>

{if $prihlasen}
<div id="formular_telo">
<form method="post" action='' name="" onsubmit="">
	<div>
		<input id="E010" onClick="location.href='../get_parameters.php?popisky=Ausliefdatum von,*DATE;Ausliefdatum bis,*DATE;Kunde&promenne=datevon;datebis;kunde&values=;;;&report=E010'" class='reportbutton' type="button"  name="E010" value="E010 - Exporte / StatNr"/>
	</div>
	<div>
		<input id="E140" onClick="location.href='../get_parameters.php?popisky=Alle MA,*CH;Sort nach,*RA&promenne=alle;sort&values=a;PersNr,geboren&report=E140'" class='reportbutton' type="button"  name="E140" value="E140 - MA Liste"/>
		<input id="E142" onClick="location.href='../get_parameters.php?popisky=Password,password;Monat;Jahr;Datum von,*DATE;Datum bis,*DATE;Persnr von;Persnr bis;Reporttyp,*RA&promenne=password;monat;jahr;von;bis;persvon;persbis;reporttyp&values=;{$aktualniMesic};{$aktualniRok};{$prvnidenmesice};{$dnes};1;99999;lohn&report=E142'" class='reportbutton' type="button"  name="E142" value="E142"/>
		<input id="E145" onClick="location.href='../get_parameters.php?popisky=Datum von,*DATE;Datum bis,*DATE;Pers von;Pers bis&promenne=datevon;datebis;persvon;persbis&values=;;;;&report=E145'" class='reportbutton' type="button"  name="E145" value="E145 - Pers Anwesenheit Edata"/>
	</div>
	<div>
		<input id="E310" onClick="location.href='../get_parameters.php?popisky=Datum von,*DATE;Datum bis,*DATE;Kunde&promenne=datevon;datebis;kunde&values=;;;&report=E310'" class='reportbutton' type="button"  name="E310" value="E310 - Teil - TatNr - Uebersicht"/>
		<input id="E320" onClick="location.href='../get_parameters.php?popisky=Datum von,*DATE;Datum bis,*DATE;Kunde von;Kunde bis&promenne=datevon;datebis;kundevon;kundebis&values=;;;;&report=E320'" class='reportbutton' type="button"  name="E320" value="E320 - Restmengenverwaltung"/>
		<input id="E530" onClick="location.href='../get_parameters.php?popisky=Datum von,*DATE;Datum bis,*DATE&promenne=datevon;datebis&values={$prvnidenmesice};{$dnes}&report=E530'" class='reportbutton' type="button"  name="E530" value="E530 - Reparaturen Ersatzteile"/>
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

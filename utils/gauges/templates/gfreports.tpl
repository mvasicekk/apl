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
GF Berichte / vystupni sestavy pro vedeni
</div>

{if $prihlasen}
<div id="formular_telo">
<form method="post" action='' name="" onsubmit="">
	<div>
		<input id="S090" onClick="location.href='../get_parameters.php?popisky=Datum von,*DATE;Datum bis,*DATE;ITGStk pro Fraese UP;ITGStk pro Fraese DOWN;Belohnung Kc;Strafe Kc&promenne=datevon;datebis;up;down;kcplus;kcminus&values=;;100;80;50;50&report=S090'" class='reportbutton' type="button"  name="fraeseWettkampf" value="Fraeser Wettkampf"/>
                <input id="D793" onClick="location.href='../get_parameters.php?popisky=Kunde;AuslieferDatum von,*DATE;AuslieferDatum bis,*DATE;Reporttyp,*RA;KomplexKZ;Rechnung,*RA&promenne=kunde;von;bis;typ;komplex;rechnung&values=;;;sort Teil,sort Komplex,summen Teil_TatNr;*;DCZ,ABY&report=D793'" class='reportbutton' type="button"  name="D793" value="D793 - Umsatztatliste"/>
	</div>
	<div>
		<input id="S282" onClick="location.href='../get_parameters.php?popisky=Password,password;Datum von,*DATE;Datum bis,*DATE;Pers von;Pers bis;Schicht von;Schicht bis;Kunde&promenne=password;datum_von;datum_bis;pers_von;pers_bis;schicht_von;schicht_bis;kunde&values=;{$predchozi_den};{$predchozi_den};1;99999;1;9999;*&report=S282'" class='reportbutton' type="button"  name="S282" value="S282 - WerkvertraegeAbrechnung Detail"/>
		<input id="S284" onClick="location.href='../get_parameters.php?popisky=Password,password;Datum von,*DATE;Datum bis,*DATE;Pers von;Pers bis&promenne=password;datum_von;datum_bis;pers_von;pers_bis&values=;{$predchozi_den};{$predchozi_den};1;99999&report=S284'" class='reportbutton' type="button"  name="S284" value="S284 - WerkvertraegeAbrechnung"/>
                <input id="S430" onClick="location.href='../get_parameters.php?popisky=Password,password;Kunde;Teil;TaetNr;Report,*RA;J-BED,*RA;mit ALT,*RA;Preise,*RA&promenne=password;kunde;teil;abgnr;typ;jb;alt;preise&values=;;*;*;STANDARD,mit ZP,PREIS/STK;nein,ja;ja,nein;aktuell,neu&report=S430'" class='reportbutton' type="button"  name="S430" value="S430 - Kunde - Preise und Vorgabezeiten"/>
	</div>
	<div>
		<input id="S820" onClick="location.href='../get_parameters.php?popisky=Password,password;Datum von,*DATE;Datum bis,*DATE;Kunde von;Kunde bis;Reporttyp,*RA&promenne=password;datevon;datebis;kundevon;kundebis;reporttyp&values=;{$now};{$now};0;999;sort VerbZeit,sort Teil&report=S820'" class='reportbutton' type="button"  name="s820" value="S820 - Teilstat-Sort-VerbSumme ('Hitliste')"/>
                <input id="S821" onClick="location.href='../get_parameters.php?popisky=Password,password;Datum von,*DATE;Datum bis,*DATE;Kunde von;Kunde bis&promenne=password;datevon;datebis;kundevon;kundebis&values=;{$now};{$now};0;999&report=S821'" class='reportbutton' type="button"  name="s821" value="S821 - Teilstat-Sort-VerbSumme FarbeAnalyse "/>
	</div>
    	<div>
		<input id="T003" onClick="location.href='../get_parameters.php?popisky=Kunde;Preis alt;Preis neu&promenne=kunde;preis_alt;preis_neu&values=;;&report=T003'" class='reportbutton' type="button"  name="T003" value="T003 - Minpreisaendern Stat"/>
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

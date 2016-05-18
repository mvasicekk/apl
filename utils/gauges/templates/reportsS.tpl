<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta name="generator" content="Bluefish 1.0.5">
    <title>
      Berichtswesen / vystupni sestavy
    </title>

<link rel="stylesheet" href="./styl.css" type="text/css">
<link rel="stylesheet" href="../styldesign.css" type="text/css">
<script type="text/javascript" src="../js/detect.js"></script>
<script type="text/javascript" src="../js/eventutil.js"></script>
<script type = "text/javascript" src = "../js/ajaxgold.js"></script>

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
Berichte / vystupni sestavy
</div>

{if $prihlasen}
<div id="formular_telo">
	<form method="post" action='' name="" onsubmit="">
    <div id='D1XX'>
			<input id="D350" onClick="location.href='../get_parameters.php?popisky=AuftragsNr&promenne=auftragsnr&values=&report=D350'" class='reportbutton' type="button"  name="D350" value="D350 - Ausschuss Auftrag mit Palette"/>
			<input id="D360" onClick="location.href='../get_parameters.php?popisky=AuftragsNr&promenne=auftragsnr&values=&report=D360'"class='reportbutton' type="button"  name="D360" value="D360 - Ausschuss Auftrag"/>
			<input id="D361" onClick="location.href='../get_parameters.php?popisky=AuftragsNr von;AuftragsNr bis&promenne=auftragsnr_von;auftragsnr_bis&values=&report=D361'"class='reportbutton' type="button"  name="D361" value="D361 - Summe Auschuss nach Ausschussarten"/>
			<input id="D370" onClick="location.href='../get_parameters.php?popisky=AuftragsNr&promenne=auftragsnr&values=&report=D370'"class='reportbutton' type="button"  name="D370" value="D370 - Ausschuss Auftrag mit Palette nach Aussuchsstypen"/>
            <input id="D520" onClick="location.href='../get_parameters.php?popisky=Import&promenne=import&values=&report=D520'"class='reportbutton' type="button"  name="D520" value="D520 - Arbeitsplan für Auftra"/>
            <input id="D570" onClick="location.href='../get_parameters.php?popisky=Kunde;Datum von;Datum bis;sortieren nach Original&promenne=kunde;datumvon;datumbis;teillangsort&values=0;{$prvnidenroku};{$dnes};0&report=D570'"class='reportbutton' type="button"  name="D570" value="D570 - Musterlager"/>
			<input id="D710" onClick="location.href='../get_parameters.php?popisky=Export;Termin&promenne=export;termin&values=;{$now}&report=D710'" class='reportbutton' type="button"  name="D710" value="D710 - Liefer und Leistungsuebersicht"/>
	</div>

	<div id='S1XX'>
                        <!--get_parameters.php?popisky=Export (leer = Blanko );Termin;Text,*RA;Wassermarke,*RA&promenne=export;termin;popisek;watermark&values={$auftragsnr_value};{$ausliefer_datum_value};FREIGABE,ZWEIFLER;nein,ja&report=D64X'-->
                        <input id="S110" onClick="location.href='../get_parameters.php?popisky=Datum von;Datum bis;Schicht von;Schicht bis&promenne=von;bis;schichtvon;schichtbis&values={$prvnidenmesice};{$dnes};1;999&report=S110'" class='reportbutton' type="button"  name="S110" value="S110 - Pplanschichtstatistik"/>
                        <input id="S112" onClick="location.href='../get_parameters.php?popisky=Datum von;Datum bis&promenne=von;bis&values={$prvnidenmesice};{$dnes}&report=S112'" class='reportbutton' type="button"  name="S112" value="S112 - PplanOEstatistik"/>
                        <input id="S122" onClick="location.href='../get_parameters.php?popisky=Password,password;Monat;Jahr;Persnr von;Persnr bis;OE;Reporttyp,*RA&promenne=password;monat;jahr;persvon;persbis;oe;reporttyp&values=;{$aktualniMesic};{$aktualniRok};1;99999;*;ist&report=S122'" class='reportbutton' type="button"  name="S122" value="S122 - Personal Plan / Anwesenheit"/>
                        <!--<input id="S122" onClick="location.href='../get_parameters.php?popisky=Tag von;Tag bis;Monat;Jahr;Persnr von;Persnr bis&promenne=tagvon;tagbis;monat;jahr;persvon;persbis&values={$tagvon};{$tagbis};{$aktualniMesic};{$aktualniRok};1;99999&report=S122'" class='reportbutton' type="button"  name="S122" value="S122 - Persplan - Leistung / Anwesenheit / Anwesenheit"/>-->
                        <input id="S132" onClick="location.href='../get_parameters.php?popisky=Password,password;Tag von;Tag bis;Monat;Jahr;Persnr von;Persnr bis;OE;Reporttyp,*RA&promenne=password;tagvon;tagbis;monat;jahr;persvon;persbis;oe;reporttyp&values=;{$tagvon};{$tagbis};{$aktualniMesic};{$aktualniRok};1;99999;*;ist&report=S132'" class='reportbutton' type="button"  name="S132" value="S132 - Personal Plan / Anwesenheit"/>
                        <!--<input id="S134" onClick="location.href='../get_parameters.php?popisky=Datum von;Datum bis;Persnr von;Persnr bis;OE&promenne=von;bis;persvon;persbis;oe&values=;;1;99999&report=S134'" class='reportbutton' type="button"  name="S134" value="S134 - Personal Plan / Anwesenheit"/>-->
                        <input id="S140" onClick="location.href='../get_parameters.php?popisky=Password,password;Monat;Jahr;Persnr von;Persnr bis&promenne=password;monat;jahr;persvon;persbis&values=;{$aktualniMesic};{$aktualniRok};1;99999&report=S140'" class='reportbutton' type="button"  name="S140" value="S140 - PplanGesamt"/>
			<input id="S160" onClick="location.href='../get_parameters.php?popisky=Datum;Schicht von;Schicht bis&promenne=datum;schicht_von;schicht_bis&values={$predchozi_den};1;99999&report=S160'" class='reportbutton' type="button"  name="S160" value="S160 - Leistung-Tag-Anwesenheit"/>
			<input id="S168" onClick="location.href='../get_parameters.php?popisky=Datum;Schicht von;Schicht bis&promenne=datum;schicht_von;schicht_bis&values={$predchozi_den};1;99999&report=S168'" class='reportbutton' type="button"  name="S168" value="S168 - Leistung-Tag-Anwesenheit"/>
	</div>
	
	<div id='S2XX'>
			<input id="S210" onClick="location.href='../get_parameters.php?popisky=AuftragsNr&promenne=auftragsnr&values=&report=S210'" class='reportbutton' type="button"  name="S210" value="S210 - Leistung Auftrag - Teil"/>
			<input id="S210noex" onClick="location.href='../get_parameters.php?popisky=AuftragsNr von;AuftragsNr bis;Teil&promenne=auftragsnr_von;auftragsnr_bis;teil&values=;;*&report=S210noex'"class='reportbutton' type="button"  name="S210noex" value="S210 - Leistung Auftrag - Teil ohne Export"/>
			<input id="S211" onClick="location.href='../get_parameters.php?popisky=ExportNr&promenne=export&values=&report=S211'" class='reportbutton' type="button"  name="D350_etc" value="S211 - Leistung Auftrag - Teil nach Export"/>
			<input id="S214" onClick="location.href='../get_parameters.php?popisky=geplant mit von;geplant mit bis;abgnr von;abgnr bis;StatNr,*CB&promenne=gepl_von;gepl_bis;abgnrvon;abgnrbis;statnr&values=;;0;9999;{$statpolozky}&report=S214'" class='reportbutton' type="button"  name="S214" value="S214 - Leistung Auftrag - geplant"/>
			<input disabled="disabled" id="S220" onClick="location.href='../get_parameters.php?popisky=Auftrag;Teil&promenne=auftragsnr;teil&values=;*&report=S220'" class='reportbutton' type="button"  name="S220" value="S220 - Leistung Auftrag - Palette Import"/>
			<input id="S221" onClick="location.href='../get_parameters.php?popisky=ExportNr&promenne=export&values=&report=S221'"class='reportbutton' type="button"  name="S221" value="S221 - Leistung Auftrag - Palette Export"/>
			<input id="S240" onClick="location.href='../get_parameters.php?popisky=Datum von;Datum bis&promenne=datum_von;datum_bis&values={$predchozi_den};{$predchozi_den}&report=S240'" class='reportbutton' type="button"  name="S240" value="S240 - Leistung Tat.gruppe - Kunde - Auftrag"/>
			<input id="S250" onClick="location.href='../get_parameters.php?popisky=Datum von;Datum bis;Schicht von;Schicht bis&promenne=datum_von;datum_bis;schicht_von;schicht_bis&values={$predchozi_den};{$predchozi_den};1;99999&report=S250'" class='reportbutton' type="button"  name="S250" value="S250 - Leistung Schicht - Auftrag"/>
			<input id="S280" onClick="location.href='../get_parameters.php?popisky=Datum von;Datum bis;Pers von;Pers bis;Schicht von;Schicht bis;Kunde;Tat von;Tat bis&promenne=datum_von;datum_bis;pers_von;pers_bis;schicht_von;schicht_bis;kunde;tatvon;tatbis&values={$predchozi_den};{$predchozi_den};1;99999;1;9999;*;0;9999&report=S280'" class='reportbutton' type="button"  name="S280" value="S280 - Leistung MA - Auftrag - Teil (Dukla)"/>
	</div>
	
	<div id='S3XX'>
			<input id="S310" onClick="location.href='../get_parameters.php?popisky=Auftrag von;Auftrag bis;Teil;Taetigkeit;Personalnummer&promenne=auftragsnr_von;auftragsnr_bis;teil;tat;persnr&values=;;;*;*&report=S310'" class='reportbutton' type="button"  name="S310" value="S310 - Leistung Auftrag - Teil - Tat - Datum - MA"/>
			<input id="S311" onClick="location.href='../get_parameters.php?popisky=Export;Teil;Taetigkeit;Personalnummer&promenne=export;teil;tat;persnr&values=;*;*;*&report=S311'" class='reportbutton' type="button"  name="S311" value="S311 - Leistung Auftrag - Teil - Tat - Datum - MA nach Export"/>
			<input id="S313" onClick="location.href='../get_parameters.php?popisky=Auftrag von;Auftrag bis;Teil;Pal von;Pal bis&promenne=auftragsnr_von;auftragsnr_bis;teil;palvon;palbis&values=;;*;0;9999&report=S313'" class='reportbutton' type="button"  name="S313" value="S313 - Leistung Auftrag - Teil - Pal"/>
			<input id="S390" onClick="location.href='../get_parameters.php?popisky=Teil&promenne=teil&values=&report=S390'" class='reportbutton' type="button"  name="S390" value="S390 - Lagerbestand - Teil"/>
            <input id="S395" onClick="location.href='../get_parameters.php?popisky=Teil;Kunde;Zeitpunkt&promenne=teil;kunde;zeitpunkt&values=;111;{$nowtime}&report=S395'" class='reportbutton' type="button"  name="S395" value="S395 - Lagerbestand - Teil"/>
	</div>

	<div id='S6XX'>
			<input id='S610' class='reportbutton' type="button" id="S610" name="S610" onClick="location.href='../get_parameters.php?popisky=Datum von;Datum bis&promenne=datevon;datebis&values={$now};{$now}&report=S610'" value="S610 - VzKd pro Lieferung und Taetigkeitsgruppe"/>
	</div>
		
	<div id='S7XX'>
			<input id="S790" onClick="location.href='../get_parameters.php?popisky=Kunde von;Kunde bis;Auslieferdatum von;Auslieferdatum bis&promenne=kunde_von;kunde_bis;ausliefer_von;ausliefer_bis&values=0;999;{$now};{$now}&report=S790'" class='reportbutton' type="button"  name="S790" value="S790 - Lieferungsuebersicht nach Import"/>
<!--            <input id="S790" disabled='disabled' class='reportbutton' type="button"  name="S610" value="S790 - Lieferungsuebersicht"/> -->
			<input id="S791" onClick="location.href='../get_parameters.php?popisky=Kunde von;Kunde bis;Auslieferdatum von;Auslieferdatum bis&promenne=kunde_von;kunde_bis;ausliefer_von;ausliefer_bis&values=0;999;{$now};{$now}&report=S791'" class='reportbutton' type="button"  name="S791" value="S791 - Lieferungsuebersicht nach Export"/>
			<input id="S795" onClick="location.href='../get_parameters.php?popisky=Kunde von;Kunde bis;Aufträge von;Rückmeldungen von;Rückmeldungen bis;Zeitpunkt&promenne=kunde_von;kunde_bis;auftr_von;rm_von;rm_bis;zeitpunkt&values=0;999;;;;&report=S795'" class='reportbutton' type="button"  name="S795" value="S795 - Abgrenzungstabelle"/>
	</div>
	
	<div id='S8XX'>
			<input id="S810" onClick="location.href='../get_parameters.php?popisky=Auftrag von;Auftrag bis;Teil&promenne=auftragsnr_von;auftragsnr_bis;teil&values=;;&report=S810'" class='reportbutton' type="button"  name="S810" value="S810 - Teil - Bearbeitungsstand"/>
			<input id="S813" disabled='disabled' class='reportbutton' type="button"  name="S610" value="S813 - Teil - Bearbeitungsstand ohne Rechnung"/>
			<input id="S816" onClick="location.href='../get_parameters.php?popisky=Auftrag von;Auftrag bis;Teil&promenne=auftragsnr_von;auftragsnr_bis;teil&values=;;*&report=S816'" class='reportbutton' type="button"  name="S816" value="S816 - T_neodeslane"/>
			<input id="S870" onClick="location.href='../get_parameters.php?popisky=Auslieferdatum von;bis;Kunde;Teil&promenne=ausliefer_von;ausliefer_bis;kunde;teil&values={$min_mesic_od};{$min_mesic_do};111;06017272&report=S870'" class='reportbutton' type="button"  name="S870" value="S870 - Taetigkeiten in Rechnungen"/>
            <input id="S890" onClick="location.href='../get_parameters.php?popisky=Kunde von;Kunde bis;Auftragsdatum von;Auftragsdatum von&promenne=kundevon;kundebis;aufdatvon;aufdatbis&values=0;999;;&report=S890'" class='reportbutton' type="button"  name="S890" value="S890 - Intrastat Importe"/>
            <input id="S895" onClick="location.href='../get_parameters.php?popisky=Kunde von;Kunde bis;Auslieferdatum von;Auslieferdatum von&promenne=kundevon;kundebis;aufdatvon;aufdatbis&values=0;999;;&report=S895'" class='reportbutton' type="button"  name="S895" value="S895 - Intrastat Exporte"/>
	</div>

    <div id='miscReports'>
			<input id="LagerVisacky" onClick="location.href='../get_parameters.php?popisky=Kunde;Regal;Teil&promenne=kunde;regal;teil&values=;;&report=T002'" class='reportbutton' type="button"  name="T002" value="T002 - Lagrvisacky"/>
            <input id="S910" onClick="location.href='../get_parameters.php?popisky=Kunde von;Kunde bis&promenne=kundevon;kundebis&values=0;9999&report=S910'" class='reportbutton' type="button"  name="S910" value="S910 - Abydos - Kunden"/>
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

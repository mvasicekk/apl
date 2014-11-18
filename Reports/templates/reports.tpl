<!DOCTYPE html>
<html>
    <head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<title>
	    Berichtswesen / vystupni sestavy
	</title>
	<link rel="stylesheet" href="../styl_common.css" type="text/css">
	<link rel="stylesheet" href="./styl.css" type="text/css">
	<script type="text/javascript" src="../js/detect.js"></script>
	<script type="text/javascript" src="../js/eventutil.js"></script>
	<script type = "text/javascript" src = "../js/ajaxgold.js"></script>
    </head>
	
    <body>
	{include file='../../templates/heading.tpl'}
	<div id="formular_header">
	    Berichte / vystupni sestavy
	</div>

{*	<div>
	    <pre>
		{$display_sec}
	    </pre>
	</div>
*}	{if $prihlasen}
	    <div id="formular_telo">
		<div id='buttoncontainer'>
		    <div id='rs_DXXX' style="display:{$display_sec.rs_DXXX}">
			<input id="D350" onClick="location.href='../get_parameters.php?popisky=AuftragsNr&promenne=auftragsnr!number&values=&report=D350'" class='reportbutton' type="button"  name="D350" value="D350 - Ausschuss Auftrag mit Palette"/>
			<input id="D360" onClick="location.href='../get_parameters.php?popisky=AuftragsNr&promenne=auftragsnr!number&values=&report=D360'"class='reportbutton' type="button"  name="D360" value="D360 - Ausschuss Auftrag"/>
			<input id="D361" onClick="location.href='../get_parameters.php?popisky=AuftragsNr von;AuftragsNr bis&promenne=auftragsnr_von!number;auftragsnr_bis!number&values=&report=D361'"class='reportbutton' type="button"  name="D361" value="D361 - Summe Auschuss nach Ausschussarten"/>
			<input id="D370" onClick="location.href='../get_parameters.php?popisky=AuftragsNr&promenne=auftragsnr!number&values=&report=D370'"class='reportbutton' type="button"  name="D370" value="D370 - Ausschuss Auftrag mit Palette nach Aussuchsstypen"/>
			<input id="D516" onClick="location.href='../get_parameters.php?popisky=Kunde;Teil;DokuNr&promenne=kunde!number;teil;dokunr!number&values=0;*;29&report=D516'"class='reportbutton' type="button"  name="D516" value="D516 - Lagerzettel"/>
			<input id="D520" onClick="location.href='../get_parameters.php?popisky=Import&promenne=import!number&values=&report=D520'"class='reportbutton' type="button"  name="D520" value="D520 - Arbeitsplan für Auftra"/>
			<input id="D550" onClick="location.href='../get_parameters.php?popisky=Kunde von;Kunde bis;Teil;Datum von,*DATE;Datum bis,*DATE;Max. Bilder&promenne=kundevon!number;kundebis!number;teil;von;bis;maxbild!number&values=0;999;*;{$prvnidenroku};{$now};0&report=D550'" class='reportbutton' type="button"  name="D550" value="D550 - Mehrarbeit Uebersicht"/>
			<input id="D555" onClick="location.href='../get_parameters.php?popisky=IMANr&promenne=imanr&values=&report=D555'" class='reportbutton' type="button"  name="D555" value="D555 - Mehrarbeitsanmeldung"/>
			<input id="D571" onClick="location.href='../get_parameters.php?popisky=Kunde;Auftragsdatum von,*DATE;Auftragsdatum bis,*DATE;DokuNr;sortieren nach,*RA&promenne=kunde!number;datumvon;datumbis;dokunr!number;sort&values=0;;;*;TeilNr,TeilNr - Original&report=D571'" class='reportbutton' type="button"  name="D571" value="D571 - Teiledokumentation"/>
			<input id="D64Y" onClick="location.href='../dauftr/freigabezettel/fz_parameters.php'" class='reportbutton' type="button"  name="D64Y" value="D6XX - Freigabezettel D6XX mit Charge"/>
			<input id="D710" onClick="location.href='../get_parameters.php?popisky=Export;Termin,*DATE&promenne=export!number;termin&values=;{$now}&report=D710'" class='reportbutton' type="button"  name="D710" value="D710 - Liefer und Leistungsuebersicht"/>
			<input id="D720" onClick="location.href='../get_parameters.php?popisky=Export;Termin,*DATE;Typ,*RA&promenne=export!number;termin;typ&values=;{$now};Gute Teile,Ausschuss,Mehrarbeit&report=D720'" class='reportbutton' type="button"  name="D720" value="D720 - Liefer und Leistungsuebersicht"/>
			<input id="D810" onClick="location.href='../get_parameters.php?popisky=Kunde von;Kunde bis;Spediteur von;Spediteur bis;Datum von,*DATE;Datum bis,*DATE;Kurs,*RA;Typ,*RA&promenne=kundevon!number;kundebis!number;spedvon!number;spedbis!number;terminvon;terminbis;kurs;typ&values=0;999;0;999;{$now};{$now};aktuell,kalk;Spediteur,InfoKD,Dispo&report=D810'" class='reportbutton' type="button"  name="D810" value="D810 - Rundlauf"/>
		    </div>
	
		    <div id='rs_S1XX' style="display:{$display_sec.rs_S1XX}">
                        <input id="S102" onClick="location.href='../get_parameters.php?popisky=Password,password;Pers von;Pers bis;Sort,*RA;Ohne Austritt,*RA&promenne=password;persvon!number;persbis!number;sort;austritt&values=;0;99999;Persnr,geboren;ja,nein&report=S102'" class='reportbutton' type="button"  name="S102" value="S102 - Personal Geburtstag-/Telefonliste"/>
                        <input id="S112" onClick="location.href='../get_parameters.php?popisky=Password,password;Datum von,*DATE;Datum bis,*DATE;Reporttyp,*RA&promenne=password;von;bis;reporttyp&values=;{$prvnidenmesice};{$dnes};OE-IST,OE-ST&report=S112'" class='reportbutton' type="button"  name="S112" value="S112 - PplanOEstatistik"/>
                        <input id="S122" onClick="location.href='../get_parameters.php?popisky=Password,password;Tag von;Tag bis;Monat;Jahr;Persnr von;Persnr bis;OE;Reporttyp,*RA&promenne=password;tagvon;tagbis;monat!number;jahr!number;persvon!number;persbis!number;oe;reporttyp&values=;{$tagvon};{$tagbis};{$aktualniMesic};{$aktualniRok};1;99999;*;Leistung/Anwesenheit,Anwesenheit&report=S122'" class='reportbutton' type="button"  name="S122" value="S122 - Persplan - Leistung / Anwesenheit / Anwesenheit"/>
                        <input id="S123" onClick="location.href='../get_parameters.php?popisky=Password,password;Tag von;Tag bis;Monat;Jahr;Persnr von;Persnr bis;OE&promenne=password;tagvon!number;tagbis!number;monat!number;jahr!number;persvon!number;persbis!number;oe&values=;{$tagvon};{$tagbis};{$aktualniMesic};{$aktualniRok};1;99999;*&report=S123'" class='reportbutton' type="button"  name="S123" value="S123 - Persplan"/>
                        <input id="S132" onClick="location.href='../get_parameters.php?popisky=Password,password;Tag von;Tag bis;Monat;Jahr;Persnr von;Persnr bis;OE;Reporttyp,*RA&promenne=password;tagvon!number;tagbis!number;monat!number;jahr!number;persvon!number;persbis!number;oe;reporttyp&values=;{$tagvon};{$tagbis};{$aktualniMesic};{$aktualniRok};1;99999;*;soll,ist,sollist&report=S132'" class='reportbutton' type="button"  name="S132" value="S132 - Personal Plan / Anwesenheit"/>
                        <input id="S134" onClick="location.href='../get_parameters.php?popisky=Password,password;Datum von,*DATE;Datum bis,*DATE;Persnr von;Persnr bis;OE&promenne=password;von;bis;persvon!number;persbis!number;oe&values=;;;1;99999&report=S134'" class='reportbutton' type="button"  name="S134" value="S134 - Personal Plan / Anwesenheit"/>
                        <input id="S135" onClick="location.href='../get_parameters.php?popisky=Password,password;Datum von,*DATE;Datum bis,*DATE;Persnr von;Persnr bis;OE;Reporttyp,*RA&promenne=password;von;bis;persvon!number;persbis!number;oe;reporttyp&values=;;;1;99999;*;OE,nichtanwesend&report=S135'" class='reportbutton' type="button"  name="S135" value="S135 - Personal Plan / Anwesenheit"/>
                        <input id="S142" onClick="location.href='../get_parameters.php?popisky=Password,password;Monat;Jahr;Datum von,*DATE;Datum bis,*DATE;Persnr von;Persnr bis;Reporttyp,*RA&promenne=password;monat!number;jahr!number;von;bis;persvon!number;persbis!number;reporttyp&values=;{$aktualniMesic};{$aktualniRok};{$prvnidenmesice};{$dnes};1;99999;lohn,info,infoVonBis&report=S142'" class='reportbutton' type="button"  name="S142" value="S142 - Lohnberechnung"/>
                        <input id="S145" onClick="location.href='../get_parameters.php?popisky=Password,password;Persnr von;Persnr bis;Pouze na dobu neurcitou,*CH;Smlouvy koncici od,*DATE;Smlouvy koncici do,*DATE;Zkusebni doba,*CH;Zkusebni doba od,*DATE;Zkusebni doba do,*DATE;2 roky doba urcita,*CH&promenne=password;persvon!number;persbis!number;dobaneurcita;befrvon;befrbis;zkusdoba;zkusdobaod;zkusdobado;roky2&values=;1;99999;a;;;a;{$prvnidenmesice};{$now};a&report=S145'" class='reportbutton' type="button"  name="S145" value="S145 - DPers Vertraege"/>
                        <input id="S165" onClick="location.href='../get_parameters.php?popisky=Datum,*DATE&promenne=datum&values={$now}&report=S165'" class='reportbutton' type="button"  name="S165" value="S165 - Anwesenheit - Tag"/>
                        <input id="S166" onClick="location.href='../get_parameters.php?popisky=Datum von,*DATE;Datum bis,*DATE;PersNr von;PersNr bis&promenne=von;bis;persvon!number;persbis!number&values={$now};{$now};0;99999&report=S166'" class='reportbutton' type="button"  name="S166" value="S166 - Anwesenheit-Tag-Edata"/>
                        <input id="S167" onClick="location.href='../get_parameters.php?popisky=Password,password;voraus. Eintritt ab,*DATE;voraus. Eintritt bis,*DATE;Bewerbungsdatum von,*DATE;Bewerbungsdatum bis,*DATE;fuer Aby geeignet,*CB;OE-P,*CB&promenne=password;eintrittab;eintrittbis;bewerbdatvon;bewerbdatbis;geeignet;vorausoe&values=;;;;;{$geeignet};{$oes}&report=S167'" class='reportbutton' type="button"  name="S167" value="S167 - Personal Bewerber"/>
                        <input id="S168" onClick="location.href='../get_parameters.php?popisky=Datum,*DATE&promenne=datum&values={$predchozi_den}&report=S168'" class='reportbutton' type="button"  name="S168" value="S168 - Leistung->Anwesenheit"/>
                        <input id="S169" onClick="location.href='../get_parameters.php?popisky=Password,password;Monat;Jahr;Reporttyp,*RA&promenne=password;monat!number;jahr!number;reporttyp&values=;{$aktualniMesic};{$aktualniRok};alle,Eintritt,Austritt&report=S169'" class='reportbutton' type="button"  name="S169" value="S169 - Personal Lohn-Parameters"/>
                        <input id="S170" onClick="location.href='../get_parameters.php?popisky=Password,password;PersNr von;PersNr bis;Qualifikationstyp,*CB&promenne=password;persvon!number;persbis!number;qtyp&values=;1;99999;{$qtypen}&report=S170'" class='reportbutton' type="button"  name="S170" value="S170 - Personal Qualifikationen"/>
                        <input id="S171" onClick="location.href='../get_parameters.php?popisky=Password,password;PersNr von;PersNr bis;Qualifikationstyp,*CB&promenne=password;persvon!number;persbis!number;qtyp&values=;1;99999;{$qtypenS171}&report=S171'" class='reportbutton' type="button"  name="S171" value="S171 - Personal Qualifikationen (Q0011-Q0061)"/>
                        <input id="S173" onClick="location.href='../get_parameters.php?popisky=Schulungdatum von,*DATE;Schulungdatum bis,*DATE;PersNr von;PersNr bis;Schulung,*CB&promenne=schulungvon;schulungbis;persvon!number;persbis!number;schulung&values={$prvnidenroku};{$dnes};1;99999;{$schulungen}&report=S173'" class='reportbutton' type="button"  name="S173" value="S173 - Schulungen - Datum - Persnr"/>
                        <input id="S182" onClick="location.href='../get_parameters.php?popisky=Datum Von,*DATE;Datum Bis,*DATE;PersNr von;PersNr bis&promenne=von;bis;persvon!number;persbis!number&values={$prvnidenmesice};{$dnes};0;99999&report=S182'" class='reportbutton' type="button"  name="S182" value="S182 - Unfalluebersicht"/>
                        <input id="S184" onClick="location.href='../get_parameters.php?popisky=Datum Von,*DATE;Datum Bis,*DATE;PersNr von;PersNr bis&promenne=von;bis;persvon!number;persbis!number&values={$prvnidenmesice};{$dnes};0;99999&report=S184'" class='reportbutton' type="button"  name="S184" value="S184 - Abmahnung"/>
                        <input id="S185" onClick="location.href='../get_parameters.php?popisky=Monat;Jahr;Persnr von;Persnr bis&promenne=monat!number;jahr!number;persvon!number;persbis!number&values={$aktualniMesic};{$aktualniRok};1;99999&report=S185'" class='reportbutton' type="button"  name="S185" value="S185 - Anwesenheit/Essen/Transport"/>
                        <input id="S186" onClick="location.href='../get_parameters.php?popisky=Monat;Jahr;Persnr von;Persnr bis&promenne=monat!number;jahr!number;persvon!number;persbis!number&values={$aktualniMesic};{$aktualniRok};1;99999&report=S186'" class='reportbutton' type="button"  name="S186" value="S186 - Essenuebersicht"/>
                        <input id="S188" onClick="location.href='../get_parameters.php?popisky=Monat;Jahr;Persnr von;Persnr bis&promenne=monat!number;jahr!number;persvon!number;persbis!number&values={$aktualniMesic};{$aktualniRok};1;99999&report=S188'" class='reportbutton' type="button"  name="S188" value="S188 - Transportuebersicht"/>
                        <input id="S192" onClick="location.href='../get_parameters.php?popisky=Persnr;Eintritt vom,*DATE&promenne=persnr!number;eintrittvom&values=&report=S192'" class='reportbutton' type="button"  name="S192" value="S192 - Zuschlaege Einarbeitung"/>
                        <input id="S194" onClick="location.href='../get_parameters.php?popisky=Password,password;Persnr von;Persnr bis;Datum vom,*DATE;Datum bis,*DATE;Reporttyp,*RA&promenne=password;persvon!number;persbis!number;datumvon;datumbis;reporttyp&values=;0;99999;;;Summen,Detail&report=S194'" class='reportbutton' type="button"  name="S194" value="S194 - Risiko Zuschlaege"/>
                        <input id="D105" onClick="location.href='../get_parameters.php?popisky=Datum von,*DATE;Datum bis,*DATE;Pers von;Pers bis;OE;Reporttyp,*RA&promenne=datum;datumbis;persvon!number;persbis!number;oe;reporttyp&values={$nowpondeli};{$nowsobota};0;99999;*;plan,stamm OE&report=D105'" class='reportbutton' type="button"  name="D105" value="D105 - Erfassungsformular Anwesenheit"/>
		    </div>

		    <div id='rs_S190' style="display:{$display_sec.rs_S190}">
			<input id="S190" onClick="location.href='../get_parameters.php?popisky=Persnr von;Persnr bis;Datum von,*DATE;Datum bis,*DATE;AmNr;OE;Benutzer;Reporttyp,*RA&promenne=persnrvon!number;persnrbis!number;datumvon;datumbis;amnr;oe;benutzer;reporttyp&values=1;99999;;;*;*;*;summe,detail,sort lt.og-oe&report=S190'" class='reportbutton' type="button"  name="S190" value="S190 - Arbeitsmittelausgabe"/>	
		    </div>
		    
		    <div id='rs_S2XX' style="display:{$display_sec.rs_S2XX}">
			<input id="S210" onClick="location.href='../get_parameters.php?popisky=AuftragsNr&promenne=auftragsnr!number&values=&report=S210'" class='reportbutton' type="button"  name="S210" value="S210 - Leistung Auftrag - Teil"/>
			<input id="S210noex" onClick="location.href='../get_parameters.php?popisky=AuftragsNr von;AuftragsNr bis;Teil&promenne=auftragsnr_von!number;auftragsnr_bis!number;teil&values=;;*&report=S210noex'"class='reportbutton' type="button"  name="S210noex" value="S210 - Leistung Auftrag - Teil ohne Export"/>
			<input id="S211" onClick="location.href='../get_parameters.php?popisky=ExportNr;TaetNr von;TeatNr bis&promenne=export!number;tatvon!number;tatbis!number&values=;0;9999&report=S211'" class='reportbutton' type="button"  name="D350_etc" value="S211 - Leistung Auftrag - Teil nach Export"/>
                        <input id="S212" onClick="location.href='../get_parameters.php?popisky=Export (geplant);Reporttyp,*RA&promenne=termin;reporttyp&values=;Kunde,Expediteur&report=S212'" class='reportbutton' type="button"  name="S212" value="S212 - Export Tablo"/>
			<input id="S214" onClick="location.href='../get_parameters.php?popisky=geplant mit von;geplant mit bis;abgnr von;abgnr bis;Teil;StatNr,*CB;Reporttyp,*RA&promenne=gepl_von!number;gepl_bis!number;abgnrvon!number;abgnrbis!number;teil;statnr;reporttyp&values=;;0;9999;*;{$statpolozky};mit VzKd,VzAby&report=S214'" class='reportbutton' type="button"  name="S214" value="S214 - Leistung Auftrag - geplant"/>
			<input id="S215" onClick="location.href='../get_parameters.php?popisky=geplant mit von;geplant mit bis;abgnr von;abgnr bis;Teil;StatNr,*CB;Reporttyp,*RA&promenne=gepl_von!number;gepl_bis!number;abgnrvon!number;abgnrbis!number;teil;statnr;reporttyp&values=;;0;9999;*;{$statpolozky};mit VzKd,VzAby&report=S215'" class='reportbutton' type="button"  name="S215" value="S215 - Leistung Teil - Auftrag - geplant"/>
			<input id="S216" onClick="location.href='../get_parameters.php?popisky=Kunde von;Kunde bis&promenne=kundevon!number;kundebis!number&values=0;999&report=S216'" class='reportbutton' type="button"  name="S216" value="S216 - VzKd stand - Dispo"/>
			<input id="S218" onClick="location.href='../get_parameters.php?popisky=Datum von,*DATE;Datum bis,*DATE;RM bis (Zeit);Kunde von;Kunde bis&promenne=von;bis;rm_bis;kundevon!number;kundebis!number&values={$now};;{$nowZeit};0;999&report=S218'" class='reportbutton' type="button"  name="S218" value="S218 - Dispo Plan"/>
			<input id="S220noex" onClick="location.href='../get_parameters.php?popisky=AuftragsNr von;AuftragsNr bis;Teil&promenne=auftragsnr_von!number;auftragsnr_bis!number;teil&values=;;*&report=S220noex'"class='reportbutton' type="button"  name="S220noex" value="S220 - Leistung Auftrag - Teil/Pal ohne Export"/>
			<input id="S221" onClick="location.href='../get_parameters.php?popisky=ExportNr&promenne=export!number&values=&report=S221'"class='reportbutton' type="button"  name="S221" value="S221 - Leistung Auftrag - Palette Export"/>
			<input id="S240" onClick="location.href='../get_parameters.php?popisky=Datum von,*DATE;Datum bis,*DATE&promenne=datum_von;datum_bis&values={$predchozi_den};{$predchozi_den}&report=S240'" class='reportbutton' type="button"  name="S240" value="S240 - Leistung Tat.gruppe - Kunde - Auftrag"/>
			<input id="S250" onClick="location.href='../get_parameters.php?popisky=Datum von,*DATE;Datum bis,*DATE;Schicht von;Schicht bis&promenne=datum_von;datum_bis;schicht_von!number;schicht_bis!number&values={$predchozi_den};{$predchozi_den};1;99999&report=S250'" class='reportbutton' type="button"  name="S250" value="S250 - Leistung Schicht - Auftrag"/>
		    </div>
		    
		    <div id='rs_S280' style="display:{$display_sec.rs_S280}">
			<input id="S280" onClick="location.href='../get_parameters.php?popisky=Datum von,*DATE;Datum bis,*DATE;Pers von;Pers bis;OE-Rueck;OE-Stamm oder OE-Rueck;OG-Stamm oder OG-Rueck;Teil;Kunde;Tat von;Tat bis;VzKd sichtbar&promenne=datum_von;datum_bis;pers_von!number;pers_bis!number;oe;oeStamm;ogStamm;teil;kunde;tatvon!number;tatbis!number;vzkdsicht&values={$predchozi_den};{$predchozi_den};1;99999;*;*;*;*;*;0;9999;1&report=S280'" class='reportbutton' type="button"  name="S280" value="S280 - Leistung MA - Auftrag - Teil (Dukla)"/>
		    </div>	    
		    
		    <div id='rs_S31X' style="display:{$display_sec.rs_S31X}">
			<input id="S310" onClick="location.href='../get_parameters.php?popisky=Auftrag von;Auftrag bis;Teil;Taetigkeit;Personalnummer&promenne=auftragsnr_von!number;auftragsnr_bis!number;teil;tat;persnr!number&values=;;;*;*&report=S310'" class='reportbutton' type="button"  name="S310" value="S310 - Leistung Auftrag - Teil - Tat - Datum - MA"/>
			<input id="S311" onClick="location.href='../get_parameters.php?popisky=Export;Teil;Taetigkeit;Personalnummer&promenne=export!number;teil;tat;persnr!number&values=;*;*;*&report=S311'" class='reportbutton' type="button"  name="S311" value="S311 - Leistung Auftrag - Teil - Tat - Datum - MA nach Export"/>
			<input id="S313" onClick="location.href='../get_parameters.php?popisky=Auftrag von;Auftrag bis;Teil;Pal von;Pal bis;Reporttyp,*RA&promenne=auftragsnr_von!number;auftragsnr_bis!number;teil;palvon!number;palbis!number;reporttyp&values=;;*;0;9999;alles,ohne EX&report=S313'" class='reportbutton' type="button"  name="S313" value="S313 - Leistung Auftrag - Teil - Pal"/>
		    </div>
		    
		    <div id='rs_S34XX' style="display:{$display_sec.rs_S34XX}">
                        <input id="S350" onClick="location.href='../get_parameters.php?popisky=BehaelterNr von;BehaelterNr bis;Inventurdatum,*DATE&promenne=behnrvon!number;behnrbis!number;invdatum&values=;;&report=S350'" class='reportbutton' type="button"  name="S350" value="S350 - Verpackungsinventur"/>
                        <input id="S352" onClick="location.href='../get_parameters.php?popisky=BehaelterNr von;BehaelterNr bis;Inventurdatum,*DATE&promenne=behnrvon!number;behnrbis!number;invdatum&values=;;&report=S352'" class='reportbutton' type="button"  name="S352" value="S352 - Verpackungsinventur"/>
                        <input id="S355" onClick="location.href='../get_parameters.php?popisky=BehaelterNr;Bewegung von,*DATE;Bewegung bis,*DATE;Kunde&promenne=behnr!number;bewvon;bewbis;kundevon!number&values=;{$now};{$now};*&report=S355'" class='reportbutton' type="button"  name="S355" value="S355 - Behaelterbewegung - Detail"/>
                        <input id="S357" onClick="location.href='../get_parameters.php?popisky=Kunde von;Kunde bis;BehaelterNr von;BehaelterNr bis;Zeitpunkt,*DATE&promenne=kundevon!number;kundebis!number;behnrvon!number;behnrbis!number;zeitpunkt&values=111;999;0;9999999;{$now}&report=S357'" class='reportbutton' type="button"  name="S357" value="S357 - Behaelter Kdkonto Stand"/>
			<input id="S360" onClick="location.href='../get_parameters.php?popisky=Kunde von;Kunde bis;Erhalten am von,*DATE;Erhalten am bis,*DATE;ReklNr&promenne=kundevon!number;kundebis!number;erhvon;erhbis;reklnr&values=111;999;{$prvnidenroku};{$now};*&report=S360'" class='reportbutton' type="button"  name="S360" value="S360 - Übersicht Mängelrüge"/>
			<input id="S362" onClick="location.href='../get_parameters.php?popisky=ReklNr&promenne=reklnr&values=&report=S362'" class='reportbutton' type="button"  name="S362" value="S362 - Mängelrüge"/>
                        <input id="S372" onClick="location.href='../get_parameters.php?popisky=Kunde von;Kunde bis;Teil;Datum von,*DATE;Datum bis,*DATE;Reporttyp,*RA;Datum (wenn Reporttyp=IM),*RA&promenne=kundevon!number;kundebis!number;teil;date_von;date_bis;reporttyp;datumtyp&values=;;*;;;IM,EX;import,drueck&report=S372'"class='reportbutton' type="button"  name="S372" value="S372 - Teil/Auftrag Ausschussarten"/>
			<input id="S390" onClick="location.href='../get_parameters.php?popisky=Teil;Datum von (wenn keine Inventur);mit Bewegungen,*CH&promenne=teil;datumvon;bewegungen&values=;{$prvnidenrokuDB} 00:00:00;a&report=S390'" class='reportbutton' type="button"  name="S390" value="S390 - Lagerbestand - Teil"/>
                        <input id="S395" onClick="location.href='../get_parameters.php?popisky=Teil;Kunde;Datum von (wenn keine Inventur);Zeitpunkt&promenne=teil;kunde!number;datumvon;zeitpunkt&values=;111;{$prvnidenrokuDB} 00:00:00;{$nowtime}&report=S395'" class='reportbutton' type="button"  name="S395" value="S395 - Lagerbestand - Teil"/>
                        <input id="S410" onClick="location.href='../get_parameters.php?popisky=Kunde von;Kunde bis&promenne=kundevon!number;kundebis!number&values=0;999&report=S410'" class='reportbutton' type="button"  name="S410" value="S410 - ALT? Teil - Schwierigkeitsgrad"/>
                        <input id="S420" onClick="location.href='../get_parameters.php?popisky=Import&promenne=import!number&values=&report=S420'" class='reportbutton' type="button"  name="S420" value="S420 - Import - Gewicht"/>
		    </div>

		    <div id='rs_S5XX' style="display:{$display_sec.rs_S5XX}">
                        <input id="S510" onClick="location.href='../get_parameters.php?popisky=Datum von,*DATE;Datum bis,*DATE;PersNr von;PersNr bis&promenne=von;bis;persvon!number;persbis!number&values=;;0;99999&report=S510'" class='reportbutton' type="button"  name="S510" value="S510 - Reparaturen nach PersNr"/>
                        <input id="S515" onClick="location.href='../get_parameters.php?popisky=Invnr von;Invnr bis&promenne=invvon;invbis&values=;;&report=S515'" class='reportbutton' type="button"  name="S515" value="S515 - Reparaturen nach InvNr"/>
                        <input id="S520" onClick="location.href='../get_parameters.php?popisky=Password,password;Datum von,*DATE;Datum bis,*DATE;InvNr von;InvNr bis;ErsatzTeil;PersNr von;PersNr bis;Grenze unten;Grenze oben;Premie[%];Report,*RA;mit VzKd,*CH&promenne=password;von;bis;invnrvon;invnrbis;et;persvon!number;persbis!number;gu;go;p;reporttyp;mitvzkd&values=;;;0;999999;*;0;99999;0,14;0,30;10;nach PersNr,PersNr Praemien,nach Invnummer;a&report=S520'" class='reportbutton' type="button"  name="S520" value="S520 - Reparaturen nach PersNr - Detail"/>
		    </div>
		    
		    <div id='rs_S67XX' style="display:{$display_sec.rs_S67XX}">
			<input id='S610' class='reportbutton' type="button" id="S610" name="S610" onClick="location.href='../get_parameters.php?popisky=Datum von,*DATE;Datum bis,*DATE&promenne=datevon;datebis&values={$now};{$now}&report=S610'" value="S610 - VzKd pro Lieferung und Taetigkeitsgruppe"/>
			<input id='S617' class='reportbutton' type="button" id="S617" name="S617" onClick="location.href='../get_parameters.php?popisky=Datum von,*DATE;Datum bis,*DATE&promenne=datevon;datebis&values={$now};{$now}&report=S617'" value="S617 - VzKd pro Plan und Taetigkeitsgruppe"/>
			<input id="S790" onClick="location.href='../get_parameters.php?popisky=Kunde von;Kunde bis;Auslieferdatum von,*DATE;Auslieferdatum bis,*DATE&promenne=kunde_von!number;kunde_bis!number;ausliefer_von;ausliefer_bis&values=0;999;{$now};{$now}&report=S790'" class='reportbutton' type="button"  name="S790" value="S790 - Lieferungsuebersicht nach Import"/>
			<input id="S791" onClick="location.href='../get_parameters.php?popisky=Kunde von;Kunde bis;Auslieferdatum von,*DATE;Auslieferdatum bis,*DATE&promenne=kunde_von!number;kunde_bis!number;ausliefer_von;ausliefer_bis&values=0;999;{$now};{$now}&report=S791'" class='reportbutton' type="button"  name="S791" value="S791 - Lieferungsuebersicht nach Export"/>
			<input id="S795" onClick="location.href='../get_parameters.php?popisky=Kunde von;Kunde bis;Aufträge von,*DATE;Rückmeldungen von,*DATE;Rückmeldungen bis,*DATE;Zeitpunkt,*DATE&promenne=kunde_von!number;kunde_bis!number;auftr_von;rm_von;rm_bis;zeitpunkt&values=0;999;;;;&report=S795'" class='reportbutton' type="button"  name="S795" value="S795 - Abgrenzungstabelle"/>
		    </div>

		    <div id='rs_S8XX' style="display:{$display_sec.rs_S8XX}">
			<input id="S805" onClick="location.href='../get_parameters.php?popisky=Kunde von;Kunde bis;Jahr&promenne=kundevon!number;kundebis!number;jahr!number&values=0;999;{$aktualniRok}&report=S805'" class='reportbutton' type="button"  name="S805" value="S805 - Umsatz je Kunde + StatNr nach Monaten"/>
			<input id="S810" onClick="location.href='../get_parameters.php?popisky=Auftrag von;Auftrag bis;Teil&promenne=auftragsnr_von!number;auftragsnr_bis!number;teil&values=;;&report=S810'" class='reportbutton' type="button"  name="S810" value="S810 - Teil - Bearbeitungsstand"/>
			<input id="S813" disabled='disabled' class='reportbutton' type="button"  name="S610" value="S813 - Teil - Bearbeitungsstand ohne Rechnung"/>
			<input id="S816" onClick="location.href='../get_parameters.php?popisky=Auftrag von;Auftrag bis;Teil&promenne=auftragsnr_von!number;auftragsnr_bis!number;teil&values=;;*&report=S816'" class='reportbutton' type="button"  name="S816" value="S816 - T_neodeslane"/>
                        <input id="S817" onClick="location.href='../get_parameters.php?popisky=Auftrag von;Auftrag bis;Teil&promenne=auftragsnr_von!number;auftragsnr_bis!number;teil&values=;;*&report=S817'" class='reportbutton' type="button"  name="S817" value="S817 - T ohne Leistung"/>
			<input id="S818" onClick="location.href='../get_parameters.php?popisky=Auftrag von;Auftrag bis;Teil;Pal&promenne=auftragsnr_von!number;auftragsnr_bis!number;teil;pal&values=;;*;*&report=S818'" class='reportbutton' type="button"  name="S818" value="S818 - T_neodeslane Teil/Auftr"/>
			<input id="S847" onClick="location.href='../get_parameters.php?popisky=Password,password;Teil;Hitliste datum von,*DATE;Hitliste datum bis,*DATE;RM datum von,*DATE&promenne=password;teil;hitvon;hitbis;rmab&values=;;{$prvnidenroku};{$now};{$predtricetidny}&report=S847'" class='reportbutton' type="button"  name="S847" value="S847 - Teileinfo"/>
			<input id="S870" onClick="location.href='../get_parameters.php?popisky=Auslieferdatum von,*DATE;bis,*DATE;Kunde;Teil&promenne=ausliefer_von;ausliefer_bis;kunde!number;teil&values={$min_mesic_od};{$min_mesic_do};111;06017272&report=S870'" class='reportbutton' type="button"  name="S870" value="S870 - Taetigkeiten in Rechnungen"/>
                        <input id="S875" onClick="location.href='../get_parameters.php?popisky=Auslieferdatum von,*DATE;bis,*DATE;Kunde;Teil&promenne=ausliefer_von;ausliefer_bis;kunde!number;teil&values={$min_mesic_od};{$min_mesic_do};*;*&report=S875'" class='reportbutton' type="button"  name="S875" value="S875 - StatNr in Rechnungen"/>
			<input id="S890" onClick="location.href='../get_parameters.php?popisky=Kunde von;Kunde bis;Auftragsdatum von,*DATE;Auftragsdatum von,*DATE&promenne=kundevon!number;kundebis!number;aufdatvon;aufdatbis&values=0;999;;&report=S890'" class='reportbutton' type="button"  name="S890" value="S890 - Intrastat Importe"/>
			<input id="S895" onClick="location.href='../get_parameters.php?popisky=Kunde von;Kunde bis;Auslieferdatum von,*DATE;Auslieferdatum von,*DATE&promenne=kundevon!number;kundebis!number;aufdatvon;aufdatbis&values=0;999;;&report=S895'" class='reportbutton' type="button"  name="S895" value="S895 - Intrastat Exporte"/>
		    </div>
	    
{*		    <div id='miscReports'>
                        <input id="LagerVisacky" onClick="location.href='./vysacky.php'" class='reportbutton' type="button"  name="T002" value="Lagrvisacky"/>
		    </div>
*}			
		    <div id='rs_GF' style="display:{$display_sec.rs_GF}">
			<input id="S090" onClick="location.href='../get_parameters.php?popisky=Datum von,*DATE;Datum bis,*DATE;ITGStk pro Fraese UP;ITGStk pro Fraese DOWN;Belohnung Kc;Strafe Kc&promenne=datevon;datebis;up;down;kcplus;kcminus&values=;;100;80;50;50&report=S090'" class='reportbutton' type="button"  name="fraeseWettkampf" value="Fraeser Wettkampf"/>
			<input id="D793" onClick="location.href='../get_parameters.php?popisky=Kunde;AuslieferDatum von,*DATE;AuslieferDatum bis,*DATE;Reporttyp,*RA;KomplexKZ;Rechnung,*RA&promenne=kunde!number;von;bis;typ;komplex;rechnung&values=;;;sort Teil,sort Komplex,summen Teil_TatNr;*;DCZ,ABY&report=D793'" class='reportbutton' type="button"  name="D793" value="D793 - Umsatztatliste"/>
			<input id="S282" onClick="location.href='../get_parameters.php?popisky=Password,password;Datum von,*DATE;Datum bis,*DATE;Pers von;Pers bis;Schicht von;Schicht bis;Kunde&promenne=password;datum_von;datum_bis;pers_von!number;pers_bis!number;schicht_von!number;schicht_bis!number;kunde!number&values=;{$predchozi_den};{$predchozi_den};1;99999;1;9999;*&report=S282'" class='reportbutton' type="button"  name="S282" value="S282 - WerkvertraegeAbrechnung Detail"/>
			<input id="S284" onClick="location.href='../get_parameters.php?popisky=Password,password;Datum von,*DATE;Datum bis,*DATE;Pers von;Pers bis&promenne=password;datum_von;datum_bis;pers_von!number;pers_bis!number&values=;{$predchozi_den};{$predchozi_den};1;99999&report=S284'" class='reportbutton' type="button"  name="S284" value="S284 - WerkvertraegeAbrechnung"/>
			<input id="S430" onClick="location.href='../get_parameters.php?popisky=Password,password;Kunde;Teil;TaetNr;Report,*RA;J-BED,*RA;mit ALT,*RA;Preise,*RA&promenne=password;kunde!number;teil;abgnr!number;typ;jb;alt;preise&values=;;*;*;STANDARD,mit ZP,PREIS/STK;nein,ja;ja,nein;aktuell,neu&report=S430'" class='reportbutton' type="button"  name="S430" value="S430 - Kunde - Preise und Vorgabezeiten"/>
			<input id="S820" onClick="location.href='../get_parameters.php?popisky=Password,password;Datum von,*DATE;Datum bis,*DATE;Kunde von;Kunde bis;Reporttyp,*RA&promenne=password;datevon;datebis;kundevon!number;kundebis!number;reporttyp&values=;{$now};{$now};0;999;sort VerbZeit,sort Teil&report=S820'" class='reportbutton' type="button"  name="s820" value="S820 - Teilstat-Sort-VerbSumme ('Hitliste')"/>
			<input id="S821" onClick="location.href='../get_parameters.php?popisky=Password,password;Datum von,*DATE;Datum bis,*DATE;Kunde von;Kunde bis&promenne=password;datevon;datebis;kundevon!number;kundebis!number&values=;{$now};{$now};0;999&report=S821'" class='reportbutton' type="button"  name="s821" value="S821 - Teilstat-Sort-VerbSumme FarbeAnalyse "/>
			<input id="S910" onClick="location.href='../get_parameters.php?popisky=Kunde von;Kunde bis&promenne=kundevon!number;kundebis!number&values=0;9999&report=S910'" class='reportbutton' type="button"  name="S910" value="S910 - Abydos - Kunden"/>
			<input id="T003" onClick="location.href='../get_parameters.php?popisky=Kunde;Preis alt;Preis neu&promenne=kunde!number;preis_alt;preis_neu&values=;;&report=T003'" class='reportbutton' type="button"  name="T003" value="T003 - Minpreisaendern Stat"/>
			<input id="T100" onClick="location.href='../get_parameters.php?popisky=Report;Benutzer;Datum von,*DATE;Datum bis,*DATE;mit Details,*CH&promenne=reportname;benutzer;von;bis;details&values=*;*;{$now};{$now};a&report=T100'" class='reportbutton' type="button"  name="T100" value="T100 - Reportusage"/>
		    </div>
		    
		    
		</div>
	    </div>
	{/if}
    
    </body>
</html>
    
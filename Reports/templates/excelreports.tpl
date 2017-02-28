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
    </head>

    <body>
	{include file='../../templates/heading.tpl'}  
	
	<div id="formular_header">
	    PHPExcel Exporte / exporty dat do Excelu
	</div>
    
	{if $prihlasen}
	    <div id="formular_telo">
		    <div id='miscReports'>
			<input id="E010" onClick="location.href='../get_parameters.php?popisky=Ausliefdatum von,*DATE;Ausliefdatum bis,*DATE;Kunde&promenne=datevon;datebis;kunde&values=;;;&report=E010'" class='reportbutton' type="button"  name="E010" value="E010 - Exporte / StatNr"/>
		    </div>
		    <div id='S1XX'>
			<input id="E140" onClick="location.href='../get_parameters.php?popisky=Alle MA,*CH;Sort nach,*RA&promenne=alle;sort&values=a;PersNr,geboren&report=E140'" class='reportbutton' type="button"  name="E140" value="E140 - MA Liste"/>
			<input id="E142" onClick="location.href='../get_parameters.php?popisky=Password,password;Monat;Jahr;Datum von,*DATE;Datum bis,*DATE;Persnr von;Persnr bis;Reporttyp,*RA&promenne=password;monat;jahr;von;bis;persvon;persbis;reporttyp&values=;{$aktualniMesic};{$aktualniRok};{$prvnidenmesice};{$dnes};1;99999;lohn&report=E142'" class='reportbutton' type="button"  name="E142" value="E142"/>
			<input id="E145" onClick="location.href='../get_parameters.php?popisky=Datum von,*DATE;Datum bis,*DATE;Pers von;Pers bis&promenne=datevon;datebis;persvon;persbis&values=;;;;&report=E145'" class='reportbutton' type="button"  name="E145" value="E145 - Pers Anwesenheit Edata"/>
			<input id="E147" onClick="location.href='../get_parameters.php?popisky=Pers von;Pers bis;Jahr;Monat&promenne=persvon;persbis;jahr;monat&values=;;{$aktualniRok};{$aktualniMesic}&report=E147'" class='reportbutton' type="button"  name="E147" value="E147 - Pers Anwesenheit"/>
		    </div>
		    <div id='S3XX'>
			<input id="E310" onClick="location.href='../get_parameters.php?popisky=Datum von,*DATE;Datum bis,*DATE;Kunde&promenne=datevon;datebis;kunde&values=;;;&report=E310'" class='reportbutton' type="button"  name="E310" value="E310 - Teil - TatNr - Uebersicht"/>
			<input id="E320" onClick="location.href='../get_parameters.php?popisky=Datum von,*DATE;Datum bis,*DATE;Kunde von;Kunde bis&promenne=datevon;datebis;kundevon;kundebis&values=;;;;&report=E320'" class='reportbutton' type="button"  name="E320" value="E320 - Restmengenverwaltung"/>
			<input id="E395" onClick="location.href='../get_parameters.php?popisky=Teil;Kunde;Datum von (wenn keine Inventur);Zeitpunkt&promenne=teil;kunde!number;datumvon;zeitpunkt&values=;111;{$prvnidenrokuDB} 00:00:00;{$nowtime}&report=E395'" class='reportbutton' type="button"  name="E395" value="E395 - Lagerbestand - Teil"/>
			<input id="E530" onClick="location.href='../get_parameters.php?popisky=Datum von,*DATE;Datum bis,*DATE&promenne=datevon;datebis&values={$prvnidenmesice};{$dnes}&report=E530'" class='reportbutton' type="button"  name="E530" value="E530 - Reparaturen Ersatzteile"/>
		    </div>
	    </div>
    	{/if}
    
    </body>
</html>
    
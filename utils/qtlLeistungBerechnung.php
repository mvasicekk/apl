<html>
    <head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<title>
	    Personal - Lohn - QTL Leistung
	</title>
	<style>
	    body{
		font-family: roboto,sans-serif;
	    }
	    table {
		border-collapse: collapse;
		border:1px solid black;
	    }
	    td {
		border-collapse: collapse;
		border:1px solid black;
		padding: 3px;
	    }
	    th {
		border-collapse: collapse;
		border:1px solid black;
		padding: 3px;
		background-color: lightblue;
		font-size: small;
	    }
	</style>
    </head>
    <?php
    require_once '../db.php';

    $a = AplDB::getInstance();

    $persvon = $_GET['persvon'];
    $persbis = $_GET['persbis'];
    $jahr = $_GET['jahr'];
    $qtl = $_GET['qtl'];
    $mzdaPodleAdaptace = FALSE;



    $monateVonBisProQTL = array(
	1 => array('von' => 1, 'bis' => 3),
	2 => array('von' => 4, 'bis' => 6),
	3 => array('von' => 7, 'bis' => 9),
	4 => array('von' => 10, 'bis' => 12),
    );

    $von = sprintf("%04d-%02d-01", $jahr, $monateVonBisProQTL[$qtl]['von']);
    $pocetDnuVMesici = cal_days_in_month(CAL_GREGORIAN, $monateVonBisProQTL[$qtl]['bis'], $jahr);
    $bis = sprintf("%04d-%02d-%02d", $jahr, $monateVonBisProQTL[$qtl]['bis'], $pocetDnuVMesici);

    echo "persvon = $persvon, persbis=$persbis, jahr = $jahr, QTL = $qtl,von = $von, bis = $bis<br>";
//grundinfo z E143
    $sql.=" select";
    $sql.="     dpers.persnr,";
    $sql.="     dpers.`Name` as name,";
    $sql.="     dpers.`Vorname` as vorname,";
    $sql.="     CONCAT(dpers.`Name`,' ',dpers.`Vorname`) as vollname,";
    $sql.="     dpers.lohnfaktor/60 as perslohnfaktor,";
    $sql.="     dpers.leistfaktor,";
    $sql.="     dpers.premie_za_vykon,";
    $sql.="     dpers.regeloe,";
    $sql.="     dpers.alteroe,";
    $sql.="     dpers.premie_za_kvalitu,";
    $sql.="     dpers.qpremie_akkord,";
    $sql.="     dpers.qpremie_zeit,";
    $sql.="     dpers.premie_za_prasnost,";
    $sql.="     dpers.premie_za_3_mesice,";
    $sql.="     dpers.MAStunden,";
    $sql.="     dpers.dpersstatus,";
    $sql.="     if(dpersbewerber.exekution is null,0,dpersbewerber.exekution) as exekution,";
    $sql.="     DATE_FORMAT(dpers.eintritt,'%y-%m-%d') as eintritt,";
    $sql.="     DATE_FORMAT(dpers.austritt,'%y-%m-%d') as austritt,";
    $sql.="     DATE_FORMAT(dpers.geboren,'%Y-%m-%d') as geboren,";
    $sql.="     DATE_FORMAT(dpersdetail1.dobaurcita,'%y-%m-%d') as dobaurcita,";
    $sql.="     DATE_FORMAT(dpersdetail1.zkusebni_doba_dobaurcita,'%y-%m-%d') as zkusebni_doba_dobaurcita,";
//$sql.="     dzeit.Datum as datum,";
    $sql.="     sum(dzeit.`Stunden`) as sumstunden,";
    $sql.="     sum(if(dtattypen.oestatus='a',dzeit.`Stunden`,0)) as sumstundena,";
    $sql.="     sum(if(dtattypen.oestatus='a' and dtattypen.akkord<>0,dzeit.`Stunden`,0)) as sumstundena_akkord,";
    $sql.="     sum(if(dtattypen.erschwerniss<>0,dzeit.`Stunden`*6,0)) as erschwerniss,";
    $sql.="     sum(if(dzeit.tat='z',1,0)) as tage_z,";
    $sql.="     sum(if(dzeit.tat='z',dzeit.Stunden,0)) as stunden_z,";
    $sql.="     sum(if(dzeit.tat='nv',1,0)) as tage_nv,";
    $sql.="     sum(if(dzeit.tat='nv',dzeit.Stunden,0)) as stunden_nv,";
    $sql.="     sum(if(dzeit.tat='nw',1,0)) as tage_nw,";
    $sql.="     sum(if(dzeit.tat='d',1,0)) as tage_d,";
    $sql.="     sum(if(dzeit.tat='d',dzeit.Stunden,0)) as stunden_d,";
    $sql.="     sum(if(dzeit.tat='np',1,0)) as tage_np,";
    $sql.="     sum(if(dzeit.tat='n',1,0)) as tage_n,";
    $sql.="     sum(if(dzeit.tat='n',dzeit.Stunden,0)) as stunden_n,";
    $sql.="     sum(if(dzeit.tat='nu',1,0)) as tage_nu,";
    $sql.="     sum(if(dzeit.tat='p',1,0)) as tage_p,";
    $sql.="     sum(if(dzeit.tat='p',dzeit.Stunden,0)) as stunden_p,";
    $sql.="     sum(if(dzeit.tat='u',1,0)) as tage_u,";
    $sql.="     sum(if(dzeit.tat='?',1,0)) as tage_frage";
    $sql.="     ,sum(if(calendar.cislodne<>7 and calendar.svatek<>0,dzeit.Stunden/8,0)) as tage_svatek";
    $sql.="     ,sum(if(calendar.cislodne<>7 and calendar.svatek<>0,dzeit.Stunden,0)) as stunden_svatek";
    $sql.="     ,sum(if(dtattypen.fr_sp='N',dzeit.stunden,0)) as nachtstd";
    $sql.="     ,durlaub1.jahranspruch";
    $sql.="     ,durlaub1.rest";
    $sql.="     ,durlaub1.gekrzt";
    $sql.=" from dpers";
    $sql.=" join dzeit on dzeit.PersNr=dpers.PersNr";
    $sql.=" join dtattypen on dzeit.tat=dtattypen.tat";
    $sql.=" join calendar on calendar.datum=dzeit.Datum";
    $sql.=" left join dpersdetail1 on dpersdetail1.persnr=dpers.`PersNr`";
    $sql.=" left join dpersbewerber on dpersbewerber.persnr=dpers.`PersNr`";
    $sql.=" left join durlaub1 on durlaub1.`PersNr`=dpers.`PersNr`";
    $sql.=" where";
    $sql.=" (";
    $sql.="     (dpers.austritt is null or dpers.austritt>='$von' or dpers.eintritt>dpers.austritt)";
    $sql.="     and (dzeit.`Datum` between '$von' and '$bis')";
    $sql.="     and (dpers.persnr between '$persvon' and '$persbis')";
    $sql.=" )";
    $sql.=" group by ";
    $sql.="     dpers.`PersNr`";

    $rows = $a->getQueryRows($sql);
    $persAplRows = array();

    if ($rows != NULL) {
	foreach ($rows as $r) {
	    $persnr = $r['persnr'];
	    //$datum = $r['datum'];
	    $persAplRows[$persnr]['grundinfo'] = $r;
	    //$persRows[$persnr][$datum] = $r;
	}
    }

//    AplDB::varDump($persAplRows);

    $sql = "";
    $sql.=" select";
    $sql.="     dpers.PersNr as persnr,";
    $sql.="     concat(dpers.`name`,' ',dpers.`vorname`) as persname,";
    $sql.="     dpers.eintritt,";
    $sql.="     dpers.austritt,";
    $sql.="     dpers.dpersstatus,";
    $sql.="     dpers.einarb_zuschlag,";
    $sql.="     dpers.adaptace_bis,";
    $sql.="     if(dpersdetail1.zkusebni_doba_dobaurcita is not null,DATE_FORMAT(dpersdetail1.zkusebni_doba_dobaurcita,'%Y-%m-%d'),null) as zkusebni_doba_dobaurcita,";
    $sql.="     dpers.lohnfaktor/60 as perslohnfaktor,";
    $sql.="     dpers.leistfaktor,";
    $sql.="     if(dpers.austritt is not null,DATEDIFF(NOW(),dpers.austritt),0) as austritt_diff";
    $sql.=" from";
    $sql.="     dpers";
    $sql.=" join dpersdetail1 on dpersdetail1.persnr=dpers.persnr";
    $sql.=" where";
    $sql.="	    (dpers.persnr between '$persvon' and '$persbis')";
    $sql.="	    and (dpers.kor=0)";
    $sql.="     AND";
    $sql.="     (dpers.dpersstatus='MA'";
    $sql.="     or";
    $sql.="     if(dpers.austritt is not null,DATEDIFF(NOW(),dpers.austritt),10000)<60)";
    $sql.=" order by";
    $sql.="     dpers.PersNr";

    $persRows = $a->getQueryRows($sql);


    $sql = " select ";
    $sql.=" drueck.PersNr as persnr,";
    $sql.=" sum(if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-IST`,(drueck.`Stück`)*drueck.`VZ-IST`)) as vzaby,";
    $sql.=" sum(if(doe.akkord<>0,if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-IST`,(drueck.`Stück`)*drueck.`VZ-IST`),0)) as vzaby_akkord";
    $sql.=" from drueck";
    $sql.=" join dtattypen on dtattypen.tat=drueck.oe";
    $sql.=" join doe on doe.oe=dtattypen.oe";
    $sql.=" join dpers on dpers.PersNr=drueck.PersNr";
    $sql.=" where";
    $sql.=" drueck.Datum between '$von' and '$bis'";
    $sql.=" and";
    $sql.=" drueck.PersNr between '$persvon' and '$persbis'";
    $sql.=" and";
    $sql.=" dpers.kor=0";
    $sql.=" group by";
    $sql.=" drueck.PersNr";

    $rows = $a->getQueryRows($sql);
    $persLeistungRows = array();
//
    if ($rows != NULL) {
	foreach ($rows as $r) {
	    $persnr = $r['persnr'];
	    $persLeistungRows[$persnr]['vzabyAkkord'] = floatval($r['vzaby_akkord']);
	    $persLeistungRows[$persnr]['vzabyZeit'] = floatval($r['vzaby']) - floatval($r['vzaby_akkord']);
	}
    }


    echo "<table>";
    echo "<thead>";
    echo "<tr>";
    echo "<th style='text-align:right;'>";
    echo "persnr";
    echo "</th>";
    echo "<th>";
    echo "name";
    echo "</th>";
    echo "<th>";
    echo "ET";
    echo "</th>";
    echo "<th style='text-align:right;'>";
    echo "dovolena [dny]";
    echo "</th>";
    echo "<th style='text-align:right;'>";
    echo "qtlLeistungSoll [min]";
    echo "</th>";
    echo "<th style='text-align:right;'>";
    echo "qtlLeistungAkkordIst [min]";
    echo "</th>";
    echo "<th style='text-align:right;'>";
    echo "qtlLeistungZeitIst [min]";
    echo "</th>";
    echo "<th style='text-align:right;'>";
    echo "qtlLeistungFaktor";
    echo "</th>";
    echo "</tr>";
    echo "</thead>";

    if ($persRows !== NULL) {
	echo "<tbody>";
	foreach ($persRows as $pers) {
	    echo "<tr>";

	    $persnr = $pers['persnr'];
	    $persname = $pers['persname'];
	    $eintrittDate = $pers['eintritt'];
	    $perslohnfaktor = $pers['perslohnfaktor'];
	    $leistFaktor = $pers['leistfaktor'];
	    //kvartalni premie
	    $pracovnik = $persnr;
	    $leistungArray = array('leistung_min' => 0, 'leistung_kc' => 0);
	    $qtlTageSoll = $a->sollTageQTLProPersNr($jahr, $qtl, $pracovnik,FALSE);
	    $vzabyAkkordGesamt = 0;
	    $vzabyZeitGesamt = 0;
	    if (array_key_exists($pracovnik, $persLeistungRows)) {
		$vzabyAkkordGesamt = $persLeistungRows[$pracovnik]['vzabyAkkord'];
		$vzabyZeitGesamt = $persLeistungRows[$pracovnik]['vzabyZeit'];
	    }
	    //zobrazeni dnu soll
	    $qtlLeistungSoll = isset($qtlTageSoll) ? $qtlTageSoll * 480 : 0;
	    $vzabyGesamt = $vzabyAkkordGesamt + $vzabyZeitGesamt;
	    $leistFaktor = $qtlLeistungSoll != 0 ? $vzabyGesamt / $qtlLeistungSoll : 0;

	    $d = 0;
	    if (array_key_exists($pracovnik, $persAplRows)) {
		$d = $persAplRows[$pracovnik]['grundinfo']['tage_d'];
	    }


	    echo "<td style='text-align:right;white-space:nowrap;'>";
	    echo $persnr;
	    echo "</td>";
	    echo "<td style='white-space:nowrap;'>";
	    echo $persname;
	    echo "</td>";
	    echo "<td style='white-space:nowrap;'>";
	    echo $eintrittDate;
	    echo "</td>";
	    echo "<td style='text-align:right;white-space:nowrap;'>";
	    echo number_format($d, 0, ',', ' ');
	    echo "</td>";
	    echo "<td style='text-align:right;white-space:nowrap;'>";
	    echo number_format($qtlLeistungSoll, 0, ',', ' ');
	    echo "</td>";
	    echo "<td style='text-align:right;white-space:nowrap;'>";
	    echo number_format($vzabyAkkordGesamt, 0, ',', ' ');
	    echo "</td>";
	    echo "<td style='text-align:right;white-space:nowrap;'>";
	    echo number_format($vzabyZeitGesamt, 0, ',', ' ');
	    echo "</td>";
	    echo "<td style='text-align:right;white-space:nowrap;'>";
	    echo number_format($leistFaktor, 2, ',', ' ');
	    echo "</td>";
	    echo "<tr>";
	}
	echo "</tbody>";
    }

    echo "</table>";
    ?>
</html>
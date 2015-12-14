<style>
    body{
	font-size: 0.8em;
	font-family: monospace;
    }
    td{
	white-space: nowrap;
    }
td.monat{
    background-color:#ffe;
    font-weight: bold;
}
td.kunde{
    background-color:#efe;
    font-weight: bold;
}
</style>
<?php

require '../db.php';
$a = AplDB::getInstance();

$kundeVon = 1;
$kundeBis = 99999;
$datVon = '2015-01-01';
$datBis = '2015-12-31';

$kdArray = array(
    111,122,130,138,195
);

$sql.=" select";
$sql.="     dreklamation.kunde,";
$sql.="     YEAR(dreklamation.rekl_datum) as jahr,";
$sql.="     MONTH(dreklamation.rekl_datum) as monat,";
$sql.="     WEEKOFYEAR(dreklamation.rekl_datum) as kw,";
$sql.="     sum(dreklamation.anerkannt_stk_ausschuss+dreklamation.anerkannt_stk_nacharbeit) as stk_all,";
$sql.="     sum(if(dreklamation.ppm<>0,dreklamation.anerkannt_stk_ausschuss+dreklamation.anerkannt_stk_nacharbeit,0)) as stk_ppm";
$sql.=" from dreklamation";
$sql.=" where";
$sql.="     dreklamation.rekl_nr like 'E%'";
$sql.="     and dreklamation.kunde between '$kundeVon' and '$kundeBis'";
$sql.="     and dreklamation.rekl_datum between '$datVon' and '$datBis'";
$sql.=" group by";
$sql.="     dreklamation.kunde,";
$sql.="     YEAR(dreklamation.rekl_datum),";
$sql.="     MONTH(dreklamation.rekl_datum),";
$sql.="     WEEKOFYEAR(dreklamation.rekl_datum)";

$ppmRows = $a->getQueryRows($sql);

$ppmArray = array();

if($ppmRows!==NULL){
    foreach ($ppmRows as $ppm){
	$ppmArray[$ppm['kunde']]['jahre'][$ppm['jahr']]['monate'][$ppm['monat']]['kw'][$ppm['kw']]['stk_all'] = $ppm['stk_all'];
	$ppmArray[$ppm['kunde']]['jahre'][$ppm['jahr']]['monate'][$ppm['monat']]['kw'][$ppm['kw']]['stk_ppm'] = $ppm['stk_ppm'];
    }
}

$sql =" select";
$sql.="     daufkopf.kunde,";
$sql.="     YEAR(daufkopf.Aufdat) as jahr,";
$sql.="     MONTH(daufkopf.Aufdat) as monat,";
$sql.="     WEEKOFYEAR(daufkopf.Aufdat) as kw,";
$sql.="     sum(if(dauftr.KzGut='G',dauftr.`stück`,0)) as stk_import";
$sql.=" from dauftr";
$sql.=" join daufkopf on daufkopf.auftragsnr=dauftr.auftragsnr";
$sql.=" join dkopf on dkopf.Teil=dauftr.teil";
$sql.=" where";
$sql.="     daufkopf.Aufdat between '$datVon' and '$datBis'";
$sql.="     and dkopf.dummy_flag=0";
$sql.="     and dkopf.Gew<>0";
$sql.="     and dkopf.Teilbez not like '%reisla%'";
$sql.="     and daufkopf.kunde between '$kundeVon' and '$kundeBis'";
$sql.=" group by";
$sql.="     daufkopf.kunde,";
$sql.="     YEAR(daufkopf.Aufdat),";
$sql.="     MONTH(daufkopf.Aufdat),";
$sql.="     WEEKOFYEAR(daufkopf.Aufdat)";

$ppmRows = $a->getQueryRows($sql);

$importArray = array();

if($ppmRows!==NULL){
    foreach ($ppmRows as $ppm){
	$importArray[$ppm['kunde']]['jahre'][$ppm['jahr']]['monate'][$ppm['monat']]['kw'][$ppm['kw']]['stk_import'] = $ppm['stk_import'];
    }
}

$sql =" select";
$sql.="     daufkopf.kunde,";
$sql.="     YEAR(daufkopf.ausliefer_datum) as jahr,";
$sql.="     MONTH(daufkopf.ausliefer_datum) as monat,";
$sql.="     WEEKOFYEAR(daufkopf.ausliefer_datum) as kw,";
$sql.="     sum(if(dauftr.KzGut='G',dauftr.`stk-exp`,0)) as stk_ex_gut,";
$sql.="     sum(auss2_stk_exp) as stk_ex_auss2,";
$sql.="     sum(auss4_stk_exp) as stk_ex_auss4,";
$sql.="     sum(auss6_stk_exp) as stk_ex_auss6";
$sql.=" from dauftr";
$sql.=" join daufkopf on daufkopf.auftragsnr=dauftr.`auftragsnr-exp`";
$sql.=" join dkopf on dkopf.Teil=dauftr.teil";
$sql.=" where";
$sql.="     daufkopf.ausliefer_datum between '$datVon' and '$datBis'";
$sql.="     and dkopf.dummy_flag=0";
$sql.="     and dkopf.Gew<>0";
$sql.="     and dkopf.Teilbez not like '%reisla%'";
$sql.="     and daufkopf.kunde between '$kundeVon' and '$kundeBis'";
$sql.=" group by";
$sql.="     daufkopf.kunde,";
$sql.="     YEAR(daufkopf.ausliefer_datum),";
$sql.="     MONTH(daufkopf.ausliefer_datum),";
$sql.="     WEEKOFYEAR(daufkopf.ausliefer_datum)";

$ppmRows = $a->getQueryRows($sql);

$exportArray = array();

if($ppmRows!==NULL){
    foreach ($ppmRows as $ppm){
	$exportArray[$ppm['kunde']]['jahre'][$ppm['jahr']]['monate'][$ppm['monat']]['kw'][$ppm['kw']]['stk_ex_gut'] = $ppm['stk_ex_gut'];
	$exportArray[$ppm['kunde']]['jahre'][$ppm['jahr']]['monate'][$ppm['monat']]['kw'][$ppm['kw']]['stk_ex_auss2'] = $ppm['stk_ex_auss2'];
	$exportArray[$ppm['kunde']]['jahre'][$ppm['jahr']]['monate'][$ppm['monat']]['kw'][$ppm['kw']]['stk_ex_auss4'] = $ppm['stk_ex_auss4'];
	$exportArray[$ppm['kunde']]['jahre'][$ppm['jahr']]['monate'][$ppm['monat']]['kw'][$ppm['kw']]['stk_ex_auss6'] = $ppm['stk_ex_auss6'];
    }
}

$sql =" select";
$sql.="     YEAR(calendar.datum) as jahr,";
$sql.="     MONTH(calendar.datum) as monat,";
$sql.="     WEEKOFYEAR(calendar.datum) as kw,";
$sql.="     max(calendar.datum) as letzt_datum_kw";
$sql.=" from calendar";
$sql.=" where";
$sql.="     calendar.datum between '$datVon' and '$datBis'";
$sql.=" group by";
$sql.="     YEAR(calendar.datum),";
$sql.="     MONTH(calendar.datum),";
$sql.="     WEEKOFYEAR(calendar.datum)";

$ppmRows = $a->getQueryRows($sql);

$calArray = array();

if($ppmRows!==NULL){
    foreach ($ppmRows as $ppm){
	$calArray['jahre'][$ppm['jahr']]['monate'][$ppm['monat']]['kw'][$ppm['kw']]['letzt_datum_kw'] = $ppm['letzt_datum_kw'];
    }
}

$sumMonat = array(
    '2015'=>array(
	'1'=>array('stk'=>0)
    ),
);

$sumJahr = array(
    '2015'=>array('stk'=>0)
    );
//AplDB::varDump($ppmArray);
//AplDB::varDump($importArray);
//AplDB::varDump($exportArray);
//AplDB::varDump($calArray);

foreach ($kdArray as $kunde) {
    echo "<table border='1' style='border-collapse:collapse;'>";

    $kundeSection = "kunde";
// tablehead
    echo "<thead>";
    echo "<tr>";
    echo "<th style='text-align:right;'>Jahr</th>";
    echo "<th style='text-align:right;'>Monat</th>";
    echo "<th style='text-align:right;'>KW</th>";
    echo "<th>Datum bis</th>";
    echo "<th style='text-align:right;'>KD:$kunde stk_ppm</th>";
    echo "<th style='text-align:right;'>KD:$kunde stk_all</th>";
    echo "<th style='text-align:right;'>KD:$kunde stk_import</th>";
    echo "<th style='text-align:right;'>KD:$kunde stk_ex_gesamt</th>";
    echo "<th style='text-align:right;'>KD:$kunde PPM ppm / import</th>";
    echo "<th style='text-align:right;'>KD:$kunde PPM all / import</th>";
    echo "<th style='text-align:right;'>KD:$kunde PPM ppm / export</th>";
    echo "<th style='text-align:right;'>KD:$kunde PPM all / export</th>";
    echo "</tr>";
    echo "</thead>";

    

    foreach ($calArray['jahre'] as $jahr => $jahrArray) {
	$section = "jahr";
	foreach ($jahrArray['monate'] as $monat => $monatArray) {
	    $msection = "monat";
	    foreach ($monatArray['kw'] as $kw => $kwArray) {
		$section = "kw";
		echo "<tr>";
		echo "<td class='$section' style='text-align:right;'>" . $jahr . "</td>";
		echo "<td class='$section'  style='text-align:right;'>" . $monat . "</td>";
		echo "<td class='$section'  style='text-align:right;'>" . $kw . "</td>";
		echo "<td class='$section' >" . substr($kwArray['letzt_datum_kw'], 0, 10) . "</td>";
		//pojedu zakazniky
		//stk_ppm
		$stk_ppm = 0;
		if (is_array($ppmArray[$kunde])) {
		    if (is_array($ppmArray[$kunde]['jahre'][$jahr])) {
			if (is_array($ppmArray[$kunde]['jahre'][$jahr]['monate'][$monat])) {
			    if (is_array($ppmArray[$kunde]['jahre'][$jahr]['monate'][$monat]['kw'][$kw])) {
				$stk_ppm = $ppmArray[$kunde]['jahre'][$jahr]['monate'][$monat]['kw'][$kw]['stk_ppm'];
			    }
			}
		    }
		}
		$sumMonat[$kunde][$monat]['stk_ppm']+=$stk_ppm;
		$sumJahr[$kunde]['stk_ppm']+=$stk_ppm;
		echo "<td  class='$section' style='text-align:right;'>";
		echo $stk_ppm;
		echo "</td>";
		//stk_all
		$stk_all = 0;
		if (is_array($ppmArray[$kunde])) {
		    if (is_array($ppmArray[$kunde]['jahre'][$jahr])) {
			if (is_array($ppmArray[$kunde]['jahre'][$jahr]['monate'][$monat])) {
			    if (is_array($ppmArray[$kunde]['jahre'][$jahr]['monate'][$monat]['kw'][$kw])) {
				$stk_all = $ppmArray[$kunde]['jahre'][$jahr]['monate'][$monat]['kw'][$kw]['stk_all'];
			    }
			}
		    }
		}
		$sumMonat[$kunde][$monat]['stk_all']+=$stk_all;
		$sumJahr[$kunde]['stk_all']+=$stk_all;
		echo "<td  class='$section' style='text-align:right;'>";
		echo $stk_all;
		echo "</td>";
		//stk_import
		$stk_import = 0;
		if (is_array($importArray[$kunde])) {
		    if (is_array($importArray[$kunde]['jahre'][$jahr])) {
			if (is_array($importArray[$kunde]['jahre'][$jahr]['monate'][$monat])) {
			    if (is_array($importArray[$kunde]['jahre'][$jahr]['monate'][$monat]['kw'][$kw])) {
				$stk_import = $importArray[$kunde]['jahre'][$jahr]['monate'][$monat]['kw'][$kw]['stk_import'];
			    }
			}
		    }
		}
		$sumMonat[$kunde][$monat]['stk_import']+=$stk_import;
		$sumJahr[$kunde]['stk_import']+=$stk_import;
		echo "<td  class='$section' style='text-align:right;'>";
		echo $stk_import;
		echo "</td>";
//		stk_ex_gut
		$stk_ex_gut = 0;
		if (is_array($exportArray[$kunde])) {
		    if (is_array($exportArray[$kunde]['jahre'][$jahr])) {
			if (is_array($exportArray[$kunde]['jahre'][$jahr]['monate'][$monat])) {
			    if (is_array($exportArray[$kunde]['jahre'][$jahr]['monate'][$monat]['kw'][$kw])) {
				$stk_ex_gut = $exportArray[$kunde]['jahre'][$jahr]['monate'][$monat]['kw'][$kw]['stk_ex_gut'];
			    }
			}
		    }
		}
		$sumMonat[$kunde][$monat]['stk_ex_gut']+=$stk_ex_gut;
		$sumJahr[$kunde]['stk_ex_gut']+=$stk_ex_gut;
//		echo "<td style='text-align:right;'>";
//		echo $stk_ex_gut;
//		echo "</td>";
//		stk_ex_auss2
		$stk_ex_auss2 = 0;
		if (is_array($exportArray[$kunde])) {
		    if (is_array($exportArray[$kunde]['jahre'][$jahr])) {
			if (is_array($exportArray[$kunde]['jahre'][$jahr]['monate'][$monat])) {
			    if (is_array($exportArray[$kunde]['jahre'][$jahr]['monate'][$monat]['kw'][$kw])) {
				$stk_ex_auss2 = $exportArray[$kunde]['jahre'][$jahr]['monate'][$monat]['kw'][$kw]['stk_ex_auss2'];
			    }
			}
		    }
		}
		$sumMonat[$kunde][$monat]['stk_ex_auss2']+=$stk_ex_auss2;
		$sumJahr[$kunde]['stk_ex_auss2']+=$stk_ex_auss2;
//		echo "<td style='text-align:right;'>";
//		echo $stk_ex_auss2;
//		echo "</td>";
//		stk_ex_auss4
		$stk_ex_auss4 = 0;
		if (is_array($exportArray[$kunde])) {
		    if (is_array($exportArray[$kunde]['jahre'][$jahr])) {
			if (is_array($exportArray[$kunde]['jahre'][$jahr]['monate'][$monat])) {
			    if (is_array($exportArray[$kunde]['jahre'][$jahr]['monate'][$monat]['kw'][$kw])) {
				$stk_ex_auss4 = $exportArray[$kunde]['jahre'][$jahr]['monate'][$monat]['kw'][$kw]['stk_ex_auss4'];
			    }
			}
		    }
		}
		$sumMonat[$kunde][$monat]['stk_ex_auss4']+=$stk_ex_auss4;
		$sumJahr[$kunde]['stk_ex_auss4']+=$stk_ex_auss4;
//		echo "<td style='text-align:right;'>";
//		echo $stk_ex_auss4;
//		echo "</td>";
//		stk_ex_auss6
		$stk_ex_auss6 = 0;
		if (is_array($exportArray[$kunde])) {
		    if (is_array($exportArray[$kunde]['jahre'][$jahr])) {
			if (is_array($exportArray[$kunde]['jahre'][$jahr]['monate'][$monat])) {
			    if (is_array($exportArray[$kunde]['jahre'][$jahr]['monate'][$monat]['kw'][$kw])) {
				$stk_ex_auss6 = $exportArray[$kunde]['jahre'][$jahr]['monate'][$monat]['kw'][$kw]['stk_ex_auss6'];
			    }
			}
		    }
		}
		$sumMonat[$kunde][$monat]['stk_ex_auss6']+=$stk_ex_auss6;
		$sumJahr[$kunde]['stk_ex_auss6']+=$stk_ex_auss6;
//		echo "<td style='text-align:right;'>";
//		echo $stk_ex_auss6;
//		echo "</td>";
//		stk_ex_gesamt
		$stk_ex_gesamt = 0;
		if (is_array($exportArray[$kunde])) {
		    if (is_array($exportArray[$kunde]['jahre'][$jahr])) {
			if (is_array($exportArray[$kunde]['jahre'][$jahr]['monate'][$monat])) {
			    if (is_array($exportArray[$kunde]['jahre'][$jahr]['monate'][$monat]['kw'][$kw])) {
				$stk_ex_gesamt = $stk_ex_gut + $stk_ex_auss2 + $stk_ex_auss4 + $stk_ex_auss6;
			    }
			}
		    }
		}
		$sumMonat[$kunde][$monat]['stk_ex_gesamt']+=$stk_ex_gesamt;
		$sumJahr[$kunde]['stk_ex_gesamt']+=$stk_ex_gesamt;
		echo "<td  class='$section' style='text-align:right;'>";
		echo $stk_ex_gesamt;
		echo "</td>";

		//ppm vypocty
		$ppmPpmImport = $stk_import != 0 ? 1e6 / $stk_import * $stk_ppm : 0;
		$ppmAllImport = $stk_import != 0 ? 1e6 / $stk_import * $stk_all : 0;
		$ppmPpmExport = $stk_ex_gesamt != 0 ? 1e6 / $stk_ex_gesamt * $stk_ppm : 0;
		$ppmAllExport = $stk_ex_gesamt != 0 ? 1e6 / $stk_ex_gesamt * $stk_all : 0;
		echo "<td  class='$section' style='text-align:right;'>";
		echo number_format($ppmPpmImport, 0, ',', ' ');
		echo "</td>";
		echo "<td  class='$section' style='text-align:right;'>";
		echo number_format($ppmAllImport, 0, ',', ' ');
		echo "</td>";
		echo "<td  class='$section' style='text-align:right;'>";
		echo number_format($ppmPpmExport, 0, ',', ' ');
		echo "</td>";
		echo "<td  class='$section' style='text-align:right;'>";
		echo number_format($ppmAllExport, 0, ',', ' ');
		echo "</td>";
		echo "</tr>";
	    }
	    //ppm vypocty
	    $ppmPpmImport = $sumMonat[$kunde][$monat]['stk_import'] != 0 ? 1e6 / $sumMonat[$kunde][$monat]['stk_import'] * $sumMonat[$kunde][$monat]['stk_ppm'] : 0;
	    $ppmAllImport = $sumMonat[$kunde][$monat]['stk_import'] != 0 ? 1e6 / $sumMonat[$kunde][$monat]['stk_import'] * $sumMonat[$kunde][$monat]['stk_all'] : 0;
	    $ppmPpmExport = $sumMonat[$kunde][$monat]['stk_ex_gesamt'] != 0 ? 1e6 / $sumMonat[$kunde][$monat]['stk_ex_gesamt'] * $sumMonat[$kunde][$monat]['stk_ppm'] : 0;
	    $ppmAllExport = $sumMonat[$kunde][$monat]['stk_ex_gesamt'] != 0 ? 1e6 / $sumMonat[$kunde][$monat]['stk_ex_gesamt'] * $sumMonat[$kunde][$monat]['stk_all'] : 0;
	    echo "<tr  class='$msection' style='bagkground-color:lightred;'>";
	    echo "<td  class='$msection' colspan='4'>";
	    echo "Sum monat $monat";
	    echo "</td>";
	    echo "<td  class='$msection' colspan='4'>";
	    echo "";
	    echo "</td>";
	    echo "<td  class='$msection' style='text-align:right;' colspan='1'>";
	    echo number_format($ppmPpmImport, 0, ',', ' ');
	    echo "</td>";
	    echo "<td  class='$msection' style='text-align:right;' colspan='1'>";
	    echo number_format($ppmAllImport, 0, ',', ' ');
	    echo "</td>";
	    echo "<td  class='$msection' style='text-align:right;' colspan='1'>";
	    echo number_format($ppmPpmExport, 0, ',', ' ');
	    echo "</td>";
	    echo "<td  class='$msection' style='text-align:right;' colspan='1'>";
	    echo number_format($ppmAllExport, 0, ',', ' ');
	    echo "</td>";
	    echo "</tr>";
	}
	//ppm vypocty
	$ppmPpmImport = $sumJahr[$kunde]['stk_import'] != 0 ? 1e6 / $sumJahr[$kunde]['stk_import'] * $sumJahr[$kunde]['stk_ppm'] : 0;
	$ppmAllImport = $sumJahr[$kunde]['stk_import'] != 0 ? 1e6 / $sumJahr[$kunde]['stk_import'] * $sumJahr[$kunde]['stk_all'] : 0;
	$ppmPpmExport = $sumJahr[$kunde]['stk_ex_gesamt'] != 0 ? 1e6 / $sumJahr[$kunde]['stk_ex_gesamt'] * $sumJahr[$kunde]['stk_ppm'] : 0;
	$ppmAllExport = $sumJahr[$kunde]['stk_ex_gesamt'] != 0 ? 1e6 / $sumJahr[$kunde]['stk_ex_gesamt'] * $sumJahr[$kunde]['stk_all'] : 0;
	echo "<tr  class='$kundeSection' style='bagkground-color:lightred;'>";
	echo "<td  class='$kundeSection' colspan='4'>";
	echo "Sum Kunde $kunde";
	echo "</td>";
	echo "<td  class='$kundeSection' colspan='4'>";
	echo "";
	echo "</td>";
	echo "<td  class='$kundeSection' style='text-align:right;' colspan='1'>";
	echo number_format($ppmPpmImport, 0, ',', ' ');
	echo "</td>";
	echo "<td  class='$kundeSection' style='text-align:right;' colspan='1'>";
	echo number_format($ppmAllImport, 0, ',', ' ');
	echo "</td>";
	echo "<td  class='$kundeSection' style='text-align:right;' colspan='1'>";
	echo number_format($ppmPpmExport, 0, ',', ' ');
	echo "</td>";
	echo "<td  class='$kundeSection' style='text-align:right;' colspan='1'>";
	echo number_format($ppmAllExport, 0, ',', ' ');
	echo "</td>";
	echo "</tr>";
    }
    echo "</table>";
}

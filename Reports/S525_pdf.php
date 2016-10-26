<?php
require_once '../security.php';
require_once "../fns_dotazy.php";
require_once '../db.php';

$doc_title = "S525";
$doc_subject = "S525 Report";
$doc_keywords = "S525";

// necham si vygenerovat XML
$parameters=$_GET;

// vytahnu paramety z _GET ( z getparameters.php )

$von=make_DB_datum($_GET['von']);
$bis=make_DB_datum($_GET['bis']);
$persvon = $_GET['persvon'];
$persbis = $_GET['persbis'];
$grenzeup = 0.3;
$grenzedown = 0.14;
$premiePct = 10;

$a = AplDB::getInstance();

$pA = $a->getHFPremieArray($von, $bis, $persvon, $persbis, $grenzeup, $grenzedown, $premiePct);
$jahrMonatArray = $pA['jahrmonatArray'];

echo "<table border='1' style='border-collapse:collapse;'>";
echo "<thead>";
echo "<th>";
echo "persnr<br>(status)<br>eintritt<br>austritt";
echo "</th>";
foreach ($jahrMonatArray as $jm => $p) {
    echo "<th>";
    echo "$jm";
    echo "</th>";
}
echo "</thead>";
echo "<tbody>";
foreach ($pA as $persnr=>$p){
    if($p['persinfo']['jahrpremie']!=0){
	//zobrazit jen pokud ma v obdobi nejakou premii
	$persnr = $p['persinfo']['persnr'];
	$persstatus = $p['persinfo']['dpersstatus'];
	$austritt = $p['persinfo']['austritt'];
	$eintritt = $p['persinfo']['eintritt'];
	echo "<tr>";
	echo "<td>";
	echo "$persnr<br>($persstatus)";
	echo "<br>Eintr.:".substr($eintritt, 0, 10);
	if(strlen(trim($austritt))>0){
	    echo "<br>Austr.:".substr($austritt, 0, 10);
	}
	echo "</td>";
	foreach ($jahrMonatArray as $jm => $p) {
	    echo "<td  style='white-space:nowrap;'>";
	    echo "<table width='100%'>";
	    echo "<tr  class='vzaby'>";
	    echo "<td>vzaby:</td>";
	    echo "<td style='text-align:right;white-space:nowrap;'>".number_format($pA[$persnr]['monate'][$jm]['vzaby'],0,',',' ')."</td>";
	    echo "</tr>";
	    echo "<tr class='vzkd'>";
	    echo "<td>vzkd(11+51):</td>";
	    echo "<td style='text-align:right;white-space:nowrap;'>".number_format($pA[$persnr]['monate'][$jm]['vzkd'],0,',',' ')."</td>";
	    echo "</tr>";
	    echo "<tr class='repkosten'>";
	    echo "<td>repkosten:</td>";
	    echo "<td style='text-align:right;white-space:nowrap;'>".number_format($pA[$persnr]['monate'][$jm]['repkosten'],0,',',' ')."</td>";
	    echo "</tr>";
	    echo "<tr class='premie'>";
	    echo "<td>premie:</td>";
	    echo "<td style='text-align:right;white-space:nowrap;'>".number_format($pA[$persnr]['monate'][$jm]['premie'],0,',',' ')."</td>";
	    echo "</tr>";
	    echo "</table>";
	    echo "</td>";
	}
	echo "</tr>";
    }
}
echo "</tbody>";
echo "</table>";


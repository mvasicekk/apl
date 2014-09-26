<?php

session_start();
require '../db.php';

$apl = AplDB::getInstance();

$dat_auss_von = '2014-07-21';
$dat_auss_bis = '2014-07-27';
$dat_gut_von = '2014-07-14';
$dat_gut_bis = '2014-07-27';

$kd_von = 195;
$kd_bis = 195;


// vyber pozic/palet se zmetkama
$sql = "";
$sql.=" select";
$sql.="     drueck.Teil as teil,";
$sql.="     drueck.PersNr as persnr,";
$sql.="     drueck.Datum as auss_datum,";
$sql.="     drueck.AuftragsNr as auftragsnr,";
$sql.="     drueck.`pos-pal-nr` as pal,";
$sql.="     sum(drueck.`Auss-Stück`) as auss50_stk";
$sql.=" from drueck";
$sql.=" join daufkopf on daufkopf.auftragsnr=drueck.AuftragsNr";
$sql.=" join dpers on dpers.PersNr=drueck.PersNr";
$sql.=" where";
$sql.="     (drueck.Datum between '$dat_auss_von' and '$dat_auss_bis')";
$sql.="     and";
$sql.="     (daufkopf.kunde between $kd_von and $kd_bis)";
$sql.="     and";
$sql.="     (drueck.auss_typ=6)";
$sql.="     and";
$sql.="     (drueck.`Auss-Stück`<>0)";
$sql.="     and";
$sql.="     (dpers.kor=0)";
$sql.=" group by";
$sql.="     drueck.Teil,";
$sql.="     drueck.persnr,";
$sql.="     drueck.Datum,";
$sql.="     drueck.AuftragsNr,";
$sql.="     drueck.`pos-pal-nr`";


$aussRows = $apl->getQueryRows($sql);

echo "<style>td {border: 1px solid;}</style>";
echo "<table style='border-collapse:collapse;width:100%;'>";
echo "<tr>";
echo "<th>Teil</th>";
echo "<th>PersNr</th>";
echo "<th>Datum</th>";
echo "<th>Auftragsnr</th>";
echo "<th>pal</th>";
echo "<th>auss_50_stk</th>";
echo "</tr>";
foreach ($aussRows as $as) {
    
    // k radku se zmetkem spocitam pocet dobrych kusu
    $sql="";
    $sql.=" select";
    $sql.=" sum(drueck.`Stück`) as gut_stk";
    $sql.=" from drueck";
    $sql.=" where";
    $sql.=" (drueck.Datum between '$dat_gut_von' and '$dat_gut_bis')";
    $sql.=" and";
    $sql.=" (drueck.teil='".$as['teil']."')";
    $sql.=" and";
    $sql.=" (drueck.persnr='".$as['persnr']."')";
    
//    echo $sql;
    $gutRows = $apl->getQueryRows($sql);
//    var_dump($gutRows);
    if($gutRows===NULL){
	$gut = 0;
    }
    else{
	$gut = $gutRows[0]['gut_stk'];
    }

    
    echo "<tr>";
    echo "<td>";
    echo $as['teil'];
    echo "</td>";

    echo "<td>";
    echo $as['persnr'];
    echo "</td>";

    echo "<td>";
    echo $as['auss_datum'];
    echo "</td>";

    echo "<td>";
    echo $as['auftragsnr'];
    echo "</td>";

    echo "<td>";
    echo $as['pal'];
    echo "</td>";

    echo "<td>";
    echo $as['auss50_stk'];
    echo "</td>";

    echo "<td>";
    echo $gut;
    echo "</td>";

    echo "</tr>";
}
echo "</table>";

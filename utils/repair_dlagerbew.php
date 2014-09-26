<?php

session_start();
require '../fns_dotazy.php';
require '../db.php';

$apl = AplDB::getInstance();

// vyber podle insertstamp
$sql = "select ";
$sql.="     drueck.drueck_id,";
$sql.="     drueck.insert_stamp,";
$sql.="     drueck.AuftragsNr as auftrag,";
$sql.="     drueck.Teil as teil,";
$sql.="     drueck.`pos-pal-nr` as pal,";
$sql.="     drueck.`Stück` as stk,";
$sql.="     drueck.`Auss-Stück` as auss_stk,";
$sql.="     drueck.auss_typ,";
$sql.="     drueck.comp_user_accessuser as u,";
$sql.="     drueck.TaetNr as abgnr";

$sql.=" from drueck";
$sql.=" join daufkopf on daufkopf.auftragsnr=drueck.AuftragsNr";
$sql.=" where";
$sql.="     (drueck.insert_stamp between '2014-07-21 12:00:00' and '2014-07-22 12:00:00')";
//$sql.="     and (daufkopf.kunde=195)";
$sql.=" order by";
$sql.="     drueck.drueck_id";

$drueckRows = $apl->getQueryRows($sql);

echo "<style>td {border: 1px solid;}</style>";
echo "<table style='border-collapse:collapse;width:100%;'>";
echo "<tr>";
echo "<th>TABLE</th>";
echo "<th>auftrag</th>";
echo "<th>teil</th>";
echo "<th>pal</th>";
echo "<th>abgnr</th>";
echo "<th>stk</th>";
echo "<th>auss_stk</th>";
echo "<th>auss_typ</th>";
echo "<th>insert_stamp</th>";
echo "</tr>";
foreach ($drueckRows as $dr) {
    echo "<tr>";

    echo "<td>";
    echo "DRUECK";
    echo "</td>";

    echo "<td>";
    echo $dr['auftrag'];
    echo "</td>";
    echo "<td>";
    echo $dr['teil'];
    echo "</td>";
    echo "<td>";
    echo $dr['pal'];
    echo "</td>";
    echo "<td>";
    echo $dr['abgnr'];
    echo "</td>";
    echo "<td>";
    echo $dr['stk'];
    echo "</td>";
    echo "<td>";
    echo $dr['auss_stk'];
    echo "</td>";
    echo "<td>";
    echo $dr['auss_typ'];
    echo "</td>";
    echo "<td>";
    echo $dr['insert_stamp'];
    echo "</td>";

    echo "</tr>";

    // vyhledat zaznamy v dlagerbew
    $sql = " select ";
    $sql.=" dlagerbew.auftrag_import,";
    $sql.=" dlagerbew.teil,";
    $sql.=" dlagerbew.pal_import,";
    $sql.=" dlagerbew.gut_stk,";
    $sql.=" dlagerbew.auss_stk,";
    $sql.=" dlagerbew.lager_von,";
    $sql.=" dlagerbew.lager_nach,";
    $sql.=" dlagerbew.date_stamp,";
    $sql.=" dlagerbew.abgnr";
    $sql.=" from dlagerbew";
    $sql.=" where";
    $sql.=" (dlagerbew.auftrag_import='" . $dr['auftrag'] . "')";
    $sql.=" and";
    $sql.=" (dlagerbew.teil='" . $dr['teil'] . "')";
    $sql.=" and";
    $sql.=" (dlagerbew.pal_import='" . $dr['pal'] . "')";
    $sql.=" and";
    $sql.=" (dlagerbew.abgnr='" . $dr['abgnr'] . "')";
    $sql.=" and";
    $sql.=" (dlagerbew.date_stamp='" . $dr['insert_stamp'] . "')";

    $lagerRows = $apl->getQueryRows($sql);
    if ($lagerRows !== NULL) {
	foreach ($lagerRows as $lr) {
	    echo "<tr>";
	    echo "<td>";
	    echo "DLAGERBEW";
	    echo "</td>";

	    echo "<td>";
	    echo $lr['auftrag_import'];
	    echo "</td>";

	    echo "<td>";
	    echo $lr['teil'];
	    echo "</td>";

	    echo "<td>";
	    echo $lr['pal_import'];
	    echo "</td>";

	    echo "<td>";
	    echo $lr['abgnr'];
	    echo "</td>";

	    echo "<td>";
	    echo $lr['gut_stk'];
	    echo "</td>";

	    echo "<td>";
	    echo $lr['auss_stk'];
	    echo "</td>";

	    echo "<td>";
	    echo $lr['lager_von'] . "/" . $lr['lager_nach'];
	    echo "</td>";

	    echo "<td>";
	    echo $lr['date_stamp'];
	    echo "</td>";
	    echo "</tr>";
	}
    } else {
	// nemam zaznam, musim vytvorit dotazy
	//	zjistim nazvy lagru
	//kolik a jake operace jsou zadany
	//1.operace, ta je tam vzdy
	$l_von = lager_von($dr['teil'], $dr['abgnr']);
	$l_nach = lager_nach($dr['teil'], $dr['abgnr']);

	$sql_lager = "insert into dlagerbew (";
	$sql_lager.="teil,";
	$sql_lager.="auftrag_import,";
	$sql_lager.="pal_import,";
	$sql_lager.="gut_stk,";
	$sql_lager.="auss_stk,";
	$sql_lager.="lager_von,";
	$sql_lager.="lager_nach,";
	$sql_lager.="comp_user_accessuser,";
	$sql_lager.="date_stamp,";
	$sql_lager.="abgnr)";
	$sql_lager.=" values(";
	$sql_lager.="'" . $dr['teil'] . "',";
	$sql_lager.="'" . $dr['auftrag'] . "',";
	$sql_lager.="'" . $dr['pal'] . "',";
	$sql_lager.="'" . $dr['stk'] . "',";
	$sql_lager.="0,";
	$sql_lager.="'" . $l_von . "',";
	$sql_lager.="'" . $l_nach . "',";
	$sql_lager.="'" . 'jr' . $dr['u'] . "',";
	$sql_lager.="'" . $dr['insert_stamp'] . "',";
	$sql_lager.="'" . $dr['abgnr'] . "')";

	echo "<tr>";
	echo "<td>";
	echo "DLAGERBEW SQL";
	echo "</td>";

	echo "<td colspan='8'>";
	echo $sql_lager;
	echo "</td>";
	// exec query
//	mysql_query($sql_lager);
	echo "</tr>";

	if ($dr['auss_stk'] != 0) {
	    $l_nach = "AX";
	    if ($dr['auss_typ'] == 2) $l_nach = "A2";
	    if ($dr['auss_typ'] == 4)	$l_nach = "A4";
	    if ($dr['auss_typ'] == 6)	$l_nach = "A6";
	    $sql_lager = "insert into dlagerbew (";
	    $sql_lager.="teil,";
	    $sql_lager.="auftrag_import,";
	    $sql_lager.="pal_import,";
	    $sql_lager.="gut_stk,";
	    $sql_lager.="auss_stk,";
	    $sql_lager.="lager_von,";
	    $sql_lager.="lager_nach,";
	    $sql_lager.="comp_user_accessuser,";
	    $sql_lager.="date_stamp,";
	    $sql_lager.="abgnr)";
	    $sql_lager.=" values(";
	    $sql_lager.="'" . $dr['teil'] . "',";
	    $sql_lager.="'" . $dr['auftrag'] . "',";
	    $sql_lager.="'" . $dr['pal'] . "',";
	    $sql_lager.="0,";
	    $sql_lager.="'" . $dr['auss_stk'] . "',";
	    $sql_lager.="'" . $l_von . "',";
	    $sql_lager.="'" . $l_nach . "',";
	    $sql_lager.="'" . 'jr' . $dr['u'] . "',";
	    $sql_lager.="'" . $dr['insert_stamp'] . "',";
	    $sql_lager.="'" . $dr['abgnr'] . "')";

	    echo "<tr>";
	    echo "<td>";
	    echo "DLAGERBEW AUSS-SQL";
	    echo "</td>";

	    echo "<td colspan='8'>";
	    echo $sql_lager;
	    echo "</td>";
	    // exec query
//	    mysql_query($sql_lager);
	    echo "</tr>";
	}
    }
}
echo "</table>";

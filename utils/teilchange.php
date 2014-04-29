<?php
session_start();
//require './fns_dotazy.php';
require '../db.php';

$apl = AplDB::getInstance();

echo "<link rel='stylesheet' href='./styl.css' type='text/css'>";


$sqlArray = array(
    "select count(dauftr.teil) from dauftr where teil",
    "select count(dkopf.teil) from dkopf where teil",
    "select count(dkopf_attachment.teil) from dkopf_attachment where teil",
    "select count(dlagerbew.teil) from dlagerbew where teil",
    "select count(dlagerstk.teil) from dlagerstk where teil",
    "select count(dpos.teil) from dpos where teil",
    "select count(dposbedarflager.teil) from dposbedarflager where teil",
    "select count(drech.teil) from drech where teil",
    "select count(drechbew.teil) from drechbew where teil",
    "select count(drechdeleted.teil) from drechdeleted where teil",
    "select count(drechneu.teil) from drechneu where teil",
    "select count(dreklamation.teil) from dreklamation where teil",
    "select count(drueck.teil) from drueck where teil",
    "select count(dteildokument.teil) from dteildokument where teil",
    "select count(dverp.teil_id) from dverp where teil_id",
);

$sqlArray = array(
    "select count(dauftr.teil) from dauftr where teil",
    "select count(dkopf.teil) from dkopf where teil",
    "select count(dkopf_attachment.teil) from dkopf_attachment where teil",
    "select count(dlagerbew.teil) from dlagerbew where teil",
    "select count(dlagerstk.teil) from dlagerstk where teil",
    "select count(dpos.teil) from dpos where teil",
    "select count(dposbedarflager.teil) from dposbedarflager where teil",
    "select count(drech.teil) from drech where teil",
    "select count(drechbew.teil) from drechbew where teil",
    "select count(drechdeleted.teil) from drechdeleted where teil",
    "select count(drechneu.teil) from drechneu where teil",
    "select count(dreklamation.teil) from dreklamation where teil",
    "select count(drueck.teil) from drueck where teil",
    "select count(dteildokument.teil) from dteildokument where teil",
    "select count(dverp.teil_id) from dverp where teil_id",
);

$sqlUpdateArray = array(
    "update dauftr set teil=? where teil",
    "update dkopf set teil=? where teil",
    "update dkopf_attachment set teil=? where teil",
    "update dlagerbew set teil=? where teil",
    "update dlagerstk set teil=? where teil",
    "update dpos set teil=? where teil",
    "update dposbedarflager set teil=? where teil",
    "update drech set teil=? where teil",
    "update drechbew set teil=? where teil",
    "update drechdeleted set teil=? where teil",
    "update drechneu set teil=? where teil",
    "update dreklamation set teil=? where teil",
    "update drueck set teil=? where teil",
    "update dteildokument set teil=? where teil",
    "update dverp set teil_id=? where teil_id",
);

$teile = array(
    "05026310"	=>"4502631000",
    "050263101"	=>"4502631001",
    "05063962"	=>"0506396200",
    "05123964"	=>"0512396400",
    "05163960"	=>"4516396000",
    "05203964"	=>"4520396000",
    "05213991"	=>"4521399002",
    "05213994"	=>"4521399001",
    "06017272"	=>"4601727400",
    "06017274"	=>"4601727400",
    "06081866"	=>"0608186600",
    "06141864"	=>"4614186400",
    "06171864"	=>"0617186400",
    "06191864"	=>"0619186400",
    "06261864"	=>"0626186400",
);

echo "<table class='teilchange'>";
foreach ($teile as $alt=>$neu){
    echo "<tr style='background-color:yellow;'>";
    echo "<td>$alt</td>";
    echo "<td>$neu</td>";
    echo "</tr>";
    foreach ($sqlArray as $sql){
	echo "<tr>";
	$table = substr($sql, strpos($sql, "from")+4,  strpos($sql, 'where')-(strpos($sql, "from")+4));
	echo "<td>$table</td>";
	$where = "='$alt'";
	$sql.=$where;
	$rows = $apl->getQueryRows($sql);
	$pocet = 0;
	if($rows!==NULL){
	    foreach ($rows as $row){
		foreach ($row as $field){
		    $pocet = intval($field);
		}
	    }
	}
	echo "<td style='text-align:right;'>$pocet</td>";
	echo "</tr>";
    }
    echo "</tr>";
}
echo "</table>";

foreach ($teile as $alt=>$neu){
    foreach ($sqlUpdateArray as $sql){
	$sqlN = substr($sql, 0,  strpos($sql, "?"))."'$neu' ".  substr($sql, strpos($sql, '?')+1)."='$alt'";
	echo $sqlN."<br>";
	//$apl->query($sqlN);
    }
}
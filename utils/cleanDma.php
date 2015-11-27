<?php
require '../db.php';
$apl = AplDB::getInstance();

// vyber dma

$sql = "select * from dma where stamp between '2015-01-01' and '2015-01-31'";

$dmaRows = $apl->getQueryRows($sql);

foreach ($dmaRows as $dma){
    $imanr = $dma['imanr'];
    $typen = array("imaAntrag","emaAntrag","imaGenehmigt","emaGenehmigt");
    foreach ($typen as $typ){
	$arr = $apl->getDauftrIdArrayForIMA($imanr, $typ);
	echo "<h1>$imanr</h1>";
	echo "<h3>$typ</h3>";
	echo "<p>".join(';',$arr['did_read'])."</p>";
	echo "<hr>";
    }
}

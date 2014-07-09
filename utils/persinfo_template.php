<?php

require_once '../phpword/PHPWord.php';
require_once '../db.php';

$PHPWord = new PHPWord();
$a = AplDB::getInstance();



$persnrArra = array(104, 222, 225);
foreach ($persnrArra as $p) {
    $document = $PHPWord->loadTemplate('persinfo_template.docx');
    $pi = $a->getPersInfoArray($p);
    if ($pi !== NULL) {
	$r = $pi[0];
        echo "<pre>";
	var_dump($r);
	echo "</pre>";

	$gbdatum = $r['geboren'];
	$persnr = $r['PersNr'];
	$vollname = $r['Vorname'] . ' ' . $r['Name'];
	echo  $persnr. ' ' . $vollname."<br>";
	$document->setValue('vollname', $vollname);
	$document->setValue('datumnarozeni', $gbdatum);
	$document->save("../Reports/persinfo_$persnr.docx");
    }
}
?>
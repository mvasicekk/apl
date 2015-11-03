<?

require_once '../db.php';

$target_id = $_POST['target_id'];
$dropped_id = $_POST['dropped_id'];
$dropInfo = $_POST['dropInfo'];
$kunde = $_POST['kunde'];

$apl = AplDB::getInstance();
$a = $apl;

$kundeVon = $kunde;
$kundeBis = $kunde;
$datumVon = substr($target_id, strpos($target_id, '_') + 1, 10);
$datumBis = $datumVon;

// je to ex nebo im
$imex = substr($dropped_id, 0, strpos($dropped_id, '_'));


if ($imex == 'ex') {
    $exnr = substr($dropped_id, strpos($dropped_id, '_') + 1);
    $r = $apl->getExDatumSoll($exnr);
    $exSollAlt = $r['ex_datetime_soll'];
    $exAltHM = substr($exSollAlt, strpos($exSollAlt, ' ') + 1);
    $exSollDatumNew = substr($target_id, strpos($target_id, '_') + 1, 10);
    $exSollNew = $exSollDatumNew . ' ' . $exAltHM;
    $kunde = $apl->getKundeFromAuftransnr($exnr);
    $target_id = 'tag_' . $exSollDatumNew . "_" . $kunde;
    // prepsat ex_datum_soll
    $apl->updateDaufkopfField('ex_datum_soll', $exSollNew, $exnr);
    // vytvorit novy obsah pro target policko
    $kundeVon = $kunde;
    $kundeBis = $kunde;
    $datumVon = $exSollDatumNew;
    $datumBis = $exSollDatumNew;
}

if ($imex == 'im') {
    $imnr = substr($dropped_id, strpos($dropped_id, '_') + 1);
    $r = $apl->getImDatumSoll($imnr);
    $imSollAlt = $r['im_datetime_soll'];
    $imAltHM = substr($imSollAlt, strpos($imSollAlt, ' ') + 1);
    $imSollDatumNew = substr($target_id, strpos($target_id, '_') + 1, 10);
    $imSollNew = $imSollDatumNew . ' ' . $imAltHM;
    $kunde = $apl->getKundeFromAuftransnr($imnr);
    $target_id = 'tag_' . $imSollDatumNew . "_" . $kunde;
    // prepsat ex_datum_soll
    $apl->updateDaufkopfField('im_datum_soll', $imSollNew, $imnr);
    $apl->updateDaufkopfField('aufdat', $imSollNew, $imnr);
    // vytvorit novy obsah pro target policko
    $kundeVon = $kunde;
    $kundeBis = $kunde;
    $datumVon = $imSollDatumNew;
    $datumBis = $imSollDatumNew;
}

    $importeDatumArray = array();
    $importeDatumArrayDB = $a->getImporteDatumKunde($kundeVon, $kundeBis, $datumVon, $datumBis);
    if ($importeDatumArrayDB !== NULL) {
	foreach ($importeDatumArrayDB as $imRow) {
	    $importDatum = $imRow['import_datum'];
	    $kunde = $imRow['kunde'];
	    if (!is_array($importeDatumArray[$importDatum][$kunde])) {
		$importeDatumArray[$importDatum][$kunde] = array();
	    }
	    		$draggable=$imRow['ausliefer_datum']=='noex'&&$imRow['fertig']=='norech'?'draggable':'';
			$draggable='draggable';
		array_push($importeDatumArray[$importDatum][$kunde]
			,array(
			    'kunde'=>$imRow['kunde'],
			    'import'=>$imRow['import'],
			    'bestellnr'=>$imRow['bestellnr'],
			    'im_soll_datum'=>$imRow['im_soll_datum'],
			    'im_soll_time'=>$imRow['im_soll_time'],
			    'vzkdsoll_import'=>$a->getVzKdSollImport($imRow['import']),
			    'draggable'=>$draggable,
			    'imauto'=>$a->isAuftragImRundlauf($imRow['import'],'I')?'imauto':'',
			)
			);
	}
    }

    $exporteDatumArray = array();
    $exporteDatumArrayDB = $a->getExporteDatumKunde($kundeVon, $kundeBis, $datumVon, $datumBis);
    if ($exporteDatumArrayDB !== NULL) {
	foreach ($exporteDatumArrayDB as $exRow) {
	    $exportDatum = $exRow['export_datum'];
	    $kunde = $exRow['kunde'];
	    if (!is_array($exporteDatumArray[$exportDatum][$kunde])) {
		$exporteDatumArray[$exportDatum][$kunde] = array();
	    }
	    $vzkdRest = $a->getRestVzkdForEx($exRow['export']);
	    $draggable = ($exRow['ausliefer_datum'] == 'noex') && ($exRow['fertig'] == 'norech') ? 'draggable' : '';
	    $draggable='draggable';
	    array_push($exporteDatumArray[$exportDatum][$kunde]
		    , array(
		'kunde' => $exRow['kunde'],
		'export' => $exRow['export'],
		'vzkdrest'=>$vzkdRest,
		'auslief' => $exRow['ausliefer_datum'],
		'fertig' => $exRow['fertig'],
		'draggable' => $draggable,
		'zielort' => $exRow['zielort'],
		'exporttime' => $exRow['export_time'],
		'imauto'=>$a->isAuftragImRundlauf($exRow['export'],'E')?'imauto':'',
		    )
	    );
	}
    }

    $targetTD = "";
    if (is_array($importeDatumArray[$datumVon][$kundeVon])) {
	foreach ($importeDatumArray[$datumVon][$kundeVon] as $import) {
	    $targetTD.="<div title='BestellNr:".$import['bestellnr']."' id='im_" . $import['import'] . "' class='importnr ".$import['draggable']." ".$import['imauto']."'>Im<b>" . $import['import'] . "</b>/" . $import['im_soll_time'] . "<br>IM:vzkd&nbsp;".$import['vzkdsoll_import']."</div>";
	}
	if (count($importeDatumArray[$datumVon][$kundeVon]) > 0) {
	    $targetTD.="<br>";
	}
    }
    if (is_array($exporteDatumArray[$datumVon][$kundeVon])) {
	foreach ($exporteDatumArray[$datumVon][$kundeVon] as $export) {
	    $targetTD.="<div id='ex_" . $export['export'] . "' class='exportnr " . $export['draggable'] . " " . $export['auslief']. " " . $export['imauto'] . " " . $export['fertig'] . "'>Ex<b>" . $export['export'] . "</b>/" . $export['exporttime'] . "<br>" . $export['zielort'] . "<br>Rest:<b>".$export['vzkdrest']."</b></div>";
	}
    }
    
    
$returnArray = array(
    'target_id' => $target_id,
    'dropped_id' => $dropped_id,
    'imex' => $imex,
    'exnr' => $exnr,
    'exSollNew' => $exSollNew,
    'exSollAlt' => $exSollAlt,
    'kundeVon' => $kundeVon,
    'kundeBis' => $kundeBis,
    'datumVon' => $datumVon,
    'datumBis' => $datumBis,
    'targetTD' => $targetTD,
    'exporteDatumArray' => $exporteDatumArray,
    'countE' => count($exporteDatumArray),
    'exporteDatumArrayDB' => $exporteDatumArrayDB,
);


echo json_encode($returnArray);
?>


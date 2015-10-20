<?

require_once '../db.php';
$apl = AplDB::getInstance();
$a = $apl;

$lid = $_POST['id'];
$id = substr($lid, strpos($lid, '_') + 1);

$ar = $apl->deleteRundlauf($id);
$ar = $apl->deleteRundlaufImEx($id);
$th = $_POST['th'];

$datum = substr($th, strrpos($th, '_') + 1);

$datumsToUpdate = array();
$divsToUpdate = array();
array_push($datumsToUpdate, $datum);

foreach ($datumsToUpdate as $datum) {
    $tagDiv = "";
    $lkwDatumArray = array();
    $lkwDatumArrayDB = $a->getLkwDatumArray($datum, $datum);
    if ($lkwDatumArrayDB !== NULL) {
	foreach ($lkwDatumArrayDB as $lkwRow) {
	    //zjistit imex
	    $imexArray = $a->getRundlaufImExArray($lkwRow['id']);
	    $imexStr = "";
	    if ($imexArray !== NULL) {
		foreach ($imexArray as $imex) {
		    $auftrStr = substr($imex['auftragsnr'], 4);
		    $imexStr.= "<span style='border:1px solid black;padding:0.1em;' class='payLoad_" . $imex['imex'] . "'>" . $auftrStr . "</span>";
		}
	    }

	    $ab_aby = $lkwRow['ab_aby'];
	    $an_aby = $lkwRow['an_aby'];
	    $lkwRow['imexstr'] = $imexStr;

	    if (strlen(trim($ab_aby)) > 0) {
		if (!is_array($lkwDatumArray[$ab_aby])) {
		    $lkwDatumArray[$ab_aby] = array();
		}
		array_push($lkwDatumArray[$ab_aby], $lkwRow);
	    }
	    if (strlen(trim($an_aby)) > 0) {
		if (!is_array($lkwDatumArray[$an_aby])) {
		    $lkwDatumArray[$an_aby] = array();
		}
		if ($ab_aby != $an_aby) {
		    array_push($lkwDatumArray[$an_aby], $lkwRow);
		}
	    }
	}
    }

    $tagdatum = $datum;
    $dnyvTydnu = array('Ne', 'Po', 'Út', 'St', 'Čt', 'Pá', 'So');
    $den = $dnyvTydnu[date('w', strtotime($tagdatum))];
    $tagDiv = "$datum $den";
    if (count($lkwDatumArray) > 0) {
	foreach ($lkwDatumArray[$tagdatum] as $lkw) {
	    $tagDiv.="<div title='" . $lkw['id'] . "' id='" . "lkw_" . $lkw['id'] . "' class='" . "lkw lkwdraggable lkw_" . $lkw['id'] . "'>" . $lkw['lkw_kz'] . "/" . $lkw['imexstr'] . "</div>";
	}
    }
    $divsToUpdate[$datum] = $tagDiv;
}



$returnArray = array(
    'divsToUpdate' => $divsToUpdate,
    'datumsToUpdate' => $datumsToUpdate,
    'lid' => $lid,
    'lkwId' => $id,
    'ar' => $ar,
    'divid' => 'editlkw_' . $id,
    'lkwDatumArray' => $lkwDatumArray,
    'lkwDatumArrayDB' => $lkwDatumArrayDB,
    'datum' => $datum,
    'tagDiv' => $tagDiv,
    'th' => $th,
);


echo json_encode($returnArray);

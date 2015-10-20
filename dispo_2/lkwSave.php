<?
require_once '../db.php';
$apl = AplDB::getInstance();
$a = $apl;

    $lid = $_POST['id'];
    $id = substr($lid, strpos($lid,'_')+1);
    $params = $_POST['params'];
    $th = $_POST['th'];
    $datum = substr($th, strrpos($th, '_')+1);
    
    $datumsToUpdate = array();
    $divsToUpdate = array();
    array_push($datumsToUpdate, $datum);
    
    //$ar = $apl->deleteRundlauf($id);
    $fieldsArray = array_keys($params);
    $fa = array();
    
    
    if(is_array($fieldsArray)){
	foreach ($fieldsArray as $field1){
	    $field = substr($field1, 0,strrpos($field1, '_'));
	    $fa[$field] = $params[$field1];
	}
	
	// ab_aby_ort ----------------------------------------------------------
	$ab_aby_ort = trim($fa['ab_aby_ort']);
	$a->updateRundlaufField('ab_aby_ort',$ab_aby_ort,$id);
	
	// ab_aby_soll_datetime ------------------------------------------------
	$ab_aby_soll_date = $fa['ab_aby_soll_date'];
	$ab_aby_soll_time = $fa['ab_aby_soll_time'];
	$ab_aby_soll_datetime = $apl->datetimeOrNull($ab_aby_soll_date,$ab_aby_soll_time);
	array_push($datumsToUpdate, $apl->make_DB_datum($ab_aby_soll_date));
	if($ab_aby_soll_datetime!==NULL){
	    $a->updateRundlaufField('ab_aby_soll_datetime',$ab_aby_soll_datetime,$id);
	}
	
	// ab_aby_ist_datetime -------------------------------------------------
	$ab_aby_ist_date = $fa['ab_aby_ist_date'];
	$ab_aby_ist_time = $fa['ab_aby_ist_time'];
	$ab_aby_ist_datetime = $apl->datetimeOrNull($ab_aby_ist_date,$ab_aby_ist_time);
	
	//  ----------------------------------------------------------
	$lkw_kz = trim($fa['lkw_kz']);
	$a->updateRundlaufField('lkw_kz',$lkw_kz,$id);	
	
	//  proforma ----------------------------------------------------------
	$v = trim($fa['proforma']);
	$a->updateRundlaufField('proforma',$v,$id);	
	
	//  fahrername ----------------------------------------------------------
	$v = trim($fa['fahrername']);
	$a->updateRundlaufField('fahrername',$v,$id);	
	
	//  an_aby_nutzlast ----------------------------------------------------------
	$v = trim($fa['an_aby_nutzlast']);
	$a->updateRundlaufField('an_aby_nutzlast',$v,$id);	
	
	// bemerkung ----------------------------------------------------------
	$v = trim($fa['bemerkung']);
	$a->updateRundlaufField('bemerkung',$v,$id);	
	
	// preis ----------------------------------------------------------
	$v = intval($fa['preis']);
	$a->updateRundlaufField('preis',$v,$id);	
	
	// rabatt ----------------------------------------------------------
	$v = floatval(strtr($fa['rabatt'],',','.'));
	$a->updateRundlaufField('rabatt',$v,$id);	
	
	// betrag ----------------------------------------------------------
	$v = floatval(strtr($fa['betrag'],',','.'));
	$a->updateRundlaufField('betrag',$v,$id);	
	
	// nasleduji casy a datumy ---------------------------------------------
	
    }
    // nactu novy seznam rundlaufu pro dany datum ------------------------------
    
    
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
	'divsToUpdate'=>$divsToUpdate,
	'datumsToUpdate'=>$datumsToUpdate,
	'ab_aby_soll_datetime'=>$ab_aby_soll_datetime,
	'ab_aby_ist_datetime'=>$ab_aby_ist_datetime,
	'actualFieldsArray'=>$fa,
	'fieldsArray'=>$fieldsArray,
	'lid'=>$lid,
	'lkwId'=>$id,
	'ar'=>$ar,
	'params'=>$params,
	'divid'=>'editlkw_'.$id,
	'lkwDatumArray'=>$lkwDatumArray,
	'lkwDatumArrayDB'=>$lkwDatumArrayDB,
	'datum'=>$datum,
	'tagDiv'=>$tagDiv,
	'th'=>$th,
    );

    
    echo json_encode($returnArray);
?>


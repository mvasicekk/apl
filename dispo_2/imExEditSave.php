<?
require_once '../db.php';

    $id = $_POST['id'];
    $kundeBoxId = $_POST['kundeBoxId'];
    $auftragsnr = $_POST['auftragsnr'];
    $bestellnr = $_POST['bestellnr'];
    $bemerkung = $_POST['bemerkung'];
    $imsolldate = $_POST['imsolldate'];
    $imsolltime = $_POST['imsolltime'];
    $exsolldate = $_POST['exsolldate'];
    $exsolltime = $_POST['exsolltime'];
    $zielort = $_POST['zielort'];
    $zielvalue = $_POST['zielvalue'];
    $aufdatdate = $_POST['aufdatdate'];
    $aufdattime = $_POST['aufdattime'];
    $ausgeliefertamdate = $_POST['ausgeliefertamdate'];
    $ausgeliefertamtime = $_POST['ausgeliefertamtime'];
    $rechnungam = $_POST['rechnungam'];

    
    $apl = AplDB::getInstance();

    $auftrag = intval($auftragsnr);
    $kunde = substr($kundeBoxId,  strrpos($kundeBoxId, '_')+1);
    
    //bestellnr
    $bestellnr_ar = $apl->updateDaufkopfField('bestellnr', trim($bestellnr), $auftrag);
    //bemerkung
    $bemerkung_ar = $apl->updateDaufkopfField('bemerkung', trim($bemerkung), $auftrag);
    
    //zielvalue
    $zielvalue_ar = $apl->updateDaufkopfField('zielort_id', $zielvalue, $auftrag);
    
    //imsoll datetime
    if(($imsolldate=$apl->validateDatum($imsolldate))!==NULL){
	$imsolltime = $apl->validateZeit($imsolltime);
	$imsoll_datetime = $apl->make_DB_datetime($imsolltime, $imsolldate);
	$imsoll_datetime_ar = $apl->updateDaufkopfField('im_datum_soll', $imsoll_datetime, $auftrag);
    }
    
    //exsoll datetime
    if (strlen(trim($exsolldate)) == 0) {
	//chci zrusit planovane exportni datum
	$exsoll_datetime_ar = $apl->updateDaufkopfField('ex_datum_soll', NULL, $auftrag);
	$sollNull=1;
    }
    else {
	if(($exsolldate=$apl->validateDatum($exsolldate))!==NULL){
	    $exsolltime = $apl->validateZeit($exsolltime);
	    $exsoll_datetime = $apl->make_DB_datetime($exsolltime, $exsolldate);
	    $exsoll_datetime_ar = $apl->updateDaufkopfField('ex_datum_soll', $exsoll_datetime, $auftrag);
	}
    }
    
    //auftragseingang
    if(($aufdatdate=$apl->validateDatum($aufdatdate))!==NULL){
	$aufdattime = $apl->validateZeit($aufdattime);
	$aufdat_datetime = $apl->make_DB_datetime($aufdattime, $aufdatdate);
	$aufdat_datetime_ar = $apl->updateDaufkopfField('aufdat', $aufdat_datetime, $auftrag);
    }
    
    //ausgeliefertam
    $sollNull=0;
    if (strlen(trim($ausgeliefertamdate)) == 0) {
	//chci zrusit exportni datum
	$ausgeliefertam_datetime_ar = $apl->updateDaufkopfField('ausliefer_datum', NULL, $auftrag);
	$sollNull=1;
    } else {
	if (($ausgeliefertamdate = $apl->validateDatum($ausgeliefertamdate)) !== NULL) {
	    $ausgeliefertamtime = $apl->validateZeit($ausgeliefertamtime);
	    $ausgeliefertam_datetime = $apl->make_DB_datetime($ausgeliefertamtime, $ausgeliefertamdate);
	    $ausgeliefertam_datetime_ar = $apl->updateDaufkopfField('ausliefer_datum', $ausgeliefertam_datetime, $auftrag);
	}
    }

//podle updatnutych polozek urcim ktere kundenboxy musim updatnout
    $updateIdArray = array();
    array_push($updateIdArray, $kundeBoxId);
    if($exsoll_datetime_ar>0){
	$kundeBoxId1="tag_".substr($exsoll_datetime,0,10)."_".$kunde;
	array_push($updateIdArray, $kundeBoxId1);
    }
	

$returnArray = array(
	'kunde'=>$kunde,
	'updateIdArray'=>$updateIdArray,
	'sollNull'=>$sollNull,
	'bestellnr_ar'=>$bestellnr_ar,
	'bemerkung_ar'=>$bemerkung_ar,
	'imsoll_datetime_ar'=>$imsoll_datetime_ar,
	'imsoll_datetime'=>$imsoll_datetime,
	'exsoll_datetime_ar'=>$exsoll_datetime_ar,
	'exsoll_datetime'=>$exsoll_datetime,
	'aufdat_datetime_ar'=>$aufdat_datetime_ar,
	'aufdat_datetime'=>$aufdat_datetime,
	'ausgeliefertam_datetime_ar'=>$ausgeliefertam_datetime_ar,
	'ausgeliefertam_datetime'=>$ausgeliefertam_datetime,
	'id'=>$id,
	'kundeBoxId'=>$kundeBoxId,
	'auftragsnr'=>$auftragsnr,
	'bestellnr'=>$bestellnr,
	'bemerkung' =>$bemerkung,
	'imsolldate' =>$imsolldate,
	'imsolltime' =>$imsolltime,
	'exsolldate' =>$exsolldate,
	'exsolltime' =>$exsolltime,
	'zielort' =>$zielort,
	'zielvalue' =>$zielvalue,
	'zielvalue_ar' =>$zielvalue_ar,
	'aufdatdate' =>$aufdatdate,
	'aufdattime' =>$aufdattime,
	'ausgeliefertamdate' =>$ausgeliefertamdate,
	'ausgeliefertamtime' =>$ausgeliefertamtime,
	'rechnungam' =>$rechnungam,
    );

    
    echo json_encode($returnArray);
?>


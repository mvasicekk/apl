<?
require_once '../db.php';

    $id = $_POST['id'];
    $val = $_POST['val'];
    $auftragsnr = $_GET['auftragsnr'];
    
    $apl = AplDB::getInstance();
    $dbDatetime = '';
    $ar = 0;
    
    if($id=="ex_datum_soll"){
	$dbDatumSoll = $apl->make_DB_datum($val);
	$rowExSoll = $apl->getExDatumSoll($auftragsnr);
	$exDateTime = $rowExSoll['ex_datetime_soll'];
	if(strlen(trim($exDateTime))>0){
	    // v db uz nejaky datum a cas mam
	    $stamp = strtotime($exDateTime);
	    $dbDatetime = $apl->make_DB_datetime(date('H:i',$stamp), date('d.m.Y',  strtotime($dbDatumSoll)));
	}
	else{
	    // jeste nebyl zadan zadny datum a cas exportu
	    $dbDatetime = $apl->make_DB_datetime("00:00", date('d.m.Y',  strtotime($dbDatumSoll)));
	}
	$ar = $apl->updateDaufkopfField("ex_datum_soll", $dbDatetime, $auftragsnr);
    }
    
    if($id=="ex_zeit_soll"){
	// validace zadaneho casu
	$zeit = $apl->validateZeit($val);
//	$zeit = "00:00";
	$rowExSoll = $apl->getExDatumSoll($auftragsnr);
	$exDateTime = $rowExSoll['ex_datetime_soll'];
	if(strlen(trim($exDateTime))>0){
	    // v db uz nejaky datum a cas mam
	    $stamp = strtotime($exDateTime);
	    $dbDatetime = $apl->make_DB_datetime($zeit, date('d.m.Y',  strtotime($exDateTime)));
	}
	else{
	    // jeste nebyl zadan zadny datum a cas exportu, datum dam dnesni
	    $dbDatetime = $apl->make_DB_datetime($zeit, date('d.m.Y'));
	}
	$ar = $apl->updateDaufkopfField("ex_datum_soll", $dbDatetime, $auftragsnr);
    }

    
    echo json_encode(array('id'=>$id,'val'=>$val,'auftragsnr'=>$auftragsnr,'dbDateTime'=>$dbDatetime,'ar'=>$ar,'zeit'=>$zeit));
?>
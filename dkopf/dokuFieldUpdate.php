<?
require_once '../db.php';

    $id=$_POST['id'];
    $val = $_POST['val'];

    // zjistim zda mam hodnotu value ulozenou v databazi artiklu

    $apl = AplDB::getInstance();
    $ar = 0;
    $dokuId = substr($id, strrpos($id, '_')+1);
    $fieldName = substr($id, 2, strrpos($id, '_')-2);
    
    $goUpdate = FALSE;
    
    if($fieldName=="einlag_datum"){
	// nesmi byt prazdne
	if(strlen($val)>0){
	    $val = $apl->make_DB_datum($val);
	    if(strlen($val)>0) $goUpdate = TRUE;
	}
    }
    
    if($fieldName=="freigabe_am"){
	// muze byt prazdne
	if(strlen($val)>0){
	    $val = $apl->make_DB_datum($val);
	    if(strlen($val)>0) $goUpdate = TRUE;
	}
	else{
	    $val = NULL;
	    $goUpdate = TRUE;
	}
    }

    if($fieldName=="musterplatz"){
	// muze byt prazdne
        $val = trim($val);
        $goUpdate = TRUE;
    }
    
    if($fieldName=="freigabe_vom"){
	// musi obsahovat polozku ze seznamu nebo musi byt prazdne
	$rows = $apl->getMusterVomRow($val);
	if($rows!==NULL){
	    // obsahuje polozku
	    $goUpdate = TRUE;
	}
	else{
	    if(strlen(trim($val))==0){
		// policko je prazdne
		$val = trim($val);
		$goUpdate = TRUE;
	    }
	}
    }

    if($goUpdate){
	// validace parametru mi prosla, provedu vlastni update v databazi
	$ar = $apl->updateTeilDokuField($dokuId,$fieldName,$val);
    }
    
    $returnArray = array(
	'ar'=>$ar,
	'id'=>$id,
	'val'=>$val,
	'dokuId'=>$dokuId,
	'fieldName'=>$fieldName,
	'goUpdate'=>$goUpdate,
    );
    echo json_encode($returnArray);

?>


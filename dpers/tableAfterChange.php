<?
session_start();
require_once '../db.php';

$id = $_POST['id'];
$changes = $_POST['changes'];
$source = $_POST['source'];
$sourceDataRowA = $_POST['sourceDataRowA'];


$a = AplDB::getInstance();


$idArrayUpdate = array();
$testA = array();

if($source=="edit"||$source=="paste"||$source=="autofill"){
    $insertId=-1;
    $arrayIndex = 0;
    $rowOld=-1;
    foreach ($sourceDataRowA as $dr){
	$id_vorschuss = $dr['id_vorschuss'];
	array_push($testA, $dr);
	if($id_vorschuss==""){
	    // bude to novy radek, pokud mam vyplnene spravne vsechny pozadovane hodnoty
	    $persnr = intval($dr['persnr']);
	    $datum = $a->make_DB_datum($dr['datumF']);
	    $vorschuss = intval($dr['vorschuss']);
	    $rowAktual = $changes[$arrayIndex][0];
	    $bInsert = TRUE;
	    $pa = $a->getPersonalArray($persnr, TRUE);
	    if($pa===NULL) $bInsert=FALSE;

	    if((strlen($datum)>0) && ($persnr>0) && ($vorschuss<>0) && ($bInsert)){
		// muzu vlozit novy radek do DB
		if($rowAktual!=$rowOld){
		    $user=$a->get_user_pc();
		    $insertId = $a->insertVorschuss($datum,$persnr,$vorschuss,$user);
		    array_push($idArrayUpdate, array('row'=>$changes[$arrayIndex][0],'prop'=>$changes[$arrayIndex][1],'insertId'=>$insertId,'typ'=>'insert'));
		    $rowOld=$rowAktual;
		}
	    }
	}
	else{
	    // bude to update
	    $id_vorschuss = intval($id_vorschuss);
	    $fieldToUpdate = $changes[$arrayIndex][1];
	    $oldValue = $changes[$arrayIndex][2];
	    $newValue = $changes[$arrayIndex][3];
	    
	    $bInsert = TRUE;
	    //upravit datum
	    if($fieldToUpdate=="datumF"){
		$fieldToUpdate="datum";
		$newValue = $a->make_DB_datum($newValue);
		if(strlen($newValue)==0) $bInsert=FALSE;
	    }
	    if($fieldToUpdate=="persnr"){
		//test na persnr
		$pa = $a->getPersonalArray($newValue, TRUE);
		if($pa===NULL){
		    $bInsert=FALSE;
		}
	    }
	    $ar=0;
	    
	    if($bInsert){
		$user=$a->get_user_pc();
		$ar = $a->updateVorschuss($id_vorschuss,$fieldToUpdate,$newValue,$user);
	    }
//	    else{
//		if($fieldToUpdate=="persnr"){
//		    $pa = $a->getPersonalArray($newValue, TRUE);
//		    if($pa){
//			$name = $pa[0]['name'].' '.$pa[0]['vorname'];
//		    }
//		    else{
//			$name = "persnr ERROR";
//		    }
//		    array_push($idArrayUpdate, array('row'=>$changes[$arrayIndex][0],'prop'=>$changes[$arrayIndex][1],'insertId'=>$insertId,'ar'=>$ar,'typ'=>'persnr_update','oldValue'=>$oldValue,'name'=>$name));
//		}
//	    }
		
	    array_push($idArrayUpdate, array('row'=>$changes[$arrayIndex][0],'prop'=>$changes[$arrayIndex][1],'insertId'=>$insertId,'ar'=>$ar,'typ'=>'update','oldValue'=>$oldValue));
	}
	$arrayIndex++;
    }
}

$retArray = array(
    'id'=>$id,
    'changes'=>$changes,
    'source'=>$source,
    'sourceDataRowA'=>$sourceDataRowA,
    'idArrayUpdate'=>$idArrayUpdate,
    'testA'=>$testA,
);


echo json_encode($retArray);
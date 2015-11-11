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
	$id_rundlauf = $dr[0];
	array_push($testA, $dr);
	// bude to update
	$id_rundlauf = intval($id_rundlauf);
	$fieldToUpdate = $changes[$arrayIndex][1];
	$oldValue = $changes[$arrayIndex][2];
	$newValue = $changes[$arrayIndex][3];
	$ar=0;
	$user=$a->get_user_pc();
	$a->updateRundlaufField($fieldToUpdate, $newValue, $id_rundlauf);
	//$ar = $a->updateVorschuss($id_vorschuss,$fieldToUpdate,$newValue,$user);
	array_push($idArrayUpdate, array('row'=>$changes[$arrayIndex][0],'prop'=>$changes[$arrayIndex][1],'insertId'=>$insertId,'ar'=>$ar,'typ'=>'update','oldValue'=>$oldValue));
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
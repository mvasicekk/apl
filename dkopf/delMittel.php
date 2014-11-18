<?
require_once '../db.php';

    $id=$_POST['id'];

    // zjistim zda mam hodnotu value ulozenou v databazi artiklu

    $apl = AplDB::getInstance();
    $ar = 0;
    $dokuId = substr($id, strrpos($id, '_')+1);
    $ar = $apl->delTeilMittel($dokuId);
    
    $returnArray = array(
	'ar'=>$ar,
	'id'=>$id,
	'dokuId'=>$dokuId,
    );
    echo json_encode($returnArray);

?>


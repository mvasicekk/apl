<?
require_once '../db.php';

    $id = $_POST['id'];
    $val = $_POST['val'];
    $auftragsnr = $_GET['auftragsnr'];
    
    $apl = AplDB::getInstance();
    $ar = 0;
    
    if(strlen(trim($val))==0) $val = 0;
    $ar = $apl->updateDaufkopfField('zielort_id', $val, $auftragsnr);
    
    echo json_encode(array('id'=>$id,'val'=>$val,'auftragsnr'=>$auftragsnr,'ar'=>$ar));
?>
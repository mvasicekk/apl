<?
require_once '../../db.php';

    $id = $_POST['id'];
    $value = trim($_POST['value']);
    
    $apl = AplDB::getInstance();

    $dbField = substr($id, 0,  strpos($id, '_'));
    $drechId = substr($id, strpos($id, '_')+1);
    $ar = $apl->updateDRechField($dbField, $value, $drechId);
    
    $retArray = array(
	'id'=>$id,
	'value'=>$value,
	'ar'=>$ar,
	'dbField'=>$dbField,
	'drechId'=>$drechId,
    );
    
    echo json_encode($retArray);
?>
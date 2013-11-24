<?php
require './../db.php';
//-------------------------------------------------------------------------------------------------------------------------

$id = $_POST['id'];
$value = $_POST['value'];
$transportId = intval(substr($id, strrpos($id, '_')+1));
$ar = AplDB::getInstance()->updateTransportPreis($transportId, $value);
echo json_encode(array('affectedrows'=>$ar,'transportId'=>$transportId,'value'=>$value));
?>

<?php
require './../db.php';
//-------------------------------------------------------------------------------------------------------------------------

$id = $_POST['id'];
$value = $_POST['value'];
$transportId = intval(substr($id, strrpos($id, '_')+1));
AplDB::getInstance()->deleteDzeitRow($transportId);
echo json_encode(array('dzeit_id'=>$transportId));


?>

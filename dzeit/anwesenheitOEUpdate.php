<?php
require './../db.php';
//-------------------------------------------------------------------------------------------------------------------------

$id = $_POST['id'];
$value = $_POST['value'];
$dzeitId = intval(substr($id, strrpos($id, '_')+1));
$ar = AplDB::getInstance()->updateDzeitOE($dzeitId, $value);
echo json_encode($ar);
?>

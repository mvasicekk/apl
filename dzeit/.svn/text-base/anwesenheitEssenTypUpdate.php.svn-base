<?php
require './../db.php';
//-------------------------------------------------------------------------------------------------------------------------

$id = $_POST['id'];
$essenId = $_POST['value'];
$dzeitId = intval(substr($id, strrpos($id, '_')+1));
$ar = AplDB::getInstance()->updateDzeitEssenId($dzeitId, $essenId);
echo json_encode(array('affectedrows'=>$ar,'dzeitId'=>$dzeitId,'essenid'=>$essenId));
?>

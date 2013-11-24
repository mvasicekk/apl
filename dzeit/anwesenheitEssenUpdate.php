<?php
require './../db.php';
//-------------------------------------------------------------------------------------------------------------------------

$id = $_POST['id'];
$essenId = $_POST['essenId'];
$essen = $_POST['essen'];
$dzeitId = intval(substr($id, strrpos($id, '_')+1));
$ar = AplDB::getInstance()->updateDzeitEssen($dzeitId, $essen,$essenId);
echo json_encode(array('affectedrows'=>$ar,'dzeitId'=>$dzeitId,'essenid'=>$essenId,'essen'=>$essen));
?>

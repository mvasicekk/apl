<?php
require './../db.php';
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

//-------------------------------------------------------------------------------------------------------------------------

$id = $_POST['id'];


$apl = AplDB::getInstance();

$kfzFahrtenId = intval(substr($id, strrpos($id, '_')+1));

$ar = $apl->deleteKfzFahrtenRow($kfzFahrtenId);
 $value = array('id'=>$id,'KfzFahrtenId'=>$kfzFahrtenId,'ar'=>$ar);
 echo json_encode($value);
?>

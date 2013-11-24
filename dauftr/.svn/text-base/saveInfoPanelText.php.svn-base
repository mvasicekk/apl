<?
require './../db.php';
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

//-------------------------------------------------------------------------------------------------------------------------
$ip = $_SERVER['REMOTE_ADDR'];
$dt = date('Y-m-d H:i');
$id = $_POST['id'];
$value = $_POST['value'];

$a = AplDB::getInstance();

$podtrzitko = strpos($id, '_', 0);

$fieldName = substr($id, 0, $podtrzitko);
$itid = substr($id, $podtrzitko+1);
$ar = 0;
$ar = $a->updateIntoTableText($itid,$fieldName,$value);

    
 $value = array('id'=>$id,'value'=>$value,'ar'=>$ar,'field'=>$fieldName,'itid'=>$itid);
 
 echo json_encode($value);

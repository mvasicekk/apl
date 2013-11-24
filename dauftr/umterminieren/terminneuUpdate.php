<?
require '../../db.php';
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

//-------------------------------------------------------------------------------------------------------------------------
$ip = $_SERVER['REMOTE_ADDR'];
$dt = date('Y-m-d H:i');
$export = $_POST['value'];
$kunde = $_POST['kunde'];
$id = $_POST['id'];

$a = AplDB::getInstance();

$exportInfo = $a->getAuftragInfoArray($export,$kunde);

 $value = array('id'=>$id,'ip'=>$ip,'dt'=>$dt,'export'=>$export,'kunde'=>$kunde,'exportinfo'=>$exportInfo);
 
 echo json_encode($value);

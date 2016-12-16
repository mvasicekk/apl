<?
session_start();
require_once '../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);

$a = AplDB::getInstance();

$th = $o->th;
if($o->termin!==NULL){
    $termin = date('Y-m-d',strtotime($o->termin));
}

if(is_array($th)){
    $order = 10;
    foreach ($th as $t){
	$teil = $t->teil;
	$sql = "update dauftr set f_tablo_order='$order' where teil='$teil' and f_tablo_termin='$termin'";
	$ar = $a->query($sql);
	$order += 10;
    }
}

$returnArray = array(
	'th'=>$th,
	'termin'=>$termin,
	'sql'=>$sql,
	'ar'=>$ar
    );
    
echo json_encode($returnArray);

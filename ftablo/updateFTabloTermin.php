<?
session_start();
require_once '../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);

$a = AplDB::getInstance();

$t = $o->t;
if($o->termin!==NULL){
    $termin = date('Y-m-d',strtotime($o->termin));
}
else{
    // "odterminovat"
    $termin = NULL;
}

if($termin!==NULL){
    $sql = "update dauftr set f_tablo_termin='$termin',f_tablo_order=1 where auftragsnr='".$t->auftragsnr."' and `pos-pal-nr`='".$t->pal."' and teil='".$t->teil."'";
    $ar = $a->query($sql);
}
else{
    $sql = "update dauftr set f_tablo_termin=NULL,f_tablo_order=0 where auftragsnr='".$t->auftragsnr."' and `pos-pal-nr`='".$t->pal."' and teil='".$t->teil."'";
    $ar = $a->query($sql);
}

$a = AplDB::getInstance();
		


$returnArray = array(
	't'=>$t,
	'termin'=>$termin,
	'sql'=>$sql,
	'ar'=>$ar
    );
    
echo json_encode($returnArray);

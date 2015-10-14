<?
require_once '../db.php';
$data = file_get_contents("php://input");
$o = json_decode($data);
$import = $o->r->import;
$pal = $o->r->pal;
$field = $o->field;
$bemerkung = $o->r->palInfo->bemerkung;
$bemerkung = strip_tags(trim($bemerkung));
$gt = strip_tags(trim($o->r->giesstag));

$a = AplDB::getInstance();
$ar = -1;

if($field=='bemerkung'){
    $sql = "update dauftr set bemerkung='$bemerkung' where (auftragsnr='$import') and (`pos-pal-nr`='$pal') and (kzgut='G') limit 1";
}

if($field=='gt'){
//    $sql = "update dauftr set giesstag='$gt' where (auftragsnr='$import') and (`pos-pal-nr`='$pal') and (kzgut='G') limit 1";
    // gt dat na celou paletu
    $sql = "update dauftr set giesstag='$gt' where (auftragsnr='$import') and (`pos-pal-nr`='$pal')";
}

$ar = $a->query($sql);

$returnArray = array(
	'ar'=>$ar,
	'field'=>$field,
	"import"=>$import,
	"pal"=>$pal,
	"bemerkung"=>$bemerkung,
	"sql"=>$sql,
    );
    
    echo json_encode($returnArray);

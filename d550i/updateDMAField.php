<?
require_once '../db.php';
$data = file_get_contents("php://input");
$o = json_decode($data);
$r = $o->r;
$field = $o->field;
$value = strip_tags(trim($r->dmaRow->{$field}));
$dmaid = $r->dmaRow->id;

$a = AplDB::getInstance();
$ar = -1;

$ar = $a->updateDMAField($dmaid,$field,$value);

$returnArray = array(
	'dmaid'=>$dmaid,
	'ar'=>$ar,
	'field'=>$field,
	"value"=>$value,
    );
    
    echo json_encode($returnArray);

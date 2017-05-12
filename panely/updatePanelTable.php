<?
session_start();
require_once '../db.php';
require_once '../sqldb.php';

$data = file_get_contents("php://input");
$o = json_decode($data);

$panel = $o->panel;
$t = $o->t;

$panelTableId = $panel->id;
$field = $t;
$value = $panel->{$field};

$a = AplDB::getInstance();

$u = $_SESSION['user'];

$index = substr($field, -1);


// sanace maximalni delky
$trim = array(
    "1" => 12,
    "2" => 12,
    "3" => 8,
    "4" => 12,
    "5" => 16,
);
$value = substr(trim($value),0,$trim[$index]);


$sql = "update dinfotable set `$field`='".trim($value)."' where id='".$panelTableId."'";
$ar = $a->query($sql);


$returnArray = array(
	'ar'=>$ar,
	't'=>$t,
	'panel'=>$panel,
	'panelTableId'=>$panelTableId,
	'field'=>$field,
	'value'=>$value,
	'sql'=>$sql,
    );
    
echo json_encode($returnArray);

<?
require_once '../db.php';

function toDBDate($t){
    $d = date('Y-m-d',  strtotime($t));
    if($d=="1970-01-01"){
	return NULL;
    }
    else{
	return $d;
    }
}

$data = file_get_contents("php://input");

$o = json_decode($data);
$rekl = $o->rekl;
$apl = AplDB::getInstance();
$insertId = -1;

$rekl_datum = toDBDate($o->rekl_datum);
$sql = "insert into dreklamation (rekl_datum) values('$rekl_datum')";
$insertId = $apl->insert($sql);

$returnArray = array(
	'reklid'=>$insertId,
	'ar'=>$updatedRows,
	"insertId"=>$insertId,
	"field2Value"=>$field2Value,
	"reklId"=>$reklId,
	"objdata"=>$o,
	"sql"=>$sql,
    );
    
    echo json_encode($returnArray);

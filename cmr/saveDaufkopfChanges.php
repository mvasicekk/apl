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
$propChanges = $o->propChanges;
$a = AplDB::getInstance();
$ar = 0;

//vytvorim sql
if(count($propChanges)>0){
    $sql="update daufkopf ";
    foreach ($propChanges as $ch){
	$field = $ch->prop;
	$value = $ch->newVal;
	if($field=='imsolldat1'){
	    $field="im_datum_soll";
	    $value = date('Y-m-d H:i:s',strtotime($value));
	}
	if($field=='exsolldat1'){
	    $field="ex_datum_soll";
	    $value = date('Y-m-d H:i:s',strtotime($value));
	}
	if($field=='aufdat1'){
	    $field="aufdat";
	    $value = date('Y-m-d H:i:s',strtotime($value));
	}
	if($field=='auslieferdat1'){
	    $field="ausliefer_datum";
	    $value = date('Y-m-d H:i:s',strtotime($value));
	}
	
	$sql.=" set `$field`='$value'";
    }
    $sql.= " where auftragsnr='$o->auftragsnr' limit 1";
}

//$rekl_datum = toDBDate($o->rekl_datum);
//$sql = "insert into dreklamation (rekl_datum) values('$rekl_datum')";
//$insertId = $apl->insert($sql);

$ar=$a->query($sql);

$returnArray = array(
	'ar'=>$ar,
	'propChanges'=>$propChanges,
	'sql'=>$sql
    );
    
    echo json_encode($returnArray);

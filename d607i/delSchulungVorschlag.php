<?
require_once '../db.php';

$data = file_get_contents("php://input");

$o = json_decode($data);
$apl = AplDB::getInstance();
$affectedRows = 0;
    
if($o->id>0){
    $affectedRows = $apl->delSchulung($o->id);
    if($affectedRows>0){
	$schulungen = $apl->getSchulungenForReklamation($o->rekl_id);
    }
}
    
    
    $returnArray = array(
	"affectedRows"=>$affectedRows,
	"inputData"=>$inputData,
	"objdata"=>$o,
	"schulungen"=>$schulungen,
    );
    
    echo json_encode($returnArray);

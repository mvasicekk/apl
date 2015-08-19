<?
require_once '../db.php';

$data = file_get_contents("php://input");

$o = json_decode($data);
$apl = AplDB::getInstance();
$affectedRows = 0;
    
if($o->id>0){
    $affectedRows = $apl->delAbmahnung($o->id);
    if($affectedRows>0){
	$abmahnungen = $apl->getAbmahnungenForReklamation($o->rekl_id);
    }
}
    
    
    $returnArray = array(
	"affectedRows"=>$affectedRows,
	"inputData"=>$inputData,
	"objdata"=>$o,
	"abmahnungen"=>$abmahnungen,
    );
    
    echo json_encode($returnArray);

<?
require_once '../db.php';

$data = file_get_contents("php://input");

$o = json_decode($data);
$apl = AplDB::getInstance();
$insertId = -1;
    
if($o->persnr>0){
    $persnr = $o->persnr;
    $schulung_id = 999;
    $datum = substr($o->datum, 0, 10);
    $dreklamation_id = $o->rekl_id;
    $ergebniss = "Schulungvorschlag";
    $insertId = $apl->addSchulungVorschlag($persnr,$schulung_id,$datum,$dreklamation_id,$ergebniss);
//    $insertId = $apl->addAbmahnung($persnr,$grund,$datum,$betr,$dreklamation_id,$vorschlag,$vorschlag_von,$vorschlag_betrag,$vorschlag_bemerkung);
    if($insertId>0){
	$schulungen = $apl->getSchulungenForReklamation($dreklamation_id);
    }
}
    
    
    $returnArray = array(
	"insertId"=>$insertId,
	"inputData"=>$inputData,
	"persnr"=>$o->persnr,
	"objdata"=>$o,
	"schulungen"=>$schulungen,
    );
    
    echo json_encode($returnArray);

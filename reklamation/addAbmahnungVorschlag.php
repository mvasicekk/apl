<?
require_once '../db.php';

$data = file_get_contents("php://input");

$o = json_decode($data);
$apl = AplDB::getInstance();
$insertId = -1;
    
if($o->persnr>0){
    $persnr = $o->persnr;
    $grund = "Qualität";
    $datum = substr($o->datum, 0, 10);
    $betr = 0;
    $dreklamation_id = $o->rekl_id;
    $vorschlag = 1;
    $vorschlag_von = $o->vorschlagUser;
    $vorschlag_betrag = $o->vorschlagBetrag;
    $vorschlag_bemerkung = $o->vorschlagBemerkung;
    $insertId = $apl->addAbmahnung($persnr,$grund,$datum,$betr,$dreklamation_id,$vorschlag,$vorschlag_von,$vorschlag_betrag,$vorschlag_bemerkung);
    if($insertId>0){
	$abmahnungen = $apl->getAbmahnungenForReklamation($dreklamation_id);
    }
}
    
    
    $returnArray = array(
	"insertId"=>$insertId,
	"inputData"=>$inputData,
	"persnr"=>$o->persnr,
	"objdata"=>$o,
	"abmahnungen"=>$abmahnungen,
    );
    
    echo json_encode($returnArray);

<?
session_start();
require_once '../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);

$abgnr = $o->abgnr;

$teil = $o->p->Teil;
$tatnr = $abgnr;



$a = AplDB::getInstance();
$kunde = $a->getKundeFromTeil($teil);

$sql = "select * from `dtaetkz-abg` where `abg-nr`='$abgnr'";
$abgnrInfo = $a->getQueryRows($sql);

$vzkdVorschlag=$a->getZeitVorschlag($kunde,$teil,$tatnr,'vzkd');
$vzabyVorschlag=$a->getZeitVorschlag($kunde,$teil,$tatnr,'vzaby');
		
$returnArray = array(
	'teil'=>$teil,
	'kunde'=>$kunde,
	'abgnr'=>$abgnr,
	'abgnrInfo'=>$abgnrInfo,
	'vzkd'=>$vzkdVorschlag,
	'vzaby'=>$vzabyVorschlag,
    );
    
echo json_encode($returnArray);

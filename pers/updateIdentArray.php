<?
require_once '../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);
$oe = $o->oe;
$kunde = $o->kunde;
$ctrl = $o->ctrl;

$a = AplDB::getInstance();

if($ctrl=='oe'){
    //identKundeArray
    $kundeIdentArray = $a->getKundeIdentArrayForOE($oe);
    $kundeIdentSelected = $kundeIdentArray[0]['kunde'];
    //identifikatorArray
    $identifikatorArray = $a->getIdentifikatorArrayForOEKunde($oe,$kundeIdentSelected);
    $identifikatorSelected = $identifikatorArray[0]['iident'];
}


if($ctrl=='kunde'){
    //identifikatorArray
    $identifikatorArray = $a->getIdentifikatorArrayForOEKunde($oe,$kunde);
    $identifikatorSelected = $identifikatorArray[0]['iident'];
}



$returnArray = array(
    'oe'=>$oe,
    'kunde' => $kunde,
    'ctrl'=>$ctrl,
    'sql' => $sql,
    'kundeIdentArray'=>$kundeIdentArray,
    'kundeIdentSelected'=>$kundeIdentSelected,
    'identifikatorArray'=>$identifikatorArray,
    'identifikatorSelected'=>$identifikatorSelected,
);

echo json_encode($returnArray);


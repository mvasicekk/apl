<?
require_once '../db.php';
$data = file_get_contents("php://input");
$o = json_decode($data);

$placeid = $o->placeid;

$a = AplDB::getInstance();


$panels = array();
$places = $a->getInfoPanelPlaces();

if (intval($placeid) > 0) {
    foreach ($places as $place) {
	$panels[$place['id']] = $a->getInfoPanelsForPlaceId($place['id']);
    }
}


$returnArray = array(
    'placeid' => $placeid,
    'places' => $places,
    'panels' => $panels,
);
echo json_encode($returnArray);


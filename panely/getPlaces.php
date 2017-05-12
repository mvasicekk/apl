<?

require_once '../db.php';
$data = file_get_contents("php://input");
$o = json_decode($data);

$placeid = $o->placeid;

$a = AplDB::getInstance();


$panels = array();


if (intval($placeid) > 0) {
    $panels[$placeid] = $a->getInfoPanelsForPlaceId($placeid);
} else {
    $places = $a->getInfoPanelPlaces();
}


$returnArray = array(
    'placeid' => $placeid,
    'places' => $places,
    'panels' => $panels,
);
echo json_encode($returnArray);


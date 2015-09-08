<?
require_once '../db.php';

    $a = AplDB::getInstance();

    $places = array();
    $panels = array();
    $places = $a->getInfoPanelPlaces();
    
    foreach ($places as $place){
	$panels[$place['id']] = $a->getInfoPanelsForPlaceId($place['id']);
    }
    
    $returnArray = array(
	'places'=>$places,
	'panels'=>$panels,
    );
    echo json_encode($returnArray);


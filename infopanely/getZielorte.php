<?
require_once '../db.php';

    $e = $_GET['e'];
    $kd = $_GET['k'];
    
    // zjistim zda mam hodnotu value ulozenou v databazi artiklu

    $apl = AplDB::getInstance();

    $zielortArray = NULL;
//    if(strlen($e)>=1){
	$zielortArray = $apl->getZielorteArray($kd, $e);
	//$persnrArray1 = $persnrArray['rows'];
//    }
    
    if($zielortArray!==NULL){
	foreach ($zielortArray as $i=>$row){
	    $zielortArray[$i]['formattedZielort'] = sprintf("%s",$row['zielort']);
	}
    }

    $returnArray = array(
	'e'=>$e,
	'k'=>$kd,
	'zielortArray'=>$zielortArray,
    );
    echo json_encode($returnArray);


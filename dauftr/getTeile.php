<?
require_once '../db.php';

    $t = $_GET['t'];
    $k = $_GET['kunde'];
    
    // zjistim zda mam hodnotu value ulozenou v databazi artiklu

    $apl = AplDB::getInstance();

    $teileArray = NULL;
    if(strlen($t)>=3){
	$teileArray = $apl->getTeileArrayForKundeMatch($k,$t);
    }
    
    if($teileArray!==NULL){
	foreach ($teileArray as $i=>$row){
	    $teileArray[$i]['formattedTeil'] = "".sprintf("%03d - %-10s (%s)",$row['kunde'],$row['teil'],$row['teilbez'])."";
	}
    }

    $returnArray = array(
	't'=>$t,
	'teileArray'=>$teileArray,
    );
    echo json_encode($returnArray);
    //$dokuArray = 55;
    //echo json_encode($dokuArray);


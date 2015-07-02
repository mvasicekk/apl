<?
require_once '../db.php';

    $t = $_GET['t'];
    
    
    // zjistim zda mam hodnotu value ulozenou v databazi artiklu

    $apl = AplDB::getInstance();

    $behArray = NULL;
    $behArray = $apl->getBehDataArray($t);
    
    if($behArray!==NULL){
	foreach ($behArray as $i=>$row){
	    $behArray[$i]['formattedBeh'] = sprintf("%-10d - %8d ($10d)",$row['teil'],$row['import'],$row['impal'])."";
	}
    }

    $returnArray = array(
	't'=>$t,
	'behArray'=>$behArray,
    );
    echo json_encode($returnArray);


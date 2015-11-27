<?
require_once '../db.php';

    $e = $_GET['e'];
    
    // zjistim zda mam hodnotu value ulozenou v databazi artiklu

    $apl = AplDB::getInstance();

    $kundeArray = NULL;
    if(strlen($e)>=1){
	$kundeArray = $apl->getKundeArrayMatch($e);
	//$persnrArray1 = $persnrArray['rows'];
    }
    
    if($kundeArray!==NULL){
	foreach ($kundeArray as $i=>$row){
	    $kundeArray[$i]['formattedKunde'] = sprintf("%03d - %s %s",$row['kunde'],$row['Name1'],$row['Name2']);
	}
    }

    $returnArray = array(
	'e'=>$e,
	'kundeArray'=>$kundeArray,
    );
    echo json_encode($returnArray);


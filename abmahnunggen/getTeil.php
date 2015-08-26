<?
require_once '../db.php';

    $e = $_GET['e'];
    $k = $_GET['k'];

    $apl = AplDB::getInstance();

    $teilArray = NULL;
    if(strlen($e)>=1){
	$teilArray = $apl->getTeilArrayForKundeMatch($k,$e);
	//$persnrArray1 = $persnrArray['rows'];
    }
    
    if($teilArray!==NULL){
	foreach ($teilArray as $i=>$row){
	    $teilArray[$i]['formattedTeil'] = sprintf("%s - (%s) %s",$row['teil'],$row['teillang'],$row['teilbez']);
	}
    }

    $returnArray = array(
	'e'=>$e,
	'teilArray'=>$teilArray,
    );
    echo json_encode($returnArray);


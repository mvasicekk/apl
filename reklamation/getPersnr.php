<?
require_once '../db.php';

    $e = $_GET['e'];
    
    // zjistim zda mam hodnotu value ulozenou v databazi artiklu

    $apl = AplDB::getInstance();

    $persnrArray = NULL;
    if(strlen($e)>=2){
	// povolit i lidi s vystupem
	$persnrArray = $apl->getPersonalArrayMatch($e,FALSE,1);
	$persnrArray1 = $persnrArray['rows'];
    }
    
    if($persnrArray1!==NULL){
	foreach ($persnrArray1 as $i=>$row){
	    $austritt = $row['austritt'];
	    if(strlen(trim($austritt))>0){
		$d = strtotime($austritt)?date('d.m.Y',  strtotime($austritt)):'';
		$austrittStr = " ( Austritt am : $d )";
		$austrittClass = "austritt";
	    }
	    else{
		$austrittStr = "";
		$austrittClass = "";
	    }
	    $persnrArray1[$i]['formattedPersnr'] = sprintf("<div class='$austrittClass'>%d - %s %s $austrittStr</div>",$row['persnr'],$row['name'],$row['vorname']);
	}
    }

    $returnArray = array(
	'e'=>$e,
	'persnrArray'=>$persnrArray1,
	'pA'=>$persnrArray
    );
    echo json_encode($returnArray);


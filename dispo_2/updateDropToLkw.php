<?

require_once '../db.php';

$target_id = $_POST['target_id'];
$dropped_id = $_POST['dropped_id'];


$apl = AplDB::getInstance();
$a = $apl;

// je to ex nebo im
$imex = substr($dropped_id, 0, strpos($dropped_id, '_'));
$rundlaufid = substr($target_id, strpos($target_id, '_')+1);
$ie = $imex=="im"?'I':'E';
$auftragsnr = substr($dropped_id, strpos($dropped_id, '_')+1);

$insertid = $a->addRundlaufPayload($rundlaufid,$ie,$auftragsnr);

if($insertid>0){
    $imexArray = $apl->getRundlaufImExArray($rundlaufid);
    if($imexArray!==NULL){
	foreach ($imexArray as $imex){
	    $payload = $imex['imex'].$imex['auftragsnr'];
	    $payloadId = $imex['id'];
	    $ie=$imex['imex'];
	    $payloadDiv.="<div id='payloadId_$payloadId' class='lkwPayLoad payLoad_$ie'>$payload</div>";
	}
    }

    $lkwDiv = "";
    $imexStr = "";
    if($imexArray!==NULL){
	foreach ($imexArray as $imex){
	    $auftrStr = substr($imex['auftragsnr'],4);
	    $imexStr.= "<span style='border:1px solid black;padding:0.1em;' class='payLoad_".$imex['imex']."'>".$auftrStr."</span>";
	}
	$rliA= $apl->getRundlaufInfoArray($rundlaufid);
	$rli = $rliA[0];
	$lkwDiv.=$rli['lkw_kz']."/".$imexStr;
    }    
}

$returnArray = array(
    'target_id' => $target_id,
    'dropped_id' => $dropped_id,
    'ie'=>$ie,
    'auftragsnr'=>$auftragsnr,
    'rundlaufid'=>$rundlaufid,
    'imex' => $imex,
    'insertid'=>$insertid,
    'payloadDiv'=>$payloadDiv,
    'lkwDiv'=>$lkwDiv,
    'divid'=>$target_id,
);


echo json_encode($returnArray);
?>


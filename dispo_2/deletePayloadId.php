<?
require_once '../db.php';
$apl = AplDB::getInstance();

    $lid = $_POST['payloadid'];
    $rid = $_POST['rid'];
    
    $id = substr($lid, strpos($lid,'_')+1);
    $rundlaufid = substr($rid, strpos($rid,'_')+1);
    
    $apl->deletePayloadRundlauf($id,$rundlaufid);
    
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

$returnArray = array(
	'lid'=>$lid,
	'id'=>$id,
	'rundlaufid'=>$rundlaufid,
	'payloadDiv'=>$payloadDiv,
	'lkwDiv'=>$lkwDiv,
	'divid'=>$rid
    );

    echo json_encode($returnArray);
?>


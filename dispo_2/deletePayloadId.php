<?
require_once '../db.php';
require_once './commons.php';

$apl = AplDB::getInstance();

    $lid = $_POST['payloadid'];
    $rid = $_POST['rid'];
    
    $id = substr($lid, strpos($lid,'_')+1);
    $rundlaufid = substr($rid, strpos($rid,'_')+1);
    
    $payloadInfoA = $apl->getPayloadInfo($id);
    if($payloadInfoA!==NULL){
	$payloadInfo = $payloadInfoA[0];
	$imex = $payloadInfo['imex']=='E'?'ex':'im';
	$imexDivToUpdate = $imex.'_'.$payloadInfo['auftragsnr'];
    }
    
    $apl->deletePayloadRundlauf($id,$rundlaufid);
    
    $sectionA = getLkwFormDivs($rundlaufid);
    
    $exCount = $sectionA['exCount'];
    $imCount = $sectionA['imCount'];
    $divAnKundeZielorte = $sectionA['divAnKundeZielorte'];
    $ab_aby_soll_dateVorschlag = $sectionA['ab_aby_soll_dateVorschlag'];
    $ab_aby_soll_timeVorschlag = $sectionA['ab_aby_soll_timeVorschlag'];
    $an_aby_soll_dateVorschlag = $sectionA['an_aby_soll_dateVorschlag'];
    $an_aby_soll_timeVorschlag = $sectionA['an_aby_soll_timeVorschlag'];
    $payloadDiv = $sectionA['payloadDiv'];
    $imexArrayToUpdate = $sectionA['imexArrayToUpdate'];
    $imexArray = $sectionA['imexArray'];

    
    $lkwDiv = "";
    $imexStr = "";
    if($imexArray!==NULL){
	$pocet = 0;
	foreach ($imexArray as $imex){
	    $auftrStr = substr($imex['auftragsnr'],4);
	    if($pocet>1){
		$imexStr.="<br>";
		$pocet = 0;
	    }
	    $imexStr.= "<span style='border:1px solid black;padding:0.1em;' class='payLoad_".$imex['imex']."'>".$auftrStr."</span>";
	    $pocet++;
	}
	$rliA= $apl->getRundlaufInfoArray($rundlaufid);
	$rli = $rliA[0];
	$lkwDiv.=$rli['lkw_kz']."/".$imexStr;
    }

    
$returnArray = array(
	'exCount'=>$exCount,
	'imCount'=>$imCount,
	'ab_aby_soll_date_vorschlag'=>$ab_aby_soll_dateVorschlag,
	'ab_aby_soll_time_vorschlag'=>$ab_aby_soll_timeVorschlag,
	'an_aby_soll_date_vorschlag'=>$an_aby_soll_dateVorschlag,
	'an_aby_soll_time_vorschlag'=>$an_aby_soll_timeVorschlag,
	'divAnKundeZielorte'=>$divAnKundeZielorte,
	'imexDivToUpdate'=>$imexDivToUpdate,
	'lid'=>$lid,
	'id'=>$id,
	'rundlaufid'=>$rundlaufid,
	'payloadDiv'=>$payloadDiv,
	'lkwDiv'=>$lkwDiv,
	'divid'=>$rid
    );

    echo json_encode($returnArray);
?>


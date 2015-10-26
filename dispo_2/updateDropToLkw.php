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

$lkwInfoArray = $apl->getRundlaufInfoArray($rundlaufid);

$insertid = $a->addRundlaufPayload($rundlaufid,$ie,$auftragsnr);
$ab_aby_soll_datetime = strtotime('2100-01-01');
$ab_aby_soll_datetimeVorschlag = "";

if($insertid>0){
    $imexArray = $apl->getRundlaufImExArray($rundlaufid);
    if($imexArray!==NULL){
	$pocet=0;
	$anKundeOrtArray = array();
	foreach ($imexArray as $imex){
	    $payload = $imex['imex'].$imex['auftragsnr'];
	    $payloadId = $imex['id'];
	    $ie=$imex['imex'];
	    if($pocet>3){
		$payloadDiv.="<br>";
	    }
	    if($ie=='E'){
		// v pripade exportu muzu podle nejnizsiho ex_soll navrhnout ab_aby_soll_datetime
		// take muzu navrhnout An Kunde Ort
		$aI = $apl->getAuftragInfoArray($imex['auftragsnr']);
		if($aI!==NULL){
		    $ex_soll_datetime = $aI[0]['ex_soll_datetime'];
		    $exStime = strtotime($ex_soll_datetime);
		    if($exStime<$ab_aby_soll_datetime){
			$ab_aby_soll_datetime = $exStime;
		    }
		    $anKundeOrtArray[$aI[0]['zielort_id']]=$imex['auftragsnr'];
		}
	    }
	    $payloadDiv.="<div id='payloadId_$payloadId' class='lkwPayLoad payLoad_$ie'>$payload</div>";
	    $pocet++;
	}
	$ab_aby_soll_dateVorschlag = date('d.m.Y',$ab_aby_soll_datetime);
	$ab_aby_soll_timeVorschlag = date('H:i',$ab_aby_soll_datetime);
	//an kunde ort div, pripravit
	$anKundeOrtDiv = "<select id='an_kunde_ort_id_$rundlaufid'>";
	foreach ($anKundeOrtArray as $zielort_id=>$ex){
	    $zielortName = $apl->getZielortName($zielort_id);
	    $selected = $zielort_id==$lkwInfoArray[0]['an_kunde_ort_id']?'selected':'';
	    $anKundeOrtDiv.= "<option $selected value='".$zielort_id."'>$zielortName</option>";
	}
	$anKundeOrtDiv.= "</select>";
    }

    $lkwDiv = "";
    $imexStr = "";
    if($imexArray!==NULL){
	$pocet=0;
	foreach ($imexArray as $imex){
	    $auftrStr = substr($imex['auftragsnr'],4);
	    if($pocet>1){
		$imexStr.="<br>";
	    }
	    $imexStr.= "<span style='border:1px solid black;padding:0.1em;' class='payLoad_".$imex['imex']."'>".$auftrStr."</span>";
	}
	$rliA= $apl->getRundlaufInfoArray($rundlaufid);
	$rli = $rliA[0];
	$lkwDiv.=$rli['lkw_kz']."/".$imexStr;
    }    
}

$returnArray = array(
    'anKundeOrtDiv'=>$anKundeOrtDiv,
    'an_kunde_ort_array'=>$anKundeOrtArray,
    'ab_aby_soll_date_vorschlag'=>$ab_aby_soll_dateVorschlag,
    'ab_aby_soll_time_vorschlag'=>$ab_aby_soll_timeVorschlag,
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


<?

require_once '../db.php';
require_once './commons.php';

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

$imexDivToUpdate = $imex.'_'.$auftragsnr;

if($insertid>0){
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
	$pocet=0;
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
}

$returnArray = array(
    'exCount'=>$exCount,
    'imCount'=>$imCount,
    'divAnKundeZielorte'=>$divAnKundeZielorte,
    'imexDivToUpdate'=>$imexDivToUpdate,
    'anKundeOrtDiv'=>$anKundeOrtDiv,
    'an_kunde_ort_array'=>$anKundeOrtArray,
    'ab_aby_soll_date_vorschlag'=>$ab_aby_soll_dateVorschlag,
    'ab_aby_soll_time_vorschlag'=>$ab_aby_soll_timeVorschlag,
    'an_aby_soll_date_vorschlag'=>$an_aby_soll_dateVorschlag,
    'an_aby_soll_time_vorschlag'=>$an_aby_soll_timeVorschlag,
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


<?
require_once '../db.php';

    $id = $_POST['id'];
    $val = $_POST['val'];
    $kd_von = $_POST['kd_von'];
    $kd_bis = $_POST['kd_bis'];
    $bis = $_POST['bis'];
    
    // zjistim zda mam hodnotu value ulozenou v databazi artiklu

    $apl = AplDB::getInstance();

    $minuten = intval($val);
    $planNrPosition = strpos($id, '_')+1;
    $plan = intval(substr($id, $planNrPosition+1, 6));
    $statnr = substr($id, $planNrPosition+8, 5);
    $datum = substr($id, $planNrPosition+14);
    $dbDatum = substr($datum, 0,4)."-".  substr($datum, 4,2)."-".  substr($datum, 6,2);
    $dbDatum_von = date('Y-m-d',strtotime("+1 day", strtotime($dbDatum)));
    $bisTime = strtotime($apl->make_DB_datum($bis));
    $exSollTime = $bisTime;
    $exDateTimeSollRow = $apl->getExDatumSoll($plan);
    if($exDateTimeSollRow!==NULL){
	$exDateTimeSoll = $exDateTimeSollRow['ex_datetime_soll'];
	$exSollTime = strtotime($exDateTimeSoll);
    }
    if($exSollTime<$bisTime) $bisTime = $exSollTime;
    $dbDatum_bis = date('Y-m-d',$bisTime);

    $sql = $apl->updatePlanSollTag($plan,$statnr,$dbDatum,$minuten);
    $summeProPlanTag = $apl->getPlanSollTagSumme($plan,$dbDatum);

    $time = strtotime($dbDatum_von);
    $zuBearbArray = array();
    while($time<=$bisTime){
	$index = "zubearbeiten_P".$plan."_".$statnr."_".date('Ymd',$time);
	$zuBearbArray[$index] = number_format($apl->getPlanZuBearbeiten("P".$plan, $statnr, $time),0,',',' ');
	$index = "zubearbeiten_P".$plan."_sum_".date('Ymd',$time);
	$zuBearbArray[$index] = number_format($apl->getPlanZuBearbeiten("P".$plan, "sum", $time),0,',',' ');
	$time = strtotime("+1 day",$time);
    }
    
    
    $summeProTatNrTag = $apl->getPlanSollStatnrSumme($dbDatum,$statnr,$kd_von,$kd_bis);
    $summeProTag = $apl->getPlanSollTagKundeSumme($dbDatum,$kd_von,$kd_bis);
    $summinAll = $apl->getPlanSollTagAll($dbDatum);
    
    
    // solltag_P122576_S0041_20140310
    $summeid = "solltag_P".$plan."_sum_".$datum;
    
    //solltagsum_S0041_20140312
    $summeTatnrTagId = "solltagsum_".$statnr."_".$datum;
    $summeTagId = "solltagsum_sum_".$datum;
    
    $summinAllId = 'summinall_'.$datum;
    
    $returnArray = array(
	'id'=>$id,
	'val'=>$val,
	'kd_von'=>$kd_von,
	'kd_bis'=>$kd_bis,
	'minuten'=>number_format($minuten, 0, ',', ' '),
	'plan'=>$plan,
	'statnr'=>$statnr,
	'datum'=>$datum,
	'dbDatum'=>$dbDatum,
//	'sql'=>$sql,
	'summeplan'=>  number_format($summeProPlanTag, 0, ',', ' '),
	'summeid'=>$summeid,
	'summestatnrtagId'=>$summeTatnrTagId,
	'summestatnrtagValue'=>number_format($summeProTatNrTag, 0, ',', ' '),
	'summetagId'=>$summeTagId,
	'summetagValue'=>number_format($summeProTag, 0, ',', ' '),
	'summinAllId'=>$summinAllId,
	'summinAllValue'=>number_format($summinAll, 0, ',', ' '),
	'datum_von_DB'=>$dbDatum_von,
	'datum_bis_DB'=>$dbDatum_bis,
	'zubearbarray'=>$zuBearbArray,
    );

    
    
    echo json_encode($returnArray);
?>


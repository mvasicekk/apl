<?
require_once '../db.php';

    $von = $_POST['von'];
    $bis = $_POST['bis'];
    $kd_von = $_POST['kd_von'];
    $kd_bis = $_POST['kd_bis'];

    
    // zjistim zda mam hodnotu value ulozenou v databazi artiklu

    $apl = AplDB::getInstance();

    $timeVon = strtotime($apl->make_DB_datum($von));
    $timeBis = strtotime($apl->make_DB_datum($bis));
    $planyArray = $apl->getPlaene($kd_von,$kd_bis,$timeVon,$timeBis);
    $summeIA = array();
    
    if ($planyArray !== NULL) {
    if (($timeVon > 0) && ($timeBis >= $timeVon)) {
	$time = $timeVon;
	$dispoDiv.="<table class='dispotable'>";
	$dispoDiv.="<tbody>";
	while ($time <= $timeBis) {
	    $sollProTagArray = $apl->getPlanSollProTagArray($kd_von,$kd_bis);
    	    foreach ($planyArray as $plan) {
		$planT = $plan['auftragsnr'];
		$pIA = $apl->getPlanInfoArray("P".$planT,$von,$bis,$time);
		$planyInfoArray[$planT] = $pIA;
		$timeID = date('Ymd',$time);
		//sumy pro zobrazene plany
		if($pIA!==NULL){
		foreach ($pIA as $statnr=>$pI){
		    $summeIA[$timeID][$statnr]['ist']+=$pI['ist'];
		    $summeIA[$timeID][$statnr]['solltag']+=$pI['solltag'];
		    $summeIA[$timeID][$statnr]['sollprotag']=$sollProTagArray[$statnr];
		    //$summeIA[$timeID]['sum']['sollprotag']+=$sollProTagArray[$statnr];
		    
		}
		}
	    }
	    foreach ($sollProTagArray as $sV)
		$summeIA[$timeID]['sum']['sollprotag']+=$sV;

	    $dayClass = date('D', $time);
	    $todayTime = strtotime($apl->make_DB_datum(date('d.m.Y')));
	    $summinAll = $apl->getPlanSollTagAll(date('Y-m-d',$time));
	    if ($time == $todayTime)
		$dayClass = "today";
	    $dispoDiv.="<tr>";
	    $dispoDiv.="<th class='$dayClass'>";
	    $dispoDiv.="<table class='tagsummetable'>";
		    $dispoDiv.="<tr><td class='datumheader' colspan='4'>".date('d.m.Y', $time)."</td></tr>";
		    //radek pod datumem , suma minut pro vsechny zakazniky i nezobrazene
		    $dispoDiv.="<tr>";
		    $dispoDiv.="<td class='summinheader'>Soll(ALL)</td>";
		    $dispoDiv.="<td class='summinvalue' colspan='3' id='summinall_$timeID'>".  number_format($summinAll, 0, ',', ' ')."</td>";
		    $dispoDiv.="</tr>";
		    //------------------------------------------------------------------
		    $dispoDiv.="<tr>";
		    $dispoDiv.="<td class='statnr'>Statnr</td>";
		    $dispoDiv.="<td class='solltag'>SollproTag</td>";
		    $dispoDiv.="<td class='solltag'>Soll/Tag</td>";
		    $dispoDiv.="<td class='ist'>Ist</td>";
		    $dispoDiv.="</tr>";
		    foreach ($summeIA[$timeID] as $statnr=>$pi){
			$rowClass = $statnr;
			$dispoDiv.="<tr class='$rowClass'>";
			$dispoDiv.="<td class='statnr'>";
			$dispoDiv.=$statnr;
			$dispoDiv.="</td>";
			$dispoDiv.="<td class='sollprotag'>";
			$dispoDiv.=number_format($pi['sollprotag'], 0, ',', ' ');
			$dispoDiv.="</td>";
			$dispoDiv.="<td id='solltagsum"."_".$statnr."_".$timeID."' class='solltag'>";
			$sollTagValue = number_format($pi['solltag'], 0, ',', ' ');
			$dispoDiv.= $sollTagValue;
			$dispoDiv.="</td>";
			$dispoDiv.="<td class='ist'>";
			$dispoDiv.=number_format($pi['ist'], 0, ',', ' ');
			$dispoDiv.="</td>";
			$dispoDiv.="</tr>";
		    }
		    $dispoDiv.="</table>";
	    $dispoDiv.="</td>";
	    
	    foreach ($planyArray as $plan) {
		$planT=$plan['auftragsnr'];
		$planExDatum = $plan['ex_datum_soll'];
		$zielort = $plan['zielort'];
		$exDatumTime = strtotime(substr($planExDatum,0,10));
		$timeID = date('Ymd',$time);
		if($time==$exDatumTime) 
		    $exTagClass='exporttag';
		else
		    $exTagClass = '';
		
		$terminAktual = "P".$planT;
		// bunka s tabulkou pro planovany export
		$dispoDiv.="<td class='$dayClass'>";
		//$planInfoArray = $apl->getPlanInfoArray($terminAktual,$von,$bis,$time);
		$planInfoArray = $planyInfoArray[$planT];
		//$dispoDiv.=print_r($planInfoArray,TRUE);
		if($planInfoArray!==NULL){
		    $dispoDiv.="<table class='exporttable $exTagClass'>";
		    $dispoDiv.="<tr><td class='planheader' colspan='6'>$planT ($planExDatum) $zielort</td></tr>";
		    $dispoDiv.="<tr>";
		    $dispoDiv.="<td class='planheader' colspan='2'>".date('d.m.Y',$time)."</td>";
		    $dispoDiv.="<td class='planheader' colspan='4'></td>";
		    $dispoDiv.="</tr>";
		    $dispoDiv.="<tr>";
		    $dispoDiv.="<td class='statnr'>Statnr</td>";
		    $dispoDiv.="<td class='vzkdplan'>VzKdPlan</td>";
		    $dispoDiv.="<td class='istfertig'>ist fertig</td>";
		    $dispoDiv.="<td class='zubearbeiten'>zu bearb.</td>";
		    $dispoDiv.="<td class='solltag'>Soll/Tag</td>";
		    $dispoDiv.="<td class='ist'>Ist</td>";
		    $dispoDiv.="</tr>";
		    foreach ($planInfoArray as $statnr=>$pi){
			$rowClass = $statnr;
			if($statnr=='sum') 
			    $readonly="readonly='readonly'";
			else
			    $readonly="";
			$dispoDiv.="<tr class='$rowClass'>";
			$dispoDiv.="<td class='statnr'>";
			$dispoDiv.=$statnr;
			$dispoDiv.="</td>";
			$dispoDiv.="<td class='vzkdplan'>";
			$dispoDiv.=number_format($pi['vzkdplan'], 0, ',', ' ');
			$dispoDiv.="</td>";
			$dispoDiv.="<td class='istfertig'>";
			$dispoDiv.=number_format($pi['fertig'], 0, ',', ' ');
			$dispoDiv.="</td>";
			// 2014-04-01 odecist predesle naplanovane minuty ------
			
			if($statnr=="sum")
			    $beforeMins = floatval($apl->getPlanSollTagSumme($planT, date('Y-m-d',$time), TRUE));
			else
			    $beforeMins = floatval($apl->getPlanSollTagMinuten($planT, $statnr, date('Y-m-d',$time), TRUE));
			
			$zubearbeiten = $pi['vzkdplan'] - $pi['fertig'] - $beforeMins;
			$negativClass = $zubearbeiten<0?'negativ':'';
			$dispoDiv.="<td class='zubearbeiten $negativClass' id='zubearbeiten_".$terminAktual.'_'.$statnr.'_'.$timeID."'>";
			$dispoDiv.=number_format($zubearbeiten, 0, ',', ' ');
			// -----------------------------------------------------
			$dispoDiv.="</td>";
			$dispoDiv.="<td class='solltag'>";
			$sollTagValue = number_format($pi['solltag'], 0, ',', ' ');
			$dispoDiv.= "<input $readonly maxlength='10' acturl='./sollTagChanged.php' type='text' id='solltag_".$terminAktual."_".$statnr."_".$timeID."' value='".$sollTagValue."'/>";
			$dispoDiv.="</td>";
			$dispoDiv.="<td class='ist'>";
			$dispoDiv.=number_format($pi['ist'], 0, ',', ' ');
			$dispoDiv.="</td>";
			$dispoDiv.="</tr>";
		    }
		    $dispoDiv.="</table>";
		}
		else{
		    $dispoDiv.="&nbsp;";
		}
		$dispoDiv.="</td>";
	    }
	    $dispoDiv.="</tr>";
	    //pridam 1 den
	    //$time+=60 * 60 * 24;
	    $time = strtotime("+1 day", $time);
	}
	$dispoDiv.="</tbody>";
	$dispoDiv.="</table>";
    }
}

$returnArray = array(
	'von'=>$von,
	'bis'=>$bis,
	'sollProTagArray'=>$sollProTagArray,
	'planyArray'=>$planyArray,
	'summeIA'=>$summeIA,
	'divcontent'=>$dispoDiv,
    );

    echo json_encode($returnArray);
?>


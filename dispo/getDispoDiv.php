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
	while ($time <= $timeBis) {
	    
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
		}
		}
	    }

	    $dayClass = date('D', $time);
	    $todayTime = strtotime($apl->make_DB_datum(date('d.m.Y')));
	    $summinAll = $apl->getPlanSollTagAll(date('Y-m-d',$time));
	    if ($time == $todayTime)
		$dayClass = "today";
	    $dispoDiv.="<tr>";
	    $dispoDiv.="<td class='$dayClass'>";
	    $dispoDiv.="<table class='tagsummetable'>";
		    $dispoDiv.="<tr><td class='datumheader' colspan='3'>".date('d.m.Y', $time)."</td></tr>";
		    //radek pod datumem , suma minut pro vsechny zakazniky i nezobrazene
		    $dispoDiv.="<tr>";
		    $dispoDiv.="<td class='summinheader'>Soll(ALL)</td>";
		    $dispoDiv.="<td class='summinvalue' colspan='2' id='summinall_$timeID'>".  number_format($summinAll, 0, ',', ' ')."</td>";
		    $dispoDiv.="</tr>";
		    //------------------------------------------------------------------
		    $dispoDiv.="<tr>";
		    $dispoDiv.="<td class='statnr'>Statnr</td>";
		    $dispoDiv.="<td class='solltag'>Soll/Tag</td>";
		    $dispoDiv.="<td class='ist'>Ist</td>";
		    $dispoDiv.="</tr>";
		    foreach ($summeIA[$timeID] as $statnr=>$pi){
			$rowClass = $statnr;
			$dispoDiv.="<tr class='$rowClass'>";
			$dispoDiv.="<td class='statnr'>";
			$dispoDiv.=$statnr;
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
		    $dispoDiv.="<tr><td class='planheader' colspan='6'></td></tr>";
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
			$dispoDiv.="<td class='zubearbeiten'>";
			$zubearbeiten = $pi['vzkdplan'] - $pi['fertig'];
			$dispoDiv.=number_format($zubearbeiten, 0, ',', ' ');
			$dispoDiv.="</td>";
			$dispoDiv.="<td class='solltag'>";
			$sollTagValue = number_format($pi['solltag'], 0, ',', ' ');
//			if($statnr=='sum')
//			    $sollTagValue = intval($apl->getPlanSollTagSumme($planT, date('Y-m-d',$time)));
//			else
//			    $sollTagValue = intval($apl->getPlanSollTagMinuten($planT, $statnr, date('Y-m-d',$time)));
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
	    $time+=60 * 60 * 24;
	}
	$dispoDiv.="</table>";
    }
}

$returnArray = array(
	'von'=>$von,
	'bis'=>$bis,
	'planyArray'=>$planyArray,
	'summeIA'=>$summeIA,
	'divcontent'=>$dispoDiv,
    );

    echo json_encode($returnArray);
?>


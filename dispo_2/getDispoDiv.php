<?
require_once '../db.php';

    $von = $_POST['von'];
    $bis = $_POST['bis'];
    $kd_von = $_POST['kd_von'];
    $kd_bis = $_POST['kd_bis'];
    $rm_bis = $_POST['rm_bis'];
    $nurMitMinutenCheck = $_POST['nurMitMin'];

    
    //$statnrArray = array("S0011","S0041","S0051","S0061","S0081");
    
    if($nurMitMinutenCheck==1)
	$nurMitMinutenFlag = TRUE;
    else
	$nurMitMinutenFlag = FALSE;
    // zjistim zda mam hodnotu value ulozenou v databazi artiklu

    $apl = AplDB::getInstance();
    
    $statnrArray = $apl->getStatNrArray(TRUE);
    //AplDB::varDump($statnrArray);
    
    $rmZeit = $apl->validateZeit($rm_bis);
    if($rmZeit=="00:00"){
	$rmZeit = date("H:i");
    }
    $rm_bis = $rmZeit;

    $rmDateTime = $apl->make_DB_datetime($rmZeit, date('d.m.Y'));
    
    $timeVon = strtotime($apl->make_DB_datum($von));
    $timeBis = strtotime($apl->make_DB_datum($bis));
    $planyArray = $apl->getKundenMitGeplantenMinuten($kd_von, $kd_bis);
    $summeIA = array();
    $columns = array();
    
    if ($planyArray !== NULL) {
    if (($timeVon > 0) && ($timeBis >= $timeVon)) {
	$time = $timeVon;
	$dispoDiv.="<table class='dispotable'>";
	$dispoDiv.="<thead>";
	$dispoDiv.="<th>";
	$dispoDiv.="Datum";
	$dispoDiv.="</th>";
	array_push($columns, array('width'=>380,'align'=>'center'));
	foreach ($planyArray as $plan) {
//		$planT=$plan['auftragsnr'];
//		$planExDatum = $plan['ex_datum_soll'];
//		$zielort = $plan['zielort'];
		$kunde = $plan['kunde'];
		$dispoDiv.="<th>";
		$dispoDiv.="$kunde";
		$dispoDiv.="</th>";
		array_push($columns, array('width'=>380,'align'=>'center'));
	}
	$dispoDiv.="</thead>";
	
	$dispoDiv.="<tbody>";
	while ($time <= $timeBis) {
	    $timeID = date('Ymd',$time);
	    
	    // pripravit pole s hodnotama pro soucty v levem sloupci
	    $sollProTagArray[$timeID] = $apl->getPlanSollProTagArray($kd_von,$kd_bis,$time,FALSE);
	    $istTagArray1 = $apl->getIstFertigKunde($kd_von, $kd_bis, date('Y-m-d',$time), $rmDateTime);
	    foreach ($statnrArray as $statnr){
		$istTagArray[$timeID][$statnr] = 0;
		foreach ($planyArray as $plan){
		    $sollTagArray[$timeID][$statnr] += intval($apl->getPlanSollTagMinuten($plan['kunde']."NOEX", $statnr, date('Y-m-d',$time)));
		    if(is_array($istTagArray1) && array_key_exists($plan['kunde'], $istTagArray1)){
			$istTagArray[$timeID][$statnr] += intval($istTagArray1[$plan['kunde']]['sum_vzkd_'.$statnr]);
		    }
		}
		$diffTagArray[$timeID][$statnr] = $sollProTagArray[$timeID][$statnr]-$sollTagArray[$timeID][$statnr];
	    }

	    $dayClass = date('D', $time);
	    $todayTime = strtotime($apl->make_DB_datum(date('d.m.Y')));
	    $summinAll = $apl->getPlanSollTagAll(date('Y-m-d',$time));
	    if ($time == $todayTime)
		$dayClass = "today";
	    $dispoDiv.="<tr>";
	    $dispoDiv.="<th class='$dayClass'>";
	    $dispoDiv.="<table class='tagsummetable'>";
		    $tagSummeArray = array();
		    $dispoDiv.="<tr><td class='datumheader' colspan='5'>".date('d.m.Y', $time)."</td></tr>";
		    //radek pod datumem , suma minut pro vsechny zakazniky i nezobrazene
		    $dispoDiv.="<tr>";
		    $dispoDiv.="<td class='summinheader'>Soll(ALL)</td>";
		    $dispoDiv.="<td class='summinvalue' colspan='4' id='summinall_$timeID'>".  number_format($summinAll, 0, ',', ' ')."</td>";
		    $dispoDiv.="</tr>";
		    //------------------------------------------------------------------
		    $dispoDiv.="<tr>";
		    $dispoDiv.="<td class='statnr'>Statnr</td>";
		    $dispoDiv.="<td class='solltag'>VzKdPlan</td>";
		    $dispoDiv.="<td class='solltag'>VzKdSoll</td>";
		    $dispoDiv.="<td class='solltag'>Diff</td>";
		    $dispoDiv.="<td class='ist'>VzKdIst(!EX)</td>";
		    $dispoDiv.="</tr>";
		    foreach ($statnrArray as $statnr){
			$rowClass = $statnr;
			$dispoDiv.="<tr class='$rowClass'>";
			
			$dispoDiv.="<td class='statnr'>";
			$dispoDiv.=$statnr;
			$dispoDiv.="</td>";
			
			$dispoDiv.="<td id='sollprotagsum"."_".$statnr."_".$timeID."' class='sollprotag'>";
			$tagSummeArray['sollprotagsum']+=$sollProTagArray[$timeID][$statnr];
			$dispoDiv.=number_format($sollProTagArray[$timeID][$statnr], 0, ',', ' ');
			$dispoDiv.="</td>";
			
			$dispoDiv.="<td id='solltagsum"."_".$statnr."_".$timeID."' class='solltag'>";
			$tagSummeArray['solltagsum']+=$sollTagArray[$timeID][$statnr];
			$sollTagValue = number_format($sollTagArray[$timeID][$statnr], 0, ',', ' ');
			$dispoDiv.= $sollTagValue;
			$dispoDiv.="</td>";
			
			$dispoDiv.="<td id='diff"."_".$statnr."_".$timeID."' class='solltag'>";
			$tagSummeArray['diff']+=$diffTagArray[$timeID][$statnr];
			$diffTagValue = number_format($diffTagArray[$timeID][$statnr], 0, ',', ' ');
			$dispoDiv.= $diffTagValue;
			$dispoDiv.="</td>";

			$dispoDiv.="<td class='ist'>";
			$istValue = $istTagArray[$timeID][$statnr];
			$tagSummeArray['ist']+=$istTagArray[$timeID][$statnr];
			$dispoDiv.=number_format($istValue, 0, ',', ' ');
			$dispoDiv.="</td>";
			$dispoDiv.="</tr>";
		    }
		    //radek se sumou
		    $rowClass = 'sum';
		    $statnr = 'sum';
		    $dispoDiv.="<tr class='$rowClass'>";
			
		    $dispoDiv.="<td class='statnr'>";
		    $dispoDiv.='Sum';
		    $dispoDiv.="</td>";
			
		    $dispoDiv.="<td id='sollprotagsum"."_".$statnr."_".$timeID."' class='sollprotag'>";
		    $dispoDiv.=number_format($tagSummeArray['sollprotagsum'], 0, ',', ' ');
		    $dispoDiv.="</td>";
			
		    $dispoDiv.="<td id='solltagsum"."_".$statnr."_".$timeID."' class='solltag'>";
		    $sollTagValue = number_format($tagSummeArray['solltagsum'], 0, ',', ' ');
		    $dispoDiv.= $sollTagValue;
		    $dispoDiv.="</td>";
			
		    $dispoDiv.="<td id='diff"."_".$statnr."_".$timeID."' class='solltag'>";
		    $diffValue = number_format($tagSummeArray['diff'], 0, ',', ' ');
		    $dispoDiv.= $diffValue;
		    $dispoDiv.="</td>";

		    $dispoDiv.="<td class='ist'>";
		    $dispoDiv.=number_format($tagSummeArray['ist'], 0, ',', ' ');
		    $dispoDiv.="</td>";
		    $dispoDiv.="</tr>";
		    
	    $dispoDiv.="</table>";
	    $dispoDiv.="</th>";
//	    
	    foreach ($planyArray as $plan) {
		$kunde = $plan['kunde'];
		
		//planvzkd
		$r = $apl->getPlanVzKdNoEx($kunde, $time);
		if($r!==NULL){
		    $vzkdPlanArray[$timeID][$kunde] = $r[0];
		}
		else {
		    $vzkdPlanArray[$timeID][$kunde] = array(
			"termin"=>$kunde."NOEX",
			"sum_vzkd_S0011"=>0,
			"sum_vzkd_S0041"=>0,
			"sum_vzkd_S0043"=>0,
			"sum_vzkd_S0051"=>0,
			"sum_vzkd_S0061"=>0,
			"sum_vzkd_S0062"=>0,
			"sum_vzkd_S0081"=>0,
			"sum_vzkd"=>0,
			);
		}
		
		//istfertig
		$r = $apl->getPlanIstFertigNoEx($kunde, $time,$rmDateTime);
		if($r!==NULL){
		    $istFertigArray[$timeID][$kunde] = $r[0];
		}
		else {
		    $istFertigArray[$timeID][$kunde] = array(
			"termin"=>$kunde."NOEX",
			"sum_vzkd_S0011"=>0,
			"sum_vzkd_S0041"=>0,
			"sum_vzkd_S0043"=>0,
			"sum_vzkd_S0051"=>0,
			"sum_vzkd_S0061"=>0,
			"sum_vzkd_S0062"=>0,
			"sum_vzkd_S0081"=>0,
			"sum_vzkd"=>0,
			);
		}
		
		//zubearbeiten
		foreach ($vzkdPlanArray[$timeID][$kunde] as $minIndex=>$val){
		    $zuBearbeitenArray[$timeID][$kunde][$minIndex] = intval($val)-intval($istFertigArray[$timeID][$kunde][$minIndex]);
		}
		
		//solltag + ist
		foreach ($statnrArray as $statnr){
		    $minuten = $apl->getPlanSollTagMinuten($kunde."NOEX", $statnr, date('Y-m-d',$time));
		    $minuten = intval($minuten);
		    $sollPlanTagArray[$timeID][$kunde][$statnr] = $minuten;
		    $istTagArray1 = $apl->getIstFertigKunde($kunde, $kunde, date('Y-m-d',$time), $rmDateTime);
		    if(is_array($istTagArray1) && array_key_exists($kunde, $istTagArray1)){
			$istTagStatNrArray[$timeID][$kunde][$statnr] = intval($istTagArray1[$kunde]['sum_vzkd_'.$statnr]);
		    }
		}
		
		$planT = $vzkdPlanArray[$timeID][$kunde]['termin'];
		$terminAktual = $planT;
		
		$timeID = date('Ymd',$time);
		$exTagClass = '';
//		// bunka s tabulkou pro planovany export
		$dispoDiv.="<td class='$dayClass'>";
		$dispoDiv.="<table class='exporttable $exTagClass'>";
		$dispoDiv.="<tr>";
		$dispoDiv.="<td class='planheader' colspan='2'>$kunde</td>";
		$exporteInfo = "";
		$exporteArray = $apl->getExporteVzkdDatumKunde($kunde,$time);
		$exClass="";
		if($exporteArray!==NULL){
		    $exMin = 0;
		    $exNrArray = array();
		    foreach ($exporteArray as $ex){
			//$exMin += intval($ex['vzkd']);
			array_push($exNrArray, substr($ex['auftragsnr'], 3));
		    }
		    $exporteInfo = "EX:(".  join(',', $exNrArray).") ";//.number_format($exMin,0,',',' ');
		    $exClass = "exportsollweg";
		}
		$dispoDiv.="<td class='$exClass planheader' colspan='3'>$exporteInfo</td>";
		$dispoDiv.="</tr>";
		$dispoDiv.="<tr>";
		$dispoDiv.="<td class='planheader' colspan='2'>".date('d.m.Y',$time)."</td>";
		$importeInfo = "";
		$importeArray = $apl->getImporteVzkdDatumKunde($kunde,$time);
		$imClass="";
		if($importeArray!==NULL){
		    $imMin = 0;
		    $imNrArray = array();
		    foreach ($importeArray as $im){
			$imMin += intval($im['vzkd']);
			array_push($imNrArray, substr($im['auftragsnr'], 3));
		    }
		    $imNrs = "IM:(".  join(',', $imNrArray).") ";
		    $imMins = number_format($imMin,0,',',' ');
		    $importeInfo = "IM:(".  join(',', $imNrArray).") ".number_format($imMin,0,',',' ');
		    $imClass = "importarrived";
		}
		
		if(strlen($importeInfo)>0){
		    $dispoDiv.="<td class='planheader $imClass' style='border-right:none;' colspan='2'>$imNrs</td>";
		    $dispoDiv.="<td class='planheader $imClass' style='border-left:none;text-align:right;' colspan='1'>$imMins</td>";
		}
		else {
		    $dispoDiv.="<td class='planheader $imClass' colspan='3'>$importeInfo</td>";
		}
		
		$dispoDiv.="</tr>";
		$dispoDiv.="<tr>";
		$dispoDiv.="<td class='vzkdplan'>VzKdGesamt</td>";
		$dispoDiv.="<td class='istfertig'>VzKdFertig</td>";
		$dispoDiv.="<td class='zubearbeiten'>VzKdRest</td>";
		$dispoDiv.="<td class='solltag'>VzKdSoll</td>";
		$dispoDiv.="<td class='ist'>VzKdIst(!EX)</td>";
		$dispoDiv.="</tr>";
		$kundeSollTagPlanArray[$kunde] = $apl->getPlanSollProTagArray($kunde, $kunde);
		foreach ($statnrArray as $statnr){
			$rowClass = $statnr;
			if($statnr=='sum') 
			    $readonly="readonly='readonly'";
			else
			    $readonly="";
			$dispoDiv.="<tr class='$rowClass'>";
			
			$dispoDiv.="<td class='vzkdplan'>";
			$vzkdPlanValue = intval($vzkdPlanArray[$timeID][$kunde]['sum_vzkd_'.$statnr]);
			$tagKundeSummeArray[$timeID][$kunde]['vzkdplan']+=$vzkdPlanValue;
			$dispoDiv.=number_format($vzkdPlanValue, 0, ',', ' ');
			$dispoDiv.="</td>";
			
			$dispoDiv.="<td class='istfertig'>";
			$istFertigValue = intval($istFertigArray[$timeID][$kunde]['sum_vzkd_'.$statnr]);
			$tagKundeSummeArray[$timeID][$kunde]['istfertig']+=$istFertigValue;
			$dispoDiv.=number_format($istFertigValue, 0, ',', ' ');
			$dispoDiv.="</td>";

//			// 2014-04-01 odecist predesle naplanovane minuty ------
			$beforeMins = floatval($apl->getPlanSollTagMinuten($planT, $statnr, date('Y-m-d',$time), TRUE));
			$zubearbeiten = $vzkdPlanValue - $istFertigValue - $beforeMins;
			$tagKundeSummeArray[$timeID][$kunde]['zubearbeiten']+=$zubearbeiten;
			$negativClass = $zubearbeiten<0?'negativ':'';
			$dispoDiv.="<td class='zubearbeiten $negativClass' data-name='zubearbeiten_".$terminAktual.'_'.$statnr.'_'.$timeID."' id='zubearbeiten_".$terminAktual.'_'.$statnr.'_'.$timeID."'>";
			$dispoDiv.=number_format($zubearbeiten, 0, ',', ' ');
			$dispoDiv.="</td>";
			
			
			$dispoDiv.="<td class='solltaginput'>";
			$val = intval($sollPlanTagArray[$timeID][$kunde][$statnr]);
			$sollTagValue = number_format($val, 0, ',', ' ');
			$tagKundeSummeArray[$timeID][$kunde]['solltag']+=$val;
			$kdSollTagMin = "Standardplan: ".$kundeSollTagPlanArray[$kunde][$statnr];
			$dispoDiv.= "<input title='$kdSollTagMin' maxlength='10' acturl='./sollTagChanged.php' type='text' id='solltag_".$terminAktual."_".$statnr."_".$timeID."' name='solltag_".$terminAktual."_".$statnr."_".$timeID."' value='".$sollTagValue."'/>";
			$dispoDiv.="</td>";
			
			$dispoDiv.="<td class='ist'>";
			$val = intval($istTagStatNrArray[$timeID][$kunde][$statnr]);
			$tagKundeSummeArray[$timeID][$kunde]['ist']+=$val;
			$dispoDiv.=number_format($val, 0, ',', ' ');
			$dispoDiv.="</td>";

			$dispoDiv.="</tr>";
		    }
		    
		    // radek se sumou
		    $statnr = "sum";
		    $rowClass = $statnr;
		    
    			$dispoDiv.="<tr class='$rowClass'>";
			
			$dispoDiv.="<td class='vzkdplan'>";
			$val = $tagKundeSummeArray[$timeID][$kunde]['vzkdplan'];
			$dispoDiv.=number_format($val, 0, ',', ' ');
			$dispoDiv.="</td>";
			
			$dispoDiv.="<td class='istfertig'>";
			$val = $tagKundeSummeArray[$timeID][$kunde]['istfertig'];
			$dispoDiv.=number_format($val, 0, ',', ' ');
			$dispoDiv.="</td>";

//			// 2014-04-01 odecist predesle naplanovane minuty ------
			$beforeMins = floatval($apl->getPlanSollTagSumme($planT, date('Y-m-d',$time), TRUE));
			$val = $tagKundeSummeArray[$timeID][$kunde]['zubearbeiten'];
			$negativClass = $val<0?'negativ':'';
			$dispoDiv.="<td class='zubearbeiten $negativClass' data-name='zubearbeiten_".$terminAktual.'_'.$statnr.'_'.$timeID."' id='zubearbeiten_".$terminAktual.'_'.$statnr.'_'.$timeID."'>";
			$dispoDiv.=number_format($val, 0, ',', ' ');
			$dispoDiv.="</td>";
			
			
			$dispoDiv.="<td class='solltag'>";
			$sollTagValue = number_format($tagKundeSummeArray[$timeID][$kunde]['solltag'], 0, ',', ' ');
			$dispoDiv.= "<input disabled='disabled' readonly='readonly' maxlength='10' acturl='./sollTagChanged.php' type='text' id='solltag_".$terminAktual."_".$statnr."_".$timeID."' name='solltag_".$terminAktual."_".$statnr."_".$timeID."' value='".$sollTagValue."'/>";
			$dispoDiv.="</td>";
			
			$dispoDiv.="<td class='ist'>";
			$val = $tagKundeSummeArray[$timeID][$kunde]['ist'];
			$dispoDiv.=number_format($val, 0, ',', ' ');
			$dispoDiv.="</td>";

			$dispoDiv.="</tr>";

		    $dispoDiv.="</table>";
		$dispoDiv.="</td>";
	    }
	    $dispoDiv.="</tr>";
//	    //pridam 1 den
	    $time = strtotime("+1 day", $time);
	}
	$dispoDiv.="</tbody>";
	$dispoDiv.="</table>";
    }
}

$returnArray = array(
	'von'=>$von,
	'bis'=>$bis,
	'rm_bis'=>$rm_bis,
	'rmDateTime'=>$rmDateTime,
	'columns'=>$columns,
	'sollProTagArray'=>$sollProTagArray,
	'sollTagArray'=>$sollTagArray,
	'diffTagArray'=>$diffTagArray,
	'istTagArray'=>$istTagArray,
	'planyArray'=>$planyArray,
	'vzkdPlanArray'=>$vzkdPlanArray,
	'istFertigArray'=>$istFertigArray,
	'zuBearbeitenArray'=>$zuBearbeitenArray,
	'sollPlanTagArray'=>$sollPlanTagArray,
	'istTagStatNrArray'=>$istTagStatNrArray,
	'kundeSollTagPlanArray'=>$kundeSollTagPlanArray,
	'summeIA'=>$summeIA,
	'divcontent'=>$dispoDiv,
    );

    echo json_encode($returnArray);
?>


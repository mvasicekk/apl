<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function getLkwFormDivs($rundlaufid) {
    $apl = AplDB::getInstance();

    $imexArrayToUpdate = array();
    $ab_aby_soll_datetime = strtotime('2100-01-01');
    $ab_aby_soll_datetimeVorschlag = "";
    $an_aby_soll_datetime = strtotime('2100-01-01');
    $an_aby_soll_datetimeVorschlag = "";
    
    $exCount = 0;
    $imCount = 0;

    //prehled nalozenych zakazek
    //vsechny na aute
    $imexArray = $apl->getRundlaufImExArray($rundlaufid);
    if ($imexArray !== NULL) {
	$pocet = 0;
	$anKundeOrtArray = array();
	foreach ($imexArray as $imex) {
	    $payload = $imex['imex'] . $imex['auftragsnr'];
	    $payloadId = $imex['id'];
	    $ie = $imex['imex'];
	    $prefix = $ie == 'E' ? 'ex' : 'im';
	    array_push($imexArrayToUpdate, $prefix ."_". $imex['auftragsnr']);
	    if ($pocet > 3) {
		$payloadDiv.="<br>";
	    }
	    if ($ie == 'E') {
		$exCount++;
		// v pripade exportu muzu podle nejnizsiho ex_soll navrhnout ab_aby_soll_datetime
		// take muzu navrhnout An Kunde Ort
		$aI = $apl->getAuftragInfoArray($imex['auftragsnr']);
		if ($aI !== NULL) {
		    $ex_soll_datetime = $aI[0]['ex_soll_datetime'];
		    $exStime = strtotime($ex_soll_datetime);
		    if ($exStime < $ab_aby_soll_datetime) {
			$ab_aby_soll_datetime = $exStime;
		    }
		    //$anKundeOrtArray[$aI[0]['zielort_id']]=$imex['auftragsnr'];
		    if (!is_array($anKundeOrtArray[$aI[0]['zielort_id']])) {
			$anKundeOrtArray[$aI[0]['zielort_id']] = array();
		    }
		    array_push($anKundeOrtArray[$aI[0]['zielort_id']], $imex['auftragsnr']);
		}
	    }
	    else{
		$imCount++;
		// v pripade importu muzu podle nejnizsiho im_soll navrhnout an_aby_soll_datetime
		$aI = $apl->getAuftragInfoArray($imex['auftragsnr']);
		if ($aI !== NULL) {
		    $im_soll_datetime = $aI[0]['im_soll_datetime'];
		    $imStime = strtotime($im_soll_datetime);
		    if ($imStime < $an_aby_soll_datetime) {
			$an_aby_soll_datetime = $imStime;
		    }
		}
	    }
	    $payloadDiv.="<div id='payloadId_$payloadId' class='lkwPayLoad payLoad_$ie'>$payload</div>";
	    $pocet++;
	}
	$ab_aby_soll_dateVorschlag = date('d.m.Y', $ab_aby_soll_datetime);
	$ab_aby_soll_timeVorschlag = date('H:i', $ab_aby_soll_datetime);
	$an_aby_soll_dateVorschlag = date('d.m.Y', $an_aby_soll_datetime);
	$an_aby_soll_timeVorschlag = date('H:i', $an_aby_soll_datetime);

	
	$divAnKundeZielorte = "";
	$divAnKundeZielorte.="<tr class='header'>";
	$divAnKundeZielorte.="<td>Ankunft-Kd Zielort</td>";
	$divAnKundeZielorte.="<td>Soll Tag/Zeit</td>";
	$divAnKundeZielorte.="<td>Ist Tag/Zeit</td>";
	$divAnKundeZielorte.="</tr>";
	foreach ($anKundeOrtArray as $zielort_id => $exArray) {
	    
	    $tTZA = $apl->getZielortInfoArray($zielort_id);
	    if($tTZA!==NULL){
		$timeToZielort = floatval($tTZA[0]['route_zeit']) * 60 * 60;
	    }
	    else{
		$timeToZielort = 0;
	    }
	    
	    $anKundeSollDateTime = $ab_aby_soll_datetime+$timeToZielort;
	    
	    $divAnKundeZielorte.= "<tr>";
	    $zielortName = $apl->getZielortName($zielort_id);
	    $divAnKundeZielorte.= "<td>";
	    $divAnKundeZielorte.= $zielortName; //."(".  join(',', $exArray).")";
	    $divAnKundeZielorte.= "</td>";
	    //soll
	    $divAnKundeZielorte.= "<td>";
	    $dateValue = date('d.m.Y', $anKundeSollDateTime);
	    $divAnKundeZielorte.="<input class='datepicker' type='text' id='an_kunde_soll_date_$zielort_id" . "_" . $rundlaufid . "' maxlength='10' size='10' value='" . $dateValue . "' />";
	    $divAnKundeZielorte.= "/";
	    $dateValue = date('H:i', $anKundeSollDateTime);
	    $divAnKundeZielorte.="<input type='text' id='an_kunde_soll_time_$zielort_id" . "_" . $rundlaufid . "' maxlength='5' size='5' value='" . $dateValue . "' />";
	    $divAnKundeZielorte.= "</td>";

	    //ist
	    $divAnKundeZielorte.= "<td>";
	    $dateValue = date('d.m.Y', $anKundeSollDateTime);
	    $divAnKundeZielorte.="<input disabled='disabled' class='datepicker' type='text' id='an_kunde_ist_date_$zielort_id" . "_" . $rundlaufid . "' maxlength='10' size='10' value='" . $dateValue . "' />";
	    $divAnKundeZielorte.= "/";
	    $dateValue = date('H:i', $anKundeSollDateTime);
	    $divAnKundeZielorte.="<input disabled='disabled' type='text' id='an_kunde_ist_time_$zielort_id" . "_" . $rundlaufid . "' maxlength='5' size='5' value='" . $dateValue . "' />";
	    $divAnKundeZielorte.= "</td>";
	    $divAnKundeZielorte.= "</tr>";
	}
    }
    
    return array(
	'divAnKundeZielorte'=>$divAnKundeZielorte,
	'ab_aby_soll_dateVorschlag'=>$ab_aby_soll_dateVorschlag,
	'ab_aby_soll_timeVorschlag'=>$ab_aby_soll_timeVorschlag,
	'an_aby_soll_dateVorschlag'=>$an_aby_soll_dateVorschlag,
	'an_aby_soll_timeVorschlag'=>$an_aby_soll_timeVorschlag,
	'payloadDiv'=>$payloadDiv,
	'imexArrayToUpdate'=>$imexArrayToUpdate,
	'imexArray'=>$imexArray,
	'exCount'=>$exCount,
	'imCount'=>$imCount,
    );
}

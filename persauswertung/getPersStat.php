<?php

require_once '../db.php';

$inputData = $_GET;

// utility functions -----------------------------------------------------------
function getSumRow($a) {
    $sum = 0;
    if (is_array($a)) {
	foreach ($a as $val) {
	    $sum+=$val;
	}
    }
    return $sum;
}

function getAvgRow($a) {
    $sum = 0;
    $count = 0;
    $avg = 0;
    if (is_array($a)) {
	foreach ($a as $val) {
	    $sum+=$val;
	    $count++;
	}
    }
    
    if($count>0){
	$avg = $sum/$count;
    }
    return $avg;
}

function formatRowValues(&$a,$decimals,$decsep,$thoussep){
    foreach ($a as $index=>$valArr){
	$a[$index] = $a[$index]!=0?number_format($a[$index],$decimals,$decsep,$thoussep):'';
    }
}



// zjistim zda mam hodnotu value ulozenou v databazi artiklu

$a = AplDB::getInstance();

//$terminMatch = trim($_GET['termin']);
$persVon = intval(trim($_GET['persvon']));
$persBis = intval(trim($_GET['persbis']));
$datumVon = trim($_GET['von']);
$datumBis = trim($_GET['bis']);


if ($datumVon != 0 && $datumBis != 0) {
    $datumVon = date('Y-m-d', $datumVon / 1000);
    $datumBis = date('Y-m-d', $datumBis / 1000);
}


$monthsArrayAll = array();
// vytvorim si pole mesico podle zadaneho rozsahu von a bis
$start = strtotime($datumVon);
$end = strtotime($datumBis);
$increment = 60 * 60 * 24; // 1 den
while($start<=$end){
    $year = date('y',$start);
    $month = date('m',$start);
    $yearMonth = "$year-$month";
    $monthsArrayAll[$yearMonth]+=1;
    $start+=$increment;
}


$sql="select dpers.PersNr as persnr from dpers where (PersNr between '$persVon' and '$persBis') and (austritt is null or austritt<eintritt) and (dpersstatus='MA')";
$persnrArray = $a->getQueryRows($sql);


foreach ($persnrArray as $p) {
    $persnr = $p['persnr'];
    // Ausschuss ---------------------------------------------------------------
    $sql =" select";
    $sql.="     drueck.PersNr as persnr,";
    $sql.="     drueck.Teil,";
    $sql.="     drueck.insert_stamp,";
    $sql.="     drueck.`Stück` as stk,";
    $sql.="     drueck.Datum as datum,";
    $sql.="     dkopf.Gew as teil_gew,";
    $sql.="     count(TaetNr) as tat_count,";
    $sql.="     sum(`Auss-Stück`) as stk_auss_sum";
    $sql.=" from";
    $sql.="     drueck";
    $sql.=" join dkopf on dkopf.Teil=drueck.Teil";
    $sql.=" where";
    $sql.="     PersNr='$persnr'";
    $sql.="     and Datum between '$datumVon' and '$datumBis'";
    $sql.="     and (DATE_FORMAT(`verb-von`,'%H:%i:%s')!='00:00:00')";
    $sql.=" group by";
    $sql.="     PersNr,";
    $sql.="     drueck.Teil,";
    $sql.="     drueck.insert_stamp,";
    $sql.="     drueck.`Stück`";

    $persRows = $a->getQueryRows($sql);
    
    $monthsArray = array();
    if ($persRows !== NULL) {
	foreach ($persRows as $pr) {
	    //$persnr = $pr['persnr'];
	    $datum = $pr['datum'];
	    $month = date('m', strtotime($datum));
	    $yearMonth = date('y-m', strtotime($datum));
	    $monthsArray[$yearMonth]+=1;
	    
	    $stkGut = intval($pr['stk']);
	    $stkAuss = intval($pr['stk_auss_sum']);
	    $gew = floatval($pr['teil_gew']);
	    $zeilen[$persnr]['A6']['sum_gew'][$yearMonth]+=($stkGut + $stkAuss) * $gew;
	}

	$monthsArray = array_keys($monthsArray);
	sort($monthsArray);
	foreach ($monthsArray as $yearMonth) {
	    $year = 2000 + intval(substr($yearMonth, 0, 2));
	    $month = intval(substr($yearMonth, 3));
	    $a6Gew = $a->getGewAussTypYearMonthPersnr(6, $year, $month, $persnr);

	    $sumGew = floatval($zeilen[$persnr]['A6']['sum_gew'][$yearMonth]);

//	    $zeilen[$persnr]['A6']['sum_gew'][$yearMonth] = $sumGew == 0 ? '' : number_format($sumGew, 0, ',', ' ');
	    $zeilen[$persnr]['A6']['a6_gew'][$yearMonth] = $a6Gew;

	    if (($sumGew != 0) && ($a6Gew!=0)) {
		$zeilen[$persnr]['A6']['a6_prozent'][$yearMonth] = ($a6Gew / $sumGew) * 100;
	    } else {
		$zeilen[$persnr]['A6']['a6_prozent'][$yearMonth] = 0;
	    }
	}
    }
    
    // reklamace ---------------------------------------------------------------
    $sql = " select";
    $sql.= " dpersschulung.persnr,";
    $sql.= " dreklamation.rekl_nr,";
    $sql.= "     dreklamation.rekl_datum,";
    $sql.= " dreklamation.interne_bewertung";
    $sql.= " from";
    $sql.= " dreklamation";
    $sql.= " join dpersschulung on dpersschulung.rekl_id=dreklamation.id";
    $sql.= " where";
    $sql.= " dreklamation.rekl_datum between '$datumVon' and '$datumBis'";
    $sql.= " and dpersschulung.persnr='$persnr'";
    $sql.= " and dpersschulung.rekl_verursacher<>0";
    $sql.= " group by";
    $sql.= " dpersschulung.persnr,";
    $sql.= " dreklamation.rekl_nr";
    
    $monthsArray = array();
    $persRows = $a->getQueryRows($sql);
    if ($persRows !== NULL) {
	foreach ($persRows as $pr) {
	    $datum = $pr['rekl_datum'];
	    $month = date('m', strtotime($datum));
	    $yearMonth = date('y-m', strtotime($datum));
	    $monthsArray[$yearMonth]+=1;
	    
	    $ie = strtoupper(substr($pr['rekl_nr'], 0,1));
	    if($ie=='I'){
		$zeilen[$persnr]['rekl']['sum_bewertung_I'][$yearMonth]+=$pr['interne_bewertung'];
	    }
	    if($ie=='E'){
		$zeilen[$persnr]['rekl']['sum_bewertung_E'][$yearMonth]+=$pr['interne_bewertung'];
	    }
	}
    }
    
    // abmahnung ---------------------------------------------------------------
    $sql = " select";
    $sql.= " dabmahnung.persnr,";
    $sql.= " dabmahnung.grund,";
    $sql.= " dabmahnung.datum";
    $sql.= " from";
    $sql.= " dabmahnung";
    $sql.= " where";
    $sql.= " dabmahnung.persnr='$persnr'";
    $sql.= " and dabmahnung.datum between '$datumVon' and '$datumBis'";
    $sql.= " group by";
    $sql.= " dabmahnung.persnr,";
    $sql.= " dabmahnung.grund,";
    $sql.= " dabmahnung.datum";
    
    
    $monthsArray = array();
    $persRows = $a->getQueryRows($sql);
    if ($persRows !== NULL) {
	foreach ($persRows as $pr) {
	    $datum = $pr['datum'];
	    $month = date('m', strtotime($datum));
	    $yearMonth = date('y-m', strtotime($datum));
	    $monthsArray[$yearMonth]+=1;
	    
	    $zeilen[$persnr]['abmahnung'][$pr['grund']][$yearMonth]+=1;
	}
    }
    
    //dochazka -----------------------------------------------------------------
    $sql = " select";
    $sql.= " dzeit.PersNr as persnr,";
    $sql.= " dzeit.tat,";
    $sql.= " dzeit.Datum as datum";
    $sql.= " from";
    $sql.= " dzeit";
    $sql.= " where";
    $sql.= " dzeit.persnr='$persnr'";
    $sql.= " and dzeit.datum between '$datumVon' and '$datumBis'";
    $sql.= " group by";
    $sql.= " dzeit.persnr,";
    $sql.= " dzeit.tat,";
    $sql.= " dzeit.Datum";
    
    $monthsArray = array();
    $persRows = $a->getQueryRows($sql);
    if ($persRows !== NULL) {
	foreach ($persRows as $pr) {
	    $datum = $pr['datum'];
	    $month = date('m', strtotime($datum));
	    $yearMonth = date('y-m', strtotime($datum));
	    $monthsArray[$yearMonth]+=1;
	    
	    if($pr['tat']=='n' || $pr['tat']=='z'|| $pr['tat']=='d'|| $pr['tat']=='nw'){
		// nacitat jen ty, ktere me zajimaji
		$zeilen[$persnr]['dzeit'][$pr['tat']][$yearMonth]+=1;
	    }
	}
    }
    
    // leistung ----------------------------------------------------------------
    foreach ($monthsArrayAll as $yearMonth=>$dayCount){
	$year = 2000 + intval(substr($yearMonth, 0, 2));
	$month = intval(substr($yearMonth, 3));
	$von = "$year-$month-01";
	$bis = "$year-$month-$dayCount";
	$arbTageProMonat = $a->getArbTageBetweenDatums($von, $bis);
	$eintritt = $a->getEintrittsDatumDB($persnr);
	if(strtotime($eintritt)>  strtotime($von)){
	    $vonPers = $eintritt;
	}
	else{
	    $vonPers = $von;
	}
	$arbTagePersMonat = $a->getArbTageBetweenDatums($vonPers, $bis);
	$dTage = $a->getTatTageBetweenDatums('d',$vonPers,$bis,$persnr);
	$nwTage = $a->getTatTageBetweenDatums('nw',$vonPers,$bis,$persnr);
	$monatNormMinuten = ($arbTagePersMonat - $dTage - $nwTage) * 8 * 60;
	$ganzMonatNormMinuten = $arbTageProMonat * 8 * 60;
	$leistungArray = $a->getPersLeistungArray($persnr,$von,$bis);
	$persInfoA = $a->getPersInfoArray($persnr);
	$leistFaktor = $persInfoA[0]['leistfaktor'];
	
	if($leistungArray!==NULL){
	    $vzaby = $leistungArray['vzaby'];
	    $vzaby_akkord = $leistungArray['vzaby_akkord'];
	    $vzaby_zeit = ($vzaby-$vzaby_akkord)*$leistFaktor;
	}
	else{
	    $vzaby = 0;
	    $vzaby_akkord = 0;
	    $vzaby_zeit = 0;
	}
	
	$anwTageArbeitsTage = $a->getATageProPersnrBetweenDatums($persnr, $vonPers, $bis, 1);
	
	$leistungsGradGanzMonatR = $ganzMonatNormMinuten!=0?(($vzaby_akkord+$vzaby_zeit) /  $ganzMonatNormMinuten):0;
	$leistungsGradR = $monatNormMinuten!=0?(($vzaby_akkord+$vzaby_zeit) / $monatNormMinuten):0;
	
	$leistungsGradGanzMonat = $leistungsGradGanzMonatR*100;
	$leistungsGrad = $leistungsGradR*100;
	
	
	$leistPraemieBerechnet1 = $a->getLeistungsPraemieBetragProLeistungsFaktor($leistungsGradGanzMonatR) * $arbTageProMonat;
	
        if ($a->getLeistungsPraemieBetragProLeistungsFaktor($leistungsGradGanzMonatR) == 200)
            $leistPraemieBerechnet = $leistPraemieBerechnet1;
        else {
            if ($a->getLeistungsPraemieBetragProLeistungsFaktor($leistungsGradR) > $a->getLeistungsPraemieBetragProLeistungsFaktor($leistungsGradGanzMonatR))
                $leistPraemieBerechnet = $a->getLeistungsPraemieBetragProLeistungsFaktor($leistungsGradR) * $anwTageArbeitsTage;
            else
                $leistPraemieBerechnet = $leistPraemieBerechnet1;
        }
	
//	$zeilen[$persnr]['leistung']['daycount'][$yearMonth] = $dayCount;
//	$zeilen[$persnr]['leistung']['arbTageProMonat'][$yearMonth] = $arbTageProMonat;
	$zeilen[$persnr]['leistung']['ganzMonatNormMinuten'][$yearMonth] = $ganzMonatNormMinuten;
//	$zeilen[$persnr]['leistung']['arbTagePersMonat'][$yearMonth] = $arbTagePersMonat;
//	$zeilen[$persnr]['leistung']['dTage'][$yearMonth] = $dTage;
//	$zeilen[$persnr]['leistung']['nwTage'][$yearMonth] = $nwTage;
	$zeilen[$persnr]['leistung']['monatNormMinuten'][$yearMonth] = $monatNormMinuten;
//	$zeilen[$persnr]['leistung']['vzaby'][$yearMonth] = number_format($vzaby,0,',',' ');
	$zeilen[$persnr]['leistung']['vzaby_akkord'][$yearMonth] = $vzaby_akkord;
	$zeilen[$persnr]['leistung']['vzaby_zeit'][$yearMonth] = $vzaby_zeit;
//	$zeilen[$persnr]['leistung']['leistfaktor'][$yearMonth] = $leistFaktor;
	$zeilen[$persnr]['leistung']['leistGrad'][$yearMonth] = $leistungsGrad;
	$zeilen[$persnr]['leistung']['leistGradGanzMonat'][$yearMonth] = $leistungsGradGanzMonat;
//	$zeilen[$persnr]['leistung']['leistPrem'][$yearMonth] = $leistPraemieBerechnet;
    }
    
    
//    HF reparaturen -----------------------------------------------------------
    $sql=" select";
    $sql.=" dreparaturkopf.persnr_ma as persnr,";
    $sql.=" dreparaturkopf.datum,";
    $sql.=" sum((dreparaturkopf.repzeit*5)+if(dreparaturpos.anzahl is null,0,dreparaturpos.anzahl)*if(dreparaturpos.et_alt=1,if(`eink-artikel`.`art-vr-preis` is null,0,`eink-artikel`.`art-vr-preis`*0.4),if(`eink-artikel`.`art-vr-preis` is null,0,`eink-artikel`.`art-vr-preis`))) as sum_rep_kosten";
    $sql.=" from dreparaturkopf";
    $sql.=" join dreparatur_geraete on dreparatur_geraete.invnummer=dreparaturkopf.invnummer";
    $sql.=" join dreparatur_anlagen on dreparatur_anlagen.anlage_id=dreparatur_geraete.anlage_id";
    $sql.=" left join dreparaturpos on dreparaturpos.reparatur_id=dreparaturkopf.id";
    $sql.=" left join `eink-artikel` on CONVERT(`eink-artikel`.`art-nr`,char)=convert(dreparaturpos.artnr,char)";
    $sql.=" where";
    $sql.=" dreparaturkopf.datum between '$datumVon' and '$datumBis'";
    $sql.=" and dreparaturkopf.persnr_ma='$persnr'";
    $sql.=" group by";
    $sql.=" dreparaturkopf.persnr_ma,";
    $sql.=" dreparaturkopf.datum";
    
        
    $monthsArray = array();
    $persRows = $a->getQueryRows($sql);
    if ($persRows !== NULL) {
	foreach ($persRows as $pr) {
	    $datum = $pr['datum'];
	    $month = date('m', strtotime($datum));
	    $yearMonth = date('y-m', strtotime($datum));
	    $monthsArray[$yearMonth]+=1;
	    $sumKosten = 1.1 * $pr['sum_rep_kosten'];
	    $zeilen[$persnr]['HF_repkosten']['repkosten'][$yearMonth]+=$sumKosten;
	}
    }
    
    $sql=" select ";
    $sql.=" persnr,";
    $sql.=" datum,";
    $sql.=" sum(if(`dtaetkz-abg`.Stat_Nr='S0011',if(auss_typ=4,`VZ-SOLL`*(`Stück`+`Auss-Stück`),`VZ-SOLL`*(`Stück`)),0)) as sumvzkd_11,";
    $sql.=" sum(if(`dtaetkz-abg`.Stat_Nr='S0051',if(auss_typ=4,`VZ-SOLL`*(`Stück`+`Auss-Stück`),`VZ-SOLL`*(`Stück`)),0)) as sumvzkd_51";
    $sql.=" from drueck";
    $sql.=" join `dtaetkz-abg` on `dtaetkz-abg`.`abg-nr`=drueck.TaetNr";
    $sql.=" join daufkopf on daufkopf.auftragsnr=drueck.AuftragsNr";
    $sql.=" where ";
    $sql.=" daufkopf.kunde<>355 and";
    $sql.=" datum between '$datumVon' and '$datumBis'";
    $sql.=" and persnr='$persnr'";
    $sql.=" group by ";
    $sql.=" persnr,";
    $sql.=" datum";
    $sql.=" having (sumvzkd_11<>0 or sumvzkd_51<>0)";
    
    $monthsArray = array();
    $persRows = $a->getQueryRows($sql);
    if ($persRows !== NULL) {
	foreach ($persRows as $pr) {
	    $datum = $pr['datum'];
	    $month = date('m', strtotime($datum));
	    $yearMonth = date('y-m', strtotime($datum));
	    $monthsArray[$yearMonth]+=1;
	    $zeilen[$persnr]['HF_repkosten']['vzkd_S0011'][$yearMonth]+=$pr['sumvzkd_11'];
	    $zeilen[$persnr]['HF_repkosten']['vzkd_S0051'][$yearMonth]+=$pr['sumvzkd_51'];
	    $zeilen[$persnr]['HF_repkosten']['vzkd_sum'][$yearMonth]+=($pr['sumvzkd_11']+$pr['sumvzkd_51']);

	}
	
	foreach ($monthsArrayAll as $yearMonth=>$dayCount){
	    $year = 2000 + intval(substr($yearMonth, 0, 2));
	    $month = intval(substr($yearMonth, 3));
	    
	    $vzkd_S0011 = $zeilen[$persnr]['HF_repkosten']['vzkd_S0011'][$yearMonth];
	    $vzkd_S0051 = $zeilen[$persnr]['HF_repkosten']['vzkd_S0051'][$yearMonth];
	    $vzkd_sum = $zeilen[$persnr]['HF_repkosten']['vzkd_sum'][$yearMonth];
	    $repKosten = $zeilen[$persnr]['HF_repkosten']['repkosten'][$yearMonth];
	    
//	    $zeilen[$persnr]['HF_repkosten']['repkosten'][$yearMonth] = $repKosten!=0?number_format($zeilen[$persnr]['HF_repkosten']['repkosten'][$yearMonth],0,',',' '):'';
//	    $zeilen[$persnr]['HF_repkosten']['vzkd_S0011'][$yearMonth] = $vzkd_S0011!=0?number_format($vzkd_S0011,0,',',' '):'';
//	    $zeilen[$persnr]['HF_repkosten']['vzkd_S0051'][$yearMonth] = $vzkd_S0051!=0?number_format($vzkd_S0051,0,',',' '):'';
//	    $zeilen[$persnr]['HF_repkosten']['vzkd_sum'][$yearMonth] = $vzkd_sum!=0?number_format($vzkd_sum,0,',',' '):'';
	    $zeilen[$persnr]['HF_repkosten']['faktor'][$yearMonth] = $vzkd_sum!=0?$repKosten/$vzkd_sum:0;
	}

    }
}

$zeilenArray = array();

$monthsArrayAll = array_keys($monthsArrayAll);
sort($monthsArrayAll);

// hodnoty do sloupce Sum a formtovani
foreach ($persnrArray as $p) {
    $persnr = $p['persnr'];
    // naplneni hodnot ve sloupci sum
    $zeilen[$persnr]['A6']['sum_gew']['sum'] = getSumRow($zeilen[$persnr]['A6']['sum_gew']);
    $zeilen[$persnr]['A6']['a6_gew']['sum'] = getSumRow($zeilen[$persnr]['A6']['a6_gew']);
    $zeilen[$persnr]['A6']['a6_prozent']['sum'] = $zeilen[$persnr]['A6']['sum_gew']['sum']!=0?$zeilen[$persnr]['A6']['a6_gew']['sum']/$zeilen[$persnr]['A6']['sum_gew']['sum']*100:0;
    // jen kdy mam nejakou reklamaci
    if(is_array($zeilen[$persnr]['rekl']['sum_bewertung_I'])){
	$zeilen[$persnr]['rekl']['sum_bewertung_I']['sum'] = getSumRow($zeilen[$persnr]['rekl']['sum_bewertung_I']);
    }
    if(is_array($zeilen[$persnr]['rekl']['sum_bewertung_E'])){
	$zeilen[$persnr]['rekl']['sum_bewertung_E']['sum'] = getSumRow($zeilen[$persnr]['rekl']['sum_bewertung_E']);
    }
    
    if(is_array($zeilen[$persnr]['dzeit'])){
	foreach ($zeilen[$persnr]['dzeit'] as $tat=>$t){
	    $zeilen[$persnr]['dzeit'][$tat]['sum'] = getSumRow($zeilen[$persnr]['dzeit'][$tat]);
	}
    }
    if(is_array($zeilen[$persnr]['abmahnung'])){
	foreach ($zeilen[$persnr]['abmahnung'] as $tat=>$t){
	    $zeilen[$persnr]['abmahnung'][$tat]['sum'] = getSumRow($zeilen[$persnr]['abmahnung'][$tat]);
	}
    }
    

    $zeilen[$persnr]['leistung']['vzaby_akkord']['sum'] = getSumRow($zeilen[$persnr]['leistung']['vzaby_akkord']);
    $zeilen[$persnr]['leistung']['vzaby_zeit']['sum'] = getSumRow($zeilen[$persnr]['leistung']['vzaby_zeit']);
    $zeilen[$persnr]['leistung']['ganzMonatNormMinuten']['sum'] = getSumRow($zeilen[$persnr]['leistung']['ganzMonatNormMinuten']);
    $zeilen[$persnr]['leistung']['monatNormMinuten']['sum'] = getSumRow($zeilen[$persnr]['leistung']['monatNormMinuten']);
    $citatel = $zeilen[$persnr]['leistung']['vzaby_akkord']['sum'] + $zeilen[$persnr]['leistung']['vzaby_zeit']['sum'];
    
    $zeilen[$persnr]['leistung']['leistGrad']['sum'] = $zeilen[$persnr]['leistung']['monatNormMinuten']['sum']!=0?$citatel/$zeilen[$persnr]['leistung']['monatNormMinuten']['sum']*100:0;
    $zeilen[$persnr]['leistung']['leistGradGanzMonat']['sum'] = $zeilen[$persnr]['leistung']['ganzMonatNormMinuten']['sum']!=0?$citatel/$zeilen[$persnr]['leistung']['ganzMonatNormMinuten']['sum']*100:0;
    
    $zeilen[$persnr]['HF_repkosten']['repkosten']['sum'] = getSumRow($zeilen[$persnr]['HF_repkosten']['repkosten']);
    $zeilen[$persnr]['HF_repkosten']['vzkd_S0011']['sum'] = getSumRow($zeilen[$persnr]['HF_repkosten']['vzkd_S0011']);
    $zeilen[$persnr]['HF_repkosten']['vzkd_S0051']['sum'] = getSumRow($zeilen[$persnr]['HF_repkosten']['vzkd_S0051']);
    $zeilen[$persnr]['HF_repkosten']['vzkd_sum']['sum'] = getSumRow($zeilen[$persnr]['HF_repkosten']['vzkd_sum']);
    $zeilen[$persnr]['HF_repkosten']['faktor']['sum'] = $zeilen[$persnr]['HF_repkosten']['vzkd_sum']['sum']!=0?$zeilen[$persnr]['HF_repkosten']['repkosten']['sum']/$zeilen[$persnr]['HF_repkosten']['vzkd_sum']['sum']:0;
    
// zformatovani hodnot v radku
    formatRowValues($zeilen[$persnr]['A6']['sum_gew'],0,',',' ');
    formatRowValues($zeilen[$persnr]['A6']['a6_gew'],0,',',' ');
    formatRowValues($zeilen[$persnr]['A6']['a6_prozent'],2,',',' ');
    formatRowValues($zeilen[$persnr]['leistung']['vzaby_akkord'],0,',',' ');
    formatRowValues($zeilen[$persnr]['leistung']['vzaby_zeit'],0,',',' ');
    formatRowValues($zeilen[$persnr]['leistung']['leistGrad'],0,',',' ');
    formatRowValues($zeilen[$persnr]['leistung']['leistGradGanzMonat'],0,',',' ');
    formatRowValues($zeilen[$persnr]['leistung']['leistGradGanzMonat'],0,',',' ');
    formatRowValues($zeilen[$persnr]['leistung']['ganzMonatNormMinuten'],0,',',' ');
    formatRowValues($zeilen[$persnr]['leistung']['monatNormMinuten'],0,',',' ');
    
    formatRowValues($zeilen[$persnr]['HF_repkosten']['repkosten'],0,',',' ');
    formatRowValues($zeilen[$persnr]['HF_repkosten']['vzkd_S0011'],0,',',' ');
    formatRowValues($zeilen[$persnr]['HF_repkosten']['vzkd_S0051'],0,',',' ');
    formatRowValues($zeilen[$persnr]['HF_repkosten']['vzkd_sum'],0,',',' ');
    formatRowValues($zeilen[$persnr]['HF_repkosten']['faktor'],2,',',' ');
    
    
}

foreach ($persnrArray as $p) {
    $persnr = $p['persnr'];
    $rowsArray = $zeilen[$persnr];
    
    $nameA = $a->getNameVorname($persnr);
    $name = "";
    if ($nameA !== NULL) {
	$name = $nameA['name'] . ' ' . $nameA['vorname'];
    }
    array_push($zeilenArray, array('section' => 'persheader', 'persnr' => $persnr, 'name' => $name));
    if (is_array($rowsArray)) {
	foreach ($rowsArray as $group => $groupArray) {
	    foreach ($groupArray as $groupDetail => $monthArray) {
		array_push($zeilenArray, array('section' => 'groupdetail', 'persnr' => $persnr, 'name' => $name, 'group' => $group, 'groupDetail' => $groupDetail, 'monthValues' => $monthArray));
	    }
	}
    }
}

$returnArray = array(
    'persNrArray'=>$persnrArray,
    'monthsArray'=>$monthsArrayAll,
    'zeilenraw' => $zeilen,
    'von' => $datumVon,
    'bis' => $datumBis,
    'persvon' => $persVon,
    'persbis' => $persBis,
    "zeilen" => $zeilenArray,
);

echo json_encode($returnArray);
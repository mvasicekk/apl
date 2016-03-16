<?php

require_once '../db.php';

$inputData = $_GET;

// utility functions -----------------------------------------------------------
function getKoefMonths($mesicu){
    $koef=0;
    if($mesicu>=9){
	$koef = 1;
    }
    else if($mesicu>=6){
	$koef = 0.8;
    }
    else if($mesicu>=3){
	$koef = 0.5;
    }
    else if($mesicu>=1){
	$koef = 0.2;
    }
    return $koef;
}

function getKoefYears($mesicu){
    $koef=0;
    if($mesicu>=10){
	$koef = 2;
    }
    else if($mesicu>=5){
	$koef = 1.5;
    }
    else if($mesicu>=3){
	$koef = 1.3;
    }
    else if($mesicu>=2){
	$koef = 1.2;
    }
    else if($mesicu>=1){
	$koef = 1;
    }
    return $koef;
}

function getSumRow($a) {
    $sum = 0;
    if (is_array($a)) {
	foreach ($a as $key=>$val) {
	    if($key!='sum'){
		$sum+=$val;
	    }
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

function formatRowValues(&$a,$decimals,$decsep,$thoussep,$keepNulls=FALSE){
    foreach ($a as $index=>$valArr){
	if($keepNulls===FALSE){
	    $a[$index] = ($a[$index]!=0?number_format($a[$index],$decimals,$decsep,$thoussep):'');
	}
	else{
	    $a[$index] = ($a[$index]!==''?number_format($a[$index],$decimals,$decsep,$thoussep):'');
	}
    }
}



// zjistim zda mam hodnotu value ulozenou v databazi artiklu

$a = AplDB::getInstance();

//$terminMatch = trim($_GET['termin']);
$persVon = intval(trim($_GET['persvon']));
$persBis = intval(trim($_GET['persbis']));
$datumVon = trim($_GET['von']);
$datumBis = trim($_GET['bis']);
$stammOE = strtoupper(strtr(trim($_GET['stammoe']),'*','%'));


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
if((strlen($stammOE)>0) && ($stammOE!='%')){
    $sql.=" and dpers.regeloe like '%$stammOE%'";
}
$sql.=" order by dpers.persnr";

$persnrArray = $a->getQueryRows($sql);
if($persnrArray!==NULL){
foreach ($persnrArray as $p) {
    $persnr = $p['persnr'];
    
    //loajalita ----------------------------------------------------------------
    //loajalita, hodnoty jen do sloupce sum
    $eintritt = $a->getEintrittsDatumDB($persnr);
    $zeilen[$persnr]['loajalita']['eintritt']['sum'] = date('d.m.Y',strtotime($eintritt));
    // pocet mesicu pp do datumu $bisDatum
    $d1 = strtotime($eintritt);
    $d2 = strtotime($datumBis);
    $min_date = min($d1, $d2);
    $max_date = max($d1, $d2);
    $mesicu = 0;
    while (($min_date = strtotime("+1 MONTH", $min_date)) <= $max_date) {
	$mesicu++;
    }
    $zeilen[$persnr]['loajalita']['pp_months']['sum'] = $mesicu;
    $zeilen[$persnr]['loajalita']['pp_months']['czk'] = getKoefMonths($mesicu);
    //pocet let do datumBis
    $zeilen[$persnr]['loajalita']['pp_years']['sum'] = number_format($mesicu/12,1,',',' ');
    $roku = $mesicu / 12;
    $loajalitaPp_yearsCzk[$persnr] = getKoefYears($roku);
    $zeilen[$persnr]['loajalita']['pp_years']['czk'] = $loajalitaPp_yearsCzk[$persnr];
    //"rocni" fond hodin, presne od datumVon do datumBis
    $aTageFond = $a->getArbTageBetweenDatums($datumVon, $datumBis);
    $zeilen[$persnr]['loajalita']['von_bis_fond_days']['sum'] = $aTageFond;
    $zeilen[$persnr]['loajalita']['von_bis_fond_hours']['sum'] = $aTageFond*8;
    
    
    // nacharbeit ---------------------------------------------------------------
    $sql =" select";
    $sql.="     drueck.PersNr as persnr,";
    $sql.="     drueck.Datum as datum,";
    $sql.="     sum(if(TaetNr>=6500 and TaetNr<=6599,if(auss_typ=4,abs(drueck.`Stück`+`Auss-Stück`)*`VZ-IST`,abs(drueck.`Stück`)*`VZ-IST`),0)) as vzaby_65xx,";
    $sql.="     sum(if(auss_typ=4,(drueck.`Stück`+`Auss-Stück`)*`VZ-SOLL`,(drueck.`Stück`)*`VZ-SOLL`)) as vzkd";
    $sql.=" from";
    $sql.="     drueck";
    $sql.=" where";
    $sql.="     PersNr='$persnr'";
    $sql.="     and Datum between '$datumVon' and '$datumBis'";
    $sql.=" group by";
    $sql.="     PersNr,";
    $sql.="     drueck.Datum";
    
    $persRows = $a->getQueryRows($sql);
    $monthsArray = array();
    if ($persRows !== NULL) {
	foreach ($persRows as $pr) {
	    //$persnr = $pr['persnr'];
	    $datum = $pr['datum'];
	    $month = date('m', strtotime($datum));
	    $yearMonth = date('y-m', strtotime($datum));
	    $monthsArray[$yearMonth]+=1;
	    $vzaby_65xx = floatval($pr['vzaby_65xx']);
	    $vzkd = floatval($pr['vzkd']);
	    $zeilen[$persnr]['nacharbeit']['vzaby_65xx'][$yearMonth]+=$vzaby_65xx;
	    $zeilen[$persnr]['nacharbeit']['vzkd'][$yearMonth]+=$vzkd;
	}

	$monthsArray = array_keys($monthsArray);
	sort($monthsArray);
	foreach ($monthsArray as $yearMonth) {
	    $vzaby_65xx = floatval($zeilen[$persnr]['nacharbeit']['vzaby_65xx'][$yearMonth]);
	    $vzkd = floatval($zeilen[$persnr]['nacharbeit']['vzkd'][$yearMonth]);

	    if (($vzkd != 0)) {
		$zeilen[$persnr]['nacharbeit']['faktor'][$yearMonth] = ($vzaby_65xx / $vzkd) * 100;
	    } else {
		$zeilen[$persnr]['nacharbeit']['faktor'][$yearMonth] = '';
	    }
	}
    }
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
	    $zeilen[$persnr]['A6']['a6_gew'][$yearMonth] = $a6Gew;

	    if (($sumGew != 0)) {
		$zeilen[$persnr]['A6']['a6_prozent'][$yearMonth] = ($a6Gew / $sumGew) * 100;
	    } else {
		$zeilen[$persnr]['A6']['a6_prozent'][$yearMonth] = '';
	    }
	    
	    //vyhodnoceni pomoci kriterii
	    //$value = $zeilen[$persnr]['A6']['a6_prozent'][$yearMonth];
	    //$bew = $a->getBewertungKriterium(100,'q_auss',$value,'bis',$yearMonth,1);
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
		//$zeilen[$persnr]['rekl']['bewertung_I'][$yearMonth] = 0;
	    }
	    if($ie=='E'){
		$zeilen[$persnr]['rekl']['sum_bewertung_E'][$yearMonth]+=$pr['interne_bewertung'];
		//$zeilen[$persnr]['rekl']['bewertung_E'][$yearMonth] = 0;
	    }
	}
	//projit vsechny mesice pro vyhodnoceni kriterii
	$monthsArray = array_keys($monthsArrayAll);
	sort($monthsArray);
	foreach ($monthsArray as $yearMonth) {
	    //vyhodnoceni pomoci kriterii I
	    $value = $zeilen[$persnr]['rekl']['sum_bewertung_I'][$yearMonth];
	    if(intval($value)==0){
		$zeilen[$persnr]['rekl']['sum_bewertung_I'][$yearMonth]=0;
	    }
	    //$bew = $a->getBewertungKriterium(100, 'q_reklamationen', $value, 'bis', $yearMonth, 1);
	    //$zeilen[$persnr]['rekl']['bewertung_I'][$yearMonth] = $bew;
	    //vyhodnoceni pomoci kriterii E
	    $value = $zeilen[$persnr]['rekl']['sum_bewertung_E'][$yearMonth];
	    if(intval($value)==0){
		$zeilen[$persnr]['rekl']['sum_bewertung_E'][$yearMonth]=0;
	    }
	    //$bew = $a->getBewertungKriterium(100, 'q_reklamationen', $value, 'bis', $yearMonth, 1);
	    //$zeilen[$persnr]['rekl']['bewertung_E'][$yearMonth] = $bew;
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
    $sql.= " dtattypen.oestatus,";
    $sql.= " dzeit.Datum as datum,";
    $sql.=" sum(if(dtattypen.oestatus='a',dzeit.stunden,0)) as sum_stundena";
    $sql.= " from";
    $sql.= " dzeit";
    $sql.= " join dtattypen on dtattypen.tat=dzeit.tat";
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
	    
	    $zeilen[$persnr]['dzeit']['anwstd'][$yearMonth] += $pr['sum_stundena'];
	    
	    if($pr['tat']=='n' || $pr['tat']=='z'|| $pr['tat']=='d'|| $pr['tat']=='nw'|| $pr['tat']=='nv'){
		// nacitat jen ty, ktere me zajimaji
		$zeilen[$persnr]['dzeit'][$pr['tat']][$yearMonth]+=1;
	    }
	}
    }
    foreach ($monthsArrayAll as $yearMonth=>$dayCount){
	$year = 2000 + intval(substr($yearMonth, 0, 2));
	$month = intval(substr($yearMonth, 3));
	$von = "$year-$month-01";
	$bis = "$year-$month-$dayCount";
	$arbTageProMonat = $a->getArbTageBetweenDatums($von, $bis);
	$zeilen[$persnr]['dzeit']['astunden_fond'][$yearMonth] = $arbTageProMonat*8;
	$zeilen[$persnr]['dzeit']['anw_prozent'][$yearMonth] = $zeilen[$persnr]['dzeit']['astunden_fond'][$yearMonth]!=0?$zeilen[$persnr]['dzeit']['anwstd'][$yearMonth]/$zeilen[$persnr]['dzeit']['astunden_fond'][$yearMonth]*100:0;
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
	//2016-03-15
	$zeilen[$persnr]['leistung']['leistGrad'][$yearMonth] = $zeilen[$persnr]['dzeit']['anwstd'][$yearMonth]!=0?($vzaby_akkord+$vzaby_zeit)/($zeilen[$persnr]['dzeit']['anwstd'][$yearMonth]*60)*100:0;
	$zeilen[$persnr]['leistung']['leistGrad1'][$yearMonth] = $leistungsGrad;
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
	    $sumKosten = 1.1 * intval($pr['sum_rep_kosten']);
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
	    $zeilen[$persnr]['HF_repkosten']['faktor'][$yearMonth] = $vzkd_sum!=0?$repKosten/$vzkd_sum:'';
	    //vyhodnoceni pomoci kriterii
	    //$value = floatval(round($zeilen[$persnr]['HF_repkosten']['faktor'][$yearMonth],2));
	    //$bew = $a->getBewertungKriterium(100, 'q_reparaturen', $value, 'bis', $yearMonth, 1);
	    //$zeilen[$persnr]['HF_repkosten']['bewertung'][$yearMonth] = $bew;
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
    $zeilen[$persnr]['A6']['a6_prozent']['sum'] = $zeilen[$persnr]['A6']['sum_gew']['sum']!=0?$zeilen[$persnr]['A6']['a6_gew']['sum']/$zeilen[$persnr]['A6']['sum_gew']['sum']*100:'';
    // kriteria
    $value = $zeilen[$persnr]['A6']['a6_prozent']['sum'];
    $yearMonth = substr($datumBis, 2, 5); // z 2015-12-31 vyberu 15-12
    //$bew = $a->getBewertungKriterium(100,'q_auss',$value,'bis',$yearMonth,1);
    //$zeilen[$persnr]['A6']['bewertung']['sum'] = $bew;

    $zeilen[$persnr]['nacharbeit']['vzaby_65xx']['sum'] = getSumRow($zeilen[$persnr]['nacharbeit']['vzaby_65xx']);
    $zeilen[$persnr]['nacharbeit']['vzkd']['sum'] = getSumRow($zeilen[$persnr]['nacharbeit']['vzkd']);
    $zeilen[$persnr]['nacharbeit']['faktor']['sum'] = $zeilen[$persnr]['nacharbeit']['vzkd']['sum']!=0?$zeilen[$persnr]['nacharbeit']['vzaby_65xx']['sum']/$zeilen[$persnr]['nacharbeit']['vzkd']['sum']:'';
    // jen kdy mam nejakou reklamaci
    
    if(!is_array($zeilen[$persnr]['rekl']['sum_bewertung_I'])){
        //pokud nemam zadne hodnoceni z reklamaci, pridam je pro jednotlive mesice umele
        foreach ($monthsArrayAll as $yearMonth){
	    $zeilen[$persnr]['rekl']['sum_bewertung_I'][$yearMonth] = 0;
        }
    }
    //if(is_array($zeilen[$persnr]['rekl']['sum_bewertung_I'])){
	$zeilen[$persnr]['rekl']['sum_bewertung_I']['sum'] = getSumRow($zeilen[$persnr]['rekl']['sum_bewertung_I']);
    //}
    // kriteria i kdyz nemam zadne reklamace
    $value = $zeilen[$persnr]['rekl']['sum_bewertung_I']['sum'];
    $yearMonth = substr($datumBis, 2, 5); // z 2015-12-31 vyberu 15-12
    //$bew = $a->getBewertungKriterium(100,'q_reklamationen',$value,'bis',$yearMonth,12);
    //$zeilen[$persnr]['rekl']['bewertung_I']['sum'] = $bew;
    
    
    if(!is_array($zeilen[$persnr]['rekl']['sum_bewertung_E'])){
	//pokud nemam zadne hodnoceni z reklamaci, pridam je pro jednotlive mesice umele
	foreach ($monthsArrayAll as $yearMonth){
	    $zeilen[$persnr]['rekl']['sum_bewertung_E'][$yearMonth] = 0;
	}
    }
    //if(is_array($zeilen[$persnr]['rekl']['sum_bewertung_E'])){
	$zeilen[$persnr]['rekl']['sum_bewertung_E']['sum'] = getSumRow($zeilen[$persnr]['rekl']['sum_bewertung_E']);
    //}
    // kriteria i kdyz nemam zadne reklamace
    //$value = $zeilen[$persnr]['rekl']['sum_bewertung_E']['sum'];
    //$yearMonth = substr($datumBis, 2, 5); // z 2015-12-31 vyberu 15-12
    //$bew = $a->getBewertungKriterium(100,'q_reklamationen',$value,'bis',$yearMonth,12);
    //$zeilen[$persnr]['rekl']['bewertung_E']['sum'] = $bew;
    
    
    if(is_array($zeilen[$persnr]['dzeit'])){
	foreach ($zeilen[$persnr]['dzeit'] as $tat=>$t){
	    $zeilen[$persnr]['dzeit'][$tat]['sum'] = getSumRow($zeilen[$persnr]['dzeit'][$tat]);
	}
    }
    if(!is_array($zeilen[$persnr]['dzeit']['z'])){
	// pokud nemam zadna zetka, tak pridam nulovou sumu umele, abych mel kam pridat penize za poctivou praci
	$zeilen[$persnr]['dzeit']['z']['sum'] = 0;
    }
    
    if(is_array($zeilen[$persnr]['abmahnung'])){
	foreach ($zeilen[$persnr]['abmahnung'] as $tat=>$t){
	    $zeilen[$persnr]['abmahnung'][$tat]['sum'] = getSumRow($zeilen[$persnr]['abmahnung'][$tat]);
	}
    }

    $zeilen[$persnr]['dzeit']['anw_prozent']['sum'] = $zeilen[$persnr]['dzeit']['astunden_fond']['sum']!=0?$zeilen[$persnr]['dzeit']['anwstd']['sum']/$zeilen[$persnr]['dzeit']['astunden_fond']['sum']*100:0;
    $zeilen[$persnr]['dzeit']['astunden_fond']['sum'] = getSumRow($zeilen[$persnr]['dzeit']['astunden_fond']);
    
    
    // spocitat sumy pro mesice
    $persATage = $a->getATageProPersnrBetweenDatums($persnr, $datumVon, $datumBis);
    $zeilen[$persnr]['dzeit']['von_bis_anw_nurarbtage']['sum'] = $persATage;
    $fondAnwProzent = $aTageFond!=0?$persATage/$aTageFond:0;
    
    $zeilen[$persnr]['dzeit']['von_bis_anw_nurarbtage']['sum'] = $persATage." / ".number_format($fondAnwProzent*100,0,',',' ')."%";
    $bezZCZK = 0;
    if($fondAnwProzent>=0.6 && $zeilen[$persnr]['dzeit']['z']['sum']==0){
	$bezZCZK = 2500;
    }
    $sumPremieCZK[$persnr]+=$bezZCZK;
    
    //$zeilen[$persnr]['dzeit']['z']['czk'] = number_format($bezZCZK,0,',',' ')."CZK";
    
    
    $zeilen[$persnr]['leistung']['vzaby_akkord']['sum'] = getSumRow($zeilen[$persnr]['leistung']['vzaby_akkord']);
    $zeilen[$persnr]['leistung']['vzaby_zeit']['sum'] = getSumRow($zeilen[$persnr]['leistung']['vzaby_zeit']);
    $zeilen[$persnr]['leistung']['ganzMonatNormMinuten']['sum'] = getSumRow($zeilen[$persnr]['leistung']['ganzMonatNormMinuten']);
    $zeilen[$persnr]['leistung']['monatNormMinuten']['sum'] = getSumRow($zeilen[$persnr]['leistung']['monatNormMinuten']);
    $citatel = $zeilen[$persnr]['leistung']['vzaby_akkord']['sum'] + $zeilen[$persnr]['leistung']['vzaby_zeit']['sum'];
    
    $zeilen[$persnr]['leistung']['leistGrad1']['sum'] = $zeilen[$persnr]['leistung']['monatNormMinuten']['sum']!=0?$citatel/$zeilen[$persnr]['leistung']['monatNormMinuten']['sum']*100:0;
    $zeilen[$persnr]['leistung']['leistGrad']['sum'] = $zeilen[$persnr]['dzeit']['anwstd']['sum']!=0?$citatel/($zeilen[$persnr]['dzeit']['anwstd']['sum']*60)*100:0;
    $zeilen[$persnr]['leistung']['leistGradGanzMonat']['sum'] = $zeilen[$persnr]['leistung']['ganzMonatNormMinuten']['sum']!=0?$citatel/$zeilen[$persnr]['leistung']['ganzMonatNormMinuten']['sum']*100:0;
    
    $zeilen[$persnr]['HF_repkosten']['repkosten']['sum'] = getSumRow($zeilen[$persnr]['HF_repkosten']['repkosten']);
    $zeilen[$persnr]['HF_repkosten']['vzkd_S0011']['sum'] = getSumRow($zeilen[$persnr]['HF_repkosten']['vzkd_S0011']);
    $zeilen[$persnr]['HF_repkosten']['vzkd_S0051']['sum'] = getSumRow($zeilen[$persnr]['HF_repkosten']['vzkd_S0051']);
    $zeilen[$persnr]['HF_repkosten']['vzkd_sum']['sum'] = getSumRow($zeilen[$persnr]['HF_repkosten']['vzkd_sum']);
    $zeilen[$persnr]['HF_repkosten']['faktor']['sum'] = $zeilen[$persnr]['HF_repkosten']['vzkd_sum']['sum']!=0?$zeilen[$persnr]['HF_repkosten']['repkosten']['sum']/$zeilen[$persnr]['HF_repkosten']['vzkd_sum']['sum']:'';
    
    //vyhodnoceni pomoci kriterii
    //$value = floatval(round($zeilen[$persnr]['HF_repkosten']['faktor']['sum'],2));
    //$yearMonth = substr($datumBis, 2, 5); // z 2015-12-31 vyberu 15-12
    //$bew = $a->getBewertungKriterium(100, 'q_reparaturen', $value, 'bis', $yearMonth, 12);
    //$zeilen[$persnr]['HF_repkosten']['bewertung']['sum'] = $bew;
    
    // celkova sum CZK pro osobu
    // nasobit koeficientem za loajalitu podle let
    $sumPremieCZK[$persnr] = $sumPremieCZK[$persnr] * $loajalitaPp_yearsCzk[$persnr];
    
// zformatovani hodnot v radku
    formatRowValues($zeilen[$persnr]['A6']['sum_gew'],0,',',' ');
    formatRowValues($zeilen[$persnr]['A6']['a6_gew'],0,',',' ');
    formatRowValues($zeilen[$persnr]['A6']['a6_prozent'],2,',',' ',TRUE);
    
    formatRowValues($zeilen[$persnr]['nacharbeit']['vzaby_65xx'],0,',',' ');
    formatRowValues($zeilen[$persnr]['nacharbeit']['vzkd'],0,',',' ');
    formatRowValues($zeilen[$persnr]['nacharbeit']['faktor'],2,',',' ',TRUE);
    
    
    formatRowValues($zeilen[$persnr]['dzeit']['anw_prozent'],2,',',' ',TRUE);
    
    formatRowValues($zeilen[$persnr]['leistung']['vzaby_akkord'],0,',',' ');
    formatRowValues($zeilen[$persnr]['leistung']['vzaby_zeit'],0,',',' ');
    formatRowValues($zeilen[$persnr]['leistung']['leistGrad'],0,',',' ');
    formatRowValues($zeilen[$persnr]['leistung']['leistGrad1'],0,',',' ');
    formatRowValues($zeilen[$persnr]['leistung']['leistGradGanzMonat'],0,',',' ');
    formatRowValues($zeilen[$persnr]['leistung']['leistGradGanzMonat'],0,',',' ');
    formatRowValues($zeilen[$persnr]['leistung']['ganzMonatNormMinuten'],0,',',' ');
    formatRowValues($zeilen[$persnr]['leistung']['monatNormMinuten'],0,',',' ');
    
    formatRowValues($zeilen[$persnr]['HF_repkosten']['repkosten'],0,',',' ',TRUE);
    formatRowValues($zeilen[$persnr]['HF_repkosten']['vzkd_S0011'],0,',',' ');
    formatRowValues($zeilen[$persnr]['HF_repkosten']['vzkd_S0051'],0,',',' ');
    formatRowValues($zeilen[$persnr]['HF_repkosten']['vzkd_sum'],0,',',' ');
    formatRowValues($zeilen[$persnr]['HF_repkosten']['faktor'],2,',',' ',TRUE);
    
    formatRowValues($zeilen[$persnr]['rekl']['sum_bewertung_I'],0,',',' ',TRUE);
    formatRowValues($zeilen[$persnr]['rekl']['sum_bewertung_E'],0,',',' ',TRUE);
    
    
}

$groups = array();
$groupDetails = array();

foreach ($persnrArray as $p) {
    $persnr = $p['persnr'];
    $rowsArray = $zeilen[$persnr];
    
    $nameA = $a->getNameVorname($persnr);
    $name = "";
    if ($nameA !== NULL) {
	$name = $nameA['name'] . ' ' . $nameA['vorname'];
    }
    $persInfoA = $a->getPersInfoArray($persnr);
    $regelOE = $persInfoA[0]['regeloe'];
    array_push($zeilenArray, array('section' => 'persheader','regeloe'=>$regelOE, 'persnr' => $persnr, 'name' => $name,'sumPremieCZK'=>  number_format($sumPremieCZK[$persnr],0,',',' ')));
    if (is_array($rowsArray)) {
	foreach ($rowsArray as $group => $groupArray) {
	    foreach ($groupArray as $groupDetail => $monthArray) {
		$groups[$group]+=1;
		$groupDetails[$groupDetail]+=1;
		array_push($zeilenArray, array('section' => 'groupdetail', 'regeloe'=>$regelOE,'persnr' => $persnr, 'name' => $name, 'group' => $group, 'groupDetail' => $groupDetail, 'monthValues' => $monthArray));
	    }
	}
    }
}

// vytahnout si pomocna pole

// grupy
$Groups = array_keys($groups);
sort($Groups);
// detaily
$GroupDetails = array_keys($groupDetails);
sort($GroupDetails);

$groupsInfoArray = array(
    'A6'=>array(
	'label'=>'Kvalita A6'
    ),
    'HF_repkosten'=>array(
	'label'=>'Reparaturkosten HF'
    ),
    'abmahnung'=>array(
	'label'=>'Abmahnungen'
    ),
    'dzeit'=>array(
	'label'=>'Anwesenheit'
    ),
    'leistung'=>array(
	'label'=>'Leistung'
    ),
    'loajalita'=>array(
	'label'=>'Loajalita'
    ),
    'rekl'=>array(
	'label'=>'Reklamace'
    ),
);
    
}



$returnArray = array(
    "groupDetails"=>$GroupDetails,
    "groups"=>$Groups,
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
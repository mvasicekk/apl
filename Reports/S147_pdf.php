<?php
require_once '../security.php';
require_once "../fns_dotazy.php";
require_once '../db.php';

$doc_title = "S147";
$doc_subject = "S147 Report";
$doc_keywords = "S147";

// necham si vygenerovat XML
$parameters=$_GET;

// vytahnu paramety z _GET ( z getparameters.php )
$parameters=$_GET;
$monat = $_GET['monat'];
$jahr = $_GET['jahr'];
$persvon = $_GET['persvon'];
$persbis = $_GET['persbis'];
$stammOE = strtoupper(strtr(trim($_GET['stammoe']),'*','%'));
$persVon = $persvon;
$persBis = $persbis;

$a = AplDB::getInstance();


$von = $jahr . "-" . $monat . "-01";
$pocetDnuVMesici = cal_days_in_month(CAL_GREGORIAN, $monat, $jahr);
$bis = $jahr . "-" . $monat . "-" . $pocetDnuVMesici;


$user = $_SESSION['user'];
$password = $_GET['password'];

$fullAccess = testReportPassword("S147",$password,$user,0);
if(!$fullAccess){
    echo "<h4>pristup nepovolen</h4>";
    exit();
}

$a->query('set names utf8');

// nechci zobrazit parametry
$params="";

$datumVon = $von;
$datumBis = $bis;

$monthsArrayAll = array();
// vytvorim si pole mesico podle zadaneho rozsahu von a bis
$start = strtotime($datumVon);
$end = strtotime($datumBis." 23:59:59");
$increment = 60 * 60 * 24; // 1 den
while($start<=$end){
    $year = date('y',$start);
    $month = date('m',$start);
    $yearMonth = "$year-$month";
    $monthsArrayAll[$yearMonth]+=1;
    $start+=$increment;
}



$monthsArray = array_keys($monthsArrayAll);

sort($monthsArray);
$mj = $monthsArray[0];


$sql="select dpers.PersNr as persnr from dpers";
$sql.=" where (PersNr between '$persVon' and '$persBis') and (austritt is null or austritt<eintritt or datediff(now(),austritt)<=60) and (dpersstatus='MA' or dpersstatus='BEENDET')";
$sql.=" and (kor=0)";

if((strlen($stammOE)>0) && ($stammOE!='%')){
    $sql.=" and dpers.regeloe like '%$stammOE%'";
}
$sql.=" order by dpers.persnr";

$persnrArray = $a->getQueryRows($sql);
if($persnrArray!==NULL){
foreach ($persnrArray as $p) {
    $persnr = $p['persnr'];
    
    //loajalita ----------------------------------------------------------------
    $persInfoA = $a->getPersInfoArray($persnr);
    $zeilen[$persnr]['name'] = $persInfoA[0]['Name'].' '.$persInfoA[0]['Vorname'];

    
    $zeilen[$persnr]['apremie_flag'] = $persInfoA[0]['a_praemie']!=0?$persInfoA[0]['a_praemie_st']!=0?'!':'V':'';

    $regeloe = $persInfoA[0]['regeloe'];
    $zeilen[$persnr]['regeloe'] = $persInfoA[0]['regeloe'];
    
    $eintritt = $a->getEintrittsDatumDB($persnr);
    $zeilen[$persnr]['loajalita']['eintritt']['sum'] = date('y-m-d',strtotime($eintritt));
    $zeilen[$persnr]['loajalita']['austritt']['sum'] = strlen(trim($persInfoA[0]['austritt']))==0?'':date('y-m-d',strtotime($persInfoA[0]['austritt']));
    $aTageFond = $a->getArbTageBetweenDatums($datumVon, $datumBis);
    $zeilen[$persnr]['loajalita']['von_bis_fond_days'] = $aTageFond;
    $zeilen[$persnr]['loajalita']['von_bis_fond_hours'] = $aTageFond*8;
    
    
    // nacharbeit ---------------------------------------------------------------
    
    $sql =" select";
    $sql.=" drueck.PersNr as persnr";
    $sql.=" ,drueck.Datum as datum";
    //2016-07-08
    //$sql.=" ,sum(if(TaetNr>=6500 and TaetNr<=6599,if(auss_typ=4,abs(drueck.`Stück`+`Auss-Stück`)*`VZ-IST`,abs(drueck.`Stück`)*`VZ-IST`),0)) as vzaby_65xx";
    $sql.=" ,sum(if(TaetNr>=6500 and TaetNr<=6599,if(auss_typ=4,(drueck.`Stück`+`Auss-Stück`)*`VZ-IST`,(drueck.`Stück`)*`VZ-IST`),0)) as vzaby_65xx";
    $sql.=" ,sum(if(auss_typ=4,(drueck.`Stück`+`Auss-Stück`)*`VZ-SOLL`,(drueck.`Stück`)*`VZ-SOLL`)) as vzkd";
    $sql.=" from";
    $sql.=" drueck";
    $sql.=" where";
    $sql.=" PersNr='$persnr'";
    $sql.=" and Datum between '$datumVon' and '$datumBis'";
    $sql.=" group by";
    $sql.=" PersNr,";
    $sql.=" drueck.Datum";
    
//    AplDB::varDump($sql);
    
    $persRows = $a->getQueryRows($sql);

//    AplDB::varDump($persRows);
    
    $monthsArray = array();
    if ($persRows !== NULL) {
	foreach ($persRows as $pr) {
	    //$persnr = $pr['persnr'];
	    $datum = $pr['datum'];
	    $month = date('m', strtotime($datum));
	    $yearMonth = date('y-m', strtotime($datum));
	    $monthsArray[$yearMonth]+=1;
	    $vzaby_65xx = abs(floatval($pr['vzaby_65xx']));
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
	
	//bewertung czk
	$value = $zeilen[$persnr]['nacharbeit']['faktor'][$mj];
	//echo "value: $value<br>";
	$bew = $a->getBewertungKriteriumArray(100,'q_nacharbeit',$value,'bis',$mj,1,$regeloe);
	//AplDB::varDump($bew);
	if($bew==NULL){
	    $zeilen[$persnr]['nacharbeit']['faktor']['czk']='';
	}
	else{
	    $zeilen[$persnr]['nacharbeit']['faktor']['czk']=$bew['betrag'];
	}
    }
    //--------------------------------------------------------------------------
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
	//bewertung czk
	$value = $zeilen[$persnr]['A6']['a6_prozent'][$mj];
	//echo "value: $value<br>";
	$bew = $a->getBewertungKriteriumArray(100,'q_auss',$value,'bis',$mj,1,$regeloe);
	//AplDB::varDump($bew);
	if($bew==NULL){
	    $zeilen[$persnr]['A6']['a6_prozent']['czk']='';
	}
	else{
	    $zeilen[$persnr]['A6']['a6_prozent']['czk']=$bew['betrag'];
	}
    }
    //--------------------------------------------------------------------------
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
    
    //bewertung czk
	$value = $zeilen[$persnr]['rekl']['sum_bewertung_I'][$mj];
	$bew = $a->getBewertungKriteriumArray(100,'q_reklamationen_I',$value,'bis',$mj,1,$regeloe);
	//AplDB::varDump($bew);
	if($bew==NULL){
	    $zeilen[$persnr]['rekl']['sum_bewertung_I']['czk']='';
	}
	else{
	    $zeilen[$persnr]['rekl']['sum_bewertung_I']['czk']=$bew['betrag'];
	}
	
	$value = $zeilen[$persnr]['rekl']['sum_bewertung_E'][$mj];
	$bew = $a->getBewertungKriteriumArray(100,'q_reklamationen_E',$value,'bis',$mj,1,$regeloe);
	if($bew==NULL){
	    $zeilen[$persnr]['rekl']['sum_bewertung_E']['czk']='';
	}
	else{
	    $zeilen[$persnr]['rekl']['sum_bewertung_E']['czk']=$bew['betrag'];
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
	    
	    if($pr['tat']=='d' || $pr['tat']=='n' || $pr['tat']=='np'|| $pr['tat']=='nu'|| $pr['tat']=='nv'|| $pr['tat']=='nw'|| $pr['tat']=='p'|| $pr['tat']=='u'|| $pr['tat']=='z'|| $pr['tat']=='?'){
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
	    $vzaby_zeit = ($vzaby-$vzaby_akkord);
	    //$vzaby_zeit = ($vzaby-$vzaby_akkord)*$leistFaktor;
	}
	else{
	    $vzaby = 0;
	    $vzaby_akkord = 0;
	    $vzaby_zeit = 0;
	}
	$zeilen[$persnr]['leistung']['leistGrad'][$yearMonth] = $zeilen[$persnr]['dzeit']['anwstd'][$yearMonth]!=0?($vzaby_akkord+$vzaby_zeit)/($zeilen[$persnr]['dzeit']['anwstd'][$yearMonth]*60)*100:0;
    }

    //ko_kriteria
    $koKriteriaArray[$persnr] = array();
    $value = $zeilen[$persnr]['dzeit']['z'][$mj];
    $bew = $a->getBewertungKriteriumArray(100,'ko_dzeit_z',$value,'bis',$mj,1,'abcd');
    if($bew===NULL){
	$koKriteriaArray[$persnr]['ko_dzeit_z']['multi'] = 0;
    }
    else{
	$koKriteriaArray[$persnr]['ko_dzeit_z']['multi'] = $bew['betrag'];
    }
    
    $value = $zeilen[$persnr]['dzeit']['anw_prozent'][$mj];
    //AplDB::varDump($value);
    $bew = $a->getBewertungKriteriumArray(100,'ko_dzeit_anw_prozent',$value,'von',$mj,1,'abcd');
    if($bew===NULL){
	$koKriteriaArray[$persnr]['ko_dzeit_anw_prozent']['multi'] = 0;
    }
    else{
	$koKriteriaArray[$persnr]['ko_dzeit_anw_prozent']['multi'] = $bew['betrag'];
    }
    
    //ko_a50
    $value = $zeilen[$persnr]['A6']['a6_prozent'][$mj];
    //AplDB::varDump($value);
    $bew = $a->getBewertungKriteriumArray(100,'ko_a50',$value,'bis',$mj,1,'abcd');
    if($bew===NULL){
	$koKriteriaArray[$persnr]['ko_a50']['multi'] = 0;
    }
    else{
	$koKriteriaArray[$persnr]['ko_a50']['multi'] = $bew['betrag'];
    }
    
    //sum_bewertung_E
    $value = $zeilen[$persnr]['rekl']['sum_bewertung_E'][$mj];
    //AplDB::varDump($value);
    $bew = $a->getBewertungKriteriumArray(100,'ko_rekl_E',$value,'bis',$mj,1,'abcd');
    if($bew===NULL){
	$koKriteriaArray[$persnr]['ko_rekl_E']['multi'] = 0;
    }
    else{
	$koKriteriaArray[$persnr]['ko_rekl_E']['multi'] = $bew['betrag'];
    }
    
}
}

//AplDB::varDump($zeilen);
//exit();

$persnrWidth = 13;
$nameWidth = 50;
$apremieFlagWidth = 10;
$regelOEWidth = 12;
$eintrittWidth = 17;
$anwWidth = 15;
$tatWidth = 7;
$leistgradWidth = 12;
$a6Width = 12;
$naWidth = 12;
$reklWidth = 12;
$apremieCZKWidth = 0;

$tatArray = array("d","n","np","nu","nv","nw","p","u","z","?");


function test_pageoverflow($pdf,$vyskaradku)
{
	// pokud bych prelezl s nasledujicim vystupem vysku stranky
	// tak vytvorim novou stranku i se zahlavim
	if(($pdf->GetY()+$vyskaradku)>($pdf->getPageHeight()-$pdf->getBreakMargin()))
	{
		return TRUE;
	}
	else{
	    return FALSE;
	}
}

/**
 * 
 * @param TCPDF $pdf
 */
function pageHeader($pdf){
    global $persnrWidth,$nameWidth,$apremieFlagWidth,$regelOEWidth,$eintrittWidth,$anwWidth,$tatWidth,$leistgradWidth,$a6Width,$naWidth,$reklWidth,$apremieCZKWidth;
    global $tatArray;
    $headerHeight = 10;
    $fill = TRUE;
    $pdf->SetFillColor(230,230,255);
    $pdf->MultiCell($persnrWidth, $headerHeight, "PersNr.", 'LRBT', 'R', $fill, FALSE, '', '',TRUE,0,FALSE,FALSE,$headerHeight,'M');
    $pdf->MultiCell($nameWidth, $headerHeight, "Name Vorname", 'LRBT', 'L', $fill, FALSE, '', '',TRUE,0,FALSE,FALSE,$headerHeight,'M');
    $pdf->MultiCell($apremieFlagWidth, $headerHeight, "A\nPr", 'LRBT', 'C', $fill, FALSE, '', '',TRUE,0,FALSE,FALSE,$headerHeight,'M');
    $pdf->MultiCell($regelOEWidth, $headerHeight, "Regel\nOE", 'LRBT', 'L', $fill, FALSE, '', '',TRUE,0,FALSE,FALSE,$headerHeight,'M');
    $pdf->MultiCell($eintrittWidth, $headerHeight, "Eintritt\nAustritt", 'LRBT', 'L', $fill, FALSE, '', '',TRUE,0,FALSE,FALSE,$headerHeight,'M');
    $pdf->MultiCell($anwWidth, $headerHeight, "Anw[%]\nAT*8=100", 'LRBT', 'R', $fill, FALSE, '', '',TRUE,0,FALSE,FALSE,$headerHeight,'M');
    foreach ($tatArray as $tat){
	$pdf->MultiCell($tatWidth, $headerHeight, "$tat", 'LRBT', 'C', $fill, FALSE, '', '',TRUE,0,FALSE,FALSE,$headerHeight,'M');
    }
    
    $pdf->MultiCell($leistgradWidth, $headerHeight, "vzaby/\nAnw", 'LRBT', 'R', $fill, FALSE, '', '',TRUE,0,FALSE,FALSE,$headerHeight,'M',TRUE);
    
    $pdf->MultiCell($a6Width, $headerHeight, "A6\nCZK", 'LRBT', 'R', $fill, FALSE, '', '',TRUE,0,FALSE,FALSE,$headerHeight,'M');
    $pdf->MultiCell($naWidth, $headerHeight, "NA\nCZK", 'LRBT', 'R', $fill, FALSE, '', '',TRUE,0,FALSE,FALSE,$headerHeight,'M');
    $pdf->MultiCell($reklWidth, $headerHeight, "ReklI\nCZK", 'LRBT', 'R', $fill, FALSE, '', '',TRUE,0,FALSE,FALSE,$headerHeight,'M');
    $pdf->MultiCell($reklWidth, $headerHeight, "ReklE\nCZK", 'LRBT', 'R', $fill, FALSE, '', '',TRUE,0,FALSE,FALSE,$headerHeight,'M');
    
    $pdf->MultiCell($apremieCZKWidth, $headerHeight, "A-Premie", 'LRBT', 'R', $fill, FALSE, '', '',TRUE,0,FALSE,FALSE,$headerHeight,'M');
    
    $pdf->Ln();
}

require_once('../tcpdf_new/tcpdf.php');

$pdf = new TCPDF('L','mm','A4',1);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(PDF_AUTHOR);
$pdf->SetTitle($doc_title);
$pdf->SetSubject($doc_subject);
$pdf->SetKeywords($doc_keywords);

$params = "Pers $persvon - $persbis, MJ: $mj, OE:".$_GET['stammoe']."";
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "S147 A-Praemie ( Detail - Monat )", $params);
//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP-10, PDF_MARGIN_RIGHT);
//set auto page breaks
//$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO); //set image scale factor

//$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setHeaderFont(Array("FreeSans", '', 9));
$pdf->setFooterFont(Array("FreeSans", '', 8));

$pdf->setLanguageArray($l); //set language items

//initialize document
//$pdf->AliasNbPages();
$pdf->SetFont("FreeSans", "", 8);
$pdf->SetLineWidth(0.1);
// prvni stranka
$pdf->AddPage();

pageheader($pdf);

$persHeight = 10;

/*
MultiCell  This method allows printing text with line breaks. They can be automatic (as soon as the text reaches the right border of the cell) or explicit (via the \n character). As many cells as necessary are output, one below the other. Text can be aligned, centered or justified. The cell block can be framed and the background painted. 
Parameters:
 

$w
(float) Width of cells. If 0, they extend up to the right margin of the page.
 

$h
(float) Cell minimum height. The cell extends automatically if needed.
 

$txt
(string) String to print
 

$border
(mixed) Indicates if borders must be drawn around the cell. The value can be a number:
0: no border (default)
1: frame
or a string containing some or all of the following characters (in any order):
L: left
T: top
R: right
B: bottom
or an array of line styles for each border group - for example: array('LTRB' => array('width' => 2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)))
 

$align
(string) Allows to center or align the text. Possible values are:
L or empty string: left align
C: center
R: right align
J: justification (default value when $ishtml=false)
 

$fill
(boolean) Indicates if the cell background must be painted (true) or transparent (false).
 

$ln
(int) Indicates where the current position should go after the call. Possible values are:
0: to the right
1: to the beginning of the next line [DEFAULT]
2: below
 

$x
(float) x position in user units
 

$y
(float) y position in user units
 

$reseth
(boolean) if true reset the last cell height (default true).
 

$stretch
(int) font stretch mode:
0 = disabled
1 = horizontal scaling only if text is larger than cell width
2 = forced horizontal scaling to fit cell width
3 = character spacing only if text is larger than cell width
4 = forced character spacing to fit cell width
General font stretching and scaling values will be preserved when possible.
 

$ishtml
(boolean) INTERNAL USE ONLY -- set to true if $txt is HTML content (default = false). Never set this parameter to true, use instead writeHTMLCell() or writeHTML() methods.
 

$autopadding
(boolean) if true, uses internal padding and automatically adjust it to account for line width.
 

$maxh
(float) maximum height. It should be >= $h and less then remaining space to the bottom of the page, or 0 for disable this feature. This feature works only when $ishtml=false.
 

$valign
(string) Vertical alignment of text (requires $maxh = $h > 0). Possible values are:
T: TOP
M: middle
B: bottom
. This feature works only when $ishtml=false and the cell must fit in a single page.
 

$fitcell
(boolean) if true attempt to fit all the text within the cell by reducing the font size (do not work in HTML mode). $maxh must be greater than 0 and wqual to $h.
Returns:
Type:
int
Description:
Return the number of cells or 1 for html mode.
 * 
 */

$gesammtSummePremie = 0;
foreach ($zeilen as $persnr=>$persZeile){
    $sumPremie = 0;
    
    if($persZeile['apremie_flag']=='!'){
	$fill = TRUE;
	$pdf->SetFillColor(255,255,230);
    }
    else{
	$fill = FALSE;
    }
    $pdf->MultiCell($persnrWidth, $persHeight, $persnr, 'LRBT', 'R', $fill, FALSE, '','',TRUE,0,FALSE,FALSE,$persHeight,'M');

    $name = $persZeile['name'];
    $pdf->MultiCell($nameWidth, $persHeight, $name, 'LRBT', 'L', $fill, FALSE, '','',TRUE,0,FALSE,FALSE,$persHeight,'M');
    
    $pdf->MultiCell($apremieFlagWidth, $persHeight, $persZeile['apremie_flag'], 'LRBT', 'C', $fill, FALSE, '','',TRUE,0,FALSE,FALSE,$persHeight,'M');
    
    $pdf->MultiCell($regelOEWidth, $persHeight, $persZeile['regeloe'], 'LRBT', 'L', $fill, FALSE, '','',TRUE,0,FALSE,FALSE,$persHeight,'M');
    
    $austr = $persZeile['loajalita']['austritt']['sum']==''?' ':$persZeile['loajalita']['austritt']['sum'];
    $pdf->MultiCell($eintrittWidth, $persHeight, 
	    $persZeile['loajalita']['eintritt']['sum']."\n".$austr, 
	    'LRBT', 'L', $fill, FALSE, '','',TRUE,0,FALSE,FALSE,$persHeight,'M');

    // anw_prozent
    if($koKriteriaArray[$persnr]['ko_dzeit_anw_prozent']['multi']==0){
	$fill = 1;
	$pdf->SetFillColor(255,230,230);
    }
    $pdf->MultiCell($anwWidth, $persHeight, number_format(floatval($persZeile['dzeit']['anw_prozent'][$mj]),2,',',' '), 'LRBT', 'R', $fill, FALSE, '','',TRUE,0,FALSE,FALSE,$persHeight,'M');
    $fill = 0;
    $pdf->SetFillColor(255,255,230);
	

    if($persZeile['apremie_flag']=='!'){
	$fill = TRUE;
	$pdf->SetFillColor(255,255,230);
    }
    else{
	$fill = FALSE;
    }
    // tatigkeiten
    foreach ($tatArray as $tat){
	$t = $persZeile['dzeit'][$tat][$mj]!=0?$persZeile['dzeit'][$tat][$mj]:'';
	if($tat=='z'){
	    //test na ko_kriterium
	    if($koKriteriaArray[$persnr]['ko_dzeit_z']['multi'] == 0){
		$fill = 1;
		$pdf->SetFillColor(255,230,230);
	    }
	}
	$pdf->MultiCell($tatWidth, $persHeight, $t, 'LRBT', 'C', $fill, FALSE, '','',TRUE,0,FALSE,FALSE,$persHeight,'M');
	$fill = 0;
	$pdf->SetFillColor(255,255,230);
	if($persZeile['apremie_flag']=='!'){
	    $fill = TRUE;
	    $pdf->SetFillColor(255,255,230);
	}
	else{
	    $fill = FALSE;
	}
    }
    
    if($persZeile['apremie_flag']=='!'){
	$fill = TRUE;
	$pdf->SetFillColor(255,255,230);
    }
    else{
	$fill = FALSE;
    }
    
    //leistgrad
    $pdf->MultiCell($leistgradWidth, $persHeight, 
	    //number_format(floatval($persZeile['leistung']['vzaby_akkord'][$mj])+floatval($persZeile['leistung']['vzaby_zeit'][$mj]),0,',',' ')."\n"
	    //.number_format(floatval($persZeile['dzeit']['anwstd'][$mj])*60,0,',',' ')."\n"
	    number_format(floatval($persZeile['leistung']['leistGrad'][$mj]/100),2,',',' ')
	    ,'LRBT', 'R', $fill, FALSE, '','',TRUE,0,FALSE,FALSE,$persHeight,'M');

    //a6
    if($koKriteriaArray[$persnr]['ko_a50']['multi']==0){
	$fill = 1;
	$pdf->SetFillColor(255,230,230);
    }
    $pdf->MultiCell($leistgradWidth, $persHeight, 
	    number_format(floatval($persZeile['A6']['a6_prozent'][$mj]),2,',',' ')."\n"
	    .number_format(floatval($persZeile['A6']['a6_prozent']['czk']),0,',',' '), 
	    'LRBT', 'R', $fill, FALSE, '','',TRUE,0,FALSE,FALSE,$persHeight,'M');
    $sumPremie += floatval($persZeile['A6']['a6_prozent']['czk']);
    $fill = 0;
    $pdf->SetFillColor(255,255,230);

    if($persZeile['apremie_flag']=='!'){
	$fill = TRUE;
	$pdf->SetFillColor(255,255,230);
    }
    else{
	$fill = FALSE;
    }
    $pdf->MultiCell($naWidth, $persHeight, 
	    number_format(floatval($persZeile['nacharbeit']['faktor'][$mj]),2,',',' ')."\n"
	    .number_format(floatval($persZeile['nacharbeit']['faktor']['czk']),0,',',' '), 
	    'LRBT', 'R', $fill, FALSE, '','',TRUE,0,FALSE,FALSE,$persHeight,'M');
    $sumPremie += floatval($persZeile['nacharbeit']['faktor']['czk']);

    
    $pdf->MultiCell($reklWidth, $persHeight, 
	    number_format(floatval($persZeile['rekl']['sum_bewertung_I'][$mj]),0,',',' ')."\n"
	    .number_format(floatval($persZeile['rekl']['sum_bewertung_I']['czk']),0,',',' ') 
	    ,'LRBT', 'R', $fill, FALSE, '','',TRUE,0,FALSE,FALSE,$persHeight,'M');
    $sumPremie += floatval($persZeile['rekl']['sum_bewertung_I']['czk']);

    //rekl_E
    if($koKriteriaArray[$persnr]['ko_rekl_E']['multi']==0){
	$fill = 1;
	$pdf->SetFillColor(255,230,230);
    }
    $pdf->MultiCell($reklWidth, $persHeight, 
	    number_format(floatval($persZeile['rekl']['sum_bewertung_E'][$mj]),0,',',' ')."\n"
	    .number_format(floatval($persZeile['rekl']['sum_bewertung_E']['czk']),0,',',' ') 
	    ,'LRBT', 'R', $fill, FALSE, '','',TRUE,0,FALSE,FALSE,$persHeight,'M');
    $sumPremie += floatval($persZeile['rekl']['sum_bewertung_E']['czk']);
    $fill = 0;
    $pdf->SetFillColor(255,255,230);

    if($persZeile['apremie_flag']=='!'){
	$fill = TRUE;
	$pdf->SetFillColor(255,255,230);
    }
    else{
	$fill = FALSE;
    }
    $sumPremie = round(floatval($persZeile['leistung']['leistGrad'][$mj]/100),2) * $sumPremie;
    $sumPremie *= floatval($koKriteriaArray[$persnr]['ko_dzeit_z']['multi']);
    $sumPremie *= floatval($koKriteriaArray[$persnr]['ko_dzeit_anw_prozent']['multi']);
    $sumPremie *= floatval($koKriteriaArray[$persnr]['ko_a50']['multi']);
    $sumPremie *= floatval($koKriteriaArray[$persnr]['ko_rekl_E']['multi']);
    
    if($persZeile['apremie_flag']==''){
	$sumPremie = 0;
    }
    
    $obsah = number_format(floatval($sumPremie),0,',',' ');
    $pdf->MultiCell($apremieCZKWidth, $persHeight, $obsah, 'LRBT', 'R', $fill, FALSE, '','',TRUE,0,FALSE,FALSE,$persHeight,'M');
    
    $pdf->Ln();
    
    $gesammtSummePremie += $sumPremie;
    
    if(test_pageoverflow($pdf, $persHeight)){
	$pdf->AddPage();
	pageheader($pdf);
    }
}


    $pdf->SetFillColor(230,255,230);
    $fill=TRUE;
    $pdf->MultiCell($persnrWidth, $persHeight, "Summe", 'LBT', 'L', $fill, FALSE, '','',TRUE,0,FALSE,FALSE,$persHeight,'M');
    $pdf->MultiCell(
	$nameWidth+$apremieFlagWidth+$regelOEWidth
	+$eintrittWidth+$anwWidth+($tatWidth*count($tatArray))
	+$leistgradWidth*2
	+$naWidth
	+$reklWidth*2
	, $persHeight
	, ''
	, 'BT', 'L', $fill, FALSE, '','',TRUE,0,FALSE,FALSE,$persHeight,'M');

    
    $obsah = number_format(floatval($gesammtSummePremie),0,',',' ');
    $pdf->MultiCell($apremieCZKWidth, $persHeight, $obsah, 'LRBT', 'R', $fill, FALSE, '','',TRUE,0,FALSE,FALSE,$persHeight,'M');

    
//Close and output PDF document
$pdf->Output();

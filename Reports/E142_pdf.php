<?php
session_start();
require_once "../fns_dotazy.php";
require_once '../db.php';



$doc_title = "S142";
$doc_subject = "S142 Report";
$doc_keywords = "S142";

// necham si vygenerovat XML

$parameters=$_GET;
$monat = $_GET['monat'];
$jahr = $_GET['jahr'];
$persvon = $_GET['persvon'];
$persbis = $_GET['persbis'];
$reporttyp = $_GET['reporttyp'];
$a = AplDB::getInstance();

if ($reporttyp == 'infoVonBis') {
    $von = $a->make_DB_datum($_GET['von']);
    $bis = $a->make_DB_datum($_GET['bis']);
} else {
    $von = $jahr . "-" . $monat . "-01";
    $pocetDnuVMesici = cal_days_in_month(CAL_GREGORIAN, $monat, $jahr);
    $bis = $jahr . "-" . $monat . "-" . $pocetDnuVMesici;
}


$user = $_SESSION['user'];
$password = $_GET['password'];

$fullAccess = testReportPassword("S142",$password,$user,0);

if((!$fullAccess) && ($reporttyp=='lohn'))
{
    echo "Nemate povoleno zobrazeni teto sestavy / Sie sind nicht berechtigt dieses Report zu starten.";
    exit;
}


require_once('S142_xml.php');

//exit;
// vytvorit string s popisem parametru
// parametry mam v XML souboru, tak je jen vytahnu
$parameters=$domxml->getElementsByTagName("parameters");


foreach ($parameters as $param)
{
	$parametry=$param->childNodes;
	// v ramci parametru si prectu label a hodnotu
	foreach($parametry as $parametr)
	{
		$parametr=$parametr->childNodes;
		foreach($parametr as $par)
		{
			if($par->nodeName=="label")
				$label=$par->nodeValue;
			if($par->nodeName=="value")
				$value=$par->nodeValue;
		}
            if(strtolower($label)!="password")
            $params .= $label.": ".$value."  ";
	}
}

$columnsArray = array(
    "persnr"=>array("colname"=>"persnr"),
    "at"=>array("colname"=>"mzd_1"),
    "z"=>array("colname"=>"mzd_13"),
    "d"=>array("colname"=>"mzd_9"),
    "nachtstd"=>array("colname"=>"mzd_8"),
    "sostd"=>array("colname"=>"mzd_7"),
    "svatky"=>array("colname"=>"mzd_11"),
    "anw_std_z"=>array("colname"=>"mzd_2"),
    "anw_std_a"=>array("colname"=>"mzd_4"),
    "lohn_kc_z"=>array("colname"=>"mzd_16"),
    "lohn_kc_a"=>array("colname"=>"mzd_17"),
    "prem_q_celk"=>array("colname"=>"mzd_21"),
    "prem_leist_celk"=>array("colname"=>"mzd_22"),
    "prem_quart_celk"=>array("colname"=>"mzd_23"),
    "erschw_celk"=>array("colname"=>"mzd_24"),
    "transport"=>array("colname"=>"mzd_51"),
    "vorschuss"=>array("colname"=>"mzd_46"),
    "essen"=>array("colname"=>"mzd_48"),
);

$headerCells = array(
    'persnr'=>array(
        'width'=>7.5,
        'text'=>'PersNr',
        'align'=>'',
    ),
    'name'=>array(
        'width'=>25,
        'text'=>'Name Vorname',
        'align'=>'',
     ),
    'stdlohn'=>array(
        'width'=>9,
        'text'=>'Std.Lohn\nL.Faktor\nOE',
        'align'=>'',
    ),
    'eintrittAustritt'=>array(
        'width'=>10,
        'text'=>'eintritt\naustritt',
        'align'=>'',
    ),
    'befristetProbezeit'=>array(
        'width'=>10,
        'text'=>'befristet\nProbezeit',
        'align'=>'',

    ),
    'a'=>array(
        'width'=>6,
        'text'=>'a',
        'align'=>'',

    ),
//    'aNAT'=>array(
//        'width'=>5,
//        'text'=>'a n AT',
//        'align'=>'',
//
//    ),
    'd'=>array(
        'width'=>5,
        'text'=>'d',
        'align'=>'',

    ),
    'n'=>array(
        'width'=>5,
        'text'=>'n',
        'align'=>'',

    ),
    'np'=>array(
        'width'=>4,
        'text'=>'np',
        'align'=>'',

    ),
    'nv'=>array(
        'width'=>4,
        'text'=>'nv',
        'align'=>'',

    ),
    'nw'=>array(
        'width'=>4,
        'text'=>'nw',
        'align'=>'',

    ),
    'nu'=>array(
        'width'=>4,
        'text'=>'nu',
        'align'=>'',

    ),
    'p'=>array(
        'width'=>4,
        'text'=>'p',
        'align'=>'',

    ),
    'u'=>array(
        'width'=>4,
        'text'=>'u',
        'align'=>'',

    ),
    'z'=>array(
        'width'=>4,
        'text'=>'z',
        'align'=>'',

    ),
    'frage'=>array(
        'width'=>4,
        'text'=>'?',
        'align'=>'',

    ),
    'mehrarbeit'=>array(
        'width'=>8,
        'text'=>'+-Std',
        'align'=>'',
     ),
    'nachtstd'=>array(
        'width'=>7,
        'text'=>'Nacht',
        'align'=>'',
    ),
    'sonestd'=>array(
        'width'=>7,
        'text'=>'Nacht',
        'align'=>'',
    ),
    'vjRst'=>array(
        'width'=>6,
        'text'=>'VJrst',
        'align'=>'',

    ),
    'jAnsp'=>array(
        'width'=>6.5,
        'text'=>'JAns',
        'align'=>'',

    ),
    'kor'=>array(
        'width'=>6,
        'text'=>'kor',
        'align'=>'',

    ),

    'genommen'=>array(
        'width'=>6,
        'text'=>'gen',
        'align'=>'',

    ),
    'offen'=>array(
        'width'=>6.5,
        'text'=>'offen',
        'align'=>'',

    ),
    'transport'=>array(
        'width'=>6,
        'text'=>'Trans\nCZK',
    ),
    'vorschuss'=>array(
        'width'=>7,
        'text'=>'Vorsch\nCZK',
        'align'=>'',

    ),
    'abmahnung'=>array(
        'width'=>7,
        'text'=>'Vorsch\nCZK',
        'align'=>'',

    ),
    'essen'=>array(
        'width'=>7,
        'text'=>'Essen\nCZK',
        'align'=>'',

    ),

    'exekution'=>array(
        'width'=>0,
        'text'=>'',
        'align'=>'',

    ),

    'dummy1'=>array(
        'width'=>8,
        'text'=>'',
        'align'=>'',

    ),
    'factoren'=>array(
        'width'=>7,
        'text'=>'',
        'align'=>'',

    ),
    'anwesenheit'=>array(
        'width'=>9,
        'text'=>'Anw\nStd',
        'align'=>'',

    ),
    'leistungmin'=>array(
        'width'=>10,
        'text'=>'Leistung\nmin',
        'align'=>'',

    ),
    'leistungkc'=>array(
        'width'=>10,
        'text'=>'Leistung\nCZK',
        'align'=>'',

    ),
    'qpraemie'=>array(
        'width'=>8,
        'text'=>'Qualitaet\nCZK',
        'align'=>'',

    ),
    'leistungpraemie'=>array(
        'width'=>8,
        'text'=>'Leistung\nCZK',
        'align'=>'',

    ),
    'quartalpraemie'=>array(
        'width'=>8,
        'text'=>'Quartal\nCZK',
        'align'=>'',

    ),
    'sonstpremie'=>array(
        'width'=>8,
        'text'=>'Sonst\nCZK',
        'align'=>'',

    ),
    'erschwerniss'=>array(
        'width'=>8,
        'text'=>'Erschw.\nCZK',
        'align'=>'',

    ),
    'lohn'=>array(
        'width'=>10,
        'text'=>'Lohn\nCZK',
        'align'=>'',

    ),
);

$aTageProMonat = 0;
$stundenDecimals = 1;

function showNoNull($hodnota){
    if($hodnota==0)
        return '';
    else
        return $hodnota;
}




/**
 *
 * @param SimpleXMLElement $pole
 * @param string $og
 * @param string $datum
 * @param string $field 
 */
function getMinuten($pole,$og,$datum,$field){
    //najedu si na zvolene datum
    foreach($pole->person->tage->tag as $tag){
        $date = trim($tag->datum);
//        echo "<br>date=$date ";
        if(!strcmp($date, $datum)){
            // nasel jsem datum
//            echo "$date==$datum, pokacuju na hledani og ";
            foreach($tag->ogs->og as $ogcko){
                $ognr = trim($ogcko->ognr);
//                echo "ognr=$ognr ";
                if(!strcmp($ogcko->ognr,$og)){
//                    echo "$ognr==$og , vracim hodnotu";
                    return floatval($ogcko->{$field});
                }
            }
        }
    }
    return 0;
}


/**
 *
 * @global int $aTageProMonat
 * @global array $headerCells
 * @global type $summenZapati
 * @global int $stundenDecimals
 * @global type $reporttyp
 * @param type $e
 * @param type $vyskaradku
 * @param type $rgb
 * @param type $person
 * @param type $monat
 * @param type $jahr 
 */
function radek_personE($objPHPExcel, $vyskaradku, $rgb, $person, $monat, $jahr) {
    global $aTageProMonat;
    global $headerCells;
    global $summenZapati;
    global $stundenDecimals;
    global $reporttyp;
    global $radek,$sloupec;
    global $columnsArray;

    $fill = 0;

    $a = AplDB::getInstance();

    if($reporttyp=='infoVonBis'){
        $von = $a->make_DB_datum($_GET['von']);
        $pocetDnu = cal_days_in_month(CAL_GREGORIAN, $monat, $jahr);
        $bis = $a->make_DB_datum($_GET['bis']);
    }
    else{
        $von = $jahr . "-" . $monat . "-01";
        $pocetDnuVMesici = cal_days_in_month(CAL_GREGORIAN, $monat, $jahr);
        $bis = $jahr . "-" . $monat . "-" . $pocetDnuVMesici;
    }
    $vorjahr = $jahr;
    $vormonat = $monat - 1;
    if ($vormonat == 0) {
        $vormonat = 12;
        $vorjahr--;
    }
    $pocetDnuVorMonat = cal_days_in_month(CAL_GREGORIAN, $vormonat, $vorjahr);

    $datumEndeMonat = sprintf("%04d-%02d-%02d", $jahr, $monat, $pocetDnuVMesici);
    $datumEndeVorMonat = sprintf("%04d-%02d-%02d", $vorjahr, $vormonat, $pocetDnuVorMonat);

    
//    $pdf->SetFont("FreeSans", "", 6);
    $eintritt = trim($person->eintritt);
    $persnr = trim($person->persnr);

    $aplDB = new AplDB();

    $pocetSvatkuVMesici = $aplDB->getSvatekCount($jahr,$monat);
    
    $persLohnFaktor = floatval(trim($person->perslohnfaktor));
    $leistFaktor = floatval(trim($person->leistfaktor));

    $faktorenInfo = sprintf("Std.Lohn: %.02f, Leistfaktor: %.02f", $persLohnFaktor * 60, $leistFaktor);

    $stundenA = floatval(trim($person->sumstundena));
    $stundenAkkord = floatval(trim($person->sumstundena_akkord));
    $erschwerniss = intval(trim($person->risiko));

    $essen = intval(trim($person->essen));
    $exekution = intval(trim($person->exekution));
    $vorschuss = intval(trim($person->vorschuss));
    $transport = intval(trim($person->transport));
    $sonstpremie = intval(trim($person->sonstpremie));
    $abmahnung = intval(trim($person->abmahnung));

    $dpp_von = trim($person->dpp_von);
    $dpp_bis = trim($person->dpp_bis);
    
    $hodCasove = $stundenA - $stundenAkkord;
    $hodUkolove = $stundenAkkord;

    $bErschwerniss = intval(trim($person->premie_za_prasnost)) != 0 ? TRUE : FALSE;
    $bleistungsPraemie = intval(trim($person->premie_za_vykon)) != 0 ? TRUE : FALSE;
    $bQPraemie_akkord = intval(trim($person->qpremie_akkord)) != 0 ? TRUE : FALSE;
    $bQPraemie_zeit = intval(trim($person->qpremie_zeit)) != 0 ? TRUE : FALSE;
    $bQTLPraemie = intval(trim($person->premie_za_3_mesice)) != 0 ? TRUE : FALSE;
    $bMAStunden = intval(trim($person->MAStunden)) != 0 ? TRUE : FALSE;

    $erschwerniss = $bErschwerniss===TRUE?$erschwerniss:0;
    
    $d = intval(trim($person->tage_d));
    $p = intval(trim($person->tage_p));
    $z = intval(trim($person->tage_z));
    $nv = intval(trim($person->tage_nv));
    $nw = intval(trim($person->tage_nw));
    $nachtstd = round(floatval(trim($person->nachtstd)), 1);

    // pokud ma nejake z-tky, ovlivni to jeho narok na premii za kvalitu
    if ($z > 0) {
        $bQPraemie_akkord = FALSE;
        $bQPraemie_zeit = FALSE;
    }

    $persnr = $person->persnr;

    $vonTimestamp = strtotime($von);
    $eintrittTimestamp = strtotime($eintritt);
    if ($eintrittTimestamp > $vonTimestamp)
        $arbTage = $aplDB->getArbTageBetweenDatums($eintritt, $bis);
    else
        $arbTage = $aplDB->getArbTageBetweenDatums($von, $bis);

    $anwTage = $aplDB->getATageProPersnrBetweenDatums($persnr, $von, $bis, 0);
    $anwTageArbeitsTage = $aplDB->getATageProPersnrBetweenDatums($persnr, $von, $bis, 1);

    $monatNormStunden = ($arbTage - $d - $nw) * 8;
    $monatNormMinuten = $monatNormStunden * 60;
    $ganzMonatNormMinuten = $aTageProMonat * 8 * 60;
    $ganzMonatNormStunden = $aTageProMonat * 8;

    $gesamtLohnZeitKc = 0;
    $gesamtLeistungZeit = 0;
    $gesamtLohnAkkordKc = 0;
    $gesamtVzAby = 0;
    $gesamtVzAbyAkkord = 0;
    $gesamtVzAbyZeit = 0;
    $gesamtQPraemie = 0;
    $gesamtQPraemie_akkord = 0;
    $gesamtQPraemie_zeit = 0;
    // vypisu vsecha OG

    foreach ($person->ogs->og as $og) {
        $personal_faktor = floatval(trim($og->og_personalfaktor));
        $vzaby = intval(trim($og->vzaby));

        $vzaby_akkord = intval(trim($og->vzaby_akkord));
        $vzaby_zeit = $vzaby - $vzaby_akkord;

        $vzaby_akkord_kc = intval(trim($og->vzaby_akkord_kc));
        $vzaby_zeit_kc = $vzaby_zeit * $persLohnFaktor;
        $vzaby_zeit_leistung = $vzaby_zeit * $leistFaktor;

        //qpraemie
        $qpraemie_kc = floatval(trim($og->qpraemie_kc));
        $qpraemie_akkord_kc = floatval(trim($og->qpraemie_akkord_kc));
        $qpraemie_zeit_min = floatval(trim($og->qpraemie_zeit_min));
        $qpraemie_zeit_kc = $qpraemie_zeit_min * $persLohnFaktor;

        $gesamtLohnZeitKc += $vzaby_zeit_kc;
        $gesamtLeistungZeit += $vzaby_zeit_leistung;
        $gesamtLohnAkkordKc += $vzaby_akkord_kc;

        $gesamtVzAby += $vzaby;
        $gesamtVzAbyAkkord += $vzaby_akkord;
        $gesamtVzAbyZeit += ( $vzaby - $vzaby_akkord);
        $gesamtQPraemie += $qpraemie_kc;
        $gesamtQPraemie_akkord += $qpraemie_akkord_kc;
        $gesamtQPraemie_zeit += $qpraemie_zeit_kc;
    }

    // leistungsgrad
    $citatel = $gesamtLeistungZeit + $gesamtVzAbyAkkord;

    if ($monatNormMinuten != 0)
        $leistungsGrad = round(($citatel) / $monatNormMinuten, 2);
    else
        $leistungsGrad = 0;

    if ($ganzMonatNormMinuten != 0)
        $leistungsGradGanzMonat = round(($citatel) / $ganzMonatNormMinuten, 2);
    else
        $leistungsGradGanzMonat = 0;


    if ($reporttyp == 'lohn') {
        $leistPraemieBerechnet1 = $aplDB->getLeistungsPraemieBetragProLeistungsFaktor($leistungsGradGanzMonat) * $aTageProMonat;
        if ($aplDB->getLeistungsPraemieBetragProLeistungsFaktor($leistungsGradGanzMonat) == 200)
            $leistPraemieBerechnet = $leistPraemieBerechnet1;
        else {
            if ($aplDB->getLeistungsPraemieBetragProLeistungsFaktor($leistungsGrad) > $aplDB->getLeistungsPraemieBetragProLeistungsFaktor($leistungsGradGanzMonat))
                $leistPraemieBerechnet = $aplDB->getLeistungsPraemieBetragProLeistungsFaktor($leistungsGrad) * $anwTageArbeitsTage;
            else
                $leistPraemieBerechnet = $leistPraemieBerechnet1;
        }
    }
    // leistungspreamie

    $leistPraemie = $bleistungsPraemie == true ? $leistPraemieBerechnet : 0;

    $anspruchLeistungspremie = $bleistungsPraemie == true ? 'ja' : 'nein';

    // QTL Praemie
    $leistungArray = array('leistung_min' => 0, 'leistung_kc' => 0);
    if ($monat % 3 == 0) {
        $qtl = ceil($monat / 3);
        $qtlTageSoll = $aplDB->sollTageQTLProPersNr($jahr, $qtl, $persnr);
        $leistungArray = $aplDB->getQTLLeistungProPersNr($jahr, $qtl, $persnr);
    }

    if ($reporttyp == 'lohn') {
        $qtlLeistungIst = $leistungArray['leistung_min'];
        $qtlLeistungIstKc = $leistungArray['leistung_kc'];
        $qtlLeistungSoll = isset($qtlTageSoll) ? $qtlTageSoll * 480 : 0;
        $qtlPraemie = $bQTLPraemie == true ? round(0.1 * $qtlLeistungIstKc) : 0;
        if ($qtlLeistungIst < $qtlLeistungSoll)
            $qtlPraemie = 0;
        $anspruchQTLPremie = $bQTLPraemie == true ? 'ja' : 'nein';
    }


    $gesamtQPraemie_zeit = $bQPraemie_zeit == true ? round($gesamtQPraemie_zeit) : 0;
    $gesamtQPraemie_akkord = $bQPraemie_akkord == true ? round($gesamtQPraemie_akkord) : 0;
    $anspruchQPremie_zeit = $bQPraemie_zeit == true ? 'ja' : 'nein';
    $anspruchQPremie_akkord = $bQPraemie_akkord == true ? 'ja' : 'nein';

    $hodCelkem = $hodCasove + $hodUkolove;
    $sumVzAby = $gesamtVzAbyZeit + $gesamtVzAbyAkkord;
    $sumLohn = $gesamtLohnZeitKc + $gesamtLohnAkkordKc;
    $sumQPraemie = $gesamtQPraemie_akkord + $gesamtQPraemie_zeit;
    $gesamtLohn = $sumLohn + $sumQPraemie + $erschwerniss + $leistPraemie + $qtlPraemie + $sonstpremie;
    $prozentPritomnostZFonduPracHodin = round($hodCelkem / $ganzMonatNormStunden, 2) * 100;

    if ($prozentPritomnostZFonduPracHodin < 50)
        $gesamtLohn -= $sumQPraemie;

    if ($hodUkolove != 0)
        $factorAkkord = $gesamtVzAbyAkkord / ($hodUkolove * 60);
    else
        $factorAkkord = 0;

    if ($hodCasove != 0)
        $factorZeit = $gesamtVzAbyZeit / ($hodCasove * 60);
    else
        $factorZeit = 0;

    if (($hodCelkem != 0))
        $factorGesamt = $sumVzAby / (($hodUkolove + $hodCasove) * 60);
    else
        $factorGesamt = 0;


    if ($reporttyp == 'lohn') {
        if ($bMAStunden) {

            $mehrarb = $aplDB->getPlusMinusStunden($monat, $jahr, $persnr);
            $vorjahr = $jahr;
            $vormonat = $monat - 1;
            if ($vormonat == 0) {
                $vormonat = 12;
                $vorjahr--;
            }
            $mehrarbVor = $aplDB->getPlusMinusStunden($vormonat, $vorjahr, $persnr);
        } else {
            $mehrarb = 0;
            $mehrarbVor = 0;
        }
    }

//    "persnr"=>array("colname"=>"persnr"),
    $popis = intval($persnr);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec, $radek, $popis);
    $sloupec++;
//    "at"=>array("colname"=>"mzd_1"),
//    $popis = $anwTageArbeitsTage;
    $popis = $anwTage;
    $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec, $radek, $popis);
    $sloupec++;
//    "z"=>array("colname"=>"mzd_13"),
    $popis = $person->tage_z;
    $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec, $radek, $popis);
    $sloupec++;
//    "d"=>array("colname"=>"mzd_9"),
    $popis = $person->tage_d;
    $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec, $radek, $popis);
    $sloupec++;
//    "nachtstd"=>array("colname"=>"mzd_8"),
    $popis = number_format(floatval($person->nachtstd), 1, '.', '');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec, $radek, $popis);
    $sloupec++;
//    "sostd"=>array("colname"=>"mzd_7"),
    $popis = number_format(floatval($person->sonestd), 1, '.', '');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec, $radek, $popis);
    $sloupec++;
//    "svatky"=>array("colname"=>"mzd_11"),
    $popis = number_format($pocetSvatkuVMesici, 1, '.', '');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec, $radek, $popis);
    $sloupec++;
    
//    "anw_std_z"=>array("colname"=>"mzd_2"),
    $popis = number_format($hodCasove, 1, '.', '');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec, $radek, $popis);
    $sloupec++;
//    "anw_std_a"=>array("colname"=>"mzd_4"),
    $popis = number_format($hodUkolove, 1, '.', '');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec, $radek, $popis);
    $sloupec++;
//    "lohn_kc_z"=>array("colname"=>"mzd_16"),
    $popis = number_format($gesamtLohnZeitKc, 0, '.', '');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec, $radek, $popis);
    $sloupec++;
//    "lohn_kc_a"=>array("colname"=>"mzd_17"),
    $popis = number_format($gesamtLohnAkkordKc, 0, '.', '');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec, $radek, $popis);
    $sloupec++;
//    "prem_q_celk"=>array("colname"=>"mzd_21"),
    $popis = number_format($sumQPraemie-$abmahnung, 0, '.', '');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec, $radek, $popis);
    $sloupec++;
//    "prem_leist_celk"=>array("colname"=>"mzd_22"),
    $popis = number_format($leistPraemie, 0, '.', '');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec, $radek, $popis);
    $sloupec++;
//    "prem_quart_celk"=>array("colname"=>"mzd_23"),
    $popis = number_format($qtlPraemie, 0, '.', '');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec, $radek, $popis);
    $sloupec++;
//    "erschw_celk"=>array("colname"=>"mzd_24"),
    $popis = number_format($erschwerniss, 0, '.', '');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec, $radek, $popis);
    $sloupec++;
//    "transport"=>array("colname"=>"mzd_51"),
    $popis = -$transport;
    $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec, $radek, $popis);
    $sloupec++;
//    "vorschuss"=>array("colname"=>"mzd_46"),
    $popis = -$vorschuss;
    $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec, $radek, $popis);
    $sloupec++;
//    "essen"=>array("colname"=>"mzd_48"),
    $popis = -$essen;
    $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec, $radek, $popis);
    $sloupec++;

    
}

date_default_timezone_set('Europe/Prague');

/** PHPExcel */
require_once '../Classes/PHPExcel.php';

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

$user = get_user_pc();
// Set properties
$objPHPExcel->getProperties()->setCreator($user)
							 ->setLastModifiedBy($user)
							 ->setTitle("E142")
							 ->setSubject("E142")
							 ->setDescription("E142")
							 ->setKeywords("office openxml php")
							 ->setCategory("phpexcel");

$personen = simplexml_import_dom($domxml);

$apl = new AplDB();
$aTageProMonat = $apl->getArbTageBetweenDatums($von, $bis);

// popisky sloupcu
$radek = 1;
$sloupec = 0;

foreach($columnsArray as $column=>$colInfo){
    $popis = $colInfo['colname'];
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValueByColumnAndRow($sloupec, $radek, $popis);
    $sloupec++;
}


$radek++;
$sloupec = 0;

foreach ($personen as $klic => $person) {
    if ($klic == 'person') {
//        test_pageoverflow($pdf, 4 * 3, array(230, 230, 230), $monat, $jahr);
        radek_personE($objPHPExcel, 4, array(255, 255, 255), $person, $monat, $jahr);
	$radek++;
	$sloupec=0;
    }
}

//============================================================+
// END OF FILE                                                 
//============================================================+
//
// Rename sheet
$objPHPExcel->getActiveSheet()->setTitle('E142');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

$filename = "E142_".$jahr."_".$monat."_".date('His').".csv";
// Redirect output to a client’s web browser (Excel5)
//header('Content-Type: application/vnd.ms-excel');
header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename="'.$filename.'"');
header('Cache-Control: max-age=0');

$objWriter = new PHPExcel_Writer_CSV($objPHPExcel);
$objWriter->setEnclosure('');
//$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;


//$pdf->Output();
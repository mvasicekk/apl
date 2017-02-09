<?php

require_once '../db.php';

$inputData = $_GET;

// zjistim zda mam hodnotu value ulozenou v databazi artiklu

$a = AplDB::getInstance();

//$terminMatch = trim($_GET['termin']);
$kundeVon = intval(trim($_GET['kundevon']));
$kundeBis = intval(trim($_GET['kundebis']));
$datumVon = trim($_GET['von']);
$datumBis = trim($_GET['bis']);
$reklnr = trim($_GET['reklnr']);
$teilnr = trim($_GET['teilnr']);

if ($datumVon != 0 && $datumBis != 0) {
    $datumVon = date('Y-m-d', $datumVon / 1000);
    $datumBis = date('Y-m-d', $datumBis / 1000) . " 23:59:59";
}


$zeilenArray = array();

$sql = "";
$sql.=" select * ";
$sql.=" from dreklamation";
$sql.=" join dkopf on dkopf.Teil=dreklamation.teil";
$sql.=" where (1)";
if ($datumVon != 0 && $datumBis != 0) {
    $sql.="   and (rekl_datum between '$datumVon' and '$datumBis')";
}
if ($kundeVon != 0 && $kundeBis != 0) {
    $sql.="   and (dreklamation.kunde between '$kundeVon' and '$kundeBis')";
}
if (strlen($reklnr) > 0) {
    $sql.="   and (dreklamation.rekl_nr like '%$reklnr%')";
}
if (strlen($teilnr) > 0) {
    $sql.="   and (dreklamation.teil like '%$teilnr%')";
}

$sql.=" order by ";
$sql.="     dreklamation.kunde,";
$sql.="     dreklamation.rekl_datum,";
$sql.="     dreklamation.rekl_nr";

if (
	($datumVon != 0 && $datumBis != 0) || ($kundeVon != 0 && $kundeBis != 0) || (strlen($reklnr) > 0)
) {
    $reklArray = $a->getQueryRows($sql);
    if ($reklArray !== NULL) {
	foreach ($reklArray as $rekl) {
	    $kurs = $a->getKurs($rekl['rekl_datum'], 'EUR', 'CZK');
	    //abmahnung vorschlag
	    $sumAbmahnungen = $a->getBetragSumAbmahnungenForReklId($rekl['id']);
	    $rekl['abmahnungenbetrag'] = $sumAbmahnungen;
	    //strzene penize
	    $rekl['abmahnungenbetrag_ist'] = $a->getBetragSumAbmahnungenForReklId($rekl['id'],FALSE);
	    
	    $zeilen['kunden'][$rekl['kunde']]['reklamationen'][$rekl['rekl_nr']] = $rekl;
	    $interne_bewertung = intval($rekl['interne_bewertung']);
	    $zeilen['kunden'][$rekl['kunde']]['summen']['interne_bewertung'] += $interne_bewertung*$interne_bewertung;
	    //kosten
	    $zeilen['kunden'][$rekl['kunde']]['summen']['abmahnungenbetrag'] += floatval($rekl['abmahnungenbetrag']);
	    $zeilen['kunden'][$rekl['kunde']]['summen']['abmahnungenbetrag_ist'] += floatval($rekl['abmahnungenbetrag_ist']);
	    
	    $zeilen['kunden'][$rekl['kunde']]['summen']['anerkannt_ausschuss_preis_eur'] += floatval($rekl['anerkannt_ausschuss_preis_eur']);
	    $zeilen['kunden'][$rekl['kunde']]['summen']['anerkannt_ausschuss_preis_czk'] += floatval($rekl['anerkannt_ausschuss_preis_eur'])*$kurs;
	    
	    $zeilen['kunden'][$rekl['kunde']]['summen']['anerkannt_ausschuss_selbst_preis_eur'] += floatval($rekl['anerkannt_ausschuss_selbst_preis_eur']);
	    $zeilen['kunden'][$rekl['kunde']]['summen']['anerkannt_ausschuss_selbst_preis_czk'] += floatval($rekl['anerkannt_ausschuss_selbst_preis_eur'])*$kurs;
	    
	    $zeilen['kunden'][$rekl['kunde']]['summen']['anerkannt_nacharbeit_preis_eur'] += floatval($rekl['anerkannt_nacharbeit_preis_eur']);
	    $zeilen['kunden'][$rekl['kunde']]['summen']['anerkannt_nacharbeit_preis_czk'] += floatval($rekl['anerkannt_nacharbeit_preis_eur'])*$kurs;
	    
	    $zeilen['kunden'][$rekl['kunde']]['summen']['dif_falsch_deklariert_preis_eur'] += floatval($rekl['dif_falsch_deklariert_preis_eur']);
	    $zeilen['kunden'][$rekl['kunde']]['summen']['dif_falsch_deklariert_preis_czk'] += floatval($rekl['dif_falsch_deklariert_preis_eur'])*$kurs;
	    
	    $zeilen['kunden'][$rekl['kunde']]['summen']['verpackung_preis_eur'] += floatval($rekl['verpackung_preis_eur']);
	    $zeilen['kunden'][$rekl['kunde']]['summen']['verpackung_preis_czk'] += floatval($rekl['verpackung_preis_eur'])*$kurs;
	    
	    $zeilen['kunden'][$rekl['kunde']]['summen']['kreislauf_preis_eur'] += floatval($rekl['kreislauf_preis_eur']);
	    $zeilen['kunden'][$rekl['kunde']]['summen']['kreislauf_preis_czk'] += floatval($rekl['kreislauf_preis_eur'])*$kurs;
	    
	    $zeilen['kunden'][$rekl['kunde']]['summen']['pauschale_preis_eur'] += floatval($rekl['pauschale_preis_eur']);
	    $zeilen['kunden'][$rekl['kunde']]['summen']['pauschale_preis_czk'] += floatval($rekl['pauschale_preis_eur'])*$kurs;
	    
	    $zeilen['kunden'][$rekl['kunde']]['summen']['forecast_anerkannt_ausschuss_eur']  += floatval($rekl['forecast_anerkannt_ausschuss_eur']);
	    $zeilen['kunden'][$rekl['kunde']]['summen']['forecast_anerkannt_ausschuss_selbst_eur']  += floatval($rekl['forecast_anerkannt_ausschuss_selbst_eur']);
	    $zeilen['kunden'][$rekl['kunde']]['summen']['forecast_anerkannt_nacharbeit_eur']  += floatval($rekl['forecast_anerkannt_nacharbeit_eur']);
	    $zeilen['kunden'][$rekl['kunde']]['summen']['forecast_dif_falsch_deklariert_eur']  += floatval($rekl['forecast_dif_falsch_deklariert_eur']);
	    $zeilen['kunden'][$rekl['kunde']]['summen']['forecast_verpackung_eur']  += floatval($rekl['forecast_verpackung_eur']);
	    $zeilen['kunden'][$rekl['kunde']]['summen']['forecast_kreislauf_eur']  += floatval($rekl['forecast_kreislauf_eur']);
	    $zeilen['kunden'][$rekl['kunde']]['summen']['forecast_pauschale_eur']  += floatval($rekl['forecast_pauschale_eur']);
	    
	    $zeilen['kunden'][$rekl['kunde']]['summen']['forecast_anerkannt_ausschuss_czk']  += floatval($rekl['forecast_anerkannt_ausschuss_eur'])*$kurs;
	    $zeilen['kunden'][$rekl['kunde']]['summen']['forecast_anerkannt_ausschuss_selbst_czk']  += floatval($rekl['forecast_anerkannt_ausschuss_selbst_eur'])*$kurs;
	    $zeilen['kunden'][$rekl['kunde']]['summen']['forecast_anerkannt_nacharbeit_czk']  += floatval($rekl['forecast_anerkannt_nacharbeit_eur'])*$kurs;
	    $zeilen['kunden'][$rekl['kunde']]['summen']['forecast_dif_falsch_deklariert_czk']  += floatval($rekl['forecast_dif_falsch_deklariert_eur'])*$kurs;
	    $zeilen['kunden'][$rekl['kunde']]['summen']['forecast_verpackung_czk']  += floatval($rekl['forecast_verpackung_eur'])*$kurs;
	    $zeilen['kunden'][$rekl['kunde']]['summen']['forecast_kreislauf_czk']  += floatval($rekl['forecast_kreislauf_eur'])*$kurs;
	    $zeilen['kunden'][$rekl['kunde']]['summen']['forecast_pauschale_czk']  += floatval($rekl['forecast_pauschale_eur'])*$kurs;
	}

	//vytvoreni sekci pro tabulku
	foreach ($zeilen['kunden'] as $kunde => $kundeArray) {
	    array_push($zeilenArray, array("section" => "kundeheader", "summen"=>$kundeArray['summen'],"kunde" => $kunde));
	    foreach ($kundeArray['reklamationen'] as $reklnr => $rekl) {
		//$teilInfo = $a->getTeilInfoArray($teil);
		$kurs = $a->getKurs($rekl['rekl_datum'], 'EUR', 'CZK');
		// po carce pridam mezeru, aby se v tabulce po mezere mohl zalamovat text
		$rekl['export'] = str_replace(',',', ',$rekl['export']);
		$rekl['giesstag'] = str_replace(',',', ',$rekl['giesstag']);
		$rekl['rekl_datum'] = ($d=strtotime($rekl['rekl_datum']))!==FALSE?date('d.m.y',$d):'';
		$rekl['rekl_erledigt_am'] = ($d=strtotime($rekl['rekl_erledigt_am']))!==FALSE?date('d.m.y',$d):'';
		$rekl['wider_am'] = ($d=strtotime($rekl['wider_am']))!==FALSE?date('d.m.y',$d):'';
		
		$rekl['anerkannt_ausschuss_preis_eur'] = floatval($rekl['anerkannt_ausschuss_preis_eur']);
		$rekl['anerkannt_ausschuss_preis_czk'] = floatval($rekl['anerkannt_ausschuss_preis_eur'])*$kurs;
	    
		$rekl['anerkannt_ausschuss_selbst_preis_eur'] = floatval($rekl['anerkannt_ausschuss_selbst_preis_eur']);
		$rekl['anerkannt_ausschuss_selbst_preis_czk'] = floatval($rekl['anerkannt_ausschuss_selbst_preis_eur'])*$kurs;

		$rekl['anerkannt_nacharbeit_preis_eur'] = floatval($rekl['anerkannt_nacharbeit_preis_eur']);
		$rekl['anerkannt_nacharbeit_preis_czk'] = floatval($rekl['anerkannt_nacharbeit_preis_eur'])*$kurs;
	    
		$rekl['dif_falsch_deklariert_preis_eur'] = floatval($rekl['dif_falsch_deklariert_preis_eur']);
		$rekl['dif_falsch_deklariert_preis_czk'] = floatval($rekl['dif_falsch_deklariert_preis_eur'])*$kurs;
	    
		$rekl['verpackung_preis_eur'] = floatval($rekl['verpackung_preis_eur']);
		$rekl['verpackung_preis_czk'] = floatval($rekl['verpackung_preis_eur'])*$kurs;
	    
		$rekl['kreislauf_preis_eur'] = floatval($rekl['kreislauf_preis_eur']);
		$rekl['kreislauf_preis_czk'] = floatval($rekl['kreislauf_preis_eur'])*$kurs;
	    
		$rekl['pauschale_preis_eur'] = floatval($rekl['pauschale_preis_eur']);
		$rekl['pauschale_preis_czk'] = floatval($rekl['pauschale_preis_eur'])*$kurs;
		
		$rekl['forecast_anerkannt_ausschuss_czk']  = floatval($rekl['forecast_anerkannt_ausschuss_eur'])*$kurs;
		$rekl['forecast_anerkannt_ausschuss_selbst_czk']  = floatval($rekl['forecast_anerkannt_ausschuss_selbst_eur'])*$kurs;
		$rekl['forecast_anerkannt_nacharbeit_czk']  = floatval($rekl['forecast_anerkannt_nacharbeit_eur'])*$kurs;
		$rekl['forecast_dif_falsch_deklariert_czk']  = floatval($rekl['forecast_dif_falsch_deklariert_eur'])*$kurs;
		$rekl['forecast_verpackung_czk']  = floatval($rekl['forecast_verpackung_eur'])*$kurs;
		$rekl['forecast_kreislauf_czk']  = floatval($rekl['forecast_kreislauf_eur'])*$kurs;
		$rekl['forecast_pauschale_czk']  = floatval($rekl['forecast_pauschale_eur'])*$kurs;
		
		$rekl['forecast_anerkannt_ausschuss_eur']  = floatval($rekl['forecast_anerkannt_ausschuss_eur']);
		$rekl['forecast_anerkannt_ausschuss_selbst_eur']  = floatval($rekl['forecast_anerkannt_ausschuss_selbst_eur']);
		
		$rekl['forecast_anerkannt_nacharbeit_eur']  = floatval($rekl['forecast_anerkannt_nacharbeit_eur']);
		$rekl['forecast_dif_falsch_deklariert_eur']  = floatval($rekl['forecast_dif_falsch_deklariert_eur']);
		$rekl['forecast_verpackung_eur']  = floatval($rekl['forecast_verpackung_eur']);
		$rekl['forecast_kreislauf_eur']  = floatval($rekl['forecast_kreislauf_eur']);
		$rekl['forecast_pauschale_eur']  = floatval($rekl['forecast_pauschale_eur']);
		
		//zjistit verurschery
		$verursacherRows = $a->getVerursacherForReklId($rekl['id']);
		$rekl['verursacherArray'] = $verursacherRows;
		array_push($zeilenArray, array("section" => "rekldetail", "rekl" => $rekl));
	    }
	    foreach ($kundeArray['summen'] as $summeIndex=>$value){
		$tableSummen[$summeIndex]+=$value;
	    }
	}
	array_push($zeilenArray, array("section" => "tableSummen", "summen"=>$tableSummen));
    }

}


$returnArray = array(
    'reklArray'=>$reklArray,
    'zeilenraw' => $zeilen,
    'von' => $datumVon,
    'bis' => $datumBis,
    'kundevon' => $kundeVon,
    'kundebis' => $kundeBis,
    "zeilen" => $zeilenArray,
    "teileKeysArray" => $teileKeysArray,
    "dmaArray" => $dmaArray,
    "sql_dma" => $sql,
);

echo json_encode($returnArray);

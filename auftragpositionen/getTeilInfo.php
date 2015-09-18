<?
require_once '../db.php';

    $teil = $_GET['teil'];
    $auftrag = $_GET['auftrag'];
    
    // zjistim zda mam hodnotu value ulozenou v databazi artiklu

    $a = AplDB::getInstance();

    $teilInfo = array();
    
    $teilInfo['teil'] = $a->getTeilInfoArray($teil);
    $teilInfo['dpos'] = $a->getDposInfo($teil,NULL,TRUE);
    
    //vypocet cen
    $auftragInfoA = $a->getAuftragInfoArray($auftrag);
    if($auftragInfoA!==NULL){
	$auftragInfo = $auftragInfoA[0];
	$minpreis = $auftragInfo['minpreis'];
	$kunde = $auftragInfo['kunde'];
	$runden = $a->getKundePreisRundenStellen($kunde);
	if($teilInfo['dpos']!==NULL){
	    foreach ($teilInfo['dpos'] as $key=>$r){
		$teilInfo['dpos'][$key]['preis'] = round($minpreis * $r['vzkd'],$runden);
		$teilInfo['dpos'][$key]['vzkd'] = number_format($r['vzkd'],4);
		$teilInfo['dpos'][$key]['vzaby'] = number_format($r['vzaby'],4);
	    }
	}
    }
    
    //fremd
    $sql="select daufkopf.auftragsnr,fremdauftr,fremdpos from dauftr join daufkopf using(auftragsnr)";
    $sql.="	where ((fremdauftr>0) and (teil='$teil')) order by daufkopf.aufdat desc limit 1";
    $r=$a->getQueryRows($sql);
    if($r!==NULL){
	$fremdauftr = $r[0]['fremdauftr'];
	$fremdpos = $r[0]['fremdpos'];
	$fremdauftrausauftrag = $r[0]['auftragsnr'];
    }

    
    // 5. zjistim, zda zadany dil uz neni v zakazce a ma vyplneny termin
//    $sql = "select termin from dauftr where (((dauftr.Teil) = '".$teil."') And ((dauftr.termin) Is Not Null) and (dauftr.auftragsnr=".$auftrag.")) order by dauftr.termin desc";
//    $r=$a->getQueryRows($sql);
//    if($r!==NULL){
//	$explanmit = $r[0]['termin'];
//    }
//    else{
//	$explanmit = "";
//    }

    $explanmit = "P".$kunde."99999";
    
    // jake mam dokumenty k dilu - stejna tabulka jako na prac papiru
    
    $dokLegendArray = $a->getTeilDokuDistinctDokuArray($teil,TRUE);
//    if($dokLegendArray!==NULL){
//	foreach ($dokLegendArray as $i=>$row){
//	    $dokunr = $row['doku_nr'];
//	    $dokuTypArray = $a->getDokuTypArray($dokunr);
//	    $dokLegendArray[$i]['dokutyp'] = $dokuTypArray[0]['doku_beschreibung'];
//	}
//    }
    $teilInfo['dokumente'] = $dokLegendArray;
    
    $returnArray = array(
	'e'=>$e,
	'teilInfo'=>$teilInfo,
	'auftragInfo'=>$auftragInfo,
	'fremdauftr'=>$fremdauftr,
	'fremdpos'=>$fremdpos,
	'fremdauftrausauftrag'=>$fremdauftrausauftrag,
	'explanmit'=>$explanmit,
	'minpreis'=>$minpreis,
	'runden'=>$runden,
    );
    
    echo json_encode($returnArray);


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
$teil = trim($_GET['teil']);

if($datumVon!=0 && $datumBis!=0){
    $datumVon = date('Y-m-d',  $datumVon/1000);
    $datumBis = date('Y-m-d',  $datumBis/1000)." 23:59:59";
}

$zeilenArray = array();
$sql="";
$sql.=" select ";
$sql.="     dma.id,";
$sql.="     dma.imanr,";
$sql.="     dma.emanr,";
$sql.="     dkopf.Kunde as kunde,";
$sql.="     dma.teil,";
$sql.="     dkopf.Teilbez as teilbezeichnung,";
$sql.="     dma.auftragsnrarray,";
$sql.="     dma.ema_auftragsarray,";
$sql.="     dma.ema_auftragsarray_genehmigt,";
$sql.="     dma.ima_auftragsnrarray_genehmigt,";
$sql.="     dma.palarray,";
$sql.="     dma.ima_dauftrid_array,";
$sql.="     dma.ima_dauftrid_array_genehmigt,";
$sql.="     dma.ema_dauftrid_array,";
$sql.="     dma.ema_dauftrid_array_genehmigt,";
$sql.="     dma.ema_palarray,";
$sql.="     dma.ema_palarray_genehmigt,";
$sql.="     dma.ima_palarray_genehmigt,";
$sql.="     dma.tatundzeitarray,";
$sql.="     dma.ema_tatundzeitarray,";
$sql.="     dma.ema_tatundzeitarray_genehmigt,";
$sql.="     dma.ima_tatundzeitarray_genehmigt,";
$sql.="     dma.bemerkung,";
$sql.="     dma.ema_anlagen_array,";
$sql.="     dma.imavon,";
$sql.="     dma.ima_genehmigt,";
$sql.="     dma.ema_genehmigt,";
$sql.="     dma.ima_genehmigt_user,";
$sql.="     dma.ema_genehmigt_user,";
$sql.="     dma.ima_genehmigt_stamp,";
$sql.="     dma.ema_genehmigt_stamp,";
$sql.="     dma.ima_genehmigt_bemerkung,";
$sql.="     dma.ema_genehmigt_bemerkung,";
$sql.="     dma.ema_antrag_vom,";
$sql.="     dma.ema_antrag_text,";
$sql.="     dma.ema_antrag_am,";
$sql.="     dma.stamp";
$sql.=" from dma";
$sql.=" join dkopf on dkopf.Teil=dma.teil";
$sql.=" where (1)";
if($datumVon!=0 && $datumBis!=0){
    $sql.="   and (dma.stamp between '$datumVon' and '$datumBis')";
}
if($kundeVon!=0 && $kundeBis!=0){
    $sql.="   and (dkopf.Kunde between '$kundeVon' and '$kundeBis')";
}
if(strlen($teil)>0){
    $sql.="   and (dma.teil like '%$teil%')";
}

$sql.=" order by ";
$sql.="     dkopf.Kunde,";
$sql.="     dma.teil";

if(
    ($datumVon!=0 && $datumBis!=0)
    ||($kundeVon!=0 && $kundeBis!=0)
    ||(strlen($teil)>0)
)
{
    $dmaArray = $a->getQueryRows($sql);
    if($dmaArray!==NULL){
	foreach ($dmaArray as $dma){
	    $kunde = $dma['kunde'];
	    $teil = $dma['teil'];
	    $imanr = $dma['imanr'];
	    $zeilen['kunden'][$kunde]['teile'][$teil]['ma'][$imanr]['dma'] = $dma;
	    $dauftrIdArray['ima_antrag'] = explode(";", $dma['ima_dauftrid_array']);
	    $dauftrIdArray['ima_genehmigt'] = explode(";", $dma['ima_dauftrid_array_genehmigt']);
	    $dauftrIdArray['ema_antrag'] = explode(";", $dma['ema_dauftrid_array']);
	    $dauftrIdArray['ema_genehmigt'] = explode(";", $dma['ema_dauftrid_array_genehmigt']);
	    $importStkArray = array();
	    foreach ($dauftrIdArray as $typ => $idArray) {
		$pocetId[$typ] = 0;
		$pocetDauftrRows[$typ] = 0;
		if (is_array($idArray)) {
		    $pocetId[$typ] = count($idArray);
		    foreach ($idArray as $dauftrId) {
			$dauftrRow = $a->getDauftrRow($dauftrId);
			if ($dauftrRow !== NULL) {
			    $pocetDauftrRows[$typ] ++;
			    $importStkArray[$typ][$dauftrRow['auftragsnr']]['ba_stk']+=$dauftrRow['stk'];
			    $importStkArray[$typ][$dauftrRow['auftragsnr']]['im_stk']+=$dauftrRow['im_stk'];
			    
			    //sumy pro skupiny
			    //kunde
			    $zeilen['kunden'][$kunde]['summen'][$typ]['ba_stk']+=$dauftrRow['stk'];
			    $zeilen['kunden'][$kunde]['summen'][$typ]['im_stk']+=$dauftrRow['im_stk'];
			    //teil
			    $zeilen['kunden'][$kunde]['teile'][$teil]['summen'][$typ]['ba_stk']+=$dauftrRow['stk'];
			    $zeilen['kunden'][$kunde]['teile'][$teil]['summen'][$typ]['im_stk']+=$dauftrRow['im_stk'];
			    //ma
			    $zeilen['kunden'][$kunde]['teile'][$teil]['ma'][$imanr]['summen'][$typ]['ba_stk']+=$dauftrRow['stk'];
			    $zeilen['kunden'][$kunde]['teile'][$teil]['ma'][$imanr]['summen'][$typ]['im_stk']+=$dauftrRow['im_stk'];
			}
		    }
		}
	    }
	    foreach ($importStkArray as $typ=>$importArray){
		foreach ($importArray as $import=>$stkArray){
		    //importy
		    $zeilen['kunden'][$kunde]['teile'][$teil]['ma'][$imanr]['importe'][$import] = $stkArray;		    
		}
	    }
	}
	
	
	//vytvoreni sekci pro tabulku
	foreach ($zeilen['kunden'] as $kunde=>$kundeArray){
	    array_push($zeilenArray, array("section"=>"kundeheader","kunde"=>$kunde));
	    foreach ($kundeArray['teile'] as $teil=>$teilArray){
		$teilInfo = $a->getTeilInfoArray($teil);
		array_push($zeilenArray, array("section"=>"teilheader","teil"=>$teil,"teilInfo"=>$teilInfo));
		foreach($teilArray['ma'] as $imanr=>$dma1){
		    $summen = $dma1['summen'];
		    $importe = $dma1['importe'];
		    $dma = $dma1['dma'];
		    $teileCount[$dma['teil']]+=1;
		    //uprava nekterych poli
		    //imavon - jen jmeno uzivatele
		    $dma['imavon'] = substr($dma['imavon'], strrpos($dma['imavon'], '/')+1);
		    array_push($zeilenArray, array("section"=>"dmadetail","dmaRow"=>$dma,"summen"=>$summen));
		    //sekce pro importy
		    if(is_array($importe)){
			foreach ($importe as $import=>$imArray){
			    array_push($zeilenArray, array("section"=>"importdetail","import"=>$import,"antragImportStk"=>$imArray));
			}
		    }
		}
	    }
	}
	    
	$teileKeysArray = array_keys($teileCount);
    }
}


$returnArray = array(
    'zeilenraw'=>$zeilen,
    'von'=>$datumVon,
    'bis'=>$datumBis,
    'kundevon'=>$kundeVon,
    'kundebis'=>$kundeBis,
    "zeilen" => $zeilenArray,
    "teileKeysArray"=>$teileKeysArray,
    "dmaArray"=>$dmaArray,
    "sql_dma"=>$sql,
);

echo json_encode($returnArray);

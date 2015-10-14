<?php

require_once '../db.php';

$inputData = $_GET;

// zjistim zda mam hodnotu value ulozenou v databazi artiklu

$a = AplDB::getInstance();

//$terminMatch = trim($_GET['termin']);
$terminMatchVon = trim($_GET['terminvon']);
$terminMatchBis = trim($_GET['terminbis']);
$importMatch = trim($_GET['import']);
$teilMatch = trim($_GET['teil']);
$kundeMatch = trim($_GET['kunde']);
$terminVon1 = trim($_GET['von']);
$terminBis1 = trim($_GET['bis']);
if(intval($terminVon1)>0 && intval($terminBis1)>0){
    $terminVon = date('Y-m-d',  $terminVon1/1000);
    $terminBis = date('Y-m-d',  $terminBis1/1000)." 23:59:59";
}

$sql.=" select ";
$sql.="     if(dauftr.termin is null or dauftr.termin='','NOTERMIN',dauftr.termin) as termin,";
$sql.="     dt.ex_datum_soll,";
$sql.="     dauftr.auftragsnr,";
$sql.="     DATE_FORMAT(daufkopf.aufdat,'%d.%m.%y') as import_datum,";
$sql.="     dauftr.teil,";
$sql.="     dauftr.`pos-pal-nr` as im_pal,";
$sql.="     dauftr.giesstag,";
$sql.="     dauftr.abgnr,";
$sql.="     sum(if(kzgut='G',dauftr.`stück`,0)) as sum_im_stk,";
$sql.="     sum(if(kzgut='G',dauftr.`stück`*dkopf.gew/1000,0)) as sum_im_gew,";
$sql.="     max(if(kzgut='G',dauftr.bemerkung,'')) as bemerkung,";
$sql.="     dauftr.kzgut,";
$sql.="     sum(dauftr.VzKd*dauftr.`stück`) as sum_vzkd,";
$sql.="     sum(dauftr.VzAby*dauftr.`stück`) as sum_vzaby";
$sql.=" from dauftr";
$sql.=" join dkopf on dkopf.teil=dauftr.teil";
$sql.=" join daufkopf on daufkopf.auftragsnr=dauftr.auftragsnr";
$sql.=" left join daufkopf dt on dt.auftragsnr=SUBSTRING(dauftr.termin,2)";
$sql.=" where (1)";
if(strlen($kundeMatch)==3){
    $sql.="     and (daufkopf.kunde='$kundeMatch')";
}
if(strlen($terminMatchVon)>1&&($terminMatchBis)>1){
    $sql.="   and (  dauftr.termin between 'P$terminMatchVon' and 'P$terminMatchBis')";
}
if(strlen(trim($importMatch))>0){
    $sql.="     and dauftr.auftragsnr like '%$importMatch%'";    
}
if(strlen(trim($teilMatch))>0){
    $sql.="     and dauftr.teil like '%$teilMatch%'";    
}
if($terminVon!=NULL && $terminBis!=NULL){
    $sql.="   and (  dt.ex_datum_soll between '$terminVon' and '$terminBis')";
}
$sql.="     and dauftr.`auftragsnr-exp` is null";
$sql.=" group by";
$sql.="     dauftr.termin,";
$sql.="     dauftr.auftragsnr,";
$sql.="     dauftr.teil,";
$sql.="     dauftr.`pos-pal-nr`,";
$sql.="     dauftr.abgnr";
$sql.=" HAVING ";
//$sql.="     sum_vzkd>0 and";
$sql.="     abgnr<>95 and";
$sql.="     abgnr<>93";
$sql.=" order by";
$sql.="     dauftr.termin,";
$sql.="     dauftr.teil,";
$sql.="     dauftr.auftragsnr,";
$sql.="     dauftr.`pos-pal-nr`,";
$sql.="     dauftr.abgnr";


$sqlD.=" select ";
$sqlD.="     if(dauftr.termin is null or dauftr.termin='','NOTERMIN',dauftr.termin) as termin,";
$sqlD.="     dauftr.auftragsnr,";
$sqlD.="     dauftr.teil,";
$sqlD.="     dauftr.`pos-pal-nr` as im_pal,";
$sqlD.="     dauftr.abgnr,";
$sqlD.="     sum(if(dauftr.kzgut='G' and drueck.`Stück` is not null,drueck.`stück`,0)) as sum_G_stk,";
$sqlD.="     sum(if(drueck.`Stück` is null,0,drueck.`Stück`)) as sum_gut_stk,";
$sqlD.="     sum(if(drueck.`Stück` is not null,if(auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`vz-soll`,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`vz-soll`),0)) as sum_vzkd,";
$sqlD.="     sum(if(drueck.`Stück` is not null,if(auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`vz-ist`,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`vz-ist`),0)) as sum_vzaby";

$sqlD.=" from dauftr";
$sqlD.=" join daufkopf on daufkopf.auftragsnr=dauftr.auftragsnr";
$sqlD.=" left join daufkopf dt on dt.auftragsnr=SUBSTRING(dauftr.termin,2)";
$sqlD.=" left join drueck on drueck.AuftragsNr=dauftr.auftragsnr and drueck.Teil=dauftr.teil and drueck.`pos-pal-nr`=dauftr.`pos-pal-nr` and drueck.TaetNr=dauftr.abgnr";
$sqlD.=" where (1)";
if(strlen($kundeMatch)==3){
    $sqlD.="     and (daufkopf.kunde='$kundeMatch')";
}
if(strlen($terminMatchVon)>1&&($terminMatchBis)>1){
    $sqlD.="   and (  dauftr.termin between 'P$terminMatchVon' and 'P$terminMatchBis')";
}
if(strlen(trim($importMatch))>0){
    $sqlD.="     and dauftr.auftragsnr like '%$importMatch%'";    
}
if(strlen(trim($teilMatch))>0){
    $sqlD.="     and dauftr.teil like '%$teilMatch%'";    
}
if($terminVon!=NULL && $terminBis!=NULL){
    $sqlD.="   and (  dt.ex_datum_soll between '$terminVon' and '$terminBis')";
}

//$sqlD.="     dauftr.termin like 'P$terminMatch%'";
//$sqlD.="     dauftr.termin between 'P$terminMatchVon' and 'P$terminMatchBis'";
//if(strlen(trim($importMatch))>0){
//    $sqlD.="     and dauftr.auftragsnr like '%$importMatch%'";    
//}
//if(strlen(trim($teilMatch))>0){
//    $sqlD.="     and dauftr.teil like '%$teilMatch%'";    
//}

$sqlD.="     and dauftr.`auftragsnr-exp` is null";
$sqlD.=" group by";
$sqlD.="     dauftr.termin,";
$sqlD.="     dauftr.auftragsnr,";
$sqlD.="     dauftr.teil,";
$sqlD.="     dauftr.`pos-pal-nr`,";
$sqlD.="     dauftr.abgnr";
$sqlD.=" order by";
$sqlD.="     dauftr.termin,";
$sqlD.="     dauftr.teil,";
$sqlD.="     dauftr.auftragsnr,";
$sqlD.="     dauftr.`pos-pal-nr`,";
$sqlD.="     dauftr.abgnr";

$sqlDA.=" select ";
$sqlDA.="     if(dauftr.termin is null or dauftr.termin='','NOTERMIN',dauftr.termin) as termin,";
$sqlDA.="     dauftr.auftragsnr,";
$sqlDA.="     dauftr.teil,";
$sqlDA.="     dauftr.`pos-pal-nr` as im_pal,";
$sqlDA.="     if(drueck.`auss-art` is null,0,drueck.`auss-art`) as aart,     ";
$sqlDA.="     sum(if(drueck.`Auss-Stück` is null,0,drueck.`Auss-Stück`)) as sum_auss_stk";
$sqlDA.=" from dauftr";
$sqlDA.=" join daufkopf on daufkopf.auftragsnr=dauftr.auftragsnr";
$sqlDA.=" left join daufkopf dt on dt.auftragsnr=SUBSTRING(dauftr.termin,2)";
$sqlDA.=" left join drueck on drueck.AuftragsNr=dauftr.auftragsnr and drueck.Teil=dauftr.teil and drueck.`pos-pal-nr`=dauftr.`pos-pal-nr` and drueck.TaetNr=dauftr.abgnr";
$sqlDA.=" where (1)";
if(strlen($kundeMatch)==3){
    $sqlDA.="     and (daufkopf.kunde='$kundeMatch')";
}
if(strlen($terminMatchVon)>1&&($terminMatchBis)>1){
    $sqlDA.="   and (  dauftr.termin between 'P$terminMatchVon' and 'P$terminMatchBis')";
}
if(strlen(trim($importMatch))>0){
    $sqlDA.="     and dauftr.auftragsnr like '%$importMatch%'";    
}
if(strlen(trim($teilMatch))>0){
    $sqlDA.="     and dauftr.teil like '%$teilMatch%'";    
}
if($terminVon!=NULL && $terminBis!=NULL){
    $sqlDA.="   and (  dt.ex_datum_soll between '$terminVon' and '$terminBis')";
}

//$sqlDA.="     dauftr.termin like 'P$terminMatch%'";
//$sqlDA.="     dauftr.termin between 'P$terminMatchVon' and 'P$terminMatchBis'";
//if(strlen(trim($importMatch))>0){
//    $sqlDA.="     and dauftr.auftragsnr like '%$importMatch%'";    
//}
//if(strlen(trim($teilMatch))>0){
//    $sqlDA.="     and dauftr.teil like '%$teilMatch%'";    
//}

$sqlDA.="     and dauftr.`auftragsnr-exp` is null";
$sqlDA.=" group by";
$sqlDA.="     dauftr.termin,";
$sqlDA.="     dauftr.auftragsnr,";
$sqlDA.="     dauftr.teil,";
$sqlDA.="     dauftr.`pos-pal-nr`,";
$sqlDA.="     if(drueck.`auss-art` is null,0,drueck.`auss-art`)";
$sqlDA.=" having";
$sqlDA.="     (aart=0)";
$sqlDA.="     or (aart<>0 and sum_auss_stk<>0)";
$sqlDA.=" order by";
$sqlDA.="     dauftr.termin,";
$sqlDA.="     dauftr.teil,";
$sqlDA.="     dauftr.auftragsnr,";
$sqlDA.="     dauftr.`pos-pal-nr`";
    
$aartArray = array();

if (((strlen($terminMatchVon)>=3)&&(strlen($terminMatchBis)>=3))
	||(strlen($kundeMatch)==3)
//	||(($terminVon!=NULL) && ($terminBis!=NULL) && (strlen($kundeMatch)==3))
	||(($terminVon!=NULL) && ($terminBis!=NULL))
    ) {
    $rows = $a->getQueryRows($sql);
    $rowsD = $a->getQueryRows($sqlD);
    $rowsDA = $a->getQueryRows($sqlDA);
}

$sumReport = array();
$sumTermin = array();

if ($rows != NULL) {
    foreach ($rows as $r) {
	$termin = $r['termin'];
	$import = $r['auftragsnr'];
	$teil = $r['teil'];
	$pal = $r['im_pal'];
	$gt = $r['giesstag'];
	$imdat = $r['import_datum'];
	$abgnr = $r['abgnr'];
	$abgnrArray[$abgnr] += 1;
	$teileArray[$teil]['count'] += 1;
	
	$terminA = substr($termin,1);
        $bemerkungA = $a->getAuftragInfoArray($terminA);
	$zielort = $a->getZielortAuftrag($terminA);
	$terminArray[$termin] = "( ".$bemerkungA[0]['ex_soll_datum']." ".$bemerkungA[0]['ex_soll_uhrzeit']." )".$bemerkungA[0]['bemerkung']." $zielort";

	$zeilen[$termin][$teil][$import][$pal][$abgnr]['sum_vzkd'] = $r['sum_vzkd'];
	$zeilen[$termin][$teil][$import][$pal][$abgnr]['sum_vzaby'] = $r['sum_vzaby'];
	$zeilen[$termin][$teil][$import][$pal]['sum_im_stk'] += $r['sum_im_stk'];
	$zeilen[$termin][$teil][$import][$pal]['sum_im_gew'] += $r['sum_im_gew'];
	$zeilen[$termin][$teil][$import][$pal]["import_datum"]= $imdat;
	
	if(trim($r['kzgut'])=='G'){
	    $zeilen[$termin][$teil][$import][$pal]['bemerkung'] = strip_tags(trim($r['bemerkung']));
	    $zeilen[$termin][$teil][$import][$pal]["gt"]= $gt;
	}
    }
    
    foreach($teileArray as $teil=>$val){
	$teileArray[$teil]['info'] = $a->getTeilInfoArray($teil);
	$teileArray[$teil]['rekl']['E'] = $a->getLetzteReklamation($teil, 3,'E%');
	$teileArray[$teil]['rekl']['I'] = $a->getLetzteReklamation($teil, 3,'I%');
    }


    $abgnrKeysArray = array_keys($abgnrArray);
    sort($abgnrKeysArray);
    $terminKeysArray = array_keys($terminArray);
    sort($terminKeysArray);

    $zeilenArray = array();
    foreach ($zeilen as $termin => $teile) {
	$sumTermin = array();
	foreach ($teile as $teil => $importe) {
	    $teilSummen = array();
	    foreach ($importe as $import => $paletten) {
		foreach ($paletten as $pal => $palInfoArray) {
		    array_push($zeilenArray, array("section"=>"detail","termin" => $termin, "giesstag"=>$palInfoArray['gt'],"import_datum"=>$palInfoArray['import_datum'],"import" => $import, "teil" => $teil, "pal" => $pal, "palInfo" => $palInfoArray));
		    $sumReport['sum_im_stk']+=$palInfoArray['sum_im_stk'];
		    $sumReport['sum_im_gew']+=$palInfoArray['sum_im_gew'];
		    $sumTermin[$termin]['sum_im_gew']+=$palInfoArray['sum_im_gew'];
		    foreach ($palInfoArray as $klic=>$tatArray){
			if(!is_array($tatArray)){
			    $teilSummen[$klic] += floatval($palInfoArray[$klic]);
			}
			else{
			    foreach ($tatArray as $tat=>$val){
				$sumReport['soll'][$tat]+=$val;
				$sumReport['soll'][$klic][$tat]+=$val;
				$sumTermin[$termin]['soll'][$tat]+=$val;
				$sumTermin[$termin]['soll'][$klic][$tat]+=$val;
//				$teilSummen["sum_vzkd"] += $val;
				$teilSummen[$tat] += $val;
				$teilSummen[$klic][$tat] += $val;
			    }
			}
		    }
		}
	    }
	    array_push($zeilenArray, array("section"=>"sumteil","termin" => $termin, "import" => $import, "teil" => $teil, "pal" => $pal, "palInfo" => $teilSummen));
	    array_push($zeilenArray, array("section"=>"sumteilmin","termin" => $termin, "import" => $import, "teil" => $teil, "pal" => $pal, "palInfo" => $teilSummen));
	    array_push($zeilenArray, array("section"=>"sumteiloddelovac","termin" => $termin, "import" => $import, "teil" => $teil, "pal" => $pal, "palInfo" => $teilSummen));
	}
	array_push($zeilenArray, array("section"=>"sumtermin","termin" => $termin,"palInfo" => $sumTermin));
    }
}

if ($rowsD != NULL) {
    foreach ($rowsD as $r) {
	$termin = $r['termin'];
	$import = $r['auftragsnr'];
	$teil = $r['teil'];
	$pal = $r['im_pal'];
	$abgnr = $r['abgnr'];
	//$abgnrArray[$abgnr] += 1;

	$zeilenD[$termin][$teil][$import][$pal][$abgnr]['sum_vzkd'] = $r['sum_vzkd'];
	$zeilenD[$termin][$teil][$import][$pal][$abgnr]['sum_vzaby'] = $r['sum_vzaby'];
	$zeilenD[$termin][$teil][$import][$pal][$abgnr]['sum_gut_stk'] = $r['sum_gut_stk'];
	$zeilenD[$termin][$teil][$import][$pal]['sum_G_stk'] += $r['sum_G_stk'];
    }

    $zeilenDArray = array();
    foreach ($zeilenD as $termin => $teile) {
	$sumTermin = array();
	foreach ($teile as $teil => $importe) {
	    $teilSummen = array();
	    foreach ($importe as $import => $paletten) {
		foreach ($paletten as $pal => $palInfoArray) {
		    array_push($zeilenDArray, array("section" => "detail", "termin" => $termin, "import" => $import, "teil" => $teil, "pal" => $pal, "palInfo" => $palInfoArray));
		    foreach ($palInfoArray as $klic => $tatArray) {
			if (!is_array($tatArray)) {
			    $teilSummen[$klic] += floatval($palInfoArray[$klic]);
			} else {
			    foreach ($tatArray as $tat => $val) {
				$sumReport['ist'][$tat]+=$val;
				$sumReport['ist'][$klic][$tat]+=$val;
				$sumTermin[$termin]['ist'][$tat]+=$val;
				$sumTermin[$termin]['ist'][$klic][$tat]+=$val;
//				$teilSummen["sum_vzkd"] += $val;
				$teilSummen[$tat] += $val;
				$teilSummen[$klic][$tat] += $val;
			    }
			}
		    }
		}
	    }
	    array_push($zeilenDArray, array("section" => "sumteil", "termin" => $termin, "import" => $import, "teil" => $teil, "pal" => $pal, "palInfo" => $teilSummen));
	    array_push($zeilenDArray, array("section" => "sumteilmin", "termin" => $termin, "import" => $import, "teil" => $teil, "pal" => $pal, "palInfo" => $teilSummen));
	    array_push($zeilenDArray, array("section" => "sumteiloddelovac", "termin" => $termin, "import" => $import, "teil" => $teil, "pal" => $pal, "palInfo" => $teilSummen));
	}
	array_push($zeilenDArray, array("section"=>"sumtermin","termin" => $termin,"palInfo" => $sumTermin));
    }
}

    if ($rowsDA != NULL) {
    foreach ($rowsDA as $r) {
	$termin = $r['termin'];
	$import = $r['auftragsnr'];
	$teil = $r['teil'];
	$pal = $r['im_pal'];
	$aart = $r['aart'];
	if($aart<>0){
	    $aartArray[$aart] += 1;
	}
	

	$zeilenDA[$termin][$teil][$import][$pal][$aart]['sum_auss_stk'] = $r['sum_auss_stk'];
    }

    $aartKeysArray = array_keys($aartArray);
    sort($aartKeysArray);

    $zeilenDAArray = array();
    foreach ($zeilenDA as $termin => $teile) {
	$sumTermin = array();
	foreach ($teile as $teil => $importe) {
	    $teilSummen = array();
	    foreach ($importe as $import => $paletten) {
		foreach ($paletten as $pal => $palInfoArray) {
		    array_push($zeilenDAArray, array("termin" => $termin, "import" => $import, "teil" => $teil, "pal" => $pal, "palInfo" => $palInfoArray));
		    foreach ($palInfoArray as $klic => $tatArray) {
			if (!is_array($tatArray)) {
			    $teilSummen[$klic] += floatval($palInfoArray[$klic]);
			    $sumReport['auss'][$klic] += floatval($palInfoArray[$klic]);
			} else {
			    foreach ($tatArray as $tat => $val) {
				$teilSummen[$klic][$tat] += $val;
				$sumReport['auss'][$klic][$tat] += $val;
			    }
			}
		    }
		}
	    }
	    array_push($zeilenDAArray, array("section"=>"sumteil","termin" => $termin, "import" => $import, "teil" => $teil, "pal" => $pal, "palInfo" => $teilSummen));
	    array_push($zeilenDAArray, array("section"=>"sumteilmin","termin" => $termin, "import" => $import, "teil" => $teil, "pal" => $pal, "palInfo" => $teilSummen));
	    array_push($zeilenDAArray, array("section"=>"sumteiloddelovac","termin" => $termin, "import" => $import, "teil" => $teil, "pal" => $pal, "palInfo" => $teilSummen));
	}
	array_push($zeilenDAArray, array("section"=>"sumtermin","termin" => $termin,"palInfo" => $sumTermin));
    }
}

$returnArray = array(
    'von'=>$terminVon,
    'bis'=>$terminBis,
    'von1'=>$terminVon1,
    'bis1'=>$terminBis1,
    "sumReport"=>$sumReport,
    "zeilen" => $zeilenArray,
    "zeilenD" => $zeilenDArray,
    "zeilenDA" => $zeilenDAArray,
    "abgnrKeysArray" => $abgnrKeysArray,
    "aartKeysArray" => $aartKeysArray,
    "terminKeysArray" => $terminKeysArray,
    "terminArray"=>$terminArray,
    "termin"=>$terminMatch,
    "sql"=>$sql,
    "teileArray"=>$teileArray,
);

echo json_encode($returnArray);

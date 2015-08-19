<?php

require_once '../db.php';

$inputData = $_GET;

// zjistim zda mam hodnotu value ulozenou v databazi artiklu

$a = AplDB::getInstance();

$terminMatch = trim($_GET['termin']);
$importMatch = trim($_GET['import']);
$teilMatch = trim($_GET['teil']);

$sql.=" select ";
$sql.="     dauftr.termin,";
$sql.="     dauftr.auftragsnr,";
$sql.="     dauftr.teil,";
$sql.="     dauftr.`pos-pal-nr` as im_pal,";
$sql.="     dauftr.abgnr,";
$sql.="     sum(if(kzgut='G',dauftr.`stück`,0)) as sum_im_stk,";
$sql.="     sum(dauftr.VzKd*dauftr.`stück`) as sum_vzkd";
$sql.=" from dauftr";
$sql.=" where ";
$sql.="     dauftr.termin like 'P$terminMatch%'";
if(strlen(trim($importMatch))>0){
    $sql.="     and dauftr.auftragsnr like '%$importMatch%'";    
}
if(strlen(trim($teilMatch))>0){
    $sql.="     and dauftr.teil like '%$teilMatch%'";    
}

$sql.="     and dauftr.`auftragsnr-exp` is null";
$sql.=" group by";
$sql.="     dauftr.termin,";
$sql.="     dauftr.auftragsnr,";
$sql.="     dauftr.teil,";
$sql.="     dauftr.`pos-pal-nr`,";
$sql.="     dauftr.abgnr";
$sql.=" HAVING ";
$sql.="     sum_vzkd>0";
$sql.="     and abgnr<>95";

$sqlD.=" select ";
$sqlD.="     dauftr.termin,";
$sqlD.="     dauftr.auftragsnr,";
$sqlD.="     dauftr.teil,";
$sqlD.="     dauftr.`pos-pal-nr` as im_pal,";
$sqlD.="     dauftr.abgnr,";
$sqlD.="     sum(if(drueck.`Stück` is null,0,drueck.`Stück`)) as sum_gut_stk";
$sqlD.=" from dauftr";
$sqlD.=" left join drueck on drueck.AuftragsNr=dauftr.auftragsnr and drueck.Teil=dauftr.teil and drueck.`pos-pal-nr`=dauftr.`pos-pal-nr` and drueck.TaetNr=dauftr.abgnr";
$sqlD.=" where ";
$sqlD.="     dauftr.termin like 'P$terminMatch%'";
if(strlen(trim($importMatch))>0){
    $sqlD.="     and dauftr.auftragsnr like '%$importMatch%'";    
}
if(strlen(trim($teilMatch))>0){
    $sqlD.="     and dauftr.teil like '%$teilMatch%'";    
}

$sqlD.="     and dauftr.`auftragsnr-exp` is null";
$sqlD.=" group by";
$sqlD.="     dauftr.termin,";
$sqlD.="     dauftr.auftragsnr,";
$sqlD.="     dauftr.teil,";
$sqlD.="     dauftr.`pos-pal-nr`,";
$sqlD.="     dauftr.abgnr";

if (strlen($terminMatch)>=3) {
    $rows = $a->getQueryRows($sql);
    $rowsD = $a->getQueryRows($sqlD);
}

if ($rows != NULL) {
    foreach ($rows as $r) {
	$termin = $r['termin'];
	$import = $r['auftragsnr'];
	$teil = $r['teil'];
	$pal = $r['im_pal'];
	$abgnr = $r['abgnr'];
	$abgnrArray[$abgnr] += 1;

	$zeilen[$termin][$import][$teil][$pal][$abgnr]['sum_vzkd'] = $r['sum_vzkd'];
	$zeilen[$termin][$import][$teil][$pal]['sum_im_stk'] += $r['sum_im_stk'];
    }


    $abgnrKeysArray = array_keys($abgnrArray);
    sort($abgnrKeysArray);

    $zeilenArray = array();
    foreach ($zeilen as $termin => $importe) {
	foreach ($importe as $import => $teile) {
	    foreach ($teile as $teil => $paletten) {
		foreach ($paletten as $pal => $palInfoArray) {
		    array_push($zeilenArray, array("termin" => $termin, "import" => $import, "teil" => $teil, "pal" => $pal, "palInfo" => $palInfoArray));
		}
	    }
	}
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

	$zeilenD[$termin][$import][$teil][$pal][$abgnr]['sum_gut_stk'] = $r['sum_gut_stk'];
    }

    $zeilenDArray = array();
    foreach ($zeilenD as $termin => $importe) {
	foreach ($importe as $import => $teile) {
	    foreach ($teile as $teil => $paletten) {
		foreach ($paletten as $pal => $palInfoArray) {
		    array_push($zeilenDArray, array("termin" => $termin, "import" => $import, "teil" => $teil, "pal" => $pal, "palInfo" => $palInfoArray));
		}
	    }
	}
    }
}

$returnArray = array(
    "inputData" => $inputData,
    "zeilen" => $zeilenArray,
    "zeilenD" => $zeilenDArray,
    "abgnrKeysArray" => $abgnrKeysArray,
    "termin"=>$terminMatch,
);

echo json_encode($returnArray);

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
$sql.="     sum(if(kzgut='G',dauftr.`stück`*dkopf.gew/1000,0)) as sum_im_gew,";
$sql.="     max(if(kzgut='G',dauftr.bemerkung,'')) as bemerkung,";
$sql.="     dauftr.kzgut,";
$sql.="     sum(dauftr.VzKd*dauftr.`stück`) as sum_vzkd";
$sql.=" from dauftr";
$sql.=" join dkopf on dkopf.teil=dauftr.teil";
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
$sql.=" order by";
$sql.="     dauftr.termin,";
$sql.="     dauftr.teil,";
$sql.="     dauftr.auftragsnr,";
$sql.="     dauftr.`pos-pal-nr`,";
$sql.="     dauftr.abgnr";

$sqlD.=" select ";
$sqlD.="     dauftr.termin,";
$sqlD.="     dauftr.auftragsnr,";
$sqlD.="     dauftr.teil,";
$sqlD.="     dauftr.`pos-pal-nr` as im_pal,";
$sqlD.="     dauftr.abgnr,";
$sqlD.="     sum(if(dauftr.kzgut='G' and drueck.`Stück` is not null,drueck.`stück`,0)) as sum_G_stk,";
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
$sqlD.=" order by";
$sqlD.="     dauftr.termin,";
$sqlD.="     dauftr.teil,";
$sqlD.="     dauftr.auftragsnr,";
$sqlD.="     dauftr.`pos-pal-nr`,";
$sqlD.="     dauftr.abgnr";

$sqlDA.=" select ";
$sqlDA.="     dauftr.termin,";
$sqlDA.="     dauftr.auftragsnr,";
$sqlDA.="     dauftr.teil,";
$sqlDA.="     dauftr.`pos-pal-nr` as im_pal,";
$sqlDA.="     if(drueck.`auss-art` is null,0,drueck.`auss-art`) as aart,     ";
$sqlDA.="     sum(if(drueck.`Auss-Stück` is null,0,drueck.`Auss-Stück`)) as sum_auss_stk";
$sqlDA.=" from dauftr";
$sqlDA.=" left join drueck on drueck.AuftragsNr=dauftr.auftragsnr and drueck.Teil=dauftr.teil and drueck.`pos-pal-nr`=dauftr.`pos-pal-nr` and drueck.TaetNr=dauftr.abgnr";
$sqlDA.=" where ";
$sqlDA.="     dauftr.termin like 'P$terminMatch%'";
if(strlen(trim($importMatch))>0){
    $sqlDA.="     and dauftr.auftragsnr like '%$importMatch%'";    
}
if(strlen(trim($teilMatch))>0){
    $sqlDA.="     and dauftr.teil like '%$teilMatch%'";    
}

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

if (strlen($terminMatch)>=3) {
    $rows = $a->getQueryRows($sql);
    $rowsD = $a->getQueryRows($sqlD);
    $rowsDA = $a->getQueryRows($sqlDA);
}

if ($rows != NULL) {
    foreach ($rows as $r) {
	$termin = $r['termin'];
	$import = $r['auftragsnr'];
	$teil = $r['teil'];
	$pal = $r['im_pal'];
	$abgnr = $r['abgnr'];
	$abgnrArray[$abgnr] += 1;
	$terminArray[$termin] += 1;

	$zeilen[$termin][$teil][$import][$pal][$abgnr]['sum_vzkd'] = $r['sum_vzkd'];
	$zeilen[$termin][$teil][$import][$pal]['sum_im_stk'] += $r['sum_im_stk'];
	$zeilen[$termin][$teil][$import][$pal]['sum_im_gew'] += $r['sum_im_gew'];
	if(trim($r['kzgut'])=='G'){
	    $zeilen[$termin][$teil][$import][$pal]['bemerkung'] = strip_tags(trim($r['bemerkung']));
	}
    }


    $abgnrKeysArray = array_keys($abgnrArray);
    sort($abgnrKeysArray);
    $terminKeysArray = array_keys($terminArray);
    sort($terminKeysArray);

    $zeilenArray = array();
    foreach ($zeilen as $termin => $teile) {
	foreach ($teile as $teil => $importe) {
	    $teilSummen = array();
	    foreach ($importe as $import => $paletten) {
		foreach ($paletten as $pal => $palInfoArray) {
		    array_push($zeilenArray, array("section"=>"detail","termin" => $termin, "import" => $import, "teil" => $teil, "pal" => $pal, "palInfo" => $palInfoArray));
		    foreach ($palInfoArray as $klic=>$tatArray){
			if(!is_array($tatArray)){
			    $teilSummen[$klic] += floatval($palInfoArray[$klic]);
			}
			else{
			    foreach ($tatArray as $tat=>$val){
				$teilSummen["sum_vzkd"] += $val;
			    }
			}
		    }
		}
	    }
	    array_push($zeilenArray, array("section"=>"sumteil","termin" => $termin, "import" => $import, "teil" => $teil, "pal" => $pal, "palInfo" => $teilSummen));
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

	$zeilenD[$termin][$teil][$import][$pal][$abgnr]['sum_gut_stk'] = $r['sum_gut_stk'];
	$zeilenD[$termin][$teil][$import][$pal]['sum_G_stk'] += $r['sum_G_stk'];
    }

    $zeilenDArray = array();
    foreach ($zeilenD as $termin => $teile) {
	foreach ($teile as $teil => $importe) {
	    foreach ($importe as $import => $paletten) {
		foreach ($paletten as $pal => $palInfoArray) {
		    array_push($zeilenDArray, array("termin" => $termin, "import" => $import, "teil" => $teil, "pal" => $pal, "palInfo" => $palInfoArray));
		}
	    }
	    array_push($zeilenDArray, array("section"=>"sumteil","termin" => $termin, "import" => $import, "teil" => $teil, "pal" => $pal, "palInfo" => $palInfoArray));
	}
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
	foreach ($teile as $teil => $importe) {
	    foreach ($importe as $import => $paletten) {
		foreach ($paletten as $pal => $palInfoArray) {
		    array_push($zeilenDAArray, array("termin" => $termin, "import" => $import, "teil" => $teil, "pal" => $pal, "palInfo" => $palInfoArray));
		}
	    }
	    array_push($zeilenDAArray, array("section"=>"sumteil","termin" => $termin, "import" => $import, "teil" => $teil, "pal" => $pal, "palInfo" => $palInfoArray));
	}
    }
}

$returnArray = array(
    "inputData" => $inputData,
    "zeilen" => $zeilenArray,
    "zeilenRaw" => $zeilen,
    "zeilenD" => $zeilenDArray,
    "zeilenDA" => $zeilenDAArray,
    "abgnrKeysArray" => $abgnrKeysArray,
    "aartKeysArray" => $aartKeysArray,
    "terminKeysArray" => $terminKeysArray,
    "termin"=>$terminMatch,
    "sql"=>$sql,
    "sqlDA"=>$sqlDA,
);

echo json_encode($returnArray);

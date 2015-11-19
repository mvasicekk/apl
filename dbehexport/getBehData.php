<?

require_once '../db.php';

$t = $_GET['t'];
$ex = $_GET['ex'];


// zjistim zda mam hodnotu value ulozenou v databazi artiklu

$apl = AplDB::getInstance();
$a = $apl;

$behArray = NULL;
//$behArray = $apl->getBehDataArray($t);

$sql.=" select ";
$sql.="     dauftr.teil,";
$sql.="     dauftr.auftragsnr,";
$sql.="     dauftr.termin,";
$sql.="     DATE_FORMAT(daufkopf.aufdat,'%d.%m.%y') as import_datum,";
$sql.="     dauftr.`pos-pal-nr` as im_pal,";
$sql.="     dauftr.abgnr,";
$sql.="     sum(if(kzgut='G',dauftr.`stück`,0)) as sum_im_stk,";
$sql.="     sum(if(kzgut='G',dauftr.`stück`*dkopf.gew/1000,0)) as sum_im_gew,";
$sql.="     dauftr.kzgut";
$sql.=" from dauftr";
$sql.=" join dkopf on dkopf.teil=dauftr.teil";
$sql.=" join daufkopf on daufkopf.auftragsnr=dauftr.auftragsnr";
$sql.=" where (1)";
$sql.="     and dauftr.teil='$t'";
$sql.="     and dauftr.`auftragsnr-exp` is null";
$sql.=" group by";
$sql.="     dauftr.teil,";
$sql.="     dauftr.auftragsnr,";
$sql.="     dauftr.`pos-pal-nr`,";
$sql.="     dauftr.abgnr";


$sqlD.=" select ";
$sqlD.="     dauftr.teil,";
$sqlD.="     dauftr.auftragsnr,";
$sqlD.="     dauftr.`pos-pal-nr` as im_pal,";
$sqlD.="     dauftr.abgnr,";
$sqlD.="     sum(if(dauftr.kzgut='G' and drueck.`Stück` is not null,drueck.`stück`,0)) as sum_G_stk,";
$sqlD.="     sum(if(drueck.`Stück` is null,0,drueck.`Stück`)) as sum_gut_stk";
$sqlD.=" from dauftr";
$sqlD.=" join daufkopf on daufkopf.auftragsnr=dauftr.auftragsnr";
$sqlD.=" left join drueck on drueck.AuftragsNr=dauftr.auftragsnr and drueck.Teil=dauftr.teil and drueck.`pos-pal-nr`=dauftr.`pos-pal-nr` and drueck.TaetNr=dauftr.abgnr";
$sqlD.=" where (1)";
$sqlD.="     and dauftr.teil='$t'";
$sqlD.="     and dauftr.`auftragsnr-exp` is null";
$sqlD.=" group by";
$sqlD.="     dauftr.teil,";
$sqlD.="     dauftr.auftragsnr,";
$sqlD.="     dauftr.`pos-pal-nr`,";
$sqlD.="     dauftr.abgnr";

$sqlDA.=" select ";
$sqlDA.="     dauftr.teil,";
$sqlDA.="     dauftr.auftragsnr,";
$sqlDA.="     dauftr.`pos-pal-nr` as im_pal,";
$sqlDA.="     if(drueck.`auss-art` is null,0,drueck.`auss-art`) as aart,     ";
$sqlDA.="     sum(if(drueck.`Auss-Stück` is null,0,drueck.`Auss-Stück`)) as sum_auss_stk";
$sqlDA.=" from dauftr";
$sqlDA.=" join daufkopf on daufkopf.auftragsnr=dauftr.auftragsnr";
$sqlDA.=" left join drueck on drueck.AuftragsNr=dauftr.auftragsnr and drueck.Teil=dauftr.teil and drueck.`pos-pal-nr`=dauftr.`pos-pal-nr` and drueck.TaetNr=dauftr.abgnr";
$sqlDA.=" where (1)";
$sqlDA.="     and dauftr.teil='$t'";
$sqlDA.="     and dauftr.`auftragsnr-exp` is null";
$sqlDA.=" group by";
$sqlDA.="     dauftr.teil,";
$sqlDA.="     dauftr.auftragsnr,";
$sqlDA.="     dauftr.`pos-pal-nr`,";
$sqlDA.="     if(drueck.`auss-art` is null,0,drueck.`auss-art`)";
$sqlDA.=" having";
$sqlDA.="     (aart=0)";
$sqlDA.="     or (aart<>0 and sum_auss_stk<>0)";


$aartArray = array();

if (strlen($t) > 0) {
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
	$imdat = $r['import_datum'];
	$abgnr = $r['abgnr'];
	$abgnrArray[$abgnr] += 1;
	$zeilen[$teil][$import][$pal][$abgnr]['sum_vzkd'] = $r['sum_vzkd'];
	$zeilen[$teil][$import][$pal][$abgnr]['sum_vzaby'] = $r['sum_vzaby'];
	$zeilen[$teil][$import][$pal]['sum_im_stk'] += $r['sum_im_stk'];
	$zeilen[$teil][$import][$pal]['sum_im_gew'] += $r['sum_im_gew'];
	$zeilen[$teil][$import][$pal]["import_datum"] = $imdat;
    }

    $abgnrKeysArray = array_keys($abgnrArray);
    sort($abgnrKeysArray);

    $zeilenArray = array();
    foreach ($zeilen as $teil => $importe) {
	$teilSummen = array();
	foreach ($importe as $import => $paletten) {
	    foreach ($paletten as $pal => $palInfoArray) {
		array_push($zeilenArray, array("section" => "detail", "import_datum" => $palInfoArray['import_datum'], "import" => $import, "teil" => $teil, "pal" => $pal, "termin" => $termin, "palInfo" => $palInfoArray));
		foreach ($palInfoArray as $klic => $tatArray) {
		    if (!is_array($tatArray)) {
			$teilSummen[$klic] += floatval($palInfoArray[$klic]);
		    } else {
			foreach ($tatArray as $tat => $val) {
			    $teilSummen[$tat] += $val;
			    $teilSummen[$klic][$tat] += $val;
			}
		    }
		}
	    }
	}
	array_push($zeilenArray, array("section" => "sumteil", "termin" => $termin, "import" => $import, "teil" => $teil, "pal" => $pal, "palInfo" => $teilSummen));
    }
}

// drueck

if ($rowsD != NULL) {
    foreach ($rowsD as $r) {
	$termin = $r['termin'];
	$import = $r['auftragsnr'];
	$teil = $r['teil'];
	$pal = $r['im_pal'];
	$abgnr = $r['abgnr'];
	$zeilenD[$teil][$import][$pal][$abgnr]['sum_vzkd'] = $r['sum_vzkd'];
	$zeilenD[$teil][$import][$pal][$abgnr]['sum_vzaby'] = $r['sum_vzaby'];
	$zeilenD[$teil][$import][$pal][$abgnr]['sum_gut_stk'] = $r['sum_gut_stk'];
	$zeilenD[$teil][$import][$pal]['sum_G_stk'] += $r['sum_G_stk'];
    }

    $zeilenDArray = array();
    foreach ($zeilenD as $teil => $importe) {
	$teilSummen = array();
	foreach ($importe as $import => $paletten) {
	    foreach ($paletten as $pal => $palInfoArray) {
		array_push($zeilenDArray, array("section" => "detail", "termin" => $termin, "import" => $import, "teil" => $teil, "pal" => $pal, "palInfo" => $palInfoArray));
		foreach ($palInfoArray as $klic => $tatArray) {
		    if (!is_array($tatArray)) {
			$teilSummen[$klic] += floatval($palInfoArray[$klic]);
		    } else {
			foreach ($tatArray as $tat => $val) {
			    $teilSummen[$tat] += $val;
			    $teilSummen[$klic][$tat] += $val;
			}
		    }
		}
	    }
	}
	array_push($zeilenDArray, array("section" => "sumteil", "termin" => $termin, "import" => $import, "teil" => $teil, "pal" => $pal, "palInfo" => $teilSummen));
    }
}

// Ausschuss
if ($rowsDA != NULL) {
    foreach ($rowsDA as $r) {
	$termin = $r['termin'];
	$import = $r['auftragsnr'];
	$teil = $r['teil'];
	$pal = $r['im_pal'];
	$aart = $r['aart'];
	if ($aart <> 0) {
	    $aartArray[$aart] += 1;
	}


	$zeilenDA[$teil][$import][$pal][$aart]['sum_auss_stk'] = $r['sum_auss_stk'];
    }

    $aartKeysArray = array_keys($aartArray);
    sort($aartKeysArray);

    $zeilenDAArray = array();
    foreach ($zeilenDA as $teil => $importe) {
	$teilSummen = array();
	foreach ($importe as $import => $paletten) {
	    foreach ($paletten as $pal => $palInfoArray) {
		array_push($zeilenDAArray, array("section" => 'detail', "termin" => $termin, "import" => $import, "teil" => $teil, "pal" => $pal, "palInfo" => $palInfoArray));
		foreach ($palInfoArray as $klic => $tatArray) {
		    if (!is_array($tatArray)) {
			$teilSummen[$klic] += floatval($palInfoArray[$klic]);
		    } else {
			foreach ($tatArray as $tat => $val) {
			    $teilSummen[$klic][$tat] += $val;
			}
		    }
		}
	    }
	}
	array_push($zeilenDAArray, array("section" => "sumteil", "termin" => $termin, "import" => $import, "teil" => $teil, "pal" => $pal, "palInfo" => $teilSummen));
    }
}


$sqlBehArray = "select * from dbehexport where dbehexport.teil='$t' and dbehexport.export='$ex' order by gedruckt_am,teil,ex_pal";
$behArray = $apl->getQueryRows($sqlBehArray);
//zjistim kolik mam pozic bez datumu, abych mohl povolit/zakazat tlacitko pro tisk - tisknu jen pozice bez datumu
$nochNichtGedruckt = 0;
if($behArray!==NULL){
    foreach ($behArray as $beh){
	$gedrucktAm = trim($beh['gedruckt_am']);
	if(strlen($gedrucktAm)==0){
	    $nochNichtGedruckt++;
	}
    }
}

$returnArray = array(
    't' => $t,
    'abgnrKeysArray' => $abgnrKeysArray,
    'aartKeysArray'=>$aartKeysArray,
    'behArray' => $behArray,
    'rows' => $rows,
    'rowsD' => $rowsD,
    'rowsDA' => $rowsDA,
    'zeilenArray' => $zeilenArray,
    'zeilenDArray' => $zeilenDArray,
    'zeilenDAArray' => $zeilenDAArray,
    'nochNichtGedruckt'=>$nochNichtGedruckt,
);
echo json_encode($returnArray);

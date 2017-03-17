<?

session_start();
require_once '../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);

$a = AplDB::getInstance();
$ar = 0;
$auftragsnr = $o->params->r->auftragsnr;
$dauftr_id = $o->params->r->id_dauftr;
$teil = trim($o->params->r->teil);
$dil = $teil;
$pos_pal_nr = chop($o->params->r->imp_pal);
$stk = chop($o->params->r->imp_stk);
$termin = chop($o->params->r->termin);
$auftragsnr_exp = chop($o->params->r->ex);

if (strlen(trim($auftragsnr_exp)) == 0) {
    $auftragsnr_exp = 'NULL';
}
$pos_pal_nr_exp = chop($o->params->r->palex);
if (strlen($pos_pal_nr_exp) == 0) {
    $pos_pal_nr_exp = 'NULL';
}
$stk_exp = chop($o->params->r->stkex);
if (strlen($stk_exp) == 0) {
    $stk_exp = 'NULL';
}
$fremdauftr = chop($o->params->r->fremdauftr);
$fremdpos = chop($o->params->r->fremdpos);
$gt = trim($o->params->r->giesstag);
$bemerkung = trim($o->params->r->bemerkung);
$KzGut = chop($o->params->r->KzGut);
//cokoliv jineho nez mezeru nahradim pismenem G
if (strlen($KzGut) > 0) {
    $KzGut = 'G';
}

$dauftrRow = $a->getDauftrRow($dauftr_id);
$auftragsnrExpDB = $dauftrRow['ex'];
$expStkDB = $dauftrRow['ex_stk'];
$pocitac = $_SERVER["REMOTE_ADDR"];
$ident = $a->get_user_pc();
$user = $ident;

// $termin rozkopirovat do vsech pozic pro danou paletu
// $auftragsnr_exp
// $pos_pal_nr_exp
// $fremdauftr
// $fremdpos
// podle dauftr_id si zjistim auftrag atd

$invDatum = "";

// 2017-03-15
// kontrola pokud ma zadany ex uz fakturu , nepovolim jeho zadani
$exHatRechnungOrNotExists = TRUE;
if ($auftragsnr_exp == 'NULL') {
    $exHatRechnungOrNotExists = FALSE;
} else {
    $exInfo = $a->getAuftragInfoArray($auftragsnr_exp);
    if ($exInfo !== NULL) {
	if ($exInfo[0]['hatrechnung'] == '0') {
	    $exHatRechnungOrNotExists = FALSE;
	}
    }
}

if (!$exHatRechnungOrNotExists) {
    if ($KzGut == 'G') {
	$myerror = $a->updateDauftr_Termin_AuftragsnrExp_PalExp_fremdauftr_fremdpos($stk, $termin, $auftragsnr_exp, $pos_pal_nr_exp, $fremdauftr, $fremdpos, $dauftr_id, $gt, $user, $bemerkung);
	// zjistitit, zda uz dil nahodou nemel inventuru
	$invDatum = $a->getInventurDatumForTeil($a->getTeilFromDauftrId($dauftr_id));
	$dauftrStampRow = $a->getDauftrRow($dauftr_id);
	$dauftrStamp = $dauftrStampRow['stamp1'];
	$invtime = strtotime($invDatum);
	$dauftrtime = strtotime($dauftrStamp);
	if ($invtime > $dauftrtime) {
	    $timeBeachten = 1;
	} else {
	    $timeBeachten = 0;
	}
	// 2014-02-18
	// pri vkladani do dlagerbew pripravid debug retez pro zobrazeni v debug okne
	// podle toho, kterou casti podminky projdu, vytvorim debug retez
	// zobrazeni info okna bude pomoc jquery-ui
	// 2014-02-05
	// podle puvodniho obsahu musim rozhodnout, co udelat s polozkami v dlagerbew
	$strlenExDB = strlen(trim($auftragsnrExpDB));
	$strlenEx = strlen(trim($auftragsnr_exp));
	if ($auftragsnr_exp == "NULL") {
	    $strlenEx = 0;
	}
	// 1, ex geloescht -> storno v dlagerbew
	$storno = 0;
	if (($strlenExDB > 0) && ($auftragsnr_exp == 'NULL')) {
	    $storno = 1;
	    $a->stornoLastDlagerBewExport($dauftrRow['auftragsnr'], $dauftrRow['pal'], $dauftrRow['teil'], $ident);
	} else if (($strlenExDB == 0) && ($strlenEx) > 0) {
	    // 2, vyplnen prazdny export, pohyb v dlagerbew jako u export fullen
	    $gut = intval($stk_exp);
	    $a->insertDlagerBew($dauftrRow['teil'], $dauftrRow['auftragsnr'], $dauftrRow['pal'], $gut, 0, "8E", "8X", $ident);
	    // presun do dummy lagru, aby mi nezbyvalo v prvnim skladu
	    $a->insertDlagerBewXXDummy($dauftrRow['teil'], $dauftrRow['auftragsnr'], $dauftrRow['pal'], $ident);
	    // presun zmetku ve vyrobe do zmetku vyexportovanych, pocty si beruz tabulky drueck
	    $a->moveAussLagerA2B($dauftrRow['auftragsnr'], $dauftrRow['pal'], $dauftrRow['teil'], $ident);
	} else if (($strlenExDB > 0) && ($strlenEx > 0) && (intval($stk_exp) != intval($expStkDB))) {
	    // 3, zmena poctu kusu -> storno + export fullen
	    // storno
	    $a->stornoLastDlagerBewExport($dauftrRow['auftragsnr'], $dauftrRow['pal'], $dauftrRow['teil'], $ident);
	    //insert
	    $gut = intval($stk_exp);
	    $a->insertDlagerBew($dauftrRow['teil'], $dauftrRow['auftragsnr'], $dauftrRow['pal'], $gut, 0, "8E", "8X", $ident);
	    // presun do dummy lagru, aby mi nezbyvalo v prvnim skladu
	    $a->insertDlagerBewXXDummy($dauftrRow['teil'], $dauftrRow['auftragsnr'], $dauftrRow['pal'], $ident);
	    // presun zmetku ve vyrobe do zmetku vyexportovanych, pocty si beruz tabulky drueck
	    $a->moveAussLagerA2B($dauftrRow['auftragsnr'], $dauftrRow['pal'], $dauftrRow['teil'], $ident);
	}
    }



    $sql = "update dauftr";
    $sql.=" set ";
    $sql.=" `Stück`='" . $stk . "',";
    $sql.=" `Termin`='" . $termin . "',";
    if (strlen($pos_pal_nr) > 0) {
	$sql.=" `pos-pal-nr`='" . $pos_pal_nr . "',";
    }
    $sql.=" `auftragsnr-exp`=" . $auftragsnr_exp . ",";
    $sql.=" `pal-nr-exp`=" . $pos_pal_nr_exp . ",";
    $sql.=" `stk-exp`=" . $stk_exp . ",";
    $sql.=" `fremdauftr`='" . $fremdauftr . "',";
    $sql.=" `fremdpos`='" . $fremdpos . "',";
    $sql.=" `giesstag`='" . $gt . "',";
    $sql.=" `bemerkung`='" . $bemerkung . "',";
    $sql.=" `KzGut`='" . $KzGut . "',";
    $sql.=" `comp_user_accessuser`='" . $ident . "'";
    $sql.=" where (id_dauftr=" . $dauftr_id . ") limit 1";

    $ar = $a->query($sql);
}


// vztahnu updatnute radky -----------------------------------------------------
$dauftrPos = $a->getDauftrRowsForImport($auftragsnr);
if ($dauftrPos !== NULL) {
    $oldpal = $dauftrPos[0]['imp_pal'];
    foreach ($dauftrPos as $p => $row) {
	//zjistim zda ma exportni cislo u pozice fakturu
	$ex = $row['ex'];
	$hatRechnung = 0;
	if (strlen(trim($ex)) > 0) {
	    $exInfoArray = $a->getAuftragInfoArray($ex);
	    if ($exInfoArray !== NULL) {
		$hatRechnung = $exInfoArray[0]['hatrechnung'];
	    } else {
		$hatRechnung = 0;
	    }
	}
	$dauftrPos[$p]['hatrechnung'] = $hatRechnung;
	$dauftrPos[$p]['edit'] = 0;
	if ($row['imp_pal'] != $oldpal) {
	    $dauftrPos[$p]['newpal'] = 1;
	    $oldpal = $row['imp_pal'];
	} else {
	    $dauftrPos[$p]['newpal'] = 0;
	}
    }
}

$returnArray = array(
    'ar' => $ar,
    'sql' => $sql,
    'dauftragPositionen' => $dauftrPos,
    'myerror' => $myerror,
    'exHatRechnungOrNotExists'=>$exHatRechnungOrNotExists
);

echo json_encode($returnArray);

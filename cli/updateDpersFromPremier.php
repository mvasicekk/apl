#!/usr/bin/php
<?php
require_once '/var/www/workspace/apl/db.php';
require '/var/www/workspace/apl/sqldb.php';

$a = AplDB::getInstance();

$ucetniJednotka = "";
if ($argc > 1) {
    $ucetniJednotka = trim($argv[1]);
}

//$ucetniJednotka = 'FA5';
if ($ucetniJednotka == "") {
    echo "----- START updateDpersFromPremier ($ucetniJednotka) on :" . date('Y-m-d H:i:s') . " ----- \n";
    echo "neni zadana ucetni jednotka, koncim\n";
    exit();
}

echo "----- START updateDpersFromPremier ($ucetniJednotka) on :" . date('Y-m-d H:i:s') . " ----- \n";
$sqlDB = sqldb::getInstance($ucetniJednotka);

//var_dump($sqlDB);
//pod
// cele pole pujde dolu
// vnitrni cyklus foreach

/**
 * vlozim informaci o smlouvach do stavajici struktury
 * pozdeji vyuzijeme jen strukturu z ISP
 * 
 */
function insertAplVertrag($ppRow, $persnr, $table = 'dpersvertrag_isp') {
    global $a;
    $sql = "insert into `$table` (";
    $sql.=" persnr";
    //$sql.=" ,eintritt";
    //$sql.=" ,austritt";
    $sql.=" ,befristet";
    $sql.=" ,probezeit";
    $sql.=" ,giltab";
    $sql.=" ,regelarbzeit";
    $sql.=" ,urlaub_jansp";
    $sql.=" ,urlaub_vjrest";
    $sql.=" ,urlaub_kor";
    $sql.=" ,vertrag_anfang";
    $sql.=" ,verlang";
    $sql.=" ,vertragtyp_id";
    $sql.=" ,isp_pp_cislo";
    $sql.=" ,isp_pp_kate";
    $sql.=" ,isp_pp_vstup";
    $sql.=" ,isp_pp_vystup";
    $sql.=" ,isp_dov_narok";
    $sql.=" ,isp_dov_narok_s";
    $sql.=" ,isp_dov_zust_min_rok";
    $sql.=" ,isp_uva_doba";
    $sql.=" ,isp_uva_typ_mzdy";
    $sql.=" ,isp_uva_plat_od";
    $sql.=" ,isp_sml_doba_urcita";
    $sql.=" ,isp_sml_dat_vystup";
    $sql.=")";
    $sql.=" values(";
    $sql.=" '" . $persnr . "'";
    //$sql.=" ,'".$ppRow['ppVstup']."'";
    //$sql.=" ,'".$ppRow['ppVystup']."'";
    $sql.=" ,null";
    $sql.=" ,null";
    $sql.=" ,null";
    $sql.=" ,8";
    $sql.=" ,'" . $ppRow['dovNarok'] . "'";
    $sql.=" ,'" . $ppRow['dovZusMinr'] . "'";
    $sql.=" ,0";
    $sql.=" ,0";
    $sql.=" ,0";
    $sql.=" ,1";
    $sql.=" ,'" . $ppRow['ppCislo'] . "'";
    $sql.=" ,'" . $ppRow['ppKate'] . "'";
    $sql.=" ,'" . $ppRow['ppVstup'] . "'";
    $sql.=" ,'" . $ppRow['ppVystup'] . "'";
    $sql.=" ,'" . $ppRow['dovNarok'] . "'";
    $sql.=" ,'" . $ppRow['dovNarokS'] . "'";
    $sql.=" ,'" . $ppRow['dovZusMinr'] . "'";
    $sql.=" ,'" . $ppRow['uvaDoba'] . "'";
    $sql.=" ,'" . $ppRow['uvaTypMzdy'] . "'";
    $sql.=" ,'" . $ppRow['uvaPlatOd'] . "'";
    $sql.=" ,'" . $ppRow['smlDobaUrcita'] . "'";
    $sql.=" ,'" . $ppRow['smlDatVystup'] . "'";
    $sql.=" )";
    //echo "\n $sql";
    $ins = $a->insert($sql);
    return $ins;
}

/** uprava dat poli **/
//var_dump($sqlDB);
/**
 * 
 * @param type $idVertrag
 * @param type $field
 * @param type $value
 */
function updateAplVertrag($idVertrag, $field, $value, $table = 'dpersvertrag_isp') {
    global $a;
    if($value==NULL){
	$sql = "update `$table` set `$field`=NULL where id='$idVertrag'";
    }
    else{
	$sql = "update `$table` set `$field`='$value' where id='$idVertrag'";
    }
    
    $ar = $a->query($sql);
    if ($ar > 0) {
	echo "\nUPDATEFIELD $field = $value for id=$idVertrag (ar=$ar),table = $table";
    }
}

/** zapnout */
/**
 * 
 * @global type $a
 * @param type $zCislo
 * @param type $field
 * @param type $value
 * @param type $oldValue
 * @param type $table
 */
function updateAplPersnr($zCislo, $field, $value, $oldValue, $table = 'dpers_isp') {
    global $a;
    $persnr = 0;
    if ($table == 'dpers_isp') {
	// jen v teto tabulce mam isp_cislo, muzu vybirat podle nej
	$sql = "update `$table` set `$field`='$value' where isp_cislo='$zCislo'";
//	$ar = $a->query($sql);
//	if ($ar > 0) {
//	    echo "\nUPDATEFIELD $field = $value (from $oldValue) for isp_cislo=$zCislo (ar=$ar),table = $table, persnr = $persnr";
//	}
    } else {
	// v ostatnich tabulkach mam jako primarni klic persnr, ten si musim zjistit posle isp_cislo
	$rs = $a->getQueryRows("select persnr from dpers_isp where isp_cislo='$zCislo'");
	if ($rs !== NULL) {
	    $persnr = $rs[0]['persnr'];
	}
	if ($persnr !== 0) {
	    $sql = "update `$table` set `$field`='$value' where persnr='$persnr'";
	}
    }

    // pocet ovlivnenych radku
    $ar = $a->query($sql);
    if ($ar > 0) {
	echo "\nUPDATEFIELD $field = $value (from $oldValue) for isp_cislo=$zCislo (ar=$ar),table = $table, persnr = $persnr";
    }
}

// vybrat vsechny lidi z premiera po 25 lidech, pokud ctu po vetsim mnozstvi dostanu prazdne pole
$zCisloOd = 1;$pocetLidi = 25;
$zCisloDo = $zCisloOd+$pocetLidi;
$r1 = array();

$res = $sqlDB->getResult("SELECT * from fl_PERSONAL_APL_view where Z_CISLO between ".$zCisloOd." and ".$zCisloDo." order by Z_CISLO,PP_CISLO,PP_VSTUP");
$length = intval(sizeof($res));
while($length>0){
    foreach ($res as $r){
	array_push($r1, $r);
    }
    $zCisloOd+=$pocetLidi+1;
    $zCisloDo = $zCisloOd+$pocetLidi;
    $s = "SELECT * from fl_PERSONAL_APL_view where Z_CISLO between ".$zCisloOd." and ".$zCisloDo." order by Z_CISLO,PP_CISLO,PP_VSTUP";
    echo "s=$s\n\r";
    $res = $sqlDB->getResult($s);
    $length = intval(sizeof($res));
    echo "length=$length\n\r";
}
//$res = $sqlDB->getResult("select * from fl_PERSONAL_APL_view");
//$res = $sqlDB->getResult("select * from PER_MAIN");
//var_dump($r1);
echo "pocet nactenych radku z ISP :".count($r1);
//exit(1);
// polovicni pole

$persArray = array();
if (count($r1)>0) {
    foreach ($r1 as $r) {
	$zCislo = $ucetniJednotka . '_' . intval($r['Z_CISLO']);
//	echo "zCislo = $zCislo\n";
	if (!array_key_exists($zCislo, $persArray)) {
	    $persArray[$zCislo]['pp'] = array();
	    // persnr dwedhiwuedh d  wdhqwoeiduh
	}
	$persArray[$zCislo]['zPrijmeni'] = iconv('windows-1250', 'UTF-8', trim($r['Z_PRIJMENI']));
	$persArray[$zCislo]['zJmeno'] = iconv('windows-1250', 'UTF-8', trim($r['Z_JMENO']));
	$persArray[$zCislo]['zDatNar'] = strtotime($r['Z_DAT_NAR']) === FALSE ? '' : date('Y-m-d', strtotime($r['Z_DAT_NAR']));
	$persArray[$zCislo]['zMistoNar'] = iconv('windows-1250', 'UTF-8', trim($r['Z_MISTO_NAR']));
	$persArray[$zCislo]['zStObc'] = iconv('windows-1250', 'UTF-8', trim($r['Z_ST_OBC']));
	$persArray[$zCislo]['zMobil'] = iconv('windows-1250', 'UTF-8', trim($r['Z_MOBIL']));
	$persArray[$zCislo]['zTelefon'] = iconv('windows-1250', 'UTF-8', trim($r['Z_TELEFON']));
	$persArray[$zCislo]['zEmail'] = iconv('windows-1250', 'UTF-8', trim($r['Z_EMAIL']));
	$persArray[$zCislo]['zPsTime'] = strtotime($r['Z_PS_TIME']) === FALSE ? '' : date('Y-m-d H:i:s', strtotime($r['Z_PS_TIME']));
	$persArray[$zCislo]['kUlice'] = iconv('windows-1250', 'UTF-8', trim($r['K_ULICE']));
	$persArray[$zCislo]['kMisto'] = iconv('windows-1250', 'UTF-8', trim($r['K_MISTO']));
	$persArray[$zCislo]['kPsc'] = iconv('windows-1250', 'UTF-8', trim($r['K_PSC']));
	$persArray[$zCislo]['kStat'] = iconv('windows-1250', 'UTF-8', trim($r['K_STAT']));
	$persArray[$zCislo]['tUlice'] = iconv('windows-1250', 'UTF-8', trim($r['T_ULICE']));
	$persArray[$zCislo]['tMisto'] = iconv('windows-1250', 'UTF-8', trim($r['T_MISTO']));
	$persArray[$zCislo]['tPsc'] = iconv('windows-1250', 'UTF-8', trim($r['T_PSC']));
	$persArray[$zCislo]['tStat'] = iconv('windows-1250', 'UTF-8', trim($r['T_STAT']));

	array_push($persArray[$zCislo]['pp'], array(
	    "ppCislo" => $r['PP_CISLO'],
	    "ppKate" => $r['PP_KATE'],
	    "ppVstup" => strtotime($r['PP_VSTUP']) === FALSE ? '' : date('Y-m-d H:i:s', strtotime($r['PP_VSTUP'])),
	    "ppVystup" => strtotime($r['PP_VYSTUP']) === FALSE ? '' : date('Y-m-d H:i:s', strtotime($r['PP_VYSTUP'])),
	    "dovNarok" => intval($r['DOV_NAROK']),
	    "dovNarokS" => intval($r['DOV_NAROK_S']),
	    "dovZusMinr" => intval($r['DOV_ZUS_MINR']),
	    "uvaDoba" => floatval($r['UVA_DOBA']),
	    "uvaTypMzdy" => intval($r['UVA_TYP_MZDY']),
	    "uvaPlatOd" => strtotime($r['UVA_PLAT_OD']) === FALSE ? '' : date('Y-m-d H:i:s', strtotime($r['UVA_PLAT_OD'])),
	    "smlDobaUrcita" => intval($r['SML_DOBA_URCITA']),
	    "smlDatVystup" => strtotime($r['SML_DAT_VYSTUP']) === FALSE ? '' : date('Y-m-d H:i:s', strtotime($r['SML_DAT_VYSTUP'])),
		)
	);
    }
}

//2017-10-02
// pokud v live tabulce nemam vyplnene isp_cislo, pretahnu si ho z dpers_isp pro pozdejsi praci s zivou tabulkou
$sql = " update dpers";
$sql.= " join dpers_isp on dpers_isp.PersNr=dpers.PersNr";
$sql.= " set dpers.isp_cislo = dpers_isp.isp_cislo";
$sql.= " where dpers.isp_cislo is null";
$a->query($sql);

//seznam lidi z apl ============================================================
$sql = "select";
$sql.=" dpers_isp.persnr,";
$sql.=" isp_cislo,name,";
$sql.=" vorname,";
$sql.=" geboren,";
$sql.=" gebort,";
$sql.=" email,";
$sql.=" dpersstatus";
$sql.=" ,dpersdetail1_isp.kom7 as mobil";
$sql.=" ,dpersdetail1_isp.strasse as kulice";
$sql.=" ,dpers_isp.komm_ort as kmisto";
$sql.=" ,dpersdetail1_isp.plz as kpsc";
$sql.=" ,dpersdetail1_isp.stat as kstat";
$sql.=" ,dpersdetail1_isp.strasse_op as tulice";
$sql.=" ,dpersdetail1_isp.ort_op as tmisto";
$sql.=" ,dpersdetail1_isp.plz_op as tpsc";
$sql.=" ,dpersdetail1_isp.stat_op as tstat";
$sql.=" from dpers_isp";
$sql.=" join  dpersdetail1_isp on dpersdetail1_isp.persnr=dpers_isp.persnr";
$aplPersDBRows = $a->getQueryRows($sql);
//var_dump($aplPersDBRows);
$aplPersArray = array();
if ($aplPersDBRows !== NULL) {
    foreach ($aplPersDBRows as $r) {
	$zCislo = trim($r['isp_cislo']);
	if (!array_key_exists($zCislo, $aplPersArray)) {
	    $aplPersArray[$zCislo]['pp'] = array();
	}
	foreach($r as $fieldName=>$fieldValue){
	    $aplPersArray[$zCislo][$fieldName] = $fieldValue;
	}
    }
}

//seznam lidi z apl live =======================================================
$sql = "select";
$sql.=" dpers.persnr,";
$sql.=" isp_cislo,name,";
$sql.=" vorname,";
$sql.=" geboren,";
$sql.=" gebort,";
$sql.=" email,";
$sql.=" dpersstatus";
$sql.=" ,dpersdetail1.kom7 as mobil";
$sql.=" ,dpersdetail1.strasse as kulice";
$sql.=" ,dpers.komm_ort as kmisto";
$sql.=" ,dpersdetail1.plz as kpsc";
$sql.=" ,dpersdetail1.stat as kstat";
$sql.=" ,dpersdetail1.strasse_op as tulice";
$sql.=" ,dpersdetail1.ort_op as tmisto";
$sql.=" ,dpersdetail1.plz_op as tpsc";
$sql.=" ,dpersdetail1.stat_op as tstat";
$sql.=" from dpers";
$sql.=" join  dpersdetail1 on dpersdetail1.persnr=dpers.persnr";
$aplPersDBRows = $a->getQueryRows($sql);
//var_dump($aplPersDBRows);
$aplPersArrayLive = array();
if ($aplPersDBRows !== NULL) {
    foreach ($aplPersDBRows as $r) {
	$zCislo = trim($r['isp_cislo']);
	if (strlen($zCislo)) {
	    if (!array_key_exists($zCislo, $aplPersArrayLive)) {
		$aplPersArrayLive[$zCislo]['pp'] = array();
	    }
	    foreach ($r as $fieldName => $fieldValue) {
		$aplPersArrayLive[$zCislo][$fieldName] = $fieldValue;
	    }
	}
    }
}

//------------------------------------------------------------------------------
//
// projdu podle lidi z premiera
foreach ($persArray as $zCislo => $persRow) {
    $import = TRUE;
    //interni cislo zamestnance
    echo "pers start --------------------------------------------------------------------------------------------------\n";
    echo "$zCislo:" . $persRow['zPrijmeni'] . ' ' . $persRow['zJmeno'] . ' ' . $persRow['zDatNar'] . "\n";
    // cisla pracovnich pomeru -------------------------------------------------
    $persNr = 0;
    echo "pp start ==========================================================\n";
    if (count($persRow['pp']) == 1) {
	//ma je jeden prac pomer, nemusim prochazet vsechny
	echo "jen jeden prac pomer, beru tento jeden\n";
	$poradi = 0;
	$ppRow = $persRow['pp'][0];
	$ppCislo = intval($ppRow['ppCislo']);
	$persNr = $ppCislo;
	echo "ppCislo = $ppCislo \n";
	echo "ppKate = " . $ppRow['ppKate'] . "\n";
	echo "ppVstup = " . $ppRow['ppVstup'] . "\n";
	echo "ppVystup = " . $ppRow['ppVystup'] . "\n";
	echo "dovNarok = " . $ppRow['dovNarok'] . "\n";
	echo "dovNarokS = " . $ppRow['dovNarokS'] . "\n";
	echo "dovZusMinr = " . $ppRow['dovZusMinr'] . "\n";
	echo "uvaDoba = " . $ppRow['uvaDoba'] . "\n";
	echo "uvaTypMzdy = " . $ppRow['uvaTypMzdy'] . "\n";
	echo "uvaPlatOd = " . $ppRow['uvaPlatOd'] . "\n";
	echo "smlDobaUrcita = " . $ppRow['smlDobaUrcita'] . "\n";
	echo "smlDatVystup = " . $ppRow['smlDatVystup'] . "\n";
    } else {
	foreach ($persRow['pp'] as $poradi => $ppRow) {
	    $ppCislo = intval($ppRow['ppCislo']);
	    if ($ppCislo == 0) {
		echo "zadne cislo prac. pomeru !, pokud je novy - nebudu importovat do APL!\n";
		$import = FALSE;
	    } else {
		if (strlen(trim($ppRow['ppVystup'])) == 0) {
		    // jako osobni cislo vezmu to , ktere nema ukonceny prac pomer
		    // tj. nema vyplneno pp_vystup
		    $persNr = $ppCislo;
		}
		echo "ppCislo = $ppCislo \n";
		echo "ppKate = " . $ppRow['ppKate'] . "\n";
		echo "ppVstup = " . $ppRow['ppVstup'] . "\n";
		echo "ppVystup = " . $ppRow['ppVystup'] . "\n";
		echo "dovNarok = " . $ppRow['dovNarok'] . "\n";
		echo "dovNarokS = " . $ppRow['dovNarokS'] . "\n";
		echo "dovZusMinr = " . $ppRow['dovZusMinr'] . "\n";
		echo "uvaDoba = " . $ppRow['uvaDoba'] . "\n";
		echo "uvaTypMzdy = " . $ppRow['uvaTypMzdy'] . "\n";
		echo "uvaPlatOd = " . $ppRow['uvaPlatOd'] . "\n";
		echo "smlDobaUrcita = " . $ppRow['smlDobaUrcita'] . "\n";
		echo "smlDatVystup = " . $ppRow['smlDatVystup'] . "\n";
	    }
	    echo "pp $poradi ----------------------------------------------------\n";
	}
    }

    echo " persnr = $persNr\n";
    echo "pp end ============================================================\n";

    // tree full, node add, recursive nodelist()
    // kontrola duplicit !!!
    // mam cloveka s timto isp cislem v apl ?
    if (array_key_exists($zCislo, $aplPersArray)) {
	// cloveka s timto cislem mam apl, zkontroluju zmeny v hodnotach poli
	// prijmrni ------------------------------------------------------------
	if ($aplPersArray[$zCislo]['name'] != $persArray[$zCislo]['zPrijmeni']) {
	    updateAplPersnr($zCislo, 'name', $persArray[$zCislo]['zPrijmeni'], $aplPersArray[$zCislo]['name']);
	}
	//----------------------------------------------------------------------
	// prijmeni live--------------------------------------------------------
	if ($aplPersArrayLive[$zCislo]['name'] != $persArray[$zCislo]['zPrijmeni']) {
	    updateAplPersnr($zCislo, 'name', $persArray[$zCislo]['zPrijmeni'], $aplPersArray[$zCislo]['name'],"dpers");
	}
	//----------------------------------------------------------------------
	//
	// jmeno ---------------------------------------------------------------
	if ($aplPersArray[$zCislo]['vorname'] != $persArray[$zCislo]['zJmeno']) {
	    updateAplPersnr($zCislo, 'vorname', $persArray[$zCislo]['zJmeno'], $aplPersArray[$zCislo]['vorname']);
	}
	//----------------------------------------------------------------------
	// jmeno live ----------------------------------------------------------
	if ($aplPersArrayLive[$zCislo]['vorname'] != $persArray[$zCislo]['zJmeno']) {
	    updateAplPersnr($zCislo, 'vorname', $persArray[$zCislo]['zJmeno'], $aplPersArray[$zCislo]['vorname'],"dpers");
	}
	//----------------------------------------------------------------------
	//
	// misto narozeni ------------------------------------------------------
	if ($aplPersArray[$zCislo]['gebort'] != $persArray[$zCislo]['zMistoNar']) {
	    updateAplPersnr($zCislo, 'gebort', $persArray[$zCislo]['zMistoNar'], $aplPersArray[$zCislo]['gebort']);
	}
	//----------------------------------------------------------------------
	// misto narozeni live -------------------------------------------------
	if ($aplPersArrayLive[$zCislo]['gebort'] != $persArray[$zCislo]['zMistoNar']) {
	    updateAplPersnr($zCislo, 'gebort', $persArray[$zCislo]['zMistoNar'], $aplPersArray[$zCislo]['gebort'],"dpers");
	}
	//
	// email ---------------------------------------------------------------
	if ($aplPersArray[$zCislo]['email'] != $persArray[$zCislo]['zEmail']) {
	    updateAplPersnr($zCislo, 'email', $persArray[$zCislo]['zEmail'], $aplPersArray[$zCislo]['email']);
	}
	// email live ----------------------------------------------------------
	if ($aplPersArrayLive[$zCislo]['email'] != $persArray[$zCislo]['zEmail']) {
	    updateAplPersnr($zCislo, 'email', $persArray[$zCislo]['zEmail'], $aplPersArray[$zCislo]['email'],"dpers");
	}
	//----------------------------------------------------------------------
	//
	// mobil ---------------------------------------------------------------
	$tel = $persArray[$zCislo]['zMobil'];
	if(strlen($persArray[$zCislo]['zTelefon'])>0){
	    if(strlen($tel)>0){
		$tel.=','.$persArray[$zCislo]['zTelefon'];
	    }
	    else{
		$tel.= $persArray[$zCislo]['zTelefon'];
	    }
	}
	if ($aplPersArray[$zCislo]['mobil'] != $tel) {
	    updateAplPersnr($zCislo, 'kom7', $tel, $aplPersArray[$zCislo]['mobil'], 'dpersdetail1_isp');
	}
	// mobil live ----------------------------------------------------------
	if ($aplPersArrayLive[$zCislo]['mobil'] != $tel) {
	    updateAplPersnr($zCislo, 'kom7', $tel, $aplPersArray[$zCislo]['mobil'], 'dpersdetail1');
	}
	//----------------------------------------------------------------------
	//
	// korespondencni adresa, ulice ----------------------------------------
	if ($aplPersArray[$zCislo]['kulice'] != $persArray[$zCislo]['kUlice']) {
	    updateAplPersnr($zCislo, 'strasse', $persArray[$zCislo]['kUlice'], $aplPersArray[$zCislo]['kulice'], 'dpersdetail1_isp');
	}
	// korespondencni adresa, ulice live -----------------------------------
	if ($aplPersArrayLive[$zCislo]['kulice'] != $persArray[$zCislo]['kUlice']) {
	    updateAplPersnr($zCislo, 'strasse', $persArray[$zCislo]['kUlice'], $aplPersArray[$zCislo]['kulice'], 'dpersdetail1');
	}
	//----------------------------------------------------------------------
	//
	// korespondencni adresa, mesto ----------------------------------------
	if ($aplPersArray[$zCislo]['kmisto'] != $persArray[$zCislo]['kMisto']) {
	    updateAplPersnr($zCislo, 'komm_ort', $persArray[$zCislo]['kMisto'], $aplPersArray[$zCislo]['kmisto'], 'dpers_isp');
	}
	// korespondencni adresa, mesto live -----------------------------------
	if ($aplPersArrayLive[$zCislo]['kmisto'] != $persArray[$zCislo]['kMisto']) {
	    updateAplPersnr($zCislo, 'komm_ort', $persArray[$zCislo]['kMisto'], $aplPersArray[$zCislo]['kmisto'], 'dpers');
	}
	//----------------------------------------------------------------------
	//
	// korespondencni adresa, psc ------------------------------------------
	if ($aplPersArray[$zCislo]['kpsc'] != $persArray[$zCislo]['kPsc']) {
	    updateAplPersnr($zCislo, 'plz', $persArray[$zCislo]['kPsc'], $aplPersArray[$zCislo]['kpsc'], 'dpersdetail1_isp');
	}
	// korespondencni adresa, psc live -------------------------------------
	if ($aplPersArrayLive[$zCislo]['kpsc'] != $persArray[$zCislo]['kPsc']) {
	    updateAplPersnr($zCislo, 'plz', $persArray[$zCislo]['kPsc'], $aplPersArray[$zCislo]['kpsc'], 'dpersdetail1');
	}
	//----------------------------------------------------------------------
	//
	// korespondencni adresa, stat -----------------------------------------
	if ($aplPersArray[$zCislo]['kstat'] != $persArray[$zCislo]['kStat']) {
	    updateAplPersnr($zCislo, 'stat', $persArray[$zCislo]['kStat'], $aplPersArray[$zCislo]['kstat'], 'dpersdetail1_isp');
	}
	// korespondencni adresa, stat live ------------------------------------
	if ($aplPersArrayLive[$zCislo]['kstat'] != $persArray[$zCislo]['kStat']) {
	    updateAplPersnr($zCislo, 'stat', $persArray[$zCislo]['kStat'], $aplPersArray[$zCislo]['kstat'], 'dpersdetail1');
	}
	//----------------------------------------------------------------------
	//
	// trvala adresa, ulice ------------------------------------------------
	if ($aplPersArray[$zCislo]['tulice'] != $persArray[$zCislo]['tUlice']) {
	    updateAplPersnr($zCislo, 'strasse_op', $persArray[$zCislo]['tUlice'], $aplPersArray[$zCislo]['tulice'], 'dpersdetail1_isp');
	}
	// trvala adresa, ulice live -------------------------------------------
	if ($aplPersArrayLive[$zCislo]['tulice'] != $persArray[$zCislo]['tUlice']) {
	    updateAplPersnr($zCislo, 'strasse_op', $persArray[$zCislo]['tUlice'], $aplPersArray[$zCislo]['tulice'], 'dpersdetail1');
	}
	//----------------------------------------------------------------------
	//
	// trvala adresa, mesto ------------------------------------------------
	if ($aplPersArray[$zCislo]['tmisto'] != $persArray[$zCislo]['tMisto']) {
	    updateAplPersnr($zCislo, 'ort_op', $persArray[$zCislo]['tMisto'], $aplPersArray[$zCislo]['tmisto'], 'dpersdetail1_isp');
	}
	// trvala adresa, mesto live -------------------------------------------
	if ($aplPersArrayLive[$zCislo]['tmisto'] != $persArray[$zCislo]['tMisto']) {
	    updateAplPersnr($zCislo, 'ort_op', $persArray[$zCislo]['tMisto'], $aplPersArray[$zCislo]['tmisto'], 'dpersdetail1');
	}
	//----------------------------------------------------------------------
	//
	// trvala adresa, psc --------------------------------------------------
	if ($aplPersArray[$zCislo]['tpsc'] != $persArray[$zCislo]['tPsc']) {
	    updateAplPersnr($zCislo, 'plz_op', $persArray[$zCislo]['tPsc'], $aplPersArray[$zCislo]['tpsc'], 'dpersdetail1_isp');
	}
	// trvala adresa, psc live ---------------------------------------------
	if ($aplPersArrayLive[$zCislo]['tpsc'] != $persArray[$zCislo]['tPsc']) {
	    updateAplPersnr($zCislo, 'plz_op', $persArray[$zCislo]['tPsc'], $aplPersArray[$zCislo]['tpsc'], 'dpersdetail1');
	}
	//----------------------------------------------------------------------
	//
	// trvala adresa, stat -------------------------------------------------
	if ($aplPersArray[$zCislo]['tstat'] != $persArray[$zCislo]['tStat']) {
	    updateAplPersnr($zCislo, 'stat_op', $persArray[$zCislo]['tStat'], $aplPersArray[$zCislo]['tstat'], 'dpersdetail1_isp');
	}
	// trvala adresa, stat live --------------------------------------------
	if ($aplPersArrayLive[$zCislo]['tstat'] != $persArray[$zCislo]['tStat']) {
	    updateAplPersnr($zCislo, 'stat_op', $persArray[$zCislo]['tStat'], $aplPersArray[$zCislo]['tstat'], 'dpersdetail1');
	}
	//----------------------------------------------------------------------
	//
	// datum narozeni ------------------------------------------------------
	// prevedu jen Y-m-d pomoci date

	$geboren = $aplPersArray[$zCislo]['geboren'] == '0000-00-00 00:00:00' ? NULL : $aplPersArray[$zCislo]['geboren'];
	$zDatNar = $persArray[$zCislo]['zDatNar'] == '0000-00-00 00:00:00' ? NULL : $persArray[$zCislo]['zDatNar'];

	if (date('Y-m-d', strtotime($geboren)) != date('Y-m-d', strtotime($zDatNar))) {
	    updateAplPersnr($zCislo, 'geboren', $zDatNar, $geboren);
	}
	
	// datum narozeni live -------------------------------------------------
	// prevedu jen Y-m-d pomoci date

	$geboren = $aplPersArrayLive[$zCislo]['geboren'] == '0000-00-00 00:00:00' ? NULL : $aplPersArrayLive[$zCislo]['geboren'];
	$zDatNar = $persArray[$zCislo]['zDatNar'] == '0000-00-00 00:00:00' ? NULL : $persArray[$zCislo]['zDatNar'];

	if (date('Y-m-d', strtotime($geboren)) != date('Y-m-d', strtotime($zDatNar))) {
	    updateAplPersnr($zCislo, 'geboren', $zDatNar, $geboren,"dpers");
	}
	//----------------------------------------------------------------------
	// pri update se podivam i na pracovni pomery
	$aplVertragRows = $a->getPersVertragArray($aplPersArray[$zCislo]['persnr']);
//	echo "\n START pracovni pomery =========================================";
	foreach ($persArray[$zCislo]['pp'] as $ind => $ppRow) {
	    // hledam v dpersvertrag polozku s isp_pp_cislo
	    $isp_pp_cislo = $ppRow['ppCislo'];
	    if ($aplVertragRows !== NULL) {
		// mam nejake prac pomery v apl
		$naselPPvDPersVertrag = FALSE;
		foreach ($aplVertragRows as $foundIndex => $vr) {
		    //hledam cislo prac.pomeru
		    if ($vr['isp_pp_cislo'] == $isp_pp_cislo) {
			$naselPPvDPersVertrag = TRUE;
			break;
		    }
		}
		//podle toho, jestli najdu prac pomer v dpersvertrag bud updatuju hodnoty nebo vlozim novy radek
		if ($naselPPvDPersVertrag === TRUE) {
		    //echo "\n nasel ppVertrag, foundIndex = $foundIndex";
		    // zkontrolovat datum a pripadne i nastavit dpersstatus na MA,BEENDET
//		    echo "\n vertrag $isp_pp_cislo v dpersvertrag existuje, provedu DPERSVERTRAG_UPDATE";
		    $r = $aplVertragRows[$foundIndex];
		    //var_dump($r);
		    //var_dump($ppRow);
		    $isp2apl = array(
			"ppVstup" => "isp_pp_vstup",
			"ppVystup" => "isp_pp_vystup",
			"dovNarok" => "isp_dov_narok",
			"dovNarokS" => "isp_dov_narok_s",
			"dovZusMinr" => "isp_dov_zust_min_rok",
			"uvaDoba" => "isp_uva_doba",
			"uvaTypMzdy" => "isp_uva_typ_mzdy",
			"uvaPlatOd" => "isp_uva_plat_od",
			"ppKate" => "isp_pp_kate",
			"smlDobaUrcita" => "isp_sml_doba_urcita",
			"smlDatVystup" => "isp_sml_dat_vystup",
		    );
		    foreach ($isp2apl as $ispKey => $aplKey) {
			$ispValue = iconv('windows-1250', 'UTF-8', trim($ppRow[$ispKey]));
			$aplValue = $r[$aplKey] == '0000-00-00 00:00:00' ? NULL : $r[$aplKey]; //osetrit null datumy z mysql
			if ($ispValue != $aplValue) {
			    updateAplVertrag($r['id'], $aplKey, $ispValue);
			}
			//echo "\n aplKey=$aplKey";
			//zpracovani datumu nastupu, status MA
			if ($aplKey == "isp_pp_vstup") {
			    //echo "\n kontroluji isp_pp_vstup";
			    $eintrittTime = strtotime($r['eintritt']);
			    $ppVstupTime = strtotime($ppRow[$ispKey]);
			    //echo "\n eintrittTime=$eintrittTime, ppVstupTime=$ppVstupTime";
			    if (strtotime($r['eintritt']) < strtotime($ppRow[$ispKey])) {
				//echo "\menim eintritt a dpersstatus, newEintritt: $ispValue, zcislo=$zCislo";
				//posunu eintritt, zmenim status na MA
				updateAplVertrag($r['id'], 'eintritt', $ispValue);
				updateAplPersnr($zCislo, 'dpersstatus', 'MA', $aplPersArray[$zCislo]['dpersstatus'], 'dpers_isp');
			    }
			}
			//------------------------------------------------------
			//zpracovani datumu vystupu, status BEENDET
			if ($aplKey == "isp_pp_vystup") {
			    //echo "\n kontroluji isp_pp_vystup";
			    $austrittTime = strtotime($r['austritt']);
			    $ppVystupTime = strtotime($ppRow[$ispKey]);
			    $dobaUrcita = $ppRow['smlDobaUrcita'];
			    $dobaUrcitaTime = strtotime($ppRow['smlDatVystup']);
			    echo "\n austrittTime=$austrittTime, ppVystupTime=$ppVystupTime,dobaurcita=$dobaUrcita,dobaurcitatime=$dobaUrcitaTime";
			    if (
				    (($austrittTime < $ppVystupTime) && ($dobaUrcitaTime != $ppVystupTime)) //zadan vystup
				    ||
				    ((time() >= $dobaUrcitaTime) && ($dobaUrcita == 1))
			    ) {
				//splneny podminky pro ukonceni prac pomeru, bud zadan datum ukonceni nebo uplynula doba pro dobu urcitou
				updateAplVertrag($r['id'], 'austritt', $ispValue);
				updateAplPersnr($zCislo, 'dpersstatus', 'BEENDET', $aplPersArray[$zCislo]['dpersstatus'], 'dpers_isp');
			    }
			    //2017-11-15, osetreni pripadu, ze ppVystupTime bude vymazan, nastavit austritt na null a dpersstatus opet na MA
			    if($austrittTime>0 && $ppVystupTime==FALSE){
				$ppKate = $ppRow['ppKate'];
				if($ppKate=="HPP"){
				    echo "\n,ppKate=$ppKate, vymazan Austritt\n";
				    updateAplVertrag($r['id'], 'austritt', NULL);
				    updateAplPersnr($zCislo, 'dpersstatus', 'MA', $aplPersArray[$zCislo]['dpersstatus'], 'dpers_isp');
				}
			    }
			}
			//------------------------------------------------------
			//zpracovani doby urcite
			if ($aplKey == "isp_sml_doba_urcita") {
			    //echo "\n kontroluji isp_sml_doba_urcita";
			    $dobaUrcita = $ppRow['smlDobaUrcita'];
			    $dobaUrcitaTime = strtotime($ppRow['smlDatVystup']);
			    //echo "\n austrittTime=$austrittTime, ppVystupTime=$ppVystupTime,dobaurcita=$dobaUrcita,dobaurcitatime=$dobaUrcitaTime";
			    if (
				    intval($dobaUrcita) == 1
			    ) {
				//splneny podminky pro ukonceni prac pomeru, bud zadan datum ukonceni nebo uplynula doba pro dobu urcitou
				updateAplVertrag($r['id'], 'befristet', $ppRow['smlDatVystup']);
			    }
			}
		    }
		} else {
		    $ins = insertAplVertrag($ppRow, $aplPersArray[$zCislo]['persnr'], 'dpersvertrag_isp');
////		    echo "\n vertrag $isp_pp_cislo v dpersvertrag neexistuje, provedu DPERSVERTRAG_INSERT";
//		    echo " ($ins)";
		}
	    } else {
		// nemam prac pomery v apl, vlozim do tabulky dpersvertrag
//		echo "\n zadne zaznamy v dpersvertrag, provedu DPERSVERTRAG_INSERT";
		$ins = insertAplVertrag($ppRow, $aplPersArray[$zCislo]['persnr'], 'dpersvertrag_isp');
		//echo "\n $sql";
		//$ins = $a->insert($sql);
//		echo " ($ins)";
	    }
//	    echo "\n-----------------------------------------------------------\n";
	}
//	echo "\n END pracovni pomery ===========================================";
    } else {
	// cloveka s timto cislem nemam apl, pokud je jasne i persnr, zkusim ho zalozit v apl
	if ($persNr !== 0) {
	    // zalozit
	    echo " zkusim zalozit noveho MA s persnr=$persNr a zCislo=$zCislo";
	    // test na duplicitu persnr
	    foreach ($aplPersArray as $z => $aplPers) {
		if ($aplPers['persnr'] == $persNr) {
		    //duplicita
		    echo " DUPLICITPERSNR - v apl jiz mam MA s persnr=$persNr, neimportuji";
		    $import = FALSE;
		    break;
		}
	    }
	    if ($import === TRUE) {
		// testy prosly, zakladam dpers_isp
		echo " INSERTNEW - zakladam !";
		$sql_insert = "insert into dpers_isp";
		$sql_insert.=" (";
		$sql_insert.=" persnr,";
		$sql_insert.=" isp_cislo,";
		$sql_insert.=" name,";
		$sql_insert.=" vorname,";
		$sql_insert.=" geboren,";
		$sql_insert.=" gebort,";
		$sql_insert.=" dpersstatus";
		$sql_insert.=" )";
		$sql_insert.=" values(";
		$sql_insert.=" '$persNr',";
		$sql_insert.=" '$zCislo',";
		$sql_insert.=" '" . $persRow['zPrijmeni'] . "',";
		$sql_insert.=" '" . $persRow['zJmeno'] . "',";
		$sql_insert.=" '" . $persRow['zDatNar'] . "',";
		$sql_insert.=" '" . $persRow['zMistoNar'] . "',";
		$sql_insert.=" 'BEWERBER'";
		$sql_insert.=" )";
		$iid = $a->insert($sql_insert);
		echo " (dpers_isp,$iid)";
		
		// testy prosly, zakladam dpers
		echo " INSERTNEW - zakladam !";
		$sql_insert = "insert into dpers";
		$sql_insert.=" (";
		$sql_insert.=" persnr,";
		$sql_insert.=" isp_cislo,";
		$sql_insert.=" name,";
		$sql_insert.=" vorname,";
		$sql_insert.=" geboren,";
		$sql_insert.=" gebort,";
		$sql_insert.=" dpersstatus";
		$sql_insert.=" )";
		$sql_insert.=" values(";
		$sql_insert.=" '$persNr',";
		$sql_insert.=" '$zCislo',";
		$sql_insert.=" '" . $persRow['zPrijmeni'] . "',";
		$sql_insert.=" '" . $persRow['zJmeno'] . "',";
		$sql_insert.=" '" . $persRow['zDatNar'] . "',";
		$sql_insert.=" '" . $persRow['zMistoNar'] . "',";
		$sql_insert.=" 'BEWERBER'";
		$sql_insert.=" )";
		$iid = $a->insert($sql_insert);
		echo " (dpers,$iid)";
		
		// testy prosly, zakladam dpersdetail1_isp
		$sql_insert = "insert into dpersdetail1_isp";
		$sql_insert.=" (";
		$sql_insert.=" persnr";
		$sql_insert.=" )";
		$sql_insert.=" values(";
		$sql_insert.=" '$persNr'";
		$sql_insert.=" )";
		$iid = $a->insert($sql_insert);
		echo " (dpersdetail1_isp,$iid)";
		
		// testy prosly, zakladam dpersdetail1
		$sql_insert = "insert into dpersdetail1";
		$sql_insert.=" (";
		$sql_insert.=" persnr";
		$sql_insert.=" )";
		$sql_insert.=" values(";
		$sql_insert.=" '$persNr'";
		$sql_insert.=" )";
		$iid = $a->insert($sql_insert);
		echo " (dpersdetail1,$iid)";
		
		// testy prosly, zakladam dpersbewerber_isp
		$sql_insert = "insert into dpersbewerber_isp";
		$sql_insert.=" (";
		$sql_insert.=" persnr,";
		$sql_insert.=" bewerbe_datum";
		$sql_insert.=" )";
		$sql_insert.=" values(";
		$sql_insert.=" '$persNr'";
		$sql_insert.=" ,'" . $persRow['zPsTime'] . "'";
		$sql_insert.=" )";
		$iid = $a->insert($sql_insert);
		echo " (dpersbewerber_isp,$iid)";
		
		// testy prosly, zakladam dpersbewerber
		$sql_insert = "insert into dpersbewerber";
		$sql_insert.=" (";
		$sql_insert.=" persnr,";
		$sql_insert.=" bewerbe_datum";
		$sql_insert.=" )";
		$sql_insert.=" values(";
		$sql_insert.=" '$persNr'";
		$sql_insert.=" ,'" . $persRow['zPsTime'] . "'";
		$sql_insert.=" )";
		$iid = $a->insert($sql_insert);
		echo " (dpersbewerber,$iid)";
	    }
	} else {
	    //nezakladam, protoze nevim jako persnr v apl ma mit
	    echo " NOPERSNR - nevkladam noveho MA s zCislo=$zCislo, protoze neznam jeho persnr (nema prac pomer)";
	}
    }
    echo "pers end  --------------------------------------------------------------------------------------------------\n";
    echo "\n";
}
// ukonceni synchronizace do logu
echo "----- KONEC updateDpersFromPremier on :" . date('Y-m-d H:i:s') . " ----- \n";

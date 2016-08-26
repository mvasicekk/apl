#!/usr/bin/php
<?php
require_once '/var/www/workspace/apl/db.php';
require '/var/www/workspace/apl/sqldb.php';

$ucetniJednotka = "";
if($argc>1){
    $ucetniJednotka = trim($argv[1]);
}

//$ucetniJednotka = 'FA5';
if($ucetniJednotka==""){
    echo "----- START updateDpersFromPremier ($ucetniJednotka) on :".date('Y-m-d H:i:s')." ----- \n";
    echo "neni zadana ucetni jednotka, koncim\n";
    exit();
}

echo "----- START updateDpersFromPremier ($ucetniJednotka) on :".date('Y-m-d H:i:s')." ----- \n";
$sqlDB = sqldb::getInstance($ucetniJednotka);

//var_dump($sqlDB);

function updateAplPersnr($zCislo,$field,$value,$oldValue,$table='dpers_isp'){
    global $a;
    $persnr = 0;
    if($table=='dpers_isp'){
	// jen v teto tabulce mam isp_cislo, muzu vybirat podle nej
	$sql = "update `$table` set `$field`='$value' where isp_cislo='$zCislo'";
    }
    else{
	// v ostatnich tabulkach mam jako primarni klic persnr, ten si musim zjistit posle isp_cislo
	$rs = $a->getQueryRows("select persnr from dpers_isp where isp_cislo='$zCislo'");
	if($rs!==NULL){
	    $persnr = $rs[0]['persnr'];
	}
	if($persnr!==0){
	    $sql = "update `$table` set `$field`='$value' where persnr='$persnr'";
	}
    }
    
    // pocet ovlivnenych radku
    $ar = $a->query($sql);
    
    echo "\nUPDATEFIELD $field = $value (from $oldValue) for isp_cislo=$zCislo (ar=$ar),table = $table, persnr = $persnr";
}
// vybrat vsechny lidi

$res = $sqlDB->getResult("select * from fl_PERSONAL_APL_view order by Z_CISLO,PP_CISLO");

/*
column:Z_CISLO - value:6
column:Z_PRIJMENI - value:Ku��tko                                 
column:Z_JMENO - value:Lubom�r                                 
column:Z_DAT_NAR - value:Feb 17 1956 12:00:00:000AM
column:Z_MISTO_NAR - value:Cheb                                                                  
column:Z_ST_OBC - value:CZ 
column:Z_MOBIL - value:                    
column:Z_EMAIL - value:                                                                
column:Z_PS_TIME - value:Jun  1 2016 05:43:14:000AM
column:K_ULICE - value:Dvo��kova 15
column:K_MISTO - value:Cheb
column:K_PSC - value:35002
column:K_STAT - value:CZ
column:T_ULICE - value:Dvo��kova 15
column:T_MISTO - value:Cheb
column:T_PSC - value:35002
column:T_STAT - value:CZ
column:PP_CISLO - value:5887
column:PP_KATE - value:HPP
column:PP_VSTUP - value:Aug 10 2015 12:00:00:000AM
column:PP_VYSTUP - value:
column:DOV_NAROK - value:
column:DOV_NAROK_S - value:
column:DOV_ZUS_MINR - value:
column:UVA_DOBA - value:2
column:UVA_TYP_MZDY - value:3
column:UVA_PLAT_OD - value:Aug 10 2015 12:00:00:000AM
*/

$persArray = array();
if ($res !== NULL) {
    foreach($res as $r){
	$zCislo = $ucetniJednotka.'_'.intval($r['Z_CISLO']);
//	echo "zcislo:$zCislo\n";
	if(!array_key_exists($zCislo,$persArray)){
	    $persArray[$zCislo]['pp'] = array();
	}
	$persArray[$zCislo]['zPrijmeni'] = iconv('windows-1250', 'UTF-8', trim($r['Z_PRIJMENI']));
	$persArray[$zCislo]['zJmeno'] = iconv('windows-1250', 'UTF-8', trim($r['Z_JMENO']));
	$persArray[$zCislo]['zDatNar'] = strtotime($r['Z_DAT_NAR'])===FALSE?'':date('Y-m-d',  strtotime($r['Z_DAT_NAR']));
	$persArray[$zCislo]['zMistoNar'] = iconv('windows-1250', 'UTF-8', trim($r['Z_MISTO_NAR']));
	$persArray[$zCislo]['zStObc'] = iconv('windows-1250', 'UTF-8', trim($r['Z_ST_OBC']));
	$persArray[$zCislo]['zMobil'] = iconv('windows-1250', 'UTF-8', trim($r['Z_MOBIL']));
	$persArray[$zCislo]['zEmail'] = iconv('windows-1250', 'UTF-8', trim($r['Z_EMAIL']));
	$persArray[$zCislo]['zPsTime'] = strtotime($r['Z_PS_TIME'])===FALSE?'':date('Y-m-d H:i:s',  strtotime($r['Z_PS_TIME']));
	$persArray[$zCislo]['kUlice'] = iconv('windows-1250', 'UTF-8', trim($r['K_ULICE']));
	$persArray[$zCislo]['kMisto'] = iconv('windows-1250', 'UTF-8', trim($r['K_MISTO']));
	$persArray[$zCislo]['kPsc'] = iconv('windows-1250', 'UTF-8', trim($r['K_PSC']));
	$persArray[$zCislo]['kStat'] = iconv('windows-1250', 'UTF-8', trim($r['K_STAT']));
	$persArray[$zCislo]['tUlice'] = iconv('windows-1250', 'UTF-8', trim($r['T_ULICE']));
	$persArray[$zCislo]['tMisto'] = iconv('windows-1250', 'UTF-8', trim($r['T_MISTO']));
	$persArray[$zCislo]['tPsc'] = iconv('windows-1250', 'UTF-8', trim($r['T_PSC']));
	$persArray[$zCislo]['tStat'] = iconv('windows-1250', 'UTF-8', trim($r['T_STAT']));
	
	array_push($persArray[$zCislo]['pp'], 
		    array(
			"ppCislo"=>$r['PP_CISLO'],
			"ppKate"=>$r['PP_KATE'],
			"ppVstup"=>strtotime($r['PP_VSTUP'])===FALSE?'':date('Y-m-d H:i:s',  strtotime($r['PP_VSTUP'])),
			"ppVystup"=>strtotime($r['PP_VYSTUP'])===FALSE?'':date('Y-m-d H:i:s',  strtotime($r['PP_VYSTUP'])),
			"dovNarok"=>  intval($r['DOV_NAROK']),
			"dovNarokS"=>  intval($r['DOV_NAROK_S']),
			"dovZusMinr"=>  intval($r['DOV_ZUS_MINR']),
			"uvaDoba"=>  floatval($r['UVA_DOBA']),
			"uvaTypMzdy"=>  intval($r['UVA_TYP_MZDY']),
			"uvaPlatOd"=>strtotime($r['UVA_PLAT_OD'])===FALSE?'':date('Y-m-d H:i:s',  strtotime($r['UVA_PLAT_OD'])),
		    )
		);
    }
}

//seznam lidi z apl ============================================================

$sql = "select dpers_isp.persnr,isp_cislo,name,vorname,geboren,gebort,email";
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


$a = AplDB::getInstance();
$aplPersDBRows = $a->getQueryRows($sql);

//var_dump($aplPersDBRows);
$aplPersArray = array();
if ($aplPersDBRows !== NULL) {
    foreach ($aplPersDBRows as $r) {
	$zCislo = trim($r['isp_cislo']);
	if (!array_key_exists($zCislo, $aplPersArray)) {
	    $aplPersArray[$zCislo]['pp'] = array();
	}
	$aplPersArray[$zCislo]['persnr'] = $r['persnr'];
	$aplPersArray[$zCislo]['name'] = $r['name'];
	$aplPersArray[$zCislo]['vorname'] = $r['vorname'];
	$aplPersArray[$zCislo]['geboren'] = $r['geboren'];
	$aplPersArray[$zCislo]['gebort'] = $r['gebort'];
	$aplPersArray[$zCislo]['email'] = $r['email'];
	$aplPersArray[$zCislo]['mobil'] = $r['mobil'];
	$aplPersArray[$zCislo]['kulice'] = $r['kulice'];
	$aplPersArray[$zCislo]['kmisto'] = $r['kmisto'];
	$aplPersArray[$zCislo]['kpsc'] = $r['kpsc'];
	$aplPersArray[$zCislo]['kstat'] = $r['kstat'];
	$aplPersArray[$zCislo]['tulice'] = $r['tulice'];
	$aplPersArray[$zCislo]['tmisto'] = $r['tmisto'];
	$aplPersArray[$zCislo]['tpsc'] = $r['tpsc'];
	$aplPersArray[$zCislo]['tstat'] = $r['tstat'];
    }
}

// projdu podle lidi z premiera
foreach ($persArray as $zCislo=>$persRow){
    $import = TRUE;
    //interni cislo zamestnance
    echo "$zCislo:".$persRow['zPrijmeni'].' '.$persRow['zJmeno'].' '.$persRow['zDatNar'].' - ';
    // cisla pracovnich pomeru -------------------------------------------------
    $persNr = 0;
    foreach ($persRow['pp'] as $poradi=>$ppRow){
	$ppCislo = intval($ppRow['ppCislo']);
	if($ppCislo==0){
	    echo "zadne cislo prac. pomeru !, pokud je novy - nebudu importovat do APL!\n";
	    $import = FALSE;
	}
	else{
	    if($poradi==0){
		// nejnizsi cislo prac. pomeru ( to ze bude prvni je zajisteno -
		// sort by v dotazu ) bude zaroven persnr ----------------------
		$persNr = $ppCislo;
	    }
	    echo "$ppCislo ,";
	}
    }
    echo " persnr = $persNr";
    
    // mam cloveka s timto isp cislem v apl ?
    if(array_key_exists($zCislo, $aplPersArray)){
	// cloveka s timto cislem mam apl, zkontroluju zmeny v hodnotach poli
//	$persArray[$zCislo]['zDatNar'] = strtotime($r['Z_DAT_NAR'])===FALSE?'':date('Y-m-d',  strtotime($r['Z_DAT_NAR']));
//	$persArray[$zCislo]['zStObc'] = iconv('windows-1250', 'UTF-8', trim($r['Z_ST_OBC']));

	// prijmrni ------------------------------------------------------------
	if($aplPersArray[$zCislo]['name']!=$persArray[$zCislo]['zPrijmeni']){
	    updateAplPersnr($zCislo,'name',$persArray[$zCislo]['zPrijmeni'],$aplPersArray[$zCislo]['name']);
	}
	//----------------------------------------------------------------------
	//
	// jmeno ---------------------------------------------------------------
	if($aplPersArray[$zCislo]['vorname']!=$persArray[$zCislo]['zJmeno']){
	    updateAplPersnr($zCislo,'vorname',$persArray[$zCislo]['zJmeno'],$aplPersArray[$zCislo]['vorname']);
	}
	//----------------------------------------------------------------------
	//
	// misto narozeni ------------------------------------------------------
	if($aplPersArray[$zCislo]['gebort']!=$persArray[$zCislo]['zMistoNar']){
	    updateAplPersnr($zCislo,'gebort',$persArray[$zCislo]['zMistoNar'],$aplPersArray[$zCislo]['gebort']);
	}
	//----------------------------------------------------------------------
	//
	// email ---------------------------------------------------------------
	if($aplPersArray[$zCislo]['email']!=$persArray[$zCislo]['zEmail']){
	    updateAplPersnr($zCislo,'email',$persArray[$zCislo]['zEmail'],$aplPersArray[$zCislo]['email']);
	}
	//----------------------------------------------------------------------
	//
	// mobil ---------------------------------------------------------------
	if($aplPersArray[$zCislo]['mobil']!=$persArray[$zCislo]['zMobil']){
	    updateAplPersnr($zCislo,'kom7',$persArray[$zCislo]['zMobil'],$aplPersArray[$zCislo]['mobil'],'dpersdetail1_isp');
	}
	//----------------------------------------------------------------------
	//
	// korespondencni adresa, ulice ----------------------------------------
	if($aplPersArray[$zCislo]['kulice']!=$persArray[$zCislo]['kUlice']){
	    updateAplPersnr($zCislo,'strasse',$persArray[$zCislo]['kUlice'],$aplPersArray[$zCislo]['kulice'],'dpersdetail1_isp');
	}
	//----------------------------------------------------------------------
	//
	// korespondencni adresa, mesto ----------------------------------------
	if($aplPersArray[$zCislo]['kmisto']!=$persArray[$zCislo]['kMisto']){
	    updateAplPersnr($zCislo,'komm_ort',$persArray[$zCislo]['kMisto'],$aplPersArray[$zCislo]['kmisto'],'dpers_isp');
	}
	//----------------------------------------------------------------------
	//
	// korespondencni adresa, psc ------------------------------------------
	if($aplPersArray[$zCislo]['kpsc']!=$persArray[$zCislo]['kPsc']){
	    updateAplPersnr($zCislo,'plz',$persArray[$zCislo]['kPsc'],$aplPersArray[$zCislo]['kpsc'],'dpersdetail1_isp');
	}
	//----------------------------------------------------------------------
	//
	// korespondencni adresa, stat -----------------------------------------
 	if($aplPersArray[$zCislo]['kstat']!=$persArray[$zCislo]['kStat']){
	    updateAplPersnr($zCislo,'stat',$persArray[$zCislo]['kStat'],$aplPersArray[$zCislo]['kstat'],'dpersdetail1_isp');
	}
	//----------------------------------------------------------------------
	//
	// trvala adresa, ulice ------------------------------------------------
	if($aplPersArray[$zCislo]['tulice']!=$persArray[$zCislo]['tUlice']){
	    updateAplPersnr($zCislo,'strasse_op',$persArray[$zCislo]['tUlice'],$aplPersArray[$zCislo]['tulice'],'dpersdetail1_isp');
	}
	//----------------------------------------------------------------------
	//
	// trvala adresa, mesto ------------------------------------------------
	if($aplPersArray[$zCislo]['tmisto']!=$persArray[$zCislo]['tMisto']){
	    updateAplPersnr($zCislo,'ort_op',$persArray[$zCislo]['tMisto'],$aplPersArray[$zCislo]['tmisto'],'dpersdetail1_isp');
	}
	//----------------------------------------------------------------------
	//
	// trvala adresa, psc --------------------------------------------------
	if($aplPersArray[$zCislo]['tpsc']!=$persArray[$zCislo]['tPsc']){
	    updateAplPersnr($zCislo,'plz_op',$persArray[$zCislo]['tPsc'],$aplPersArray[$zCislo]['tpsc'],'dpersdetail1_isp');
	}
	//----------------------------------------------------------------------
	//
	// trvala adresa, stat -------------------------------------------------
	if($aplPersArray[$zCislo]['tstat']!=$persArray[$zCislo]['tStat']){
	    updateAplPersnr($zCislo,'stat_op',$persArray[$zCislo]['tStat'],$aplPersArray[$zCislo]['tstat'],'dpersdetail1_isp');
	}
	//----------------------------------------------------------------------
	//
	// datum narozeni ------------------------------------------------------
	// prevedu jen Y-m-d pomoci date
	// pro test
//	$d = date('Y-m-d',  strtotime('2016-08-20'));
	
	$geboren = $aplPersArray[$zCislo]['geboren']=='0000-00-00 00:00:00'?NULL:$aplPersArray[$zCislo]['geboren'];
	$zDatNar = $persArray[$zCislo]['zDatNar']=='0000-00-00 00:00:00'?NULL:$persArray[$zCislo]['zDatNar'];
	
	//echo "\nDatumu geboren=".$geboren.", date('Y-m-d',geboren=".date('Y-m-d',  strtotime($geboren));
	//echo "\nDatumu zDatNar=".$zDatNar.", date('Y-m-d',zDatNar=".date('Y-m-d',  strtotime($zDatNar));
	if(date('Y-m-d',  strtotime($geboren))!=date('Y-m-d',  strtotime($zDatNar))){
	    updateAplPersnr($zCislo,'geboren',$zDatNar,$geboren);
	}
	//----------------------------------------------------------------------
	// pri update se podivam i na pracovni pomery
	$aplVertragRows = $a->getPersVertragArray($aplPersArray[$zCislo]['persnr']);
	echo "\n START pracovni pomery =========================================";
	foreach ($persArray[$zCislo]['pp'] as $ind=>$ppRow){
	    // hledam v dpersvertrag polozku s isp_pp_cislo
	    $isp_pp_cislo = $ppRow['ppCislo'];
	    if($aplVertragRows!==NULL){
		// mam nejake prac pomery v apl
		$naselPPvDPersVertrag = FALSE;
		foreach ($aplVertragRows as $vr){
		    //hledam cislo prac.pomeru
		    if($vr['isp_pp_cislo']==$isp_pp_cislo){
			$naselPPvDPersVertrag = TRUE;
			break;
		    }
		}
		//podle toho, jestli najdu prac pomer v dpersvertrag bud updatuju hodnoty nebo vlozim novy radek
		if($naselPPvDPersVertrag===TRUE){
		    echo "\n vertrag $isp_pp_cislo v dpersvertrag existuje, provedu DPERSVERTRAGUPDATE";
		}
		else{
		    echo "\n vertrag $isp_pp_cislo v dpersvertrag neexistuje, provedu DPERSVERTRAGINSERT";
		}
	    }
	    else{
		// nemam prac pomery v apl, vlozim do tabulky dpersvertrag
		echo "\n zadne zaznamy v dpersvertrag, provedu DPERSVERTRAGINSERT";
		$sql = "insert into dpersvertrag_isp (";
		$sql.=" persnr";
		$sql.=" ,eintritt";
		$sql.=" ,austritt";
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
		$sql.=" ,isp_div_zust_min_rok";
		$sql.=" ,isp_uva_doba";
		$sql.=" ,isp_uva_typ_mzdy";
		$sql.=" ,isp_uva_plat_od";
		$sql.=")";
		$sql.=" values(";
		$sql.=" '".$aplPersArray[$zCislo]['persnr']."'";
		$sql.=" ,'".$ppRow['ppVstup']."'";
		$sql.=" ,'".$ppRow['ppVystup']."'";
		$sql.=" ,null";
		$sql.=" ,null";
		$sql.=" ,null";
		$sql.=" ,8";
		$sql.=" ,'".$ppRow['dovNarok']."'";
		$sql.=" ,'".$ppRow['dovZusMinr']."'";
		$sql.=" ,0";
		$sql.=" ,0";
		$sql.=" ,0";	
		$sql.=" ,1";
		$sql.=" ,'".$ppRow['ppCislo']."'";
		$sql.=" ,'".$ppRow['ppKate']."'";
		$sql.=" ,'".$ppRow['ppVstup']."'";
		$sql.=" ,'".$ppRow['ppVystup']."'";
		$sql.=" ,'".$ppRow['dovNarok']."'";
		$sql.=" ,'".$ppRow['dovNarokS']."'";
		$sql.=" ,'".$ppRow['dovZusMinr']."'";
		$sql.=" ,'".$ppRow['uvaDoba']."'";
		$sql.=" ,'".$ppRow['uvaTypMzdy']."'";
		$sql.=" ,'".$ppRow['uvaPlatOd']."'";
		$sql.=" )";
		//echo "\n $sql";
		$ins = $a->insert($sql);
		echo " ($ins)";
	    }
	    echo "\n-----------------------------------------------------------\n";
	}
	echo "\n END pracovni pomery ===========================================";
	
    }
    else{
	// cloveka s timto cislem nemam apl, pokud je jasne i persnr, zkusim ho zalozit v apl
	if($persNr!==0){
	    // zalozit
	    echo " zkusim zalozit noveho MA s persnr=$persNr a zCislo=$zCislo";
	    // test na duplicitu persnr
	    foreach ($aplPersArray as $z=>$aplPers){
		if($aplPers['persnr']==$persNr){
		    //duplicita
		    echo " DUPLICITPERSNR - v apl jiz mam MA s persnr=$persNr, neimportuji";
		    $import = FALSE;
		    break;
		}
	    }
	    if($import===TRUE){
		// testy prosly, zakladam dpers
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
		$sql_insert.=" '".$persRow['zPrijmeni']."',";
		$sql_insert.=" '".$persRow['zJmeno']."',";
		$sql_insert.=" '".$persRow['zDatNar']."',";
		$sql_insert.=" '".$persRow['zMistoNar']."',";
		$sql_insert.=" 'BEWERBER'";
		$sql_insert.=" )";
		$iid = $a->insert($sql_insert);
		echo " (dpers,$iid)";
		// testy prosly, zakladam dpersdetail1
		$sql_insert = "insert into dpersdetail1_isp";
		$sql_insert.=" (";
		$sql_insert.=" persnr";
		$sql_insert.=" )";
		$sql_insert.=" values(";
		$sql_insert.=" '$persNr'";
		$sql_insert.=" )";
		$iid = $a->insert($sql_insert);
		echo " (dpersdetail1,$iid)";
		// testy prosly, zakladam dpersbewerber
		$sql_insert = "insert into dpersbewerber_isp";
		$sql_insert.=" (";
		$sql_insert.=" persnr,";
		$sql_insert.=" bewerbe_datum";
		$sql_insert.=" )";
		$sql_insert.=" values(";
		$sql_insert.=" '$persNr'";
		$sql_insert.=" ,'".$persRow['zPsTime']."'";
		$sql_insert.=" )";
		$iid = $a->insert($sql_insert);
		echo " (dpersbewerber,$iid)";
	    }
	}
	else{
	    //nezakladam, protoze nevim jako persnr v apl ma mit
	    echo " NOPERSNR - nevkladam noveho MA s zCislo=$zCislo, protoze neznam jeho persnr (nema prac pomer)";
	}
    }
    echo "\n";
}

echo "----- KONEC updateDpersFromPremier on :".date('Y-m-d H:i:s')." ----- \n";

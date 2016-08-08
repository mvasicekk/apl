#!/usr/bin/php
<?php
require_once '/var/www/workspace/apl/db.php';
require '/var/www/workspace/apl/sqldb.php';


echo "----- START updateDpersFromPremier on :".date('Y-m-d H:i:s')." ----- \n";
$sqlDB = sqldb::getInstance('FA4');


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
	$zCislo = intval($r['Z_CISLO']);
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
$sql = "select persnr,isp_cislo from dpers_isp";
$a = AplDB::getInstance();
$aplPersDBRows = $a->getQueryRows($sql);
$aplPersArray = array();
if ($aplPersDBRows !== NULL) {
    foreach ($aplPersDBRows as $r) {
	$zCislo = intval($r['isp_cislo']);
	if (!array_key_exists($zCislo, $aplPersArray)) {
	    $aplPersArray[$zCislo]['pp'] = array();
	}
	$aplPersArray[$zCislo]['persnr'] = $r['persnr'];
    }
}

// projdu podle lidi z premiera
foreach ($persArray as $zCislo=>$persRow){
    $import = TRUE;
    //interni cislo zamestnance
    echo "$zCislo:".$persRow['zPrijmeni'].' '.$persRow['zJmeno'].' '.$persRow['zDatNar'].' - ';
    //cisla pracovnich pomeru
    $persNr = 0;
    foreach ($persRow['pp'] as $poradi=>$ppRow){
	$ppCislo = intval($ppRow['ppCislo']);
	if($ppCislo==0){
	    echo "zadne cislo prac. pomeru !, pokud je novy - nebudu importovat do APL!\n";
	    $import = FALSE;
	}
	else{
	    if($poradi==0){
		//nejnizsi cislo prac. pomeru ( to ze bude prvni je zajisteno sort by v dotazu ) bude zaroven persnr
		$persNr = $ppCislo;
	    }
	    echo "$ppCislo ,";
	}
    }
    echo " persnr = $persNr";
    
    // mam cloveka s timto isp cislem v apl ?
    if(array_key_exists($zCislo, $aplPersArray)){
	// cloveka s timto cislem mam apl, zkontroluju zmeny v hodnotach poli
	echo " UPDATEFIELDS - mam v APL, kontroluji zmeny v hodnotach";
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
		// testy prosly, zakladam
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
		$sql_insert.=" 'MA'";
		$sql_insert.=" )";
		$iid = $a->insert($sql_insert);
		echo " ($iid)";
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

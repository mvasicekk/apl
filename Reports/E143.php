<?php
session_start();
require_once '../db.php';

$parameters=$_GET;
$monat = $_GET['monat'];
$jahr = $_GET['jahr'];
$persvon = $_GET['persvon'];
$persbis = $_GET['persbis'];

$a = AplDB::getInstance();

$von = $jahr . "-" . $monat . "-01";
$pocetDnuVMesici = cal_days_in_month(CAL_GREGORIAN, $monat, $jahr);
$bis = $jahr . "-" . $monat . "-" . $pocetDnuVMesici;

$user = $_SESSION['user'];
$password = $_GET['password'];

//$fullAccess = testReportPassword("S142",$password,$user,0);
//
//if((!$fullAccess) && ($reporttyp=='lohn'))
//{
//    echo "Nemate povoleno zobrazeni teto sestavy / Sie sind nicht berechtigt dieses Report zu starten.";
//    exit;
//}

printf("monat: %02d\n",$monat);
printf("jahr: %04d\n",$jahr);
printf("persvon: %05d\n",$persvon);
printf("persbis: %05d\n",$persbis);
echo "<hr>";

$pA = array(4815,2440,5887,276,567,5557,2310,1490);

// a-premie
// v E143 je toto
$aPremienArray = $a->getPersnrApremieArray($monat, $jahr, $persvon, $persbis, '*',FALSE);
// na zkousku
//$aPremienArray = $a->getPersnrApremieArray($monat, $jahr, $persvon, $persbis, '*',TRUE);
//AplDB::varDump($aPremienArray);
// dochazka, dovolena nemoci, zakladni udaje o persnr

$sql="";
$sql.=" select";
$sql.="     dpers.persnr,";
$sql.="     dpers.`Name` as name,";
$sql.="     dpers.`Vorname` as vorname,";
$sql.="     CONCAT(dpers.`Name`,' ',dpers.`Vorname`) as vollname,";
$sql.="     dpers.lohnfaktor/60 as perslohnfaktor,";
$sql.="     dpers.leistfaktor,";
$sql.="     dpers.premie_za_vykon,";
$sql.="     dpers.regeloe,";
$sql.="     dpers.alteroe,";
$sql.="     dpers.premie_za_kvalitu,";
$sql.="     dpers.qpremie_akkord,";
$sql.="     dpers.qpremie_zeit,";
$sql.="     dpers.premie_za_prasnost,";
$sql.="     dpers.premie_za_3_mesice,";
$sql.="     dpers.MAStunden,";
$sql.="     if(dpersbewerber.exekution is null,0,dpersbewerber.exekution) as exekution,";
$sql.="     DATE_FORMAT(dpers.eintritt,'%y-%m-%d') as eintritt,";
$sql.="     DATE_FORMAT(dpers.austritt,'%y-%m-%d') as austritt,";
$sql.="     DATE_FORMAT(dpers.geboren,'%Y-%m-%d') as geboren,";
$sql.="     DATE_FORMAT(dpersdetail1.dobaurcita,'%y-%m-%d') as dobaurcita,";
$sql.="     DATE_FORMAT(dpersdetail1.zkusebni_doba_dobaurcita,'%y-%m-%d') as zkusebni_doba_dobaurcita,";
$sql.="     dzeit.Datum as datum,";
$sql.="     sum(dzeit.`Stunden`) as sumstunden,";
$sql.="     sum(if(dtattypen.oestatus='a',dzeit.`Stunden`,0)) as sumstundena,";
$sql.="     sum(if(dtattypen.oestatus='a' and dtattypen.akkord<>0,dzeit.`Stunden`,0)) as sumstundena_akkord,";
$sql.="     sum(if(dtattypen.erschwerniss<>0,dzeit.`Stunden`*6,0)) as erschwerniss,";
$sql.="     sum(if(dzeit.tat='z',1,0)) as tage_z,";
$sql.="     sum(if(dzeit.tat='nv',1,0)) as tage_nv,";
$sql.="     sum(if(dzeit.tat='nw',1,0)) as tage_nw,";
$sql.="     sum(if(dzeit.tat='d',1,0)) as tage_d,";
$sql.="     sum(if(dzeit.tat='np',1,0)) as tage_np,";
$sql.="     sum(if(dzeit.tat='n',1,0)) as tage_n,";
$sql.="     sum(if(dzeit.tat='nu',1,0)) as tage_nu,";
$sql.="     sum(if(dzeit.tat='p',1,0)) as tage_p,";
$sql.="     sum(if(dzeit.tat='u',1,0)) as tage_u,";
$sql.="     sum(if(dzeit.tat='?',1,0)) as tage_frage";
$sql.="     ,sum(if(dtattypen.fr_sp='N',dzeit.stunden,0)) as nachtstd";
$sql.="     ,durlaub1.jahranspruch";
$sql.="     ,durlaub1.rest";
$sql.="     ,durlaub1.gekrzt";
$sql.=" from dpers";
$sql.=" join dzeit on dzeit.PersNr=dpers.PersNr";
$sql.=" join dtattypen on dzeit.tat=dtattypen.tat";
$sql.=" left join dpersdetail1 on dpersdetail1.persnr=dpers.`PersNr`";
$sql.=" left join dpersbewerber on dpersbewerber.persnr=dpers.`PersNr`";
$sql.=" left join durlaub1 on durlaub1.`PersNr`=dpers.`PersNr`";
$sql.=" where";
$sql.=" (";
$sql.="     (dpers.austritt is null or dpers.austritt>='$von' or dpers.eintritt>dpers.austritt)";
$sql.="     and (dzeit.`Datum` between '$von' and '$bis')";
$sql.="     and (dpers.persnr between '$persvon' and '$persbis')";
$sql.=" )";
$sql.=" group by ";
$sql.="     dpers.`PersNr`,";
$sql.="     dzeit.datum";

$rows = $a->getQueryRows($sql);
$persRows = array();

if($rows!=NULL){
    foreach ($rows as $r){
	$persnr = $r['persnr'];
	$datum = $r['datum'];
	$persRows[$persnr]['grundinfo'] = $r;
	$persRows[$persnr][$datum] = $r;
    }
}

// transport
//transport
$pt="";
$pt.=" select dperstransport.persnr,sum(dperstransport.preis) as transport";
$pt.=" from dperstransport";
$pt.=" where dperstransport.persnr between '$persvon' and '$persbis' and dperstransport.datum between '$von' and '$bis'";
$pt.=" group by dperstransport.persnr";

$rows = $a->getQueryRows($pt);
$persTransportRows = array();

if($rows!=NULL){
    foreach ($rows as $r){
	$persnr = $r['persnr'];
	$persTransportRows[$persnr] = $r;
    }
}

// vorschuss
$pt="";
$pt.=" select";
$pt.=" dvorschuss.persnr,sum(dvorschuss.vorschuss) as sumvorschuss from dvorschuss where dvorschuss.datum between '$von' and '$bis' and dvorschuss.persnr between '$persvon' and '$persbis' group by dvorschuss.persnr";

$rows = $a->getQueryRows($pt);
$persVorschussRows = array();

if($rows!=NULL){
    foreach ($rows as $r){
	$persnr = $r['persnr'];
	$persVorschussRows[$persnr] = $r;
    }
}

//essen

$pt="";
$pt.="select";
$pt.=" dzeit.`PersNr` as persnr,";
$pt.=" sum(dessen.essen_preis) as essen";
$pt.=" from dzeit ";
$pt.=" join dessen on dessen.id_essen=dzeit.id_essen";
$pt.=" where dzeit.`Datum` between '$von' and '$bis' and dzeit.`PersNr` between '$persvon' and '$persbis' and dzeit.essen<>0";
$pt.=" group by dzeit.`PersNr`";

$rows = $a->getQueryRows($pt);
$persEssenRows = array();

if($rows!=NULL){
    foreach ($rows as $r){
	$persnr = $r['persnr'];
	$persEssenRows[$persnr] = $r;
    }
}

// nachzuschlag, so ne
// nachtzuschlag
$pt="";
$pt.="select";
$pt.="     dzeit.PersNr as persnr,";
$pt.="     sum(if(";
$pt.="          (DATE_FORMAT(dzeit.anw_von,'%H:%i')>='22:00' and DATE_FORMAT(dzeit.anw_von,'%H:%i')<='23:59') and (dzeit.anw_bis>dzeit.anw_von),";
$pt.="         if(TIME_TO_SEC(TIMEDIFF(dzeit.anw_bis,dzeit.anw_von))>4*60*60,TIME_TO_SEC(TIMEDIFF(dzeit.anw_bis,dzeit.anw_von))-0.5*60*60,TIME_TO_SEC(TIMEDIFF(dzeit.anw_bis,dzeit.anw_von))),";
$pt.="         if(";
$pt.="             DATE_FORMAT(dzeit.anw_von,'%H:%i')>='00:01' and DATE_FORMAT(dzeit.anw_von,'%H:%i')<='06:00' and DATE_FORMAT(dzeit.anw_bis,'%H:%i')<='06:00',";
$pt.="             if(TIME_TO_SEC(TIMEDIFF(dzeit.anw_bis,dzeit.anw_von))>4*60*60,TIME_TO_SEC(TIMEDIFF(dzeit.anw_bis,dzeit.anw_von))-0.5*60*60,TIME_TO_SEC(TIMEDIFF(dzeit.anw_bis,dzeit.anw_von))),";
$pt.="             if(";
$pt.="                 DATE_FORMAT(dzeit.anw_von,'%H:%i')>='00:01' and DATE_FORMAT(dzeit.anw_von,'%H:%i')<='06:00' and DATE_FORMAT(dzeit.anw_bis,'%H:%i')>='06:00',";
$pt.="                 if(TIME_TO_SEC(TIMEDIFF(ADDTIME(dzeit.datum,'06:00:00'),dzeit.anw_von))>4*60*60,TIME_TO_SEC(TIMEDIFF(ADDTIME(dzeit.datum,'06:00:00'),dzeit.anw_von))-0.5*60*60,TIME_TO_SEC(TIMEDIFF(ADDTIME(dzeit.datum,'06:00:00'),dzeit.anw_von))),";
$pt.="                 if(";
$pt.="                     DATE_FORMAT(dzeit.anw_bis,'%H:%i')>='22:00' and DATE_FORMAT(dzeit.anw_bis,'%H:%i')<='23:59' and DATE_FORMAT(dzeit.anw_von,'%H:%i')<='22:00',";
$pt.="                     if(TIME_TO_SEC(TIMEDIFF(dzeit.anw_bis,ADDTIME(dzeit.datum,'22:00:00')))>4*60*60,TIME_TO_SEC(TIMEDIFF(dzeit.anw_bis,ADDTIME(dzeit.datum,'22:00:00')))*0.5*60*60,TIME_TO_SEC(TIMEDIFF(dzeit.anw_bis,ADDTIME(dzeit.datum,'22:00:00')))),";
$pt.="                     0";
$pt.="                 )";
$pt.="             )";
$pt.="         )";
$pt.=" )/(60*60)) as nacht,";
$pt.=" sum(if(cislodne=6,dzeit.stunden,0)) as sostd,";
$pt.=" sum(if(cislodne=7,dzeit.stunden,0)) as nestd";
$pt.=" from";
$pt.="     dzeit";
$pt.=" join calendar on calendar.datum=dzeit.datum";
$pt.=" where";
$pt.="     dzeit.Datum between '$von' and '$bis'";
$pt.="     and dzeit.persnr between $persvon and $persbis";
$pt.=" group by";
$pt.="     dzeit.PersNr";

$rows = $a->getQueryRows($pt);
$persNachtSoNeRows = array();

if($rows!=NULL){
    foreach ($rows as $r){
	$persnr = $r['persnr'];
	$persNachtSoNeRows[$persnr] = $r;
    }
}

//AplDB::varDump($persNachtSoNeRows);
//------------------------------------------------------------------------------
// casova a vykonova mzda
$sql="";
$sql.=" select";
$sql.="     drueck.persnr,";
//$sql.="     drueck.Datum as datum,";
$sql.="     sum(if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-IST`,(drueck.`Stück`)*drueck.`VZ-IST`)) as vzaby,";
$sql.="     sum(if(dtattypen.akkord<>0,if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-IST`,(drueck.`Stück`)*drueck.`VZ-IST`),0)) as vzaby_akkord,";
$sql.="     sum(if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-IST`*dtattypen.lohnfaktor,(drueck.`Stück`)*drueck.`VZ-IST`*dtattypen.lohnfaktor)) as vzaby_kc,";
$sql.="     sum(if(dtattypen.akkord<>0,if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-IST`*dtattypen.lohnfaktor,(drueck.`Stück`)*drueck.`VZ-IST`*dtattypen.lohnfaktor),0)) as vzaby_akkord_kc";
$sql.=" from drueck";
$sql.=" join dtattypen on drueck.oe=dtattypen.tat";
$sql.=" join dpers on dpers.persnr=drueck.persnr";
$sql.=" where";
$sql.=" (";
$sql.=" (dpers.austritt is null or dpers.austritt>='$von' or dpers.eintritt>dpers.austritt)";
$sql.=" and (drueck.`Datum` between '$von' and '$bis')";
$sql.=" and (drueck.persnr between '$persvon' and '$persbis')";
$sql.=" )";
$sql.=" group by ";
$sql.=" drueck.persnr";
//$sql.=" drueck.Datum";

$rows = $a->getQueryRows($sql);
$persLeistRows = array();

if($rows!=NULL){
    foreach ($rows as $r){
	$persnr = $r['persnr'];
	$persLeistRows[$persnr] = $r;
    }
}

$fieldSeparator = ';';
$msRows = array();

// vytvoreni pole mzdovych slozek z jednotlivych podpoli

// casova a vykonova mzda, sumy za cely mesic za cely mesic
foreach ($persLeistRows as $persnr=>$pers) {
    
    if(!in_array($persnr, $pA)){
	continue;
    }
    
    $rok = sprintf("%04d", $jahr);
    $mesic = sprintf("%02d", $monat);
    $stredisko = sprintf("%d", 0);
    $pracovnik = $persnr;
    $persLohnFaktor = floatval($persRows[$persnr]['grundinfo']['perslohnfaktor']);
    
    $zakazka = 0;
    $da1 = '';
    $da2 = '';
    $da3 = '';
    $dat_od = date("d.m.Y", strtotime($von));
    $dat_do = date("d.m.Y", strtotime($bis));

	// nacht , 008  
    	$kod = sprintf("%d", 8);
	if(array_key_exists($persnr, $persNachtSoNeRows)){
	    $hodiny = number_format(floatval($persNachtSoNeRows[$persnr]['nacht']), 2, ',', '');
	}
	else{
	    $hodiny = 0;
	}
	
	$korunyCelkem = 0;
	$dny = 0;
	
	$exportRow = sprintf("%04d;%02d;%d;%d;%d;%s;%d;%s;%d;%d;%d;%d;%s;%s\n"
		, $rok
		, $mesic
		, $stredisko
		, $pracovnik
		, $kod
		, $korunyCelkem
		, $dny
		, $hodiny
		, $zakazka
		, $da1
		, $da2
		, $da3
		, $dat_od
		, $dat_do
	);
	array_push($msRows, $exportRow);
	
	// So , 006  
    	$kod = sprintf("%d", 6);
	if(array_key_exists($persnr, $persNachtSoNeRows)){
	    $hodiny = number_format(floatval($persNachtSoNeRows[$persnr]['sostd']), 2, ',', '');
	}
	else{
	    $hodiny = 0;
	}
	
	$korunyCelkem = 0;
	$dny = 0;
	
	$exportRow = sprintf("%04d;%02d;%d;%d;%d;%s;%d;%s;%d;%d;%d;%d;%s;%s\n"
		, $rok
		, $mesic
		, $stredisko
		, $pracovnik
		, $kod
		, $korunyCelkem
		, $dny
		, $hodiny
		, $zakazka
		, $da1
		, $da2
		, $da3
		, $dat_od
		, $dat_do
	);
	array_push($msRows, $exportRow);
	
	// Ne , 007  
    	$kod = sprintf("%d", 7);
	if(array_key_exists($persnr, $persNachtSoNeRows)){
	    $hodiny = number_format(floatval($persNachtSoNeRows[$persnr]['nestd']), 2, ',', '');
	}
	else{
	    $hodiny = 0;
	}
	
	$korunyCelkem = 0;
	$dny = 0;
	
	$exportRow = sprintf("%04d;%02d;%d;%d;%d;%s;%d;%s;%d;%d;%d;%d;%s;%s\n"
		, $rok
		, $mesic
		, $stredisko
		, $pracovnik
		, $kod
		, $korunyCelkem
		, $dny
		, $hodiny
		, $zakazka
		, $da1
		, $da2
		, $da3
		, $dat_od
		, $dat_do
	);
	array_push($msRows, $exportRow);
    
	
    // essen,slozka c.748
    	$kod = sprintf("%d", 748);
	if(array_key_exists($persnr, $persEssenRows)){
	    $korunyCelkem = number_format(floatval($persEssenRows[$persnr]['essen']), 0, ',', '');
	}
	else{
	    $korunyCelkem = 0;
	}
	
	$dny = 0;
	$hodiny = 0;
	$exportRow = sprintf("%04d;%02d;%d;%d;%d;%s;%d;%s;%d;%d;%d;%d;%s;%s\n"
		, $rok
		, $mesic
		, $stredisko
		, $pracovnik
		, $kod
		, $korunyCelkem
		, $dny
		, $hodiny
		, $zakazka
		, $da1
		, $da2
		, $da3
		, $dat_od
		, $dat_do
	);
	array_push($msRows, $exportRow);
    
    
    // vorschuss,slozka c.746
    	$kod = sprintf("%d", 746);
	if(array_key_exists($persnr, $persVorschussRows)){
	    $korunyCelkem = number_format(floatval($persVorschussRows[$persnr]['sumvorschuss']), 0, ',', '');
	}
	else{
	    $korunyCelkem = 0;
	}
	
	$dny = 0;
	$hodiny = 0;
	$exportRow = sprintf("%04d;%02d;%d;%d;%d;%s;%d;%s;%d;%d;%d;%d;%s;%s\n"
		, $rok
		, $mesic
		, $stredisko
		, $pracovnik
		, $kod
		, $korunyCelkem
		, $dny
		, $hodiny
		, $zakazka
		, $da1
		, $da2
		, $da3
		, $dat_od
		, $dat_do
	);
	array_push($msRows, $exportRow);
	
	
    // transport,slozka c.751
    	$kod = sprintf("%d", 751);
	if(array_key_exists($persnr, $persTransportRows)){
	    $korunyCelkem = number_format(floatval($persTransportRows[$persnr]['transport']), 0, ',', '');
	}
	else{
	    $korunyCelkem = 0;
	}
	
	$dny = 0;
	$hodiny = 0;
	$exportRow = sprintf("%04d;%02d;%d;%d;%d;%s;%d;%s;%d;%d;%d;%d;%s;%s\n"
		, $rok
		, $mesic
		, $stredisko
		, $pracovnik
		, $kod
		, $korunyCelkem
		, $dny
		, $hodiny
		, $zakazka
		, $da1
		, $da2
		, $da3
		, $dat_od
		, $dat_do
	);
	array_push($msRows, $exportRow);
	
    // ukolova mzda, slozka c.117 ---------------------------------------
	$kod = sprintf("%d", 117);
	$korunyCelkem = number_format(floatval($pers['vzaby_akkord_kc']), 0, ',', '');
	$dny = 0;
	$hodiny = 0;
	$exportRow = sprintf("%04d;%02d;%d;%d;%d;%s;%d;%s;%d;%d;%d;%d;%s;%s\n"
		, $rok
		, $mesic
		, $stredisko
		, $pracovnik
		, $kod
		, $korunyCelkem
		, $dny
		, $hodiny
		, $zakazka
		, $da1
		, $da2
		, $da3
		, $dat_od
		, $dat_do
	);
	array_push($msRows, $exportRow);
    
    // casova mzda, slozka c.116 -----------------------------------------------
	$kod = sprintf("%d", 116);
	$kc = ($pers['vzaby'] - $pers['vzaby_akkord'])*$persLohnFaktor;
	$korunyCelkem = number_format(floatval($kc), 0, ',', '');
	$dny = 0;
	$hodiny = 0;
	$exportRow = sprintf("%04d;%02d;%d;%d;%d;%s;%d;%s;%d;%d;%d;%d;%s;%s\n"
		, $rok
		, $mesic
		, $stredisko
		, $pracovnik
		, $kod
		, $korunyCelkem
		, $dny
		, $hodiny
		, $zakazka
		, $da1
		, $da2
		, $da3
		, $dat_od
		, $dat_do
	);
	array_push($msRows, $exportRow);
}

// dochazka, dovolena nemoci = mzdove slozky po dnech, zakladni udaje o persnr
foreach ($persRows as $pers){
    
    $rok = sprintf("%04d",$jahr);
    $mesic = sprintf("%02d",$monat);
    $stredisko = sprintf("%d",0);
    $pracovnik = sprintf("%d",$pers['grundinfo']['persnr']);
    $persLohnFaktor = floatval($pers['grundinfo']['perslohnfaktor']);
    
    if(!in_array($pracovnik, $pA)){
	continue;
    }
    
    // projit datumy
    foreach ($pers as $datum=>$datumRow){
	if($datum=='grundinfo'){
	    continue;
	}
	else{
	    $zakazka = 0;
	    $da1 = '';
	    $da2 = '';
	    $da3 = '';
	    $dat_od = date("d.m.Y",  strtotime($datum));
	    $dat_do = date("d.m.Y",  strtotime($datum));
	    
	    
	    // dny dvolene, slozka c.009 ---------------------------------------
	    if(intval($datumRow['tage_d'])!=0){
		$kod = sprintf("%d",9);
		$korunyCelkem = number_format(0,0,',','');
		$dny = $datumRow['tage_d'];
		$hodiny = 0;
		$exportRow = sprintf("%04d;%02d;%d;%d;%d;%s;%d;%s;%d;%d;%d;%d;%s;%s\n"
		    ,$rok
		    ,$mesic
		    ,$stredisko
		    ,$pracovnik
		    ,$kod
		    ,$korunyCelkem
		    ,$dny
		    ,$hodiny
		    ,$zakazka
		    ,$da1
		    ,$da2
		    ,$da3
		    ,$dat_od
		    ,$dat_do
		);
		array_push($msRows, $exportRow);
	    }
	    //------------------------------------------------------------------
	    
	    // dny nemoci, slozka c.012 ---------------------------------------
	    if(intval($datumRow['tage_n'])!=0){
		$kod = sprintf("%d",12);
		$korunyCelkem = number_format(0,0,',','');
		$dny = $datumRow['tage_n'];
		$hodiny = 0;
		$exportRow = sprintf("%04d;%02d;%d;%d;%d;%s;%d;%s;%d;%d;%d;%d;%s;%s\n"
		    ,$rok
		    ,$mesic
		    ,$stredisko
		    ,$pracovnik
		    ,$kod
		    ,$korunyCelkem
		    ,$dny
		    ,$hodiny
		    ,$zakazka
		    ,$da1
		    ,$da2
		    ,$da3
		    ,$dat_od
		    ,$dat_do
		);
		array_push($msRows, $exportRow);
	    }
	    
	    // dny neomluvena absence, slozka c.013 ---------------------------------------
	    if(intval($datumRow['tage_z'])!=0){
		$kod = sprintf("%d",13);
		$korunyCelkem = number_format(0,0,',','');
		$dny = $datumRow['tage_z'];
		$hodiny = 0;
		$exportRow = sprintf("%04d;%02d;%d;%d;%d;%s;%d;%s;%d;%d;%d;%d;%s;%s\n"
		    ,$rok
		    ,$mesic
		    ,$stredisko
		    ,$pracovnik
		    ,$kod
		    ,$korunyCelkem
		    ,$dny
		    ,$hodiny
		    ,$zakazka
		    ,$da1
		    ,$da2
		    ,$da3
		    ,$dat_od
		    ,$dat_do
		);
		array_push($msRows, $exportRow);
	    }
	    //------------------------------------------------------------------
	    
	    // dny neplaceneho volna, slozka c.014 ---------------------------------------
	    if(intval($datumRow['tage_nv'])!=0){
		$kod = sprintf("%d",14);
		$korunyCelkem = number_format(0,0,',','');
		$dny = $datumRow['tage_nv'];
		$hodiny = 0;
		$exportRow = sprintf("%04d;%02d;%d;%d;%d;%s;%d;%s;%d;%d;%d;%d;%s;%s\n"
		    ,$rok
		    ,$mesic
		    ,$stredisko
		    ,$pracovnik
		    ,$kod
		    ,$korunyCelkem
		    ,$dny
		    ,$hodiny
		    ,$zakazka
		    ,$da1
		    ,$da2
		    ,$da3
		    ,$dat_od
		    ,$dat_do
		);
		array_push($msRows, $exportRow);
	    }
	    
	    // dny paragrafu, slozka c.010 ---------------------------------------
	    if(intval($datumRow['tage_p'])!=0){
		$kod = sprintf("%d",10);
		$korunyCelkem = number_format(0,0,',','');
		$dny = $datumRow['tage_p'];
		$hodiny = 0;
		$exportRow = sprintf("%04d;%02d;%d;%d;%d;%s;%d;%s;%d;%d;%d;%d;%s;%s\n"
		    ,$rok
		    ,$mesic
		    ,$stredisko
		    ,$pracovnik
		    ,$kod
		    ,$korunyCelkem
		    ,$dny
		    ,$hodiny
		    ,$zakazka
		    ,$da1
		    ,$da2
		    ,$da3
		    ,$dat_od
		    ,$dat_do
		);
		array_push($msRows, $exportRow);
	    }
	    //------------------------------------------------------------------

	    // hodiny casove, slozka c.002 ---------------------------------------
	    if(floatval($datumRow['sumstundena'])-floatval($datumRow['sumstundena_akkord'])!=0){
		$kod = sprintf("%d",2);
		$korunyCelkem = number_format(0,0,',','');
		$dny = 0;
		$hodiny = number_format(floatval($datumRow['sumstundena'])-floatval($datumRow['sumstundena_akkord']),2,',','');
		$exportRow = sprintf("%04d;%02d;%d;%d;%d;%s;%d;%s;%d;%d;%d;%d;%s;%s\n"
		    ,$rok
		    ,$mesic
		    ,$stredisko
		    ,$pracovnik
		    ,$kod
		    ,$korunyCelkem
		    ,$dny
		    ,$hodiny
		    ,$zakazka
		    ,$da1
		    ,$da2
		    ,$da3
		    ,$dat_od
		    ,$dat_do
		);
		array_push($msRows, $exportRow);
	    }
	    //------------------------------------------------------------------
	    
	    // hodiny ukolove, slozka c.004 ---------------------------------------
	    if(floatval($datumRow['sumstundena_akkord'])!=0){
		$kod = sprintf("%d",4);
		$korunyCelkem = number_format(0,0,',','');
		$dny = 0;
		$hodiny = number_format(floatval($datumRow['sumstundena_akkord']),2,',','');
		$exportRow = sprintf("%04d;%02d;%d;%d;%d;%s;%d;%s;%d;%d;%d;%d;%s;%s\n"
		    ,$rok
		    ,$mesic
		    ,$stredisko
		    ,$pracovnik
		    ,$kod
		    ,$korunyCelkem
		    ,$dny
		    ,$hodiny
		    ,$zakazka
		    ,$da1
		    ,$da2
		    ,$da3
		    ,$dat_od
		    ,$dat_do
		);
		array_push($msRows, $exportRow);
	    }
	    //------------------------------------------------------------------
	    
	}
    }
}


// premie nakonec, sumy pro cely mesic
foreach ($persRows as $pers) {

    $rok = sprintf("%04d", $jahr);
    $mesic = sprintf("%02d", $monat);
    $stredisko = sprintf("%d", 0);
    $pracovnik = sprintf("%d", $pers['grundinfo']['persnr']);
    $persLohnFaktor = floatval($pers['grundinfo']['perslohnfaktor']);
    $zakazka = 0;
    $da1 = '';
    $da2 = '';
    $da3 = '';
    $dat_od = date("d.m.Y", strtotime($von));
    $dat_do = date("d.m.Y", strtotime($bis));

    if (!in_array($pracovnik, $pA)) {
	continue;
    }

    // a-premie, slozka 330 ----------------------------------------------------
    if(array_key_exists($pracovnik, $aPremienArray)) {
	$kod = sprintf("%d", 330);
	$korunyCelkem = number_format(floatval($aPremienArray[$pracovnik]['apremie']), 0, ',', '');
	$dny = 0;
	$hodiny = 0;
	$exportRow = sprintf("%04d;%02d;%d;%d;%d;%s;%d;%s;%d;%d;%d;%d;%s;%s\n"
		, $rok
		, $mesic
		, $stredisko
		, $pracovnik
		, $kod
		, $korunyCelkem
		, $dny
		, $hodiny
		, $zakazka
		, $da1
		, $da2
		, $da3
		, $dat_od
		, $dat_do
	);
	array_push($msRows, $exportRow);
    }
}

// ulozit do souboru
$timestamp = date('His');
$path = sprintf("%s%s/%04d%02d_%s.TXT",$a->getGdatPath(),$a->getDat99Path(),$jahr,$monat,$timestamp);

file_put_contents($path, $msRows);

foreach ($msRows as $msRow){
    echo $msRow."<br>";
}
//AplDB::varDump($msRows);
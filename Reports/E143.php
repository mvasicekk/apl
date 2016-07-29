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

$fieldSeparator = ';';
$msRows = array();

foreach ($persRows as $pers){
    
    $rok = sprintf("%04d",$jahr);
    $mesic = sprintf("%02d",$monat);
    $stredisko = sprintf("%d",0);
    $pracovnik = sprintf("%d",$pers['grundinfo']['persnr']);
    
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

// ulozit do souboru
$timestamp = date('His');
$path = sprintf("%s%s/%04d%02d_%s.TXT",$a->getGdatPath(),$a->getDat99Path(),$jahr,$monat,$timestamp);

file_put_contents($path, $msRows);

foreach ($msRows as $msRow){
    echo $msRow."<br>";
}
//AplDB::varDump($msRows);
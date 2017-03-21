<?php
require_once '../db.php';

$a = AplDB::getInstance();

$persvon = $_GET['persvon'];
$persbis = $_GET['persbis'];
$jahr = $_GET['jahr'];
$monat = $_GET['monat'];

$von = sprintf("%04d-%02d-%02d",$jahr,$monat,1);
$bis = sprintf("%04d-%02d-%02d",$jahr,$monat,  cal_days_in_month(CAL_GREGORIAN, $monat, $jahr));


function getPersGrundLeistung($persnr, $von, $bis) {
    global $a;
    $sql = "";
    $sql.=" select";
    $sql.="     drueck.PersNr,";
//    $sql.="     dtattypen.oe,";
    $sql.="     sum(if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-IST`,(drueck.`Stück`)*drueck.`VZ-IST`)) as vzaby,";
    $sql.="     sum(if(doe.akkord<>0,if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-IST`,(drueck.`Stück`)*drueck.`VZ-IST`),0)) as vzaby_akkord,";
    $sql.="     sum(if(doe.akkord<>0,if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-IST`*doe.czk/60,(drueck.`Stück`)*drueck.`VZ-IST`*doe.czk/60),0)) as vzaby_akkord_kc";
    $sql.=" from drueck";
    $sql.=" join dtattypen on dtattypen.tat=drueck.oe";
    $sql.=" join doe on doe.oe=dtattypen.oe";
    $sql.=" where";
    $sql.="     drueck.PersNr='$persnr'";
    $sql.="     and";
    $sql.="     drueck.Datum between '$von' and '$bis'";
    $sql.=" group by";
    $sql.="     drueck.PersNr";
//    $sql.="     dtattypen.oe";

    $rs = $a->getQueryRows($sql);
    return $rs;
}

AplDB::varDump($_GET);
echo "von = $von,bis = $bis<br>";

$sql = "";
$sql.=" select";
$sql.="     dpers.PersNr as persnr,";
$sql.="     dpers.eintritt,";
$sql.="     dpers.austritt,";
$sql.="     dpers.dpersstatus,";
$sql.="     dpers.einarb_zuschlag,";
$sql.="     dpersdetail1.zkusebni_doba_dobaurcita,";
$sql.="     dpers.lohnfaktor/60 as perslohnfaktor,";
$sql.="     if(dpers.austritt is not null,DATEDIFF(NOW(),dpers.austritt),0) as austritt_diff";
$sql.=" from";
$sql.="     dpers";
$sql.=" join dpersdetail1 on dpersdetail1.persnr=dpers.persnr";
$sql.=" where";
$sql.="	    (dpers.persnr between '$persvon' and '$persbis')";
$sql.="     AND";
$sql.="     (dpers.dpersstatus='MA'";
$sql.="     or";
$sql.="     if(dpers.austritt is not null,DATEDIFF(NOW(),dpers.austritt),10000)<60)";
$sql.=" order by";
$sql.="     dpers.PersNr";
	
$persRows = $a->getQueryRows($sql);

if($persRows!==NULL){
    foreach ($persRows as $pers){
	$persnr = $pers['persnr'];
	$eeZuschlagBerechnen = $pers['einarb_zuschlag'];
	$zkusebni_doba_dobaurcita = $pers['zkusebni_doba_dobaurcita'];
	$perslohnfaktor = $pers['perslohnfaktor'];
	echo "<hr>";
	echo "persnr = $persnr<br>einarbzuschlag_berechnen=$eeZuschlagBerechnen<br>zkusebni_doba_dobaurcita=$zkusebni_doba_dobaurcita<br>perslohnfaktor=$perslohnfaktor";
	$leistArray = getPersGrundLeistung($persnr, $von, $bis);
	if($leistArray!==NULL){
	    $vzaby = $leistArray[0]['vzaby'];
	    $vzaby_akkord = $leistArray[0]['vzaby_akkord'];
	    $vzaby_zeit = $vzaby - $vzaby_akkord;
	    $vzaby_akkord_kc = $leistArray[0]['vzaby_akkord_kc'];
	    $vzaby_zeit_kc = $vzaby_zeit*$perslohnfaktor;
	}
	else{
	    $vzaby = 0;
	    $vzaby_akkord = 0;
	    $vzaby_zeit = 0;
	    $vzaby_akkord_kc = 0;
	    $vzaby_zeit_kc = 0;
	}
	
	
	echo "<br>vzaby = $vzaby<br>vzaby_akkord = $vzaby_akkord<br>vzaby_akkord_kc = $vzaby_akkord_kc<br>vzaby_zeit = $vzaby_zeit<br>vzaby_zeit_kc = $vzaby_zeit_kc<br>";
	// test na moznost adaptace, tj. mam vyplneno probezeit ?
	if($eeZuschlagBerechnen==1 && strlen(trim($zkusebni_doba_dobaurcita))>0){
	    echo "<br><strong>pocitam hodinovou mzdu podle adaptacnich pravidel</strong><br>";
	    //potrebuju dochazku pro zadane obdobi
	    
	}
    }
}

//------------------------------------------------------------------------------
// formatovany vystup                                                          |
//------------------------------------------------------------------------------
//persnr = 2857
//einarbzuschlag_berechnen=1
//zkusebni_doba_dobaurcita=
//perslohnfaktor=1
//vzaby = 10474.6
//vzaby_akkord = 10474.6
//vzaby_akkord_kc = 17457.6666666667
//vzaby_zeit = 0
//vzaby_zeit_kc = 0
//------------------------------------------------------------------------------
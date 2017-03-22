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

function getAdaptaceLevel($persArbTageBetweenEintrittAktual){
    $adaptace = 0;
    if($persArbTageBetweenEintrittAktual>0){
	if($persArbTageBetweenEintrittAktual>40){
	    $adaptace = 3;
	}
	else if($persArbTageBetweenEintrittAktual>20){
	    $adaptace = 2;
	}
	else{
	    $adaptace = 1;
	}
    }
    return $adaptace;
}

function getStdLohnForAdaptace($adaptace){
    if($adaptace>2){
	return 130;
    }
    elseif($adaptace>1){
	return 120;
    }
    elseif($adaptace>0){
	return 110;
    }
    return 0;
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
$sql.="     dpers.adaptace_bis,";
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

$premieZaKvalifikaciArray = $a->getPremieZaKvalifikaci($persvon, $persbis, $von, $bis);

if($persRows!==NULL){
    foreach ($persRows as $pers){
	$persnr = $pers['persnr'];
	$eeZuschlagBerechnen = $pers['einarb_zuschlag'];
	$eintrittDate = $pers['eintritt'];
	$zkusebni_doba_dobaurcita = $pers['zkusebni_doba_dobaurcita'];
	$perslohnfaktor = $pers['perslohnfaktor'];
	$adaptaceBisTime = $pers['adaptace_bis']!=NULL?strtotime($pers['adaptace_bis']):strtotime("2100-01-01");
	echo "<hr>";
	echo "persnr = $persnr (ET:$eintrittDate)<br>einarbzuschlag_berechnen=$eeZuschlagBerechnen<br>zkusebni_doba_dobaurcita=$zkusebni_doba_dobaurcita<br>perslohnfaktor=$perslohnfaktor";
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
	
	echo "<h4>mzda pro cely mesic bez adaptace</h4>";
	echo "<br>vzaby = $vzaby<br>vzaby_akkord = $vzaby_akkord<br>vzaby_akkord_kc = $vzaby_akkord_kc<br>vzaby_zeit = $vzaby_zeit<br>vzaby_zeit_kc = $vzaby_zeit_kc<br>";
	
	
	// adaptace ------------------------------------------------------------
	// test na moznost adaptace, tj. mam vyplneno probezeit ?
	if($eeZuschlagBerechnen==1 && strlen(trim($zkusebni_doba_dobaurcita))>0){
	    $anwArray = $a->getPersAnwStdArbeit($persnr,$von,$bis);
	    $adaptaceBisDate = date('Y-m-d',$adaptaceBisTime);
	    echo "<br><strong>pocitam hodinovou mzdu podle adaptacnich pravidel (adaptace bis: $adaptaceBisDate)</strong><br>";
	    //potrebuju dochazku pro zadane obdobi
	    //projdu po jednotlivych dne za cely mesic
	    $vonTime = strtotime($von);$bisTime = strtotime($bis." 23:59:59");
	    $adaptLohnSum = 0;
	    for($aktualTime=$vonTime;$aktualTime<=$bisTime && $aktualTime<=$adaptaceBisTime;$aktualTime+=24*60*60){
		$aktualDate = date('Y-m-d',$aktualTime);
		
		echo "<br>$aktualDate";
		$arbTageBetweenEintrittAktual = $a->getArbTageBetweenDatums($eintrittDate, date('Y-m-d',$aktualTime));
		$persArbTageBetweenEintrittAktual = $a->getATageProPersnrBetweenDatumsAdaptace($persnr, $eintrittDate, date('Y-m-d',$aktualTime));
		echo "persArbTage ab Eintritt = $persArbTageBetweenEintrittAktual";
		$adaptace = getAdaptaceLevel($persArbTageBetweenEintrittAktual);
//		echo ", adaptace = $adaptace";
		$stdLohn = getStdLohnForAdaptace($adaptace);
		echo ", stdLohn = $stdLohn";
		$aStunden = array_key_exists($aktualDate, $anwArray)?$anwArray[$aktualDate]:0;
		echo ", anwArbStunden = $aStunden";
		$tagLohn = $aStunden * $stdLohn;
		echo ", tagesLohn = $tagLohn";
		$adaptLohnSum += $tagLohn;
	    }
	    echo "<br>adaptLohn = $adaptLohnSum";
	    //test jestli mu adaptace konci pred koncem mesice, tj. od konce adaptace do konce mesice mu spocitam vykon normalne (ukolove)
	    if ($adaptaceBisTime < $bisTime) {
		//adaptace konci pred koncem mesice
		$leistArray = getPersGrundLeistung($persnr, date('Y-m-d', $adaptaceBisTime), $bis);
		if ($leistArray !== NULL) {
		    $vzaby = $leistArray[0]['vzaby'];
		    $vzaby_akkord = $leistArray[0]['vzaby_akkord'];
		    $vzaby_zeit = $vzaby - $vzaby_akkord;
		    $vzaby_akkord_kc = $leistArray[0]['vzaby_akkord_kc'];
		    $vzaby_zeit_kc = $vzaby_zeit * $perslohnfaktor;
		} else {
		    $vzaby = 0;
		    $vzaby_akkord = 0;
		    $vzaby_zeit = 0;
		    $vzaby_akkord_kc = 0;
		    $vzaby_zeit_kc = 0;
		}
		echo "<h4>mzda po ukonceni adaptace do konce mesice ".date('Y-m-d', $adaptaceBisTime+24*60*60).' - '.$bis.'</h4>';
		echo "<br>vzaby = $vzaby<br>vzaby_akkord = $vzaby_akkord<br>vzaby_akkord_kc = $vzaby_akkord_kc<br>vzaby_zeit = $vzaby_zeit<br>vzaby_zeit_kc = $vzaby_zeit_kc<br>";
	    }
	}
	// adaptace konec ------------------------------------------------------
	
	// premie za kvalifikaci
	$sumPremieZaKvalifikaci = 0;
	echo "<h4>premie za kvalifikaci</h4>";
	if(array_key_exists($persnr, $premieZaKvalifikaciArray)){
	    $persQArray = $premieZaKvalifikaciArray[$persnr];
	    foreach ($persQArray as $oe=>$qpremie){
		echo "<br>OE=$oe,premie=$qpremie";
		$sumPremieZaKvalifikaci	+= $qpremie;
	    }
	}
	echo "<br>premieZaKvalifikaci=$sumPremieZaKvalifikaci";
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
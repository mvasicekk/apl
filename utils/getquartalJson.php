    <?php
    require_once '../db.php';

    $a = AplDB::getInstance();

    $data = file_get_contents("php://input");
    $o = json_decode($data);


    $persvon = $o->persvon;//$_GET['persvon'];
    $persbis = $o->persbis;//$_GET['persbis'];
    $jahr = $o->jahr;//$_GET['jahr'];
    $monat = $o->monat;//$_GET['monat'];
    $mzdaPodleAdaptace = FALSE;

    $von = sprintf("%04d-%02d-%02d", $jahr, $monat, 1);
    $bis = sprintf("%04d-%02d-%02d", $jahr, $monat, cal_days_in_month(CAL_GREGORIAN, $monat, $jahr));

    
    $lohnArray = array(
	'params'=>array(
	    'persvon'=>$persvon,
	    'persbis'=>$persbis,
	    'jahr'=>$jahr,
	    'monat'=>$monat,
	    'mzdaPodleAdaptace'=>$mzdaPodleAdaptace,
	    'von'=>$von,
	    'bis'=>$bis,
	),
    );
    
    
    
    function getPersGrundLeistung($persnr, $von, $bis) {
	global $a;
	$sql = "";
	$sql.=" select";
	$sql.="     drueck.PersNr,";
	$sql.="     dtattypen.oe,";
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
	$sql.="     drueck.PersNr,";
	$sql.="     dtattypen.oe";

	$rs = $a->getQueryRows($sql);
	return $rs;
    }

    function getAdaptaceLevel($persArbTageBetweenEintrittAktual) {
	$adaptace = 0;
	if ($persArbTageBetweenEintrittAktual > 0) {
	    if ($persArbTageBetweenEintrittAktual > 40) {
		$adaptace = 3;
	    } else if ($persArbTageBetweenEintrittAktual > 20) {
		$adaptace = 2;
	    } else {
		$adaptace = 1;
	    }
	}
	return $adaptace;
    }

    function getStdLohnForAdaptace($adaptace) {
	if ($adaptace > 2) {
	    return 130;
	} elseif ($adaptace > 1) {
	    return 120;
	} elseif ($adaptace > 0) {
	    return 110;
	}
	return 0;
    }

//grundinfo z E143
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
    $sql.="     dpers.dpersstatus,";
    $sql.="     if(dpersbewerber.exekution is null,0,dpersbewerber.exekution) as exekution,";
    $sql.="     DATE_FORMAT(dpers.eintritt,'%y-%m-%d') as eintritt,";
    $sql.="     DATE_FORMAT(dpers.austritt,'%y-%m-%d') as austritt,";
    $sql.="     DATE_FORMAT(dpers.geboren,'%Y-%m-%d') as geboren,";
    $sql.="     DATE_FORMAT(dpersdetail1.dobaurcita,'%y-%m-%d') as dobaurcita,";
    $sql.="     DATE_FORMAT(dpersdetail1.zkusebni_doba_dobaurcita,'%y-%m-%d') as zkusebni_doba_dobaurcita,";
//$sql.="     dzeit.Datum as datum,";
    $sql.="     sum(dzeit.`Stunden`) as sumstunden,";
    $sql.="     sum(if(dtattypen.oestatus='a',dzeit.`Stunden`,0)) as sumstundena,";
    $sql.="     sum(if(dtattypen.oestatus='a' and dtattypen.akkord<>0,dzeit.`Stunden`,0)) as sumstundena_akkord,";
    $sql.="     sum(if(dtattypen.erschwerniss<>0,dzeit.`Stunden`*6,0)) as erschwerniss,";
    $sql.="     sum(if(dzeit.tat='z',1,0)) as tage_z,";
    $sql.="     sum(if(dzeit.tat='z',dzeit.Stunden,0)) as stunden_z,";
    $sql.="     sum(if(dzeit.tat='nv',1,0)) as tage_nv,";
    $sql.="     sum(if(dzeit.tat='nv',dzeit.Stunden,0)) as stunden_nv,";
    $sql.="     sum(if(dzeit.tat='nw',1,0)) as tage_nw,";
    $sql.="     sum(if(dzeit.tat='d',1,0)) as tage_d,";
    $sql.="     sum(if(dzeit.tat='d',dzeit.Stunden,0)) as stunden_d,";
    $sql.="     sum(if(dzeit.tat='np',1,0)) as tage_np,";
    $sql.="     sum(if(dzeit.tat='n',1,0)) as tage_n,";
    $sql.="     sum(if(dzeit.tat='n',dzeit.Stunden,0)) as stunden_n,";
    $sql.="     sum(if(dzeit.tat='nu',1,0)) as tage_nu,";
    $sql.="     sum(if(dzeit.tat='p',1,0)) as tage_p,";
    $sql.="     sum(if(dzeit.tat='p',dzeit.Stunden,0)) as stunden_p,";
    $sql.="     sum(if(dzeit.tat='u',1,0)) as tage_u,";
    $sql.="     sum(if(dzeit.tat='?',1,0)) as tage_frage";
    $sql.="     ,sum(if(calendar.cislodne<>7 and calendar.svatek<>0,dzeit.Stunden/8,0)) as tage_svatek";
    $sql.="     ,sum(if(calendar.cislodne<>7 and calendar.svatek<>0,dzeit.Stunden,0)) as stunden_svatek";
    $sql.="     ,sum(if(dtattypen.fr_sp='N',dzeit.stunden,0)) as nachtstd";
    $sql.="     ,durlaub1.jahranspruch";
    $sql.="     ,durlaub1.rest";
    $sql.="     ,durlaub1.gekrzt";
    $sql.=" from dpers";
    $sql.=" join dzeit on dzeit.PersNr=dpers.PersNr";
    $sql.=" join dtattypen on dzeit.tat=dtattypen.tat";
    $sql.=" join calendar on calendar.datum=dzeit.Datum";
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
    $sql.="     dpers.`PersNr`";

    $rows = $a->getQueryRows($sql);
    $persAplRows = array();

    if ($rows != NULL) {
	foreach ($rows as $r) {
	    $persnr = $r['persnr'];
	    //$datum = $r['datum'];
	    $persAplRows[$persnr]['grundinfo'] = $r;
	    //$persRows[$persnr][$datum] = $r;
	}
    }


    $sql = "";
    $sql.=" select";
    $sql.="     dpers.PersNr as persnr,";
    $sql.="     concat(dpers.`name`,' ',dpers.`vorname`) as persname,";
    $sql.="     dpers.eintritt,";
    $sql.="     dpers.austritt,";
    $sql.="     dpers.dpersstatus,";
    $sql.="     dpers.einarb_zuschlag,";
    $sql.="     dpers.adaptace_bis,";
    $sql.="     if(dpersdetail1.zkusebni_doba_dobaurcita is not null,DATE_FORMAT(dpersdetail1.zkusebni_doba_dobaurcita,'%Y-%m-%d'),null) as zkusebni_doba_dobaurcita,";
    $sql.="     dpers.lohnfaktor/60 as perslohnfaktor,";
    $sql.="     dpers.leistfaktor,";
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
    $premieZaKvalifikaciPctArray = $a->getPremieZaKvalifikaciPctArray($persvon, $persbis, $von, $bis);
//pocitat jen pro lidi s priznakem a_premie
    $aPremienArray = $a->getPersnrApremieArray($monat, $jahr, $persvon, $persbis, '*', FALSE);

    if ($persRows !== NULL) {
	foreach ($persRows as $pers) {
	    $persnr = $pers['persnr'];
	    $persname = $pers['persname'];
	    $eeZuschlagBerechnen = $pers['einarb_zuschlag'];
	    $eintrittDate = $pers['eintritt'];
	    $zkusebni_doba_dobaurcita = $pers['zkusebni_doba_dobaurcita']==NULL?NULL:$pers['zkusebni_doba_dobaurcita'] . ' 23:59:59';
	    $perslohnfaktor = $pers['perslohnfaktor'];
	    $leistFaktor = $pers['leistfaktor'];
	    $adaptaceBisTime = $pers['adaptace_bis'] != NULL ? strtotime($pers['adaptace_bis']) : strtotime("2100-01-01");
	    
	    $lohnArray['personen'][$persnr]['persinfoinfo'] = $pers;
	    $lohnArray['personen'][$persnr]['grundinfo'] = $persAplRows[$persnr]['grundinfo'];
	    
	    $leistArray = getPersGrundLeistung($persnr, $von, $bis);
	    $lAAll = array();
	    $sumVzaby = 0;
	    $sumVzabyAkkord = 0;
	    $sumVzabyZeit = 0;
	    $sumVzabyAkkordKc = 0;
	    $sumVzabyZeitKc = 0;

	    if ($leistArray !== NULL) {
		foreach ($leistArray as $lA) {
		    array_push($lAAll, array(
			'vzaby' => $lA['vzaby'],
			'vzaby_akkord' => $lA['vzaby_akkord'],
			'vzaby_zeit' => $lA['vzaby'] - $lA['vzaby_akkord'],
			'vzaby_akkord_kc' => $lA['vzaby_akkord_kc'],
			'vzaby_zeit_kc' => ($lA['vzaby'] - $lA['vzaby_akkord']) * $perslohnfaktor,
			'oe' => $lA['oe']
			    )
		    );
		    $sumVzaby += $lA['vzaby'];
		    $sumVzabyAkkord += $lA['vzaby_akkord'];
		    $sumVzabyZeit += ($lA['vzaby'] - $lA['vzaby_akkord']);
		    $sumVzabyAkkordKc += $lA['vzaby_akkord_kc'];
		    $sumVzabyZeitKc += ($lA['vzaby'] - $lA['vzaby_akkord']) * $perslohnfaktor;
		}
	    } else {
		array_push($lAAll, array(
		    'vzaby' => 0,
		    'vzaby_akkord' => 0,
		    'vzaby_zeit' => 0,
		    'vzaby_akkord_kc' => 0,
		    'vzaby_zeit_kc' => 0,
		    'oe' => 'bez vykonu'
			)
		);
	    }

	    $lohnArray['personen'][$persnr]['monatlohn']['oes'] = $lAAll;
	    $lohnArray['personen'][$persnr]['monatlohn']['sumVzaby'] = $sumVzaby;
	    $lohnArray['personen'][$persnr]['monatlohn']['sumVzabyAkkord'] = $sumVzabyAkkord;
	    $lohnArray['personen'][$persnr]['monatlohn']['sumVzabyZeit'] = $sumVzabyZeit;
	    $lohnArray['personen'][$persnr]['monatlohn']['sumVzabyAkkordKc'] = $sumVzabyAkkordKc;
	    $lohnArray['personen'][$persnr]['monatlohn']['sumVzabyZeitKc'] = $sumVzabyZeitKc;
	    // adaptace ------------------------------------------------------------
	    // test na moznost adaptace, tj. mam vyplneno probezeit ?
	    if ($eeZuschlagBerechnen == 1 && strlen(trim($zkusebni_doba_dobaurcita)) > 0) {
		$mzdaPodleAdaptace = TRUE;
		$anwArray = $a->getPersAnwStdArbeit($persnr, $von, $bis);
		$adaptaceBisDate = date('Y-m-d', $adaptaceBisTime);
		$zkusebnidobaTime = strtotime($zkusebni_doba_dobaurcita);
		//potrebuju dochazku pro zadane obdobi
		//projdu po jednotlivych dne za cely mesic
		$vonTime = strtotime($von);
		$bisTime = strtotime($bis . " 23:59:59");
		$adaptLohnSum = 0;
		for ($aktualTime = $vonTime; $aktualTime <= $bisTime && $aktualTime <= $adaptaceBisTime && $aktualTime <= $zkusebnidobaTime; $aktualTime+=24 * 60 * 60) {
		    $aktualDate = date('Y-m-d', $aktualTime);
		    $arbTageBetweenEintrittAktual = $a->getArbTageBetweenDatums($eintrittDate, date('Y-m-d', $aktualTime));
		    $persArbTageBetweenEintrittAktual = $a->getATageProPersnrBetweenDatumsAdaptace($persnr, $eintrittDate, date('Y-m-d', $aktualTime));
		    $adaptace = getAdaptaceLevel($persArbTageBetweenEintrittAktual);
		    $stdLohn = getStdLohnForAdaptace($adaptace);
		    $aStunden = array_key_exists($aktualDate, $anwArray) ? $anwArray[$aktualDate] : 0;
		    $tagLohn = $aStunden * $stdLohn;
		    $adaptLohnSum += $tagLohn;
		    $lohnArray['personen'][$persnr]['adaptlohn']['tage'][$aktualDate] = array(
			'tageAbEintritt'=>$persArbTageBetweenEintrittAktual,
			'stundenLohn'=>$stdLohn,
			'anwStunden'=>$aStunden,
			'tagLohn'=>$tagLohn,
		    );
		}
		$lohnArray['personen'][$persnr]['adaptlohn']['summeLohn'] = $adaptLohnSum;
		
		//test jestli mu adaptace konci pred koncem mesice, tj. od konce adaptace do konce mesice mu spocitam vykon normalne (ukolove)
		if ($adaptaceBisTime < $bisTime || $zkusebnidobaTime < $bisTime) {
		    //adaptace konci pred koncem mesice
		    //odkdy skoncila adaptace?
		    $konecAdaptaceTime = min(array($adaptaceBisTime, $zkusebnidobaTime));
		    $leistArray = getPersGrundLeistung($persnr, date('Y-m-d', $konecAdaptaceTime), $bis);
		    $lohnArray['personen'][$persnr]['monatlohnRest'] = $leistArray;
		    
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
		}
	    }
	    // adaptace konec ------------------------------------------------------

	    $lohnArray['personen'][$persnr]['mzdaPodleAdaptace'] = $mzdaPodleAdaptace;
	    
//	    if (!$mzdaPodleAdaptace) {
	    if (TRUE) {
		// premie za kvalifikaci II ------------------------------------
		$sumPremieZaKvalifikaciPct = 0;
		if (array_key_exists($persnr, $premieZaKvalifikaciPctArray)) {
		    $persQArray = $premieZaKvalifikaciPctArray[$persnr];
		    $lohnArray['personen'][$persnr]['premieZaKvalifikaci']['oeKvalifikaceArray'] = $persQArray;
		    foreach ($persQArray as $oe => $qpremieArray) {
			$sumPremieZaKvalifikaciPct += floatval($qpremieArray['pct']);
		    }
		}
		$lohnArray['personen'][$persnr]['premieZaKvalifikaci']['sumPctKvalifikace'] = $sumPremieZaKvalifikaciPct;
		
		

		$qPremieAkkord = $sumPremieZaKvalifikaciPct * $sumVzabyAkkordKc;
		$qPremieZeit = $sumPremieZaKvalifikaciPct * $sumVzabyZeitKc;
		
		$lohnArray['personen'][$persnr]['premieZaKvalifikaci']['akkord'] = $qPremieAkkord;
		$lohnArray['personen'][$persnr]['premieZaKvalifikaci']['zeit'] = $qPremieZeit;
		//a-premie

		if (array_key_exists($persnr, $aPremienArray)) {
		    $persAArray = $aPremienArray[$persnr];
		    $lohnArray['personen'][$persnr]['aPremie'] = $persAArray;
		}


		$bLeistPremie = FALSE;
		$bQTLPremie = FALSE;
		if (array_key_exists($persnr, $persAplRows)) {
		    $d = $persAplRows[$persnr]['grundinfo']['tage_d'];
		    $nw = $persAplRows[$persnr]['grundinfo']['tage_nw'];
		    $z = $persAplRows[$persnr]['grundinfo']['tage_z'];
		    $bLeistPremie = $persAplRows[$persnr]['grundinfo']['premie_za_vykon'] <> 0 ? TRUE : FALSE;
		    $bQTLPremie = $persAplRows[$persnr]['grundinfo']['premie_za_3_mesice'] <> 0 ? TRUE : FALSE;
		}

		//kvartalni premie
		if ($bQTLPremie) {

		    $pracovnik = $persnr;
		    $leistungArray = array('leistung_min' => 0, 'leistung_kc' => 0);
		    if ($monat % 3 == 0) {
			$qtl = ceil($monat / 3);
			$qtlTageSoll = $a->sollTageQTLProPersNr($jahr, $qtl, $pracovnik);
			$leistungArray = $a->getQTLLeistungProPersNrNeu($jahr, $qtl, $pracovnik);
		    }

		    //zobrazeni dnu soll

		    $qtlLeistungIst = $leistungArray['leistung_min'];
		    $qtlLeistungIstKc = $leistungArray['leistung_kc'];
		    $qtlLeistungSoll = isset($qtlTageSoll) ? $qtlTageSoll * 480 : 0;

		    $qtlPraemie = $bQTLPremie == true ? round(0.1 * $qtlLeistungIstKc) : 0;
		    if ($qtlLeistungIst < $qtlLeistungSoll) {
			$qtlPraemie = 0;
		    }
		    $qtlPremieBetrag = $qtlPraemie;

		    
		    $lohnArray['personen'][$persnr]['qtlPremie']['qtlLeistungIst'] = $qtlLeistungIst;
		    $lohnArray['personen'][$persnr]['qtlPremie']['qtlLeistungIstKc'] = $qtlLeistungIstKc;
		    $lohnArray['personen'][$persnr]['qtlPremie']['qtlLeistungSoll'] = $qtlLeistungSoll;
		    $lohnArray['personen'][$persnr]['qtlPremie']['qtlPremieBetrag'] = $qtlPremieBetrag;
		}
		
		
		// premie za vykon
		// pocet kalendarnik prac dnu
		$pracKalDny = $a->getArbTageBetweenDatums($von, $bis);
		$pracovnik = $persnr;
		$gesamtVzabyAkkord = $sumVzabyAkkord;
		$gesamtLeistungZeit = $sumVzabyZeit * $leistFaktor;
		$citatel = $gesamtLeistungZeit + $gesamtVzabyAkkord;
		$aTageProMonat = $pracKalDny;
		$anwTageArbeitsTage = $a->getATageProPersnrBetweenDatums($pracovnik, $von, $bis, 1);
		$ganzMonatNormMinuten = $aTageProMonat * 8 * 60;
		$vonTimestamp = strtotime($von);
		$eintrittTimestamp = strtotime($eintrittDate);
//		$d = 0;
//		$nw = 0;


		if ($eintrittTimestamp > $vonTimestamp)
		    $arbTage = $a->getArbTageBetweenDatums($eintrittDate, $bis);
		else
		    $arbTage = $a->getArbTageBetweenDatums($von, $bis);

		//$monatNormStunden = 8 * ($arbTage - $d - $nw);
		$monatNormStunden = 8 * ($arbTage - $d);
		$monatNormMinuten = $monatNormStunden * 60;
		if ($monatNormMinuten != 0) {
		    $leistungsGrad = round(($citatel) / $monatNormMinuten, 2);
		} else {
		    $leistungsGrad = 0;
		}
		if ($ganzMonatNormMinuten != 0) {
		    $leistungsGradGanzMonat = round(($citatel) / $ganzMonatNormMinuten, 2);
		} else {
		    $leistungsGradGanzMonat = 0;
		}

		$leistPraemieBerechnet1 = $a->getLeistungsPraemieBetragProLeistungsFaktor($leistungsGradGanzMonat) * $aTageProMonat;
		if ($a->getLeistungsPraemieBetragProLeistungsFaktor($leistungsGradGanzMonat) == 200) {
		    $leistPraemieBerechnet = $leistPraemieBerechnet1;
		} else {
		    if ($a->getLeistungsPraemieBetragProLeistungsFaktor($leistungsGrad) > $a->getLeistungsPraemieBetragProLeistungsFaktor($leistungsGradGanzMonat)) {
			$leistPraemieBerechnet = $a->getLeistungsPraemieBetragProLeistungsFaktor($leistungsGrad) * $anwTageArbeitsTage;
		    } else {
			$leistPraemieBerechnet = $leistPraemieBerechnet1;
		    }
		}
		$leistPremieBetrag = $bLeistPremie ? $leistPraemieBerechnet : 0;

		$lohnArray['personen'][$persnr]['leistungPremie']['pracKalDny'] = $pracKalDny;
		$lohnArray['personen'][$persnr]['leistungPremie']['vzabyAkkord'] = $gesamtVzabyAkkord;
		$lohnArray['personen'][$persnr]['leistungPremie']['leistungZeit'] = $gesamtLeistungZeit;
		$lohnArray['personen'][$persnr]['leistungPremie']['monatNormMinuten'] = $monatNormMinuten;
		$lohnArray['personen'][$persnr]['leistungPremie']['ganzMonatNormMinuten'] = $ganzMonatNormMinuten;
		$lohnArray['personen'][$persnr]['leistungPremie']['leistungsGrad'] = $leistungsGrad;
		$lohnArray['personen'][$persnr]['leistungPremie']['leistungsGradGanzMonat'] = $leistungsGradGanzMonat;
		$lohnArray['personen'][$persnr]['leistungPremie']['leistungsPremieBetrag'] = $leistPremieBetrag;
		
		$lohnArray['personen'][$persnr]['zTageWarnung'] = $z;
	    }
	}
    }
    
    echo json_encode($lohnArray);
    ?>

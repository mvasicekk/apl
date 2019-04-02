<?

session_start();
require_once '../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);
$a = AplDB::getInstance();

$persPlanVon = $o->persPlanVon;
$persPlanBis = $o->persPlanBis;
$von = date('Y-m-d',strtotime($persPlanVon));
$bis = date('Y-m-d',strtotime($persPlanBis));

$planPodleRegelOE = $o->planPodleRegelOE;
$oeneu = $o->oeneu;
$planFullen = $o->planFullen;

$persnrList = trim($o->persnrList);
$persnrArray = preg_split("/[\s,]+/", $persnrList);
$osoby = array();

$u = $a->get_user_pc();

if ($planFullen === TRUE) {
    // naplnit vsechny aktivni zamestnance
    $sql = "select persnr,`name`,vorname,regeloe,alteroe,oe3 from dpers where dpersstatus='MA' and kor=0 order by persnr";
    $rs = $a->getQueryRows($sql);
    if ($rs !== NULL) {
	foreach ($rs as $r) {
	    array_push($osoby, $r);
	}
    }
    $planPodleRegelOE = TRUE;
} else {
    $planFullen = FALSE;
    if (count($persnrArray) > 0) {
	$persnrArray = array_unique($persnrArray);
	sort($persnrArray);
	foreach ($persnrArray as $persnr) {
	    $sql = "select persnr,`name`,vorname,regeloe,alteroe,oe3 from dpers where persnr='$persnr' and dpersstatus='MA'";
	    $rs = $a->getQueryRows($sql);
	    if ($rs !== NULL) {
		array_push($osoby, $rs[0]);
	    }
	}
    }
}


$stampVon = strtotime($persPlanVon);
$stampBis = strtotime($persPlanBis);
$denSekund = 24*60*60;

$sqlArray = array();

foreach ($osoby as $osoba) {
    
    $persnr = $osoba["persnr"];
    $regeloe = $a->getRegelOE($persnr);
    if ($regeloe == null) {
	$regeloe = '-';
    }
    $alteroe = $a->getAlternativOE($persnr);
    if ($alteroe == null) {
	$alteroe = $regeloe;
    }
    $regelarbzeit = floatval($a->getRegelarbzeit($persnr));
    //regelarbzeit nastavim na 0 u OE, ktera nejsou pracovni, ale jen u oeneu
    if(!$planPodleRegelOE){
	$oeStatus = $a->getOEStatusForOE($oeneu);
//	if($oeStatus!='a'){
//	    $regelarbzeit = 0;
//	}
    }
    
    $stampAktual = $stampVon;
    while ($stampAktual <= $stampBis) {
	$year = date('Y', $stampAktual);
	$month = date('m', $stampAktual);
	$day = date('d', $stampAktual);
	$datum = sprintf("%04d-%02d-%02d", $year, $month, $day);
	$svatkyArray = $a->getSvatkyArray($year, $month);
	$cislodne = date('w', mktime(0, 1, 1, $month, $day, $year));
	$svatek = array_search($day, $svatkyArray);
	// zjistim cislo tydne, rozlisim sudy a lichy tyden
	// v lichem tydnu pouziju regeloe v sudem pouziju alteroe
	$cislotydne = date('W', mktime(0, 1, 1, $month, $day, $year));
	$lichyTyden = $cislotydne % 2 == 0 ? FALSE : TRUE;
	$pocetOEDatum = $a->getPersPlanDatumOECount($persnr,$datum);
	$insertSql = "";
	if ($cislodne == 0 || $cislodne == 6 || $svatek !== FALSE) {
	    //vlozim jen pokud uz tam neco neni
	    if($pocetOEDatum==0){
		$insertSql = "insert into dzeitsoll (persnr,oe,stunden,datum,user) values('$persnr','-','0','$datum','$u')";
	    }
	} else {
	    // jeste rozlisit, zda budu plnit podle regel nebo podle oeneu
	    if($planPodleRegelOE){
		if ($lichyTyden == TRUE) {
		    if($pocetOEDatum==1){
			//update
			if(!$planFullen){
			    $insertSql = "update dzeitsoll set oe='$regeloe',stunden='$regelarbzeit',user='$u' where persnr='$persnr' and datum='$datum' limit 1";
			}
		    }
		    else{
			if(($planFullen===TRUE && $pocetOEDatum==0)||($planFullen===FALSE)){
			    $insertSql = "insert into dzeitsoll (persnr,oe,stunden,datum,user) values('$persnr','$regeloe','$regelarbzeit','$datum','$u')";
			}
		    }
		} else {
		    if($pocetOEDatum==1){
			if(!$planFullen){
			    $insertSql = "update dzeitsoll set oe='$alteroe',stunden='$regelarbzeit',user='$u' where persnr='$persnr' and datum='$datum' limit 1";
			}
		    }
		    else{
			if(($planFullen===TRUE && $pocetOEDatum==0)||($planFullen===FALSE)){
			    $insertSql = "insert into dzeitsoll (persnr,oe,stunden,datum,user) values('$persnr','$alteroe','$regelarbzeit','$datum','$u')";
			}
		    }
		}
	    }
	    else{
		// sem se pri plneni vsech planu nedostanu, tak nemusim osetrovat existenci stavajicich planu
		if($pocetOEDatum==1){
		    $insertSql = "update dzeitsoll set oe='$oeneu',stunden='$regelarbzeit',user='$u' where persnr='$persnr' and datum='$datum' limit 1";
		}
		else{
		    $insertSql = "insert into dzeitsoll (persnr,oe,stunden,datum,user) values('$persnr','$oeneu','$regelarbzeit','$datum','$u')";
		}
	    }
	}
	
	if(strlen($insertSql)>0){
	    array_push($sqlArray, $insertSql);
	    $a->query($insertSql);
	}
	$stampAktual = strtotime("+1 day",$stampAktual);
	
    }
}





// vychytavka, podivam se, co je pro dany den naplanovano a stunden spocitam jako rozdim mezi $regelarbzeit a sumou jiz naplanovaneho
//$stundenGeplant = $a->getStundenPlanTagPersnr($persnr,$datum);
//$regelarbzeit -= $stundenGeplant;





//$insertSql = "insert into dzeitsoll (persnr,oe,stunden,datum,user) values('$persnr','-','0','$datum','$u')";

//$ar = $a->insert($insertSql);

$pA = array();
$planObj = array();

foreach ($osoby as $osoba){
    $persnr = $osoba['persnr'];
    $sql = " select dzeitsoll.id,persnr,year(datum) as `year`,month(datum) as `month`,day(datum) as `day`,oe,stunden";
    $sql.= " from dzeitsoll";
    $sql.= " where";
    $sql.= "     persnr='$persnr'";
    $sql.= "     and";
    $sql.= "     datum between '$von' and '$bis'";
    $sql.= " order by datum,id";
    $rs = $a->getQueryRows($sql);
    if($rs!==NULL){
	foreach ($rs as $r)    {
	    array_push($pA, $r);
	}
    }
}

//$planObj[$persnr][$year][$month] = array();





if(count($pA)>0){
    foreach ($pA as $r){
	$persnr = $r['persnr'];
	$month = $r['month'];
	$year = $r['year'];
	
	if(!is_array($planObj[$persnr][$year][$month])){
	    $planObj[$persnr][$year][$month] = array();
	} 
	if(!is_array($planObj[$r['persnr']][$r['year']][$r['month']][$r['day']])){
	    $planObj[$r['persnr']][$r['year']][$r['month']][$r['day']] = array();
	}
	array_push($planObj[$r['persnr']][$r['year']][$r['month']][$r['day']],$r);
    }
}
else{
    $planObj[$persnr][$year][$month] = NULL;
}


$returnArray = array(
    'planFullen'=>$planFullen,
    'sqlArray'=>$sqlArray,
    'von' => $von,
    'bis' => $bis,
    'osoby' => $osoby,
    'persnrList' => $persnrList,
    'persPlanVon' => $persPlanVon,
    'persPlanBis' => $persPlanBis,
    'planPodleRegelOE' => $planPodleRegelOE,
    'oeneu' => $oeneu,
    'u' => $u,
    'ar' => $ar,
    'value' => $value,
    'insertSql' => $insertSql,
    'p' => $p,
    'persnr' => $persnr,
    'month' => $month,
    'year' => $year,
    'planObj' => $planObj,
    'svatkyArray' => $svatkyArray,
);

echo json_encode($returnArray);
